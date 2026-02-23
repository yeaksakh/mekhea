<?php
namespace Modules\Connector\Http\Controllers\Api;

use App\Category;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\AssetManagement\Entities\Asset;
use Modules\Connector\Http\Controllers\Api\ApiController;
class TaxonomyController extends ApiController
{
  protected $moduleUtil;

  public function __construct(ModuleUtil $moduleUtil)
  {
    $this->moduleUtil = $moduleUtil;
  }
  /**
   * Display a listing of the resource.
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    try {
      $category_type = request()->get('type');
      $user = Auth::user();

      // Retrieve the business_id from the request (or from the user, depending on your setup)
      $business_id = $user->business_id;// or auth()->user()->business_id

      // Ensure business_id is valid
      if (empty($business_id)) {
        return response()->json(['error' => 'Business ID is required.'], 400);
      }

      // Query categories based on the business ID and category type
      $categories = Category::where('business_id', $business_id)
        ->where('category_type', $category_type)
        ->select(['name', 'description', 'id', 'business_id'])
        ->get();

      // Ensure categories are found
      if ($categories->isEmpty()) {
        return response()->json(['error' => 'No categories found.'], 404);
      }

      // Map the categories to include only name, quantity (count of assets), and description
      $category_data = $categories->map(function ($category) {
        $quantity = Asset::where('business_id', $category->business_id)
          ->where('category_id', $category->id)
          ->count();

        return [
          'id' => $category->id,
          'name' => $category->name,
          'short_code' => $category->short_code,
          'quantity' => $quantity,  // Count of assets in the category
          'description' => $category->description,
        ];
      });

      // Return the result as a JSON response
      return response()->json([
        'data' => $category_data
      ]);

    } catch (\Exception $e) {
      // Log the error for debugging
      \Log::error('Error in TaxonomyController@index: ' . $e->getMessage());

      // Return a generic 500 error response with the error message for easier debugging
      return response()->json(['error' => 'Internal Server Error: ' . $e->getMessage()], 500);
    }

  }
  public function store(Request $request)
  {
    try {
      // Get the authenticated user and their business ID
      $user = Auth::user();
      $business_id = $user->business_id;

      // Validate the incoming request data
      $request->validate([
        'name' => 'required|string|max:255',
        'short_code' => 'nullable|string|max:255',
        'category_type' => 'required|string',
        'description' => 'nullable|string',
        'parent_id' => 'nullable|integer',
      ]);

      // Prepare the input data for category creation
      $input = $request->only(['name', 'short_code', 'category_type', 'description']);

      // Handle subcategory logic
      if (!empty($request->input('add_as_sub_cat')) && $request->input('add_as_sub_cat') == 1 && !empty($request->input('parent_id'))) {
        $input['parent_id'] = $request->input('parent_id');
      } else {
        $input['parent_id'] = 0; // No parent, main category
      }

      // Assign business_id from the authenticated user
      $input['business_id'] = $business_id;

      // Assign the created_by field as the authenticated user ID
      $input['created_by'] = $user->id;

      // Create the category
      $category = Category::create($input);

      // Return success response
      $output = [
        'success' => true,
        'data' => $category,
        'msg' => __('category.added_success'),
      ];
    } catch (\Exception $e) {
      // Log any errors that occur
      \Log::emergency('File:' . $e->getFile() . ' Line:' . $e->getLine() . ' Message:' . $e->getMessage());

      // Return failure response
      $output = [
        'success' => false,
        'msg' => __('messages.something_went_wrong'),
      ];
    }

    // Return the response as JSON
    return response()->json($output);
  }
  public function update(Request $request, $id)
  {
    try {
      // Get the authenticated user and their business ID
      $user = Auth::user();
      $business_id = $user->business_id;

      // Validate the incoming request data
      $request->validate([
        'name' => 'required|string|max:255',
        'short_code' => 'nullable|string|max:255',
        'category_type' => 'required|string',
        'description' => 'nullable|string',
        'parent_id' => 'nullable|integer',
      ]);

      // Find the category by ID and ensure it belongs to the user's business
      $category = Category::where('business_id', $business_id)->findOrFail($id);

      // Update the category data
      $category->name = $request->input('name');
      $category->short_code = $request->input('short_code');
      $category->category_type = $request->input('category_type');
      $category->description = $request->input('description');

      // Handle subcategory logic
      if (!empty($request->input('add_as_sub_cat')) && $request->input('add_as_sub_cat') == 1 && !empty($request->input('parent_id'))) {
        $category->parent_id = $request->input('parent_id');
      } else {
        $category->parent_id = 0; // No parent, main category
      }

      // Save the updated category
      $category->save();

      // Return success response
      $output = [
        'success' => true,
        'data' => $category,
        'msg' => __('category.updated_success'),
      ];
    } catch (\Exception $e) {
      // Log any errors that occur
      \Log::emergency('File:' . $e->getFile() . ' Line:' . $e->getLine() . ' Message:' . $e->getMessage());

      // Return failure response
      $output = [
        'success' => false,
        'msg' => __('messages.something_went_wrong'),
      ];
    }

    // Return the response as JSON
    return response()->json($output);
  }

  public function destroy($id)
  {
    try {
      // Get the authenticated user and their business ID
      $user = Auth::user();
      $business_id = $user->business_id;

      // Find the category by ID and ensure it belongs to the user's business
      $category = Category::where('business_id', $business_id)->findOrFail($id);

      // Delete the category
      $category->delete();

      // Return success response
      $output = [
        'success' => true,
        'msg' => __('category.deleted_success'),
      ];
    } catch (\Exception $e) {
      // Log any errors that occur
      \Log::emergency('File:' . $e->getFile() . ' Line:' . $e->getLine() . ' Message:' . $e->getMessage());

      // Return failure response
      $output = [
        'success' => false,
        'msg' => __('messages.something_went_wrong'),
      ];
    }

    // Return the response as JSON
    return response()->json($output);
  }


}