<?php

namespace Modules\Documentary\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Contact;
use App\Product;
use Yajra\DataTables\Facades\DataTables;
use Modules\Documentary\Entities\Documentary;
use Modules\Documentary\Entities\DocumentaryCategory;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Illuminate\Support\Facades\Auth;
use App\Utils\ModuleUtil;
use Illuminate\Support\Facades\Schema;
use App\Utils\TransactionUtil;

class DocumentaryController extends Controller
{
    protected $moduleUtil;
    protected $transactionUtil;

    public function __construct(
        ModuleUtil $moduleUtil,
        TransactionUtil $transactionUtil
    ) {
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
    }

    public function modulefield()
    {
        $tableName = 'documentary_main';

        try {
            // Query the information schema to get column details
            $columns = DB::select(DB::raw("SHOW COLUMNS FROM $tableName"));

            // Prepare the response as an associative array to check for duplicates
            $columnInfo = [];
            foreach ($columns as $column) {
                $columnInfo[$column->Field] = [
                    'name' => $column->Field,
                    'type' => $column->Type,
                ];
            }

            // Add dynamic columns
            $additionalColumns = json_decode('[{"name":"title_1","type":"string"},{"name":"url_5","type":"string"},{"name":"file_6","type":"file"}]', true);

            if (is_array($additionalColumns)) {
                foreach ($additionalColumns as $additionalColumn) {
                    $columnName = $additionalColumn['name'];

                    // Always replace the existing static column with the dynamic column
                    $columnInfo[$columnName] = $additionalColumn;
                }
            }

            // Convert back to an indexed array
            $columnInfo = array_values($columnInfo);

            return response()->json($columnInfo);
        } catch (\Exception $e) {
            // Return a JSON response with the error message
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        // Fetch all categories
        $categories = \Modules\Documentary\Entities\DocumentaryCategory::all();

        // Fetch all documentaries
        $documentaries = \Modules\Documentary\Entities\Documentary::all();

        // Map documentaries by category_id
        $docsByCategory = [];
        foreach ($documentaries as $doc) {
            // Ensure file path is correct
            $filePath = $doc->file_6;
            if ($filePath) {
                // Remove any leading folder paths to avoid duplication
                $fileName = basename($filePath);
                $filePath = 'uploads/Documentary/' . $fileName;
            }

            $docsByCategory[$doc->category_id][] = [
                'id' => $doc->id,
                'title' => $doc->title_1,
                'url' => $doc->url_5,
                'file' => $filePath ? url($filePath) : null,
                'created_at' => $doc->created_at,
            ];
        }

        $result = [];

        // Main categories
        foreach ($categories->whereNull('parent_id') as $main) {
            $subcategories = [];
            foreach ($categories->where('parent_id', $main->id) as $sub) {
                $subcategories[] = [
                    'id' => $sub->id,
                    'name' => $sub->name,
                    'description' => $sub->description ? strip_tags($sub->description) : null,
                    'image' => $sub->image ? url('uploads/Documentary/' . basename($sub->image)) : null,
                    'documentary' => $docsByCategory[$sub->id] ?? [],
                ];
            }

            $result[] = [
                'id' => $main->id,
                'name' => $main->name,
                'description' => $main->description ? strip_tags($main->description) : null,
                'image' => $main->image ? url('uploads/Documentary/' . basename($main->image)) : null,
                'subcategory' => $subcategories,
                'documentary' => $docsByCategory[$main->id] ?? [],
            ];
        }

        // Include uncategorized documentaries
        $uncategorizedDocs = $docsByCategory[null] ?? [];
        if (!empty($uncategorizedDocs)) {
            $result[] = [
                'id' => null,
                'name' => 'Uncategorized',
                'description' => null,
                'image' => null,
                'subcategory' => [],
                'documentary' => $uncategorizedDocs,
            ];
        }

        return response()->json($result);
    }
    
    public function create(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'documentary')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((!auth()->user()->can('module.documentary')) && !auth()->user()->can('superadmin') && !$is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $documentary_categories = DocumentaryCategory::forDropdown($business_id);
        $users = User::forDropdown($business_id);
        $customers = Contact::where('business_id', $business_id)
            ->where('type', 'customer')
            ->pluck('mobile', 'id');
        $suppliers = Contact::where('business_id', $business_id)
            ->where('type', 'supplier')
            ->pluck('mobile', 'id');
        $products = Product::where('business_id', $business_id)
            ->pluck('name', 'id');

        return response()->json([
            'categories' => $documentary_categories,
            'users' => $users,
            'customers' => $customers,
            'suppliers' => $suppliers,
            'products' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|integer',






            'title_1' => 'nullable',


            'url_5' => 'nullable',

        ]);

        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $documentary = new Documentary();
            $documentary->title = $request->title;
            $documentary->description = $request->description;
            $documentary->business_id = $business_id;
            $documentary->category_id = $request->category_id;
            $documentary->created_by = auth()->user()->id;






            $documentary->title_1 = $request->title_1;


            $documentary->url_5 = $request->url_5;


            if ($request->hasFile('file_6')) {
                $documentPath = $this->transactionUtil->uploadFile($request, 'file_6', 'Documentary');
                $documentary->file_6 = $documentPath;
            }

            $documentary->save();

            return response()->json(['success' => true, 'msg' => __('documentary::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function edit($id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'documentary')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((!auth()->user()->can('module.documentary')) && !auth()->user()->can('superadmin') && !$is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $documentary = Documentary::find($id);
        $documentary = DocumentaryCategory::forDropdown($business_id);
        $users = User::forDropdown($business_id);

        return response()->json([
            'categories' => $documentary_categories,
            'users' => $users,
            'documentary' => $documentary,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|integer',






            'title_1' => 'nullable',


            'url_5' => 'nullable',

        ]);

        try {
            $documentary = Documentary::find($id);
            $documentary->title = $request->title;
            $documentary->description = $request->description;
            $documentary->category_id = $request->category_id;
            $documentary->created_by = auth()->user()->id;






            $documentary->title_1 = $request->title_1;


            $documentary->url_5 = $request->url_5;


            if ($request->hasFile('file_6')) {
                $oldFile = public_path('uploads/tracking/' . basename($documentary->file_6));
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
                $documentPath = $this->transactionUtil->uploadFile($request, 'file_6', 'Documentary');
                $documentary->file_6 = $documentPath;
            }

            $documentary->save();

            return response()->json(['success' => true, 'msg' => __('documentary::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }



    public function destroy($id)
    {
        try {
            Documentary::destroy($id);
            return response()->json(['success' => true, 'msg' => __('documentary::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'documentary')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((!auth()->user()->can('module.documentary')) && !auth()->user()->can('superadmin') && !$is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $categories = DocumentaryCategory::where('business_id', $business_id)->get();

        return response()->json([
            'categories' => $categories,
        ]);

    }

    public function storeCategory(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $documentary = new DocumentaryCategory();
            $documentary->name = $request->name;
            $documentary->description = $request->description;
            $documentary->business_id = $business_id;
            $documentary->save();

            return response()->json(['success' => true, 'msg' => __('documentary::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'documentary')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((!auth()->user()->can('module.documentary')) && !auth()->user()->can('superadmin') && !$is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $category = DocumentaryCategory::find($id);

        return response()->json([
            'category' => $category,
        ]);
    }

    public function updateCategory(Request $request, $id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $category = DocumentaryCategory::find($id);
            $category->name = $request->name;
            $category->description = $request->description;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('documentary::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function destroyCategory($id)
    {
        try {
            DocumentaryCategory::destroy($id);
            return response()->json(['success' => true, 'msg' => __('documentary::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
}