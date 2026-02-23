@extends('layouts.app')
@section('title', __('visa::lang.visa'))
@section('content')
@includeIf('visa::layouts.nav')
<section class="content-header no-print">
    <h1>@lang('visa::lang.visa')</h1>
</section>

<section class="content no-print">
    <div class="row">
        <div class="col-md-3">
            <!-- Year and Month Selector -->
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h4 class="box-title">@lang('Year'):</h4>
                    <!-- Search Year with Icon in the Same Row with Spacing -->
                    <div class="input-group" style="margin-bottom: 10px;">
                        <input type="text" id="selected_year" class="form-control" placeholder="Enter Year" readonly>
                        <span class="input-group-btn" style="margin-left: 5px;">
                            <button id="search_year" class="btn btn-secondary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </span>
                    </div>
                </div>

                <!-- Month Selector -->
                <div class="box-body no-padding">
                    <ul class="nav nav-pills nav-stacked" id="month-selector">
                        @foreach (range(1, 12) as $month)
                        <li><a href="#" data-month="{{ $month }}"><i class="fas fa-calendar-alt"></i> @lang(date('F', mktime(0, 0, 0, $month, 1)))</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('visa::lang.report')])
            <!-- visa Table -->
            <table class="table table-bordered table-striped" id="appraisal_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('Employee')</th>
                        <th>@lang('Department')</th>
                        <th>@lang('Month')</th>
                        <th>@lang('Expect Score')</th>
                        <th>@lang('Actual Score')</th>
                        <th>@lang('Action')</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            @endcomponent
        </div>
    </div>
</section>

<div class="modal fade kpi_report_modal" tabindex="-1" role="dialog" aria-labelledby="createKPIModalLabel" aria-hidden="true"></div>

@stop

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        var today = new Date();
        var selectedMonth = today.getMonth() + 1; // Current month
        var selectedYear = today.getFullYear(); // Current year

        // Initialize Datepicker for year selection
        $('#selected_year').datepicker({
            format: "yyyy", // Display only the year
            viewMode: "years",
            minViewMode: "years",
            autoclose: true
        }).datepicker('setDate', new Date());

        // Highlight the current month in the month selector
        $('#month-selector li a[data-month="' + selectedMonth + '"]').parent('li').addClass('active');

        // Initialize the DataTable
        var table = $('#appraisal_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('visa.appraisal.report') }}",
                data: function(d) {
                    var selectedYearVal = $('#selected_year').datepicker('getDate').getFullYear();
                    d.month = selectedMonth;
                    d.year = selectedYearVal;
                    console.log('Sending Month:', selectedMonth, 'Year:', selectedYearVal); // Debugging line
                }
            },
            columns: [
                {
                    data: null,
                    name: 'id',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'employee',
                    name: 'employee'
                },
                {
                    data: 'department',
                    name: 'department'
                },
                {
                    data: 'appraisal_month',
                    name: 'appraisal_month'
                },
                {
                    data: 'expect_score',
                    name: 'expect_score'
                },
                {
                    data: 'actual_score',
                    name: 'actual_score'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        // Handle month selection
        $('#month-selector li a').on('click', function(e) {
            e.preventDefault();
            selectedMonth = $(this).data('month'); // Update selected month

            // Highlight the active month
            $('#month-selector li').removeClass('active');
            $(this).parent('li').addClass('active');

            // Reload the table with new month and year
            table.ajax.reload();
        });

        // Handle year selection and reload DataTable
        $('#search_year').on('click', function() {
            table.ajax.reload(); // Reload table with new year
        });
    });
</script>

@endsection
