<?php

namespace Modules\EmployeeTracker\Http\Controllers;
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
        $is_module_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'employeetracker_module');

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
                        action([\Modules\EmployeeTracker\Http\Controllers\EmployeeTrackerController::class, 'index' ]), 
                        __("employeetracker::lang.daily_task_tracking"), 
                        ['icon' => "fa ", 'style' => 'color:#000000;', 'aria-hidden' => 'true', 'active' => request()->segment(1) == "employeetracker"]
                    )->order(2);
                }
            );
        }

        $is_submenu_visible = config('module.is_menu_visible', 0); // Default is true if not set
        if ($is_module_enabled && $is_submenu_visible) {
                        $menu = Menu::instance('admin-sidebar-menu');
                        $menu->whereTitle(__('employeetracker::lang.employeetracker'), function ($sub) {
                        $sub->url(
                            action([\Modules\EmployeeTracker\Http\Controllers\EmployeeTrackerController::class, 'index' ]),
                            __("employeetracker::lang.employeetracker"), 
                            ['active' => request()->segment(1) == "employeetracker"]
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
                'value' => 'employeetracker.view_employeetracker',
                'label' => __('employeetracker::lang.view_EmployeeTracker'),
                'default' => false,
            ],
            [
                'value' => 'employeetracker.create_employeetracker',
                'label' => __('employeetracker::lang.create_EmployeeTracker'),
                'default' => false,
            ],
            [
                'value' => 'employeetracker.edit_employeetracker',
                'label' => __('employeetracker::lang.edit_employeetracker'),
                'default' => false,
            ],
            [
                'value' => 'employeetracker.delete_employeetracker',
                'label' => __('employeetracker::lang.delete_employeetracker'),
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
                'name' => 'employeetracker_module',
                'label' => __('employeetracker::lang.employeetracker_module'),
                'default' => false,
            ],
        ];
    }
}