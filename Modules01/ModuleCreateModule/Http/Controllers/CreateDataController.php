<?php

namespace Modules\ModuleCreateModule\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Filesystem\Filesystem;
use Menu;
use App\Utils\ModuleUtil;
use App\Utils\Util;

class CreateDataController extends Controller
{
    protected $files;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * Dynamically creates the file that contains the modifyAdminMenu function for the given module.
     *
     * @param string $moduleName
     * @return void
     */
    public function createModifyAdminMenuFunction($moduleName, $icon, $color_code, $order, $submenuvisible = 0, $menuvisible = 0, $menu_location = null)
    {
        $controllerPath = base_path("Modules/{$moduleName}/Http/Controllers/DataController.php");
        if (!$this->files->exists($controllerPath)) {
            $moduleNameLower = strtolower($moduleName);
            if (!$menu_location) {
                $menu_location = trans("{$moduleNameLower}::lang.{$moduleNameLower}");
            }
           

            // Dynamically generate the content for the function and the class
            $content = <<<EOT
            <?php

            namespace Modules\\{$moduleName}\\Http\\Controllers;
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
                 * @param string \$moduleName
                 * @return void
                 */
                public function modifyAdminMenu()
                {
                    // Get the business ID from the session
                    \$business_id = session()->get('user.business_id');
                    \$module_util = new ModuleUtil();

                    // Check if the module is enabled in the business subscription
                    \$is_module_enabled = (bool) \$module_util->hasThePermissionInSubscription(\$business_id, '{$moduleNameLower}_module');

                    \$commonUtil = new Util();
                    \$is_admin = \$commonUtil->is_admin(auth()->user(), \$business_id);

                    \$is_menu_visible = config('module.is_menu_visible', {$menuvisible}); // Default is true if not set
                    if (\$is_module_enabled && \$is_menu_visible) {
                        // Modify the admin sidebar menu
                        Menu::modify(
                            'admin-sidebar-menu',
                            function (\$menu){                              
                                // Dynamically add menu item for the module
                                \$menu->url(
                                    action([\\Modules\\{$moduleName}\\Http\\Controllers\\{$moduleName}Controller::class, 'index' ]), 
                                    __("{$moduleNameLower}::lang.{$moduleNameLower}"), 
                                    ['icon' => "fa {$icon}", 'style' => 'color:{$color_code};', 'aria-hidden' => 'true', 'active' => request()->segment(1) == "{$moduleNameLower}"]
                                )->order({$order});
                            }
                        );
                    }

                    \$is_submenu_visible = config('module.is_menu_visible', {$submenuvisible}); // Default is true if not set
                    if (\$is_module_enabled && \$is_submenu_visible) {
                                    \$menu = Menu::instance('admin-sidebar-menu');
                                    \$menu->whereTitle(__('{$menu_location}'), function (\$sub) {
                                    \$sub->url(
                                        action([\\Modules\\{$moduleName}\\Http\\Controllers\\{$moduleName}Controller::class, 'index' ]),
                                        __("{$moduleNameLower}::lang.{$moduleNameLower}"), 
                                        ['active' => request()->segment(1) == "{$moduleNameLower}"]
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
                 * @param string \$moduleName
                 * @return void
                 */
                 public function user_permissions()
                {
                    return [
                        [
                            'value' => '{$moduleNameLower}.view_{$moduleNameLower}',
                            'label' => __('{$moduleNameLower}::lang.view_{$moduleName}'),
                            'default' => false,
                        ],
                        [
                            'value' => '{$moduleNameLower}.create_{$moduleNameLower}',
                            'label' => __('{$moduleNameLower}::lang.create_{$moduleName}'),
                            'default' => false,
                        ],
                        [
                            'value' => '{$moduleNameLower}.edit_{$moduleNameLower}',
                            'label' => __('{$moduleNameLower}::lang.edit_{$moduleNameLower}'),
                            'default' => false,
                        ],
                        [
                            'value' => '{$moduleNameLower}.delete_{$moduleNameLower}',
                            'label' => __('{$moduleNameLower}::lang.delete_{$moduleNameLower}'),
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
                            'name' => '{$moduleNameLower}_module',
                            'label' => __('{$moduleNameLower}::lang.{$moduleNameLower}_module'),
                            'default' => false,
                        ],
                    ];
                }
            }
            EOT;

            // Save the generated content to the file
            $this->files->put($controllerPath, $content);
        }
    }
}
