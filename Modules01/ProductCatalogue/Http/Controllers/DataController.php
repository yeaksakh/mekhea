<?php

namespace Modules\ProductCatalogue\Http\Controllers;

use App\Utils\ModuleUtil;
use Illuminate\Routing\Controller;
use Menu;

class DataController extends Controller
{
    /**
     * Defines module as a superadmin package.
     *
     * @return array
     */
    public function superadmin_package()
    {
        return [
            [
                'name' => 'productcatalogue_module',
                'label' => __('productcatalogue::lang.productcatalogue_module'),
                'default' => false,
            ],
        ];
    }

    /**
     * Adds Catalogue QR menus
     *
     * @return null
     */
    public function modifyAdminMenu()
    {
        $business_id = session()->get('user.business_id');
        $module_util = new ModuleUtil();
        $is_productcatalogue_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'productcatalogue_module', 'superadmin_package');

        if ($is_productcatalogue_enabled) {
            Menu::modify('admin-sidebar-menu', function ($menu) {
            // Add HRM Dashboard menu item under the 'Others' menu
            $menu->whereTitle(__('home.ecommerce'), function ($sub) {
                if ($sub) {
                	$sub->url(
	                        action([\Modules\ProductCatalogue\Http\Controllers\ProductCatalogueController::class, 'generateQr']),
	                        __('productcatalogue::lang.catalogue_qr'),
	                        ['icon' => '', 'active' => request()->segment(1) == 'product-catalogue', 'style' => config('app.env') == 'demo' ? 'background-color: #ff851b;' : '']
	                    )->order(20);
                }
             });
        });
    }
}
}