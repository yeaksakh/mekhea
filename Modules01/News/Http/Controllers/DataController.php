<?php

namespace Modules\News\Http\Controllers;
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
        $is_module_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'news_module');

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
                        action([\Modules\News\Http\Controllers\NewsController::class, 'index' ]), 
                        __("news::lang.news"), 
                        ['icon' => "fa ", 'style' => 'color:#000000;', 'aria-hidden' => 'true', 'active' => request()->segment(1) == "news"]
                    )->order(1);
                }
            );
        }

        $is_submenu_visible = config('module.is_menu_visible', 0); // Default is true if not set
        if ($is_module_enabled && $is_submenu_visible) {
                        $menu = Menu::instance('admin-sidebar-menu');
                        $menu->whereTitle(__('news::lang.news'), function ($sub) {
                        $sub->url(
                            action([\Modules\News\Http\Controllers\NewsController::class, 'index' ]),
                            __("news::lang.news"), 
                            ['active' => request()->segment(1) == "news"]
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
                'value' => 'news.view_news',
                'label' => __('news::lang.view_News'),
                'default' => false,
            ],
            [
                'value' => 'news.create_news',
                'label' => __('news::lang.create_News'),
                'default' => false,
            ],
            [
                'value' => 'news.edit_news',
                'label' => __('news::lang.edit_news'),
                'default' => false,
            ],
            [
                'value' => 'news.delete_news',
                'label' => __('news::lang.delete_news'),
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
                'name' => 'news_module',
                'label' => __('news::lang.news_module'),
                'default' => false,
            ],
        ];
    }
}