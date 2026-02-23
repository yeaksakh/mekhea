@extends('layouts.app')
@section('title', __('employeetracker::lang.EmployeeTracker'))
@section('content')
    @includeIf('employeetracker::layouts.nav')
    <section class="content-header no-print">
        <h1>@lang('employeetracker::lang.employeetracker')</h1>
    </section>
    <section class="content no-print">
        @component('components.filters', ['title' => __('report.filters')])
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('department_1', __('employeetracker::lang.department_1').':') !!}
                    {!! Form::select('department_1', $departments, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.select_option'), 'id' => 'department_1']); !!}
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('employee_2', __('employeetracker::lang.employee_2').':') !!}
                    {!! Form::select('employee_2', $users, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.select_option'), 'id' => 'employee_2']); !!}
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
        @endcomponent
        
        @component('components.widget', ['class' => 'box-primary', 'title' => __('employeetracker::lang.all_EmployeeTracker')])
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right btn-modal" data-href="{{action([\Modules\EmployeeTracker\Http\Controllers\EmployeeTrackerController::class, 'create'])}}"
                        data-container="#EmployeeTracker_modal">
                        <i class="fa fa-plus"></i> @lang('messages.add')
                    </button>
                </div>
            @endslot
            
            <table class="table table-bordered table-striped" id="EmployeeTracker_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('messages.action')</th>
                        <th>@lang('employeetracker::lang.employee')</th>
                        <th>@lang('employeetracker::lang.department')</th>
                        <th>@lang('employeetracker::lang.activities')</th>
                        <th>@lang('employeetracker::lang.last_activity')</th>
                        <th>@lang('employeetracker::lang.status')</th>
                    </tr>
                </thead>
            </table>
        @endcomponent

    </section>
    
    <div class="modal fade" id="EmployeeTracker_modal" tabindex="-1" role="dialog" aria-labelledby="createEmployeeTrackerModalLabel"></div>
@stop

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        // Initialize date range picker
        $('#employeetracker_date_range').daterangepicker(
            dateRangeSettings,
            function(start, end) {
                $('#employeetracker_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                employee_tracker_table.ajax.reload();
            }
        );
        
        $('#employeetracker_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#employeetracker_date_range').val('');
            employee_tracker_table.ajax.reload();
        });
        
        // Initialize DataTable
        var employee_tracker_table = $('#EmployeeTracker_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ action([\Modules\EmployeeTracker\Http\Controllers\EmployeeTrackerController::class, 'index']) }}",
                data: function(d) {
                    d.department_1 = $('#department_1').val();
                    d.employee_2 = $('#employee_2').val();
                    
                    if($('#employeetracker_date_range').val()) {
                        var start = $('#employeetracker_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        var end = $('#employeetracker_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        d.start_date = start;
                        d.end_date = end;
                    }
                }
            },
            columns: [
                { data: null, orderable: false, searchable: false, render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }},
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'employee', name: 'employee' },
                { data: 'department', name: 'department' },
                { data: 'activity_count', name: 'activity_count' },
                { data: 'last_activity', name: 'last_activity' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
            ],
        });
        
        // Filter event handlers
        $('#department_1, #employee_2').on('change', function() {
            employee_tracker_table.ajax.reload();
        });
        
        // Form submission handler for create/edit
        $(document).on('submit', 'form#add_EmployeeTracker_form, form#edit_EmployeeTracker_form', function(e) {
            e.preventDefault();
            var form = $(this);
            var data = new FormData(this);
            var url = form.attr('action');
            var submitButton = form.find('button[type="submit"]');
            var originalButtonText = submitButton.html();

            $.ajax({
                method: 'POST',
                url: url,
                data: data,
                processData: false,
                contentType: false,
                dataType: 'json',
                beforeSend: function() {
                    submitButton.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> ' + LANG.saving);
                },
                success: function(result) {
                    submitButton.prop('disabled', false).html(originalButtonText);
                    if (result.success) {
                        $('div#EmployeeTracker_modal').modal('hide');
                        employee_tracker_table.ajax.reload();
                        toastr.success(result.msg);
                    } else {
                        toastr.error(result.msg);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    submitButton.prop('disabled', false).html(originalButtonText);
                    if (jqXHR.status === 422) {
                        var errors = jqXHR.responseJSON.errors;
                        for (var key in errors) {
                            toastr.error(errors[key][0]);
                        }
                    } else {
                        toastr.error('Error: ' + errorThrown);
                    }
                }
            });
        });

        // Delete handler
        $(document).on('click', '.delete-EmployeeTracker', function(e) {
            e.preventDefault();
            var url = $(this).data('href');
            swal({
                title: LANG.sure,
                text: LANG.confirm_delete,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: url,
                        method: 'DELETE',
                        dataType: 'json',
                        success: function(result) {
                            if (result.success) {
                                employee_tracker_table.ajax.reload();
                                toastr.success(result.msg);
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                        error: function(xhr, status, error) {
                            toastr.error('Failed to delete item.');
                        }
                    });
                }
            });
        });
    });
</script>
@endsection