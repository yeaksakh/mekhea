<?php

namespace Modules\EmployeeCardB1\Http\Controllers;

use DB;
use App\User;
use App\Media;
use App\Contact;
use App\Business;
use App\Category;
use App\CustomerGroup;
use App\DocumentAndNote;
use App\BusinessLocation;
use App\Utils\ModuleUtil;
use App\Utils\ContactUtil;
use Illuminate\Http\Request;
use App\Utils\TransactionUtil;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Events\UserCreatedOrModified;
use Modules\Essentials\Entities\ToDo;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;
use Modules\AssetManagement\Entities\Asset;
use Modules\EmployeeCardB1\Entities\VisaAppraisal;
use Modules\AssetManagement\Entities\AssetTransaction;

class ManageUserController extends Controller
{
    /**
     * Constructor
     *
     * @param  Util  $commonUtil
     * @return void
     */
    protected $moduleUtil;
    protected $contactUtil;
    protected $transactionUtil;
    public function __construct(ModuleUtil $moduleUtil,ContactUtil $contactUtil, TransactionUtil $transactionUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->contactUtil = $contactUtil;
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('user.view') && !auth()->user()->can('user.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $roles = $this->getRolesArray($business_id);
        $usersQuery = User::where('business_id', $business_id)->get();

        if (request()->ajax()) {
            $user_id = request()->session()->get('user.id');

            // Start the base query to get users
            $usersQuery = User::where('business_id', $business_id)
                ->user()
                ->where('is_cmmsn_agnt', 0)
                ->select([
                    'id',
                    DB::raw("CONCAT('EMP-', id) as employee_id"),
                    'username',
                    DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"),
                    'email',
                    'allow_login'
                ])
                ->when(request('allow_login'), function ($query) {
                    // Apply filter for allow_login
                    return $query->where('allow_login', request('allow_login'));
                })
                ->when(request('not_allow_login'), function ($query) {
                    // Apply filter for allow_login
                    return $query->where('allow_login', request('allow_login'));
                })
                ->when(request('id'), function ($query) {
                    // Apply filter for first_name and last_name
                    return $query->where(function ($query) {
                        $query->where('id', request('id'));
                    });
                })
                ->when(request('role'), function ($query) {
                    // Apply filter for role
                    return $query->whereHas('roles', function ($q) {
                        $q->where('role_id', request('role'));
                    });
                });


            return Datatables::of($usersQuery)
                ->editColumn('username', '{{$username}} @if(empty($allow_login)) <span class="label bg-gray">@lang("lang_v1.login_not_allowed")</span>@endif')
                ->addColumn('role', function ($row) {
                    return $this->moduleUtil->getUserRoleName($row->id);
                })
                ->addColumn('action', 
                    '@can("user.view")
                    <a href="{{action(\'Modules\EmployeeCardB1\Http\Controllers\ManageUserController@show\', [$id])}}" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-info"><i class="fa fa-eye"></i> @lang("messages.view")</a>
                    &nbsp;
                    @endcan')
                ->filterColumn('full_name', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->filterColumn('employee_id', function ($query, $keyword) {
                    $query->whereRaw("CONCAT('EMP-', id) like ?", ["%{$keyword}%"]);
                })
                ->removeColumn('id')
                ->rawColumns(['action', 'username'])
                ->make(true);
        }

        return view('employeecardb1::user.index')->with(compact('roles', 'usersQuery'));
    }
    public function getUserAsset($userId)
    {
        $business_id = request()->session()->get('user.business_id');

        // Subquery for allocated assets
        $allocated = AssetTransaction::where('business_id', $business_id)
            ->where('transaction_type', 'allocate')
            ->where('receiver', $userId)
            ->select('asset_id', DB::raw('SUM(COALESCE(quantity, 0)) as allocated'))
            ->groupBy('asset_id');

        // Subquery for revoked assets
        $revoked = AssetTransaction::where('business_id', $business_id)
            ->where('transaction_type', 'revoke')
            ->where('receiver', $userId)
            ->select('asset_id', DB::raw('SUM(COALESCE(quantity, 0)) as revoked'))
            ->groupBy('asset_id');

        // Subquery to get the latest allocated_upto date
        $latestAllocations = AssetTransaction::where('business_id', $business_id)
            ->where('transaction_type', 'allocate')
            ->where('receiver', $userId)
            ->select(
                'asset_id',
                DB::raw('MAX(allocated_upto) as allocated_upto')
            )
            ->groupBy('asset_id');

        // Main query with category join
        $assets = Asset::leftJoin('categories', 'assets.category_id', '=', 'categories.id')
            ->leftJoinSub($allocated, 'allocated', function ($join) {
                $join->on('assets.id', '=', 'allocated.asset_id');
            })
            ->leftJoinSub($revoked, 'revoked', function ($join) {
                $join->on('assets.id', '=', 'revoked.asset_id');
            })
            ->leftJoinSub($latestAllocations, 'latest_alloc', function ($join) {
                $join->on('assets.id', '=', 'latest_alloc.asset_id');
            })
            ->where('assets.business_id', $business_id)
            ->select(
                'assets.*',
                'categories.name as category_name', // Add category name to selection
                DB::raw('COALESCE(allocated.allocated, 0) - COALESCE(revoked.revoked, 0) as net_allocated'),
                'latest_alloc.allocated_upto'
            )
            ->havingRaw('net_allocated > 0')
            ->get();

        return $assets;
    }


    public function getUserTasks($user_id)
{
    $business_id = request()->session()->get('user.business_id');
    
    try {
        $todos = ToDo::where('business_id', $business_id)
            ->with(['users', 'assigned_by'])
            ->whereHas('users', function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            })
            ->select('*')
            ->get();

        // Format the response
        $formatted_tasks = [];
        foreach ($todos as $task) {
            $formatted_tasks[] = [
                'id' => $task->id,
                'task' => $task->task,
                'description' => $task->description,
                'status' => $task->status,
                'priority' => $task->priority,
                'date' => $task->date,
                'end_date' => $task->end_date,
                'created_at' => $task->created_at,
                'assigned_by' => $task->assigned_by ? $task->assigned_by->user_full_name : null,
                'users' => $task->users->pluck('user_full_name')->toArray()
            ];
        }

        return [
            'success' => true,
            'tasks' => $formatted_tasks,
            'msg' => __('lang_v1.success')
        ];

    } catch (Exception $e) {
        \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

        return [
            'success' => false,
            'msg' => __('messages.something_went_wrong')
        ];
    }
}


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('user.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not, then check for users quota
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        } elseif (!$this->moduleUtil->isQuotaAvailable('users', $business_id)) {
            return $this->moduleUtil->quotaExpiredResponse('users', $business_id, action([\App\Http\Controllers\ManageUserController::class, 'index']));
        }

        $roles = $this->getRolesArray($business_id);
        $username_ext = $this->moduleUtil->getUsernameExtension();
        $locations = BusinessLocation::where('business_id', $business_id)
            ->Active()
            ->get();

        //Get user form part from modules
        $form_partials = $this->moduleUtil->getModuleData('moduleViewPartials', ['view' => 'manage_user.create']);

        return view('manage_user.create')
            ->with(compact('roles', 'username_ext', 'locations', 'form_partials'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('user.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (! empty($request->input('dob'))) {
                $request['dob'] = $this->moduleUtil->uf_date($request->input('dob'));
            }

            $request['cmmsn_percent'] = ! empty($request->input('cmmsn_percent')) ? $this->moduleUtil->num_uf($request->input('cmmsn_percent')) : 0;

            $request['max_sales_discount_percent'] = ! is_null($request->input('max_sales_discount_percent')) ? $this->moduleUtil->num_uf($request->input('max_sales_discount_percent')) : null;

            $user = $this->moduleUtil->createUser($request);
            Media::uploadMedia($user->business_id, $user, request(), 'profile_photo', true);
            if ($request->hasFile('sign_image')) {
                $sign_image_path = $this->moduleUtil->uploadFile($request, 'sign_image', 'sign_images', 'image');
                $user->sign_image = $sign_image_path;
                $user->save();
            }

            event(new UserCreatedOrModified($user, 'added'));

            $output = ['success' => 1,
                'msg' => __('user.user_added'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return redirect('users')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('user.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // Fetch business
        $business = Business::where('id', $business_id)->first();

        // Fetch user with contact access
        $user = User::where('business_id', $business_id)
            ->with(['contactAccess'])
            ->findOrFail($id);

        // Count documents and notes
        $document_note_count = DocumentAndNote::where('business_id', $business_id)
            ->where(function ($query) use ($id) {
                $query->where('is_private', 0)
                    ->orWhere(function ($q) use ($id) {
                        $q->where('is_private', 1)
                            ->where('created_by', $id);
                    });
            })
            ->count();

        // Get user view partials from modules
        $view_partials = $this->moduleUtil->getModuleData('moduleViewPartials', ['view' => 'manage_user.show', 'user' => $user]);

        $type = 'customer';
        $reward_enabled = (request()->session()->get('business.enable_rp') == 1 && in_array($type, ['customer'])) ? true : false;
        $users = User::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);

        $customer_groups = [];
        if ($type == 'customer') {
            $customer_groups = CustomerGroup::forDropdown($business_id);
        }

        $userAssets = $this->getUserAsset($id);
        $user->assets = $userAssets;

        $activities = Activity::forSubject($user)
            ->with(['causer', 'subject'])
            ->latest()
            ->get();
        $work_location = BusinessLocation::find($user->location_id);
        $user_department = Category::find($user->essentials_department_id);
        $user_designation = Category::find($user->essentials_designation_id);

        // current shift of the shown user (no model relation used)
        $userShift = \Illuminate\Support\Facades\DB::table('essentials_user_shifts as us')
            ->join('essentials_shifts as s', 's.id', '=', 'us.essentials_shift_id')
            ->where('us.user_id', $user->id)
            ->where('s.business_id', $business_id)
            ->where(function ($q) {
                $q->whereNull('us.end_date')
                ->orWhere('us.end_date', '>=', now()->format('Y-m-d'));
            })
            ->select('s.name', 'us.start_date', 'us.end_date')
            ->first();

        return view('employeecardb1::user.show')->with(compact(
            'customers',
            'customer_groups',
            'type',
            'reward_enabled',
            'user',
            'view_partials',
            'users',
            'activities',
            'document_note_count',
            'business',
            'work_location',
            'user_department',
            'user_designation',
            'userShift'
        ));
    }

    public function getVisaAppraisals(Request $request, $user_id)
    {
        if ($request->ajax()) {
            // Check permission
            
            // Query appraisals
            $query = VisaAppraisal::with(['contact', 'createdBy', 'scores'])
                ->where('created_by', $user_id);

            // Apply month/year filter
            if ($request->has('month') && $request->has('year')) {
                $appraisalMonth = $request->year . '-' . str_pad($request->month, 2, '0', STR_PAD_LEFT);
                $query->where('appraisal_month', $appraisalMonth);
            }

            // DataTable response with server-side processing
            return DataTables::of($query)
                ->addIndexColumn() // Ensures DT_RowIndex is added
                ->addColumn('contact', function ($row) {
                    return isset($row->contact) ? $row->contact->name : null;
                })
                ->addColumn('appraisal_month', function ($row) {
                    return $row->appraisal_month ?? '-';
                })
                ->addColumn('actual_value', function ($row) {
                    return $row->scores->sum(function ($score) {
                        return $score->actual_value ?: 0;
                    }) ?? 0;
                })
                ->addColumn('created_by', function ($row) {
                    return isset($row->createdBy) 
                        ? $row->createdBy->first_name . ' ' . $row->createdBy->last_name 
                        : null;
                })
                ->addColumn('action', function ($row) {
                    $viewUrl = route('employeecardb1.visa.appraisal.view', ['id' => $row->id]);
                    $deleteUrl = route('employeecardb1.visa.appraisal.delete', ['appraisal_id' => $row->id]);

                    $html = '<div class="btn-group">
                                <button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    ' . __('messages.actions') . '
                                    <span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                    
                        $html .= '<li><a href="javascript:void(0);" data-href="' . $viewUrl . '" class="btn-modal" data-container=".visa_modal">
                                    <i class="fas fa-eye"></i> ' . __('messages.view') . '</a></li>';
                    
                    if (auth()->user()->can('visa.delete')) {
                        $html .= '<li><a href="javascript:void(0);" data-href="' . $deleteUrl . '" class="delete-visa">
                                    <i class="fas fa-trash"></i> ' . __('messages.delete') . '</a></li>';
                    }
                    $html .= '</ul></div>';

                    return $html;
                })
                ->rawColumns(['action'])
                ->toJson(); // Ensures proper JSON response
        }

        Log::warning('Non-AJAX request to getVisaAppraisals', ['user_id' => $user_id]);
        return response()->json(['error' => 'This endpoint is for AJAX requests only.'], 400);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('user.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $user = User::where('business_id', $business_id)
            ->with(['contactAccess'])
            ->findOrFail($id);

        $roles = $this->getRolesArray($business_id);

        $contact_access = $user->contactAccess->pluck('name', 'id')->toArray();

        if ($user->status == 'active') {
            $is_checked_checkbox = true;
        } else {
            $is_checked_checkbox = false;
        }

        $locations = BusinessLocation::where('business_id', $business_id)
            ->get();

        $permitted_locations = $user->permitted_locations();
        $username_ext = $this->moduleUtil->getUsernameExtension();

        //Get user form part from modules
        $form_partials = $this->moduleUtil->getModuleData('moduleViewPartials', ['view' => 'manage_user.edit', 'user' => $user]);

        return view('employeecardb1::user.edit')
            ->with(compact('roles', 'user', 'contact_access', 'is_checked_checkbox', 'locations', 'permitted_locations', 'form_partials', 'username_ext'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //Disable in demo
        $notAllowed = $this->moduleUtil->notAllowedInDemo();
        if (! empty($notAllowed)) {
            return $notAllowed;
        }
        
        if (! auth()->user()->can('user.update')) {
            abort(403, 'Unauthorized action.');
        }
    
        try {
            $user_data = $request->only(['surname',
            'first_name',
            'last_name',
            'email',
            'user_type',
            'crm_contact_id',
            'allow_login',
            'cmmsn_percent',
            'max_sales_discount_percent',
            'dob',
            'gender',
            'marital_status',
            'blood_group',
            'contact_number',
            'alt_number',
            'family_number',
            'fb_link',
            'twitter_link',
            'social_media_1',
            'social_media_2',
            'custom_field_1',
            'custom_field_2',
            'custom_field_3',
            'custom_field_4',
            'guardian_name',
            'id_proof_name',
            'id_proof_number',
            'permanent_address',
            'current_address',
            'bank_details',
            'selected_contacts',
            'is_enable_service_staff_pin',
            'service_staff_pin',
            'name_in_khmer',
            'uniform_size',
            'member_date',
            'hieght',
            'weight',
            'job_history',
            'hobby',
            'education',
            'guardien_type',
            'job_description',
            'insurance_number',
            'ss_number',
            'date_left_job',
            'reason']);

    
            $user_data['status'] = ! empty($request->input('is_active')) ? 'active' : 'inactive';
            $user_data['is_enable_service_staff_pin'] = ! empty($request->input('is_enable_service_staff_pin')) ? true : false;
    
            $business_id = request()->session()->get('user.business_id');
            

            if (! isset($user_data['selected_contacts'])) {
                $user_data['selected_contacts'] = 0;
            }
    
            if (empty($request->input('allow_login'))) {
                $user_data['username'] = null;
                $user_data['password'] = null;
                $user_data['allow_login'] = 0;
            } else {
                $user_data['allow_login'] = 1;
            }
    
            if (! empty($request->input('password'))) {
                $user_data['password'] = $user_data['allow_login'] == 1 ? Hash::make($request->input('password')) : null;
            }
    
    
            if (! empty($request->input('service_staff_pin'))) {
                $user_data['service_staff_pin'] = $request->input('service_staff_pin');
            }

            $sign_image = $this->moduleUtil->uploadFile($request, 'sign_image', 'sign_images', 'image');
            if (!empty($sign_image)) {
                $user_data['sign_image'] = $sign_image;
            }

            //Sales commission percentage
            $user_data['cmmsn_percent'] = ! empty($user_data['cmmsn_percent']) ? $this->moduleUtil->num_uf($user_data['cmmsn_percent']) : 0;
    
            $user_data['max_sales_discount_percent'] = ! is_null($user_data['max_sales_discount_percent']) ? $this->moduleUtil->num_uf($user_data['max_sales_discount_percent']) : null;
    
            if (! empty($request->input('dob'))) {
                $user_data['dob'] = $this->moduleUtil->uf_date($request->input('dob'));
            }
    
            if (! empty($request->input('bank_details'))) {
                $user_data['bank_details'] = json_encode($request->input('bank_details'));
            }
    
            DB::beginTransaction();
    
            if ($user_data['allow_login'] && $request->has('username')) {
                $user_data['username'] = $request->input('username');
                $ref_count = $this->moduleUtil->setAndGetReferenceCount('username');
                if (blank($user_data['username'])) {
                    $user_data['username'] = $this->moduleUtil->generateReferenceNumber('username', $ref_count);
                }
    
                $username_ext = $this->moduleUtil->getUsernameExtension();
                if (! empty($username_ext)) {
                    $user_data['username'] .= $username_ext;
                }
            }
    
            $user = User::where('business_id', $business_id)
                          ->findOrFail($id);
    
            $user->update($user_data);
             Media::uploadMedia($user->business_id, $user, request(), 'profile_photo', true);
            $role_id = $request->input('role');
            $user_role = $user->roles->first();
            $previous_role = ! empty($user_role->id) ? $user_role->id : 0;
            if ($previous_role != $role_id) {
                $is_admin = $this->moduleUtil->is_admin($user);
                $all_admins = $this->getAdmins();
                //If only one admin then can not change role
                if ($is_admin && count($all_admins) <= 1) {
                    throw new \Exception(__('lang_v1.cannot_change_role'));
                }
                if (! empty($previous_role)) {
                    $user->removeRole($user_role->name);
                }
    
                $role = Role::findOrFail($role_id);
                $user->assignRole($role->name);
            }
    
            //Grant Location permissions
            $this->moduleUtil->giveLocationPermissions($user, $request);
    
            //Assign selected contacts
            if ($user_data['selected_contacts'] == 1) {
                $contact_ids = $request->get('selected_contact_ids');
            } else {
                $contact_ids = [];
            }
            $user->contactAccess()->sync($contact_ids);
    
            //Update module fields for user
            $this->moduleUtil->getModuleData('afterModelSaved', ['event' => 'user_saved', 'model_instance' => $user]);
    
            $this->moduleUtil->activityLog($user, 'edited', null, ['name' => $user->user_full_name]);
           
            event(new UserCreatedOrModified($user, 'updated'));
            
            $output = ['success' => 1,
                'msg' => __('user.user_update_success'),
            ];
    
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
    
           
    
            $output = ['success' => 0,
                'msg' => $e->getMessage(),
            ];
        }
    
        return redirect('users')->with('status', $output);
    }

    private function getAdmins()
    {
        $business_id = request()->session()->get('user.business_id');
        $admins = User::role('Admin#' . $business_id)->get();

        return $admins;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Disable in demo
        $notAllowed = $this->moduleUtil->notAllowedInDemo();
        if (!empty($notAllowed)) {
            return $notAllowed;
        }

        if (!auth()->user()->can('user.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                $user = User::where('business_id', $business_id)
                    ->findOrFail($id);

                $this->moduleUtil->activityLog($user, 'deleted', null, ['name' => $user->user_full_name, 'id' => $user->id]);

                $user->delete();
                event(new UserCreatedOrModified($user, 'deleted'));

                $output = [
                    'success' => true,
                    'msg' => __('user.user_delete_success'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    /**
     * Retrives roles array (Hides admin role from non admin users)
     *
     * @param  int  $business_id
     * @return array $roles
     */
    private function getRolesArray($business_id)
    {
        $roles_array = Role::where('business_id', $business_id)->get()->pluck('name', 'id');
        $roles = [];

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        foreach ($roles_array as $key => $value) {
            if (!$is_admin && $value == 'Admin#' . $business_id) {
                continue;
            }
            $roles[$key] = str_replace('#' . $business_id, '', $value);
        }

        return $roles;
    }

    /**
     * Signes in from user id
     *
     * @param  int  $id
     */
    public function signInAsUser($id)
    {
        if (!auth()->user()->can('superadmin') && empty(session('previous_user_id'))) {
            abort(403, 'Unauthorized action.');
        }

        $user_id = auth()->user()->id;
        $username = auth()->user()->username;
        session()->flush();

        if (request()->has('save_current')) {
            session(['previous_user_id' => $user_id, 'previous_username' => $username]);
        }

        Auth::loginUsingId($id);

        return redirect()->route('home');
    }
}