@extends('layouts.app')
@section('title', __('employeecardb1::lang.EmployeeCardB1'))
@section('content')
    @includeIf('employeecardb1::layouts.nav')
    <section class="content-header no-print">
        <h1>@lang('employeecardb1::lang.employeecardb1')</h1>
    </section>
    <section class="content no-print">
        @component('components.filters', ['title' => __('report.filters')])
                <div class="col-sm-3">
        <div class="form-group">
            {!! Form::label('employee_1', __('employeecardb1::lang.employee_1').':') !!}
            {!! Form::select('employee_1', $users, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('employeecardb1_date_range', __('report.date_range') . ':') !!}
            {!! Form::text('date_range', null, [
                'placeholder' => __('lang_v1.select_a_date_range'),
                'class' => 'form-control',
                'id' => 'employeecardb1_date_range',
                'readonly',
            ]) !!}
        </div>  
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('category_id', __('employeecardb1::lang.category') . ':') !!}
            {!! Form::select('category_id', ['' => 'All Categories'] + $category, null, ['class' => 'form-control select2', 'id' => 'category_id', 'style' => 'width:100%;']) !!}
        </div>
    </div>
        @endcomponent
        @component('components.widget', ['class' => 'box-primary', 'title' => __('employeecardb1::lang.all_EmployeeCardB1')])
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right btn-modal" data-href="{{action([\Modules\EmployeeCardB1\Http\Controllers\EmployeeCardB1Controller::class, 'create'])}} "
                        data-container="#EmployeeCardB1_modal">
                        <i class="fa fa-plus"></i> @lang('messages.add')
                    </button>
                </div>
            @endslot
            <table class="table table-bordered table-striped" id="EmployeeCardB1_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('messages.action')</th>
                        <th>@lang('employeecardb1::lang.category')</th>
                        <th>@lang('employeecardb1::lang.create_by')</th>
                        
                        
                        
                        
                        
                        
                        
                        <th>@lang('employeecardb1::lang.employee_1')</th>
                    
                    </tr>
                </thead>
            </table>
        @endcomponent

    </section>
    <div class="modal fade " id="EmployeeCardB1_modal" tabindex="-1" role="dialog" aria-labelledby="createEmployeeCardB1ModalLabel" ></div>
@stop
<style>
.table-ellipsis {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 150px;  /* Adjust as needed */
}
</style>

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            
                $('#employeecardb1_date_range').daterangepicker(
                    dateRangeSettings,
                    function(start, end) {
                        $('#employeecardb1_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(
                            moment_date_format));
                            table.ajax.reload();
                    }
                );
                $('#employeecardb1_date_range').on('cancel.daterangepicker', function(ev, picker) {
                    $('#employeecardb1_date_range').val('');
                    table.ajax.reload();
                });
            
            var table = $('#EmployeeCardB1_table').DataTable({
                processing: true,
                serverSide: true,
                scrollX:true,
                autoWidth: false,
                ajax: {
                    url: "{{ action([\Modules\EmployeeCardB1\Http\Controllers\EmployeeCardB1Controller::class, 'index']) }}",
                    data: function(d) {
                        d.category_id = $('#category_id').val();
                        
                                d.employee_1 = $('#employee_1').val();
                            

                if($('#employeecardb1_date_range').val()) {
                    var start = $('#employeecardb1_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#employeecardb1_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    d.start_date = start;
                    d.end_date = end;
                }
            
                    }
                },
                order: [[1, 'desc']],
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
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                    { data: 'category', name: 'category', className: 'table-ellipsis'},
                    { data: 'create_by', name: 'create_by', className: 'table-ellipsis'},
                    
                    
                    
                    
                    
                    
                    
                    
                        { data: 'employee_1', name: 'employee_1', className: 'table-ellipsis' },
                    
                    
                ],
            });
            $('#category_id').on('change', function() {
            table.ajax.reload(null, false); // Reload table without resetting the paging
        });

            
                        $(document).on('change', '#employee_1', function() {
                            table.ajax.reload();
                        });
                    
            $('#EmployeeCardB1_modal').on('shown.bs.modal', function(e) {
                $('#EmployeeCardB1_modal .select2').select2();

                $('form#add_EmployeeCardB1_form #start_date, form#add_EmployeeCardB1_form #end_date').datepicker({
                    autoclose: true,
                });
            });
                
            $(document).on('submit', 'form#add_EmployeeCardB1_form, #edit_EmployeeCardB1_form, #audit_EmployeeCardB1_form', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    method: $(this).attr('method'),
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        if (result.success) {
                            $('div#EmployeeCardB1_modal').modal('hide');
                            table.ajax.reload();
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to save EmployeeCardB1:', error);
                        toastr.error('Failed to save EmployeeCardB1');
                    }
                });
            });

            $(document).on('click', '.delete-EmployeeCardB1', function(e) {
                e.preventDefault();
                var url = $(this).data('href');
                if (confirm('Are you sure you want to delete this EmployeeCardB1?')) {
                    $.ajax({
                        url: url,
                        method: 'DELETE',
                        dataType: 'json',
                        success: function(result) {
                            if (result.success) {
                                table.ajax.reload();
                                toastr.success(result.msg);
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Failed to delete EmployeeCardB1:', error);
                            toastr.error('Failed to delete EmployeeCardB1');
                        }
                    });
                }
            });
        });
    </script>
@endsection