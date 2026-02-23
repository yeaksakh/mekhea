<?php

namespace Modules\Connector\Http\Controllers\Api;

use App\User;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Connector\Http\Controllers\Api\ApiController;
use Modules\Essentials\Entities\Document;
use Modules\Essentials\Entities\DocumentShare;
use Modules\Essentials\Notifications\DocumentShareNotification;

class DocumentShareController extends ApiController
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
   * Show the form for editing the specified resource.
   *
   * @return Response
   */
  /**
   * Update the specified resource in storage.
   *
   * @param  Request  $request
   * @return Response
   */

  public function show($id)
  {
    // Assuming the business_id is passed in the request or obtained from the authenticated user
    $business_id = auth()->user()->business_id;

    // Check permissions for superadmin or essentials_module subscription
    if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
      return response()->json(['error' => 'Unauthorized action.'], 403);
    }

    // Get the type from the request if necessary
    $type = request()->get('type', null);

    // Fetch the list of users for the business
    $users = User::forDropdown($business_id, false);

    // Fetch the roles dropdown
    $roles = $this->moduleUtil->getDropdownForRoles($business_id);

    // Fetch the shared documents grouped by value type
    $shared_documents = DocumentShare::where('document_id', $id)
      ->get()
      ->groupBy('value_type');

    // Prepare shared roles and users arrays
    $shared_role = !empty($shared_documents['role']) ? $shared_documents['role']->pluck('value')->toArray() : [];
    $shared_user = !empty($shared_documents['user']) ? $shared_documents['user']->pluck('value')->toArray() : [];

    // Prepare the data for the API response
    $response_data = [
      'users' => $users,
      'document_id' => $id,
      'roles' => $roles,
      'shared_user' => $shared_user,
      'shared_role' => $shared_role,
      'type' => $type
    ];

    // Return the data as JSON response
    return response()->json($response_data, 200);
  }

  public function update(Request $request)
  {
    // Assume that the `business_id` is passed in the request (you can adjust this as needed)
    $business_id = $request->get('business_id');

    // Check user permissions for superadmin or essentials_module
    if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
      return response()->json(['error' => 'Unauthorized action.'], 403);
    }

    // Process the document updates
    $document = $request->only(['user', 'role', 'document_id']);

    // Default values for existing users and roles
    $existing_user_id = [0];
    $existing_role_id = [0];

    // Find the document to be updated
    $document_obj = Document::find($document['document_id']);
    if (!$document_obj) {
      return response()->json(['error' => 'Document not found.'], 404);
    }

    // Update document shares for users
    if (!empty($document['user'])) {
      foreach ($document['user'] as $user_id) {
        $existing_user_id[] = $user_id;
        $share = [
          'document_id' => $document['document_id'],
          'value_type' => 'user',
          'value' => $user_id,
        ];
        $doc_share = DocumentShare::updateOrCreate($share);

        // Notify user if document share was newly created
        if ($doc_share->wasRecentlyCreated) {
          $this->notify($document_obj, $user_id);
        }
      }
    }

    // Delete users that no longer exist in the document share
    DocumentShare::where('document_id', $document['document_id'])
      ->where('value_type', 'user')
      ->whereNotIn('value', $existing_user_id)
      ->delete();

    // Update document shares for roles
    if (!empty($document['role'])) {
      foreach ($document['role'] as $role_id) {
        $existing_role_id[] = $role_id;
        $share = [
          'document_id' => $document['document_id'],
          'value_type' => 'role',
          'value' => $role_id,
        ];

        DocumentShare::updateOrCreate($share);
      }
    }

    // Delete roles that no longer exist in the document share
    DocumentShare::where('document_id', $document['document_id'])
      ->where('value_type', 'role')
      ->whereNotIn('value', $existing_role_id)
      ->delete();

    // Return success response
    return response()->json([
      'success' => true,
      'msg' => __('lang_v1.success'),
    ], 200);
  }

  /**
   * Sends notification to the user.
   *
   * @return void
   */
  private function notify($document, $user_id)
  {
    $user = User::find($user_id);
    $shared_by = auth()->user();

    $user->notify(new DocumentShareNotification($document, $shared_by));
  }

}
