<?php

namespace Modules\ProductCostingB11\Http\Controllers;
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
    $is_module_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'productcostingb11_module');

    $commonUtil = new Util();
    $is_admin = $commonUtil->is_admin(auth()->user(), $business_id);

    if ($is_module_enabled) {
        $menu = Menu::instance('admin-sidebar-menu');
        $menu->whereTitle(__('home.home'), function ($sub) {
            if ($sub) {
                $sub->url(
                    action([\Modules\ProductCostingB11\Http\Controllers\ProductCostingB11Controller::class, 'index']),
                    __("productcostingb11::lang.productcostingb11"),
                    [
                        'style' => 'line-height: 16px;',
                        'aria-hidden' => 'true',
                        'active' => request()->segment(1) == "productcostingb11"
                    ]
                )->order(70);
            }
        });
    }
}
    /**
     * Creates the menu dynamically for the given module.
     *
     * @param string $moduleName
     * @return void
     */
}