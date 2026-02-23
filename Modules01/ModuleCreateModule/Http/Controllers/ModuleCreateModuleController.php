<?php

namespace Modules\ModuleCreateModule\Http\Controllers;

use App\System;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Modules\ModuleCreateModule\Entities\Permissions;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Schema;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Menu;


class ModuleCreateModuleController extends Controller
{
    protected $files;
    protected $createNavBarController;
    protected $createDesktopBarController;
    protected $createMenuController;
    protected $IndexForModuleController;
    protected $CreateForModuleController;
    protected $deletecontrollerModuleController;
    protected $ControllerNewModuleController;
    protected $EditForModuleController;
    protected $ShowForModuleController;
    protected $AuditForModuleController;
    protected $IndexCategoryController;
    protected $CreateCategoryController;
    protected $DashboardForModuleController;
    protected $EditCategoryController;
    protected $moduleUtil;
    protected $SettingControllerNewModuleController;
    protected $SettingForModuleController;
    protected $transactionUtil;
    protected $ControllerAPIFroModuleController;
    protected $RemoveAndAddServiceProviderController;
    protected $EngLangController;
    protected $CreateDataController;
    protected $CreateInstallController;
    protected $QRViewForModuleController;
    public function __construct(
        Filesystem $files,
        CreateNavBarController $createNavBarController,
        CreateDesktopBarController $createDesktopBarController,
        CreateMenuController $createMenuController,
        IndexForModuleController $IndexForModuleController,
        CreateForModuleController $CreateForModuleController,
        deletecontrollerModuleController $deletecontrollerModuleController,
        ControllerNewModuleController $ControllerNewModuleController,
        EditForModuleController $EditForModuleController,
        ShowForModuleController $ShowForModuleController,
        QRViewForModuleController $QRViewForModuleController,
        AuditForModuleController $AuditForModuleController,
        IndexCategoryController $IndexCategoryController,
        CreateCategoryController $CreateCategoryController,
        DashboardForModuleController $DashboardForModuleController,
        EditCategoryController $EditCategoryController,
        ModuleUtil $moduleUtil,
        SettingControllerNewModuleController $SettingControllerNewModuleController,
        SettingForModuleController $SettingForModuleController,
        TransactionUtil $transactionUtil,
        ControllerAPIFroModuleController $ControllerAPIFroModuleController,
        RemoveAndAddServiceProviderController $RemoveAndAddServiceProviderController,
        EngLangController $EngLangController,
        CreateDataController $CreateDataController,
        CreateInstallModuleController $CreateInstallController


    ) {
        $this->files = $files;
        $this->createNavBarController = $createNavBarController;
        $this->createDesktopBarController = $createDesktopBarController;
        $this->createMenuController = $createMenuController;
        $this->IndexForModuleController = $IndexForModuleController;
        $this->CreateForModuleController = $CreateForModuleController;
        $this->deletecontrollerModuleController = $deletecontrollerModuleController;
        $this->ControllerNewModuleController = $ControllerNewModuleController;
        $this->EditForModuleController = $EditForModuleController;
        $this->ShowForModuleController = $ShowForModuleController;
        $this->QRViewForModuleController = $QRViewForModuleController;
        $this->AuditForModuleController = $AuditForModuleController;
        $this->IndexCategoryController = $IndexCategoryController;
        $this->CreateCategoryController = $CreateCategoryController;
        $this->DashboardForModuleController = $DashboardForModuleController;
        $this->EditCategoryController = $EditCategoryController;
        $this->moduleUtil = $moduleUtil;
        $this->SettingControllerNewModuleController = $SettingControllerNewModuleController;
        $this->SettingForModuleController = $SettingForModuleController;
        $this->transactionUtil = $transactionUtil;
        $this->ControllerAPIFroModuleController = $ControllerAPIFroModuleController;
        $this->RemoveAndAddServiceProviderController = $RemoveAndAddServiceProviderController;
        $this->EngLangController = $EngLangController;
        $this->CreateDataController = $CreateDataController;
        $this->CreateInstallController = $CreateInstallController;
    }

    // public function index()
    // {
    //     $business_id = request()->session()->get('user.business_id');

    //     $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

    //     if (! auth()->user()->can('superadmin') && ! $is_admin) {
    //         abort(403, 'Unauthorized action.');
    //     }

    //     if (request()->ajax()) {
    //         $modules = ModuleCreator::select(['id', 'module_name', 'enabled_modules'])
    //         ->where('business_id', $business_id);

    //         return DataTables::of($modules)
    //             ->addColumn('action', function ($row) {
    //                 $capitalize_first_letter = ucfirst($row->module_name);
    //                 $viewRoute = route($capitalize_first_letter . '.index');

    //                 $html = '<a href="' . $viewRoute . '" target="_blank" class="btn btn-xs btn-info"><i class="fa fa-eye"></i> ' . __('messages.view') . '</a>';

    //                 $toggleText = $row->enabled_modules == 1 ? __('modulecreatemodule::lang.disable') : __('modulecreatemodule::lang.enable');
    //                 $toggleClass = $row->enabled_modules == 1 ? 'bg-orange' : 'btn-success';

    //                 $html .= ' <button class="btn btn-xs ' . $toggleClass . ' toggle-module" data-id="' . $row->id . '"><i class="fa fa-check"></i> ' . $toggleText . '</button>';
    //                 $html .= ' <button class="btn btn-xs btn-danger delete-module" data-href="' . route('modulecreatemodule.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';

    //                 return $html;
    //             })
    //             ->rawColumns(['action'])
    //             ->make(true);
    //     }

    //     return view('modulecreatemodule::index');
    //  }

    public function index()
    {
        $business_id = request()->session()->get('user.business_id');

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if (! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {

            $basePath = base_path('Modules');
            $folders = array_filter(glob($basePath . '/*'), 'is_dir');
            $folderNames = array_map('basename', $folders);
            $modules = ModuleCreator::select(['id', 'module_name', 'enabled_modules'])
                ->where('business_id', $business_id)
                ->whereIn('module_name', $folderNames)
                ->orderBy('id', 'desc')
                ->get();

            return DataTables::of($modules)
                ->addColumn('action', function ($row) {
                    $capitalize_first_letter = ucfirst($row->module_name);
                    $viewRoute = route($capitalize_first_letter . '.index');

                    $html = '<a href="' . $viewRoute . '" target="_blank" class="btn btn-xs btn-info"><i class="fa fa-eye"></i> ' . __('messages.view') . '</a>';

                    // Set toggle button text and class based on module status
                    $toggleText = $row->enabled_modules == 1 ? __('modulecreatemodule::lang.disable') : __('modulecreatemodule::lang.enable');
                    $toggleClass = $row->enabled_modules == 1 ? 'bg-orange' : 'btn-success';

                    // Add the Enable/Disable button and install AJAX handler
                    $html .= ' <button class="btn btn-xs ' . $toggleClass . ' toggle-module" data-id="' . $row->id . '" data-name="' . $row->module_name . '" data-action="install"><i class="fa fa-check"></i> ' . $toggleText . '</button>';

                    // Add delete button
                    $html .= ' <button class="btn btn-xs btn-danger delete-module" data-href="' . route('modulecreatemodule.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';

                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('modulecreatemodule::index');
    }


    public function install(Request $request)
    {
        $module_name = $request->name; // Get the module name from the request

        try {
            $appVersion = 1;

            // Check if the module is already installed
            $is_installed = System::getProperty($module_name . '_version');
            if (!empty($is_installed)) {
                return response()->json(['success' => false, 'msg' => 'Module already installed']);
            }

            DB::statement('SET default_storage_engine=INNODB;');

            Artisan::call('migrate', [
                '--path' => "Modules/{$module_name}/Database/Migrations",
                '--force' => true
            ]);

            // Publish module assets or configurations
            Artisan::call('module:publish', ['module' => ucfirst($module_name)]);

            // Update system property to reflect the new version
            System::addProperty($module_name . '_version', $appVersion);

            // Optimize the application clearing cache
            Artisan::call('optimize:clear');

            return response()->json(['success' => 1, 'msg' => ucfirst($module_name) . ' installed successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => $e->getMessage()]);
        }
    }

    public function uninstall(Request $request)
    {
        if (! auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            System::removeProperty($request->name . '_version');

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
            ];
        } catch (\Exception $e) {
            $output = [
                'success' => false,
                'msg' => $e->getMessage(),
            ];
        }

        return redirect()->back()->with(['status' => $output]);
    }



    public function toggleModule(Request $request)
    {
        $module = ModuleCreator::findOrFail($request->id);

        if ($module->enabled_modules == 1) {
            // Call the uninstall method when the module is enabled
            $this->uninstall($request); // Pass the request to the uninstall method
            $module->enabled_modules = 0; // Set to 0 to disable the module
            $msg = __('Module uninstalled successfully');
        } else {
            // If module is disabled, enable it
            $this->install($request);
            $module->enabled_modules = 1;
            $msg = __('Module enabled successfully');
        }

        $module->save();

        return response()->json([
            'success' => true,
            'msg' => $msg,
            'enabled_modules' => $module->enabled_modules
        ]);
    }

    public function create()
    {
        $business_id = request()->session()->get('user.business_id');
        $icon_pack = !empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1';
        $svg_path = public_path('icons/' . $icon_pack . '/');

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if (!auth()->user()->can('superadmin') && !$is_admin) {
            abort(403, 'Unauthorized action.');
        }

        // Get all SVG files from the folder
        $files = File::exists($svg_path) ? File::files($svg_path) : [];
        $svg_files = [];

        foreach ($files as $file) {
            if (strtolower($file->getExtension()) === 'svg') {
                $content = file_get_contents($file->getPathname());
                if ($content !== false) { // Ensure file is readable
                    $svg_files[] = [
                        'filename' => $file->getFilename(),
                        'path' => $file->getPathname(),
                        'content' => $content
                    ];
                }
            }
        }

        return view('modulecreatemodule::create', compact('svg_files'));
    }

    public function store(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'module_name' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9\s_]+$/'
            ],
            'module_type' => 'nullable|array',
            'title' => 'nullable|array',
            'title.*' => [
                'nullable',
                'regex:/^[a-zA-Z0-9\s_]+$/'
            ],
            'type' => 'nullable|array'
        ], [
            'module_name.required' => 'The module name is required.',
            'module_name.string' => 'The module name must be a valid string.',
            'module_name.regex' => 'The module name can only contain letters (A-Z, a-z), numbers (0-9), spaces, and underscores (_).',
            'title.*.regex' => 'Each title can only contain letters (A-Z, a-z), numbers (0-9), spaces, and underscores (_).'
        ]);
        // Get the uploaded SVG file
        $selectedSvg = $request->file('svg_file');
       


        // Check if validation fails
        if ($validator->fails()) {
            // Get all error messages
            $errors = $validator->errors()->all();
            // Return the first error message in a custom format
            return response()->json([
                'success' => false,
                'msg' => implode(', ', $errors) // Combine all error messages into a single string
            ]);
        }

        // Check if validation fails
        if ($validator->fails()) {
            // Get all error messages
            $errors = $validator->errors()->all();
            // Return the first error message in a custom format
            return response()->json([
                'success' => false,
                'msg' => implode(', ', $errors) // Combine all error messages into a single string
            ]);
        }

        $types = $request->input('type', []);

        // Ensure $types is an array, even if it's null
        if (!is_array($types)) {
            $types = [];
        }

        $auditCount = array_count_values($types)['audit'] ?? 0;

        if ($auditCount > 1) {
            return response()->json([
                'success' => false,
                'msg' => __('messages.only_one_audit_allowed')
            ]);
        }

        // $moduleName = Str::studly($request->input('module_name'));
        $moduleName = Str::studly($request->input('module_name'));
        $moduleType = $request->input('module_type');
        $title = $request->input('title');
        $type = $request->input('type');

        $business_id = request()->session()->get('user.business_id');
        if ($selectedSvg) {
            $svg_file_name = $this->moduleUtil->uploadFile($request, 'svg_file', $moduleName, 'image');
        }

        try {
            $module = new ModuleCreator();
            $module->module_name = $moduleName;
            $module->business_id = $business_id;

            $module->icon = $this->transactionUtil->uploadFile($request, 'icon', 'module_creator');

            $module->save();

            $permissions_name = "module." . $moduleName;
            // Create a new permission
            $permissions = new Permission();
            $permissions->name = $permissions_name;
            $permissions->guard_name = "web";
            $permissions->save();

            $newPermissionId = $permissions->id;




            // Execute Artisan command to create module
            Artisan::call("module:make {$moduleName}");

            // Get current user (works on most systems)
            $currentUser = get_current_user();
            $modulePath = base_path("Modules/{$moduleName}");
            exec("chown -R ubuntu:www-data " . escapeshellarg($modulePath));
            exec("chmod -R 777 {$modulePath} 2>&1", $output, $returnCode);

            if ($returnCode !== 0) {
                Log::warning("Failed to change ownership: " . implode("\n", $output));
            }
            $moduleJsonPath = base_path("Modules/{$moduleName}/module.json");

            // Check if the module.json exists
            if ($this->files->exists($moduleJsonPath)) {
                // Get the content of module.json
                $moduleData = json_decode($this->files->get($moduleJsonPath), true);

                // Update the content of module.json
                $moduleData['name'] = $moduleName; // Use the actual module name
                $moduleData['alias'] = strtolower($moduleName); // Alias typically lowercase
                $moduleData['description'] = "{$moduleName}"; // Description with the module name
                $moduleData['active'] = 1;
                $moduleData['order'] = 0;
                $moduleData['providers'] = ["Modules\\{$moduleName}\\Providers\\{$moduleName}ServiceProvider"];
                $moduleData['aliases'] = new \stdClass(); // empty object to represent empty JSON object {}
                $moduleData['files'] = [];
                $moduleData['requires'] = [];

                // Save the updated module.json
                $this->files->put($moduleJsonPath, json_encode($moduleData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            }
            $configPath = base_path("Modules/{$moduleName}/Config/config.php");

            // Ensure the Config directory exists
            if (!$this->files->exists(base_path("Modules/{$moduleName}/Config"))) {
                $this->files->makeDirectory(base_path("Modules/{$moduleName}/Config"));
            }

            // Create the config.php file with the required content
            $configContent = <<<EOT
            <?php
            
            return [
                'name' => '{$moduleName}',
                'module_version' => '1.0',
            ];
            EOT;
            $this->files->put($configPath, $configContent);


            $this->createViewFolder($moduleName);
            $this->createCategoryFolder($moduleName);
            $this->createControllerAPI($moduleName);
            $this->createDocumentFolder($moduleName);
            $this->createDocumentFolderCategory($moduleName);

            // Delete the default controller created by the module generator
            $this->deleteController($moduleName);

            // Add Controller
            $this->ControllerNewModuleController->modifyController($moduleName, $moduleType, $title, $type);
            $this->SettingControllerNewModuleController->SettingController($moduleName, $newPermissionId);
            $this->ControllerAPIFroModuleController->ApiController($moduleName, $moduleType, $title, $types);
            $this->CreateDataController->createModifyAdminMenuFunction($moduleName, $request->fafa_icon, $request->color_code, $request->input_number, $request->submenu_visible, $request->menu_visible, $request->menu_location);
            $this->CreateInstallController->createInstallController($moduleName);


            // Delete and recreate the routes file
            $this->deleteAndRecreateRoutes($moduleName, $title, $type);
            $this->deleteAndRecreateRoutesAPI($moduleName, $title, $type);

            //delete and recreate service provider
            $this->RemoveAndAddServiceProviderController->RemoveAndAddServiceProvider($moduleName);

            // Create database table migration
            $this->createMigration($moduleName, $moduleType, $title, $type);
            $this->createMigrationCategory($moduleName);

            $this->createModel($moduleName, $title, $type);
            $this->createCategoryModel($moduleName);
            $this->createModuleSocialModel($moduleName);

            // Run migration command for this specific module
            Artisan::call('migrate', ['--path' => "Modules/{$moduleName}/Database/Migrations"]);

            // Add view files
            $this->createNavBarController->createNavBar($moduleName, $request->fafa_icon, $request->color_code);
            $this->createDesktopBarController->createDesktopBar($moduleName, $svg_file_name);
            $this->createMenuController->createMenu($moduleName, $svg_file_name);
            $this->IndexForModuleController->createIndex($moduleName, $moduleType, $title, $type);
            $this->CreateForModuleController->createCreate($moduleName, $moduleType, $title, $type);
            $this->EditForModuleController->createEdit($moduleName, $moduleType, $title, $type);
            $this->ShowForModuleController->createShow($moduleName, $title, $type);
            $this->QRViewForModuleController->createShow($moduleName, $title, $type);
            $this->IndexCategoryController->createIndexCategory($moduleName);
            $this->CreateCategoryController->createCreateCategory($moduleName);
            $this->DashboardForModuleController->createDashboard($moduleName);
            $this->EditCategoryController->createEditCategory($moduleName);
            $this->SettingForModuleController->createSetting($moduleName);
            $this->createEngLang($moduleName);
            $this->EngLangController->EngLang($moduleName, $title);

            $this->createMiddleware($moduleName);

            // Check if 'audit' type is present in the array and call createShow
            if (is_array($type) && in_array('audit', $type)) {
                $this->AuditForModuleController->createAudit($moduleName, $title, $type);
            }

            return response()->json([
                'success' => true,
                'msg' => __('messages.module_created_success')
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create module: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => __('messages.module_create_failed') . ': ' . $e->getMessage()
            ]);
        }
    }

    protected function createMiddleware($moduleName)
    {
        $middlewarePath = base_path("Modules/{$moduleName}/Http/Middleware/ModuleLanguageMiddleware.php");
        $moduleNameLower = strtolower($moduleName);

        $newContent = <<<EOT
        <?php

        namespace Modules\\{$moduleName}\\Http\\Middleware;

        use Closure;
        use Illuminate\\Support\\Facades\\App;

        class ModuleLanguageMiddleware
        {
            public function handle(\$request, Closure \$next)
            {
                \$locale = 'en';  // Default locale

                if (\$user = auth()->user()) {
                    \$locale = \$user->your_language ?: \$locale;  // Override with user's preferred language
                }

                App::setLocale(\$locale);

                return \$next(\$request);
            }
        }

        EOT;

        $this->files->put($middlewarePath, $newContent);
        Log::info("Middleware file created: {$middlewarePath}");
    }


    protected function createDocumentFolder($moduleName)
    {
        $viewFolderPath = base_path("public/uploads/{$moduleName}");
        if (!$this->files->exists($viewFolderPath)) {
            $this->files->makeDirectory($viewFolderPath, 0755, true);
        }
    }
    protected function createDocumentFolderCategory($moduleName)
    {
        $viewFolderPath = base_path("public/uploads/{$moduleName}Category");
        if (!$this->files->exists($viewFolderPath)) {
            $this->files->makeDirectory($viewFolderPath, 0755, true);
        }
    }

    protected function createViewFolder($moduleName)
    {
        $viewFolderPath = base_path("Modules/{$moduleName}/Resources/views/{$moduleName}");
        $viewPartialsFolderPath = base_path("Modules/{$moduleName}/Resources/views/layouts/partials");

        if (!$this->files->exists($viewPartialsFolderPath)) {
            $this->files->makeDirectory($viewPartialsFolderPath, 0755, true);
        }

        if (!$this->files->exists($viewFolderPath)) {
            $this->files->makeDirectory($viewFolderPath, 0755, true);
        }
    }

    protected function createCategoryFolder($moduleName)
    {
        $CategoryFolderPath = base_path("Modules/{$moduleName}/Resources/views/Category");

        if (!$this->files->exists($CategoryFolderPath)) {
            $this->files->makeDirectory($CategoryFolderPath, 0755, true);
        }
    }

    protected function createEngLang($moduleName)
    {
        $EngLangFolderPath = base_path("Modules/{$moduleName}/Resources/lang/en");
        $YlLangFolderPath = base_path("Modules/{$moduleName}/Resources/lang/kh");

        // Create the 'en' directory if it doesn't exist
        if (!$this->files->exists($EngLangFolderPath)) {
            $this->files->makeDirectory($EngLangFolderPath, 0755, true);
        }

        // Create the 'kh' directory if it doesn't exist
        if (!$this->files->exists($YlLangFolderPath)) {
            $this->files->makeDirectory($YlLangFolderPath, 0755, true);
        }
    }


    protected function createKhLang($moduleName)
    {
        $KhLangFolderPath = base_path("Modules/{$moduleName}/Resources/lang/kh");

        if (!$this->files->exists($KhLangFolderPath)) {
            $this->files->makeDirectory($KhLangFolderPath, 0755, true);
        }
    }

    protected function deleteController($moduleName)
    {
        $controllerPath = base_path("Modules/{$moduleName}/Http/Controllers/{$moduleName}Controller.php");

        // Log the file path to check if it's correct
        Log::info("Controller path: {$controllerPath}");

        if ($this->files->exists($controllerPath)) {
            $this->files->delete($controllerPath);
            Log::info("Controller deleted: {$controllerPath}");
        } else {
            Log::error("Controller file not found: {$controllerPath}");
            throw new \Exception("Controller file not found: {$controllerPath}");
        }
    }

    protected function deleteAndRecreateRoutes($moduleName, $title, $type)
    {
        $routesPath = base_path("Modules/{$moduleName}/Routes/web.php");
        $moduleNameLower = strtolower($moduleName);
        $controller = $moduleName . 'Controller';

        Log::info("Routes path: {$routesPath}");

        if ($this->files->exists($routesPath)) {
            $this->files->delete($routesPath);
            Log::info("Routes file deleted: {$routesPath}");
        }

        $audit = [];

        if (!is_null($title) && is_array($title) && !is_null($type) && is_array($type)) {
            foreach ($title as $index => $fieldTitle) {
                $fieldType = $type[$index];
                if ($fieldType == 'audit') {
                    $audit[] = "Route::put('/audit/{id}', [$controller::class, 'updateAuditStatus'])->name('{$moduleName}.updateAuditStatus');";
                }
            }
        }
        $audit = implode("\n", $audit);

        $newContent = <<<EOT
        <?php

        use Illuminate\\Support\\Facades\\Route;
        use Modules\\{$moduleName}\\Http\Controllers\\{$moduleName}Controller;
        use Modules\\{$moduleName}\\Http\\Controllers\\SettingController;

        Route::middleware('web', 'SetSessionData', 'auth', '{$moduleName}Language', 'timezone', 'AdminSidebarMenu')->prefix('{$moduleNameLower}')->group(function () {
            Route::get('/install', [Modules\\{$moduleName}\\Http\Controllers\\InstallController::class, 'index']);
            Route::post('/install', [Modules\\{$moduleName}\\Http\\Controllers\\InstallController::class, 'install']);
            Route::get('/install/uninstall', [Modules\\{$moduleName}\\Http\\Controllers\\InstallController::class, 'uninstall']);


            Route::get('/', [$controller::class, 'dashboard'])->name('{$moduleName}.dashboard');
            Route::get('/{$moduleName}', [$controller::class, 'index'])->name('{$moduleName}.index');
            Route::get('/{$moduleName}/{id}', [$controller::class, 'show'])->name('{$moduleName}.show');
            Route::get('/create', [$controller::class, 'create'])->name('{$moduleName}.create');
            Route::post('/create', [$controller::class, 'store'])->name('{$moduleName}.store');
            Route::get('/edit/{id}', [$controller::class, 'edit'])->name('{$moduleName}.edit');
            Route::put('/edit/{id}', [$controller::class, 'update'])->name('{$moduleName}.update');
            Route::delete('/delete/{id}', [$controller::class, 'destroy'])->name('{$moduleName}.destroy');
        
            {$audit}

            Route::get('/{$moduleName}-categories', [$controller::class, 'getCategories'])->name('{$moduleName}.getCategories');
            Route::get('/{$moduleName}-categories/create', [$controller::class, 'createCategory'])->name('{$moduleName}-categories.create');
            Route::post('/{$moduleName}-categories', [$controller::class, 'storeCategory'])->name('{$moduleName}-categories.store');
            Route::get('/{$moduleName}-categories/edit/{id}', [$controller::class, 'editCategory'])->name('{$moduleName}-categories.edit');
            Route::put('/{$moduleName}-categories/{id}', [$controller::class, 'updateCategory'])->name('{$moduleName}-categories.update');
            Route::delete('/{$moduleName}-categories/{id}', [$controller::class, 'destroyCategory'])->name('{$moduleName}-categories.destroy');
        
            Route::get('/{$moduleName}-permission', [SettingController::class, 'show{$moduleName}PermissionForm'])->name('assignPermissionToBusinessRolesForm');
            Route::post('/{$moduleName}-permission', [SettingController::class, 'assignPermissionToRoles'])->name('{$moduleName}.permission');
            Route::post('/{$moduleName}/lang', [SettingController::class, 'saveTranslations'])->name('{$moduleName}.lang');
            Route::post('/{$moduleName}/update-language', [SettingController::class, 'updateLanguage'])->name('{$moduleName}.update-language');
            Route::post('/{$moduleName}/update-social', [SettingController::class, 'updateSocial'])->name('{$moduleName}.update-social');
        });
        
        Route::get('/{$moduleName}/qrcode-show/{id}', [$controller::class, 'showQrcodeUrl'])->name('{$moduleName}.showQrcodeUrl');
        Route::get('/{$moduleName}/qrcode-qrcodeView/{id}', [$controller::class, 'qrcodeView'])->name('{$moduleName}.qrcodeView');
        EOT;

        $this->files->put($routesPath, $newContent);
        Log::info("Routes file created: {$routesPath}");
    }

    protected function deleteAndRecreateRoutesAPI($moduleName, $title, $type)
    {
        $routesPath = base_path("Modules/{$moduleName}/Routes/api.php");
        $moduleNameLower = strtolower($moduleName);
        $controller = $moduleName . 'Controller';

        Log::info("Routes path: {$routesPath}");

        if ($this->files->exists($routesPath)) {
            $this->files->delete($routesPath);
            Log::info("Routes file deleted: {$routesPath}");
        }

        $audit = [];

        if (!is_null($title) && is_array($title) && !is_null($type) && is_array($type)) {
            foreach ($title as $index => $fieldTitle) {
                $fieldType = $type[$index];
                if ($fieldType == 'audit') {
                    $audit[] = "Route::put('/audit/{id}', [$controller::class, 'updateAuditStatus'])->name('{$moduleName}.updateAuditStatus');";
                }
            }
        }
        $audit = implode("\n", $audit);

        $newContent = <<<EOT
        <?php

        use Illuminate\\Support\\Facades\Route;
        use Modules\\{$moduleName}\\Http\\Controllers\\Api\\{$moduleName}Controller;

        Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
            Route::get('/{$moduleName}-field', [{$moduleName}Controller::class, 'modulefield']);
        
            Route::get('/{$moduleName}', [{$moduleName}Controller::class, 'index']);
            Route::get('/{$moduleName}/create', [{$moduleName}Controller::class, 'create']);
            Route::post('/{$moduleName}', [{$moduleName}Controller::class, 'store']);
            Route::get('/{$moduleName}/edit/{id}', [{$moduleName}Controller::class, 'edit']);
            Route::put('/{$moduleName}/edit/{id}', [{$moduleName}Controller::class, 'update']);
            Route::delete('/{$moduleName}/delete/{id}', [{$moduleName}Controller::class, 'destroy']);
            
            Route::get('/{$moduleName}-categories', [{$moduleName}Controller::class, 'getCategories']);
            Route::post('/{$moduleName}-categories', [{$moduleName}Controller::class, 'storeCategory']);
            Route::get('/{$moduleName}-categories/edit/{id}', [{$moduleName}Controller::class, 'editCategory']);
            Route::put('/{$moduleName}-categories/{id}', [{$moduleName}Controller::class, 'updateCategory']);
            Route::delete('/{$moduleName}-categories/{id}', [{$moduleName}Controller::class, 'destroyCategory']);

            {$audit}

        });
        EOT;

        $this->files->put($routesPath, $newContent);
        Log::info("Routes file created: {$routesPath}");
    }

    protected function createMigration($moduleName, $moduleType, $title, $type)
    {
        $timestamp = now()->format('Y_m_d_His');
        $moduleNameLower = strtolower($moduleName);

        // Initialize optional field variables
        $assignToField = '';
        $supplierIdField = '';
        $customerIdField = '';
        $productIdField = '';
        $departmentField = '';
        $designationField = '';
        $dateField = '';
        $fields = [];

        // Process title and type
        if ((!is_null($title) && is_array($title)) && (!is_null($type) && is_array($type))) {
            foreach ($title as $index => $fieldTitle) {
                $fieldType = $type[$index]; // Default to string if type is not defined

                switch ($fieldType) {
                    case 'string':
                        $fields[] = "\$table->string('$fieldTitle')->nullable();";
                        break;
                    case 'float':
                        $fields[] = "\$table->float('$fieldTitle')->nullable();";
                        break;
                    case 'date':
                        $fields[] = "\$table->date('$fieldTitle')->nullable();";
                        break;
                    case 'text':
                        $fields[] = "\$table->longtext('$fieldTitle')->nullable();";
                        break;
                    case 'boolean':
                        $fields[] = "\$table->boolean('$fieldTitle')->default(0)->nullable();";
                        break;
                    case 'qrcode':
                        $fields[] = "\$table->boolean('$fieldTitle')->default(0)->nullable();";
                        break;
                    case 'file':
                        $fields[] = "\$table->string('$fieldTitle')->nullable();";
                        break;
                    case 'users':
                        $fields[] = "\$table->unsignedBigInteger('$fieldTitle')->nullable();";
                        break;
                    case 'departments':
                        $fields[] = "\$table->unsignedBigInteger('$fieldTitle')->nullable();";
                        break;
                    case 'designations':
                        $fields[] = "\$table->unsignedBigInteger('$fieldTitle')->nullable();";
                        break;
                    case 'supplier':
                        $fields[] = "\$table->unsignedBigInteger('$fieldTitle')->nullable();";
                        break;
                    case 'customer':
                        $fields[] = "\$table->unsignedBigInteger('$fieldTitle')->nullable();";
                        break;
                    case 'product':
                        $fields[] = "\$table->unsignedBigInteger('$fieldTitle')->nullable();";
                        break;
                    case 'business_location':
                        $fields[] = "\$table->unsignedBigInteger('$fieldTitle')->nullable();";
                        break;
                    case 'audit':
                        $fields[] = "\$table->text('$fieldTitle')->nullable();";
                        break;
                    case 'asset':
                        $fields[] = "\$table->unsignedBigInteger('$fieldTitle')->nullable();";
                        break;
                    case 'lead':
                        $fields[] = "\$table->unsignedBigInteger('$fieldTitle')->nullable();";
                        break;
                    case 'status_true_false':
                        $fields[] = "\$table->boolean('$fieldTitle')->nullable();";
                        break;
                    case 'status_authorize':
                        $fields[] = "\$table->string('$fieldTitle')->nullable();";
                        break;
                    case 'status_priority':
                        $fields[] = "\$table->string('$fieldTitle')->nullable();";
                        break;
                    case 'status_payment':
                        $fields[] = "\$table->string('$fieldTitle')->nullable();";
                        break;
                    case 'status_delivery':
                        $fields[] = "\$table->string('$fieldTitle')->nullable();";
                        break;
                }
            }
        }

        // Check module types
        if (!is_null($moduleType) && is_array($moduleType)) {
            if (in_array('user', $moduleType)) {
                $assignToField = "\$table->unsignedInteger('assign_to')->nullable();";
            }
            if (in_array('department', $moduleType)) {
                $assignToField = "\$table->unsignedInteger('department_id')->nullable();";
            }
            if (in_array('designation', $moduleType)) {
                $assignToField = "\$table->unsignedInteger('designation_id')->nullable();";
            }
            if (in_array('supplier', $moduleType)) {
                $supplierIdField = "\$table->unsignedInteger('supplier_id')->nullable();";
            }
            if (in_array('customer', $moduleType)) {
                $customerIdField = "\$table->unsignedInteger('customer_id')->nullable();";
            }
            if (in_array('product', $moduleType)) {
                $productIdField = "\$table->unsignedInteger('product_id')->nullable();";
            }
            if (in_array('module_date', $moduleType)) {
                $dateField = "\$table->date('module_date')->nullable();";
            }
        }

        // Generate fields string
        $fieldsString = implode("\n", $fields);

        // Generate migration content
        $migrationContent = <<<EOT
    <?php
    
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;
    
    class Create{$moduleName}Table extends Migration
    {
        public function up()
        {
            if (!Schema::hasTable('{$moduleNameLower}_main')) {
                Schema::create('{$moduleNameLower}_main', function (Blueprint \$table) {
                    \$table->id();
                    \$table->unsignedBigInteger('business_id');
                    \$table->unsignedInteger('created_by')->nullable();
                    {$assignToField}
                    {$supplierIdField}
                    {$departmentField}
                    {$designationField}
                    {$customerIdField}
                    {$productIdField}
                    {$dateField}
                    {$fieldsString}
                    \$table->unsignedInteger('category_id')->nullable();
                    \$table->timestamps();
                });
            }
            if (!Schema::hasTable('{$moduleNameLower}_socials')) {
                Schema::create('{$moduleNameLower}_socials', function (Blueprint \$table) {
                    \$table->id();
                    \$table->unsignedBigInteger('business_id');
                    \$table->string('social_type')->nullable();
                    \$table->string('social_id')->nullable();
                    \$table->string('social_token')->nullable();
                    \$table->boolean('social_status')->default(0)->nullable();
                    \$table->timestamps();
                });
            }
        }
    
        public function down()
        {
            Schema::dropIfExists('{$moduleNameLower}_socials');
        }
    }
    EOT;

        // Save migration file
        $migrationPath = base_path("Modules/{$moduleName}/Database/Migrations/{$timestamp}_create_{$moduleNameLower}_table.php");
        $this->files->put($migrationPath, $migrationContent);
        Log::info("Migration file created: {$migrationPath}");
    }


    protected function createMigrationCategory($moduleName)
    {
        $timestamp = now()->format('Y_m_d_His');
        $moduleNameLower = strtolower($moduleName);

        $migrationContent = <<<EOT
        <?php

        use Illuminate\Database\Migrations\Migration;
        use Illuminate\Database\Schema\Blueprint;
        use Illuminate\Support\Facades\Schema;

        class Create{$moduleName}CategoryTable extends Migration
        {
            public function up()
            {
                Schema::create('{$moduleNameLower}_category', function (Blueprint \$table) {
                    \$table->id();
                    \$table->unsignedBigInteger('business_id');
                    \$table->string('name');
                    \$table->string('image')->nullable();
                    \$table->text('description')->nullable();
                    \$table->timestamps();
                });
            }

            public function down()
            {
                Schema::dropIfExists('{$moduleName}Category');
            }
        }
        EOT;

        $migrationPath = base_path("Modules/{$moduleName}/Database/Migrations/{$timestamp}_create_{$moduleName}_Category_table.php");

        $this->files->put($migrationPath, $migrationContent);
        Log::info("Migration file created: {$migrationPath}");
    }

    protected function createModel($moduleName, $title, $type)
    {
        $modelNamespace = "Modules\\{$moduleName}\\Entities";
        $modelClassName = "{$moduleName}";
        $moduleNameLower = strtolower($moduleName);
        $relationship = [];

        if (!is_null($title) && is_array($title) && !is_null($type) && is_array($type)) {
            // Code inside the foreach loop executes only if $title and $type are valid arrays
            foreach ($title as $index => $fieldTitle) {
                $fieldType = $type[$index];
                $cleanFieldTitle = str_replace('_', '', $fieldTitle);
                if (!is_null($fieldType)) {
                    if ($fieldType == 'users') {
                        $relationship[] = "
                                public function {$cleanFieldTitle}()
                            {
                                return \$this->belongsTo(\App\User::class, '{$fieldTitle}');
                            }
                        ";
                    }
                    if ($fieldType == 'departments') {
                        $relationship[] = "
                                public function {$cleanFieldTitle}()
                            {
                                return \$this->belongsTo(\App\Category::class, '{$fieldTitle}');
                            }
                        ";
                    }
                    if ($fieldType == 'designations') {
                        $relationship[] = "
                                public function {$cleanFieldTitle}()
                            {
                                return \$this->belongsTo(\App\Category::class, '{$fieldTitle}');
                            }
                        ";
                    }
                    if ($fieldType == 'supplier') {
                        $relationship[] = "
                                public function {$cleanFieldTitle}()
                            {
                                return \$this->belongsTo(\App\Contact::class, '{$fieldTitle}');
                            }
                        ";
                    }
                    if ($fieldType == 'customer') {
                        $relationship[] = "
                                public function {$cleanFieldTitle}()
                            {
                                return \$this->belongsTo(\App\Contact::class, '{$fieldTitle}');
                            }
                        ";
                    }
                    if ($fieldType == 'lead') {
                        $relationship[] = "
                                public function {$cleanFieldTitle}()
                            {
                                return \$this->belongsTo(\App\Contact::class, '{$fieldTitle}');
                            }
                        ";
                    }
                    if ($fieldType == 'product') {
                        $relationship[] = "
                                public function {$cleanFieldTitle}()
                            {
                                return \$this->belongsTo(\App\Product::class, '{$fieldTitle}');
                            }
                        ";
                    }
                    if ($fieldType == 'business_location') {
                        $relationship[] = "
                                public function {$cleanFieldTitle}()
                            {
                                return \$this->belongsTo(\App\BusinessLocation::class, '{$fieldTitle}');
                            }
                        ";
                    }
                }
            }
        }
        $relationship[] = "
        public function category()
        {
            return \$this->belongsTo({$moduleName}Category::class, 'category_id');
        }
        ";

        $relationship = implode("\n", $relationship);

        $modelContent = <<<EOT
    <?php

    namespace {$modelNamespace};

    use Illuminate\\Database\\Eloquent\\Model;

    class {$modelClassName} extends Model
    {
        protected \$guarded = ['*']; // Protect all fields

        protected \$table = '{$moduleNameLower}_main'; // Specify the table name

        {$relationship}
    }
    EOT;

        $modelPath = base_path("Modules/{$moduleName}/Entities/{$modelClassName}.php");

        if (!$this->files->exists(dirname($modelPath))) {
            $this->files->makeDirectory(dirname($modelPath), 0755, true, true);
        }

        $this->files->put($modelPath, $modelContent);
        Log::info("Model file created: {$modelPath}");
    }

    protected function createModuleSocialModel($moduleName)
    {
        $modelNamespace = "Modules\\{$moduleName}\\Entities";
        $modelClassName = "{$moduleName}Social";
        $moduleNameLower = strtolower($moduleName);

        $modelContent = <<<EOT
        <?php

        namespace {$modelNamespace};

        use Illuminate\\Database\\Eloquent\\Model;

        class {$modelClassName} extends Model
        {
            protected \$guarded = ['*']; // Protect all fields
            protected \$table = '{$moduleNameLower}_socials';
            public \$fillable = ['business_id','social_type', 'social_id', 'social_token', 'social_status'];
        }
        EOT;

        $modelPath = base_path("Modules/{$moduleName}/Entities/{$modelClassName}.php");

        if (!$this->files->exists(dirname($modelPath))) {
            $this->files->makeDirectory(dirname($modelPath), 0755, true, true);
        }

        $this->files->put($modelPath, $modelContent);
        Log::info("Model file created: {$modelPath}");
    }

    protected function createCategoryModel($moduleName)
    {
        $modelNamespace = "Modules\\{$moduleName}\\Entities";
        $modelClassName = "{$moduleName}Category";
        $moduleNameLower = strtolower($moduleName);

        $modelContent = <<<EOT
    <?php

    namespace {$modelNamespace};

    use Illuminate\\Database\\Eloquent\\Model;

    class {$modelClassName} extends Model
    {
        protected \$guarded = ['*']; // Protect all fields

        protected \$table = '{$moduleNameLower}_category'; // Specify the table name

        public static function forDropdown(\$business_id)
        {
            \$categories = self::where('business_id', \$business_id)
                ->pluck('name', 'id');

            return \$categories->toArray();
        }
        public function {$moduleNameLower}()
        {
            return \$this->hasMany({$moduleName}::class, 'category_id');
        }
    }
    EOT;

        $modelPath = base_path("Modules/{$moduleName}/Entities/{$modelClassName}.php");

        if (!$this->files->exists(dirname($modelPath))) {
            $this->files->makeDirectory(dirname($modelPath), 0755, true, true);
        }

        $this->files->put($modelPath, $modelContent);
        Log::info("Model file created: {$modelPath}");
    }

    protected function createControllerAPI($moduleName)
    {
        $createControllerAPI = base_path("Modules/{$moduleName}/Http/Controllers/Api");

        if (!$this->files->exists($createControllerAPI)) {
            $this->files->makeDirectory($createControllerAPI, 0755, true);
        }
    }

    public function destroy($id)
    {
        try {

            // Find the module by ID
            $module = ModuleCreator::findOrFail($id);
            $moduleName = $module->module_name;

            $permissionId = Permission::where('name', "module." . $moduleName)->pluck('id')->first();

            $rolesWithPermission = Role::whereHas('permissions', function ($query) use ($permissionId) {
                $query->where('id', $permissionId);
            })->pluck('id')->toArray();

            $permission = Permission::findOrFail($permissionId);

            foreach ($rolesWithPermission as $roleId) {
                $role = Role::findOrFail($roleId);
                $role->revokePermissionTo($permission);
            }

            $permission->delete();

            // Delete module from the database
            $module->delete();

            // Delete module files and directories
            $this->deleteModuleFiles($moduleName);

            // Drop module database tables if they exist
            $this->dropModuleDatabaseTables($moduleName);

            $this->deleteModulePublicFolder($moduleName);
            $modulesStatusesPath = base_path('modules_statuses.json');
            if (file_exists($modulesStatusesPath)) {
                $modulesStatuses = json_decode(file_get_contents($modulesStatusesPath), true);
            } else {
                $modulesStatuses = [];
            }
            $module_name = $moduleName;
            if (isset($modulesStatuses[$module_name])) {
                unset($modulesStatuses[$module_name]);
            }
            file_put_contents($modulesStatusesPath, json_encode($modulesStatuses, JSON_PRETTY_PRINT));

            return response()->json(['success' => true, 'msg' => 'Module deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Failed to delete module: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => 'Failed to delete module: ' . $e->getMessage()]);
        }
    }

    protected function deleteModulePublicFolder($moduleName)
    {
        $publicFolderPath = base_path("public/uploads/{$moduleName}");
        if ($this->files->exists($publicFolderPath)) {
            $this->files->deleteDirectory($publicFolderPath);
        }
    }

    protected function deleteModuleFiles($moduleName)
    {
        $name = ucfirst($moduleName);
        // Define paths to delete
        $modulePath = base_path("Modules/{$name}");

        // Delete module folder and files recursively
        if ($this->files->exists($modulePath)) {
            $this->files->deleteDirectory($modulePath);
            Log::info("Module folder deleted: {$modulePath}");
        } else {
            Log::error("Module folder not found: {$modulePath}");
        }
    }

    protected function dropModuleDatabaseTables($moduleName)
    {
        try {
            // Define migration class names
            $migrationClass = "Create{$moduleName}Table";
            $migrationCategoryClass = "Create{$moduleName}CategoryTable";

            // Drop main module table
            Schema::dropIfExists(strtolower($moduleName) . '_main');

            // Drop category table if exists
            if (Schema::hasTable(strtolower($moduleName) . '_category')) {
                Schema::dropIfExists(strtolower($moduleName) . '_category');
            }
            if (Schema::hasTable(strtolower($moduleName) . '_socials')) {
                Schema::dropIfExists(strtolower($moduleName) . '_socials');
            }

            Log::info("Module database tables dropped: {$moduleName}");
        } catch (\Exception $e) {
            Log::error('Failed to drop module database tables: ' . $e->getMessage());
            throw new \Exception('Failed to drop module database tables: ' . $e->getMessage());
        }
    }

    private function copySvgToModuleDirectory($selectedSvg, $moduleName)
    {
        try {
            // Source path - where the original SVG files are stored
            $icon_pack = !empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1';
            $sourcePath = public_path('icons/' . $icon_pack . '/' . $selectedSvg);

            // $sourcePath = public_path("modules/modulecreatemodule/svgs/{$selectedSvg}");

            // Destination directory - create if it doesn't exist
            $destinationDir = base_path("public/uploads/{$moduleName}");

            // Create directory if it doesn't exist
            if (!file_exists($destinationDir)) {
                mkdir($destinationDir, 0755, true);
            }

            // Destination file path
            $destinationPath = $destinationDir . '/' . $selectedSvg;

            // Check if source file exists
            if (!file_exists($sourcePath)) {
                return null;
            }

            // Copy the file
            if (copy($sourcePath, $destinationPath)) {
                return "uploads/{$moduleName}/{$selectedSvg}"; // Return relative path for database storage
            } else {
                return null;
            }
        } catch (\Exception $e) {
            return null;
        }
    }
    private function getSvgUrl($svgPath)
    {
        if ($svgPath) {
            return url($svgPath);
        }
        return null;
    }

    // Optional: Clean up method to remove module directory when module is deleted
    private function removeModuleDirectory($moduleName)
    {
        $moduleDir = base_path("public/uploads/{$moduleName}");

        if (file_exists($moduleDir)) {
            // Remove all files in the directory
            $files = glob($moduleDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            // Remove the directory
            rmdir($moduleDir);
        }
    }

    // // Alternative method using Laravel's Storage facade (recommended)
    // private function copySvgUsingStorage($selectedSvg, $moduleName)
    // {
    //     try {
    //         // Using Laravel's Storage facade for better file handling
    //         $sourcePath = "modules/modulecreatemodule/svgs/{$selectedSvg}";
    //         $destinationPath = "uploads/{$moduleName}/{$selectedSvg}";

    //         // Check if source exists in public disk
    //         if (!\Storage::disk('public')->exists($sourcePath)) {
    //             // Try to read from the actual file system if not in storage
    //             $actualSourcePath = public_path($sourcePath);
    //             if (file_exists($actualSourcePath)) {
    //                 $content = file_get_contents($actualSourcePath);
    //                 \Storage::disk('public')->put($destinationPath, $content);
    //                 return $destinationPath;
    //             } else {
    //                 \Log::error("Source SVG file not found: {$actualSourcePath}");
    //                 return null;
    //             }
    //         }

    //         // Copy using Storage facade
    //         $content = \Storage::disk('public')->get($sourcePath);
    //         \Storage::disk('public')->put($destinationPath, $content);

    //         return $destinationPath;
    //     } catch (\Exception $e) {
    //         \Log::error("Error copying SVG file using Storage: " . $e->getMessage());
    //         return null;
    //     }
    // }
}
