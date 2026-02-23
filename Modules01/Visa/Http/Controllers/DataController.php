<?php

namespace Modules\Visa\Http\Controllers;

use App\Utils\ModuleUtil;
use App\Utils\Util;
use Illuminate\Routing\Controller;
use Menu;

class DataController extends Controller
{
    /**
     * Adds Visa menu to the sidebar.
     *
     * @return void
     */
    // public function modifyAdminMenu()
    // {
    //     Menu::modify('admin-sidebar-menu', function ($menu) {
    //         $menu->url(
    //             action([\Modules\Visa\Http\Controllers\IndicatorController::class, 'index']),
    //             __('Visa'),
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

        $is_visa_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'visa_module');

        if ($is_visa_enabled) {
            Menu::modify('admin-sidebar-menu', function ($menu) {
                $menu->whereTitle(__('home.employee'), function ($sub) {
                    if ($sub) {
                        $sub->url(
                            action([\Modules\Visa\Http\Controllers\IndicatorController::class, 'index']) . '?visa_view=list_view',
                            __('Visa::lang.Visa'),
                            [
                                'icon' => '',
                                'active' => request()->segment(1) == 'Visa' || request()->get('type') == 'Visa',
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
                'value' => 'Visa.create_visa',
                'label' => __('Visa::lang.create_visa'),
                'default' => false,
            ],
            [
                'value' => 'Visa.edit_visa',
                'label' => __('Visa::lang.edit_visa'),
                'default' => false,
            ],
            [
                'value' => 'Visa.delete_visa',
                'label' => __('Visa::lang.delete_visa'),
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
                'name' => 'visa_module',
                'label' => __('Visa::lang.visa_module'),
                'default' => false,
            ],
        ];
    }
}
