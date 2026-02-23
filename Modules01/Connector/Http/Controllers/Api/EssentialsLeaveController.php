<?php

namespace Modules\Connector\Http\Controllers\Api;

use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Essentials\Entities\EssentialsLeave;
use Modules\Essentials\Entities\EssentialsLeaveType;
use Modules\Essentials\Notifications\NewLeaveNotification;
use Modules\Essentials\Notifications\LeaveStatusNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use App\User;

class EssentialsLeaveController extends Controller
{
    protected $leave_statuses;

    protected $moduleUtil;

    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->leave_statuses = [
            'pending' => [
                'name' => __('lang_v1.pending'),
                'class' => 'bg-yellow',
            ],
            'approved' => [
                'name' => __('essentials::lang.approved'),
                'class' => 'bg-green',
            ],
            'cancelled' => [
                'name' => __('essentials::lang.cancelled'),
                'class' => 'bg-red',
            ],
        ];
    }

    public function index()
    {
        if (!$this->moduleUtil->isModuleInstalled('Essentials')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $user = Auth::user();
        $business_id = $user->business_id;

        $leaves = EssentialsLeave::where('essentials_leaves.business_id', $business_id)
            ->join('users as u', 'u.id', '=', 'essentials_leaves.user_id')
            ->join('essentials_leave_types as lt', 'lt.id', '=', 'essentials_leaves.essentials_leave_type_id')
            ->select([
                'essentials_leaves.id',
                DB::raw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as user"),
                'lt.leave_type',
                'start_date',
                'end_date',
                'ref_no',
                'essentials_leaves.status',
                'essentials_leaves.business_id',
                'reason',
                'status_note',
            ])
            ->get();

        return response()->json($leaves);
    }

    public function store(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'essentials_leave_type_id' => 'required|exists:essentials_leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:255',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Retrieve authenticated user and business ID
        $user = Auth::user();
        $business_id = $user->business_id;

        // Prepare data to create a new leave record
        $data = [
            'business_id' => $business_id,
            'user_id' => $user->id,
            'essentials_leave_type_id' => $request->input('essentials_leave_type_id'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'reason' => $request->input('reason'),
            'status' => 'pending',
        ];

        // Generate reference number if not provided in the request
        if (!$request->has('ref_no')) {
            $ref_count = $this->moduleUtil->setAndGetReferenceCount('leave', $business_id);
            $settings = []; // Replace with actual retrieval logic for settings
            $prefix = !empty($settings['leave_ref_no_prefix']) ? $settings['leave_ref_no_prefix'] : '';
            $ref_no = $this->moduleUtil->generateReferenceNumber('leave', $ref_count, null, $prefix);
            $data['ref_no'] = $ref_no;
        }

        // Create a new leave record
        $leave = EssentialsLeave::create($data);

        // $roles = Role::where('business_id', $business_id)
        //     ->where('name', '<>', "Admin#".$business_id)
        //     ->get();

        $admins = User::whereHas('roles', function ($query) use ($business_id) {
            $query->where('name', 'like', 'Admin#%')
                  ->where('name', 'like', 'Admin#' . $business_id);
        })->get();


        // Notify admins about the new leave
        // $admins = $this->moduleUtil->get_admins($business_id);
        foreach($admins as $admin){
            Notification::send($admin, new NewLeaveNotification($leave));
        }

        // Return success response
        return response()->json(['success' => true, 'msg' => 'Leave added successfully.']);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $leave = EssentialsLeave::where('business_id', $business_id)->find($id);

        if (!$leave) {
            return response()->json(['error' => 'Leave not found.'], 404);
        }

        $leave->delete();

        return response()->json(['success' => true, 'msg' => 'Leave deleted successfully.']);
    }

    public function changeStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string',
            'status_note' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $business_id = $user->business_id;

        $leave = EssentialsLeave::where('business_id', $business_id)->find($id);

        if (!$leave) {
            return response()->json(['error' => 'Leave not found.'], 404);
        }

        $leave->status = $request->input('status');
        $leave->status_note = $request->input('status_note');
        $leave->save();

        $leave->status = $this->leave_statuses[$leave->status]['name'];
        $leave->changed_by = auth()->user()->id;

        $leave->user->notify(new LeaveStatusNotification($leave));

        return response()->json(['success' => true, 'msg' => 'Leave status updated successfully.']);
    }

    public function getUserLeave()
    {
        if (!$this->moduleUtil->isModuleInstalled('Essentials')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $user = Auth::user();
        $business_id = $user->business_id;

        $leaves = EssentialsLeave::where('essentials_leaves.business_id', $business_id)
            ->where('essentials_leaves.user_id', $user->id)
            ->join('users as u', 'u.id', '=', 'essentials_leaves.user_id')
            ->join('essentials_leave_types as lt', 'lt.id', '=', 'essentials_leaves.essentials_leave_type_id')
            ->select([
                'essentials_leaves.id',
                DB::raw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as user"),
                'lt.leave_type',
                'start_date',
                'end_date',
                'ref_no',
                'essentials_leaves.status',
                'essentials_leaves.business_id',
                'essentials_leaves.reason',
                'status_note',
            ])
            ->get();

        return response()->json($leaves);
    }

    public function getLeaveType()
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $leavesType = EssentialsLeaveType::where('business_id', $business_id)
            ->get();

        return response()->json($leavesType);
    }
}