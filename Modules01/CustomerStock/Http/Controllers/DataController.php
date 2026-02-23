<?php

namespace Modules\CustomerStock\Http\Controllers;
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
        $is_module_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'customerstock_module');

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
                        action([\Modules\CustomerStock\Http\Controllers\CustomerStockController::class, 'index' ]), 
                        __("customerstock::lang.customerstock"), 
                        ['icon' => "fa ", 'style' => 'color:#ff0000;', 'aria-hidden' => 'true', 'active' => request()->segment(1) == "customerstock"]
                    )->order(7);
                }
            );
        }

        $is_submenu_visible = config('module.is_menu_visible', 0); // Default is true if not set
        if ($is_module_enabled && $is_submenu_visible) {
                        $menu = Menu::instance('admin-sidebar-menu');
                        $menu->whereTitle(__('customerstock::lang.customerstock'), function ($sub) {
                        $sub->url(
                            action([\Modules\CustomerStock\Http\Controllers\CustomerStockController::class, 'index' ]),
                            __("customerstock::lang.customerstock"), 
                            ['active' => request()->segment(1) == "customerstock"]
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
                'value' => 'customerstock.view_customerstock',
                'label' => __('customerstock::lang.view_CustomerStock'),
                'default' => false,
            ],
            [
                'value' => 'customerstock.create_customerstock',
                'label' => __('customerstock::lang.create_CustomerStock'),
                'default' => false,
            ],
            [
                'value' => 'customerstock.edit_customerstock',
                'label' => __('customerstock::lang.edit_customerstock'),
                'default' => false,
            ],
            [
                'value' => 'customerstock.delete_customerstock',
                'label' => __('customerstock::lang.delete_customerstock'),
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
                'name' => 'customerstock_module',
                'label' => __('customerstock::lang.customerstock_module'),
                'default' => false,
            ],
        ];
    }
}