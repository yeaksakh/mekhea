<?php

namespace Modules\ProductDoc\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Contact;
use App\Product;
use Yajra\DataTables\Facades\DataTables;
use Modules\ProductDoc\Entities\ProductDoc;
use Modules\ProductDoc\Entities\ProductDocCategory;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Illuminate\Support\Facades\Auth;
use App\Utils\ModuleUtil;
use Illuminate\Support\Facades\Schema;
use App\Utils\TransactionUtil;

class ProductDocController extends Controller
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
        $tableName = 'productdoc_main';

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
            $additionalColumns = json_decode('[{"name":"Product_1","type":"product"},{"name":"productFile1_5","type":"file"},{"name":"productFile2_6","type":"file"},{"name":"productFile3_7","type":"file"},{"name":"productFile4_8","type":"file"}]', true);

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

        $module = ModuleCreator::where('module_name', 'productdoc')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.productdoc'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

       $ProductDoc = ProductDoc::where('productdoc_main.business_id', $business_id)
        ->leftJoin('productdoc_category as productdoccategory', 'productdoc_main.category_id', '=', 'productdoccategory.id')
        ->where('productdoc_main.business_id', $business_id)
        ->select('productdoc_main.*', 'productdoccategory.name as category_name');

         if (!empty(request()->start_date) && !empty(request()->end_date)) {
            $start = request()->start_date;
            $end = request()->end_date;
            $ProductDoc->whereDate('created_at', '>=', $start)
                ->whereDate('created_at', '<=', $end);
        }

        
        

        $result = $ProductDoc->get();

        return response()->json($result);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'productdoc')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.productdoc'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $productdoc_categories = ProductDocCategory::forDropdown($business_id);
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
            'categories' => $productdoc_categories,
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
            
            
            
            
                
            
                            'Product_1' => 'nullable',
                                                    
        ]);

        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $productdoc = new ProductDoc();
            $productdoc->title = $request->title;
            $productdoc->description = $request->description;
            $productdoc->business_id = $business_id;
            $productdoc->category_id = $request->category_id;
            $productdoc->created_by = auth()->user()->id;
            
            
            
              
             
            
                            $productdoc->Product_1 = $request->Product_1;
                         
            
                            if ($request->hasFile('productFile1_5')) {
                                $documentPath = $this->transactionUtil->uploadFile($request, 'productFile1_5', 'ProductDoc');
                                $productdoc->productFile1_5 = $documentPath;
                            }
                        

                            if ($request->hasFile('productFile2_6')) {
                                $documentPath = $this->transactionUtil->uploadFile($request, 'productFile2_6', 'ProductDoc');
                                $productdoc->productFile2_6 = $documentPath;
                            }
                        

                            if ($request->hasFile('productFile3_7')) {
                                $documentPath = $this->transactionUtil->uploadFile($request, 'productFile3_7', 'ProductDoc');
                                $productdoc->productFile3_7 = $documentPath;
                            }
                        

                            if ($request->hasFile('productFile4_8')) {
                                $documentPath = $this->transactionUtil->uploadFile($request, 'productFile4_8', 'ProductDoc');
                                $productdoc->productFile4_8 = $documentPath;
                            }
                         
            $productdoc->save();

            return response()->json(['success' => true, 'msg' => __('productdoc::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function edit($id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'productdoc')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.productdoc'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $productdoc = ProductDoc::find($id);
        $productdoc = ProductDocCategory::forDropdown($business_id);
        $users = User::forDropdown($business_id);

        return response()->json([
            'categories' => $productdoc_categories,
            'users' => $users,
            'productdoc' => $productdoc,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|integer', 
            
            
            
              
              
            
                            'Product_1' => 'nullable',
                         
        ]);

        try {
            $productdoc = ProductDoc::find($id);
            $productdoc->title = $request->title;
            $productdoc->description = $request->description;
            $productdoc->category_id = $request->category_id;
            $productdoc->created_by = auth()->user()->id;
            
            
            
            
             
            
                            $productdoc->Product_1 = $request->Product_1;
                         
            
                            if ($request->hasFile('productFile1_5')) {
                                $oldFile = public_path('uploads/tracking/' . basename($productdoc->productFile1_5));
                                if (file_exists($oldFile)) {
                                    unlink($oldFile);
                                }
                                $documentPath = $this->transactionUtil->uploadFile($request, 'productFile1_5', 'ProductDoc');
                                $productdoc->productFile1_5 = $documentPath;
                            }
                        

                            if ($request->hasFile('productFile2_6')) {
                                $oldFile = public_path('uploads/tracking/' . basename($productdoc->productFile2_6));
                                if (file_exists($oldFile)) {
                                    unlink($oldFile);
                                }
                                $documentPath = $this->transactionUtil->uploadFile($request, 'productFile2_6', 'ProductDoc');
                                $productdoc->productFile2_6 = $documentPath;
                            }
                        

                            if ($request->hasFile('productFile3_7')) {
                                $oldFile = public_path('uploads/tracking/' . basename($productdoc->productFile3_7));
                                if (file_exists($oldFile)) {
                                    unlink($oldFile);
                                }
                                $documentPath = $this->transactionUtil->uploadFile($request, 'productFile3_7', 'ProductDoc');
                                $productdoc->productFile3_7 = $documentPath;
                            }
                        

                            if ($request->hasFile('productFile4_8')) {
                                $oldFile = public_path('uploads/tracking/' . basename($productdoc->productFile4_8));
                                if (file_exists($oldFile)) {
                                    unlink($oldFile);
                                }
                                $documentPath = $this->transactionUtil->uploadFile($request, 'productFile4_8', 'ProductDoc');
                                $productdoc->productFile4_8 = $documentPath;
                            }
                        
            $productdoc->save();

            return response()->json(['success' => true, 'msg' => __('productdoc::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    

    public function destroy($id)
    {
        try {
            ProductDoc::destroy($id);
            return response()->json(['success' => true, 'msg' => __('productdoc::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'productdoc')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.productdoc'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
        
        $categories = ProductDocCategory::where('business_id', $business_id)->get();
        
        return response()->json([
            'categories' => $categories,
        ]);
    
    }

    public function storeCategory(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $productdoc = new ProductDocCategory();
            $productdoc->name = $request->name;
            $productdoc->description = $request->description;
            $productdoc->business_id = $business_id;
            $productdoc->save();

            return response()->json(['success' => true, 'msg' => __('productdoc::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'productdoc')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.productdoc'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
            
        $category = ProductDocCategory::find($id);

        return response()->json([
            'category' => $category,
        ]);
    }

    public function updateCategory(Request $request, $id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $category = ProductDocCategory::find($id);
            $category->name = $request->name;
            $category->description = $request->description;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('productdoc::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function destroyCategory($id)
    {
        try {
            ProductDocCategory::destroy($id);
            return response()->json(['success' => true, 'msg' => __('productdoc::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
}