@extends('layouts.app')
@section('title', __('documentary::lang.Documentary'))
@section('content')
    @includeIf('documentary::layouts.nav')

    <!-- Main content -->
    <section class="content no-print">
        <div class="row">
            <div class="col-md-4">
                <div class="info-box info-box-new-style">
                    <span class="info-box-icon bg-aqua"><i class="fas fa-boxes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">@lang('documentary::lang.total_Documentary')</span>
                        <span class="info-box-number">{{ $total_documentary }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-box info-box-new-style">
                    <span class="info-box-icon bg-aqua"><i class="fas fa-boxes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">@lang('documentary::lang.total_Documentary_category')</span>
                        <span class="info-box-number">{{ $total_documentary_category }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="box box-solid">
                    <div class="box-header">
                        <h3 class="box-title">@lang('documentary::lang.Documentary_category')</h3>
                    </div>
                    <div class="box-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>@lang('documentary::lang.Documentary_category')</th>
                                    <th>@lang('documentary::lang.total_Documentary_category')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($documentary_category as $category)
                                    <tr>
                                        <td>{{ $category->category }}</td>
                                        <td>{{ $category->total }}</td>
                                    </tr>
                                @endforeach
                                @if($documentary_category->isEmpty())
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
