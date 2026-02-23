<?php

namespace Modules\AutoAudit\Http\Controllers;
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
        $is_module_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'autoaudit_module');

        $commonUtil = new Util();
        $is_admin = $commonUtil->is_admin(auth()->user(), $business_id);

        $is_menu_visible = config('module.is_menu_visible', 1); // Default is true if not set
        if ($is_module_enabled && $is_menu_visible) {
            // Modify the admin sidebar menu
            Menu::modify(
                'admin-sidebar-menu',
                function ($menu){                              
                    // Dynamically add menu item for the module
                    $menu->url(
                        action([\Modules\AutoAudit\Http\Controllers\AuditController::class, 'index' ]), 
                        __("autoaudit::lang.autoaudit"), 
                        ['icon' => "fa fa-window-restore", 'style' => 'color:#cc2828;', 'aria-hidden' => 'true', 'active' => request()->segment(1) == "autoaudit"]
                    )->order(3);
                }
            );
        }

        $is_submenu_visible = config('module.is_menu_visible', 0); // Default is true if not set
        if ($is_module_enabled && $is_submenu_visible) {
                        $menu = Menu::instance('admin-sidebar-menu');
                        $menu->whereTitle(__('autoaudit::lang.autoaudit'), function ($sub) {
                        $sub->url(
                            action([\Modules\AutoAudit\Http\Controllers\AutoAuditController::class, 'index' ]),
                            __("autoaudit::lang.autoaudit"), 
                            ['active' => request()->segment(1) == "autoaudit"]
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
                'value' => 'autoaudit.view_autoaudit',
                'label' => __('autoaudit::lang.view_AutoAudit'),
                'default' => false,
            ],
            [
                'value' => 'autoaudit.create_autoaudit',
                'label' => __('autoaudit::lang.create_AutoAudit'),
                'default' => false,
            ],
            [
                'value' => 'autoaudit.edit_autoaudit',
                'label' => __('autoaudit::lang.edit_autoaudit'),
                'default' => false,
            ],
            [
                'value' => 'autoaudit.delete_autoaudit',
                'label' => __('autoaudit::lang.delete_autoaudit'),
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
                'name' => 'autoaudit_module',
                'label' => __('autoaudit::lang.autoaudit_module'),
                'default' => false,
            ],
        ];
    }
}