<?php

namespace Modules\PurchaseAutoFill\Http\Controllers;

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
        $is_module_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'purchaseautofill_module');

        $commonUtil = new Util();
        $is_admin = $commonUtil->is_admin(auth()->user(), $business_id);

        $is_menu_visible = config('module.is_menu_visible', 0); // Default is true if not set
        if ($is_module_enabled && $is_menu_visible) {
            // Modify the admin sidebar menu
        }
        Menu::modify(
            'admin-sidebar-menu',
            function ($menu) {
                // Dynamically add menu item for the module
                $menu->url(
                    action([\Modules\PurchaseAutoFill\Http\Controllers\PurchaseAutoFillController::class, 'index']),
                    __("purchaseautofill::lang.purchaseautofill"),
                    ['icon' => "fa ", 'style' => 'color:#e11414;', 'aria-hidden' => 'true', 'active' => request()->segment(1) == "purchaseautofill"]
                )->order(2);
            }
        );

        $is_submenu_visible = config('module.is_menu_visible', 1); // Default is true if not set
        if ($is_module_enabled && $is_submenu_visible) {
            $menu = Menu::instance('admin-sidebar-menu');
            $menu->whereTitle(__('purchaseautofill::lang.purchaseautofill'), function ($sub) {
                $sub->url(
                    action([\Modules\PurchaseAutoFill\Http\Controllers\PurchaseAutoFillController::class, 'index']),
                    __("purchaseautofill::lang.purchaseautofill"),
                    ['active' => request()->segment(1) == "purchaseautofill"]
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
                'value' => 'purchaseautofill.view_purchaseautofill',
                'label' => __('purchaseautofill::lang.view_PurchaseAutoFill'),
                'default' => false,
            ],
            [
                'value' => 'purchaseautofill.create_purchaseautofill',
                'label' => __('purchaseautofill::lang.create_PurchaseAutoFill'),
                'default' => false,
            ],
            [
                'value' => 'purchaseautofill.edit_purchaseautofill',
                'label' => __('purchaseautofill::lang.edit_purchaseautofill'),
                'default' => false,
            ],
            [
                'value' => 'purchaseautofill.delete_purchaseautofill',
                'label' => __('purchaseautofill::lang.delete_purchaseautofill'),
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
                'name' => 'purchaseautofill_module',
                'label' => __('purchaseautofill::lang.purchaseautofill_module'),
                'default' => false,
            ],
        ];
    }
}
