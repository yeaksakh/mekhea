<?php

namespace Modules\PurchaseAutoFill\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Contact;
use App\Product;
use Yajra\DataTables\Facades\DataTables;
use Modules\PurchaseAutoFill\Entities\PurchaseAutoFill;
use Modules\PurchaseAutoFill\Entities\PurchaseAutoFillCategory;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Illuminate\Support\Facades\Auth;
use App\Utils\ModuleUtil;
use Illuminate\Support\Facades\Schema;
use App\Utils\TransactionUtil;

class PurchaseAutoFillController extends Controller
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
        $tableName = 'purchaseautofill_main';

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
            $additionalColumns = json_decode('[{"name":"title_1","type":"string"},{"name":"topic _5","type":"string"}]', true);

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

        $module = ModuleCreator::where('module_name', 'purchaseautofill')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.purchaseautofill'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

       $PurchaseAutoFill = PurchaseAutoFill::where('purchaseautofill_main.business_id', $business_id)
        ->leftJoin('purchaseautofill_category as purchaseautofillcategory', 'purchaseautofill_main.category_id', '=', 'purchaseautofillcategory.id')
        ->where('purchaseautofill_main.business_id', $business_id)
        ->select('purchaseautofill_main.*', 'purchaseautofillcategory.name as category_name');

         if (!empty(request()->start_date) && !empty(request()->end_date)) {
            $start = request()->start_date;
            $end = request()->end_date;
            $PurchaseAutoFill->whereDate('created_at', '>=', $start)
                ->whereDate('created_at', '<=', $end);
        }

        
        

        $result = $PurchaseAutoFill->get();

        return response()->json($result);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'purchaseautofill')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.purchaseautofill'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $purchaseautofill_categories = PurchaseAutoFillCategory::forDropdown($business_id);
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
            'categories' => $purchaseautofill_categories,
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
            
            
            
            
                
            
                            'title_1' => 'nullable',
                        

                            'topic _5' => 'nullable',
                                                    
        ]);

        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $purchaseautofill = new PurchaseAutoFill();
            $purchaseautofill->title = $request->title;
            $purchaseautofill->description = $request->description;
            $purchaseautofill->business_id = $business_id;
            $purchaseautofill->category_id = $request->category_id;
            $purchaseautofill->created_by = auth()->user()->id;
            
            
            
              
             
            
                            $purchaseautofill->title_1 = $request->title_1;
                        

                            $purchaseautofill->topic _5 = $request->topic _5;
                         
             
            $purchaseautofill->save();

            return response()->json(['success' => true, 'msg' => __('purchaseautofill::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function edit($id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'purchaseautofill')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.purchaseautofill'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $purchaseautofill = PurchaseAutoFill::find($id);
        $purchaseautofill = PurchaseAutoFillCategory::forDropdown($business_id);
        $users = User::forDropdown($business_id);

        return response()->json([
            'categories' => $purchaseautofill_categories,
            'users' => $users,
            'purchaseautofill' => $purchaseautofill,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|integer', 
            
            
            
              
              
            
                            'title_1' => 'nullable',
                        

                            'topic _5' => 'nullable',
                         
        ]);

        try {
            $purchaseautofill = PurchaseAutoFill::find($id);
            $purchaseautofill->title = $request->title;
            $purchaseautofill->description = $request->description;
            $purchaseautofill->category_id = $request->category_id;
            $purchaseautofill->created_by = auth()->user()->id;
            
            
            
            
             
            
                            $purchaseautofill->title_1 = $request->title_1;
                        

                            $purchaseautofill->topic _5 = $request->topic _5;
                         
            
            $purchaseautofill->save();

            return response()->json(['success' => true, 'msg' => __('purchaseautofill::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    

    public function destroy($id)
    {
        try {
            PurchaseAutoFill::destroy($id);
            return response()->json(['success' => true, 'msg' => __('purchaseautofill::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'purchaseautofill')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.purchaseautofill'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
        
        $categories = PurchaseAutoFillCategory::where('business_id', $business_id)->get();
        
        return response()->json([
            'categories' => $categories,
        ]);
    
    }

    public function storeCategory(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $purchaseautofill = new PurchaseAutoFillCategory();
            $purchaseautofill->name = $request->name;
            $purchaseautofill->description = $request->description;
            $purchaseautofill->business_id = $business_id;
            $purchaseautofill->save();

            return response()->json(['success' => true, 'msg' => __('purchaseautofill::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'purchaseautofill')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.purchaseautofill'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
            
        $category = PurchaseAutoFillCategory::find($id);

        return response()->json([
            'category' => $category,
        ]);
    }

    public function updateCategory(Request $request, $id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $category = PurchaseAutoFillCategory::find($id);
            $category->name = $request->name;
            $category->description = $request->description;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('purchaseautofill::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function destroyCategory($id)
    {
        try {
            PurchaseAutoFillCategory::destroy($id);
            return response()->json(['success' => true, 'msg' => __('purchaseautofill::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
}