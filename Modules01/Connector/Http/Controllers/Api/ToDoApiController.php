<?php

namespace Modules\Connector\Http\Controllers\Api;

use App\User;
use App\Media;
use App\Utils\Util;
use App\ReferenceCount;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Modules\Essentials\Entities\ToDo;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;
use Modules\Essentials\Entities\EssentialsTodoComment;
use Modules\Essentials\Notifications\NewTaskNotification;
use Modules\Essentials\Notifications\NewTaskCommentNotification;
use Modules\Essentials\Notifications\NewTaskDocumentNotification;

class ToDoApiController extends ApiController
{
  /**
   * All Utils instance.
   */
  protected $commonUtil;

  protected $moduleUtil;

  /**
   * Constructor
   *
   * @param CommonUtil
   * @return void
   */
  public function __construct(Util $commonUtil, ModuleUtil $moduleUtil)
  {
    $this->commonUtil = $commonUtil;
    $this->moduleUtil = $moduleUtil;

    $this->priority_colors = [
      'low' => 'bg-green',
      'medium' => 'bg-yellow',
      'high' => 'bg-orange',
      'urgent' => 'bg-red',
    ];

    $this->status_colors = [
      'new' => 'bg-yellow',
      'in_progress' => 'bg-light-blue',
      'on_hold' => 'bg-red',
      'completed' => 'bg-green',
    ];
  }
  public function index(Request $request)
  {
    $user = Auth::user();

    $business_id = $user->business_id;
    // Retrieve the business_id from the request (or from the user, depending on your setup)

    // Check if the user has the required permissions
    // Uncomment if permission check is needed
    // if (!$user->can('superadmin') && !$this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) {
    //   return response()->json(['error' => 'Unauthorized action.'], 403);
    // }

    // Check if the user is an admin
    $is_admin = $this->moduleUtil->is_admin($user, $business_id);
    $auth_id = $user->id;

    // Get task statuses and priorities
    $task_statuses = ToDo::getTaskStatus();
    $priorities = ToDo::getTaskPriorities();

    // Handle the number of items per page
    $per_page = !empty($request->per_page) ? (int) $request->per_page : 10; // Default to 10 if not provided

    // Build the todos query
    $todos = ToDo::where('business_id', $business_id)
      ->with(['users', 'assigned_by']) // Eager loading relationships
      ->select('*');

    // Filter by priority if provided
    if (!empty($request->priority)) {
      $todos->where('priority', $request->priority);
    }

    // Filter by status if provided
    if (!empty($request->status)) {
      $todos->where('status', $request->status);
    }

    // If the user is not an admin, only show tasks they created or are assigned to
    if (!$is_admin) {
      $todos->where(function ($query) use ($auth_id) {
        $query->where('created_by', $auth_id)
          ->orWhereHas('users', function ($q) use ($auth_id) {
            $q->where('user_id', $auth_id);
          });
      });
    }

    // Filter by assigned user ID if provided
    if (!empty($request->user_id)) {
      $todos->whereHas('users', function ($q) use ($request) {
        $q->where('user_id', $request->user_id);
      });
    }

    // Filter by date range if provided
    if (!empty($request->start_date) && !empty($request->end_date)) {
      $todos->whereBetween('date', [$request->start_date, $request->end_date]);
    }

    // Return paginated results based on the per_page input
    return response()->json($todos->paginate($per_page));
  }
  public function show($id, Request $request)
  {
    // Get business_id from request or another source instead of session
    $user = Auth::user();

    $business_id = $user->business_id;

    // Check if user has permission without using session
    if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
      return response()->json(['error' => 'Unauthorized action.'], 403);
    }

    // Determine if the user is an admin
    $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

    // Query to get the ToDo item
    $query = ToDo::where('business_id', $business_id)
      ->with([
        'assigned_by',
        'comments',
        'comments.added_by',
        'media',
        'users',
        'media.uploaded_by_user',
      ]);

    // If not admin, show only assigned tasks
    if (!$is_admin) {
      $query->where(function ($query) {
        $query->where('created_by', auth()->user()->id)
          ->orWhereHas('users', function ($q) {
            $q->where('user_id', auth()->user()->id);
          });
      });
    }

    // Find the ToDo item or fail
    $todo = $query->findOrFail($id);

    // Extract users assigned to the ToDo
    $users = [];
    foreach ($todo->users as $user) {
      $users[] = $user->user_full_name;
    }

    // Get task statuses and priorities
    $task_statuses = ToDo::getTaskStatus();
    $priorities = ToDo::getTaskPriorities();

    // Get related activities
    $activities = Activity::forSubject($todo)
      ->with(['causer', 'subject'])
      ->latest()
      ->get();

    // Return data as JSON
    return response()->json([
      'todo' => $todo,
      'users' => $users,
      'task_statuses' => $task_statuses,
      'priorities' => $priorities,
      'activities' => $activities,
    ]);
  }

  public function store(Request $request)
  {
    // Validate request data to ensure date and end_date are proper dates
    // $request->validate([
    //   'task' => 'required|string',
    //   'date' => 'required|date_format:Y-m-d', // or modify the format if needed
    //   'end_date' => 'nullable|date_format:Y-m-d', // If end_date is optional
    // ]);

    // Get the business ID directly from the request or authenticated user's business
    $business_id = $request->input('business_id') ?? auth()->user()->business_id;

    try {
      // Get the authenticated user's ID as the creator of the task
      $created_by = auth()->user()->id;

      // Extract task input fields from the request
      $input = $request->only(
        'task',
        'date',
        'description',
        'estimated_hours',
        'priority',
        'status',
        'end_date'
      );

      // Format date fields using uf_date (which is now more flexible)
      $input['date'] = $this->uf_date($input['date'], true);
      $input['end_date'] = !empty($input['end_date']) ? $this->uf_date($input['end_date'], true) : null;

      $input['business_id'] = $business_id;
      $input['created_by'] = $created_by;
      $input['status'] = $input['status'] ?? 'new';

      // Determine which users are assigned to the task
      $users = $request->input('users');
      if (!auth()->user()->can('essentials.assign_todos') || empty($users)) {
        $users = [$created_by];
      }

      // Generate task reference number
      $ref_count = $this->commonUtil->setAndGetReferenceCount('essentials_todos', $business_id);
      $settings = $request->input('essentials_settings', []);
      $settings = !empty($settings) ? json_decode($settings, true) : [];
      $prefix = $settings['essentials_todos_prefix'] ?? '';
      $input['task_id'] = $this->commonUtil->generateReferenceNumber('essentials_todos', $ref_count, null, $prefix);

      // Create the new task
      $to_dos = ToDo::create($input);
      $to_dos->users()->sync($users);
      $this->commonUtil->activityLog($to_dos, 'added');

      // Notify the users assigned to the task
      $notify_users = $to_dos->users->filter(function ($item) use ($created_by) {
        return $item->id != $created_by;
      });
      \Notification::send($notify_users, new NewTaskNotification($to_dos));

      // Handle document upload if a file is provided
      if ($request->hasFile('documents')) {
        Media::uploadMedia($to_dos->business_id, $to_dos, $request, 'documents');

        // Notify users about the document upload
        $auth_id = auth()->user()->id;
        $users_for_document = $to_dos->users->filter(function ($user) use ($auth_id) {
          return $user->id != $auth_id;
        });

        $data = [
          'task_id' => $to_dos->task_id,
          'uploaded_by' => $auth_id,
          'id' => $to_dos->id,
          'uploaded_by_user_name' => auth()->user()->user_full_name,
        ];
        \Notification::send($users_for_document, new NewTaskDocumentNotification($data));
      }

      // Return success response
      $output = [
        'success' => true,
        'msg' => __('lang_v1.success'),
        'todo_id' => $to_dos->id,
      ];
    } catch (\Exception $e) {
      \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
      \Log::emergency('Request Data: ' . json_encode($request->all()));

      // Return detailed error response for debugging
      $output = [
        'success' => false,
        'msg' => __('messages.something_went_wrong'),
        'error' => $e->getMessage(), // Include the error message for debugging
      ];
    }

    // Return the result in JSON format for the API
    return response()->json($output);
  }
  public function update(Request $request, $id)
  {
    try {
      // Get business ID from the authenticated user or request
      $business_id = $request->input('business_id') ?? auth()->user()->business_id;

      // Authorization: Check if the user has permission
      if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) && !auth()->user()->can('essentials.edit_todos')) {
        return response()->json(['success' => false, 'msg' => 'Unauthorized action.'], 403);
      }

      // Validation of the request data
      // $request->validate([
      //   'task' => 'required|string|max:255',
      //   'date' => 'required|date_format:Y-m-d',
      //   'end_date' => 'nullable|date_format:Y-m-d',
      //   'status' => 'nullable|string',
      //   'users' => 'nullable|array',
      //   'documents' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048' // File validation
      // ]);

      // Prepare input data
      if (!$request->has('only_status')) {
        $input = $request->only(
          'task',
          'date',
          'description',
          'estimated_hours',
          'priority',
          'status',
          'end_date'
        );

        // Handle date formatting
        $input['date'] = $this->uf_date($input['date'], true);
        $input['end_date'] = !empty($input['end_date']) ? $this->uf_date($input['end_date'], true) : null;
        $input['status'] = !empty($input['status']) ? $input['status'] : 'new';
      } else {
        $input = ['status' => $request->input('status')];
      }
      

      // Query to find the ToDo item
      $query = ToDo::where('business_id', $business_id);
      

      // Non-admin users can only update assigned tasks
      $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
      if (!$is_admin) {
        $query->where(function ($query) {
          $query->where('created_by', auth()->user()->id)
            ->orWhereHas('users', function ($q) {
              $q->where('user_id', auth()->user()->id);
            });
        });
      }

      // Find the ToDo item or return 404 if not found
      $todo = $query->findOrFail($id);

      // Save the previous state for logging purposes
      $todo_before = $todo->replicate();

      // Update the ToDo item with new input data
      $todo->update($input);
      $this->commonUtil->activityLog($todo, 'edited', $todo_before);
      // Sync the users if the permission is granted
      if (auth()->user()->can('essentials.assign_todos') && !$request->has('only_status')) {
        $users = $request->input('users');
        if ($users) {
          $todo->users()->sync($users);
        }
      }

      // Handle document upload if a file is provided
      if ($request->hasFile('documents')) {
        // Upload the document
        Media::uploadMedia($todo->business_id, $todo, $request, 'documents');

        // Notify users about the new document
        $auth_id = auth()->user()->id;
        $users = $todo->users->filter(function ($user) use ($auth_id) {
          return $user->id != $auth_id;
        });

        $data = [
          'task_id' => $todo->task_id,
          'uploaded_by' => $auth_id,
          'id' => $todo->id,
          'uploaded_by_user_name' => auth()->user()->user_full_name,
        ];

        \Notification::send($users, new NewTaskDocumentNotification($data));
      }

      // Log the activity
      $this->commonUtil->activityLog($todo, 'edited', $todo_before);

      // Return success response
      return response()->json([
        'success' => true,
        'msg' => __('lang_v1.success'),
      ]);

    } catch (\Exception $e) {
      // Log the error and return a failure response
      \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

      return response()->json([
        'success' => false,
        'msg' => __('messages.something_went_wrong'),
        'error' => $e->getMessage()
      ], 500);
    }
  }

  public function destroy($id)
  {
    try {
      // Get business ID from the authenticated user or request
      $business_id = request()->input('business_id') ?? auth()->user()->business_id;

      // Authorization: Check if the user has permission to delete tasks
      // if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) && !auth()->user()->can('essentials.delete_todos')) {
      //   return response()->json(['success' => false, 'msg' => 'Unauthorized action.'], 403);
      // }

      // Determine if the user is an admin
      $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

      // Query to find the ToDo item based on business_id
      $todo = ToDo::where('business_id', $business_id);

      // Non-admin users can only delete tasks they created
      if (!$is_admin) {
        $todo->where('created_by', auth()->user()->id);
      }

      // Find the task and delete it
      $todo->where('id', $id)->delete();

      // Return success response
      return response()->json([
        'success' => true,
        'msg' => __('lang_v1.success'),
      ]);

    } catch (\Exception $e) {
      // Log the error for debugging
      \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

      // Return failure response
      return response()->json([
        'success' => false,
        'msg' => __('messages.something_went_wrong'),
        'error' => $e->getMessage()
      ], 500);
    }
  }




  public function uf_date($date, $time = false, $date_format = 'Y-m-d', $time_format = 24)
  {
    try {
      // If time is true, set the appropriate format
      if ($time) {
        if ($time_format === 12) {
          return \Carbon\Carbon::parse($date)->format('Y-m-d h:i A');
        } else {
          return \Carbon\Carbon::parse($date)->format('Y-m-d H:i:s');
        }
      }

      // Return the date in default or provided format
      return \Carbon\Carbon::parse($date)->format($date_format);
    } catch (\Exception $e) {
      \Log::error('Date parsing error: ' . $e->getMessage());
      return null; // Return null if there's a parsing issue
    }
  }

  public function showComments($id, Request $request)
  {
    // Get the authenticated user and business_id
    $user = Auth::user();
    $business_id = $user->business_id;

    // Check if the user has the necessary permissions without using sessions
    // if (!($user->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
    //   return response()->json(['error' => 'Unauthorized action.'], 403);
    // }

    // Determine if the user is an admin
    $is_admin = $this->moduleUtil->is_admin($user, $business_id);

    // Build the query to get only comments for the specified ToDo item
    $query = EssentialsTodoComment::where('task_id', $id)
      ->with(['added_by']); // Assuming comments have a relationship with the user who added them

    // If not admin, restrict access to comments of assigned tasks only
    if (!$is_admin) {
      $assigned_tasks = ToDo::where('business_id', $business_id)
        ->where(function ($query) use ($user) {
          $query->where('created_by', $user->id)
            ->orWhereHas('users', function ($q) use ($user) {
              $q->where('user_id', $user->id);
            });
        })
        ->pluck('id');

      if (!$assigned_tasks->contains($id)) {
        return response()->json(['error' => 'Unauthorized action.'], 403);
      }
    }

    // Retrieve comments for the task
    $comments = $query->get();

    // Return comments as JSON
    return response()->json([
      'comments' => $comments,
    ]);
  }




  public function addComments(Request $request)
  {
    // Get business_id from the request, assuming it is passed in the request body.
    $user = Auth::user();

    $business_id = $user->business_id; // Assuming you're using API token authentication

    // Check user permissions (adjust this as needed based on your application's logic)
    // if (!($user->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
    //   return response()->json(['success' => false, 'msg' => 'Unauthorized action.'], 403);
    // }

    try {
      // Validate input
      $input = $request->only(['task_id', 'comment']);
      $auth_id = $user->id;

      // Build query for the ToDo task
      $query = ToDo::where('business_id', $business_id)->with('users');

      // Check if the user is an admin
      $is_admin = $this->moduleUtil->is_admin($user, $business_id);

      // Non-admin users can only comment on assigned tasks
      if (!$is_admin) {
        $query->where(function ($query) use ($auth_id) {
          $query->where('created_by', $auth_id)
            ->orWhereHas('users', function ($q) use ($auth_id) {
              $q->where('user_id', $auth_id);
            });
        });
      }

      // Find the task or fail
      $todo = $query->findOrFail($input['task_id']);

      // Add the comment
      $input['comment_by'] = $auth_id;
      $comment = EssentialsTodoComment::create($input);

      // Prepare the HTML (or skip this part if not needed in an API context)
      // $comment_html = view('essentials::todo.comment')
      //   ->with(compact('comment'))
      //   ->render();

      // Prepare the response
      $output = [
        'success' => true,
        'msg' => __('lang_v1.success'),
      ];

      // Notify other users
      // Remove auth user from users collection
      $users = $todo->users->filter(function ($user) use ($auth_id) {
        return $user->id != $auth_id;
      });

      \Notification::send($users, new NewTaskCommentNotification($comment));
    } catch (\Exception $e) {
      \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

      $output = [
        'success' => false,
        'msg' => __('messages.something_went_wrong'),
      ];
    }

    return response()->json($output);
  }
  public function deleteComment($id)
  {
    // Assume the business_id is passed in the API request (you can also fetch it based on the authenticated user)
    $user = Auth::user();

    $business_id = $user->business_id;

    if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
      return response()->json([
        'success' => false,
        'msg' => 'Unauthorized action.'
      ], 403); // HTTP 403 Forbidden
    }

    try {
      // Check if the comment exists and belongs to the authenticated user before deleting
      $comment = EssentialsTodoComment::where('comment_by', auth()->user()->id)
        ->where('id', $id)
        ->first();

      if ($comment) {
        $comment->delete();
        return response()->json([
          'success' => true,
          'msg' => __('lang_v1.success')
        ], 200); // HTTP 200 OK
      } else {
        return response()->json([
          'success' => false,
          'msg' => __('lang_v1.comment_not_found') // Custom message for "Comment not found"
        ], 404); // HTTP 404 Not Found
      }
    } catch (\Exception $e) {
      \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

      return response()->json([
        'success' => false,
        'msg' => __('messages.something_went_wrong')
      ], 500); // HTTP 500 Internal Server Error
    }
  }

  public function uploadDocument(Request $request)
  {
    // Logging the incoming request data (useful for debugging)
    \Log::info('Request Data: ' . json_encode($request->all()));

    // Assuming the `business_id` is passed via the request or token (not session)
    $business_id = $request->input('business_id') ?? auth()->user()->business_id;

    // Authorization check: user must be superadmin or have the necessary subscription permission
    if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
      return response()->json([
        'success' => false,
        'msg' => 'Unauthorized action.'
      ], 403); // HTTP 403 Forbidden
    }

    try {
      // Fetch task ID from the request
      $task_id = $request->input('task_id');
      $description = $request->input('description', ''); // Fetching description with default empty string if not provided

      // Query the ToDo model for the task and ensure user has permission
      $query = ToDo::with('users')->where('business_id', $business_id);
      $auth_id = auth()->user()->id;

      // Non-admin users can only add comments to their assigned tasks
      $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
      if (!$is_admin) {
        $query->where(function ($query) {
          $query->where('created_by', auth()->user()->id)
            ->orWhereHas('users', function ($q) {
              $q->where('user_id', auth()->user()->id);
            });
        });
      }

      // Fetch the task
      $todo = $query->findOrFail($task_id);

      // Handle the document upload (file upload process for API)
      if ($request->hasFile('documents')) {
        Media::uploadMedia($todo->business_id, $todo, $request, 'documents');
        \Log::info('Media uploaded successfully for task ID: ' . $todo->id);
      } else {
        return response()->json([
          'success' => false,
          'msg' => 'No document file uploaded.'
        ], 400); // HTTP 400 Bad Request
      }

      // Remove the authenticated user from the list of notified users
      $users = $todo->users->filter(function ($user) use ($auth_id) {
        return $user->id != $auth_id;
      });

      // Prepare notification data
      $data = [
        'task_id' => $todo->task_id,
        'uploaded_by' => $auth_id,
        'id' => $todo->id,
        'uploaded_by_user_name' => auth()->user()->user_full_name,
        'description' => $description, // Including description in the data
      ];

      // Send notification to the users involved in the task
      \Notification::send($users, new NewTaskDocumentNotification($data));

      // Return success response with the description included in the JSON response
      return response()->json([
        'success' => true,
        'msg' => __('lang_v1.success'),
        'description' => $description, // Include description in the response
      ], 200); // HTTP 200 OK

    } catch (\Exception $e) {
      // Log the error for debugging
      \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

      // Return error response in JSON format
      return response()->json([
        'success' => false,
        'msg' => __('messages.something_went_wrong'),
        'error' => $e->getMessage() // For debugging, remove in production
      ], 500); // HTTP 500 Internal Server Error
    }
  }

  public function deleteDocument($id, Request $request)
  {
    // Assuming the `business_id` is passed via the request or derived from the authenticated user
    $business_id = $request->input('business_id') ?? auth()->user()->business_id;

    // Authorization check: user must be superadmin or have the necessary subscription permission
    if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
      return response()->json([
        'success' => false,
        'msg' => 'Unauthorized action.'
      ], 403); // HTTP 403 Forbidden
    }

    try {
      // Find the media by ID
      $media = Media::findOrFail($id);

      // Check if the media is associated with a ToDo task
      if ($media->model_type == 'Modules\Essentials\Entities\ToDo') {
        $todo = ToDo::findOrFail($media->model_id);

        // Allow deletion only if the task is assigned to or created by the current user
        if (in_array(auth()->user()->id, [$todo->user_id, $todo->created_by])) {
          // Check if the file exists before attempting to delete
          if (file_exists($media->display_path)) {
            unlink($media->display_path); // Delete the file from the server
          }

          // Delete the media entry from the database
          $media->delete();

          // Return success response
          return response()->json([
            'success' => true,
            'msg' => __('lang_v1.success')
          ], 200); // HTTP 200 OK
        } else {
          // If the user is not authorized to delete the document
          return response()->json([
            'success' => false,
            'msg' => 'You are not authorized to delete this document.'
          ], 403); // HTTP 403 Forbidden
        }
      } else {
        // If the document's model type is invalid
        return response()->json([
          'success' => false,
          'msg' => 'Invalid document type.'
        ], 400); // HTTP 400 Bad Request
      }
    } catch (\Exception $e) {
      // Log the error for debugging
      \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

      // Return error response in JSON format
      return response()->json([
        'success' => false,
        'msg' => __('messages.something_went_wrong'),
        'error' => $e->getMessage() // For debugging, remove this in production
      ], 500); // HTTP 500 Internal Server Error
    }
  }





}