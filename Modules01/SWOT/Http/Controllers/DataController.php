<?php

namespace Modules\SWOT\Http\Controllers;
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
        $is_module_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'swot_module');

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
                        action([\Modules\SWOT\Http\Controllers\SWOTController::class, 'index' ]), 
                        __("swot::lang.swot"), 
                        ['icon' => "fa ", 'style' => 'color:#000000;', 'aria-hidden' => 'true', 'active' => request()->segment(1) == "swot"]
                    )->order(1);
                }
            );
        }

        $is_submenu_visible = config('module.is_menu_visible', 0); // Default is true if not set
        if ($is_module_enabled && $is_submenu_visible) {
                        $menu = Menu::instance('admin-sidebar-menu');
                        $menu->whereTitle(__('swot::lang.swot'), function ($sub) {
                        $sub->url(
                            action([\Modules\SWOT\Http\Controllers\SWOTController::class, 'index' ]),
                            __("swot::lang.swot"), 
                            ['active' => request()->segment(1) == "swot"]
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
                'value' => 'swot.view_swot',
                'label' => __('swot::lang.view_SWOT'),
                'default' => false,
            ],
            [
                'value' => 'swot.create_swot',
                'label' => __('swot::lang.create_SWOT'),
                'default' => false,
            ],
            [
                'value' => 'swot.edit_swot',
                'label' => __('swot::lang.edit_swot'),
                'default' => false,
            ],
            [
                'value' => 'swot.delete_swot',
                'label' => __('swot::lang.delete_swot'),
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
                'name' => 'swot_module',
                'label' => __('swot::lang.swot_module'),
                'default' => false,
            ],
        ];
    }
}