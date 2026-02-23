<?php

namespace Modules\Connector\Http\Controllers;

use App\Utils\ModuleUtil;
use Illuminate\Routing\Controller;
use Menu;

class DataController extends Controller
{
    public function superadmin_package()
    {
        return [
            [
                'name' => 'connector_module',
                'label' => __('connector::lang.connector_module'),
                'default' => false,
            ],
        ];
    }

    /**
     * Adds Connectoe menus
     *
     * @return null
     */
    public function modifyAdminMenu()
    {
        $module_util = new ModuleUtil();

        if (auth()->user()->can('superadmin')) {
            $is_connector_enabled = $module_util->isModuleInstalled('Connector');
        } else {
            $business_id = session()->get('user.business_id');
            $is_connector_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'connector_module', 'superadmin_package');
        }
        if ($is_connector_enabled) {
    Menu::modify('admin-sidebar-menu', function ($menu) {
        $menu->dropdown(
            __('connector::lang.connector'),
            function ($sub) {
                // Superadmin access to the Clients menu
                if (auth()->user()->can('superadmin')) {
                    $sub->url(
                        action([\Modules\Connector\Http\Controllers\ClientController::class, 'index']),
                        '<span 
                            style="display: inline-flex; 
                            justify-content: center; 
                            align-items: center; 
                            width: 15px; 
                            height: 15px; 
                            border: 0.5px solid #FF9800; 
                            color: #FF9800; 
                            border-radius: 50%;">
                            <i class="fa fas fa-network-wired" style="color: #FF9800; font-size: 8px;"></i>
                        </span>
                        &nbsp;&nbsp;' . __('connector::lang.clients'),
                        ['icon' => '', 'active' => request()->segment(1) == 'connector' && request()->segment(2) == 'api']
                    );
                }

                // Documentation link
                $sub->url(
                    url('\docs'),
                    '<span 
                        style="display: inline-flex; 
                        justify-content: center; 
                        align-items: center; 
                        width: 15px; 
                        height: 15px; 
                        border: 0.5px solid #2196F3; 
                        color: #2196F3; 
                        border-radius: 50%;">
                        <i class="fa fas fa-book" style="color: #2196F3; font-size: 8px;"></i>
                    </span>
                    &nbsp;&nbsp;' . __('connector::lang.documentation'),
                    ['icon' => '', 'active' => request()->segment(1) == 'docs']
                );
            },
            ['icon' => 'text-primary fas fa-plug']
        )->order(89);
    });
}

            
    }
}