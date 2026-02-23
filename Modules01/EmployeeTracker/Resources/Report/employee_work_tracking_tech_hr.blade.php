@extends('layouts.app')
@section('title', __('employeetracker::lang.employee_tracker_accounting'))
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
    @include('employeetracker::components.backbutton')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div style="margin: 16px" class="no-print">
        @component('components.filters', ['title' => 'Tech & HR Work Tracking'])
            <div class="col-md-3">
                <div class="form-group">
                    <label for="date_range_filter">Date Range</label>
                    <input type="text" class="form-control" id="date_range_filter" name="date_range_filter">
                </div>
            </div>



            @include('employeetracker::components.printbutton', [
                'report_name' => __('employeetracker::lang.employee_tracker_accounting'),
                'print_by' => $businessInfo['user_name'],
            ])
        @endcomponent
    </div>

    @include('employeetracker::components.reportheadertoggle', [
        'report_name' => __('employeetracker::lang.employee_tracker_accounting'),
        'print_by' => $businessInfo['user_name'] ?? 'System User',
        'start_date' => $start_date,
        'end_date' => $end_date,
    ])

    <div class="reusable-table-container">
        <table class="reusable-table" id="employee_work_table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Department</th>
                    <th>Employee Name</th>
                    <th>Total Transactions</th>
                    <th>Expenses</th>
                    <th>Purchases</th>
                    <th>Sales</th>
                    <th>Work Status</th>
                    <th>Last Activity</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th colspan="3" style="text-align:right">Totals:</th>
                    <th id="footer-total-transactions"></th>
                    <th id="footer-expenses"></th>
                    <th id="footer-purchases"></th>
                    <th id="footer-sales"></th>
                    <th colspan="2"></th>
                </tr>
            </tfoot>
        </table>
    </div>
@endsection

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
            // Set default dates
            var startDate = moment().subtract(30, 'days');
            var endDate = moment();

            @if (isset($start_date) && $start_date)
                startDate = moment('{{ $start_date }}', 'YYYY-MM-DD');
            @endif

            @if (isset($end_date) && $end_date)
                endDate = moment('{{ $end_date }}', 'YYYY-MM-DD');
            @endif

            // Date range picker (auto filter like income_for_month)
            $('#date_range_filter').daterangepicker({
                startDate: startDate,
                endDate: endDate,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')],
                    'This Year': [moment().startOf('year'), moment().endOf('year')],
                    'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year')
                        .endOf('year')
                    ],
                    'Last 90 Days': [moment().subtract(89, 'days'), moment()],
                    'Last 12 Months': [moment().subtract(11, 'months').startOf('month'), moment().endOf(
                        'month')]
                },
                autoUpdateInput: false,
                locale: {
                    format: 'YYYY-MM-DD',
                    cancelLabel: 'Clear'
                }
            });

            // Initialize visible value
            $('#date_range_filter').val(startDate.format('YYYY-MM-DD') + ' ~ ' + endDate.format('YYYY-MM-DD'));

            // Auto-apply on selection
            $('#date_range_filter').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' ~ ' + picker.endDate.format(
                    'YYYY-MM-DD'));
                if (employee_work_table) employee_work_table.ajax.reload();
            });

            // Clear on cancel
            $('#date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                if (employee_work_table) employee_work_table.ajax.reload();
            });

            var employee_work_table = $('#employee_work_table').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ action([\Modules\EmployeeTracker\Http\Controllers\EmployeeReportController::class, 'getTechHrEmployeeWorkTrackingReport']) }}",
                    data: function(d) {
                        var dateRange = $('#date_range_filter').val();
                        if (dateRange) {
                            var range = dateRange.split(' ~ ');
                            if (range.length === 2) {
                                d.start_date = range[0];
                                d.end_date = range[1];
                            }
                        }
                        // No employee/department filters for this view
                    },
                    dataSrc: function(json) {
                        return json.data;
                    }
                },
                columns: [{
                        data: null,
                        defaultContent: '',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        },
                        name: 'index'
                    },
                    {
                        data: 'department',
                        name: 'department'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'total_transactions',
                        name: 'total_transactions',
                        className: 'text-center'
                    },
                    {
                        data: 'expense_count',
                        name: 'expense_count',
                        className: 'text-center'
                    },
                    {
                        data: 'purchase_count',
                        name: 'purchase_count',
                        className: 'text-center'
                    },
                    {
                        data: 'sell_audit_count',
                        name: 'sell_audit_count',
                        className: 'text-center'
                    },
                    {
                        data: 'work_status',
                        name: 'work_status',
                        render: function(data) {
                            var statusClass = data === 'Working' ? 'status-working' :
                                'status-not-working';
                            var icon = data === 'Working' ? '✓' : '✗';
                            return '<span class="' + statusClass + '">' + icon + ' ' + data +
                                '</span>';
                        }
                    },
                    {
                        data: 'last_activity',
                        name: 'last_activity',
                        className: 'text-center'
                    }
                ],
                order: [
                    [1, 'asc']
                ],
                paging: false, // Disable paging
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

                    var totalTransactions = api.column(3).data().reduce((a, b) => a + parseInt(b || 0),
                        0);
                    var totalExpenses = api.column(4).data().reduce((a, b) => a + parseInt(b || 0), 0);
                    var totalPurchases = api.column(5).data().reduce((a, b) => a + parseInt(b || 0), 0);
                    var totalSales = api.column(6).data().reduce((a, b) => a + parseInt(b || 0), 0);

                    $('#footer-total-transactions').html(totalTransactions);
                    $('#footer-expenses').html(totalExpenses);
                    $('#footer-purchases').html(totalPurchases);
                    $('#footer-sales').html(totalSales);
                }
            });

            // Simplified to rely on date range picker
        });
    </script>
@endsection
