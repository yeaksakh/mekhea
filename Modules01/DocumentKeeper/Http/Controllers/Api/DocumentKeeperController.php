<?php

namespace Modules\DocumentKeeper\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Contact;
use App\Product;
use Yajra\DataTables\Facades\DataTables;
use Modules\DocumentKeeper\Entities\DocumentKeeper;
use Modules\DocumentKeeper\Entities\DocumentKeeperCategory;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Illuminate\Support\Facades\Auth;
use App\Utils\ModuleUtil;
use Illuminate\Support\Facades\Schema;
use App\Utils\TransactionUtil;

class DocumentKeeperController extends Controller
{
    protected $moduleUtil;
    protected $transactionUtil;

    public function __construct(
        ModuleUtil $moduleUtil,
        TransactionUtil $transactionUtil
    )
    {
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
    }
    
     public function modulefield()
    {
        $tableName = 'documentkeeper_main';

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
            $additionalColumns = json_decode('[{"name":"title_1","type":"text"},{"name":"file_2","type":"file"}]', true);

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
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'documentkeeper')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.documentkeeper'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

       $DocumentKeeper = DocumentKeeper::where('documentkeeper_main.business_id', $business_id)
        ->leftJoin('documentkeeper_category as documentkeepercategory', 'documentkeeper_main.category_id', '=', 'documentkeepercategory.id')
        ->where('documentkeeper_main.business_id', $business_id)
        ->select('documentkeeper_main.*', 'documentkeepercategory.name as category_name');

         if (!empty(request()->start_date) && !empty(request()->end_date)) {
            $start = request()->start_date;
            $end = request()->end_date;
            $DocumentKeeper->whereDate('created_at', '>=', $start)
                ->whereDate('created_at', '<=', $end);
        }

        
        

        $result = $DocumentKeeper->get();

        return response()->json($result);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'documentkeeper')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.documentkeeper'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $documentkeeper_categories = DocumentKeeperCategory::forDropdown($business_id);
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
            'categories' => $documentkeeper_categories,
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
                                                    
        ]);

        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $documentkeeper = new DocumentKeeper();
            $documentkeeper->title = $request->title;
            $documentkeeper->description = $request->description;
            $documentkeeper->business_id = $business_id;
            $documentkeeper->category_id = $request->category_id;
            $documentkeeper->created_by = auth()->user()->id;
            
            
            
              
             
            
                            $documentkeeper->title_1 = $request->title_1;
                         
            
                            if ($request->hasFile('file_2')) {
                                $documentPath = $this->transactionUtil->uploadFile($request, 'file_2', 'DocumentKeeper');
                                $documentkeeper->file_2 = $documentPath;
                            }
                         
            $documentkeeper->save();

            return response()->json(['success' => true, 'msg' => __('documentkeeper::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function edit($id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'documentkeeper')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.documentkeeper'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $documentkeeper = DocumentKeeper::find($id);
        $documentkeeper = DocumentKeeperCategory::forDropdown($business_id);
        $users = User::forDropdown($business_id);

        return response()->json([
            'categories' => $documentkeeper_categories,
            'users' => $users,
            'documentkeeper' => $documentkeeper,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|integer', 
            
            
            
              
              
            
                            'title_1' => 'nullable',
                         
        ]);

        try {
            $documentkeeper = DocumentKeeper::find($id);
            $documentkeeper->title = $request->title;
            $documentkeeper->description = $request->description;
            $documentkeeper->category_id = $request->category_id;
            $documentkeeper->created_by = auth()->user()->id;
            
            
            
            
             
            
                            $documentkeeper->title_1 = $request->title_1;
                         
            
                            if ($request->hasFile('file_2')) {
                                $oldFile = public_path('uploads/tracking/' . basename($documentkeeper->file_2));
                                if (file_exists($oldFile)) {
                                    unlink($oldFile);
                                }
                                $documentPath = $this->transactionUtil->uploadFile($request, 'file_2', 'DocumentKeeper');
                                $documentkeeper->file_2 = $documentPath;
                            }
                        
            $documentkeeper->save();

            return response()->json(['success' => true, 'msg' => __('documentkeeper::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    

    public function destroy($id)
    {
        try {
            DocumentKeeper::destroy($id);
            return response()->json(['success' => true, 'msg' => __('documentkeeper::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'documentkeeper')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.documentkeeper'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
        
        $categories = DocumentKeeperCategory::where('business_id', $business_id)->get();
        
        return response()->json([
            'categories' => $categories,
        ]);
    
    }

    public function storeCategory(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $documentkeeper = new DocumentKeeperCategory();
            $documentkeeper->name = $request->name;
            $documentkeeper->description = $request->description;
            $documentkeeper->business_id = $business_id;
            $documentkeeper->save();

            return response()->json(['success' => true, 'msg' => __('documentkeeper::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'documentkeeper')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.documentkeeper'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
            
        $category = DocumentKeeperCategory::find($id);

        return response()->json([
            'category' => $category,
        ]);
    }

    public function updateCategory(Request $request, $id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $category = DocumentKeeperCategory::find($id);
            $category->name = $request->name;
            $category->description = $request->description;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('documentkeeper::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function destroyCategory($id)
    {
        try {
            DocumentKeeperCategory::destroy($id);
            return response()->json(['success' => true, 'msg' => __('documentkeeper::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
}