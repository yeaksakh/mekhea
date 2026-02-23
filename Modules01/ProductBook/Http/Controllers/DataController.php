<?php

namespace Modules\ProductBook\Http\Controllers;
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
        $is_module_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'productbook_module');

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
                        action([\Modules\ProductBook\Http\Controllers\ProductBookController::class, 'threeTabs' ]), 
                        __("productbook::lang.productbook"), 
                        ['icon' => "fa ", 'style' => 'color:#000000;', 'aria-hidden' => 'true', 'active' => request()->segment(1) == "productbook"]
                    )->order(2);
                }
            );
        }

        $is_submenu_visible = config('module.is_menu_visible', 0); // Default is true if not set
        if ($is_module_enabled && $is_submenu_visible) {
                        $menu = Menu::instance('admin-sidebar-menu');
                        $menu->whereTitle(__('productbook::lang.productbook'), function ($sub) {
                        $sub->url(
                            action([\Modules\ProductBook\Http\Controllers\ProductBookController::class, 'threeTabs' ]),
                            __("productbook::lang.productbook"), 
                            ['active' => request()->segment(1) == "productbook"]
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
                'value' => 'productbook.view_productbook',
                'label' => __('productbook::lang.view_ProductBook'),
                'default' => false,
            ],
            [
                'value' => 'productbook.create_productbook',
                'label' => __('productbook::lang.create_ProductBook'),
                'default' => false,
            ],
            [
                'value' => 'productbook.edit_productbook',
                'label' => __('productbook::lang.edit_productbook'),
                'default' => false,
            ],
            [
                'value' => 'productbook.delete_productbook',
                'label' => __('productbook::lang.delete_productbook'),
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
                'name' => 'productbook_module',
                'label' => __('productbook::lang.productbook_module'),
                'default' => false,
            ],
        ];
    }
}