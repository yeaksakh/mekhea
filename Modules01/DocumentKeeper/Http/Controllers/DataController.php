<?php

namespace Modules\DocumentKeeper\Http\Controllers;
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
        $is_module_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'documentkeeper_module');

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
                        action([\Modules\DocumentKeeper\Http\Controllers\DocumentKeeperController::class, 'index' ]), 
                        __("documentkeeper::lang.documentkeeper"), 
                        ['icon' => "fa fa-file-alt", 'style' => 'color:#000000;', 'aria-hidden' => 'true', 'active' => request()->segment(1) == "documentkeeper"]
                    )->order(2);
                }
            );
        }

        $is_submenu_visible = config('module.is_menu_visible', 0); // Default is true if not set
        if ($is_module_enabled && $is_submenu_visible) {
                        $menu = Menu::instance('admin-sidebar-menu');
                        $menu->whereTitle(__('documentkeeper::lang.documentkeeper'), function ($sub) {
                        $sub->url(
                            action([\Modules\DocumentKeeper\Http\Controllers\DocumentKeeperController::class, 'index' ]),
                            __("documentkeeper::lang.documentkeeper"), 
                            ['active' => request()->segment(1) == "documentkeeper"]
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
                'value' => 'documentkeeper.view_documentkeeper',
                'label' => __('documentkeeper::lang.view_DocumentKeeper'),
                'default' => false,
            ],
            [
                'value' => 'documentkeeper.create_documentkeeper',
                'label' => __('documentkeeper::lang.create_DocumentKeeper'),
                'default' => false,
            ],
            [
                'value' => 'documentkeeper.edit_documentkeeper',
                'label' => __('documentkeeper::lang.edit_documentkeeper'),
                'default' => false,
            ],
            [
                'value' => 'documentkeeper.delete_documentkeeper',
                'label' => __('documentkeeper::lang.delete_documentkeeper'),
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
                'name' => 'documentkeeper_module',
                'label' => __('documentkeeper::lang.documentkeeper_module'),
                'default' => false,
            ],
        ];
    }
}