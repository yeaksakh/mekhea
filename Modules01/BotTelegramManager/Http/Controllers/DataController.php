<?php

namespace Modules\BotTelegramManager\Http\Controllers;
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
        $is_module_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'bottelegrammanager_module');

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
                        action([\Modules\BotTelegramManager\Http\Controllers\BotTelegramManagerController::class, 'index' ]), 
                        __("bottelegrammanager::lang.bottelegrammanager"), 
                        ['icon' => "fa ", 'style' => 'color:#9c2626;', 'aria-hidden' => 'true', 'active' => request()->segment(1) == "bottelegrammanager"]
                    )->order();
                }
            );
        }

        $is_submenu_visible = config('module.is_menu_visible', 0); // Default is true if not set
        if ($is_module_enabled && $is_submenu_visible) {
                        $menu = Menu::instance('admin-sidebar-menu');
                        $menu->whereTitle(__('bottelegrammanager::lang.bottelegrammanager'), function ($sub) {
                        $sub->url(
                            action([\Modules\BotTelegramManager\Http\Controllers\BotTelegramManagerController::class, 'index' ]),
                            __("bottelegrammanager::lang.bottelegrammanager"), 
                            ['active' => request()->segment(1) == "bottelegrammanager"]
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
                'value' => 'bottelegrammanager.view_bottelegrammanager',
                'label' => __('bottelegrammanager::lang.view_BotTelegramManager'),
                'default' => false,
            ],
            [
                'value' => 'bottelegrammanager.create_bottelegrammanager',
                'label' => __('bottelegrammanager::lang.create_BotTelegramManager'),
                'default' => false,
            ],
            [
                'value' => 'bottelegrammanager.edit_bottelegrammanager',
                'label' => __('bottelegrammanager::lang.edit_bottelegrammanager'),
                'default' => false,
            ],
            [
                'value' => 'bottelegrammanager.delete_bottelegrammanager',
                'label' => __('bottelegrammanager::lang.delete_bottelegrammanager'),
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
                'name' => 'bottelegrammanager_module',
                'label' => __('bottelegrammanager::lang.bottelegrammanager_module'),
                'default' => false,
            ],
        ];
    }
}