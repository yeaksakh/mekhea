<?php

namespace Modules\ModuleCreateModule\Http\Controllers;

use Illuminate\Routing\Controller;
use Menu;

class DataController extends Controller
{
    /**
     * Adds Test menu under home.apps in the sidebar.
     *
     * @return void
     */
    public function modifyAdminMenu()
    {
        $menu = Menu::instance('admin-sidebar-menu');
        $menu->whereTitle(__('home.apps'), function ($sub) {
            if ($sub) {
                $sub->url(
                    action([\Modules\ModuleCreateModule\Http\Controllers\ModuleCreateModuleController::class, 'index']),
                    __('Mini App'),
                    [
                        'active' => request()->segment(1) == 'ModuleCreator'
                    ]
                )->order(100);
            }
        });
    }
}