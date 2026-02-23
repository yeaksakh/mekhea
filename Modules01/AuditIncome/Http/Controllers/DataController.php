<?php

namespace Modules\AuditIncome\Http\Controllers;
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
        $is_module_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'auditincome_module');

        $commonUtil = new Util();
        $is_admin = $commonUtil->is_admin(auth()->user(), $business_id);

        $is_menu_visible = config('module.is_menu_visible', 1); // Default is true if not set
        if ($is_module_enabled && $is_menu_visible) {
            // Modify the admin sidebar menu
            Menu::modify(
                'admin-sidebar-menu',
                function ($menu) {
                    // Dynamically add menu item for the module
                    $menu->url(
                        action([\Modules\AuditIncome\Http\Controllers\SellController::class, 'index']),
                        __("auditincome::lang.auditincome"),
                        ['icon' => "fa fa-hand-holding-usd", 'style' => 'color:#000000;', 'aria-hidden' => 'true', 'active' => request()->segment(1) == "auditincome"]
                    )->order(3);
                }
            );
        }

        $is_submenu_visible = config('module.is_menu_visible', 0); // Default is true if not set
        if ($is_module_enabled && $is_submenu_visible) {
            $menu = Menu::instance('admin-sidebar-menu');
            $menu->whereTitle(__('auditincome::lang.auditincome'), function ($sub) {
                if ($sub) {
                    $sub->url(
                        action([\Modules\AuditIncome\Http\Controllers\SellController::class, 'index']),
                        __("auditincome::lang.auditincome"),
                        ['active' => request()->segment(1) == "auditincome"]
                    );
                }
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
                'value' => 'auditincome.view_auditincome',
                'label' => __('auditincome::lang.view_AuditIncome'),
                'default' => false,
            ],
            [
                'value' => 'auditincome.create_auditincome',
                'label' => __('auditincome::lang.create_AuditIncome'),
                'default' => false,
            ],
            [
                'value' => 'auditincome.edit_auditincome',
                'label' => __('auditincome::lang.edit_auditincome'),
                'default' => false,
            ],
            [
                'value' => 'auditincome.delete_auditincome',
                'label' => __('auditincome::lang.delete_auditincome'),
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
                'name' => 'auditincome_module',
                'label' => __('auditincome::lang.auditincome_module'),
                'default' => false,
            ],
        ];
    }
}