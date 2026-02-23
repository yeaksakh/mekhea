<?php

namespace Modules\Connector\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\LaundryCategory;
use App\LaundryPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LaundryPackageApiController extends Controller
{
    public function index(Request $request)
{
    $query = LaundryPackage::query();

    // Filter by id if provided
    if ($request->has('id')) {
        $query->where('id', $request->input('id'));
    }

    // Filter by category_id if provided
    if ($request->has('category_id')) {
        $query->where('category_id', $request->input('category_id'));
    }

    $packages = $query->get()->groupBy('category.name');

    return response()->json([
        'status' => 'success',
        'data' => $packages
    ], 200);
}

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer|exists:laundry_category,id',
            'business_id' => 'required|integer|exists:business,id',
            'location_id' => 'required|integer|exists:business_locations,id',
            'package_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $package = LaundryPackage::create($request->only([
            'category_id',
            'business_id',
            'location_id',
            'package_name',
            'quantity',
            'price'
        ]));

        return response()->json([
            'status' => 'success',
            'data' => $package->load('category')
        ], 201);
    }

    public function show(LaundryPackage $laundryPackage)
    {
        return response()->json([
            'status' => 'success',
            'data' => $laundryPackage->load('category')
        ], 200);
    }

    public function update(Request $request, LaundryPackage $laundryPackage)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer|exists:laundry_category,id',
            'business_id' => 'required|integer|exists:business,id',
            'location_id' => 'required|integer|exists:business_locations,id',
            'package_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $laundryPackage->update($request->only([
            'category_id',
            'business_id',
            'location_id',
            'package_name',
            'quantity',
            'price'
        ]));

        return response()->json([
            'status' => 'success',
            'data' => $laundryPackage->load('category')
        ], 200);
    }

    public function destroy(LaundryPackage $laundryPackage)
    {
        $laundryPackage->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Package deleted successfully'
        ], 200);
    }



    public function getCategories()
    {
        // Get business_id from authenticated user or request
        $business_id =  auth()->user()->business_id;
        
        $categories = LaundryCategory::where('business_id', $business_id)->get();

        // Return JSON response
        return response()->json([
            'status' => 'success',
            'data' => $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'business_id' => $category->business_id,
                ];
            }),
        ], 200);
    }
    public function storeCategory(Request $request)
    {
        $business_id = $request->business_id;
        try {
            // Validate the request data
            $request->validate([
                'name' => 'required|string|max:255',
                'business_id' => 'required|exists:business,id',
                'description' => 'nullable|string',
            ]);

            $existingCategory = LaundryCategory::where('name', $request->name)
                ->where('business_id', $business_id)
                ->first();

            if ($existingCategory) {
                return response()->json([
                    'success' => false,
                    'msg' => __('LaundryCategory::lang.category_already_exists'),
                ], 422); // 422 Unprocessable Entity for validation-like errors
            }

            // Update or create the category
            $category = LaundryCategory::updateOrCreate(
                [
                    'name' => $request->name,
                    'business_id' => $business_id,
                ],
                [
                    'description' => $request->description,
                ]
            );

            return response()->json([
                'success' => true,
                'msg' => __('LaundryCategory::lang.saved_successfully'),
                'category' => $category,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
