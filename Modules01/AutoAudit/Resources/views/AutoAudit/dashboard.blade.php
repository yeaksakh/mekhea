@extends('layouts.app')
@section('title', __('autoaudit::lang.AutoAudit'))
@section('content')
    @includeIf('autoaudit::layouts.nav')

    <!-- Main content -->
    <section class="content no-print">
        <div class="row">
            <div class="col-md-4">
                <div class="info-box info-box-new-style">
                    <span class="info-box-icon bg-aqua"><i class="fas fa-boxes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">@lang('autoaudit::lang.total_AutoAudit')</span>
                        <span class="info-box-number">{{ $total_autoaudit }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-box info-box-new-style">
                    <span class="info-box-icon bg-aqua"><i class="fas fa-boxes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">@lang('autoaudit::lang.total_AutoAudit_category')</span>
                        <span class="info-box-number">{{ $total_autoaudit_category }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="box box-solid">
                    <div class="box-header">
                        <h3 class="box-title">@lang('autoaudit::lang.AutoAudit_category')</h3>
                    </div>
                    <div class="box-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>@lang('autoaudit::lang.AutoAudit_category')</th>
                                    <th>@lang('autoaudit::lang.total_AutoAudit_category')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($autoaudit_category as $category)
                                    <tr>
                                        <td>{{ $category->category }}</td>
                                        <td>{{ $category->total }}</td>
                                    </tr>
                                @endforeach
                                @if($autoaudit_category->isEmpty())
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
