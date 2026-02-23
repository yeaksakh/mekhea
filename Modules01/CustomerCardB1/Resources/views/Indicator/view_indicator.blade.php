@extends('layouts.app')
@section('title', __('customercardb1::visa.view_indicator'))
@section('content')

@include('customercardb1::layouts.nav_visa')

<!-- Content Header -->
<section class="content-header">
    <h1>@lang('customercardb1::visa.view_indicator')</h1>
    <div class="row justify-content-right">
        <div class="col-md-12 text-right">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fa fa-print"></i> Print
            </button>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="content">
    <!-- Performance Appraisal Section -->
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-lg p-4 mb-5 bg-white rounded" style="border: 1px solid #e3e3e3; box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);">
                <h3 class="card-header bg-light text-center" style="border-bottom: 2px solid #007bff;">@lang('customercardb1::visa.indicator')</h3>
                <div class="card-body">
                    <div class="form-group row justify-content-center">
                        <label for="title" class="col-sm-6 col-form-label text-right">@lang('customercardb1::visa.title')</label>
                        <div class="col-sm-4">
                            <p>{{ $indicator->title }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Technical Competencies Section -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-lg p-4 mb-5 bg-white rounded" style="border: 1px solid #e3e3e3; box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);">
                <h3 class="card-header bg-light text-center" style="border-bottom: 2px solid #007bff;">@lang('customercardb1::visa.financial')</h3>
                <div class="card-body">
                    <table class="table table-hover" id="technical-competencies-table">
                        <thead>
                            <tr class="bg-light">
                                <th style="width: 5%;">#</th>
                                <th style="width: 40%;">@lang('customercardb1::visa.indicator')</th>
                                <th style="width: 25%;">@lang('customercardb1::visa.expect_value_money')</th>
                                <th style="width: 15%;">@lang('customercardb1::visa.expect_score')</th>
                            </tr>
                        </thead>
                        <tbody id="technical-competencies-body">
                            @foreach ($indicator->competencies->where('type', 'technical') as $key => $competency)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $competency->name }}</td>
                                    <td>{{ $competency->value }}</td>
                                    <td>{{ $competency->score }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

@stop