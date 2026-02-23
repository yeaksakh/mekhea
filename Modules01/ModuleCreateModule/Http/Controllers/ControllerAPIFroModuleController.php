<?php

namespace Modules\ModuleCreateModule\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ControllerAPIFroModuleController extends Controller
{
    protected $files;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function ApiController($moduleName, $moduleType, $title, $type)
    {
        $controllerPath = base_path("Modules/{$moduleName}/Http/Controllers/Api/{$moduleName}Controller.php");
        $moduleNameLower = strtolower($moduleName);

        $assignToStoreEdit = '';
        $supplierIdStoreEdit = '';
        $customerIdStoreEdit = '';
        $productIdStoreEdit = '';
        $dateStoreEdit = '';

        $assignToTryStoreEdit = '';
        $supplierIdTryStoreEdit = '';
        $customerIdTryStoreEdit = '';
        $productIdTryStoreEdit = '';
        $dateTryStoreEdit = '';

        if (!is_null($moduleType) && is_array($moduleType)) {
            // Check if 'user' type is selected
            if (in_array('user', $moduleType)) {
                $assignToStoreEdit = "
                    'assign_to' => 'nullable|integer',
                ";
                $assignToTryStoreEdit = "
                    \${$moduleNameLower}->assign_to = \$request->assign_to;
                ";
            }

            // Check if 'supplier' type is selected
            if (in_array('supplier', $moduleType)) {
                $supplierIdStoreEdit = "
                    'supplier_id' => 'nullable|integer',
                ";
                $supplierIdTryStoreEdit = "
                    \${$moduleNameLower}->supplier_id = \$request->supplier_id;
                ";
            }

            // Check if 'customer' type is selected
            if (in_array('customer', $moduleType)) {
                $customerIdStoreEdit = "
                    'customer_id' => 'nullable|integer',
                ";
                $customerIdTryStoreEdit = "
                    \${$moduleNameLower}->customer_id = \$request->customer_id;
                ";
            }

            // Check if 'product' type is selected
            if (in_array('product', $moduleType)) {
                $productIdStoreEdit = "
                    'product_id' => 'nullable|integer',
                ";
                $productIdTryStoreEdit = "
                    \${$moduleNameLower}->product_id = \$request->product_id;
                ";
            }

            if (in_array('module_date', $moduleType)) {
                $dateStoreEdit = "
                    'module_date' => 'nullable|date',
                ";
                $dateTryStoreEdit = "
                    \${$moduleNameLower}->module_date = \$request->module_date;
                ";
            }
        }

        $fieldsStoreEdit = [];
        $fieldsTryStoreEdit = [];
        $fileUploadStatements = [];
        $fileUpdateStatements = [];
        $filters = [];
        $update_audit_status = [];
        $types = [];
        $titles = [];
        $columnInfo = [];

        if (!is_null($title) && is_array($title) && !is_null($type) && is_array($type)) {
            foreach ($title as $index => $fieldTitle) {
                $fieldType = $type[$index];
                if (!is_null($fieldType)) {
                    $columnInfo[] = [
                        'name' => $fieldTitle,
                        'type' => $fieldType
                    ];

                    if ($fieldType !== 'file') {
                        $fieldsStoreEdit[] = "
                            '$fieldTitle' => 'nullable',
                        ";
                        $fieldsTryStoreEdit[] = "
                            \${$moduleNameLower}->$fieldTitle = \$request->$fieldTitle;
                        ";
                        if (in_array($fieldType, ['users', 'supplier', 'customer', 'product', 'business_location'])) {
                            $filters[] = "
                                if (!empty(request()->input('{$fieldTitle}'))) {
                                    \${$fieldTitle} = request()->input('{$fieldTitle}');
                                    \${$moduleName}->where('{$fieldTitle}', \${$fieldTitle});
                                }
                            ";
                        }
                        if ($fieldType == 'audit'){
                            $update_audit_status[] = "
                                public function updateAuditStatus(Request \$request, \$id)
                                {
                                    try {
                                        \$user = Auth::user();
                                        \$business_id = \$user->business_id;

                                        \${$moduleNameLower}_get = {$moduleName}::where('business_id', \$business_id)->where('id', \$id)->first();
                                        \${$moduleNameLower} = {$moduleName}::where('business_id', \$business_id)->findOrFail(\$id);

                                        \${$moduleNameLower}->{$fieldTitle} = \$request->input('audit_status');
                                        \${$moduleNameLower}->save();

                                        \$output = [
                                            'success' => 1,
                                            'msg' => trans('lang_v1.updated_success'),
                                        ];
                                    } catch (\Exception \$e) {
                                        \Log::emergency('File:' . \$e->getFile() . ' Line:' . \$e->getLine() . ' Message:' . \$e->getMessage());

                                        \$output = [
                                            'success' => 0,
                                            'msg' => trans('messages.something_went_wrong'),
                                        ];
                                    }

                                    return response()->json(\$output);
                                }
                            ";
                            $filters[] = "
                                if (!empty(request()->input('{$fieldTitle}'))) {
                                    if (request()->has('{$fieldTitle}') && request()->{$fieldTitle} === 'Pending') {
                                        \${$moduleName}->where(function (\$query) {
                                            \$query->where('{$fieldTitle}', 'Pending')
                                                  ->orWhereNull('{$fieldTitle}');
                                        });
                                    } else {
                                        \${$fieldTitle} = request()->input('{$fieldTitle}');
                                        \${$moduleName}->where('{$fieldTitle}', \${$fieldTitle});
                                    }
                                }
                            ";
                        }    
                    } else {
                        $fileUploadStatements[] = "
                            if (\$request->hasFile('{$fieldTitle}')) {
                                \$documentPath = \$this->transactionUtil->uploadFile(\$request, '{$fieldTitle}', '{$moduleName}');
                                \${$moduleNameLower}->$fieldTitle = \$documentPath;
                            }
                        ";
                        $fileUpdateStatements[] = "
                            if (\$request->hasFile('{$fieldTitle}')) {
                                \$oldFile = public_path('uploads/tracking/' . basename(\${$moduleNameLower}->$fieldTitle));
                                if (file_exists(\$oldFile)) {
                                    unlink(\$oldFile);
                                }
                                \$documentPath = \$this->transactionUtil->uploadFile(\$request, '{$fieldTitle}', '{$moduleName}');
                                \${$moduleNameLower}->$fieldTitle = \$documentPath;
                            }
                        ";
                    }
                }
            }
        }

        $fieldsStoreEditString = implode("\n", $fieldsStoreEdit);
        $fieldsTryStoreEditString = implode("\n", $fieldsTryStoreEdit);
        $fileUploadStatements = implode("\n", $fileUploadStatements);
        $fileUpdateStatements = implode("\n", $fileUpdateStatements);
        $filters = implode("\n", $filters);
        $update_audit_status = implode("\n", $update_audit_status);
        $titles = implode("\n", $titles);
        $types = implode("\n", $types);

        // Convert the column info array to a JSON string to use in the generated file
        $columnInfoString = json_encode($columnInfo);

        $content = <<<EOT
        <?php

        namespace Modules\\{$moduleName}\\Http\\Controllers\\Api;

        use Illuminate\\Http\\Request;
        use Illuminate\\Routing\\Controller;
        use Illuminate\\Http\\Response;
        use Illuminate\\Support\\Facades\\DB;
        use App\\User;
        use App\\Contact;
        use App\\Product;
        use Yajra\\DataTables\\Facades\\DataTables;
        use Modules\\{$moduleName}\\Entities\\{$moduleName};
        use Modules\\{$moduleName}\\Entities\\{$moduleName}Category;
        use Modules\\ModuleCreateModule\\Entities\\ModuleCreator;
        use Illuminate\\Support\\Facades\\Auth;
        use App\\Utils\\ModuleUtil;
        use Illuminate\\Support\\Facades\\Schema;
        use App\\Utils\\TransactionUtil;

        class {$moduleName}Controller extends Controller
        {
            protected \$moduleUtil;
            protected \$transactionUtil;
        
            public function __construct(
                ModuleUtil \$moduleUtil,
                TransactionUtil \$transactionUtil
            )
            {
                \$this->moduleUtil = \$moduleUtil;
                \$this->transactionUtil = \$transactionUtil;
            }
            
             public function modulefield()
            {
                \$tableName = '{$moduleNameLower}_main';

                try {
                    // Query the information schema to get column details
                    \$columns = DB::select(DB::raw("SHOW COLUMNS FROM \$tableName"));
            
                    // Prepare the response as an associative array to check for duplicates
                    \$columnInfo = [];
                    foreach (\$columns as \$column) {
                        \$columnInfo[\$column->Field] = [
                            'name' => \$column->Field,
                            'type' => \$column->Type,
                        ];
                    }

                    // Add dynamic columns
                    \$additionalColumns = json_decode('{$columnInfoString}', true);

                    if (is_array(\$additionalColumns)) {
                        foreach (\$additionalColumns as \$additionalColumn) {
                            \$columnName = \$additionalColumn['name'];
                            
                            // Always replace the existing static column with the dynamic column
                            \$columnInfo[\$columnName] = \$additionalColumn;
                        }
                    }

                    // Convert back to an indexed array
                    \$columnInfo = array_values(\$columnInfo);

                    return response()->json(\$columnInfo);
                } catch (\Exception \$e) {
                    // Return a JSON response with the error message
                    return response()->json(['error' => \$e->getMessage()], 500);
                }
            }

            public function index(Request \$request)
            {
                \$user = Auth::user();
                \$business_id = \$user->business_id;

                \$module = ModuleCreator::where('module_name', '$moduleNameLower')->first();

                \$is_admin = \$this->moduleUtil->is_admin(auth()->user(), \$business_id);
                
                if ((! auth()->user()->can('module.{$moduleNameLower}'))  && ! auth()->user()->can('superadmin') && ! \$is_admin) {
                    abort(403, 'Unauthorized action.');
                }

               \${$moduleName} = {$moduleName}::where('{$moduleNameLower}_main.business_id', \$business_id)
                ->leftJoin('{$moduleNameLower}_category as {$moduleNameLower}category', '{$moduleNameLower}_main.category_id', '=', '{$moduleNameLower}category.id')
                ->where('{$moduleNameLower}_main.business_id', \$business_id)
                ->select('{$moduleNameLower}_main.*', '{$moduleNameLower}category.name as category_name');

                 if (!empty(request()->start_date) && !empty(request()->end_date)) {
                    \$start = request()->start_date;
                    \$end = request()->end_date;
                    \${$moduleName}->whereDate('created_at', '>=', \$start)
                        ->whereDate('created_at', '<=', \$end);
                }

                {$types}
                {$titles}

                \$result = \${$moduleName}->get();

                return response()->json(\$result);
            }

            public function create(Request \$request)
            {
                \$user = Auth::user();
                \$business_id = \$user->business_id;

                \$module = ModuleCreator::where('module_name', '$moduleNameLower')->first();

                \$is_admin = \$this->moduleUtil->is_admin(auth()->user(), \$business_id);
                
                if ((! auth()->user()->can('module.{$moduleNameLower}'))  && ! auth()->user()->can('superadmin') && ! \$is_admin) {
                    abort(403, 'Unauthorized action.');
                }

                \${$moduleNameLower}_categories = {$moduleName}Category::forDropdown(\$business_id);
                \$users = User::forDropdown(\$business_id);
                \$customers = Contact::where('business_id', \$business_id)
                    ->where('type', 'customer')
                    ->pluck('mobile', 'id');
                \$suppliers = Contact::where('business_id', \$business_id)
                    ->where('type', 'supplier')
                    ->pluck('mobile', 'id');
                \$products = Product::where('business_id', \$business_id)
                    ->pluck('name', 'id');

                return response()->json([
                    'categories' => \${$moduleNameLower}_categories,
                    'users' => \$users,
                    'customers' => \$customers,
                    'suppliers' => \$suppliers,
                    'products' => \$products,
                ]);
            }

            public function store(Request \$request)
            {
                \$request->validate([
                    'title' => 'nullable|string|max:255',
                    'description' => 'nullable|string',
                    'category_id' => 'nullable|integer',                 
                    {$assignToStoreEdit}
                    {$supplierIdStoreEdit}
                    {$customerIdStoreEdit}
                    {$productIdStoreEdit}
                    {$dateStoreEdit}    
                    {$fieldsStoreEditString}                            
                ]);

                \$user = Auth::user();
                \$business_id = \$user->business_id;

                try {
                    \${$moduleNameLower} = new {$moduleName}();
                    \${$moduleNameLower}->title = \$request->title;
                    \${$moduleNameLower}->description = \$request->description;
                    \${$moduleNameLower}->business_id = \$business_id;
                    \${$moduleNameLower}->category_id = \$request->category_id;
                    \${$moduleNameLower}->created_by = auth()->user()->id;
                    {$assignToTryStoreEdit}
                    {$supplierIdTryStoreEdit}
                    {$customerIdTryStoreEdit}
                    {$productIdTryStoreEdit}  
                    {$dateTryStoreEdit} 
                    {$fieldsTryStoreEditString} 
                    {$fileUploadStatements} 
                    \${$moduleNameLower}->save();

                    return response()->json(['success' => true, 'msg' => __('{$moduleNameLower}::lang.saved_successfully')]);
                } catch (\Exception \$e) {
                    return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
                }
            }

            public function edit(\$id)
            {
                \$user = Auth::user();
                \$business_id = \$user->business_id;

                \$module = ModuleCreator::where('module_name', '$moduleNameLower')->first();

                \$is_admin = \$this->moduleUtil->is_admin(auth()->user(), \$business_id);
                
                if ((! auth()->user()->can('module.{$moduleNameLower}'))  && ! auth()->user()->can('superadmin') && ! \$is_admin) {
                    abort(403, 'Unauthorized action.');
                }

                \${$moduleNameLower} = {$moduleName}::find(\$id);
                \${$moduleNameLower} = {$moduleName}Category::forDropdown(\$business_id);
                \$users = User::forDropdown(\$business_id);

                return response()->json([
                    'categories' => \${$moduleNameLower}_categories,
                    'users' => \$users,
                    '{$moduleNameLower}' => \${$moduleNameLower},
                ]);
            }

            public function update(Request \$request, \$id)
            {
                \$request->validate([
                    'title' => 'nullable|string|max:255',
                    'description' => 'nullable|string',
                    'category_id' => 'nullable|integer', 
                    {$assignToStoreEdit}
                    {$supplierIdStoreEdit}
                    {$customerIdStoreEdit}
                    {$productIdStoreEdit}  
                    {$dateStoreEdit}  
                    {$fieldsStoreEditString} 
                ]);

                try {
                    \${$moduleNameLower} = {$moduleName}::find(\$id);
                    \${$moduleNameLower}->title = \$request->title;
                    \${$moduleNameLower}->description = \$request->description;
                    \${$moduleNameLower}->category_id = \$request->category_id;
                    \${$moduleNameLower}->created_by = auth()->user()->id;
                    {$assignToTryStoreEdit}
                    {$supplierIdTryStoreEdit}
                    {$customerIdTryStoreEdit}
                    {$productIdTryStoreEdit}
                    {$dateTryStoreEdit} 
                    {$fieldsTryStoreEditString} 
                    {$fileUpdateStatements}
                    \${$moduleNameLower}->save();

                    return response()->json(['success' => true, 'msg' => __('{$moduleNameLower}::lang.updated_successfully')]);
                } catch (\Exception \$e) {
                    return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
                }
            }

            {$update_audit_status}

            public function destroy(\$id)
            {
                try {
                    {$moduleName}::destroy(\$id);
                    return response()->json(['success' => true, 'msg' => __('{$moduleNameLower}::lang.deleted_successfully')]);
                } catch (\Exception \$e) {
                    return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
                }
            }

            public function getCategories(Request \$request)
            {
                \$user = Auth::user();
                \$business_id = \$user->business_id;

                \$module = ModuleCreator::where('module_name', '$moduleNameLower')->first();

                \$is_admin = \$this->moduleUtil->is_admin(auth()->user(), \$business_id);
                
                if ((! auth()->user()->can('module.{$moduleNameLower}'))  && ! auth()->user()->can('superadmin') && ! \$is_admin) {
                    abort(403, 'Unauthorized action.');
                }
                
                \$categories = {$moduleName}Category::where('business_id', \$business_id)->get();
                
                return response()->json([
                    'categories' => \$categories,
                ]);
            
            }

            public function storeCategory(Request \$request)
            {
                \$user = Auth::user();
                \$business_id = \$user->business_id;

                try {
                    \${$moduleNameLower} = new {$moduleName}Category();
                    \${$moduleNameLower}->name = \$request->name;
                    \${$moduleNameLower}->description = \$request->description;
                    \${$moduleNameLower}->business_id = \$business_id;
                    \${$moduleNameLower}->save();

                    return response()->json(['success' => true, 'msg' => __('{$moduleNameLower}::lang.saved_successfully')]);
                } catch (\Exception \$e) {
                    return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
                }
            }

            public function editCategory(\$id)
            {
                \$user = Auth::user();
                \$business_id = \$user->business_id;

                \$module = ModuleCreator::where('module_name', '$moduleNameLower')->first();

                \$is_admin = \$this->moduleUtil->is_admin(auth()->user(), \$business_id);
                
                if ((! auth()->user()->can('module.{$moduleNameLower}'))  && ! auth()->user()->can('superadmin') && ! \$is_admin) {
                    abort(403, 'Unauthorized action.');
                }
                    
                \$category = {$moduleName}Category::find(\$id);

                return response()->json([
                    'category' => \$category,
                ]);
            }

            public function updateCategory(Request \$request, \$id)
            {
                \$user = Auth::user();
                \$business_id = \$user->business_id;

                try {
                    \$category = {$moduleName}Category::find(\$id);
                    \$category->name = \$request->name;
                    \$category->description = \$request->description;
                    \$category->save();

                    return response()->json(['success' => true, 'msg' => __('{$moduleNameLower}::lang.updated_successfully')]);
                } catch (\Exception \$e) {
                    return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
                }
            }

            public function destroyCategory(\$id)
            {
                try {
                    {$moduleName}Category::destroy(\$id);
                    return response()->json(['success' => true, 'msg' => __('{$moduleNameLower}::lang.deleted_successfully')]);
                } catch (\Exception \$e) {
                    return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
                }
            }
        }
        EOT;

        $this->files->put($controllerPath, $content);
    }
}
