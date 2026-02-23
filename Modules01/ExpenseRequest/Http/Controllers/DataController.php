<?php

namespace Modules\ExpenseRequest\Http\Controllers;
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
        $is_module_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'expenserequest_module');

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
                        action([\Modules\ExpenseRequest\Http\Controllers\ExpenseController::class, 'indexExpenseRequest']),
                        __("expenserequest::lang.expenserequest"),
                        ['icon' => "fa fa-dollar-sign", 'style' => 'color:#000000;', 'aria-hidden' => 'true', 'active' => request()->segment(1) == "expenserequest"]
                    )->order(2);
                }
            );
        }

        $is_submenu_visible = config('module.is_menu_visible', 0); // Default is true if not set
        if ($is_module_enabled && $is_submenu_visible) {
            $menu = Menu::instance('admin-sidebar-menu');
            $menu->whereTitle(__('expenserequest::lang.expenserequest'), function ($sub) {
                if ($sub) {
                    $sub->url(
                        action([\Modules\ExpenseRequest\Http\Controllers\ExpenseController::class, 'indexExpenseRequest']),
                        __("expenserequest::lang.expenserequest"),
                        ['active' => request()->segment(1) == "expenserequest"]
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
                'value' => 'expenserequest.view_expenserequest',
                'label' => __('expenserequest::lang.view_ExpenseRequest'),
                'default' => false,
            ],
            [
                'value' => 'expenserequest.create_expenserequest',
                'label' => __('expenserequest::lang.create_ExpenseRequest'),
                'default' => false,
            ],
            [
                'value' => 'expenserequest.edit_expenserequest',
                'label' => __('expenserequest::lang.edit_expenserequest'),
                'default' => false,
            ],
            [
                'value' => 'expenserequest.delete_expenserequest',
                'label' => __('expenserequest::lang.delete_expenserequest'),
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
                'name' => 'expenserequest_module',
                'label' => __('expenserequest::lang.expenserequest_module'),
                'default' => false,
            ],
        ];
    }
}