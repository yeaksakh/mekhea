<?php

namespace Modules\ExpenseRequest\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Contact;
use App\Product;
use Yajra\DataTables\Facades\DataTables;
use Modules\ExpenseRequest\Entities\ExpenseRequest;
use Modules\ExpenseRequest\Entities\ExpenseRequestCategory;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Illuminate\Support\Facades\Auth;
use App\Utils\ModuleUtil;
use Illuminate\Support\Facades\Schema;
use App\Utils\TransactionUtil;

class ExpenseRequestController extends Controller
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
        $tableName = 'expenserequest_main';

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
            $additionalColumns = json_decode('[{"name":"amount_1","type":"float"},{"name":"expense_for_2","type":"string"},{"name":"who_request_expense_3","type":"users"}]', true);

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

        $module = ModuleCreator::where('module_name', 'expenserequest')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.expenserequest'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

       $ExpenseRequest = ExpenseRequest::where('expenserequest_main.business_id', $business_id)
        ->leftJoin('expenserequest_category as expenserequestcategory', 'expenserequest_main.category_id', '=', 'expenserequestcategory.id')
        ->where('expenserequest_main.business_id', $business_id)
        ->select('expenserequest_main.*', 'expenserequestcategory.name as category_name');

         if (!empty(request()->start_date) && !empty(request()->end_date)) {
            $start = request()->start_date;
            $end = request()->end_date;
            $ExpenseRequest->whereDate('created_at', '>=', $start)
                ->whereDate('created_at', '<=', $end);
        }

        
        

        $result = $ExpenseRequest->get();

        return response()->json($result);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'expenserequest')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.expenserequest'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $expenserequest_categories = ExpenseRequestCategory::forDropdown($business_id);
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
            'categories' => $expenserequest_categories,
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
            
            
            
            
                
            
                            'amount_1' => 'nullable',
                        

                            'expense_for_2' => 'nullable',
                        

                            'who_request_expense_3' => 'nullable',
                                                    
        ]);

        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $expenserequest = new ExpenseRequest();
            $expenserequest->title = $request->title;
            $expenserequest->description = $request->description;
            $expenserequest->business_id = $business_id;
            $expenserequest->category_id = $request->category_id;
            $expenserequest->created_by = auth()->user()->id;
            
            
            
              
             
            
                            $expenserequest->amount_1 = $request->amount_1;
                        

                            $expenserequest->expense_for_2 = $request->expense_for_2;
                        

                            $expenserequest->who_request_expense_3 = $request->who_request_expense_3;
                         
             
            $expenserequest->save();

            return response()->json(['success' => true, 'msg' => __('expenserequest::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function edit($id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'expenserequest')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.expenserequest'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $expenserequest = ExpenseRequest::find($id);
        $expenserequest = ExpenseRequestCategory::forDropdown($business_id);
        $users = User::forDropdown($business_id);

        return response()->json([
            'categories' => $expenserequest_categories,
            'users' => $users,
            'expenserequest' => $expenserequest,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|integer', 
            
            
            
              
              
            
                            'amount_1' => 'nullable',
                        

                            'expense_for_2' => 'nullable',
                        

                            'who_request_expense_3' => 'nullable',
                         
        ]);

        try {
            $expenserequest = ExpenseRequest::find($id);
            $expenserequest->title = $request->title;
            $expenserequest->description = $request->description;
            $expenserequest->category_id = $request->category_id;
            $expenserequest->created_by = auth()->user()->id;
            
            
            
            
             
            
                            $expenserequest->amount_1 = $request->amount_1;
                        

                            $expenserequest->expense_for_2 = $request->expense_for_2;
                        

                            $expenserequest->who_request_expense_3 = $request->who_request_expense_3;
                         
            
            $expenserequest->save();

            return response()->json(['success' => true, 'msg' => __('expenserequest::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    

    public function destroy($id)
    {
        try {
            ExpenseRequest::destroy($id);
            return response()->json(['success' => true, 'msg' => __('expenserequest::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'expenserequest')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.expenserequest'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
        
        $categories = ExpenseRequestCategory::where('business_id', $business_id)->get();
        
        return response()->json([
            'categories' => $categories,
        ]);
    
    }

    public function storeCategory(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $expenserequest = new ExpenseRequestCategory();
            $expenserequest->name = $request->name;
            $expenserequest->description = $request->description;
            $expenserequest->business_id = $business_id;
            $expenserequest->save();

            return response()->json(['success' => true, 'msg' => __('expenserequest::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'expenserequest')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.expenserequest'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
            
        $category = ExpenseRequestCategory::find($id);

        return response()->json([
            'category' => $category,
        ]);
    }

    public function updateCategory(Request $request, $id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $category = ExpenseRequestCategory::find($id);
            $category->name = $request->name;
            $category->description = $request->description;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('expenserequest::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function destroyCategory($id)
    {
        try {
            ExpenseRequestCategory::destroy($id);
            return response()->json(['success' => true, 'msg' => __('expenserequest::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
}