<?php

namespace Modules\Connector\Http\Controllers\Api;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Modules\Essentials\Entities\KnowledgeBase;
class KnowledgeBaseApiController extends ApiController
{
  protected $moduleUtil;
  public function __construct(ModuleUtil $moduleUtil)
  {
    $this->moduleUtil = $moduleUtil;
  }
  public function index()
  {
    $business_id = auth()->user()->business_id; // Assuming business_id is part of the user's profile

    // Permission check
    if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
      return response()->json(['error' => 'Unauthorized action.'], 403);
    }

    $user_id = auth()->user()->id;

    // Fetch the knowledge bases
    $knowledge_bases = KnowledgeBase::where('business_id', $business_id)
      ->where('kb_type', 'knowledge_base')
      ->whereNull('parent_id')
      ->with(['children', 'children.children'])
      ->where(function ($query) use ($user_id) {
        $query->whereHas('users', function ($q) use ($user_id) {
          $q->where('user_id', $user_id);
        })->orWhere('created_by', $user_id)
          ->orWhere('share_with', 'public');
      })
      ->get();

    // Iterate over the knowledge bases and clean the content
    foreach ($knowledge_bases as $kb) {
      $kb->content = $this->cleanContent($kb->content);

      foreach ($kb->children as $child) {
        $child->content = $this->cleanContent($child->content);

        foreach ($child->children as $grandchild) {
          $grandchild->content = $this->cleanContent($grandchild->content);
        }
      }
    }

    return response()->json([
      'success' => true,
      'data' => $knowledge_bases
    ]);
  }

  /**
   * Cleans the content by stripping HTML tags, replacing special characters,
   * and removing excess newlines and spaces.
   */
  private function cleanContent($content)
  {
    // Remove all HTML tags
    $content = strip_tags($content);

    // Replace non-breaking spaces (&nbsp;) with a regular space
    $content = str_replace('&nbsp;', ' ', $content);

    // Remove excess newlines, carriage returns, and tabs
    $content = preg_replace("/[\r\n]+/", "\n", $content); // Replace multiple newlines with a single newline
    $content = preg_replace("/\s+/", ' ', $content); // Replace multiple spaces and tabs with a single space

    // Trim any remaining excess whitespace at the start or end
    return trim($content);
  }

  public function store(Request $request)
  {
    // Step 1: Check if the request has any data
    if (!$request->has(['title', 'content']) || empty($request->input('title')) || empty($request->input('content'))) {
      return response()->json([
        'success' => false,
        'msg' => 'Please input data'
      ], 400); // Return a 400 Bad Request status
    }

    // Assume that the authenticated user has a 'business_id' and 'id' as part of their user model.
    $business_id = auth()->user()->business_id;

    // Permission check: Ensure the user has the right to perform this action.
    if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
      return response()->json([
        'success' => false,
        'msg' => 'Unauthorized action.'
      ], 403); // Return a 403 status for unauthorized action
    }

    try {
      $user_id = auth()->user()->id;
      $input = $request->only(['title', 'content', 'kb_type', 'parent_id', 'share_with']);

      // Add additional fields for the database record
      $input['business_id'] = $business_id;
      $input['created_by'] = $user_id;
      $input['kb_type'] = $request->input('kb_type', 'knowledge_base'); // Default to 'knowledge_base'
      $input['parent_id'] = $request->input('parent_id', null); // Default to null
      $input['share_with'] = $request->input('share_with', null); // Default to null

      // Create the knowledge base entry
      $kb = KnowledgeBase::create($input);

      // Handle user-specific sharing if 'share_with' is set to 'only_with'
      if ($kb->kb_type == 'knowledge_base' && $kb->share_with == 'only_with') {
        $kb->users()->sync($request->input('user_ids'));
      }

      // Success output
      $output = [
        'success' => true,
        'msg' => __('lang_v1.success'),
      ];

    } catch (\Exception $e) {
      // Log the error for debugging
      \Log::emergency('File:' . $e->getFile() . ' Line:' . $e->getLine() . ' Message:' . $e->getMessage());

      // Error output
      $output = [
        'success' => false,
        'msg' => __('messages.something_went_wrong'),
      ];
      // Return a 500 status code for server errors
      return response()->json($output, 500);
    }
    // Return a successful JSON response with the output
    return response()->json($output);
  }

  public function show($id)
  {
    // Retrieve business_id from the authenticated user
    $business_id = auth()->user()->business_id;

    // Permission check: Ensure the user has the right to view the knowledge base
    if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
      return response()->json([
        'success' => false,
        'msg' => 'Unauthorized action.'
      ], 403); // Return a 403 status for unauthorized action
    }

    // Fetch the knowledge base entry
    $kb_object = KnowledgeBase::where('business_id', $business_id)
      ->with(['children', 'children.children', 'users'])
      ->find($id);

    // Check if the knowledge base entry exists
    if (!$kb_object) {
      return response()->json([
        'success' => false,
        'msg' => 'Knowledge base entry not found.'
      ], 404); // Return a 404 status if not found
    }

    // Clean the content of the knowledge base entry
    $kb_object->content = $this->cleanContent($kb_object->content);

    // Clean the content for children and grandchildren
    foreach ($kb_object->children as $child) {
      $child->content = $this->cleanContent($child->content);

      foreach ($child->children as $grandchild) {
        $grandchild->content = $this->cleanContent($grandchild->content);
      }
    }

    // Return the data in JSON format
    return response()->json([
      'success' => true,
      'data' => [
        'kb_object' => $kb_object
      ]
    ]);
  }

  /**
   * Cleans the content by stripping HTML tags, replacing special characters,
   * and removing excess newlines and spaces.
   */


  public function update(Request $request, $id)
  {
    // Get the business_id from the authenticated user
    $business_id = auth()->user()->business_id;

    // Check if the user has the necessary permissions
    if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
      return response()->json([
        'success' => false,
        'msg' => 'Unauthorized action.'
      ], 403); // Return a 403 status for unauthorized action
    }

    try {
      // Get the title and content from the request
      $input = $request->only(['title', 'content']);

      // Find the knowledge base entry by its ID and business ID
      $kb = KnowledgeBase::where('business_id', $business_id)->findOrFail($id);

      // Update the share_with field if provided
      $input['share_with'] = !empty($request->input('share_with')) ? $request->input('share_with') : null;

      // Update the knowledge base entry
      $kb->update($input);

      // Handle user-specific sharing if 'share_with' is set to 'only_with'
      $user_ids = !empty($request->input('user_ids')) ? $request->input('user_ids') : [];

      if ($kb->kb_type == 'knowledge_base' && $kb->share_with == 'only_with') {
        $kb->users()->sync($user_ids);
      }

      // Success output
      $output = [
        'success' => true,
        'msg' => __('lang_v1.success'),
      ];

    } catch (\Exception $e) {
      // Log the error details for debugging
      \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

      // Error output
      $output = [
        'success' => false,
        'msg' => __('messages.something_went_wrong'),
      ];

      return response()->json($output, 500); // Return a 500 status for server errors
    }

    // Return a successful JSON response with the output
    return response()->json($output, 200);
  }
  public function destroy($id)
  {
    // Get the business_id from the authenticated user
    $business_id = auth()->user()->business_id;

    // Check if the user has the necessary permissions
    if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
      return response()->json([
        'success' => false,
        'msg' => 'Unauthorized action.'
      ], 403); // Return a 403 status for unauthorized action
    }

    try {
      // Attempt to delete the knowledge base entry
      KnowledgeBase::where('business_id', $business_id)
        ->where('id', $id)
        ->delete();

      // Return success message
      return response()->json([
        'success' => true,
        'msg' => __('lang_v1.success')
      ], 200); // 200 OK status

    } catch (\Exception $e) {
      // Log the error for debugging
      \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

      // Return error message
      return response()->json([
        'success' => false,
        'msg' => __('messages.something_went_wrong')
      ], 500); // Return a 500 status for server errors
    }
  }

}