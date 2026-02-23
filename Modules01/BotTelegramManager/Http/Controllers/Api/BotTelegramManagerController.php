<?php

namespace Modules\BotTelegramManager\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Contact;
use App\Product;
use Yajra\DataTables\Facades\DataTables;
use Modules\BotTelegramManager\Entities\BotTelegramManager;
use Modules\BotTelegramManager\Entities\BotTelegramManagerCategory;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Illuminate\Support\Facades\Auth;
use App\Utils\ModuleUtil;
use Illuminate\Support\Facades\Schema;
use App\Utils\TransactionUtil;

class BotTelegramManagerController extends Controller
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
        $tableName = 'bottelegrammanager_main';

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
            $additionalColumns = json_decode('[{"name":"id_1","type":"file"}]', true);

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

        $module = ModuleCreator::where('module_name', 'bottelegrammanager')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.bottelegrammanager'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

       $BotTelegramManager = BotTelegramManager::where('bottelegrammanager_main.business_id', $business_id)
        ->leftJoin('bottelegrammanager_category as bottelegrammanagercategory', 'bottelegrammanager_main.category_id', '=', 'bottelegrammanagercategory.id')
        ->where('bottelegrammanager_main.business_id', $business_id)
        ->select('bottelegrammanager_main.*', 'bottelegrammanagercategory.name as category_name');

         if (!empty(request()->start_date) && !empty(request()->end_date)) {
            $start = request()->start_date;
            $end = request()->end_date;
            $BotTelegramManager->whereDate('created_at', '>=', $start)
                ->whereDate('created_at', '<=', $end);
        }

        
        

        $result = $BotTelegramManager->get();

        return response()->json($result);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'bottelegrammanager')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.bottelegrammanager'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $bottelegrammanager_categories = BotTelegramManagerCategory::forDropdown($business_id);
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
            'categories' => $bottelegrammanager_categories,
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
            
            
            
            
                
                                        
        ]);

        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $bottelegrammanager = new BotTelegramManager();
            $bottelegrammanager->title = $request->title;
            $bottelegrammanager->description = $request->description;
            $bottelegrammanager->business_id = $business_id;
            $bottelegrammanager->category_id = $request->category_id;
            $bottelegrammanager->created_by = auth()->user()->id;
            
            
            
              
             
             
            
                            if ($request->hasFile('id_1')) {
                                $documentPath = $this->transactionUtil->uploadFile($request, 'id_1', 'BotTelegramManager');
                                $bottelegrammanager->id_1 = $documentPath;
                            }
                         
            $bottelegrammanager->save();

            return response()->json(['success' => true, 'msg' => __('bottelegrammanager::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function edit($id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'bottelegrammanager')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.bottelegrammanager'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $bottelegrammanager = BotTelegramManager::find($id);
        $bottelegrammanager = BotTelegramManagerCategory::forDropdown($business_id);
        $users = User::forDropdown($business_id);

        return response()->json([
            'categories' => $bottelegrammanager_categories,
            'users' => $users,
            'bottelegrammanager' => $bottelegrammanager,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|integer', 
            
            
            
              
              
             
        ]);

        try {
            $bottelegrammanager = BotTelegramManager::find($id);
            $bottelegrammanager->title = $request->title;
            $bottelegrammanager->description = $request->description;
            $bottelegrammanager->category_id = $request->category_id;
            $bottelegrammanager->created_by = auth()->user()->id;
            
            
            
            
             
             
            
                            if ($request->hasFile('id_1')) {
                                $oldFile = public_path('uploads/tracking/' . basename($bottelegrammanager->id_1));
                                if (file_exists($oldFile)) {
                                    unlink($oldFile);
                                }
                                $documentPath = $this->transactionUtil->uploadFile($request, 'id_1', 'BotTelegramManager');
                                $bottelegrammanager->id_1 = $documentPath;
                            }
                        
            $bottelegrammanager->save();

            return response()->json(['success' => true, 'msg' => __('bottelegrammanager::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    

    public function destroy($id)
    {
        try {
            BotTelegramManager::destroy($id);
            return response()->json(['success' => true, 'msg' => __('bottelegrammanager::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'bottelegrammanager')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.bottelegrammanager'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
        
        $categories = BotTelegramManagerCategory::where('business_id', $business_id)->get();
        
        return response()->json([
            'categories' => $categories,
        ]);
    
    }

    public function storeCategory(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $bottelegrammanager = new BotTelegramManagerCategory();
            $bottelegrammanager->name = $request->name;
            $bottelegrammanager->description = $request->description;
            $bottelegrammanager->business_id = $business_id;
            $bottelegrammanager->save();

            return response()->json(['success' => true, 'msg' => __('bottelegrammanager::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'bottelegrammanager')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.bottelegrammanager'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
            
        $category = BotTelegramManagerCategory::find($id);

        return response()->json([
            'category' => $category,
        ]);
    }

    public function updateCategory(Request $request, $id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $category = BotTelegramManagerCategory::find($id);
            $category->name = $request->name;
            $category->description = $request->description;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('bottelegrammanager::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function destroyCategory($id)
    {
        try {
            BotTelegramManagerCategory::destroy($id);
            return response()->json(['success' => true, 'msg' => __('bottelegrammanager::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
}