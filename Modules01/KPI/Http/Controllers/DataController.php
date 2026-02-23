<?php

namespace Modules\KPI\Http\Controllers;

use App\Utils\ModuleUtil;
use App\Utils\Util;
use Illuminate\Routing\Controller;
use Menu;

class DataController extends Controller
{
    /**
     * Adds kpi menu to the sidebar.
     *
     * @return void
     */
    // public function modifyAdminMenu()
    // {
    //     Menu::modify('admin-sidebar-menu', function ($menu) {
    //         $menu->url(
    //             action([\Modules\KPI\Http\Controllers\IndicatorController::class, 'index']),
    //             __('KPI'),
    //             ['icon' => 'fas fa-chart-line']
    //         )->order(100);
    //     });
    // }
  public function modifyAdminMenu()
{
    $business_id = session()->get('user.business_id');
    $module_util = new ModuleUtil();
    $commonUtil = new Util();
    $is_admin = $commonUtil->is_admin(auth()->user(), $business_id);

    $is_kpi_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'kpi_module');

    if ($is_kpi_enabled) {
        Menu::modify('admin-sidebar-menu', function ($menu) {
            $menu->whereTitle(__('home.employee'), function ($sub) {
                if ($sub) {
                	$sub->url(
	                    action([\Modules\KPI\Http\Controllers\IndicatorController::class, 'index']) . '?kpi_view=list_view',
	                    __('kpi::lang.kpi'),
	                    [
	                        'icon' => '',
	                        'active' => request()->segment(1) == 'kpi' || request()->get('type') == 'kpi',
	                        'style' => config('app.env') == 'demo' ? 'background-color: #e4186d !important; color: white;' : '',
	                    ]
	                )->order(70);
                }
            });
        });
    }
}


    /**
     * Defines user permissions for the module.
     *
     * @return array
     */
    public function user_permissions()
    {
        return [
            [
                'value' => 'kpi.view_kpi',
                'label' => __('kpi::lang.view_kpi'),
                'default' => false,
            ],
            [
                'value' => 'kpi.create_kpi',
                'label' => __('kpi::lang.create_kpi'),
                'default' => false,
            ],
            [
                'value' => 'kpi.edit_kpi',
                'label' => __('kpi::lang.edit_kpi'),
                'default' => false,
            ],
            [
                'value' => 'kpi.delete_kpi',
                'label' => __('kpi::lang.delete_kpi'),
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
                'name' => 'kpi_module',
                'label' => __('kpi::lang.kpi_module'),
                'default' => false,
            ],
        ];
    }
    
}