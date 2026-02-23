<?php

namespace Modules\AiStudio\Http\Controllers;
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
        $is_module_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'aistudio_module');

        $commonUtil = new Util();
        $is_admin = $commonUtil->is_admin(auth()->user(), $business_id);

        $is_menu_visible = config('module.is_menu_visible', 1); // Default is true if not set
        if ($is_module_enabled && $is_menu_visible) {
            // Modify the admin sidebar menu
            Menu::modify(
                'admin-sidebar-menu',
                function ($menu) {
                    // Dynamically add menu item for the module
                    $menu->url(
                        action([\Modules\AiStudio\Http\Controllers\ChatController::class, 'index']),
                        __("aistudio::lang.aistudio"),
                        ['icon' => "fa fa-ad", 'style' => 'color:#000000;', 'aria-hidden' => 'true', 'active' => request()->segment(1) == "aistudio"]
                    )->order(2);
                }
            );
        }

        $is_submenu_visible = config('module.is_menu_visible', 0); // Default is true if not set
        if ($is_module_enabled && $is_submenu_visible) {
            $menu = Menu::instance('admin-sidebar-menu');
            $menu->whereTitle(__('aistudio::lang.aistudio'), function ($sub) {
                if ($sub) {
                    $sub->url(
                        action([\Modules\AiStudio\Http\Controllers\ChatController::class, 'index']),
                        __("aistudio::lang.aistudio"),
                        ['active' => request()->segment(1) == "aistudio"]
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
                'value' => 'aistudio.view_aistudio',
                'label' => __('aistudio::lang.view_AiStudio'),
                'default' => false,
            ],
            [
                'value' => 'aistudio.create_aistudio',
                'label' => __('aistudio::lang.create_AiStudio'),
                'default' => false,
            ],
            [
                'value' => 'aistudio.edit_aistudio',
                'label' => __('aistudio::lang.edit_aistudio'),
                'default' => false,
            ],
            [
                'value' => 'aistudio.delete_aistudio',
                'label' => __('aistudio::lang.delete_aistudio'),
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
                'name' => 'aistudio_module',
                'label' => __('aistudio::lang.aistudio_module'),
                'default' => false,
            ],
        ];
    }
}