@extends('layouts.app')
@section('title', __('bottelegrammanager::lang.BotTelegramManager'))
@section('content')
    @includeIf('bottelegrammanager::layouts.nav')

    <!-- Main content -->
    <section class="content no-print">
        <div class="row">
            <div class="col-md-4">
                <div class="info-box info-box-new-style">
                    <span class="info-box-icon bg-aqua"><i class="fas fa-boxes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">@lang('bottelegrammanager::lang.total_BotTelegramManager')</span>
                        <span class="info-box-number">{{ $total_bottelegrammanager }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-box info-box-new-style">
                    <span class="info-box-icon bg-aqua"><i class="fas fa-boxes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">@lang('bottelegrammanager::lang.total_BotTelegramManager_category')</span>
                        <span class="info-box-number">{{ $total_bottelegrammanager_category }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="box box-solid">
                    <div class="box-header">
                        <h3 class="box-title">@lang('bottelegrammanager::lang.BotTelegramManager_category')</h3>
                    </div>
                    <div class="box-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>@lang('bottelegrammanager::lang.BotTelegramManager_category')</th>
                                    <th>@lang('bottelegrammanager::lang.total_BotTelegramManager_category')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bottelegrammanager_category as $category)
                                    <tr>
                                        <td>{{ $category->category }}</td>
                                        <td>{{ $category->total }}</td>
                                    </tr>
                                @endforeach
                                @if($bottelegrammanager_category->isEmpty())
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
