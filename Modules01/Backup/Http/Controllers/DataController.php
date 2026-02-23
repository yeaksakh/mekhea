<?php

namespace Modules\Backup\Http\Controllers;

use App\Utils\ModuleUtil;
use App\Utils\Util;
use DB;
use Illuminate\Routing\Controller;
use Menu;

class DataController extends Controller
{
    /**
     * Dynamically add menu item for the module to the admin sidebar.
     *
     * @param string $moduleName
     * @return void
     */
    public function modifyAdminMenu()
    {
        // Get the business ID from the session
        $business_id = session()->get('user.business_id');
        $module_util = new ModuleUtil();

        // Check if the module is enabled in the business subscription
        $is_module_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'backup_module');

        $commonUtil = new Util();
        $is_admin = $commonUtil->is_admin(auth()->user(), $business_id);

        $is_menu_visible = config('module.is_menu_visible', 0); // Default is true if not set
        if ($is_module_enabled && $is_menu_visible) {
            // Modify the admin sidebar menu
            Menu::modify(
                'admin-sidebar-menu',
                function ($menu) {
                    // Dynamically add menu item for the module
                    $menu->url(
                        action([\Modules\Backup\Http\Controllers\BackupController::class, 'index']),
                        __("backup::lang.backup"),
                        ['icon' => "fa ", 'style' => 'color:#000000;', 'aria-hidden' => 'true', 'active' => request()->segment(1) == "backup"]
                    )->order(1);
                }
            );
        }

        $is_submenu_visible = config('module.is_menu_visible', 0); // Default is true if not set
        if ($is_module_enabled && $is_submenu_visible) {
            $menu = Menu::instance('admin-sidebar-menu');
            $menu->whereTitle(__('backup::lang.backup'), function ($sub) {
                $sub->url(
                    action([\Modules\Backup\Http\Controllers\BackupController::class, 'index']),
                    __("backup::lang.backup"),
                    ['active' => request()->segment(1) == "backup"]
                );
            });
        }

        return [
            'is_menu_visible' => true, // Set to false to hide the menu
        ];
    }

    /**
     * Creates the menu dynamically for the given module.
     *
     * @param string $moduleName
     * @return void
     */
    public function user_permissions()
    {
        return [
            [
                'value' => 'backup.view_backup',
                'label' => __('backup::lang.view_Backup'),
                'default' => false,
            ],
            [
                'value' => 'backup.create_backup',
                'label' => __('backup::lang.create_Backup'),
                'default' => false,
            ],
            [
                'value' => 'backup.edit_backup',
                'label' => __('backup::lang.edit_backup'),
                'default' => false,
            ],
            [
                'value' => 'backup.backup.delete_backup',
                'label' => __('backup::lang.delete_backup'),
                'default' => false,
            ],
        ];
    }

    /**
     * Superadmin package permissions
     *
     * @return array
     */
    public function superadmin_package()
    {
        return [
            [
                'name' => 'backup_module',
                'label' => __('backup::lang.backup_module'),
                'default' => false,
            ],
        ];
    }
}
