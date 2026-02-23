<?php

namespace Modules\ModuleCreateModule\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Filesystem\Filesystem;

class SettingControllerNewModuleController extends Controller
{
    protected $files;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function SettingController($moduleName, $newPermissionId)
    {
        $controllerPath = base_path("Modules/{$moduleName}/Http/Controllers/SettingController.php");
        $moduleNameLower = strtolower($moduleName);

        $content = <<<EOT
        <?php

        namespace Modules\\{$moduleName}\\Http\\Controllers;

        use Illuminate\\Http\\Request;
        use Illuminate\\Routing\\Controller;
        use Illuminate\\Support\\Facades\\DB;
        use Illuminate\\Support\\Facades\\Log;
        use Modules\\ModuleCreateModule\\Entities\\ModuleCreator;
        use Illuminate\\Support\\Facades\\Auth;
        use App\\Utils\\ModuleUtil;
        use Spatie\\Permission\\Models\\Permission;
        use Spatie\\Permission\\Models\\Role;
        use Modules\\{$moduleName}\\Entities\\{$moduleName}Social;

        class SettingController extends Controller
        {
            protected \$moduleUtil;
            protected \$langFile;

            public function __construct(ModuleUtil \$moduleUtil)
            {
                \$this->moduleUtil = \$moduleUtil;
                \$this->langFile = base_path('Modules/{$moduleName}/Resources/lang/kh/lang.php');
            }

            public function show{$moduleName}PermissionForm()
            {
                \$business_id = request()->session()->get('user.business_id');
                \$module = ModuleCreator::where('module_name', '{$moduleNameLower}')->first();
                \$is_admin = \$this->moduleUtil->is_admin(auth()->user(), \$business_id);

                if (! auth()->user()->can('superadmin') && ! \$is_admin) {
                    abort(403, 'Unauthorized action.');
                }

                \$permissions = Permission::where('id', {$newPermissionId})->first();
                \$rolePermissions = DB::table('role_has_permissions')
                    ->where('permission_id', \$permissions->id)
                    ->pluck('role_id')
                    ->toArray();

                \$roles = Role::where('business_id', \$business_id)
                    ->where('name', '<>', "Admin#".\$business_id)
                    ->select(['name', 'id', 'is_default', 'business_id'])
                    ->get();

                if (!file_exists(\$this->langFile)) {
                    return view('{$moduleNameLower}::{$moduleName}.setting', ['error' => "File not found: {\$this->langFile}"]);
                }

                \$lang = include(\$this->langFile);
                if (!is_array(\$lang)) {
                    return view('{$moduleNameLower}::{$moduleName}.setting', ['error' => "Invalid lang file: {\$this->langFile}"]);
                }
                \${$moduleName} = {$moduleName}Social::where('business_id',\$business_id)->first();

                \$user = auth()->user();
                \$languages = ['en' => 'English', 'kh' => 'Khmer']; // Add more languages as needed

                return view('{$moduleNameLower}::{$moduleName}.setting', [
                    'roles' => \$roles,
                    'rolePermissions' => \$rolePermissions,
                    'translations' => \$lang,
                    'user' => \$user,
                    'languages' => \$languages,
                    '{$moduleName}' => \${$moduleName}
                ]);
            }

            public function assignPermissionToRoles(Request \$request)
            {
                \$request->validate([
                    'roles' => 'required|array',
                ]);

                try {
                    \$permissionId = {$newPermissionId};
                    \$permission = Permission::findOrFail(\$permissionId);
                    \$selectedRoleIds = \$request->roles;
                    \$rolesWithPermission = Role::whereHas('permissions', function (\$query) use (\$permissionId) {
                        \$query->where('id', \$permissionId);
                    })->pluck('id')->toArray();

                    \$rolesToRemovePermission = array_diff(\$rolesWithPermission, \$selectedRoleIds);

                    foreach (\$rolesToRemovePermission as \$roleId) {
                        \$role = Role::findOrFail(\$roleId);
                        \$role->revokePermissionTo(\$permission);
                    }

                    foreach (\$selectedRoleIds as \$roleId) {
                        \$role = Role::findOrFail(\$roleId);
                        if (!\$role->hasPermissionTo(\$permission)) {
                            \$role->givePermissionTo(\$permission);
                        }
                    }

                    \$status = [
                        'success' => true,
                        'msg' => __('user.role_updated'),
                    ];
                } catch (\Exception \$e) {
                    Log::error('Error updating role permissions: ' . \$e->getMessage());

                    \$status = [
                        'success' => false,
                        'msg' => __('messages.something_went_wrong'),
                    ];
                }

                return redirect()->back()->with('status', \$status);
            }

            public function saveTranslations(Request \$request)
            {
                \$translations = \$request->except('_token');

                \$content = "<?php\n\nreturn [\n";
                foreach (\$translations as \$key => \$value) {
                    \$content .= "    '{\$key}' => '" . addslashes(\$value) . "',\n";
                }
                \$content .= "];\n";

                try {
                    file_put_contents(\$this->langFile, \$content);
                    return redirect()->back()->with('success', 'Translations saved successfully.');
                } catch (\Exception \$e) {
                    Log::error('Error saving translations: ' . \$e->getMessage());
                    return back()->withErrors(['error' => 'Failed to save translations.']);
                }
            }

            public function updateLanguage(Request \$request)
            {
                \$request->validate([
                    'your_language' => 'required|string|max:255',
                ]);

                \$user = auth()->user();
                \$user->your_language = \$request->your_language;
                \$user->save();

                return redirect()->back()->with('success', 'Language updated successfully.');
            }

            public function updateSocial(Request \$request)
            {
                \$request->validate([
                    'social_id' => 'required|string|max:255',
                    'social_token' =>'required|string|max:255',
                ]);

                \$business_id = \$request->session()->get('user.business_id');

                // Retrieve or create a
                \$module = {$moduleName}Social::firstOrNew([
                    'business_id' => \$business_id
                ]);

                // Update the social details
                \$module->social_status = \$request->social_status;
                \$module->social_type = \$request->social_type;
                \$module->social_id = \$request->social_id;
                \$module->social_token = \$request->social_token;
                \$module->save();

                return redirect()->back()->with('success', 'Social settings updated successfully.');
            }
        }
        EOT;

        $this->files->put($controllerPath, $content);
    }
}
