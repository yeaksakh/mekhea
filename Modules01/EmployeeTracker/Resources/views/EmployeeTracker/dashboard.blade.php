@extends('layouts.app')
@section('title', __('employeetracker::lang.EmployeeTracker'))
@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('modules/minireportb1/css/module.css') }}">
    <style>
        .tick-box {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #28a745;
            border-radius: 4px;
            text-align: center;
            line-height: 18px;
            font-size: 14px;
            color: #28a745;
            background-color: #f8fff9;
            font-weight: bold;
        }

        .tick-box-empty {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #ddd;
            border-radius: 4px;
            background-color: #fafafa;
        }

        .tick-hidden .tick-box,
        .tick-hidden .tick-box-empty {
            visibility: hidden !important;
        }

        .toggle-ticks-btn {
            margin-left: 10px;
            padding: 6px 12p
            background: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .toggle-ticks-btn:hover {
            background: #218838;
        }

        .toggle-ticks-btn.active {
            background: #dc3545;
        }

        .report-link {
            color: #007bff !important;
            font-weight: 500;
        }

        .report-link:hover {
            color: #0056b3 !important;
            text-decoration: underline !important;
        }

        .reusable-table {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .reusable-table th {
            background: #f8f9fc;
        }

        .dashboard-title {
            text-align: center;
            margin-bottom: 30px;
            color: #4e73df;
        }

        .tick-wrapper {
            display: inline-block;
        }
    </style>
@endsection

@section('content')
    @includeIf('employeetracker::layouts.nav')

    <!-- Main content -->
    <section class="content no-print">

        @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    <label for="date_range_filter">@lang('report.date_range')</label>
                    <input type="text" class="form-control" id="date_range_filter" name="date_range_filter">
                </div>
            </div>


            <!-- Toggle Ticks Button -->
            <div class="col-md-3">
                <div class="box-body" style="margin: 15px 0;">
                    <button id="toggleTicksBtn" class="toggle-ticks-btn" onclick="toggleTicks()">
                        👁️ Hide Ticks
                    </button>
                </div>
            </div>

            <div class="col-md-3">
                @include('employeetracker::components.printbutton', [
                    'report_name' => __('employeetracker::lang.employee_tracker_by_module'),
                    'print_by' => $businessInfo['user_name'],
                ])
            </div>
        @endcomponent

        @include('employeetracker::components.reportheadertoggle', [
            'report_name' => __('employeetracker::lang.employee_tracker_by_module'),
            'print_by' => $businessInfo['user_name'] ?? 'System User',
            'start_date' => '',
            'end_date' => '',
        ])


        <div class="box-body">
            <table class="reusable-table" id="income-table">
                <thead>
                    <tr>
                        <th class="sm">#</th>
                        <th class="md">@lang('employeetracker::lang.dapartment_2')</th>
                        <th class="md">@lang('employeetracker::lang.finish')</th>
                        <th class="md">@lang('employeetracker::lang.not_finish')</th>
                        <th class="md">@lang('employeetracker::lang.have_problem')</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- <tr data-report="sale">
                        <td>1</td>
                        <td><a href="{{ route('sale-tracking.report') }}" class="report-link">@lang('employeetracker::lang.employee_tracker_sale')</a></td>
                        <td class="status-finish">
                            <span class="tick-wrapper"></span>
                        </td>
                        <td class="status-not-finish">
                            <span class="tick-wrapper"></span>
                        </td>
                        <td class="status-problem">
                            <span class="tick-wrapper">
                                <span class="tick-box-empty"></span>
                            </span>
                        </td>
                    </tr>
                    <tr data-report="franchise">
                        <td>2</td>
                        <td><a href="{{ route('franchise-tracking.report') }}" class="report-link">@lang('employeetracker::lang.employee_tracker_franchise')</a>
                        </td>
                        <td class="status-finish">
                            <span class="tick-wrapper"></span>
                        </td>
                        <td class="status-not-finish">
                            <span class="tick-wrapper"></span>
                        </td>
                        <td class="status-problem">
                            <span class="tick-wrapper">
                                <span class="tick-box-empty"></span>
                            </span>
                        </td>
                    </tr>
                    <tr data-report="tech-hr">
                        <td>3</td>
                        <td><a href="{{ route('tech-hr-tracking.report') }}" class="report-link">@lang('employeetracker::lang.employee_tracker_accounting')</a></td>
                        <td class="status-finish">
                            <span class="tick-wrapper"></span>
                        </td>
                        <td class="status-not-finish">
                            <span class="tick-wrapper"></span>
                        </td>
                        <td class="status-problem">
                            <span class="tick-wrapper">
                                <span class="tick-box-empty"></span>
                            </span>
                        </td>
                    </tr> --}}
                     <tr data-report="sale">
                        <td>1</td>
                        <td><a href="{{ route('sale-task.report') }}" class="report-link">@lang('employeetracker::lang.employee_tracker_sale')</a></td>
                        <td class="status-finish">
                            <span class="tick-wrapper"></span>
                        </td>
                        <td class="status-not-finish">
                            <span class="tick-wrapper"></span>
                        </td>
                        <td class="status-problem">
                            <span class="tick-wrapper">
                                <span class="tick-box-empty"></span>
                            </span>
                        </td>
                    </tr>
                     <tr data-report="franchise">
                        <td>2</td>
                        <td><a href="{{ route('franchise-tracking.report') }}" class="report-link">@lang('employeetracker::lang.employee_tracker_franchise')</a>
                        </td>
                        <td class="status-finish">
                            <span class="tick-wrapper"></span>
                        </td>
                        <td class="status-not-finish">
                            <span class="tick-wrapper"></span>
                        </td>
                        <td class="status-problem">
                            <span class="tick-wrapper">
                                <span class="tick-box-empty"></span>
                            </span>
                        </td>
                    </tr>
                      <tr data-report="franchise">
                        <td>3</td>
                        <td><a href="{{ route('accounting-tracking.report') }}" class="report-link">@lang('employeetracker::lang.employee_tracker_accounting')</a>
                        </td>
                        <td class="status-finish">
                            <span class="tick-wrapper"></span>
                        </td>
                        <td class="status-not-finish">
                            <span class="tick-wrapper"></span>
                        </td>
                        <td class="status-problem">
                            <span class="tick-wrapper">
                                <span class="tick-box-empty"></span>
                            </span>
                        </td>
                    </tr>
                      <tr data-report="franchise">
                        <td>4</td>
                        <td><a href="{{ route('hr-tracking.report') }}" class="report-link">@lang('employeetracker::lang.employee_tracker_hr')</a>
                        </td>
                        <td class="status-finish">
                            <span class="tick-wrapper"></span>
                        </td>
                        <td class="status-not-finish">
                            <span class="tick-wrapper"></span>
                        </td>
                        <td class="status-problem">
                            <span class="tick-wrapper">
                                <span class="tick-box-empty"></span>
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
@endsection

@section('javascript')
    <script>
        let ticksVisible = true;

        function toggleTicks() {
            ticksVisible = !ticksVisible;
            const tickWrappers = document.querySelectorAll('.tick-wrapper');
            const toggleBtn = document.getElementById('toggleTicksBtn');

            tickWrappers.forEach(wrapper => {
                if (ticksVisible) {
                    // Show checked boxes, hide empty boxes
                    const checkedBox = wrapper.querySelector('.tick-box');
                    const emptyBox = wrapper.querySelector('.tick-box-empty');
                    if (checkedBox) checkedBox.style.display = 'inline-block';
                    if (emptyBox) emptyBox.style.display = 'none';
                } else {
                    // Hide checked boxes, show empty boxes
                    const checkedBox = wrapper.querySelector('.tick-box');
                    const emptyBox = wrapper.querySelector('.tick-box-empty');
                    if (checkedBox) checkedBox.style.display = 'none';
                    if (emptyBox) emptyBox.style.display = 'inline-block';
                }
            });

            // Update button text and style
            if (ticksVisible) {
                toggleBtn.innerHTML = '👁️ Hide Ticks';
                toggleBtn.classList.remove('active');
            } else {
                toggleBtn.innerHTML = '👁️ Show Ticks';
                toggleBtn.classList.add('active');
            }
        }

        // Initialize: hide empty boxes by default
        document.addEventListener('DOMContentLoaded', function() {
            const emptyBoxes = document.querySelectorAll('.tick-box-empty');
            emptyBoxes.forEach(box => {
                box.style.display = 'none';
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            // Initialize the date range picker
            $('#date_range_filter').daterangepicker({
                startDate: moment().subtract(29, 'days'),
                endDate: moment(),
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            });

            function updateReportStatus(reportName, reportUrl, startDate, endDate) {
                $.ajax({
                    url: reportUrl,
                    data: {
                        start_date: startDate,
                        end_date: endDate
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.data) {
                            let hasWorking = response.data.some(e => e.work_status === 'Working');
                            let hasNotWorking = response.data.some(e => e.work_status !== 'Working' && e.work_status);

                            const row = $(`tr[data-report="${reportName}"]`);
                            const finishCell = row.find('.status-finish .tick-wrapper');
                            const notFinishCell = row.find('.status-not-finish .tick-wrapper');

                            if (hasNotWorking) {
                                notFinishCell.html('<span class="tick-box">✓</span>');
                                finishCell.html('<span class="tick-box-empty" style="display: inline-block;"></span>');
                            } else if (hasWorking) {
                                finishCell.html('<span class="tick-box">✓</span>');
                                notFinishCell.html('<span class="tick-box-empty" style="display: inline-block;"></span>');
                            } else {
                                finishCell.html('<span class="tick-box-empty" style="display: inline-block;"></span>');
                                notFinishCell.html('<span class="tick-box-empty" style="display: inline-block;"></span>');
                            }
                        }
                    },
                    error: function() {
                        console.error('Failed to load data for report: ' + reportName);
                        const row = $(`tr[data-report="${reportName}"]`);
                        row.find('.status-finish .tick-wrapper').html('<span title="Error loading data">?</span>');
                        row.find('.status-not-finish .tick-wrapper').html('<span title="Error loading data">?</span>');
                    }
                });
            }

            const saleReportUrl = "{{ action([\Modules\EmployeeTracker\Http\Controllers\EmployeeReportController::class, 'getSaleEmployeeWorkTrackingReport']) }}";
            const franchiseReportUrl = "{{ action([\Modules\EmployeeTracker\Http\Controllers\EmployeeReportController::class, 'getFranchiseEmployeeWorkTrackingReport']) }}";
            const techHrReportUrl = "{{ action([\Modules\EmployeeTracker\Http\Controllers\EmployeeReportController::class, 'getTechHrEmployeeWorkTrackingReport']) }}";

            function fetchAllStatuses(startDate, endDate) {
                updateReportStatus('sale', saleReportUrl, startDate, endDate);
                updateReportStatus('franchise', franchiseReportUrl, startDate, endDate);
                updateReportStatus('tech-hr', techHrReportUrl, startDate, endDate);
            }

            // Initial fetch on page load
            var initialStartDate = $('#date_range_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
            var initialEndDate = $('#date_range_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
            fetchAllStatuses(initialStartDate, initialEndDate);

            // Re-fetch on date change
            $('#date_range_filter').on('apply.daterangepicker', function(ev, picker) {
                ev.preventDefault(); // This prevents the page from reloading
                var startDate = picker.startDate.format('YYYY-MM-DD');
                var endDate = picker.endDate.format('YYYY-MM-DD');
                fetchAllStatuses(startDate, endDate);
            });
        });
    </script>
@endsection
