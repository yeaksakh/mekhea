<?php

namespace Modules\EmployeeTracker\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\User;
use App\Contact;
use App\Product;
use App\Audit;
use App\Category;
use App\BusinessLocation;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use Modules\EmployeeTracker\Entities\EmployeeTracker;
use Modules\EmployeeTracker\Entities\EmployeeTrackerCategory;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Modules\Crm\Utils\CrmUtil;
use Modules\EmployeeTracker\Entities\EmployeeTrackerActivity;
use Modules\EmployeeTracker\Entities\EmployeeTrackerFormField;
use Modules\EmployeeTracker\Entities\EmployeeTrackerSocial;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;



class EmployeeTrackerController extends Controller
{
    protected $moduleUtil;
    protected $transactionUtil;
    protected $crmUtil;

    public function __construct(
        ModuleUtil $moduleUtil,
        TransactionUtil $transactionUtil,
        CrmUtil $crmUtil
    ) {
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
        $this->crmUtil = $crmUtil;
    }

    public function dashboard()
    {
        // $business_id = request()->session()->get('user.business_id');

        // $module = ModuleCreator::where('module_name', 'employeetracker')->first();

        // $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);


        // $total_employeetracker = EmployeeTracker::where('business_id', $business_id)->count();

        // $total_employeetracker_category = EmployeeTrackerCategory::where('business_id', $business_id)->count();

        // $employeetracker_category = DB::table('employeetracker_main as employeetracker')
        //     ->leftJoin('employeetracker_category as employeetrackercategory', 'employeetracker.category_id', '=', 'employeetrackercategory.id')
        //     ->select(
        //         DB::raw('COUNT(employeetracker.id) as total'),
        //         'employeetrackercategory.name as category'
        //     )
        //     ->where('employeetracker.business_id', $business_id)
        //     ->groupBy('employeetrackercategory.id')
        //     ->get();

        // $user_id = auth()->user()->id;

        // return view('employeetracker::EmployeeTracker.dashboard')
        //     ->with(compact('total_employeetracker', 'total_employeetracker_category', 'employeetracker_category', 'module'));

        return view('employeetracker::EmployeeTracker.dashboard');
    }

    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $user_id = request()->session()->get('user.id');



        $module = ModuleCreator::where('module_name', 'employeetracker')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((! auth()->user()->can('module.employeetracker')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            // Get distinct employees who have activities, along with their department info
            $query = EmployeeTrackerActivity::join('users', 'employeetracker_activities.user_id', '=', 'users.id')
                ->join('employeetracker_main', 'employeetracker_activities.form_id', '=', 'employeetracker_main.id')
                ->leftJoin('categories as departments', 'employeetracker_main.department', '=', 'departments.id')
                ->where('users.business_id', $business_id)
                ->where('employeetracker_activities.user_id', $user_id)
                ->select([
                    'employeetracker_activities.user_id as id',
                    'employeetracker_activities.user_id',
                    'users.first_name',
                    'users.last_name',
                    'departments.name as department_name',
                    DB::raw('COUNT(employeetracker_activities.id) as activity_count'),
                    DB::raw('MAX(employeetracker_activities.created_at) as last_activity_date')
                ])
                ->groupBy('employeetracker_activities.user_id', 'users.first_name', 'users.last_name', 'departments.name', DB::raw('DATE(employeetracker_activities.created_at)'))
                ->orderBy('employeetracker_activities.created_at', 'desc');

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $query->whereDate('employeetracker_activities.created_at', '>=', $start)
                    ->whereDate('employeetracker_activities.created_at', '<=', $end);
            }

            if (!empty(request()->department_1)) {
                $query->where('employeetracker_main.department', request()->department_1);
            }

            if (!empty(request()->employee_2)) {
                $query->where('employeetracker_activities.user_id', request()->employee_2);
            }

            return DataTables::of($query)
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">';
                    $html .= '<button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">' . __("messages.actions") . '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>';
                    $html .= '<ul class="dropdown-menu dropdown-menu-right" role="menu">';
                    $html .= '<li><a href="#" data-href="' . route('EmployeeTracker.show', [$row->id]) . '" class="btn-modal" data-container="#EmployeeTracker_modal"><i class="fa fa-eye"></i> ' . __("messages.view") . '</a></li>';
                    $html .= '<li><a href="#" data-href="' . route('EmployeeTracker.edit', [$row->id]) . '" class="btn-modal" data-container="#EmployeeTracker_modal"><i class="fa fa-edit"></i> ' . __("messages.edit") . '</a></li>';
                    $html .= '<li><a href="javascript:void(0)" data-href="' . route('EmployeeTracker.destroy', [$row->id]) . '" class="delete-EmployeeTracker"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                    $html .= '</ul></div>';
                    return $html;
                })
                ->addColumn('employee', function ($row) {
                    return $row->first_name . ' ' . $row->last_name;
                })
                ->addColumn('department', function ($row) {
                    return $row->department_name ?? '';
                })
                ->addColumn('activity_count', function ($row) {
                    return $row->activity_count;
                })
                ->addColumn('last_activity', function ($row) {
                    return $row->last_activity_date ? \Carbon\Carbon::parse($row->last_activity_date)->format('Y-m-d H:i:s') : '';
                })
                ->addColumn('status', function ($row) {
                    return '<span class="label bg-green">' . __('employeetracker::lang.active') . '</span>';
                })
                ->rawColumns(['action', 'status'])
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
        $departments = Category::where('business_id', $business_id)
            ->where('category_type', 'hrm_department')
            ->pluck('name', 'id');

        $designations = Category::where('business_id', $business_id)
            ->where('category_type', 'hrm_designation')
            ->pluck('name', 'id');
        $leads = $this->crmUtil->getLeadsListQuery($business_id);

        return view('employeetracker::EmployeeTracker.index')->with(compact('module', 'leads', 'users', 'customer', 'product', 'supplier', 'business_locations',  'departments', 'designations'));
    }

    public function showQrcodeUrl($id)
    {

        $url = route('EmployeeTracker.qrcodeView', ['id' => $id]);
        $qrcode = QrCode::size(50)->generate($url);
        return $qrcode;
    }

    public function qrcodeView($id)
    {
        $checkboxes = [

            [
                'id' => 'categorycontent',
                'label' => 'employeetracker::lang.category'
            ],
            [
                'id' => 'qrcontent',
                'label' => 'employeetracker::lang.qrcode'
            ],
            [
                'id' => 'createdbycontent',
                'label' => 'employeetracker::lang.createdby'
            ],
            [
                'id' => 'createdatcontent',
                'label' => 'employeetracker::lang.createdat'
            ],
            [
                'id' => 'department_1content',
                'label' => 'employeetracker::lang.department_1',
            ],
            [
                'id' => 'employee_2content',
                'label' => 'employeetracker::lang.employee_2',
            ],
            [
                'id' => 'title_3content',
                'label' => 'employeetracker::lang.title_3',
            ],
            [
                'id' => 'description_4content',
                'label' => 'employeetracker::lang.description_4',
            ],
            [
                'id' => 'status_7content',
                'label' => 'employeetracker::lang.status_7',
            ],

        ];

        $qrcode = $this->showQrcodeUrl($id);
        $link =  route('EmployeeTracker.qrcodeView', ['id' => $id]);
        $employeetracker = EmployeeTracker::findOrFail($id);
        $createdby = User::findOrFail($employeetracker->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;
        return view('employeetracker::EmployeeTracker.qr_view')->with(compact('employeetracker', 'qrcode', 'link', 'checkboxes', 'name'));
    }

    public function create()
    {
        $business_id = request()->session()->get('user.business_id');

        // Get departments with hrm_department category type
        $departments = Category::join('employeetracker_main', 'employeetracker_main.department', '=', 'categories.id')
            ->where('category_type', 'hrm_department')
            ->select('categories.name', 'categories.id') // Explicitly select categories.id
            ->pluck('name', 'id');

        return view('employeetracker::EmployeeTracker.create', compact('departments'));
    }

    public function getUsersByDepartment(Request $request)
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            $department_id = $request->department_id;

            if (!$department_id) {
                return response()->json([]);
            }

            // Get users who belong to the selected department

            $users = User::where('business_id', $business_id)
                ->where('essentials_department_id', $department_id)
                ->where('status', 'active')
                ->select('id', DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) AS full_name"))
                ->pluck('full_name', 'id');

            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch users',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getFormFieldsByDepartment(Request $request)
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            $department_id = $request->department_id;

            if (!$department_id) {
                return response()->json([]);
            }

            // Get department name
            $department = Category::find($department_id);

            // Get form fields for this department
            // You might need to create a relationship between departments and form configurations
            // For now, let's get all form fields and filter by department later
            $formFields = EmployeeTrackerFormFields::whereHas('employeeTrackerMain', function ($query) use ($business_id, $department) {
                $query->where('business_id', $business_id)
                    ->where('department', $department->name);
            })
                ->orderBy('field_order')
                ->get();

            return response()->json($formFields);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch form fields',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $business_id = $request->session()->get('user.business_id');
            $created_by = $request->session()->get('user.id');
            $department_id = $request->input('department_1');
            $employee_id = $request->input('employee_2');

            // Basic validation
            $validator = \Validator::make($request->all(), [
                'department_1' => 'required|exists:categories,id',
                'employee_2' => 'required|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'msg' => $validator->errors()->first()], 422);
            }

            // Step 1: Find the form configuration associated with the department
            $tracker_config = EmployeeTracker::where('department', $department_id)
                ->where('business_id', $business_id)
                ->first();

            // Step 2: If no configuration exists, return error
            if (!$tracker_config) {
                return response()->json(['success' => false, 'msg' => __('employeetracker::lang.no_form_found')]);
            }

            // Step 3: Fetch the valid form fields for this configuration
            $form_fields = EmployeeTrackerFormField::where('form_id', $tracker_config->id)->get();

            if ($form_fields->isEmpty()) {
                return response()->json(['success' => false, 'msg' => __('employeetracker::lang.no_fields_configured')]);
            }

            DB::beginTransaction();

            // Step 4: Add batch identifier to group this submission and prevent duplicates
            $batch_id = uniqid('batch_', true);

            // Step 5: Process and save the dynamic fields as NEW activities (no deletion)
            if ($request->has('dynamic_fields')) {
                $dynamic_fields = $request->input('dynamic_fields');

                foreach ($form_fields as $field) {
                    $field_id = $field->id;

                    // REMOVED: No deletion of existing data - we're adding new entries

                    // Handle file uploads (including multiple files)
                    if ($request->hasFile("dynamic_fields.{$field_id}")) {
                        $files = $request->file("dynamic_fields.{$field_id}");

                        // If it's not an array, make it one for consistent handling
                        if (!is_array($files)) {
                            $files = [$files];
                        }

                        foreach ($files as $file) {
                            if ($file && $file->isValid()) {
                                // Upload file
                                $uploaded_file_path = $this->uploadFileObject($file, 'EmployeeTracker');

                                // Create NEW activity entry
                                if ($uploaded_file_path) {
                                    // Check if this exact entry already exists
                                    $existing = EmployeeTrackerActivity::where([
                                        'form_id' => $tracker_config->id,
                                        'field_id' => $field_id,
                                        'user_id' => $employee_id,
                                        'value' => $uploaded_file_path,
                                    ])->whereDate('created_at', today())->first();

                                    if (!$existing) {
                                        EmployeeTrackerActivity::create([
                                            'form_id' => $tracker_config->id,
                                            'field_id' => $field_id,
                                            'user_id' => $employee_id,
                                            'value' => $uploaded_file_path,
                                            'created_by' => $created_by,
                                            'created_at' => now(),
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                    // Handle submitted values (including empty strings)
                    else if (isset($dynamic_fields[$field_id])) {
                        $submitted_value = $dynamic_fields[$field_id];

                        // Handle array values (like multiple selections)
                        if (is_array($submitted_value)) {
                            // For array values, create separate records for each value
                            foreach ($submitted_value as $value) {
                                if ($value !== null && $value !== '') {
                                    // Check if this exact entry already exists
                                    $existing = EmployeeTrackerActivity::where([
                                        'form_id' => $tracker_config->id,
                                        'field_id' => $field_id,
                                        'user_id' => $employee_id,
                                        'value' => $value,
                                    ])->whereDate('created_at', today())->first();

                                    if (!$existing) {
                                        EmployeeTrackerActivity::create([
                                            'form_id' => $tracker_config->id,
                                            'field_id' => $field_id,
                                            'user_id' => $employee_id,
                                            'value' => $value,
                                            'created_by' => $created_by,
                                            'created_at' => now(),
                                        ]);
                                    }
                                }
                            }
                        } else {
                            // Single value
                            $value_to_save = $submitted_value;

                            // Create NEW activity entry for meaningful values
                            if ($value_to_save !== null && $value_to_save !== '') {
                                // Check if this exact entry already exists
                                $existing = EmployeeTrackerActivity::where([
                                    'form_id' => $tracker_config->id,
                                    'field_id' => $field_id,
                                    'user_id' => $employee_id,
                                    'value' => $value_to_save,
                                ])->whereDate('created_at', today())->first();

                                if (!$existing) {
                                    EmployeeTrackerActivity::create([
                                        'form_id' => $tracker_config->id,
                                        'field_id' => $field_id,
                                        'user_id' => $employee_id,
                                        'value' => $value_to_save,
                                        'created_by' => $created_by,
                                        'created_at' => now(),
                                    ]);
                                }
                            }
                        }
                    }
                    // Handle checkboxes that were not submitted (i.e., unchecked)
                    else if ($field->field_type === 'checkbox') {
                        // Check if this exact entry already exists
                        $existing = EmployeeTrackerActivity::where([
                            'form_id' => $tracker_config->id,
                            'field_id' => $field_id,
                            'user_id' => $employee_id,
                            'value' => '0',
                        ])->whereDate('created_at', today())->first();

                        if (!$existing) {
                            EmployeeTrackerActivity::create([
                                'form_id' => $tracker_config->id,
                                'field_id' => $field_id,
                                'user_id' => $employee_id,
                                'value' => '0',
                                'created_by' => $created_by,
                                'created_at' => now(),
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return response()->json(['success' => true, 'msg' => __('employeetracker::lang.saved_successfully')]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Employee Tracker Store Error: ' . $e->getMessage() . ' in file ' . $e->getFile() . ' on line ' . $e->getLine());
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')], 500);
        }
    }

    /**
     * Helper method for handling file uploads when dealing with multiple files
     * This method should be added if your transactionUtil doesn't handle individual file objects
     */
    private function uploadFileObject($file, $folder = 'EmployeeTracker')
    {
        if (!$file || !$file->isValid()) {
            return null;
        }

        try {
            $business_id = request()->session()->get('user.business_id');

            // If you have a transactionUtil, try to use it first
            if (isset($this->transactionUtil) && method_exists($this->transactionUtil, 'uploadFile')) {
                // Create a temporary request with the file
                $tempRequest = new \Illuminate\Http\Request();
                $tempRequest->files->set('temp_file', $file);
                return $this->transactionUtil->uploadFile($tempRequest, 'temp_file', $folder);
            }

            // Fallback to manual upload
            $upload_path = public_path('uploads') . '/' . $business_id . '/' . $folder;

            // Create directory if it doesn't exist
            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0755, true);
            }

            // Generate unique filename
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Move the file
            $file->move($upload_path, $filename);

            return $filename; // Return just the filename, not the full path

        } catch (\Exception $e) {
            \Log::error('File upload error: ' . $e->getMessage());
            return null;
        }
    }

    public function show($id)
    {
        try {
            $business_id = request()->session()->get('user.business_id');

            // Get the employee/user
            $employee = User::findOrFail($id);

            // Get all activities for this employee
            $activities = EmployeeTrackerActivity::where('user_id', $id)
                ->with(['form', 'field'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Group activities by form
            $grouped_activities = $activities->groupBy('form_id');

            return view('employeetracker::EmployeeTracker.show')->with(compact('employee', 'grouped_activities'));
        } catch (\Exception $e) {
            \Log::error('Employee Tracker Show Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')], 500);
        }
    }


    public function edit($id)
    {
        try {
            $business_id = request()->session()->get('user.business_id');

            // Get the employee/user
            $employee = User::findOrFail($id);

            // Get departments for the dropdown
            $departments = Category::where('business_id', $business_id)
                ->where('category_type', 'hrm_department')
                ->pluck('name', 'id');

            // Get existing activities to pre-fill form
            $activities = EmployeeTrackerActivity::where('user_id', $id)
                ->with(['form', 'field'])
                ->get()
                ->keyBy('field_id'); // Key by field_id for easy lookup

            // Try to determine the employee's current department from their profile or latest form
            // You might adjust this based on how you store department assignment
            $current_department_id = null;

            // Example: if the employee's department is stored in `User` model
            // If not, infer from latest EmployeeTrackerActivity
            if (!empty($employee->department_id)) {
                $current_department_id = $employee->department_id;
            } elseif ($activities->isNotEmpty()) {
                $current_department_id = $activities->first()->form->department ?? null;
            }

            return view('employeetracker::EmployeeTracker.edit')
                ->with(compact('employee', 'departments', 'activities', 'current_department_id'));
        } catch (\Exception $e) {
            \Log::error('Employee Tracker Edit Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')], 500);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $business_id = $request->session()->get('user.business_id');
            $employee_id = $id;
            $department_id = $request->input('department_1');

            // Basic validation
            $validator = \Validator::make($request->all(), [
                'department_1' => 'required|exists:categories,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'msg' => $validator->errors()->first()], 422);
            }

            // Find the existing form configuration associated with the department
            $tracker_config = EmployeeTracker::where('department', $department_id)
                ->where('business_id', $business_id)
                ->first();

            if (!$tracker_config) {
                return response()->json(['success' => false, 'msg' => __('employeetracker::lang.no_form_found')]);
            }

            // Fetch the valid form fields for this configuration
            $form_fields = EmployeeTrackerFormField::where('form_id', $tracker_config->id)->get();

            if ($form_fields->isEmpty()) {
                return response()->json(['success' => false, 'msg' => __('employeetracker::lang.no_fields_configured')]);
            }

            DB::beginTransaction();

            // Process and update the dynamic fields
            if ($request->has('dynamic_fields')) {
                $dynamic_fields = $request->input('dynamic_fields');

                foreach ($form_fields as $field) {
                    $field_id = $field->id;
                    $value_to_save = null;

                    // Handle file uploads first
                    if ($request->hasFile("dynamic_fields.{$field_id}")) {
                        $value_to_save = $this->transactionUtil->uploadFile($request, "dynamic_fields.{$field_id}", 'EmployeeTracker');
                    }
                    // Handle submitted values
                    else if (isset($dynamic_fields[$field_id])) {
                        $submitted_value = $dynamic_fields[$field_id];
                        if (is_array($submitted_value)) {
                            $value_to_save = implode(', ', $submitted_value);
                        } else {
                            $value_to_save = $submitted_value;
                        }
                    }
                    // Handle checkboxes that were not submitted
                    else if ($field->field_type === 'checkbox') {
                        $value_to_save = '0';
                    }

                    if (!is_null($value_to_save)) {
                        // Check if activity already exists
                        $existing_activity = EmployeeTrackerActivity::where('form_id', $tracker_config->id)
                            ->where('field_id', $field_id)
                            ->where('user_id', $employee_id)
                            ->first();

                        if ($existing_activity) {
                            // Update existing activity
                            $existing_activity->update([
                                'value' => $value_to_save,
                            ]);
                        } else {
                            // Create new activity
                            EmployeeTrackerActivity::create([
                                'form_id' => $tracker_config->id,
                                'field_id' => $field_id,
                                'user_id' => $employee_id,
                                'value' => $value_to_save,
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return response()->json(['success' => true, 'msg' => __('employeetracker::lang.updated_successfully')]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Employee Tracker Update Error: ' . $e->getMessage() . ' in file ' . $e->getFile() . ' on line ' . $e->getLine());
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')], 500);
        }
    }



    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Delete all activities for this employee
            EmployeeTrackerActivity::where('user_id', $id)->delete();

            DB::commit();

            return response()->json(['success' => true, 'msg' => __('employeetracker::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Employee Tracker Delete Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')], 500);
        }
    }

    public function getCategories(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'employeetracker')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((! auth()->user()->can('module.employeetracker')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $categories = EmployeeTrackerCategory::where('business_id', $business_id)->orderBy('id', 'desc')->get();

            return DataTables::of($categories)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('EmployeeTracker-categories.edit', $row->id) . '" data-container=".category_modal"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-category" data-href="' . route('EmployeeTracker-categories.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('employeetracker::Category.index')->with(compact('module'));
    }

    public function createCategory()
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'employeetracker')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((! auth()->user()->can('module.employeetracker')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        return view('employeetracker::Category.create');
    }

    public function storeCategory(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = new EmployeeTrackerCategory();
            $category->name = $request->name;
            if ($request->hasFile('image')) {
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'EmployeeTrackerCategory');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->business_id = $business_id;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('employeetracker::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'employeetracker')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((! auth()->user()->can('module.employeetracker')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $category = EmployeeTrackerCategory::find($id);
        return view('employeetracker::Category.edit', compact('category'));
    }

    public function updateCategory(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = EmployeeTrackerCategory::find($id);
            $category->name = $request->name;
            $category->business_id = $business_id;
            if ($request->hasFile('image')) {
                $oldFile = public_path('uploads/tracking/' . basename($category->{'image'}));
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'EmployeeTrackerCategory');
                $category->{'image'} = $documentPath;
            }
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

    public function indexActivityForm()
    {
        return view('employeetracker::EmployeeTracker.indexActivityForm');
    }

    public function getFormFields($department_id)
    {
        $business_id = request()->session()->get('user.business_id');

        // Find the main tracker configuration for the given department
        $tracker_config = EmployeeTracker::where('department', $department_id)
            ->where('business_id', $business_id)
            ->first();

        if ($tracker_config) {
            // If a configuration is found, get its associated form fields
            $fields = EmployeeTrackerFormField::where('form_id', $tracker_config->id)
                ->orderBy('field_order')
                ->get();
            return response()->json($fields);
        }

        // If no specific configuration is found, return an empty array
        return response()->json([]);
    }
}
