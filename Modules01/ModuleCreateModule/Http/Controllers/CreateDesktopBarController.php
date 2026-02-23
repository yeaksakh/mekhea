<?php

namespace Modules\ModuleCreateModule\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Filesystem\Filesystem;

class CreateDesktopBarController extends Controller
{
    protected $files;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function createDesktopBar($moduleName, $icon)
    {
        // Your function implementation here
        // Example content creation code
        $viewPath = base_path("Modules/{$moduleName}/Resources/views/layouts/partials/desktop.blade.php");
        $moduleNameLower = strtolower($moduleName);

        if (!$this->files->exists($viewPath)) {
            $content = <<<EOT
           @if(auth()->user()->can('manage_modules') && session()->has('business'))
            <div class="home-grid-tile" data-key="{$moduleNameLower}">
                <a href="{{ action([\\Modules\\{$moduleName}\\Http\\Controllers\\{$moduleName}Controller::class, 'index']) }}"  title="{{__('{$moduleNameLower}::lang.{$moduleNameLower}')}}">
                    <img src="{{ asset('public/uploads/{$moduleName}/{$icon}') }}" class="home-icon" alt="">
                    <span class="home-label">{{__('{$moduleNameLower}::lang.{$moduleNameLower}')}}</span>
                </a>
            </div>
            @endif
        EOT;

            $this->files->put($viewPath, $content);
        }
    }
}
