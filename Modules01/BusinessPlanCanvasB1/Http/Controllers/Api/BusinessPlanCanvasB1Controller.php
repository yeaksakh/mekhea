<?php

namespace Modules\BusinessPlanCanvasB1\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Contact;
use App\Product;
use Yajra\DataTables\Facades\DataTables;
use Modules\BusinessPlanCanvasB1\Entities\BusinessPlanCanvasB1;
use Modules\BusinessPlanCanvasB1\Entities\BusinessPlanCanvasB1Category;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Illuminate\Support\Facades\Auth;
use App\Utils\ModuleUtil;
use Illuminate\Support\Facades\Schema;
use App\Utils\TransactionUtil;

class BusinessPlanCanvasB1Controller extends Controller
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
        $tableName = 'businessplancanvasb1_main';

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
            $additionalColumns = json_decode('[{"name":"CustomerSegments_1","type":"text"},{"name":"ValuePropositions_2","type":"text"},{"name":"Channels_3","type":"text"},{"name":"CustomerRelationships_4","type":"text"},{"name":"ReveneuStreams_5","type":"text"},{"name":"KeyResources_6","type":"text"},{"name":"KeyActivities_7","type":"text"},{"name":"KeyPartner_8","type":"text"},{"name":"CostStructure_9","type":"text"}]', true);

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

        $module = ModuleCreator::where('module_name', 'businessplancanvasb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.businessplancanvasb1')) || $module->enabled_modules == 0 && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

       $BusinessPlanCanvasB1 = BusinessPlanCanvasB1::where('businessplancanvasb1_main.business_id', $business_id)
        ->leftJoin('businessplancanvasb1_category as businessplancanvasb1category', 'businessplancanvasb1_main.category_id', '=', 'businessplancanvasb1category.id')
        ->where('businessplancanvasb1_main.business_id', $business_id)
        ->select('businessplancanvasb1_main.*', 'businessplancanvasb1category.name as category_name');

         if (!empty(request()->start_date) && !empty(request()->end_date)) {
            $start = request()->start_date;
            $end = request()->end_date;
            $BusinessPlanCanvasB1->whereDate('created_at', '>=', $start)
                ->whereDate('created_at', '<=', $end);
        }

        
        

        $result = $BusinessPlanCanvasB1->get();

        return response()->json($result);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'businessplancanvasb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.businessplancanvasb1')) || $module->enabled_modules == 0 && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $businessplancanvasb1_categories = BusinessPlanCanvasB1Category::forDropdown($business_id);
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
            'categories' => $businessplancanvasb1_categories,
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
            
            
            
            
                
            
                            'CustomerSegments_1' => 'nullable',
                        

                            'ValuePropositions_2' => 'nullable',
                        

                            'Channels_3' => 'nullable',
                        

                            'CustomerRelationships_4' => 'nullable',
                        

                            'ReveneuStreams_5' => 'nullable',
                        

                            'KeyResources_6' => 'nullable',
                        

                            'KeyActivities_7' => 'nullable',
                        

                            'KeyPartner_8' => 'nullable',
                        

                            'CostStructure_9' => 'nullable',
                                                    
        ]);

        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $businessplancanvasb1 = new BusinessPlanCanvasB1();
            $businessplancanvasb1->title = $request->title;
            $businessplancanvasb1->description = $request->description;
            $businessplancanvasb1->business_id = $business_id;
            $businessplancanvasb1->category_id = $request->category_id;
            $businessplancanvasb1->created_by = auth()->user()->id;
            
            
            
              
             
            
                            $businessplancanvasb1->CustomerSegments_1 = $request->CustomerSegments_1;
                        

                            $businessplancanvasb1->ValuePropositions_2 = $request->ValuePropositions_2;
                        

                            $businessplancanvasb1->Channels_3 = $request->Channels_3;
                        

                            $businessplancanvasb1->CustomerRelationships_4 = $request->CustomerRelationships_4;
                        

                            $businessplancanvasb1->ReveneuStreams_5 = $request->ReveneuStreams_5;
                        

                            $businessplancanvasb1->KeyResources_6 = $request->KeyResources_6;
                        

                            $businessplancanvasb1->KeyActivities_7 = $request->KeyActivities_7;
                        

                            $businessplancanvasb1->KeyPartner_8 = $request->KeyPartner_8;
                        

                            $businessplancanvasb1->CostStructure_9 = $request->CostStructure_9;
                         
             
            $businessplancanvasb1->save();

            return response()->json(['success' => true, 'msg' => __('businessplancanvasb1::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function edit($id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'businessplancanvasb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.businessplancanvasb1')) || $module->enabled_modules == 0 && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $businessplancanvasb1 = BusinessPlanCanvasB1::find($id);
        $businessplancanvasb1 = BusinessPlanCanvasB1Category::forDropdown($business_id);
        $users = User::forDropdown($business_id);

        return response()->json([
            'categories' => $businessplancanvasb1_categories,
            'users' => $users,
            'businessplancanvasb1' => $businessplancanvasb1,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|integer', 
            
            
            
              
              
            
                            'CustomerSegments_1' => 'nullable',
                        

                            'ValuePropositions_2' => 'nullable',
                        

                            'Channels_3' => 'nullable',
                        

                            'CustomerRelationships_4' => 'nullable',
                        

                            'ReveneuStreams_5' => 'nullable',
                        

                            'KeyResources_6' => 'nullable',
                        

                            'KeyActivities_7' => 'nullable',
                        

                            'KeyPartner_8' => 'nullable',
                        

                            'CostStructure_9' => 'nullable',
                         
        ]);

        try {
            $businessplancanvasb1 = BusinessPlanCanvasB1::find($id);
            $businessplancanvasb1->title = $request->title;
            $businessplancanvasb1->description = $request->description;
            $businessplancanvasb1->category_id = $request->category_id;
            $businessplancanvasb1->created_by = auth()->user()->id;
            
            
            
            
             
            
                            $businessplancanvasb1->CustomerSegments_1 = $request->CustomerSegments_1;
                        

                            $businessplancanvasb1->ValuePropositions_2 = $request->ValuePropositions_2;
                        

                            $businessplancanvasb1->Channels_3 = $request->Channels_3;
                        

                            $businessplancanvasb1->CustomerRelationships_4 = $request->CustomerRelationships_4;
                        

                            $businessplancanvasb1->ReveneuStreams_5 = $request->ReveneuStreams_5;
                        

                            $businessplancanvasb1->KeyResources_6 = $request->KeyResources_6;
                        

                            $businessplancanvasb1->KeyActivities_7 = $request->KeyActivities_7;
                        

                            $businessplancanvasb1->KeyPartner_8 = $request->KeyPartner_8;
                        

                            $businessplancanvasb1->CostStructure_9 = $request->CostStructure_9;
                         
            
            $businessplancanvasb1->save();

            return response()->json(['success' => true, 'msg' => __('businessplancanvasb1::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    

    public function destroy($id)
    {
        try {
            BusinessPlanCanvasB1::destroy($id);
            return response()->json(['success' => true, 'msg' => __('businessplancanvasb1::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'businessplancanvasb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.businessplancanvasb1')) || $module->enabled_modules == 0 && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
        
        $categories = BusinessPlanCanvasB1Category::where('business_id', $business_id)->get();
        
        return response()->json([
            'categories' => $categories,
        ]);
    
    }

    public function storeCategory(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $businessplancanvasb1 = new BusinessPlanCanvasB1Category();
            $businessplancanvasb1->name = $request->name;
            $businessplancanvasb1->description = $request->description;
            $businessplancanvasb1->business_id = $business_id;
            $businessplancanvasb1->save();

            return response()->json(['success' => true, 'msg' => __('businessplancanvasb1::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'businessplancanvasb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.businessplancanvasb1')) || $module->enabled_modules == 0 && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
            
        $category = BusinessPlanCanvasB1Category::find($id);

        return response()->json([
            'category' => $category,
        ]);
    }

    public function updateCategory(Request $request, $id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $category = BusinessPlanCanvasB1Category::find($id);
            $category->name = $request->name;
            $category->description = $request->description;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('businessplancanvasb1::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function destroyCategory($id)
    {
        try {
            BusinessPlanCanvasB1Category::destroy($id);
            return response()->json(['success' => true, 'msg' => __('businessplancanvasb1::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
}