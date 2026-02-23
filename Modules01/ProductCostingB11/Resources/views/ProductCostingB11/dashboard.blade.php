@extends('layouts.app')
@section('title', __('productcostingb11::lang.ProductCostingB11'))
@section('content')
    @includeIf('productcostingb11::layouts.nav')

    <!-- Main content -->
    <section class="content no-print">
        <div class="row">
            <div class="col-md-4">
                <div class="info-box info-box-new-style">
                    <span class="info-box-icon bg-aqua"><i class="fas fa-boxes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">@lang('productcostingb11::lang.total_ProductCostingB11')</span>
                        <span class="info-box-number">{{ $total_productcostingb11 }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-box info-box-new-style">
                    <span class="info-box-icon bg-aqua"><i class="fas fa-boxes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">@lang('productcostingb11::lang.total_ProductCostingB11_category')</span>
                        <span class="info-box-number">{{ $total_productcostingb11_category }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="box box-solid">
                    <div class="box-header">
                        <h3 class="box-title">@lang('productcostingb11::lang.ProductCostingB11_category')</h3>
                    </div>
                    <div class="box-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>@lang('productcostingb11::lang.ProductCostingB11_category')</th>
                                    <th>@lang('productcostingb11::lang.total_ProductCostingB11_category')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productcostingb11_category as $category)
                                    <tr>
                                        <td>{{ $category->category }}</td>
                                        <td>{{ $category->total }}</td>
                                    </tr>
                                @endforeach
                                @if($productcostingb11_category->isEmpty())
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
