<?php

namespace Modules\SOP\Http\Controllers;
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
        $is_module_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'sop_module');

        $commonUtil = new Util();
        $is_admin = $commonUtil->is_admin(auth()->user(), $business_id);

        $is_menu_visible = config('module.is_menu_visible', 1); // Default is true if not set
        if ($is_module_enabled && $is_menu_visible) {
            // Modify the admin sidebar menu
            Menu::modify(
                'admin-sidebar-menu',
                function ($menu){                              
                    // Dynamically add menu item for the module
                    // $menu->url(
                    //     action([\Modules\SOP\Http\Controllers\SOPController::class, 'index' ]), 
                    //     __("sop::lang.sop"), 
                    //     ['icon' => "fa ", 'style' => 'color:#000000;', 'aria-hidden' => 'true', 'active' => request()->segment(1) == "sop"]
                    // )->order(2);
                }
            );
        }

        $is_submenu_visible = config('module.is_menu_visible', 0); // Default is true if not set
        if ($is_module_enabled && $is_submenu_visible) {
                        $menu = Menu::instance('admin-sidebar-menu');
                        $menu->whereTitle(__('sop::lang.sop'), function ($sub) {
                        $sub->url(
                            action([\Modules\SOP\Http\Controllers\SOPController::class, 'index' ]),
                            __("sop::lang.sop"), 
                            ['active' => request()->segment(1) == "sop"]
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
                'value' => 'sop.view_sop',
                'label' => __('sop::lang.view_SOP'),
                'default' => false,
            ],
            [
                'value' => 'sop.create_sop',
                'label' => __('sop::lang.create_SOP'),
                'default' => false,
            ],
            [
                'value' => 'sop.edit_sop',
                'label' => __('sop::lang.edit_sop'),
                'default' => false,
            ],
            [
                'value' => 'sop.delete_sop',
                'label' => __('sop::lang.delete_sop'),
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
                'name' => 'sop_module',
                'label' => __('sop::lang.sop_module'),
                'default' => false,
            ],
        ];
    }
}