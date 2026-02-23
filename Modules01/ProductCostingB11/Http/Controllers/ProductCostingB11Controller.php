<?php

namespace Modules\ProductCostingB11\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Contact;
use App\Product;
use App\Audit;
use App\BusinessLocation;
use Yajra\DataTables\Facades\DataTables;
use Modules\ProductCostingB11\Entities\ProductCostingB11;
use Modules\ProductCostingB11\Entities\ProductCostingB11Category;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Illuminate\Support\Facades\Auth;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use Modules\ProductCostingB11\Entities\ProductCost;

class ProductCostingB11Controller extends Controller
{
    protected $moduleUtil;
    protected $transactionUtil;

    public function __construct(
        ModuleUtil $moduleUtil,
        TransactionUtil $transactionUtil,
    ) {
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
    }

    public function dashboard()
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'productcostingb11')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((! auth()->user()->can('module.productcostingb11')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $total_productcostingb11 = ProductCostingB11::where('business_id', $business_id)->count();

        $total_productcostingb11_category = ProductCostingB11Category::where('business_id', $business_id)->count();

        $productcostingb11_category = DB::table('productcostingb11_main as productcostingb11')
            ->leftJoin('productcostingb11_category as productcostingb11category', 'productcostingb11.category_id', '=', 'productcostingb11category.id')
            ->select(
                DB::raw('COUNT(productcostingb11.id) as total'),
                'productcostingb11category.name as category'
            )
            ->where('productcostingb11.business_id', $business_id)
            ->groupBy('productcostingb11category.id')
            ->get();

        $user_id = auth()->user()->id;

        return view('productcostingb11::ProductCostingB11.dashboard')
            ->with(compact('total_productcostingb11', 'total_productcostingb11_category', 'productcostingb11_category', 'module'));
    }

    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'productcostingb11')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((! auth()->user()->can('module.productcostingb11')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $ProductCostingB11 = ProductCostingB11::where('business_id', $business_id);

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $ProductCostingB11->whereDate('created_at', '>=', $start)
                    ->whereDate('created_at', '<=', $end);
            }



            $ProductCostingB11->get();

            return DataTables::of($ProductCostingB11)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('ProductCostingB11.show', $row->id) . '" data-container=".ProductCostingB11_modal" style="margin-right: 5px;"><i class="fa fa-eye"></i> ' . __('messages.view') . '</button>';
                    $html .= '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('ProductCostingB11.edit', $row->id) . '" data-container=".ProductCostingB11_modal" style="margin-right: 5px;"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-ProductCostingB11" data-href="' . route('ProductCostingB11.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
                ->addColumn('category', function ($row) {
                    $category = ProductCostingB11Category::find($row->category_id);
                    return $category ? $category->name : '';
                })
                ->addColumn('create_by', function ($row) {
                    $user = User::find($row->created_by);
                    $name = $user->first_name . ' ' . $user->last_name;
                    return $name ? $name : '';
                })
                ->addColumn('total_value', function ($row) {
                    return $row->productcost->sum(function ($value) {
                        return $value->value ? floatval($value->value)  : 0;
                    });
                })
                ->addColumn('total_qty', function ($row) {
                    return $row->productcost->sum(function ($qty) {
                        return $qty->qty ? floatval($qty->qty) : 0;
                    });
                })
                ->addColumn('cost_per_unit', function ($row) {
                    $total_cost = $row->productcost->sum(function ($cost) {
                        return $cost->value;
                    });
                    $total_qty = $row->productcost->sum(function ($cost) {
                        return $cost->qty;
                    });
                    $cost_per_unit = $total_cost / $total_qty;
                    if (floor($cost_per_unit) == $cost_per_unit) {
                        return number_format($cost_per_unit, 0) . '$';
                    }

                    return number_format($cost_per_unit, 2) . '$';
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

        return view('productcostingb11::ProductCostingB11.index')->with(compact('module', 'users', 'customer', 'product', 'supplier', 'business_locations'));
    }

    public function create(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $productcostingb11_categories = ProductCostingB11Category::forDropdown($business_id);
        $products = Product::where('business_id', $business_id)->pluck('name', 'id');
        $users = User::forDropdown($business_id, false);
        $customer = Contact::where('business_id', $business_id)
            ->where('type', 'customer')
            ->pluck('name', 'id');
        $supplier = Contact::where('business_id', $business_id)
            ->where('type', 'supplier')
            ->pluck('supplier_business_name', 'id');
        $product = Product::where('business_id', $business_id)
            ->pluck('name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, false);

        return view('productcostingb11::ProductCostingB11.create', compact('productcostingb11_categories', 'products', 'users', 'customer', 'supplier', 'product', 'business_locations'));
    }

    public function show($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'productcostingb11')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((! auth()->user()->can('module.productcostingb11')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $productcostingb11 = ProductCostingB11::where('business_id', $business_id)->findOrFail($id);

        $costs = $productcostingb11->productcost;
        $quantities = $productcostingb11->productcost;
        $costs = $productcostingb11->productcost()->where('value', '>', 0)->get();

        $chartData = $costs->where('value', '>', 0)->map(function ($cost) {
            return [
                'y' => (float) $cost->value,
                'label' => $cost->name
            ];
        })->values()->toArray();

        return view('productcostingb11::ProductCostingB11.show', compact('productcostingb11', 'costs', 'quantities', 'chartData'));
    }

    public function store(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $userId = $request->session()->get('user.id');

        // Validate the input data
        $request->validate([
            'productcostingb11_category_id' => 'nullable|integer',
            'product_1' => 'nullable|string',
            'cost.*.name' => 'nullable|string',
            'cost.*.value' => 'nullable|integer',
            'quantity.*.value' => 'nullable|integer',
        ]);

        DB::beginTransaction();
        try {
            // Create main record
            $productcostingb11 = ProductCostingB11::create([
                'business_id' => $business_id,
                'category_id' => $request->productcostingb11_category_id,
                'product_1' => $request->product_1,
                'created_by' => $userId,
            ]);

            // Store cost entries with 0 qty
            if (!empty($request->input('cost'))) {
                $costData = [];
                foreach ($request->input('cost') as $costInput) {
                    if (!empty($costInput['name']) || !empty($costInput['value'])) {
                        $costData[] = [
                            'business_id' => $business_id,
                            'product_cost_id' => $productcostingb11->id,
                            'name' => $costInput['name'] ?? 'Cost Entry',
                            'value' => $costInput['value'] ?? 0,
                            'qty' => 0,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                }
                if (!empty($costData)) {
                    ProductCost::insert($costData);
                }
            }

            // Store quantity entries with 0 value
            if (!empty($request->input('quantity'))) {
                $quantityData = [];
                foreach ($request->input('quantity') as $index => $quantityInput) {
                    if (!empty($quantityInput['value'])) {
                        $quantityData[] = [
                            'business_id' => $business_id,
                            'product_cost_id' => $productcostingb11->id,
                            'name' => 'Quantity Entry ' . ($index + 1),
                            'value' => 0,
                            'qty' => $quantityInput['value'],
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                }
                if (!empty($quantityData)) {
                    ProductCost::insert($quantityData);
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'msg' => __('productcostingb11::lang.saved_successfully')
            ]);
        } catch (\Exception $e) {
            
            \Log::emergency("File:" . $e->getFile() . " Line:" . $e->getLine() . " Message:" . $e->getMessage());

            return response()->json([
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ]);
        }
    }

    public function edit(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (
            !auth()->user()->can('module.productcostingb11') &&
            !auth()->user()->can('superadmin') &&
            !$this->moduleUtil->is_admin(auth()->user(), $business_id)
        ) {
            abort(403, 'Unauthorized action.');
        }

        $productcostingb11 = ProductCostingB11::with(['productcost'])->find($id);

        $productcostingb11_categories = ProductCostingB11Category::forDropdown($business_id);
        $users = User::forDropdown($business_id, false);
        $customer = Contact::where('business_id', $business_id)
            ->where('type', 'customer')
            ->pluck('name', 'id');
        $supplier = Contact::where('business_id', $business_id)
            ->where('type', 'supplier')
            ->pluck('supplier_business_name', 'id');
        $product = Product::where('business_id', $business_id)
            ->pluck('name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, false);

        return view(
            'productcostingb11::ProductCostingB11.edit',
            compact(
                'productcostingb11',
                'productcostingb11_categories',
                'users',
                'customer',
                'supplier',
                'product',
                'business_locations'
            )
        );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'productcostingb11_category_id' => 'nullable|integer',
            'product_1' => 'nullable|string',
            'cost.*.name' => 'nullable|string',
            'cost.*.value' => 'nullable|numeric',
            'quantity.*.value' => 'nullable|numeric',
        ]);

        DB::beginTransaction();
        try {
            $business_id = $request->session()->get('user.business_id');
            $productcostingb11 = ProductCostingB11::find($id);

            // Update main record
            $productcostingb11->update([
                'category_id' => $request->productcostingb11_category_id,
                'product_1' => $request->product_1,
            ]);

            // Delete existing records
            ProductCost::where('product_cost_id', $id)->delete();

            // Store cost entries
            if (!empty($request->input('cost'))) {
                $costData = [];
                foreach ($request->input('cost') as $costInput) {
                    if (!empty($costInput['name']) || !empty($costInput['value'])) {
                        $costData[] = [
                            'business_id' => $business_id,
                            'product_cost_id' => $productcostingb11->id,
                            'name' => $costInput['name'] ?? 'Cost Entry',
                            'value' => $costInput['value'] ?? 0,
                            'qty' => 0,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                }
                if (!empty($costData)) {
                    ProductCost::insert($costData);
                }
            }

            // Store quantity entries
            if (!empty($request->input('quantity'))) {
                $quantityData = [];
                foreach ($request->input('quantity') as $index => $quantityInput) {
                    if (!empty($quantityInput['value'])) {
                        $quantityData[] = [
                            'business_id' => $business_id,
                            'product_cost_id' => $productcostingb11->id,
                            'name' => 'Quantity Entry ' . ($index + 1),
                            'value' => 0,
                            'qty' => $quantityInput['value'],
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                }
                if (!empty($quantityData)) {
                    ProductCost::insert($quantityData);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'msg' => __('productcostingb11::lang.updated_successfully')]);
        } catch (\Exception $e) {
            
            \Log::emergency("File:" . $e->getFile() . " Line:" . $e->getLine() . " Message:" . $e->getMessage());
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function destroy($id)
    {
        try {
            ProductCostingB11::destroy($id);
            return response()->json(['success' => true, 'msg' => __('productcostingb11::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'productcostingb11')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((! auth()->user()->can('module.productcostingb11')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $categories = ProductCostingB11Category::where('business_id', $business_id)->get();

            return DataTables::of($categories)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('ProductCostingB11-categories.edit', $row->id) . '" data-container=".category_modal"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-category" data-href="' . route('ProductCostingB11-categories.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('productcostingb11::Category.index')->with(compact('module'));
    }

    public function createCategory()
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'productcostingb11')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((! auth()->user()->can('module.productcostingb11')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        return view('productcostingb11::Category.create');
    }

    public function storeCategory(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = new ProductCostingB11Category();
            $category->name = $request->name;
            $category->description = $request->description;
            $category->business_id = $business_id;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('productcostingb11::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'productcostingb11')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((! auth()->user()->can('module.productcostingb11')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $category = ProductCostingB11Category::find($id);
        return view('productcostingb11::category.edit', compact('category'));
    }

    public function updateCategory(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = ProductCostingB11Category::find($id);
            $category->name = $request->name;
            $category->business_id = $business_id;
            $category->description = $request->description;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('productcostingb11::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function destroyCategory($id)
    {
        try {
            ProductCostingB11Category::destroy($id);
            return response()->json(['success' => true, 'msg' => __('productcostingb11::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
}
