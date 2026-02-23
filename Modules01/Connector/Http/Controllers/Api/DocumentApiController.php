<?php

namespace Modules\Connector\Http\Controllers\Api;

use App\User;
use App\Utils\ModuleUtil;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Essentials\Entities\Document;
use Modules\Essentials\Entities\DocumentShare;
use Yajra\DataTables\Facades\DataTables;

class 
DocumentApiController extends ApiController
{
  protected $moduleUtil;

  /**
   * Constructor
   *
   * @param  ModuleUtil  $moduleUtil
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
  public function index(Request $request)
  {
    $user = auth()->user();  // Get the authenticated user
    $business_id = $user->business_id;  // Access business ID from the authenticated user

    // Check if user has permission
    if (!($user->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
      return response()->json(['error' => 'Unauthorized action.'], 403);
    }

    $type = $request->get('type');

    // Handle AJAX / API request
    $user_id = $user->id; // Authenticated user ID
    $role_id = $user->roles()->first()->id; // Assuming a user has only one role

    $documents = Document::leftJoin('essentials_document_shares', 'essentials_documents.id', '=', 'essentials_document_shares.document_id')
      ->join('users', 'essentials_documents.user_id', '=', 'users.id')
      ->where('essentials_documents.business_id', $business_id)
      ->where(function ($query) use ($user_id, $role_id, $type) {
        $query->where('essentials_documents.user_id', $user_id)
          ->orWhere(function ($query) use ($role_id) {
            $query->where('essentials_document_shares.value', $role_id)
              ->where('essentials_document_shares.value_type', 'role');
          })
          ->orWhere(function ($query) use ($user_id) {
            $query->where('essentials_document_shares.value', $user_id)
              ->where('essentials_document_shares.value_type', 'user');
          });
      })
      ->where('essentials_documents.type', $type)
      ->select(
        'users.first_name',
        'users.last_name',
        'essentials_documents.type',
        'essentials_documents.user_id',
        'essentials_documents.name',
        'essentials_documents.description',
        'essentials_documents.created_at',
        'essentials_documents.id'
      )
      ->groupBy('essentials_documents.id')
      ->get();

    // Prepare response for API in JSON format
    return response()->json([
      'documents' => $documents,
      'status' => 'success',
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
    try {
      $user = auth()->user();  // Get the authenticated user
      $business_id = $user->business_id;  // Access business ID from the authenticated user

      // Check if user has permission
      if (!($user->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
        return response()->json(['error' => 'Unauthorized action.'], 403);
      }

      // Retrieve and validate document data
      $document = $request->only(['name', 'description']);

      if (is_string($document['name'])) {
        $type = 'memos';
        $name = $document['name'];  // Use the name directly for memos
      } else {
        $type = 'document';
        $name = $this->moduleUtil->uploadFile($request, 'name', 'documents');  // Handle document upload
      }

      // Prepare the data to be stored
      $doc = [
        'business_id' => $business_id,
        'user_id' => $user->id,  // Use authenticated user's ID
        'type' => $type,
        'name' => $name,
        'description' => $document['description'],
      ];

      // Save the document
      Document::create($doc);

      // Return success response for API
      return response()->json([
        'success' => true,
        'message' => __('lang_v1.success'),
        'document' => $doc  // Optionally return the created document
      ], 201);

    } catch (\Exception $e) {
      // Log the exception
      \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

      // Return error response for API
      return response()->json([
        'success' => false,
        'message' => __('messages.something_went_wrong'),
        'error' => $e->getMessage()
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
    $user = auth()->user();  // Get the authenticated user
    $business_id = $user->business_id;  // Get the business ID

    // Check if user has permission
    if (!($user->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
      return response()->json(['error' => 'Unauthorized action.'], 403);
    }

    try {
      // Fetch the document by ID and ensure it belongs to the authenticated user's business
      $memo = Document::where('business_id', $business_id)->findOrFail($id);

      // Return the document as a JSON response
      return response()->json([
        'success' => true,
        'memo' => $memo
      ], 200);
    } catch (\Exception $e) {
      // If the document is not found or any other error occurs
      return response()->json([
        'success' => false,
        'message' => __('messages.something_went_wrong'),
        'error' => $e->getMessage()
      ], 500);
    }
  }


  /**
   * Show the form for editing the specified resource.
   *
   * @return Response
   */
  public function edit()
  {
    return view('essentials::edit');
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  Request  $request
   * @return Response
   */
  public function update(Request $request)
  {
  }

  /**
   * Remove the specified resource from storage.
   *
   * @return Response
   */
  public function destroy($id)
  {
    $user = auth()->user();  // Get the authenticated user
    $business_id = $user->business_id;  // Access business ID from the authenticated user

    // Check if user has permission
    if (!($user->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
      return response()->json(['error' => 'Unauthorized action.'], 403);
    }

    try {
      // Retrieve the document that belongs to the business
      $document = Document::where('business_id', $business_id)->findOrFail($id);

      // Check if the document belongs to the authenticated user
      if ($user->id == $document->user_id) {
        // If the document is of type 'document', delete the associated file
        if ($document->type == 'document') {
          $file_name = $document->name;
          $path = 'documents/' . $file_name;
          Storage::delete($path);  // Delete file from storage
        }

        // Delete document or memo from the database
        $document->delete();

        // Return a success response
        return response()->json([
          'success' => true,
          'message' => __('lang_v1.success')
        ], 200);
      } else {
        // If the user doesn't own the document, return unauthorized
        return response()->json(['error' => 'Unauthorized to delete this document.'], 403);
      }
    } catch (\Exception $e) {
      // Log the exception
      \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

      // Return an error response
      return response()->json([
        'success' => false,
        'message' => __('messages.something_went_wrong'),
        'error' => $e->getMessage()
      ], 500);
    }
  }


  /**
   * Download a document
   *
   * @return Response
   */
  public function download(Request $request, $id)
  {
    try {
      $user = auth()->user();  // Get the authenticated user
      $business_id = $user->business_id;  // Get the business ID from the authenticated user

      // Check if the user has permission
      if (!($user->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
        return response()->json(['error' => 'Unauthorized action.'], 403);
      }

      // Find the document by ID and ensure it belongs to the authenticated user's business
      $document = Document::where('business_id', $business_id)->findOrFail($id);
      $creator = $document->user_id;

      // Check if the document is shared with the user or their role
      $document_share = DocumentShare::where('document_id', $id)
        ->where(function ($query) use ($user) {
          $query->where('essentials_document_shares.value', '=', $user->id)
            ->where('essentials_document_shares.value_type', '=', 'user')
            ->orWhere('essentials_document_shares.value', '=', $user->roles()->first()->id)
            ->where('essentials_document_shares.value_type', '=', 'role');
        })
        ->first();

      // Prepare the file path and name
      $name = $document->name;
      $file = explode('_', $name, 2);
      $file_name = $file[1];  // Get the actual file name
      $path = 'uploads/documents/' . $name;  // Adjusted path for public folder

      // Check if the file exists in the 'public/uploads' folder
      if (!file_exists(public_path($path))) {
        return response()->json(['error' => 'File not found.'], 404);
      }

      // Generate the public download link using the 'uploads' folder
      $download_link = url("uploads/documents/{$name}");

      return response()->json([
        'success' => true,
        'file_name' => $file_name,
        'download_link' => $download_link
      ], 200);

    } catch (\Exception $e) {
      // Log the error and return a generic error message
      \Log::emergency('File:' . $e->getFile() . ' Line:' . $e->getLine() . ' Message:' . $e->getMessage());

      return response()->json([
        'success' => false,
        'message' => __('messages.something_went_wrong'),
        'error' => $e->getMessage()
      ], 500);
    }
  }








}
