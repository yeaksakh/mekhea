<?php

namespace Modules\SchedulePayment\Http\Controllers;
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
        $is_module_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'schedulepayment_module');

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
                        action([\Modules\SchedulePayment\Http\Controllers\SchedulePaymentController::class, 'index' ]), 
                        __("schedulepayment::lang.schedulepayment"), 
                        ['icon' => "fa ", 'style' => 'color:#000000;', 'aria-hidden' => 'true', 'active' => request()->segment(1) == "schedulepayment"]
                    )->order(1);
                }
            );
        }

        $is_submenu_visible = config('module.is_menu_visible', 0); // Default is true if not set
        if ($is_module_enabled && $is_submenu_visible) {
                        $menu = Menu::instance('admin-sidebar-menu');
                        $menu->whereTitle(__('schedulepayment::lang.schedulepayment'), function ($sub) {
                        $sub->url(
                            action([\Modules\SchedulePayment\Http\Controllers\SchedulePaymentController::class, 'index' ]),
                            __("schedulepayment::lang.schedulepayment"), 
                            ['active' => request()->segment(1) == "schedulepayment"]
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
                'value' => 'schedulepayment.view_schedulepayment',
                'label' => __('schedulepayment::lang.view_SchedulePayment'),
                'default' => false,
            ],
            [
                'value' => 'schedulepayment.create_schedulepayment',
                'label' => __('schedulepayment::lang.create_SchedulePayment'),
                'default' => false,
            ],
            [
                'value' => 'schedulepayment.edit_schedulepayment',
                'label' => __('schedulepayment::lang.edit_schedulepayment'),
                'default' => false,
            ],
            [
                'value' => 'schedulepayment.delete_schedulepayment',
                'label' => __('schedulepayment::lang.delete_schedulepayment'),
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
                'name' => 'schedulepayment_module',
                'label' => __('schedulepayment::lang.schedulepayment_module'),
                'default' => false,
            ],
        ];
    }
}