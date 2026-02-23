<?php

namespace Modules\Connector\Http\Controllers\Api;

use App\Utils\ModuleUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Essentials\Entities\Reminder;

class ReminderController extends ApiController
{
    /**
     * All Utils instance.
     */
    protected $commonUtil;

    protected $moduleUtil;

    public function __construct(Util $commonUtil, ModuleUtil $moduleUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        try {
            // Get the authenticated user's business ID and user ID
            $business_id = auth()->user()->business_id;
            $user_id = auth()->user()->id;

            // Check if the user has the right permissions
            if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
                return response()->json(['error' => 'Unauthorized action.'], 403);
            }

            // Filter reminders based on optional start and end dates
            $query = Reminder::where('business_id', $business_id)
                ->where('user_id', $user_id);

            if ($request->has('start') && $request->has('end')) {
                $query->whereBetween('date', [$request->get('start'), $request->get('end')]);
            }

            $reminders = $query->get();

            // Format the response data
            $events = [];
            foreach ($reminders as $reminder) {
                $events[] = [
                    'id' => $reminder->id,  // Include the reminder id
                    'title' => $reminder->name,
                    'start' => $reminder->date . ' ' . $reminder->time,
                    'end' => $reminder->date . ' ' . $reminder->end_time,
                    'name' => $reminder->name,
                    'repeat' => $reminder->repeat,
                    'backgroundColor' => '#ff851b',  // Customize as needed
                    'borderColor' => '#ff851b',  // Customize as needed
                    'event_type' => 'reminder',
                ];
            }

            // Return the formatted reminders as JSON
            return response()->json($events);
        } catch (\Exception $e) {
            // Log the error message
            Log::error('Error fetching reminders: ' . $e->getMessage());

            // Return a generic error response
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            // Log starting of the process
            \Log::info('Starting to store a reminder.');

            // Get the authenticated user's business ID and user ID
            $business_id = auth()->user()->business_id;
            $user_id = auth()->user()->id;

            \Log::info('Business ID: ' . $business_id . ', User ID: ' . $user_id);

            // Extract input data directly
            $input = $request->only(['name', 'date', 'repeat', 'time', 'end_time']);
            \Log::info('Input Data: ' . json_encode($input));

            // Check for missing or malformed input
            if (empty($input['name']) || empty($input['date']) || empty($input['time'])) {
                \Log::error('Missing required fields.');
                return response()->json(['success' => false, 'msg' => 'Missing required fields.'], 400);
            }

            // Validate that date and time are in correct format
            if (!\DateTime::createFromFormat('Y-m-d', $input['date'])) {
                \Log::error('Invalid date format: ' . $input['date']);
                return response()->json(['success' => false, 'msg' => 'Invalid date format. Please use Y-m-d.'], 400);
            }

            if (!\DateTime::createFromFormat('H:i', $input['time'])) {
                \Log::error('Invalid time format: ' . $input['time']);
                return response()->json(['success' => false, 'msg' => 'Invalid time format. Please use H:i (24-hour format).'], 400);
            }

            if (!empty($input['end_time']) && !\DateTime::createFromFormat('H:i', $input['end_time'])) {
                \Log::error('Invalid end time format: ' . $input['end_time']);
                return response()->json(['success' => false, 'msg' => 'Invalid end time format. Please use H:i (24-hour format).'], 400);
            }

            // Prepare the data for insertion (without further formatting)
            $reminder = [
                'date' => $input['date'],
                'time' => $input['time'],
                'end_time' => !empty($input['end_time']) ? $input['end_time'] : null,
                'name' => $input['name'],
                'repeat' => $input['repeat'],
                'user_id' => $user_id,
                'business_id' => $business_id,
            ];

            \Log::info('Reminder Data: ' . json_encode($reminder));

            // Create the reminder
            Reminder::create($reminder);

            // Return success response
            return response()->json([
                'success' => true,
                'msg' => __('lang_v1.success'),
            ], 201);

        } catch (\Exception $e) {
            // Log the full error details
            \Log::emergency('Error: File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            // Return error response
            return response()->json([
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ], 500);
        }
    }









    /**
     * Show the specified resource.
     *
     * @return Response
     */
    public function show($id)
    {
        try {
            // Get authenticated user's business ID
            $business_id = auth()->user()->business_id;

            // Check permissions
            if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
                return response()->json(['error' => 'Unauthorized action.'], 403);
            }

            // Get authenticated user's ID
            $user_id = auth()->user()->id;

            // Find the reminder by ID, business_id, and user_id
            $reminder = Reminder::where('business_id', $business_id)
                ->where('user_id', $user_id)
                ->findOrFail($id); // Use findOrFail to automatically throw 404 if not found

            // Format the time
            $time = $this->commonUtil->format_time($reminder->time);

            // Prepare data for JSON response
            $response_data = [
                'id' => $reminder->id,
                'name' => $reminder->name,
                'date' => $reminder->date,
                'time' => $time,
                'end_time' => $reminder->end_time,
                'repeat' => $reminder->repeat,  // No translation, return raw value
                'created_at' => $reminder->created_at,
                'updated_at' => $reminder->updated_at,
            ];

            // Return the reminder data as a JSON response
            return response()->json($response_data);
        } catch (\Exception $e) {
            // Log error message for debugging
            Log::error('Error fetching reminder: ' . $e->getMessage());

            // Return a generic error response
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        try {
            // Get the authenticated user's business ID and user ID
            $business_id = auth()->user()->business_id;
            $user_id = auth()->user()->id;

            // Check if the user has the necessary permissions
            if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
                return response()->json(['error' => 'Unauthorized action.'], 403);
            }

            // Extract 'repeat' field from the request
            $repeat = $request->only('repeat');

            // Update the reminder for the given ID
            $updated = Reminder::where('business_id', $business_id)
                ->where('user_id', $user_id)
                ->where('id', $id)
                ->update($repeat);

            if ($updated) {
                // Return success response
                return response()->json([
                    'success' => true,
                    'msg' => trans('lang_v1.updated_success'),
                ], 200);
            } else {
                // Return error if the reminder was not found or update failed
                return response()->json([
                    'success' => false,
                    'msg' => trans('lang_v1.update_failed'),
                ], 404);
            }

        } catch (\Exception $e) {
            // Log the full error details
            \Log::emergency('Error: File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            // Return error response
            return response()->json([
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy($id)
    {
        try {
            // Get the authenticated user's business ID and user ID
            $business_id = auth()->user()->business_id;
            $user_id = auth()->user()->id;

            // Check if the user has the necessary permissions
            if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
                return response()->json(['error' => 'Unauthorized action.'], 403);
            }

            // Attempt to delete the reminder
            $deleted = Reminder::where('business_id', $business_id)
                ->where('user_id', $user_id)
                ->where('id', $id)
                ->delete();

            if ($deleted) {
                // Return success response
                return response()->json([
                    'success' => true,
                    'msg' => trans('lang_v1.deleted_success'),
                ], 200);
            } else {
                // Return error if the reminder was not found or delete failed
                return response()->json([
                    'success' => false,
                    'msg' => trans('lang_v1.delete_failed'),
                ], 404);
            }

        } catch (\Exception $e) {
            // Log the full error details
            \Log::emergency('Error: File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            // Return error response
            return response()->json([
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ], 500);
        }
    }



}
