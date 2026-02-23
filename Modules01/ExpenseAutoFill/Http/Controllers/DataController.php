<?php

namespace Modules\ExpenseAutoFill\Http\Controllers;

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
        $is_module_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'expenseautofill_module');

        $commonUtil = new Util();
        $is_admin = $commonUtil->is_admin(auth()->user(), $business_id);

        $is_menu_visible = config('module.is_menu_visible', 0); // Default is true if not set
        if ($is_module_enabled && $is_menu_visible) {
            // Modify the admin sidebar menu
        }
        Menu::modify(
            'admin-sidebar-menu',
            function ($menu) {
                // Dynamically add menu item for the module
                $menu->url(
                    action([\Modules\ExpenseAutoFill\Http\Controllers\ExpenseAutoFillController::class, 'index']),
                    __("expenseautofill::lang.expenseautofill"),
                    ['icon' => "fa ", 'style' => 'color:#e11414;', 'aria-hidden' => 'true', 'active' => request()->segment(1) == "expenseautofill"]
                )->order(2);
            }
        );

        $is_submenu_visible = config('module.is_menu_visible', 1); // Default is true if not set
        if ($is_module_enabled && $is_submenu_visible) {
            $menu = Menu::instance('admin-sidebar-menu');
            $menu->whereTitle(__('expenseautofill::lang.expenseautofill'), function ($sub) {
                $sub->url(
                    action([\Modules\ExpenseAutoFill\Http\Controllers\ExpenseAutoFillController::class, 'index']),
                    __("expenseautofill::lang.expenseautofill"),
                    ['active' => request()->segment(1) == "expenseautofill"]
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
                'value' => 'expenseautofill.view_expenseautofill',
                'label' => __('expenseautofill::lang.view_ExpenseAutoFill'),
                'default' => false,
            ],
            [
                'value' => 'expenseautofill.create_expenseautofill',
                'label' => __('expenseautofill::lang.create_ExpenseAutoFill'),
                'default' => false,
            ],
            [
                'value' => 'expenseautofill.edit_expenseautofill',
                'label' => __('expenseautofill::lang.edit_expenseautofill'),
                'default' => false,
            ],
            [
                'value' => 'expenseautofill.delete_expenseautofill',
                'label' => __('expenseautofill::lang.delete_expenseautofill'),
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
                'name' => 'expenseautofill_module',
                'label' => __('expenseautofill::lang.expenseautofill_module'),
                'default' => false,
            ],
        ];
    }
}
