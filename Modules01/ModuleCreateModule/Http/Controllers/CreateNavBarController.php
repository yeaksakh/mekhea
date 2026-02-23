<?php

namespace Modules\ModuleCreateModule\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Filesystem\Filesystem;

class CreateNavBarController extends Controller
{
    protected $files;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function createNavBar($moduleName, $icon, $color_code)
    {
        // Your function implementation here
        // Example content creation code
        $viewPath = base_path("Modules/{$moduleName}/Resources/views/layouts/nav.blade.php");
        $moduleNameLower = strtolower($moduleName);

        if (!$this->files->exists($viewPath)) {
            $content = <<<EOT
        <section class="no-print">
            <nav class="navbar navbar-default bg-white m-4">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="{{action([\\Modules\\{$moduleName}\\Http\\Controllers\\{$moduleName}Controller::class, 'dashboard'])}}">
                            <i class="fa {$icon}"  style="width: 30px; height: auto; color:{$color_code};" aria-hidden="true"></i>
                            @lang("{$moduleNameLower}::lang.dashboard")
                        </a>
                    </div>
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav">
                            <!-- Dashboard link -->
                            <li @if(request()->segment(2) == '{$moduleName}') class="active" @endif>
                                <a href="{{action([\\Modules\\{$moduleName}\\Http\\Controllers\\{$moduleName}Controller::class, 'index'])}}">
                                    @lang("{$moduleNameLower}::lang.{$moduleNameLower}")
                                </a>
                            </li>

                            <!-- Categories link -->
                            <li @if(request()->segment(2) == '{$moduleName}-categories') class="active" @endif>
                                <a href="{{action([\\Modules\\{$moduleName}\\Http\\Controllers\\{$moduleName}Controller::class, 'getCategories'])}}">
                                    @lang("{$moduleNameLower}::lang.{$moduleName}_category")
                                </a>
                            </li>

                            <!-- Permission link -->
                            <li @if(request()->segment(2) == '{$moduleName}-permission') class="active" @endif>
                                <a href="{{action([\\Modules\\{$moduleName}\\Http\\Controllers\\SettingController::class, 'show{$moduleName}PermissionForm'])}}">
                                    @lang("{$moduleNameLower}::lang.setting")
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </section>
        EOT;

            $this->files->put($viewPath, $content);
        }
    }
}
