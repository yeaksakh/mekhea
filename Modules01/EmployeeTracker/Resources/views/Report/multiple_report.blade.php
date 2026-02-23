@extends('layouts.app')
@section('title', $reportName)
@include('minireportb1::MiniReportB1.components.linkforinclude')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('modules/employeetracker/css/module.css') }}">
    <style>
        .status-working {
            color: #28a745;
            font-weight: bold;
        }

        .status-not-working {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">



    <div style="margin: 16px" class="no-print">
        @component('components.filters', ['title' => __('report.filters')])
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('department_1', __('employeetracker::lang.department_1') . ':') !!}
                    {!! Form::select('department_1', $departments, null, [
                        'class' => 'form-control select2',
                        'style' => 'width:100%',
                        'placeholder' => __('lang_v1.select_option'),
                        'id' => 'department_1',
                    ]) !!}
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('employee_2', __('employeetracker::lang.employee_2') . ':') !!}
                    {!! Form::select('employee_2', $users, null, [
                        'class' => 'form-control select2',
                        'style' => 'width:100%',
                        'placeholder' => __('lang_v1.select_option'),
                        'id' => 'employee_2',
                    ]) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('employeetracker_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range', null, [
                        'placeholder' => __('lang_v1.select_a_date_range'),
                        'class' => 'form-control',
                        'id' => 'employeetracker_date_range',
                        'readonly',
                    ]) !!}
                </div>
            </div>

            @include('employeetracker::components.printbutton', [
                'report_name' => $reportName,
                'print_by' => $businessInfo['user_name'] ?? '',
            ])
        @endcomponent
    </div>

    @include('employeetracker::components.reportheadertoggle', [
        'report_name' => $reportName,
        'print_by' => $businessInfo['user_name'] ?? 'System User',
        'start_date' => $start_date ?? '',
        'end_date' => $end_date ?? '',
    ])

    <div class="reusable-table-container">
        <table class="reusable-table" id="EmployeeTracker_table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>@lang('employeetracker::lang.employee')</th>
                    <th>@lang('employeetracker::lang.department')</th>
                    <th>@lang('employeetracker::lang.activities')</th>
                    <th>@lang('employeetracker::lang.last_activity')</th>
                    <th>@lang('employeetracker::lang.status')</th>
                    <th>@lang('messages.action')</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th colspan="3" style="text-align:right">Totals:</th>
                    <th id="footer-activities"></th>
                    <th colspan="3"></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="modal fade" id="EmployeeTracker_modal" tabindex="-1" role="dialog"
        aria-labelledby="createEmployeeTrackerModalLabel"></div>
@stop

@section('javascript')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            // Initialize date range picker
            $('#employeetracker_date_range').daterangepicker(
                dateRangeSettings,
                function(start, end) {
                    $('#employeetracker_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(
                        moment_date_format));

                    // Update the header immediately when date range changes
                    if (typeof window.updateDateRangeDisplay === 'function') {
                        window.updateDateRangeDisplay(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
                    }

                    employee_tracker_table.ajax.reload();
                }
            );

            $('#employeetracker_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#employeetracker_date_range').val('');

                // Update header to show "All Dates" when date filter is cleared
                if (typeof window.updateDateRangeDisplay === 'function') {
                    window.updateDateRangeDisplay('', '');
                }

                employee_tracker_table.ajax.reload();
            });

            // Initialize DataTable
            var employee_tracker_table = $('#EmployeeTracker_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ $ajaxUrl ?? '' }}",
                    data: function(d) {
                        d.department_1 = $('#department_1').val();
                        d.employee_2 = $('#employee_2').val();

                        if ($('#employeetracker_date_range').val()) {
                            var start = $('#employeetracker_date_range').data('daterangepicker')
                                .startDate.format('YYYY-MM-DD');
                            var end = $('#employeetracker_date_range').data('daterangepicker').endDate
                                .format('YYYY-MM-DD');
                            d.start_date = start;
                            d.end_date = end;
                        }
                    },
                    // Add dataSrc callback to update header after AJAX response
                    dataSrc: function(json) {
                        // Update header date range based on current filter values
                        updateHeaderFromCurrentFilters();
                        return json.data;
                    }
                },
                columns: [{
                        data: null,
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
                        data: 'activity_count',
                        name: 'activity_count',
                        className: 'text-center'
                    },
                    {
                        data: 'last_activity',
                        name: 'last_activity',
                        className: 'text-center'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            var statusClass = data === 'Working' ? 'status-working' :
                                'status-not-working';
                            var icon = data === 'Working' ? '✓' : '✗';
                            return '<span class="' + statusClass + '">' + icon + ' ' + data +
                                '</span>';
                        }
                    },
                    {
                        data: 'id',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return '<button class="btn btn-xs btn-primary print-report-btn" data-user-id="' +
                                row.id + '"><i class="fa fa-print"></i> Print</button>';
                        }
                    }
                ],
                order: [
                    [1, 'asc']
                ],
                paging: false,
                dom: '<"row top-controls-container"<"col-md-6"B><"col-md-6 top-right-controls"fl>>rt<"row"<"col-md-6"i>>',
                buttons: [{
                        extend: 'colvis',
                        text: 'Columns <i class="fas fa-caret-down"></i>'
                    },
                    {
                        extend: 'csv',
                        text: 'CSV <i class="fa fa-file-csv"></i>'
                    },
                    {
                        extend: 'excel',
                        text: 'Excel <i class="fa fa-file-excel"></i>'
                    },
                    {
                        extend: 'pdf',
                        text: 'PDF <i class="fa fa-file-pdf"></i>'
                    }
                ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var totalActivities = api.column(3).data().reduce((a, b) => a + parseInt(b || 0),
                    0);
                    $('#footer-activities').html(totalActivities);
                }
            });

            // Function to update header based on current filter values
            function updateHeaderFromCurrentFilters() {
                var startDate = '';
                var endDate = '';

                if ($('#employeetracker_date_range').val() && $('#employeetracker_date_range').data(
                        'daterangepicker')) {
                    startDate = $('#employeetracker_date_range').data('daterangepicker').startDate.format(
                        'YYYY-MM-DD');
                    endDate = $('#employeetracker_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                }

                if (typeof window.updateDateRangeDisplay === 'function') {
                    window.updateDateRangeDisplay(startDate, endDate);
                }
            }

            // Filter event handlers
            $('#department_1, #employee_2').on('change', function() {
                employee_tracker_table.ajax.reload();
                // Update header when other filters change (maintains current date range)
                updateHeaderFromCurrentFilters();
            });

            // Print report handler
            $(document).on('click', '.print-report-btn', function(e) {
                e.preventDefault();
                var userId = $(this).data('user-id');
                var startDate = '';
                var endDate = '';

                if ($('#employeetracker_date_range').val()) {
                    var dateRange = $('#employeetracker_date_range').val().split(' ~ ');
                    startDate = dateRange[0];
                    endDate = dateRange[1];

                    console.log(startDate, endDate);
                }

                var departmentId = $('#department_1').val();
                var reportName = "{{ $reportName ?? 'Employee Activity Report' }}"; // Get report name

                // Redirect to print route with parameters
                var printUrl = "{{ route('employee-tracking.print') }}?user_id=" + userId;
                if (startDate) printUrl += "&start_date=" + startDate;
                if (endDate) printUrl += "&end_date=" + endDate;
                if (departmentId) printUrl += "&department_id=" + departmentId;
                printUrl += "&report_name=" + encodeURIComponent(reportName); // Add report name

                window.open(printUrl, '_blank');
            });
            // Initialize header display on page load
            setTimeout(function() {
                updateHeaderFromCurrentFilters();
            }, 100);
        });
    </script>
@endsection
