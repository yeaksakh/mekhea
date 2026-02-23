<?php

namespace Modules\Connector\Http\Controllers\Api;

use App\User;
use App\BusinessLocation;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Spatie\Permission\Models\Permission;
use Modules\Essentials\Entities\EssentialsMessage;
use Modules\Essentials\Notifications\NewMessageNotification;

class EssentialsMessageController extends ApiController
{
    /**
     * All Utils instance.
     */
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
        public function index()
{
    // Retrieve the business_id from the authenticated user instead of session
    $business_id = auth()->user()->business_id;

    // Check if the user has superadmin or the necessary module permission
    if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
        return response()->json(['error' => 'Unauthorized action.'], 403);
    }

    // Check if the user can view or create messages
    if (! auth()->user()->can('essentials.view_message') && ! auth()->user()->can('essentials.create_message')) {
        return response()->json(['error' => 'Unauthorized action.'], 403);
    }

    // Query the messages associated with the business
    $query = EssentialsMessage::where('business_id', $business_id)
                    ->with(['sender'])
                    ->orderBy('created_at', 'ASC');

    // Handle permitted locations, filtering messages based on user's permitted locations
    $permitted_locations = auth()->user()->permitted_locations();
    if ($permitted_locations != 'all') {
        $query->where(function ($q) use ($permitted_locations) {
            $q->whereIn('location_id', $permitted_locations)
                ->orWhereRaw('location_id IS NULL');
        });
    }

    // Get the messages
    $messages = $query->get();

    // Optionally, retrieve the business locations if needed
    $business_locations = BusinessLocation::forDropdown($business_id);

    // Return the messages and business locations in a JSON response
    return response()->json([
        'messages' => $messages,
        'business_locations' => $business_locations
    ]);
 }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Get business_id from authenticated user
        $business_id = auth()->user()->business_id;

        // Check if the user has the necessary permissions
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        // Check if the user can create messages
        if (!auth()->user()->can('essentials.create_message')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        try {
            // Get user_id from authenticated user
            $user_id = auth()->user()->id;

            // Get input from request
            $input = $request->only(['message', 'location_id']);
            $input['business_id'] = $business_id;
            $input['user_id'] = $user_id;
            $input['message'] = nl2br($input['message']); // Convert new lines to <br>

            if (!empty($input['message'])) {
                // Create the message
                $message = EssentialsMessage::create($input);

                // Notify users based on some condition (optional)
                $this->__notify($message, true);

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'msg' => __('lang_v1.success'),
                ]);
            } else {
                return response()->json(['success' => false, 'msg' => 'Message content is required.'], 400);
            }
        } catch (\Exception $e) {
            // Log the exception and return error response
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            return response()->json([
                'success' => false,
                'msg' => 'File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage(),
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
        $business_id = request()->user()->business_id;

        // Check if the user is a superadmin or has the required module permissions
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        // Check if the user has permission to delete messages
        if (!auth()->user()->can('essentials.create_message')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        try {
            // Get the authenticated user's ID
            $user_id = request()->user()->id;

            // Attempt to delete the message with the given ID
            $deleted = EssentialsMessage::where('business_id', $business_id)
                ->where('user_id', $user_id)
                ->where('id', $id)
                ->delete();

            if ($deleted) {
                // Return a success response
                return response()->json([
                    'success' => true,
                    'message' => __('lang_v1.deleted_success')
                ], 200);
            } else {
                // If the message was not found or deleted, return an error
                return response()->json([
                    'success' => false,
                    'message' => __('messages.something_went_wrong')
                ], 404);
            }
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

            // Return a JSON error response
            return response()->json([
                'success' => false,
                'message' => __('messages.something_went_wrong')
            ], 500);
        }
    }


    /**
     * Sends notification to the user.
     *
     * @return void
     */
    private function __notify($message, $database_notification = true)
    {
        // Retrieve business_id from authenticated user
        $business_id = auth()->user()->business_id;

        // Query to get users who are not the sender and belong to the same business
        $query = User::where('id', '!=', $message->user_id)
            ->where('business_id', $business_id);

        // Handle location-based permission
        if (empty($message->location_id)) {
            // Notify all users in the business if no specific location is set
            $users = $query->get();
        } else {
            // Define the permission name based on the location ID
            $permissionName = 'location.' . $message->location_id;

            // Check if the permission exists for the 'api' guard
            $permission = Permission::where('name', $permissionName)->where('guard_name', 'api')->first();

            // If permission doesn't exist, create it
            if (!$permission) {
                Permission::create(['name' => $permissionName, 'guard_name' => 'api']);
            }

            // Now, notify only users who have permission for the specific location
            $users = $query->permission($permissionName)->get();
        }

        // Send notifications if there are users to notify
        if (count($users)) {
            $message->database_notification = $database_notification;
            \Notification::send($users, new NewMessageNotification($message));
        }
    }


    /**
     * Function to get recent messages
     *
     * @return void
     */
    public function getNewMessages()
    {
        // Get the last chat time from the request input
        $last_chat_time = request()->input('last_chat_time');

        // Get the authenticated user's business_id
        $business_id = request()->user()->business_id;

        // Check if the user has the required permissions
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        if (!auth()->user()->can('essentials.view_message') && !auth()->user()->can('essentials.create_message')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        // Build the query to fetch messages
        $query = EssentialsMessage::where('business_id', $business_id)
            ->where('user_id', '!=', auth()->user()->id)
            ->with(['sender'])
            ->orderBy('created_at', 'ASC');

        // Filter messages by last chat time if provided
        if (!empty($last_chat_time)) {
            $query->where('created_at', '>', $last_chat_time);
        }

        // Check permitted locations for the user
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->where(function ($q) use ($permitted_locations) {
                $q->whereIn('location_id', $permitted_locations)
                    ->orWhereRaw('location_id IS NULL');
            });
        }

        // Get the messages
        $messages = $query->get();

        // Return the messages as a JSON response
        return response()->json([
            'success' => true,
            'messages' => $messages
        ], 200);
    }

}
