<?php

namespace Modules\SchedulePayment\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Contact;
use App\Product;
use Yajra\DataTables\Facades\DataTables;
use Modules\SchedulePayment\Entities\SchedulePayment;
use Modules\SchedulePayment\Entities\SchedulePaymentCategory;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Illuminate\Support\Facades\Auth;
use App\Utils\ModuleUtil;
use Illuminate\Support\Facades\Schema;
use App\Utils\TransactionUtil;

class SchedulePaymentController extends Controller
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
        $tableName = 'schedulepayment_main';

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
            $additionalColumns = json_decode('[{"name":"title_1","type":"string"},{"name":"date_paid_5","type":"date"},{"name":"date_prepare_pay_6","type":"date"},{"name":"status_7","type":"status_true_false"},{"name":"note_8","type":"text"}]', true);

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

        $module = ModuleCreator::where('module_name', 'schedulepayment')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.schedulepayment'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

       $SchedulePayment = SchedulePayment::where('schedulepayment_main.business_id', $business_id)
        ->leftJoin('schedulepayment_category as schedulepaymentcategory', 'schedulepayment_main.category_id', '=', 'schedulepaymentcategory.id')
        ->where('schedulepayment_main.business_id', $business_id)
        ->select('schedulepayment_main.*', 'schedulepaymentcategory.name as category_name');

         if (!empty(request()->start_date) && !empty(request()->end_date)) {
            $start = request()->start_date;
            $end = request()->end_date;
            $SchedulePayment->whereDate('created_at', '>=', $start)
                ->whereDate('created_at', '<=', $end);
        }

        
        

        $result = $SchedulePayment->get();

        return response()->json($result);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'schedulepayment')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.schedulepayment'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $schedulepayment_categories = SchedulePaymentCategory::forDropdown($business_id);
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
            'categories' => $schedulepayment_categories,
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
                        

                            'date_paid_5' => 'nullable',
                        

                            'date_prepare_pay_6' => 'nullable',
                        

                            'status_7' => 'nullable',
                        

                            'note_8' => 'nullable',
                                                    
        ]);

        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $schedulepayment = new SchedulePayment();
            $schedulepayment->title = $request->title;
            $schedulepayment->description = $request->description;
            $schedulepayment->business_id = $business_id;
            $schedulepayment->category_id = $request->category_id;
            $schedulepayment->created_by = auth()->user()->id;
            
            
            
              
             
            
                            $schedulepayment->title_1 = $request->title_1;
                        

                            $schedulepayment->date_paid_5 = $request->date_paid_5;
                        

                            $schedulepayment->date_prepare_pay_6 = $request->date_prepare_pay_6;
                        

                            $schedulepayment->status_7 = $request->status_7;
                        

                            $schedulepayment->note_8 = $request->note_8;
                         
             
            $schedulepayment->save();

            return response()->json(['success' => true, 'msg' => __('schedulepayment::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function edit($id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'schedulepayment')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.schedulepayment'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $schedulepayment = SchedulePayment::find($id);
        $schedulepayment = SchedulePaymentCategory::forDropdown($business_id);
        $users = User::forDropdown($business_id);

        return response()->json([
            'categories' => $schedulepayment_categories,
            'users' => $users,
            'schedulepayment' => $schedulepayment,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|integer', 
            
            
            
              
              
            
                            'title_1' => 'nullable',
                        

                            'date_paid_5' => 'nullable',
                        

                            'date_prepare_pay_6' => 'nullable',
                        

                            'status_7' => 'nullable',
                        

                            'note_8' => 'nullable',
                         
        ]);

        try {
            $schedulepayment = SchedulePayment::find($id);
            $schedulepayment->title = $request->title;
            $schedulepayment->description = $request->description;
            $schedulepayment->category_id = $request->category_id;
            $schedulepayment->created_by = auth()->user()->id;
            
            
            
            
             
            
                            $schedulepayment->title_1 = $request->title_1;
                        

                            $schedulepayment->date_paid_5 = $request->date_paid_5;
                        

                            $schedulepayment->date_prepare_pay_6 = $request->date_prepare_pay_6;
                        

                            $schedulepayment->status_7 = $request->status_7;
                        

                            $schedulepayment->note_8 = $request->note_8;
                         
            
            $schedulepayment->save();

            return response()->json(['success' => true, 'msg' => __('schedulepayment::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    

    public function destroy($id)
    {
        try {
            SchedulePayment::destroy($id);
            return response()->json(['success' => true, 'msg' => __('schedulepayment::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'schedulepayment')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.schedulepayment'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
        
        $categories = SchedulePaymentCategory::where('business_id', $business_id)->get();
        
        return response()->json([
            'categories' => $categories,
        ]);
    
    }

    public function storeCategory(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $schedulepayment = new SchedulePaymentCategory();
            $schedulepayment->name = $request->name;
            $schedulepayment->description = $request->description;
            $schedulepayment->business_id = $business_id;
            $schedulepayment->save();

            return response()->json(['success' => true, 'msg' => __('schedulepayment::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'schedulepayment')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.schedulepayment'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
            
        $category = SchedulePaymentCategory::find($id);

        return response()->json([
            'category' => $category,
        ]);
    }

    public function updateCategory(Request $request, $id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $category = SchedulePaymentCategory::find($id);
            $category->name = $request->name;
            $category->description = $request->description;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('schedulepayment::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function destroyCategory($id)
    {
        try {
            SchedulePaymentCategory::destroy($id);
            return response()->json(['success' => true, 'msg' => __('schedulepayment::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
}