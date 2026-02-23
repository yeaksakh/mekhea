<?php

namespace Modules\ModuleCreateModule\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Filesystem\Filesystem;

class CreateMenuController extends Controller
{
    protected $files;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function createMenu($moduleName, $icon)
    {
        // Your function implementation here
        // Example content creation code
        $viewPath = base_path("Modules/{$moduleName}/Resources/views/layouts/partials/menu.blade.php");
        $moduleNameLower = strtolower($moduleName);

        if (!$this->files->exists($viewPath)) {
            $content = <<<EOT
            @if(auth()->user()->can('manage_modules') && session()->has('business'))
            <div class="recommended-item" data-id="{$moduleNameLower}">
                <a href="{{ action([\\Modules\\{$moduleName}\\Http\\Controllers\\{$moduleName}Controller::class, 'index']) }}"  title="{{__('{$moduleNameLower}::lang.{$moduleNameLower}')}}"
                class="recommended-link {{ request()->segment(2) == '{$moduleNameLower}' ? 'active' : '' }}">
                    <img src="{{ asset('public/uploads/{$moduleName}/{$icon}') }}"
                        class="recommended-icon" alt="">
                    <div>
                        <p class="text-base font-medium text-gray-800">{{__('{$moduleNameLower}::lang.{$moduleNameLower}')}}</p>
                        <p class="recommended-text text-sm text-gray-600">Manage {{__('{$moduleNameLower}::lang.{$moduleNameLower}')}}</p>
                    </div>
                </a>
            </div>
            @endif
        EOT;

            $this->files->put($viewPath, $content);
        }
    }
}