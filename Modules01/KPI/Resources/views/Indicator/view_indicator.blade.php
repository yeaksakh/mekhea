@extends('layouts.app')
@section('title', __('kpi::lang.view_indicator'))
@section('content')

@include('kpi::layouts.nav')

<!-- Content Header -->
<section class="content-header">
    <h1>@lang('kpi::lang.view_indicator')</h1>
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
                <h3 class="card-header bg-light text-center" style="border-bottom: 2px solid #007bff;">@lang('kpi::lang.indicator')</h3>
                <div class="card-body">
                    <div class="form-group row justify-content-center">
                        <label for="title" class="col-sm-6 col-form-label text-right">@lang('kpi::lang.title')</label>
                        <div class="col-sm-4">
                            <p>{{ $indicator->title }}</p>
                        </div>
                    </div>
                    <div class="form-group row justify-content-center">
                        <label for="department" class="col-sm-6 col-form-label text-right">@lang('kpi::lang.department')</label>
                        <div class="col-sm-4">
                            <p>{{ $department[$indicator->department_id] ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="form-group row justify-content-center">
                        <label for="designation" class="col-sm-6 col-form-label text-right">@lang('kpi::lang.designation')</label>
                        <div class="col-sm-4">
                            <p>{{ $designation[$indicator->designation_id] ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Technical Competencies Section -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-lg p-4 mb-5 bg-white rounded" style="border: 1px solid #e3e3e3; box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);">
                <h3 class="card-header bg-light text-center" style="border-bottom: 2px solid #007bff;">@lang('kpi::lang.financial')</h3>
                <div class="card-body">
                    <table class="table table-hover" id="technical-competencies-table">
                        <thead>
                            <tr class="bg-light">
                                <th style="width: 5%;">#</th>
                                <th style="width: 40%;">@lang('kpi::lang.indicator')</th>
                                <th style="width: 25%;">@lang('kpi::lang.expect_value_money')</th>
                                <th style="width: 15%;">@lang('kpi::lang.expect_score')</th>
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

        <!-- Behavioural Competencies Section -->
        <div class="col-md-6">
            <div class="card shadow-lg p-4 mb-5 bg-white rounded" style="border: 1px solid #e3e3e3; box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);">
                <h3 class="card-header bg-light text-center" style="border-bottom: 2px solid #007bff;">@lang('kpi::lang.non_financial')</h3>
                <div class="card-body">
                    <table class="table table-hover" id="behavioural-competencies-table">
                        <thead>
                             <tr class="bg-light">
                                <th style="width: 5%;">#</th>
                                <th style="width: 40%;">@lang('kpi::lang.indicator')</th>
                                <th style="width: 25%;">@lang('kpi::lang.expect_value')</th>
                                <th style="width: 15%;">@lang('kpi::lang.expect_score')</th>
                            </tr>
                        </thead>
                        <tbody id="behavioural-competencies-body">
                            @foreach ($indicator->competencies->where('type', 'behavioral') as $key => $competency)
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