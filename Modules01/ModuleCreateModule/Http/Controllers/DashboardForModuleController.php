<?php

namespace Modules\ModuleCreateModule\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Filesystem\Filesystem;

class DashboardForModuleController extends Controller
{
    protected $files;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function createDashboard($moduleName)
    {
        // Your function implementation here
        // Example content creation code
        $viewPath = base_path("Modules/{$moduleName}/Resources/views/{$moduleName}/dashboard.blade.php");
        $moduleNameLower = strtolower($moduleName);

        if (!$this->files->exists($viewPath)) {
            $content = <<<EOT
@extends('layouts.app')
@section('title', __('{$moduleNameLower}::lang.{$moduleName}'))
@section('content')
    @includeIf('{$moduleNameLower}::layouts.nav')

    <!-- Main content -->
    <section class="content no-print">
        <div class="row">
            <div class="col-md-4">
                <div class="info-box info-box-new-style">
                    <span class="info-box-icon bg-aqua"><i class="fas fa-boxes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">@lang('{$moduleNameLower}::lang.total_{$moduleName}')</span>
                        <span class="info-box-number">{{ \$total_{$moduleNameLower} }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-box info-box-new-style">
                    <span class="info-box-icon bg-aqua"><i class="fas fa-boxes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">@lang('{$moduleNameLower}::lang.total_{$moduleName}_category')</span>
                        <span class="info-box-number">{{ \$total_{$moduleNameLower}_category }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="box box-solid">
                    <div class="box-header">
                        <h3 class="box-title">@lang('{$moduleNameLower}::lang.{$moduleName}_category')</h3>
                    </div>
                    <div class="box-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>@lang('{$moduleNameLower}::lang.{$moduleName}_category')</th>
                                    <th>@lang('{$moduleNameLower}::lang.total_{$moduleName}_category')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(\${$moduleNameLower}_category as \$category)
                                    <tr>
                                        <td>{{ \$category->category }}</td>
                                        <td>{{ \$category->total }}</td>
                                    </tr>
                                @endforeach
                                @if(\${$moduleNameLower}_category->isEmpty())
                                    <tr>
                                        <td colspan="2" class="text-center">@lang('lang_v1.no_data')</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

EOT;
            $this->files->put($viewPath, $content);
        }
    }
}
