<?php

namespace Modules\BusinessPlanCanvasB1\Http\Controllers;

use App\Utils\ModuleUtil;
use App\Utils\Util;
use Illuminate\Routing\Controller;
use Menu;

class DataController extends Controller
{
    /**
     * Adds businessplancanvasb1 menu to the sidebar.
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

    $is_businessplancanvasb1_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'businessplancanvasb1_module');

    if ($is_businessplancanvasb1_enabled) {
        Menu::modify('admin-sidebar-menu', function ($menu) {
            $menu->whereTitle(__('home.business'), function ($sub) {
                if ($sub) {
                	$sub->url(
	                    action([\Modules\BusinessPlanCanvasB1\Http\Controllers\BusinessPlanCanvasB1Controller::class, 'index']) . '?businessplancanvasb1_view=list_view',
	                    __('businessplancanvasb1::lang.businessplancanvasb1'),
	                    [
	                        'icon' => '',
	                        'active' => request()->segment(1) == 'businessplancanvasb1' || request()->get('type') == 'businessplancanvasb1',
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
                'value' => 'businessplancanvasb1.create_businessplancanvasb1',
                'label' => __('businessplancanvasb1::lang.create_businessplancanvasb1'),
                'default' => false,
            ],
            [
                'value' => 'businessplancanvasb1.edit_businessplancanvasb1',
                'label' => __('businessplancanvasb1::lang.edit_businessplancanvasb1'),
                'default' => false,
            ],
            [
                'value' => 'businessplancanvasb1.delete_businessplancanvasb1',
                'label' => __('businessplancanvasb1::lang.delete_businessplancanvasb1'),
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
                'name' => 'businessplancanvasb1_module',
                'label' => __('businessplancanvasb1::lang.businessplancanvasb1_module'),
                'default' => false,
            ],
        ];
    }
    
}