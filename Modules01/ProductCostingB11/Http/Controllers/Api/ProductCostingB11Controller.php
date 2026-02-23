<?php

namespace Modules\ProductCostingB11\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Contact;
use App\Product;
use Yajra\DataTables\Facades\DataTables;
use Modules\ProductCostingB11\Entities\ProductCostingB11;
use Modules\ProductCostingB11\Entities\ProductCostingB11Category;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Illuminate\Support\Facades\Auth;
use App\Utils\ModuleUtil;
use Illuminate\Support\Facades\Schema;
use App\Utils\TransactionUtil;

class ProductCostingB11Controller extends Controller
{
    protected $moduleUtil;
    protected $transactionUtil;

    public function __construct(
        ModuleUtil $moduleUtil,
        TransactionUtil $transactionUtil
    )
    {
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
    }
    
     public function modulefield()
    {
        $tableName = 'productcostingb11_main';

        try {
            // Query the information schema to get column details
            $columns = DB::select(DB::raw("SHOW COLUMNS FROM $tableName"));
    
            // Prepare the response as an associative array to check for duplicates
            $columnInfo = [];
            foreach ($columns as $column) {
                $columnInfo[$column->Field] = [
                    'name' => $column->Field,
                    'type' => $column->Type,
                ];
            }

            // Add dynamic columns
            $additionalColumns = json_decode('[{"name":"product_1","type":"string"},{"name":"cost_2","type":"int"},{"name":"qty_3","type":"int"}]', true);

            if (is_array($additionalColumns)) {
                foreach ($additionalColumns as $additionalColumn) {
                    $columnName = $additionalColumn['name'];
                    
                    // Always replace the existing static column with the dynamic column
                    $columnInfo[$columnName] = $additionalColumn;
                }
            }

            // Convert back to an indexed array
            $columnInfo = array_values($columnInfo);

            return response()->json($columnInfo);
        } catch (\Exception $e) {
            // Return a JSON response with the error message
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'productcostingb11')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.productcostingb11'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

       $ProductCostingB11 = ProductCostingB11::where('productcostingb11_main.business_id', $business_id)
        ->leftJoin('productcostingb11_category as productcostingb11category', 'productcostingb11_main.category_id', '=', 'productcostingb11category.id')
        ->where('productcostingb11_main.business_id', $business_id)
        ->select('productcostingb11_main.*', 'productcostingb11category.name as category_name');

         if (!empty(request()->start_date) && !empty(request()->end_date)) {
            $start = request()->start_date;
            $end = request()->end_date;
            $ProductCostingB11->whereDate('created_at', '>=', $start)
                ->whereDate('created_at', '<=', $end);
        }

        
        

        $result = $ProductCostingB11->get();

        return response()->json($result);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'productcostingb11')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.productcostingb11'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $productcostingb11_categories = ProductCostingB11Category::forDropdown($business_id);
        $users = User::forDropdown($business_id);
        $customers = Contact::where('business_id', $business_id)
            ->where('type', 'customer')
            ->pluck('mobile', 'id');
        $suppliers = Contact::where('business_id', $business_id)
            ->where('type', 'supplier')
            ->pluck('mobile', 'id');
        $products = Product::where('business_id', $business_id)
            ->pluck('name', 'id');

        return response()->json([
            'categories' => $productcostingb11_categories,
            'users' => $users,
            'customers' => $customers,
            'suppliers' => $suppliers,
            'products' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|integer',                 
            
            
            
            
                
            
                            'product_1' => 'nullable',
                        

                            'cost_2' => 'nullable',
                        

                            'qty_3' => 'nullable',
                                                    
        ]);

        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $productcostingb11 = new ProductCostingB11();
            $productcostingb11->title = $request->title;
            $productcostingb11->description = $request->description;
            $productcostingb11->business_id = $business_id;
            $productcostingb11->category_id = $request->category_id;
            $productcostingb11->created_by = auth()->user()->id;
            
            
            
              
             
            
                            $productcostingb11->product_1 = $request->product_1;
                        

                            $productcostingb11->cost_2 = $request->cost_2;
                        

                            $productcostingb11->qty_3 = $request->qty_3;
                         
             
            $productcostingb11->save();

            return response()->json(['success' => true, 'msg' => __('productcostingb11::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function edit($id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'productcostingb11')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.productcostingb11'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $productcostingb11 = ProductCostingB11::find($id);
        $productcostingb11 = ProductCostingB11Category::forDropdown($business_id);
        $users = User::forDropdown($business_id);

        return response()->json([
            'categories' => $productcostingb11_categories,
            'users' => $users,
            'productcostingb11' => $productcostingb11,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|integer', 
            
            
            
              
              
            
                            'product_1' => 'nullable',
                        

                            'cost_2' => 'nullable',
                        

                            'qty_3' => 'nullable',
                         
        ]);

        try {
            $productcostingb11 = ProductCostingB11::find($id);
            $productcostingb11->title = $request->title;
            $productcostingb11->description = $request->description;
            $productcostingb11->category_id = $request->category_id;
            $productcostingb11->created_by = auth()->user()->id;
            
            
            
            
             
            
                            $productcostingb11->product_1 = $request->product_1;
                        

                            $productcostingb11->cost_2 = $request->cost_2;
                        

                            $productcostingb11->qty_3 = $request->qty_3;
                         
            
            $productcostingb11->save();

            return response()->json(['success' => true, 'msg' => __('productcostingb11::lang.updated_successfully')]);
        } catch (\Exception $e) {
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
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'productcostingb11')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.productcostingb11'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
        
        $categories = ProductCostingB11Category::where('business_id', $business_id)->get();
        
        return response()->json([
            'categories' => $categories,
        ]);
    
    }

    public function storeCategory(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $productcostingb11 = new ProductCostingB11Category();
            $productcostingb11->name = $request->name;
            $productcostingb11->description = $request->description;
            $productcostingb11->business_id = $business_id;
            $productcostingb11->save();

            return response()->json(['success' => true, 'msg' => __('productcostingb11::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'productcostingb11')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.productcostingb11'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
            
        $category = ProductCostingB11Category::find($id);

        return response()->json([
            'category' => $category,
        ]);
    }

    public function updateCategory(Request $request, $id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $category = ProductCostingB11Category::find($id);
            $category->name = $request->name;
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