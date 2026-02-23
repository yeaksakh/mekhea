<?php

namespace Modules\Announcement\Http\Controllers;
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
        $is_module_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'announcement_module');

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
                        action([\Modules\Announcement\Http\Controllers\AnnouncementController::class, 'index' ]), 
                        __("announcement::lang.announcement"), 
                        ['icon' => "fa fa-comment-alt", 'style' => 'color:#000000;', 'aria-hidden' => 'true', 'active' => request()->segment(1) == "announcement"]
                    )->order(3);
                }
            );
        }

        $is_submenu_visible = config('module.is_menu_visible', 0); // Default is true if not set
        if ($is_module_enabled && $is_submenu_visible) {
                        $menu = Menu::instance('admin-sidebar-menu');
                        $menu->whereTitle(__('announcement::lang.announcement'), function ($sub) {
                        $sub->url(
                            action([\Modules\Announcement\Http\Controllers\AnnouncementController::class, 'index' ]),
                            __("announcement::lang.announcement"), 
                            ['active' => request()->segment(1) == "announcement"]
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
                'value' => 'announcement.view_announcement',
                'label' => __('announcement::lang.view_Announcement'),
                'default' => false,
            ],
            [
                'value' => 'announcement.create_announcement',
                'label' => __('announcement::lang.create_Announcement'),
                'default' => false,
            ],
            [
                'value' => 'announcement.edit_announcement',
                'label' => __('announcement::lang.edit_announcement'),
                'default' => false,
            ],
            [
                'value' => 'announcement.delete_announcement',
                'label' => __('announcement::lang.delete_announcement'),
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
                'name' => 'announcement_module',
                'label' => __('announcement::lang.announcement_module'),
                'default' => false,
            ],
        ];
    }
}