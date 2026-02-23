<?php

namespace Modules\EmployeeTracker\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Contact;
use App\Product;
use Yajra\DataTables\Facades\DataTables;
use Modules\EmployeeTracker\Entities\EmployeeTracker;
use Modules\EmployeeTracker\Entities\EmployeeTrackerCategory;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Illuminate\Support\Facades\Auth;
use App\Utils\ModuleUtil;
use Illuminate\Support\Facades\Schema;
use App\Utils\TransactionUtil;

class EmployeeTrackerController extends Controller
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
        $tableName = 'employeetracker_main';

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
            $additionalColumns = json_decode('[{"name":"department_1","type":"departments"},{"name":"employee_2","type":"users"},{"name":"title_3","type":"string"},{"name":"description_4","type":"text"},{"name":"file_5","type":"file"},{"name":"image_6","type":"file"},{"name":"status_7","type":"status_true_false"}]', true);

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

        $module = ModuleCreator::where('module_name', 'employeetracker')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.employeetracker'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

       $EmployeeTracker = EmployeeTracker::where('employeetracker_main.business_id', $business_id)
        ->leftJoin('employeetracker_category as employeetrackercategory', 'employeetracker_main.category_id', '=', 'employeetrackercategory.id')
        ->where('employeetracker_main.business_id', $business_id)
        ->select('employeetracker_main.*', 'employeetrackercategory.name as category_name');

         if (!empty(request()->start_date) && !empty(request()->end_date)) {
            $start = request()->start_date;
            $end = request()->end_date;
            $EmployeeTracker->whereDate('created_at', '>=', $start)
                ->whereDate('created_at', '<=', $end);
        }

        
        

        $result = $EmployeeTracker->get();

        return response()->json($result);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'employeetracker')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.employeetracker'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $employeetracker_categories = EmployeeTrackerCategory::forDropdown($business_id);
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
            'categories' => $employeetracker_categories,
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
            
            
            
            
                
            
                            'department_1' => 'nullable',
                        

                            'employee_2' => 'nullable',
                        

                            'title_3' => 'nullable',
                        

                            'description_4' => 'nullable',
                        

                            'status_7' => 'nullable',
                                                    
        ]);

        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $employeetracker = new EmployeeTracker();
            $employeetracker->title = $request->title;
            $employeetracker->description = $request->description;
            $employeetracker->business_id = $business_id;
            $employeetracker->category_id = $request->category_id;
            $employeetracker->created_by = auth()->user()->id;
            
            
            
              
             
            
                            $employeetracker->department_1 = $request->department_1;
                        

                            $employeetracker->employee_2 = $request->employee_2;
                        

                            $employeetracker->title_3 = $request->title_3;
                        

                            $employeetracker->description_4 = $request->description_4;
                        

                            $employeetracker->status_7 = $request->status_7;
                         
            
                            if ($request->hasFile('file_5')) {
                                $documentPath = $this->transactionUtil->uploadFile($request, 'file_5', 'EmployeeTracker');
                                $employeetracker->file_5 = $documentPath;
                            }
                        

                            if ($request->hasFile('image_6')) {
                                $documentPath = $this->transactionUtil->uploadFile($request, 'image_6', 'EmployeeTracker');
                                $employeetracker->image_6 = $documentPath;
                            }
                         
            $employeetracker->save();

            return response()->json(['success' => true, 'msg' => __('employeetracker::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function edit($id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'employeetracker')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.employeetracker'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $employeetracker = EmployeeTracker::find($id);
        $employeetracker = EmployeeTrackerCategory::forDropdown($business_id);
        $users = User::forDropdown($business_id);

        return response()->json([
            'categories' => $employeetracker_categories,
            'users' => $users,
            'employeetracker' => $employeetracker,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|integer', 
            
            
            
              
              
            
                            'department_1' => 'nullable',
                        

                            'employee_2' => 'nullable',
                        

                            'title_3' => 'nullable',
                        

                            'description_4' => 'nullable',
                        

                            'status_7' => 'nullable',
                         
        ]);

        try {
            $employeetracker = EmployeeTracker::find($id);
            $employeetracker->title = $request->title;
            $employeetracker->description = $request->description;
            $employeetracker->category_id = $request->category_id;
            $employeetracker->created_by = auth()->user()->id;
            
            
            
            
             
            
                            $employeetracker->department_1 = $request->department_1;
                        

                            $employeetracker->employee_2 = $request->employee_2;
                        

                            $employeetracker->title_3 = $request->title_3;
                        

                            $employeetracker->description_4 = $request->description_4;
                        

                            $employeetracker->status_7 = $request->status_7;
                         
            
                            if ($request->hasFile('file_5')) {
                                $oldFile = public_path('uploads/tracking/' . basename($employeetracker->file_5));
                                if (file_exists($oldFile)) {
                                    unlink($oldFile);
                                }
                                $documentPath = $this->transactionUtil->uploadFile($request, 'file_5', 'EmployeeTracker');
                                $employeetracker->file_5 = $documentPath;
                            }
                        

                            if ($request->hasFile('image_6')) {
                                $oldFile = public_path('uploads/tracking/' . basename($employeetracker->image_6));
                                if (file_exists($oldFile)) {
                                    unlink($oldFile);
                                }
                                $documentPath = $this->transactionUtil->uploadFile($request, 'image_6', 'EmployeeTracker');
                                $employeetracker->image_6 = $documentPath;
                            }
                        
            $employeetracker->save();

            return response()->json(['success' => true, 'msg' => __('employeetracker::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    

    public function destroy($id)
    {
        try {
            EmployeeTracker::destroy($id);
            return response()->json(['success' => true, 'msg' => __('employeetracker::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'employeetracker')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.employeetracker'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
        
        $categories = EmployeeTrackerCategory::where('business_id', $business_id)->get();
        
        return response()->json([
            'categories' => $categories,
        ]);
    
    }

    public function storeCategory(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $employeetracker = new EmployeeTrackerCategory();
            $employeetracker->name = $request->name;
            $employeetracker->description = $request->description;
            $employeetracker->business_id = $business_id;
            $employeetracker->save();

            return response()->json(['success' => true, 'msg' => __('employeetracker::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'employeetracker')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.employeetracker'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
            
        $category = EmployeeTrackerCategory::find($id);

        return response()->json([
            'category' => $category,
        ]);
    }

    public function updateCategory(Request $request, $id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $category = EmployeeTrackerCategory::find($id);
            $category->name = $request->name;
            $category->description = $request->description;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('employeetracker::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function destroyCategory($id)
    {
        try {
            EmployeeTrackerCategory::destroy($id);
            return response()->json(['success' => true, 'msg' => __('employeetracker::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
}