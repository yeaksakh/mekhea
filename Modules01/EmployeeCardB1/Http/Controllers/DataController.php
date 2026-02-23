<?php

namespace Modules\EmployeeCardB1\Http\Controllers;
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
        $is_module_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'employeecardb1_module');

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
                        action([\Modules\EmployeeCardB1\Http\Controllers\ManageUserController::class, 'index']), 
                        __("employeecardb1::lang.employeecardb1"), 
                        ['icon' => "fa fa-address-card", 'style' => 'color:#000000;', 'aria-hidden' => 'true', 'active' => request()->segment(1) == "employeecardb1"]
                    )->order(6);
                }
            );
        }

        $is_submenu_visible = config('module.is_menu_visible', 1); // Default is true if not set
        if ($is_module_enabled && $is_submenu_visible) {
                        $menu = Menu::instance('admin-sidebar-menu');
                        $menu->whereTitle(__('home.employee'), function ($sub) {
                        if ($sub) {
                            $sub->url(
                                action([\Modules\EmployeeCardB1\Http\Controllers\ManageUserController::class, 'index']),
                                __("employeecardb1::lang.employeecardb1"), 
                                ['active' => request()->segment(1) == "employeecardb1"]
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
                'value' => 'employeecardb1.view_employeecardb1',
                'label' => __('employeecardb1::lang.view_EmployeeCardB1'),
                'default' => false,
            ],
            [
                'value' => 'employeecardb1.create_employeecardb1',
                'label' => __('employeecardb1::lang.create_EmployeeCardB1'),
                'default' => false,
            ],
            [
                'value' => 'employeecardb1.edit_employeecardb1',
                'label' => __('employeecardb1::lang.edit_employeecardb1'),
                'default' => false,
            ],
            [
                'value' => 'employeecardb1.delete_employeecardb1',
                'label' => __('employeecardb1::lang.delete_employeecardb1'),
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
                'name' => 'employeecardb1_module',
                'label' => __('employeecardb1::lang.employeecardb1_module'),
                'default' => false,
            ],
        ];
    }
}