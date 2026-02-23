<?php

namespace Modules\AuditExpense\Http\Controllers;
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
        $is_module_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'auditexpense_module');

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
                        action([\Modules\AuditExpense\Http\Controllers\ExpenseController::class, 'index']), 
                        __("auditexpense::lang.auditexpense"), 
                        ['icon' => "fa fa-donate", 'style' => 'color:#000000;', 'aria-hidden' => 'true', 'active' => request()->segment(1) == "auditexpense"]
                    )->order(2);
                }
            );
        }

        $is_submenu_visible = config('module.is_menu_visible', 0); // Default is true if not set
        if ($is_module_enabled && $is_submenu_visible) {
                        $menu = Menu::instance('admin-sidebar-menu');
                        $menu->whereTitle(__('auditexpense::lang.auditexpense'), function ($sub) {
                        $sub->url(
                            action([\Modules\AuditExpense\Http\Controllers\ExpenseController::class, 'index']),
                            __("auditexpense::lang.auditexpense"), 
                            ['active' => request()->segment(1) == "auditexpense"]
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
                'value' => 'auditexpense.view_auditexpense',
                'label' => __('auditexpense::lang.view_AuditExpense'),
                'default' => false,
            ],
            [
                'value' => 'auditexpense.create_auditexpense',
                'label' => __('auditexpense::lang.create_AuditExpense'),
                'default' => false,
            ],
            [
                'value' => 'auditexpense.edit_auditexpense',
                'label' => __('auditexpense::lang.edit_auditexpense'),
                'default' => false,
            ],
            [
                'value' => 'auditexpense.delete_auditexpense',
                'label' => __('auditexpense::lang.delete_auditexpense'),
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
                'name' => 'auditexpense_module',
                'label' => __('auditexpense::lang.auditexpense_module'),
                'default' => false,
            ],
        ];
    }
}