<?php

namespace Modules\CustomerStock\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\User;
use App\Contact;
use App\Product;
use App\Audit;
use App\Category;
use App\BusinessLocation;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use Modules\CustomerStock\Entities\CustomerStock;
use Modules\CustomerStock\Entities\CustomerStockCategory;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Modules\Crm\Utils\CrmUtil;
use Modules\CustomerStock\Entities\CustomerStockSocial;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;
use App\Transaction;
use App\Business;
use App\Variation;


class CustomerStockController extends Controller
{
    protected $moduleUtil;
    protected $transactionUtil;
    protected $crmUtil;

    public function __construct(
        ModuleUtil $moduleUtil,
        TransactionUtil $transactionUtil,
        CrmUtil $crmUtil
    ) {
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
        $this->crmUtil = $crmUtil;
    }

    public function dashboard()
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'customerstock')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        // if ((! auth()->user()->can('module.customerstock')) || ! auth()->user()->can('superadmin') || ! $is_admin) {
        //     abort(403, 'Unauthorized action.');
        // }

        $total_customerstock = CustomerStock::where('business_id', $business_id)->count();

        $total_customerstock_category = CustomerStockCategory::where('business_id', $business_id)->count();

        $customerstock_category = DB::table('customerstock_main as customerstock')
            ->leftJoin('customerstock_category as customerstockcategory', 'customerstock.category_id', '=', 'customerstockcategory.id')
            ->select(
                DB::raw('COUNT(customerstock.id) as total'),
                'customerstockcategory.name as category'
            )
            ->where('customerstock.business_id', $business_id)
            ->groupBy('customerstockcategory.id')
            ->get();

        $user_id = auth()->user()->id;

        return view('customerstock::CustomerStock.dashboard')
            ->with(compact('total_customerstock', 'total_customerstock_category', 'customerstock_category', 'module'));
    }

    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'customerstock')->first();
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        // if ((! auth()->user()->can('module.customerstock')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
        //     abort(403, 'Unauthorized action.');
        // }

        if ($request->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            // Main query with latest records per product
            $CustomerStock = \DB::table('customerstock_main as cs1')
                ->select(
                    'cs1.invoice_id',
                    \DB::raw('MIN(cs1.id) as id'),
                    \DB::raw('MIN(cs1.created_by) as created_by'),
                    \DB::raw('MIN(cs1.customer_id) as customer_id'),
                    \DB::raw('MIN(c.name) as customer_name'),
                    \DB::raw('MIN(t.invoice_no) as invoice_no'),
                    \DB::raw('MIN(cs1.created_at) as created_at'),
                    \DB::raw('COUNT(DISTINCT cs1.product_id) as total_items'),
                    \DB::raw('SUM(cs1.qty_reserved) as total_qty_reserved'),
                    // Calculate total delivered from all records for this invoice
                    \DB::raw('(SELECT SUM(qty_delivered) FROM customerstock_main WHERE business_id = ' . $business_id . ' AND invoice_id = cs1.invoice_id) as total_qty_delivered'),
                    \DB::raw('SUM(cs1.qty_remaining) as total_qty_remaining'),
                    \DB::raw('GROUP_CONCAT(DISTINCT cs1.status) as statuses')
                )
                ->join(
                    \DB::raw('(SELECT invoice_id, product_id, MAX(id) as latest_id FROM customerstock_main WHERE business_id = ' . $business_id . ' GROUP BY invoice_id, product_id) as latest_records'),
                    function ($join) {
                        $join->on('cs1.invoice_id', '=', 'latest_records.invoice_id')
                            ->on('cs1.product_id', '=', 'latest_records.product_id')
                            ->on('cs1.id', '=', 'latest_records.latest_id');
                    }
                )
                ->leftJoin('contacts as c', 'c.id', '=', 'cs1.customer_id')
                ->leftJoin('transactions as t', 't.id', '=', 'cs1.invoice_id')
                ->where('cs1.business_id', $business_id)
                ->groupBy('cs1.invoice_id')
                ->orderBy('cs1.invoice_id', 'desc');

            // Handle date range filter
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $CustomerStock->whereDate('cs1.created_at', '>=', $start)
                    ->whereDate('cs1.created_at', '<=', $end);
            }

            // Handle custom search for invoice number
            if (!empty(request()->search_value)) {
                $searchTerm = request()->search_value;
                $CustomerStock->where(function ($query) use ($searchTerm) {
                    // Search in invoice_id (numeric)
                    if (is_numeric($searchTerm)) {
                        $query->where('cs1.invoice_id', '=', $searchTerm);
                    }
                    // Search in invoice_no (text)
                    $query->orWhere('t.invoice_no', 'like', "%{$searchTerm}%");
                });
            }

            return DataTables::of($CustomerStock)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('CustomerStock.show', $row->invoice_id) . '" data-container=".customerstock_modal" style="margin-right: 5px;"><i class="fa fa-eye"></i> ' . __('messages.view') . '</button>';

                    $html .= '<button class="btn btn-xs btn-success btn-modal" data-href="' . route('CustomerStock.delivery', $row->invoice_id) . '" data-container=".customerstock_modal" style="margin-right: 5px;"><i class="fa fa-truck"></i> ' . __('customerstock::lang.delivery') . '</button>';
                    // Keep delete button if needed
                    $html .= ' <button class="btn btn-xs btn-danger delete-CustomerStock" data-href="' . route('CustomerStock.destroy', $row->invoice_id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';

                    return $html;
                })
                ->addColumn('create_by', function ($row) {
                    $user = User::find($row->created_by);
                    if ($user) {
                        $name = $user->first_name . ' ' . $user->last_name;
                        return $name ? $name : $user->username;
                    }
                    return '';
                })
                ->addColumn('total_items', function ($row) {
                    return $row->total_items;
                })
                ->addColumn('total_qty_reserved', function ($row) {
                    return number_format($row->total_qty_reserved, 2);
                })
                ->addColumn('total_qty_delivered', function ($row) {
                    return number_format($row->total_qty_delivered, 2);
                })
                ->addColumn('total_qty_remaining', function ($row) {
                    return number_format($row->total_qty_remaining, 2);
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $users = User::forDropdown($business_id, false, true, true);
        $customer = Contact::where('business_id', $business_id)
            ->where('type', 'customer')
            ->pluck('name', 'id');
        $supplier = Contact::where('business_id', $business_id)
            ->where('type', 'supplier')
            ->pluck('supplier_business_name', 'id');
        $product = Product::where('business_id', $business_id)
            ->pluck('name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $departments = Category::where('business_id', $business_id)
            ->where('category_type', 'hrm_department')
            ->pluck('name', 'id');
        $designations = Category::where('business_id', $business_id)
            ->where('category_type', 'hrm_designation')
            ->pluck('name', 'id');
        $leads = $this->crmUtil->getLeadsListQuery($business_id);

        return view('customerstock::CustomerStock.index')->with(compact(
            'module',
            'leads',
            'users',
            'customer',
            'product',
            'supplier',
            'business_locations',
            'departments',
            'designations'
        ));
    }


    public function create(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');



        // Fetch invoices for dropdown
        $invoices = Transaction::with('contact')
            ->select(
                'transactions.id',
                'transactions.invoice_no',
                'transactions.final_total',
                'transactions.status',
                'contacts.supplier_business_name'
            )
            ->join('contacts', 'transactions.contact_id', '=', 'contacts.id')
            ->where('transactions.business_id', $business_id)
            ->orderBy('transactions.created_at', 'desc')
            ->limit(10)
            ->get();

        return view('customerstock::CustomerStock.create', compact('invoices'));
    }



    public function show($invoice_id, Request $request)
    {
        try {
            $business_id = request()->session()->get('user.business_id');

            // Get ALL customer stock items for this invoice ORDERED BY CREATED_AT DESC
            $customerStockItems = DB::table('customerstock_main')
                ->where('invoice_id', $invoice_id)
                ->where('business_id', $business_id)
                // ->where('delivery_id', '!=', 0)
                ->orderBy('created_at', 'DESC')
                ->get();

            if ($customerStockItems->isEmpty()) {
                abort(404, 'No customer stock items found for this invoice');
            }

            // Generate delivery summary data
            $deliverySummary = $this->generateDeliverySummary($customerStockItems);

            // Get UNIQUE products for this entire invoice
            $uniqueProducts = $this->getUniqueProductsForInvoice($customerStockItems);

            // Generate LATEST product summary (most recent record for each product) with units
            $latestProductSummary = $this->generateLatestProductSummary($customerStockItems);

            // Get invoice details
            $transaction = Transaction::find($invoice_id);
          

            // Get created by user info from first record
            $firstItem = $customerStockItems->first();
            $createdby = User::find($firstItem->created_by);
            $name = $createdby ? $createdby->first_name . ' ' . $createdby->last_name : 'Unknown User';
            $print_by = auth()->user()->first_name . ' ' . auth()->user()->last_name;
            $date_range = $request->query('date_range');

            return view('customerstock::CustomerStock.show')->with(compact('customerStockItems', 'deliverySummary', 'uniqueProducts', 'latestProductSummary', 'transaction', 'invoice_id', 'name', 'print_by', 'date_range'));
        } catch (\Exception $e) {
            \Log::error('CustomerStock show error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' line ' . $e->getLine());
            abort(500, 'Something went wrong: ' . $e->getMessage());
        }
    }


    // Generate latest product summary (most recent record for each product, but sum all deliveries)
   private function generateLatestProductSummary($customerStockItems)
{
    $summary = [];

    // Group by product_id and get the latest record for each product
    $groupedProducts = $customerStockItems->groupBy('product_id');

    foreach ($groupedProducts as $product_id => $items) {
        // Get the LATEST record for this product (highest created_at)
        $latestItem = $items->sortByDesc('created_at')->first();

        // Calculate total delivered across ALL records for this product
        $totalDelivered = (float) $items->sum('qty_delivered');

        $productData = $this->getProductWithImage($product_id);

        $summary[] = [
            'product_id' => $product_id,  // Add this line
            'name' => $productData['name'],
            'image_url' => $productData['image_url'],
            'qty_reserved' => $latestItem->qty_reserved,      // Keep latest value
            'qty_delivered' => $totalDelivered,               // Use sum of all deliveries
            'qty_remaining' => $latestItem->qty_remaining,    // Keep latest value
            'created_at' => $latestItem->created_at,
            'delivery_date' => $latestItem->delivery_date

        ];
    }

    // Sort by product name
    usort($summary, function ($a, $b) {
        return strcmp($a['name'], $b['name']);
    });

    return $summary;
}



    // Helper method to generate delivery summary
    private function generateDeliverySummary($customerStockItems)
{
    $summary = [];

    // Group by delivery_id
    $groupedDeliveries = $customerStockItems->groupBy('delivery_id');

    foreach ($groupedDeliveries as $delivery_id => $items) {
        $products = [];
        $totalReserved = 0;
        $totalDelivered = 0;
        $totalRemaining = 0;
        $created_at = null;
        $delivery_date = null;

        // Get the earliest created_at for this delivery
        $created_at = $items->min('created_at');
        
        // Get the delivery date from the first item in this delivery
        $firstItem = $items->first();
        if ($firstItem && isset($firstItem->delivery_date)) {
            $delivery_date = $firstItem->delivery_date;
        }

        // Group items in this delivery by product_id to avoid duplicates
        $groupedProducts = $items->groupBy('product_id');

        foreach ($groupedProducts as $product_id => $productItems) {
            // Sum up quantities for this product in this delivery
            $productReserved = $productItems->sum('qty_reserved');
            $productDelivered = $productItems->sum('qty_delivered');
            $productRemaining = $productItems->sum('qty_remaining');

            // Get product with image
            $productData = $this->getProductWithImage($product_id);

            $products[] = [
                'name' => $productData['name'],
                'image_url' => $productData['image_url'],
                'qty_reserved' => $productReserved,
                'qty_delivered' => $productDelivered,
                'qty_remaining' => $productRemaining,
                'delivery_date' => $delivery_date, // Use the delivery_date variable we extracted
            ];

            $totalReserved += $productReserved;
            $totalDelivered += $productDelivered;
            $totalRemaining += $productRemaining;
        }

        $summary[] = [
            'created_at' => $created_at,
            'delivery_date' => $delivery_date, // Add delivery_date to the summary
            'delivery_id' => $delivery_id,
            'products' => $products,
            'totals' => [
                'qty_reserved' => $totalReserved,
                'qty_delivered' => $totalDelivered,
                'qty_remaining' => $totalRemaining,
            ]
        ];
    }

    return $summary;
}
    // Get unique products for the entire invoice (NOT per delivery)
    private function getUniqueProductsForInvoice($customerStockItems)
    {
        $uniqueProducts = [];

        // Group all items by product_id across ALL deliveries in this invoice
        $allProducts = $customerStockItems->groupBy('product_id');

        foreach ($allProducts as $product_id => $items) {
            $productData = $this->getProductWithImage($product_id);

            $uniqueProducts[] = [
                'name' => $productData['name'],
                'image_url' => $productData['image_url'],
            ];
        }

        return $uniqueProducts;
    }


    // Helper method to get product name
    private function getProductName($product_id)
    {
        $product = \DB::table('products')->where('id', $product_id)->first();
        return $product ? $product->name : 'Unknown Product';
    }
public function store(Request $request)
{
    try {
        $request->validate(['invoice_id' => 'required|exists:transactions,id']);

        $business_id = $request->session()->get('user.business_id');
        $user_id     = $request->session()->get('user.id');
        $invoice_id  = $request->invoice_id;

        // Delete old entries
        \DB::table('customerstock_main')
            ->where('invoice_id', $invoice_id)
            ->where('business_id', $business_id)
            ->delete();

        $transaction = Transaction::where('business_id', $business_id)
            ->where('id', $invoice_id)
            ->with([
                'sell_lines' => fn($q) => $q->whereNull('parent_sell_line_id'),
                'sell_lines.product',
                'sell_lines.variations'
            ])
            ->firstOrFail();

        $stocks = [];

        foreach ($transaction->sell_lines as $line) {
            $recalculated = $this->transactionUtil->recalculateSellLineTotals($business_id, $line);
            $qty = $recalculated->quantity - ($recalculated->quantity_returned ?? 0);
            if ($qty <= 0) continue;

            $product = $line->product;

            // Normal product (including free items with 100% discount)
            if ($product->type !== 'combo') {
                $stocks[$line->product_id] = ($stocks[$line->product_id] ?? 0) + $qty;
                continue;
            }

            // COMBO PRODUCT - Extract combo items from JSON column
            $comboRaw = $line->variations?->combo_variations ?? null;
            if (empty($comboRaw)) continue;

            // Handle both string JSON and already decoded array
            $comboData = is_string($comboRaw) ? json_decode($comboRaw, true) : $comboRaw;
            if (!is_array($comboData)) continue;

            foreach ($comboData as $item) {
                // Structure: $item['variation_id'], $item['quantity']
                $variationId = $item['variation_id'] ?? null;
                $comboQty    = $item['quantity'] ?? 1;

                if (!$variationId) continue;

                // Get real product_id from variation
                $variation = \App\Variation::find($variationId);
                if (!$variation || !$variation->product_id) continue;

                $realProductId = $variation->product_id;
                $totalQty = $qty * $comboQty;

                $stocks[$realProductId] = ($stocks[$realProductId] ?? 0) + $totalQty;
            }
        }

        // Insert final stock
        $insertData = [];
        foreach ($stocks as $product_id => $total_qty) {
            $insertData[] = [
                'business_id'   => $business_id,
                'created_by'    => $user_id,
                'customer_id'   => $transaction->contact_id,
                'invoice_id'    => $transaction->id,
                'product_id'    => $product_id,
                'qty_reserved'  => $total_qty,
                'qty_delivered' => 0,
                'qty_remaining' => $total_qty,
                'status'        => 'opened',
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
        }

        if (!empty($insertData)) {
            \DB::table('customerstock_main')->insert($insertData);
        }

        return response()->json([
            'success' => true,
            'msg'     => 'Customer stock created - COMBO EXPLODED!'
        ]);

    } catch (\Exception $e) {
        \Log::error('CustomerStock store error: ' . $e->getMessage());
        return response()->json(['success' => false, 'msg' => $e->getMessage()]);
    }
}


    public function delivery($invoice_id)
    {
        try {
            $business_id = request()->session()->get('user.business_id');

            if (!$invoice_id) {
                abort(404);
            }

            // Get the latest record for each unique product_id in this invoice
            $latestProductRecords = \DB::table('customerstock_main as cs1')
                ->select('cs1.*')
                ->join(
                    \DB::raw('(SELECT product_id, MAX(id) as latest_id FROM customerstock_main WHERE invoice_id = ' . $invoice_id . ' AND business_id = ' . $business_id . ' GROUP BY product_id) as latest_records'),
                    function ($join) {
                        $join->on('cs1.product_id', '=', 'latest_records.product_id')
                            ->on('cs1.id', '=', 'latest_records.latest_id');
                    }
                )
                ->where('cs1.invoice_id', $invoice_id)
                ->where('cs1.business_id', $business_id)
                ->where('cs1.qty_remaining', '>', 0);

            $customerStockItems = $latestProductRecords->get();

            // Get product units for each item
            $productUnits = [];
            foreach ($customerStockItems as $item) {
                if (isset($item->product_id)) {
                    $product = \App\Product::with(['unit', 'second_unit'])->find($item->product_id);
                    if ($product) {
                        $productUnits[$item->product_id] = [
                            'unit_name' => $product->unit ? $product->unit->short_name : 'N/A',
                            'unit_full_name' => $product->unit ? $product->unit->actual_name : 'N/A'
                        ];
                    }
                }
            }

            

            if ($customerStockItems->isEmpty()) {
                $html = '<div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Delivery Products</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fa fa-warning"></i> No items available for delivery.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>';

                return $html;
            }

            return view('customerstock::CustomerStock.delivery')
                ->with(compact('customerStockItems', 'invoice_id', 'productUnits'));
        } catch (\Exception $e) {
            \Log::error('CustomerStock delivery view error: ' . $e->getMessage());
            abort(404);
        }
    }


    public function processDelivery(Request $request)
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');

            // 1. Add validation at the beginning
            $validatedData = $request->validate([
                'invoice_id' => 'required|integer|exists:transactions,id',
                'stock_ids' => 'required|array',
                'stock_ids.*' => 'integer|exists:customerstock_main,id',
                'delivery_qty' => 'required|array',
                'delivery_qty.*' => 'nullable|numeric|min:0',
                'delivery_date' => 'required|date|date_format:Y-m-d'
            ], [
                'invoice_id.required' => 'The invoice ID is missing.',
                'invoice_id.exists' => 'The selected invoice ID is invalid.',
                'stock_ids.required' => 'No stock items selected for delivery.',
                'stock_ids.*.exists' => 'One or more selected stock items are invalid.',
                'delivery_date.date_format' => 'The delivery date must be in YYYY-MM-DD format.'
            ]);

            // 2. Access validated data
            $invoice_id = $validatedData['invoice_id'];
            $stock_ids = $validatedData['stock_ids'];
            $delivery_qtys = $validatedData['delivery_qty'];
            $delivery_date = $validatedData['delivery_date'];

            // 3. Generate unique delivery_id
            $delivery_id = $this->generateDeliveryId($business_id);

            $createdCount = 0;

            foreach ($stock_ids as $stock_id) {
                // Check if delivery_qty exists for this stock_id (allow 0 now)
                $delivery_qty = isset($delivery_qtys[$stock_id]) ? floatval($delivery_qtys[$stock_id]) : 0;

                // Get the current stock item
                $stockItem = \DB::table('customerstock_main')
                    ->where('id', $stock_id)
                    ->where('invoice_id', $invoice_id)
                    ->where('business_id', $business_id)
                    ->first();

                if ($stockItem) {
                    // Check if delivery quantity is valid (0 is now allowed)
                    if ($delivery_qty <= $stockItem->qty_remaining) {
                        // Create new delivery record with delivery_id (even if delivery_qty = 0)
                        \DB::table('customerstock_main')->insert([
                            'delivery_id' => $delivery_id,
                            'created_by' => $user_id,
                            'product_id' => $stockItem->product_id,
                            'customer_id' => $stockItem->customer_id,
                            'invoice_id' => $invoice_id,
                            'qty_reserved' => $stockItem->qty_reserved,
                            'status' => $this->getStatus($stockItem, $delivery_qty),
                            'business_id' => $business_id,
                            'qty_delivered' => $delivery_qty,
                            'qty_remaining' => $stockItem->qty_remaining - $delivery_qty,
                            'delivery_date' => $delivery_date, // Added delivery_date
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        $createdCount++;
                    } else {
                        \Log::warning("Delivery qty for stock ID {$stock_id} exceeds remaining qty.", [
                            'requested_qty' => $delivery_qty,
                            'available_qty' => $stockItem->qty_remaining
                        ]);
                    }
                }
            }

            if ($createdCount > 0) {
                return response()->json([
                    'success' => true,
                    'msg' => __('customerstock::lang.delivery_processed_successfully'),
                    'delivery_id' => $delivery_id
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'msg' => __('customerstock::lang.no_valid_stock_items_found')
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('CustomerStock delivery process error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'msg' => __('lang_v1.something_went_wrong') . ': ' . $e->getMessage()
            ]);
        }
    }

    // Helper method to generate unique delivery ID
    private function generateDeliveryId($business_id)
    {
        // Generate delivery ID like: DEL-001, DEL-002, etc.
        $lastDelivery = \DB::table('customerstock_main')
            ->where('business_id', $business_id)
            ->orderBy('delivery_id', 'desc')
            ->first();

        if ($lastDelivery) {
            $lastId = intval(str_replace('DEL-', '', $lastDelivery->delivery_id));
            $newId = $lastId + 1;
        } else {
            $newId = 1;
        }

        return  $newId;
    }

    public function destroy($invoice_id)
    {
        try {
            $business_id = request()->session()->get('user.business_id');

            // Delete all customer stock records for this invoice
            \DB::table('customerstock_main')
                ->where('invoice_id', $invoice_id)
                ->where('business_id', $business_id)
                ->delete();

            return response()->json([
                'success' => true,
                'msg' => __('customerstock::lang.deleted_successfully')
            ]);
        } catch (\Exception $e) {
            \Log::error('CustomerStock delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => __('messages.something_went_wrong') . ': ' . $e->getMessage()
            ]);
        }
    }

    public function getStatus($customerStock, $delivery_qty)
    {
        return property_exists($customerStock, 'qty_remaining') && $customerStock->qty_remaining - $delivery_qty > 0 ? 'opened' : 'closed';
    }


public function printRecord($id)
{
    $business_id = request()->session()->get('user.business_id');

    $records = DB::table('customerstock_main')
        ->where('delivery_id', $id)
        ->where('business_id', $business_id)
        ->get();

    if ($records->isEmpty()) {
        abort(404, 'Delivery not found');
    }

    $first = $records->first();
    $transaction = Transaction::find($first->invoice_id);
    $customer    = Contact::find($first->customer_id);

    $lines = [];

    // Group by product (if same product appears multiple times)
    foreach ($records->groupBy('product_id') as $product_id => $items) {
        $totalDelivered = $items->sum('qty_delivered');

        // ONLY ADD PRODUCTS THAT HAVE qty_delivered > 0
        if ($totalDelivered <= 0) {
            continue; // skip zero delivery
        }

        $product  = \App\Product::with(['unit', 'second_unit'])->find($product_id);
        $productName = $product?->name ?? 'Unknown Product';
        $unitName    = $product?->unit?->short_name ?? 'PCS';

        $lines[] = [
            'name'           => $productName,
            'variation'      => '',
            'sub_sku'        => '',
            'brand'          => '',
            'quantity'       => number_format($totalDelivered, 2),
            'units'          => $unitName,
            'sell_line_note' => '', // clean slip – remove remaining note if not needed
        ];
    }

    $business = Business::find($business_id);

    $receipt_details = (object)[
        'business_name'    => $business->name ?? 'Business',
        'business_address' => $business->address_line_1 ?? '',
        'business_mobile'  => $business->mobile ?? '',
        'business_logo'    => $business->logo ? asset('uploads/business_logos/' . $business->logo) : '',
        'invoice_no'       => $transaction?->invoice_no ?? 'N/A',
        'customer_name'    => $customer?->name ?? 'N/A',
        'customer_address' => $customer?->contact_address ?? '',
        'delivery_id'      => $id,
        'delivery_date'    => \Carbon\Carbon::parse($first->delivery_date ?? $first->created_at)->format('d/m/Y'),
        'lines'            => $lines,
        'print_date'       => now()->format('d/m/Y h:i A'),
        'printed_by'       => auth()->user()->full_name ?? '',
    ];

    return view('customerstock::CustomerStock.print_template')
        ->with(compact('receipt_details'));
}


    public function delete($delivery_id)
    {
        try {
            $business_id = request()->session()->get('user.business_id');

            $records = \DB::table('customerstock_main')
                ->where('delivery_id', $delivery_id)
                ->where('business_id', $business_id)
                ->get();

            if ($records->isEmpty()) {
                return response()->json(['success' => false, 'msg' => 'Record not found']);
            }

            // Delete the delivery records
            \DB::table('customerstock_main')
                ->where('delivery_id', $delivery_id)
                ->where('business_id', $business_id)
                ->delete();

            return response()->json(['success' => true, 'msg' => 'Delivery deleted successfully']);
        } catch (\Exception $e) {
            \Log::error('CustomerStock delete error: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }


    // Helper method to get product name and image
    private function getProductWithImage($product_id)
    {
        $product = \DB::table('products')->where('id', $product_id)->first();

        if ($product) {
            // Check if product has image
            $image_url = null;
            if (!empty($product->image) && file_exists(public_path('uploads/img/' . $product->image))) {
                $image_url = asset('uploads/img/' . $product->image);
            } elseif (!empty($product->image) && filter_var($product->image, FILTER_VALIDATE_URL)) {
                $image_url = $product->image; // External URL
            }

            return [
                'name' => $product->name ?? 'Unknown Product',
                'image_url' => $image_url
            ];
        }

        return [
            'name' => 'Unknown Product',
            'image_url' => null
        ];
    }

    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $record = \DB::table('customerstock_main')
            ->where('id', $id)
            ->where('business_id', $business_id)
            ->first();

        if (!$record) {
            abort(404);
        }

        return view('customerstock::CustomerStock.edit', compact('record'));
    }

    public function update(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        $record = \DB::table('customerstock_main')
            ->where('id', $id)
            ->where('business_id', $business_id)
            ->first();

        if (!$record) {
            return redirect()->back()->withErrors(['msg' => 'Record not found']);
        }

        \DB::table('customerstock_main')
            ->where('id', $id)
            ->update([
                'qty_reserved' => $request->qty_reserved,
                'qty_delivered' => $request->qty_delivered,
                'qty_remaining' => $request->qty_reserved - $request->qty_delivered,
                'updated_at' => now()
            ]);

        // ✅ Use $record->invoice_id, NOT $id->invoice_id
        return redirect()->route('CustomerStock.index')
            ->with('success', 'Record updated successfully');
    }


    public function editDelivery($delivery_id)
    {
        try {
            $business_id = request()->session()->get('user.business_id');

            // Get ALL records for this delivery_id
            $deliveryItems = \DB::table('customerstock_main')
                ->where('delivery_id', $delivery_id)
                ->where('business_id', $business_id)
                ->get();

            if ($deliveryItems->isEmpty()) {
                abort(404, 'Delivery not found');
            }

            // Get invoice details from first item
            $invoice_id = $deliveryItems->first()->invoice_id;
            $transaction = Transaction::find($invoice_id);

            // Get original reserved items for this invoice (to show available quantities)
            $originalItems = \DB::table('customerstock_main')
                ->where('invoice_id', $invoice_id)
                ->where('business_id', $business_id)
                ->get();

            return view('customerstock::CustomerStock.edit-delivery')->with(compact('deliveryItems', 'originalItems', 'transaction', 'invoice_id'));
        } catch (\Exception $e) {
            \Log::error('CustomerStock edit delivery error: ' . $e->getMessage());
            abort(500, 'Something went wrong');
        }
    }


    public function updateDelivery(Request $request)
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');

            // Validation - REMOVE the exists validation for stock_ids since we're deleting them
            $validatedData = $request->validate([
                'invoice_id' => 'required|integer|exists:transactions,id',
                'stock_ids' => 'required|array',
                'stock_ids.*' => 'integer', // Remove exists validation
                'delivery_qty' => 'required|array',
                'delivery_qty.*' => 'nullable|numeric|min:0'
            ]);

            $invoice_id = $validatedData['invoice_id'];
            $stock_ids = $validatedData['stock_ids'];
            $delivery_qtys = $validatedData['delivery_qty'];

            // Get the delivery_id from existing records BEFORE deleting
            $existingRecord = \DB::table('customerstock_main')
                ->where('id', $stock_ids[0])
                ->where('business_id', $business_id)
                ->first();

            if (!$existingRecord) {
                return response()->json([
                    'success' => false,
                    'msg' => __('customerstock::lang.delivery_not_found')
                ]);
            }

            $delivery_id = $existingRecord->delivery_id;

            // Store original data before deleting
            $originalItems = [];
            foreach ($stock_ids as $stock_id) {
                $originalItem = \DB::table('customerstock_main')
                    ->where('id', $stock_id)
                    ->where('business_id', $business_id)
                    ->first();

                if ($originalItem) {
                    $originalItems[$stock_id] = $originalItem;
                }
            }

            // Delete existing delivery records
            \DB::table('customerstock_main')
                ->where('delivery_id', $delivery_id)
                ->where('business_id', $business_id)
                ->delete();

            $updatedCount = 0;

            foreach ($stock_ids as $stock_id) {
                $delivery_qty = isset($delivery_qtys[$stock_id]) ? floatval($delivery_qtys[$stock_id]) : 0;

                // Use stored original item
                $originalItem = $originalItems[$stock_id] ?? null;

                if ($originalItem) {
                    // Check if delivery quantity is valid
                    if ($delivery_qty <= $originalItem->qty_reserved) {
                        // Create updated delivery record
                        \DB::table('customerstock_main')->insert([
                            'delivery_id' => $delivery_id,
                            'created_by' => $user_id,
                            'product_id' => $originalItem->product_id,
                            'customer_id' => $originalItem->customer_id,
                            'invoice_id' => $invoice_id,
                            'qty_reserved' => $originalItem->qty_reserved,
                            'status' => $this->getStatus($originalItem, $delivery_qty),
                            'business_id' => $business_id,
                            'qty_delivered' => $delivery_qty,
                            'qty_remaining' => $originalItem->qty_reserved - $delivery_qty,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        $updatedCount++;
                    } else {
                        \Log::warning("Delivery qty for stock ID {$stock_id} exceeds reserved qty.", [
                            'requested_qty' => $delivery_qty,
                            'available_qty' => $originalItem->qty_reserved
                        ]);
                    }
                }
            }

            if ($updatedCount > 0) {
                return response()->json([
                    'success' => true,
                    'msg' => __('customerstock::lang.delivery_updated_successfully'),
                    'delivery_id' => $delivery_id
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'msg' => __('customerstock::lang.no_valid_stock_items_found')
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('CustomerStock update delivery error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'msg' => __('lang_v1.something_went_wrong') . ': ' . $e->getMessage()
            ]);
        }
    }


   public function showDelivery($delivery_id)
{
    try {
        $business_id = request()->session()->get('user.business_id');

        // Get ALL records for this delivery_id
        $deliveryItems = \DB::table('customerstock_main')
            ->where('delivery_id', $delivery_id)
            ->where('business_id', $business_id)
            ->get();

        if ($deliveryItems->isEmpty()) {
            abort(404, 'Delivery not found');
        }

        // Get invoice details
        $invoice_id = $deliveryItems->first()->invoice_id;
        $transaction = Transaction::find($invoice_id);

        // Get created by user info
        $firstItem = $deliveryItems->first();
        $createdby = User::find($firstItem->created_by);
        $name = $createdby ? $createdby->first_name . ' ' . $createdby->last_name : 'Unknown User';
        $print_by = auth()->user()->first_name . ' ' . auth()->user()->last_name;

        return view('customerstock::CustomerStock.view-delivery')->with(compact('deliveryItems', 'transaction', 'name', 'print_by'));
    } catch (\Exception $e) {
        \Log::error('CustomerStock view delivery error: ' . $e->getMessage());
        abort(500, 'Something went wrong');
    }
}



    public function getInvoices(Request $request)
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            $query = $request->input('q', '');
            $type = $request->input('type', 'both');

            $transactions = Transaction::where('business_id', $business_id)
                ->where('type', 'sell');

            switch ($type) {
                case 'invoice':
                    $transactions->where('invoice_no', 'LIKE', '%' . $query . '%');
                    break;
                case 'customer':
                    $transactions->whereHas('contact', function ($q) use ($query) {
                        $q->where('name', 'LIKE', '%' . $query . '%');
                    });
                    break;
                default:
                    $transactions->where(function ($q) use ($query) {
                        $q->where('invoice_no', 'LIKE', '%' . $query . '%')
                            ->orWhereHas('contact', function ($subQ) use ($query) {
                                $subQ->where('name', 'LIKE', '%' . $query . '%');
                            });
                    });
            }

            $results = $transactions
                ->with(['contact'])
                ->limit(20)
                ->get()
                ->map(function ($transaction) {
                    return [
                        'id' => $transaction->id,
                        'text' => sprintf(
                            '%s - %s ($%s, %s, %s)',
                            $transaction->invoice_no,
                            $transaction->contact->name ?? 'N/A',
                            number_format($transaction->final_total, 2),
                            $transaction->status,
                            \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y')
                        ),
                        'customer_name' => $transaction->contact->name ?? 'N/A',
                        'total' => $transaction->final_total,
                        'status' => $transaction->status,
                        'date' => \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y'),
                        'ref_no' => $transaction->ref_no
                    ];
                });

            return response()->json($results);
        } catch (\Exception $e) {
            \Log::error('Invoice search error: ' . $e->getMessage());
            return response()->json(['error' => 'Search failed'], 500);
        }
    }
}
