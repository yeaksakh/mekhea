<?php

namespace Modules\SWOT\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Contact;
use App\Product;
use Yajra\DataTables\Facades\DataTables;
use Modules\SWOT\Entities\SWOT;
use Modules\SWOT\Entities\SWOTCategory;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Illuminate\Support\Facades\Auth;
use App\Utils\ModuleUtil;
use Illuminate\Support\Facades\Schema;
use App\Utils\TransactionUtil;

class SWOTController extends Controller
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
        $tableName = 'swot_main';

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
            $additionalColumns = json_decode('[{"name":"Title_1","type":"string"},{"name":"Strengths_5","type":"text"},{"name":"Weaknesses_6","type":"text"},{"name":"Opportunities_7","type":"text"},{"name":"Threats_8","type":"text"},{"name":"Note_9","type":"text"}]', true);

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

        $module = ModuleCreator::where('module_name', 'swot')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.swot'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

       $SWOT = SWOT::where('swot_main.business_id', $business_id)
        ->leftJoin('swot_category as swotcategory', 'swot_main.category_id', '=', 'swotcategory.id')
        ->where('swot_main.business_id', $business_id)
        ->select('swot_main.*', 'swotcategory.name as category_name');

         if (!empty(request()->start_date) && !empty(request()->end_date)) {
            $start = request()->start_date;
            $end = request()->end_date;
            $SWOT->whereDate('created_at', '>=', $start)
                ->whereDate('created_at', '<=', $end);
        }

        
        

        $result = $SWOT->get();

        return response()->json($result);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'swot')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.swot'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $swot_categories = SWOTCategory::forDropdown($business_id);
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
            'categories' => $swot_categories,
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
            
            
            
            
                
            
                            'Title_1' => 'nullable',
                        

                            'Strengths_5' => 'nullable',
                        

                            'Weaknesses_6' => 'nullable',
                        

                            'Opportunities_7' => 'nullable',
                        

                            'Threats_8' => 'nullable',
                        

                            'Note_9' => 'nullable',
                                                    
        ]);

        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $swot = new SWOT();
            $swot->title = $request->title;
            $swot->description = $request->description;
            $swot->business_id = $business_id;
            $swot->category_id = $request->category_id;
            $swot->created_by = auth()->user()->id;
            
            
            
              
             
            
                            $swot->Title_1 = $request->Title_1;
                        

                            $swot->Strengths_5 = $request->Strengths_5;
                        

                            $swot->Weaknesses_6 = $request->Weaknesses_6;
                        

                            $swot->Opportunities_7 = $request->Opportunities_7;
                        

                            $swot->Threats_8 = $request->Threats_8;
                        

                            $swot->Note_9 = $request->Note_9;
                         
             
            $swot->save();

            return response()->json(['success' => true, 'msg' => __('swot::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function edit($id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'swot')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.swot'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $swot = SWOT::find($id);
        $swot = SWOTCategory::forDropdown($business_id);
        $users = User::forDropdown($business_id);

        return response()->json([
            'categories' => $swot_categories,
            'users' => $users,
            'swot' => $swot,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|integer', 
            
            
            
              
              
            
                            'Title_1' => 'nullable',
                        

                            'Strengths_5' => 'nullable',
                        

                            'Weaknesses_6' => 'nullable',
                        

                            'Opportunities_7' => 'nullable',
                        

                            'Threats_8' => 'nullable',
                        

                            'Note_9' => 'nullable',
                         
        ]);

        try {
            $swot = SWOT::find($id);
            $swot->title = $request->title;
            $swot->description = $request->description;
            $swot->category_id = $request->category_id;
            $swot->created_by = auth()->user()->id;
            
            
            
            
             
            
                            $swot->Title_1 = $request->Title_1;
                        

                            $swot->Strengths_5 = $request->Strengths_5;
                        

                            $swot->Weaknesses_6 = $request->Weaknesses_6;
                        

                            $swot->Opportunities_7 = $request->Opportunities_7;
                        

                            $swot->Threats_8 = $request->Threats_8;
                        

                            $swot->Note_9 = $request->Note_9;
                         
            
            $swot->save();

            return response()->json(['success' => true, 'msg' => __('swot::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    

    public function destroy($id)
    {
        try {
            SWOT::destroy($id);
            return response()->json(['success' => true, 'msg' => __('swot::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'swot')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.swot'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
        
        $categories = SWOTCategory::where('business_id', $business_id)->get();
        
        return response()->json([
            'categories' => $categories,
        ]);
    
    }

    public function storeCategory(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $swot = new SWOTCategory();
            $swot->name = $request->name;
            $swot->description = $request->description;
            $swot->business_id = $business_id;
            $swot->save();

            return response()->json(['success' => true, 'msg' => __('swot::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'swot')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.swot'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
            
        $category = SWOTCategory::find($id);

        return response()->json([
            'category' => $category,
        ]);
    }

    public function updateCategory(Request $request, $id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $category = SWOTCategory::find($id);
            $category->name = $request->name;
            $category->description = $request->description;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('swot::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function destroyCategory($id)
    {
        try {
            SWOTCategory::destroy($id);
            return response()->json(['success' => true, 'msg' => __('swot::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
}