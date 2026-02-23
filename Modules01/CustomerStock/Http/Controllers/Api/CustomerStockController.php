<?php

namespace Modules\CustomerStock\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Contact;
use App\Product;
use Yajra\DataTables\Facades\DataTables;
use Modules\CustomerStock\Entities\CustomerStock;
use Modules\CustomerStock\Entities\CustomerStockCategory;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Illuminate\Support\Facades\Auth;
use App\Utils\ModuleUtil;
use Illuminate\Support\Facades\Schema;
use App\Utils\TransactionUtil;

class CustomerStockController extends Controller
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
        $tableName = 'customerstock_main';

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
            $additionalColumns = json_decode('[{"name":"customer_id","type":"float"},{"name":"invoice_id_5","type":"float"},{"name":"product_id_6","type":"float"},{"name":"qty_reserved_7","type":"float"},{"name":"qty_delivered_8","type":"float"},{"name":"qty_remaining_9","type":"float"},{"name":"status_10","type":"string"}]', true);

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

        $module = ModuleCreator::where('module_name', 'customerstock')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.customerstock'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

       $CustomerStock = CustomerStock::where('customerstock_main.business_id', $business_id)
        ->leftJoin('customerstock_category as customerstockcategory', 'customerstock_main.category_id', '=', 'customerstockcategory.id')
        ->where('customerstock_main.business_id', $business_id)
        ->select('customerstock_main.*', 'customerstockcategory.name as category_name');

         if (!empty(request()->start_date) && !empty(request()->end_date)) {
            $start = request()->start_date;
            $end = request()->end_date;
            $CustomerStock->whereDate('created_at', '>=', $start)
                ->whereDate('created_at', '<=', $end);
        }

        
        

        $result = $CustomerStock->get();

        return response()->json($result);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'customerstock')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.customerstock'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $customerstock_categories = CustomerStockCategory::forDropdown($business_id);
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
            'categories' => $customerstock_categories,
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
            
            
            
            
                
            
                            'customer_id' => 'nullable',
                        

                            'invoice_id_5' => 'nullable',
                        

                            'product_id_6' => 'nullable',
                        

                            'qty_reserved_7' => 'nullable',
                        

                            'qty_delivered_8' => 'nullable',
                        

                            'qty_remaining_9' => 'nullable',
                        

                            'status_10' => 'nullable',
                                                    
        ]);

        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $customerstock = new CustomerStock();
            $customerstock->title = $request->title;
            $customerstock->description = $request->description;
            $customerstock->business_id = $business_id;
            $customerstock->category_id = $request->category_id;
            $customerstock->created_by = auth()->user()->id;
            
            
            
              
             
            
                            $customerstock->customer_id = $request->customer_id;
                        

                            $customerstock->invoice_id_5 = $request->invoice_id_5;
                        

                            $customerstock->product_id_6 = $request->product_id_6;
                        

                            $customerstock->qty_reserved_7 = $request->qty_reserved_7;
                        

                            $customerstock->qty_delivered_8 = $request->qty_delivered_8;
                        

                            $customerstock->qty_remaining_9 = $request->qty_remaining_9;
                        

                            $customerstock->status_10 = $request->status_10;
                         
             
            $customerstock->save();

            return response()->json(['success' => true, 'msg' => __('customerstock::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function edit($id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'customerstock')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.customerstock'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $customerstock = CustomerStock::find($id);
        $customerstock = CustomerStockCategory::forDropdown($business_id);
        $users = User::forDropdown($business_id);

        return response()->json([
            'categories' => $customerstock_categories,
            'users' => $users,
            'customerstock' => $customerstock,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|integer', 
            
            
            
              
              
            
                            'customer_id' => 'nullable',
                        

                            'invoice_id_5' => 'nullable',
                        

                            'product_id_6' => 'nullable',
                        

                            'qty_reserved_7' => 'nullable',
                        

                            'qty_delivered_8' => 'nullable',
                        

                            'qty_remaining_9' => 'nullable',
                        

                            'status_10' => 'nullable',
                         
        ]);

        try {
            $customerstock = CustomerStock::find($id);
            $customerstock->title = $request->title;
            $customerstock->description = $request->description;
            $customerstock->category_id = $request->category_id;
            $customerstock->created_by = auth()->user()->id;
            
            
            
            
             
            
                            $customerstock->customer_id = $request->customer_id;
                        

                            $customerstock->invoice_id_5 = $request->invoice_id_5;
                        

                            $customerstock->product_id_6 = $request->product_id_6;
                        

                            $customerstock->qty_reserved_7 = $request->qty_reserved_7;
                        

                            $customerstock->qty_delivered_8 = $request->qty_delivered_8;
                        

                            $customerstock->qty_remaining_9 = $request->qty_remaining_9;
                        

                            $customerstock->status_10 = $request->status_10;
                         
            
            $customerstock->save();

            return response()->json(['success' => true, 'msg' => __('customerstock::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    

    public function destroy($id)
    {
        try {
            CustomerStock::destroy($id);
            return response()->json(['success' => true, 'msg' => __('customerstock::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'customerstock')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.customerstock'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
        
        $categories = CustomerStockCategory::where('business_id', $business_id)->get();
        
        return response()->json([
            'categories' => $categories,
        ]);
    
    }

    public function storeCategory(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $customerstock = new CustomerStockCategory();
            $customerstock->name = $request->name;
            $customerstock->description = $request->description;
            $customerstock->business_id = $business_id;
            $customerstock->save();

            return response()->json(['success' => true, 'msg' => __('customerstock::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'customerstock')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.customerstock'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
            
        $category = CustomerStockCategory::find($id);

        return response()->json([
            'category' => $category,
        ]);
    }

    public function updateCategory(Request $request, $id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $category = CustomerStockCategory::find($id);
            $category->name = $request->name;
            $category->description = $request->description;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('customerstock::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function destroyCategory($id)
    {
        try {
            CustomerStockCategory::destroy($id);
            return response()->json(['success' => true, 'msg' => __('customerstock::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
}