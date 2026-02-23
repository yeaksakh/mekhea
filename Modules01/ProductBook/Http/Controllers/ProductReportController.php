<?php

namespace Modules\ProductBook\Http\Controllers;


use App\Brands;
use App\Business;
use App\Product;
use App\Variation;

use App\Transaction;
use App\BusinessLocation;
use App\Category;
use App\Contact;
use App\CustomerGroup;
use App\Discount;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\VariationLocationDetails;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\PurchaseLine;
use App\SellingPriceGroup;
use App\StockAdjustmentLine;
use App\TransactionSellLine;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\ProductBook\Http\Services\DateFilterService;
use App\Http\Controllers\ProductController;
use App\Unit;
use Faker\Factory as Faker;
use App\Utils\TransactionUtil;
use App\Charts\CommonChart;
use App\Http\Controllers\ContactController;
use App\TransactionSellLinesPurchaseLines;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Modules\Manufacturing\Entities\MfgRecipe;
use Modules\Manufacturing\Entities\MfgRecipeIngredient;
use Modules\MiniReportB1\Entities\Business as EntitiesBusiness;
use Yajra\DataTables\Facades\DataTables;


class ProductReportController extends Controller
{


    protected $transactionUtil;
    protected $productUtil;

    protected $moduleUtil;

    protected $productByGroupPrice;
    protected $commonUtil;
    protected $dateFilterService;
    protected $customer;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ProductUtil $productUtil, ModuleUtil $moduleUtil, ProductController $productByGroupPrice, Util $commonUtil, DateFilterService $dateFilterService, TransactionUtil $transactionUtil, ContactController $customer, )
    {
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->productByGroupPrice = $productByGroupPrice;
        $this->commonUtil = $commonUtil;
        $this->dateFilterService = $dateFilterService;
        $this->transactionUtil = $transactionUtil;
        $this->customer = $customer;
    }



    public function productPackagePrice(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            try {
                $query = Product::with(['media', 'product_locations'])
                    ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                    ->join('units', 'products.unit_id', '=', 'units.id')
                    ->leftJoin('categories as c1', 'products.category_id', '=', 'c1.id')
                    ->leftJoin('tax_rates', 'products.tax', '=', 'tax_rates.id')
                    ->join('variations as v', 'v.product_id', '=', 'products.id')
                    ->leftJoin('variation_location_details as vld', 'vld.variation_id', '=', 'v.id')
                    ->where('products.business_id', $business_id)
                    ->where('products.type', '!=', 'modifier')
                    ->select(
                        'products.id',
                        'products.name as name',
                        'products.type',
                        'c1.name as category',
                        'brands.name as brand',
                        'tax_rates.name as tax',
                        'products.sku',
                        'products.image',
                        'products.enable_stock',
                        'products.is_inactive',
                        'products.not_for_selling',
                        'products.product_custom_field1',
                        'products.product_custom_field2',
                        'products.product_custom_field3',
                        'products.product_custom_field4',
                        'products.product_custom_field5',
                        'products.product_custom_field6',
                        'products.product_custom_field7',
                        DB::raw('SUM(vld.qty_available) as current_stock'),
                        DB::raw('MAX(v.dpp_inc_tax) as max_purchase_price'),
                        DB::raw('MAX(v.sell_price_inc_tax) as max_price')
                    )
                    ->groupBy('products.id');
                
                // Handle search functionality
                if ($request->has('search') && !empty($request->get('search')['value'])) {
                    $searchValue = $request->get('search')['value'];
                    $query->where(function ($q) use ($searchValue) {
                        $q->where('products.name', 'like', "%{$searchValue}%")
                            ->orWhere('products.sku', 'like', "%{$searchValue}%")
                            ->orWhere('c1.name', 'like', "%{$searchValue}%")
                            ->orWhere('brands.name', 'like', "%{$searchValue}%")
                            ->orWhere('products.type', 'like', "%{$searchValue}%")
                            ->orWhere('tax_rates.name', 'like', "%{$searchValue}%")
                            ->orWhere('products.product_custom_field1', 'like', "%{$searchValue}%")
                            ->orWhere('products.product_custom_field2', 'like', "%{$searchValue}%")
                            ->orWhere('products.product_custom_field3', 'like', "%{$searchValue}%")
                            ->orWhere('products.product_custom_field4', 'like', "%{$searchValue}%")
                            ->orWhere('products.product_custom_field5', 'like', "%{$searchValue}%")
                            ->orWhere('products.product_custom_field6', 'like', "%{$searchValue}%")
                            ->orWhere('products.product_custom_field7', 'like', "%{$searchValue}%");
                    });
                }

                // Temporary logging for debugging without breaking AJAX
                Log::debug('productPackagePrice SQL', [
                    'sql' => $query->toSql(),
                    'bindings' => $query->getBindings(),
                    'url' => $request->fullUrl(),
                    'search' => $request->get('search')['value'] ?? null,
                    'user_id' => optional(auth()->user())->id,
                ]);

                return Datatables::of($query)
                    ->addColumn('action', function ($row) {
                        $button = '<button class="print-button print-product-btn" data-product-id="' . $row->id . '" title="Print Product">
                            <span class="print-icon"></span>
                       </button>';
                        return $button;
                    })
                    ->editColumn('product_locations', function ($row) {
                        return $row->product_locations ? $row->product_locations->implode('name', ', ') : '';
                    })
                    ->rawColumns(['product_locations', 'action'])
                    ->make(true);

            } catch (\Exception $e) {
                // Return a JSON error response (handled by DataTables or frontend)
                return response()->json([
                    'error' => true,
                    'message' => 'Failed to load data: ' . $e->getMessage()
                ], 500);
            }
        }
        $businessInfo = [
            'user_name' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
        ];
        $custom_labels = !empty(session('business.custom_labels')) ? json_decode(session('business.custom_labels'), true) : [];

        return view('productbook::ProductBook.product_package_price', compact('businessInfo', 'custom_labels'));
    }





    public function getDetailProduct($id)
    {
        try {
            // 🔐 Ensure user is authenticated


            $user = auth()->user();
            $business_id = $user->business_id;

            // 🔍 Fetch product with relationships
            $product = Product::where('business_id', $business_id)
                ->with([
                    'brand',
                    'unit',
                    'category',
                    'sub_category',
                    'product_tax',
                    'variations' => function ($q) {
                        $q->with([
                            'product_variation',
                            'group_prices',
                            'media'
                        ]);
                    },
                    'product_locations',
                    'warranty',
                    'media',

                ])
                ->findOrFail($id);

            // 💰 Selling Price Groups
            $price_groups = SellingPriceGroup::where('business_id', $business_id)
                ->active()
                ->pluck('name', 'id');

            $allowed_group_prices = [];
            foreach ($price_groups as $group_id => $name) {
                if ($user->can('selling_price_group.' . $group_id)) {
                    $allowed_group_prices[$group_id] = $name;
                }
            }

            // 💵 Group Price Details
            $group_price_details = [];
            foreach ($product->variations as $variation) {
                foreach ($variation->group_prices as $group_price) {
                    $group_price_details[$variation->id][$group_price->price_group_id] = [
                        'price' => $group_price->price_inc_tax,
                        'price_type' => $group_price->price_type,
                        'calculated_price' => $group_price->calculated_price,
                    ];
                }
            }

            // 🧱 Rack Details
            $rack_details = $this->productUtil->getRackDetails($business_id, $id, true);

            // 🔄 Combo Variations
            $combo_variations = [];
            if ($product->type == 'combo' && isset($product->variations[0])) {
                $combo_variations = $this->__getComboProductDetails(
                    $product->variations[0]->combo_variations,
                    $business_id
                );
            }

            $products = Product::where('business_id', $business_id)
                ->with(['brand', 'unit', 'category', 'sub_category', 'product_tax', 'variations', 'variations.product_variation', 'variations.group_prices', 'variations.media', 'product_locations', 'warranty', 'media'])
                ->findOrFail($id);

            // ✅ Return structured JSON (only the data you need for printing)
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'category' => $product->category?->name ?? 'N/A',
                    'brand' => $product->brand?->name ?? 'N/A',
                    'product_locations' => $product->product_locations->pluck('name')->implode(', ') ?? 'N/A',
                    'type' => ucfirst($product->type),
                    'tax' => $product->product_tax?->name ?? 'N/A',
                    'product_custom_field1' => $product->product_custom_field1 ?? 'N/A',
                    'product_custom_field2' => $product->product_custom_field2 ?? 'N/A',
                    'product_custom_field3' => $product->product_custom_field3 ?? 'N/A',
                    'product_custom_field4' => $product->product_custom_field4 ?? 'N/A',
                    'product_custom_field5' => $product->product_custom_field5 ?? 'N/A',
                    'product_custom_field6' => $product->product_custom_field6 ?? 'N/A',
                    'product_custom_field7' => $product->product_custom_field7 ?? 'N/A',
                    'max_purchase_price' => $product->max_purchase_price ?? 'N/A',
                    'max_price' => $product->max_price ?? 'N/A',
                    'rack_details' => $rack_details,
                    'allowed_group_prices' => $allowed_group_prices,
                    'group_price_details' => $group_price_details,
                    'combo_variations' => $combo_variations,
                    'image_url' => $product->image_url,
                    'variations' => $product->variations,
                    'product' => $products,
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            dump('Product not found: ' . $id);
            return response()->json(['error' => 'Product not found'], 404);
        } catch (\Exception $e) {
            dump('Product detail fetch failed: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Server error. Please try again.'], 500);
        }
    }

    public function __getComboProductDetails($combo_variations, $business_id)
    {
        $grouped_variations = [];

        foreach ($combo_variations as $key => $value) {
            $variation = Variation::with(['product.category'])->find($value['variation_id']);
            $sub_units = $this->productUtil->getSubUnits($business_id, $variation->product->unit_id, true);


            $combo_variations[$key]['variation'] = $variation;
            $combo_variations[$key]['sub_units'] = $sub_units;
            $combo_variations[$key]['multiplier'] = 1;

            if (!empty($sub_units) && isset($sub_units[$value['unit_id']])) {
                $combo_variations[$key]['multiplier'] = $sub_units[$value['unit_id']]['multiplier'];
                $combo_variations[$key]['unit_name'] = $sub_units[$value['unit_id']]['name'];
            }

            $category_id = $variation->product->category_id ?? 'uncategorized';
            $grouped_variations[$category_id][] = $combo_variations[$key];
        }

        return $grouped_variations;
    }

}
