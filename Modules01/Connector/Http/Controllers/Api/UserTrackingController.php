<?php

namespace Modules\Connector\Http\Controllers\Api;

use App\User;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\UserTracking\Entities\UserTracking;
use App\Utils\TransactionUtil;
use Modules\Essentials\Utils\EssentialsUtil;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class UserTrackingController extends ApiController
{
    protected $moduleUtil;
    protected $essentialsUtil;
    protected $transactionUtil;

    public function __construct(ModuleUtil $moduleUtil, EssentialsUtil $essentialsUtil, TransactionUtil $transactionUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->essentialsUtil = $essentialsUtil;
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Get a list of user tracking records.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $attendance = UserTracking::where('user_tracking.business_id', $business_id)
            ->join('users as u', 'u.id', '=', 'user_tracking.user_id')
            ->select([
                'user_tracking.id',
                'clock_in_time',
                'clock_out_time',
                'clock_in_note',
                'clock_out_note',
                'ip_address',
                \DB::raw('DATE(clock_in_time) as date'),
                \DB::raw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as user"),
                'clock_in_location',
                'clock_out_location',
                'documents',
            ])
            ->orderByDesc('date');

        if (!empty($request->input('employee_id'))) {
            $attendance->where('user_tracking.user_id', $request->input('employee_id'));
        }
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start = $request->start_date;
            $end = $request->end_date;
            $attendance->whereDate('clock_in_time', '>=', $start)
                ->whereDate('clock_in_time', '<=', $end);
        }

        $attendance->where('user_tracking.user_id', auth()->user()->id);

        $records = $attendance->get();

        $users = User::forDropdown($business_id, false, true, true);

        return response()->json([
            'success' => true,
            'data' => $records,
            'users' => $users,
        ]);
    }

    /**
     * Get specific user tracking records with filtering options.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $user_id = $request->input('user_filter');
        $date_range = $request->input('date_range');

        $user_tracking_records = UserTracking::where('business_id', $business_id)
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where('user_id', $user_id);
            })
            ->when($date_range, function ($query) use ($date_range) {
                $dates = explode(' ~ ', $date_range);
                $start_date = Carbon::parse($dates[0]);
                $end_date = Carbon::parse($dates[1]);
                return $query->whereDate('clock_in_time', '>=', $start_date)->whereDate('clock_in_time', '<=', $end_date);
            })
            ->with('user');

        $perPage = $request->input('per_page', $this->perPage); // Default to $this->perPage (you can define this as a class property)
        if ($perPage == -1) {
            $user_tracking_records = $user_tracking_records->get();
            $pagination = null; // No pagination metadata when returning all records
        } else {
            $user_tracking_records = $user_tracking_records->paginate($perPage);
            $user_tracking_records->appends(request()->query()); // Append query parameters to pagination links

            // Extract pagination metadata
            $pagination = [
                'current_page' => $user_tracking_records->currentPage(),
                'first_page_url' => $user_tracking_records->url(1),
                'from' => $user_tracking_records->firstItem(),
                'last_page' => $user_tracking_records->lastPage(),
                'last_page_url' => $user_tracking_records->url($user_tracking_records->lastPage()),
                'next_page_url' => $user_tracking_records->nextPageUrl(),
                'path' => $user_tracking_records->path(),
                'per_page' => $user_tracking_records->perPage(),
                'prev_page_url' => $user_tracking_records->previousPageUrl(),
                'to' => $user_tracking_records->lastItem(),
                'total' => $user_tracking_records->total(),
            ];
        }

        $locations = $user_tracking_records->map(function ($record) {
            $clock_in_location = explode(',', $record->clock_in_location);
            return [
                'id' => $record->id,
                'lat' => isset($clock_in_location[0]) ? floatval($clock_in_location[0]) : null,
                'lng' => isset($clock_in_location[1]) ? floatval($clock_in_location[1]) : null,
                'user' => $record->user->first_name . ' ' . $record->user->last_name,
                'clock_in_time' => $record->clock_in_time,
                'clock_in_note' => $record->clock_in_note,
                'document' => $record->documents,
            ];
        });

        $users = User::forDropdown($business_id, false, true, true);

        return response()->json([
            'success' => true,
            'locations' => $locations,
            'users' => $users,
            'pagination' => $pagination, // Include the pagination metadata
        ]);
    }

    /**
     * Handle clock in and clock out actions.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function clockInClockOut(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        \Log::info('Request received for clock-in/out:', $request->all());

        try {
            $document = $this->transactionUtil->uploadFile($request, 'document', 'tracking');

            \Log::info('Uploaded document path:', ['path' => $document]);

            $data = [
                'business_id' => $business_id,
                'user_id' => auth()->user()->id,
                'clock_in_time' => Carbon::now(),
                'clock_in_note' => $request->input('clock_in_note'),
                'clock_in_location' => $request->input('clock_in_out_location'),
                'documents' => $document,
            ];

            UserTracking::create($data);

            return response()->json(['success' => true, 'msg' => __('essentials::lang.successfully_saved')]);
        } catch (\Exception $e) {
            \Log::emergency('Error during clock-in/out:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')], 500);
        }
    }
}
