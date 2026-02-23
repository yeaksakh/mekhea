<?php

namespace Modules\Backup\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Illuminate\Support\Facades\Auth;
use App\Utils\ModuleUtil;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Modules\Backup\Entities\BackupSocial;

class SettingController extends Controller
{
    protected $moduleUtil;
    protected $langFile;

    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->langFile = base_path('Modules/Backup/Resources/lang/kh/lang.php');
    }

    public function showBackupPermissionForm()
    {
        $business_id = request()->session()->get('user.business_id');
        $module = ModuleCreator::where('module_name', 'backup')->first();
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if (! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $permissions = Permission::where('id', 685)->first();
        $rolePermissions = DB::table('role_has_permissions')
            ->where('permission_id', $permissions->id)
            ->pluck('role_id')
            ->toArray();

        $roles = Role::where('business_id', $business_id)
            ->where('name', '<>', "Admin#".$business_id)
            ->select(['name', 'id', 'is_default', 'business_id'])
            ->get();

        if (!file_exists($this->langFile)) {
            return view('backup::Backup.setting', ['error' => "File not found: {$this->langFile}"]);
        }

        $lang = include($this->langFile);
        if (!is_array($lang)) {
            return view('backup::Backup.setting', ['error' => "Invalid lang file: {$this->langFile}"]);
        }
        $Backup = BackupSocial::where('business_id',$business_id)->first();

        $user = auth()->user();
        $languages = ['en' => 'English', 'kh' => 'Khmer']; // Add more languages as needed

        return view('backup::Backup.setting', [
            'roles' => $roles,
            'rolePermissions' => $rolePermissions,
            'translations' => $lang,
            'user' => $user,
            'languages' => $languages,
            'Backup' => $Backup
        ]);
    }

    public function assignPermissionToRoles(Request $request)
    {
        $request->validate([
            'roles' => 'required|array',
        ]);

        try {
            $permissionId = 685;
            $permission = Permission::findOrFail($permissionId);
            $selectedRoleIds = $request->roles;
            $rolesWithPermission = Role::whereHas('permissions', function ($query) use ($permissionId) {
                $query->where('id', $permissionId);
            })->pluck('id')->toArray();

            $rolesToRemovePermission = array_diff($rolesWithPermission, $selectedRoleIds);

            foreach ($rolesToRemovePermission as $roleId) {
                $role = Role::findOrFail($roleId);
                $role->revokePermissionTo($permission);
            }

            foreach ($selectedRoleIds as $roleId) {
                $role = Role::findOrFail($roleId);
                if (!$role->hasPermissionTo($permission)) {
                    $role->givePermissionTo($permission);
                }
            }

            $status = [
                'success' => true,
                'msg' => __('user.role_updated'),
            ];
        } catch (\Exception $e) {
            Log::error('Error updating role permissions: ' . $e->getMessage());

            $status = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return redirect()->back()->with('status', $status);
    }

    public function saveTranslations(Request $request)
    {
        $translations = $request->except('_token');

        $content = "<?php

return [
";
        foreach ($translations as $key => $value) {
            $content .= "    '{$key}' => '" . addslashes($value) . "',
";
        }
        $content .= "];
";

        try {
            file_put_contents($this->langFile, $content);
            return redirect()->back()->with('success', 'Translations saved successfully.');
        } catch (\Exception $e) {
            Log::error('Error saving translations: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to save translations.']);
        }
    }

    public function updateLanguage(Request $request)
    {
        $request->validate([
            'your_language' => 'required|string|max:255',
        ]);

        $user = auth()->user();
        $user->your_language = $request->your_language;
        $user->save();

        return redirect()->back()->with('success', 'Language updated successfully.');
    }

    public function updateSocial(Request $request)
    {
        $request->validate([
            'social_id' => 'required|string|max:255',
            'social_token' =>'required|string|max:255',
        ]);

        $business_id = $request->session()->get('user.business_id');

        // Retrieve or create a
        $module = BackupSocial::firstOrNew([
            'business_id' => $business_id
        ]);

        // Update the social details
        $module->social_status = $request->social_status;
        $module->social_type = $request->social_type;
        $module->social_id = $request->social_id;
        $module->social_token = $request->social_token;
        $module->save();

        return redirect()->back()->with('success', 'Social settings updated successfully.');
    }
}