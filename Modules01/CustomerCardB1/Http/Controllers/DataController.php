<?php

namespace Modules\CustomerCardB1\Http\Controllers;
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
        $is_module_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'customercardb1_module');

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
                        action([\Modules\CustomerCardB1\Http\Controllers\ContactController::class, 'index'], ['type' => 'customer']), 
                        __("customercardb1::lang.customercardb1"), 
                        ['icon' => "fa fa-address-card", 'style' => 'color:#000000;', 'aria-hidden' => 'true', 'active' => request()->segment(1) == "customercardb1"]
                    )->order(5);
                }
            );
        }

        $is_submenu_visible = config('module.is_menu_visible', 1); // Default is true if not set
        if ($is_module_enabled && $is_submenu_visible) {
                        $menu = Menu::instance('admin-sidebar-menu');
                        $menu->whereTitle(__('home.customers_and_leads'), function ($sub) {
                        if ($sub) {
                            $sub->url(
                                action([\Modules\CustomerCardB1\Http\Controllers\ContactController::class, 'index'], ['type' => 'customer']),
                                __("customercardb1::lang.customercardb1"), 
                                ['active' => request()->segment(1) == "customercardb1"]
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
                'value' => 'customercardb1.view_customercardb1',
                'label' => __('customercardb1::lang.view_CustomerCardB1'),
                'default' => false,
            ],
            [
                'value' => 'customercardb1.create_customercardb1',
                'label' => __('customercardb1::lang.create_CustomerCardB1'),
                'default' => false,
            ],
            [
                'value' => 'customercardb1.edit_customercardb1',
                'label' => __('customercardb1::lang.edit_customercardb1'),
                'default' => false,
            ],
            [
                'value' => 'customercardb1.delete_customercardb1',
                'label' => __('customercardb1::lang.delete_customercardb1'),
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
                'name' => 'customercardb1_module',
                'label' => __('customercardb1::lang.customercardb1_module'),
                'default' => false,
            ],
        ];
    }
}