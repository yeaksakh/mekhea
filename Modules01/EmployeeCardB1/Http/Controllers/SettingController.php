<?php

namespace Modules\EmployeeCardB1\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Illuminate\Support\Facades\Auth;
use App\Utils\ModuleUtil;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Modules\EmployeeCardB1\Entities\EmployeeCardB1Social;

class SettingController extends Controller
{
    protected $moduleUtil;
    protected $langFile;

    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->langFile = base_path('Modules/EmployeeCardB1/Resources/lang/kh/lang.php');
    }

    public function showEmployeeCardB1PermissionForm()
    {
        $business_id = request()->session()->get('user.business_id');
        $module = ModuleCreator::where('module_name', 'employeecardb1')->first();
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if (! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $permissions = Permission::where('id', 680)->first();
        $rolePermissions = DB::table('role_has_permissions')
            ->where('permission_id', $permissions->id)
            ->pluck('role_id')
            ->toArray();

        $roles = Role::where('business_id', $business_id)
            ->where('name', '<>', "Admin#".$business_id)
            ->select(['name', 'id', 'is_default', 'business_id'])
            ->get();

        if (!file_exists($this->langFile)) {
            return view('employeecardb1::EmployeeCardB1.setting', ['error' => "File not found: {$this->langFile}"]);
        }

        $lang = include($this->langFile);
        if (!is_array($lang)) {
            return view('employeecardb1::EmployeeCardB1.setting', ['error' => "Invalid lang file: {$this->langFile}"]);
        }
        $EmployeeCardB1 = EmployeeCardB1Social::where('business_id',$business_id)->first();

        $user = auth()->user();
        $languages = ['en' => 'English', 'kh' => 'Khmer']; // Add more languages as needed

        return view('employeecardb1::EmployeeCardB1.setting', [
            'roles' => $roles,
            'rolePermissions' => $rolePermissions,
            'translations' => $lang,
            'user' => $user,
            'languages' => $languages,
            'EmployeeCardB1' => $EmployeeCardB1
        ]);
    }

    public function assignPermissionToRoles(Request $request)
    {
        $request->validate([
            'roles' => 'required|array',
        ]);

        try {
            $permissionId = 680;
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
        $module = EmployeeCardB1Social::firstOrNew([
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