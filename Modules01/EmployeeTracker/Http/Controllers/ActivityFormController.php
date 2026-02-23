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
use Modules\EmployeeTracker\Entities\EmployeeTrackerSocial;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Modules\EmployeeTracker\Entities\EmployeeTrackerFormField;

class ActivityFormController extends Controller
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


    public function indexActivityForm()
    {
        return view('employeetracker::Activity.indexActivityForm');
    }


    public function create()
    {
        $business_id = request()->session()->get('user.business_id');
        // dd($business_id);
        $departments = Category::where('business_id', $business_id)
            ->where('category_type', 'hrm_department')
            ->pluck('name', 'id');

        // dd($departments['items']);
        $users = User::forDropdown($business_id, false);

        return view('employeetracker::Activity.createActivityForm', compact( 'departments', 'users'));
    }

    public function store(Request $request)
    {
        try {
            $business_id = $request->session()->get('user.business_id');
            $user_id = $request->session()->get('user.id');
            $form_data = $request->input('form_data');


            DB::beginTransaction();

            // Create the main form entry
            $employeeTracker = EmployeeTracker::create([
                'business_id' => $business_id,
                'name' => $form_data['name'],
                'description' => $form_data['description'],
                'created_by' => $user_id,
                'department' => $form_data['department'],
            ]);

            foreach ($form_data['fields'] as $index => $field) {
                EmployeeTrackerFormField::create([
                    'form_id' => $employeeTracker->id,
                    'field_label' => $field['label'],
                    'field_type' => $field['type'],
                    'field_order' => $index + 1,
                    'is_required' => $field['required'] ?? false,
                    'config' => isset($field['options']) ? json_encode(['options' => $field['options']]) : null,
                ]);
            }

            DB::commit();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong' . $e)
            ];
        }

        return response()->json($output);
    }

    public function fetchForms(Request $request)
    {
        if ($request->ajax()) {
            $business_id = $request->session()->get('user.business_id');

            $forms = EmployeeTracker::leftJoin('categories', 'categories.id', '=', 'employeetracker_main.department')
                ->leftJoin('users', 'users.id', '=', 'employeetracker_main.created_by')
                ->select(
                    'employeetracker_main.id',
                    'employeetracker_main.name',
                    'employeetracker_main.description',
                    'categories.name as department',
                    DB::raw("CONCAT(COALESCE(users.surname, ''), ' ', COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, '')) AS created_by"),
                    'employeetracker_main.created_at'
                )
                ->where('employeetracker_main.business_id', $business_id)
                ->get();

            return DataTables::of($forms)
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                <li><a href="#" data-href="' . route('activity-form.show', [$row->id]) . '" class="btn-modal" data-container="#EmployeeTracker_modal"><i class="fa fa-eye"></i> ' . __("messages.view") . '</a></li>
                                <li><a href="#" data-href="' . route('activity-form.edit', [$row->id]) . '" class="btn-modal" data-container="#EmployeeTracker_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>
                                <li><a href="#" data-href="' . route('activity-form.destroy', [$row->id]) . '" class="delete-form"><i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") . '</a></li>
                            </ul>
                        </div>';
                    return $html;
                })
                ->addColumn('title', function ($row) {
                    return $row->name;
                })
                ->addColumn('description', function ($row) {
                    return Str::limit($row->description, 50);
                })
                ->addColumn('department', function ($row) {
                    return $row->department;
                })
                ->addColumn('created_by', function ($row) {
                    return $row->created_by;
                })
                ->addColumn('field_count', function ($row) {
                    return \DB::table('employeetracker_form_fields')
                        ->where('form_id', $row->id)
                        ->count();
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function show($id)
    {
        $form = EmployeeTracker::with('fields', 'department', 'createdBy')->findOrFail($id);
        return view('employeetracker::Activity.show', compact('form'));
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

            if (!$department) {
                return response()->json([]);
            }

            // Get form fields for this department - Fix the class name
            $formFields = EmployeeTrackerFormField::whereHas('form', function ($query) use ($business_id, $department) {
                $query->where('business_id', $business_id)
                    ->where('department', $department->name);
            })
                ->orderBy('field_order')
                ->get();

            return response()->json($formFields);
        } catch (\Exception $e) {
            Log::error('Error fetching form fields by department: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch form fields',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $business_id = request()->session()->get('user.business_id');

            // Check if business_id exists
            if (!$business_id) {
                return redirect()->back()->with('error', 'Business information not found.');
            }

            // Find the form ensuring it belongs to the business
            $form = EmployeeTracker::where('business_id', $business_id)
                ->findOrFail($id);

            // Explicitly load fields (alternative approach)
            $form->setRelation('fields', EmployeeTrackerFormField::where('form_id', $form->id)
                ->orderBy('field_order')
                ->get());

            // Debug: Log the form data to see what's being loaded
            Log::info('Form loaded for editing:', [
                'form_id' => $form->id,
                'form_name' => $form->name,
                'fields_count' => $form->fields ? $form->fields->count() : 0,
                'fields_data' => $form->fields ? $form->fields->toArray() : 'No fields',
                'department' => $form->department
            ]);

            // Get departments for the business
            $departments = Category::where('business_id', $business_id)
                ->where('category_type', 'hrm_department')
                ->pluck('name', 'id');

            // Return the view
            return view('employeetracker::Activity.edit', compact('form', 'departments'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Form not found in ActivityFormController@edit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Form not found or access denied.');
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error in ActivityFormController@edit: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return redirect()->back()->with('error', 'An error occurred while loading the form.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // Validation
            $request->validate([
                'form_data' => 'required|array',
                'form_data.name' => 'required|string|max:255',
                'form_data.fields' => 'required|array|min:1',
            ]);

            $business_id = $request->session()->get('user.business_id');
            $user_id = $request->session()->get('user.id');
            $form_data = $request->input('form_data');

            // Check if business_id and user_id are available
            if (!$business_id || !$user_id) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Session data missing. Please log in again.'
                ]);
            }

            DB::beginTransaction();

            // Find the form and ensure it belongs to the business
            $employeeTracker = EmployeeTracker::where('business_id', $business_id)
                ->findOrFail($id);

            // Update the main form
            $employeeTracker->update([
                'name' => $form_data['name'],
                'description' => $form_data['description'] ?? '',
                'department' => $form_data['department'] ?? null, // This should now be the department ID or name
                'updated_by' => $user_id, // Add this if you have this column
            ]);

            // Log the form update
            Log::info('Form updated:', ['id' => $employeeTracker->id, 'name' => $form_data['name']]);

            // Delete old fields
            EmployeeTrackerFormField::where('form_id', $employeeTracker->id)->delete();

            // Create new fields
            foreach ($form_data['fields'] as $index => $field) {
                $fieldData = [
                    'form_id' => $employeeTracker->id,
                    'field_label' => $field['label'],
                    'field_type' => $field['type'],
                    'field_order' => $index + 1,
                    'is_required' => $field['required'] ?? false,
                    'config' => isset($field['options']) && !empty($field['options'])
                        ? json_encode(['options' => $field['options']])
                        : null,
                ];

                EmployeeTrackerFormField::create($fieldData);
                Log::info('Field updated:', $fieldData);
            }

            DB::commit();

            $output = [
                'success' => true,
                'msg' => 'Form updated successfully!'
            ];
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error:', $e->errors());
            $output = [
                'success' => false,
                'msg' => 'Validation failed: ' . implode(', ', array_flatten($e->errors()))
            ];
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::error('Form not found for update: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => 'Form not found or access denied.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Form update error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            $output = [
                'success' => false,
                'msg' => 'Something went wrong: ' . $e->getMessage()
            ];
        }

        return response()->json($output);
    }



    public function destroy($id)
    {
        try {
            $business_id = request()->session()->get('user.business_id');

            $form = EmployeeTracker::where('business_id', $business_id)->findOrFail($id);

            DB::beginTransaction();

            // Delete associated form fields first
            EmployeeTrackerFormField::where('form_id', $form->id)->delete();

            // Delete the main form
            $form->delete();

            DB::commit();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.deleted_success')
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return response()->json($output);
    }
}
