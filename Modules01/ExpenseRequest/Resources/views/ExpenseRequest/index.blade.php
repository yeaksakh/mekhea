@extends('layouts.app')
@section('title', __('expenserequest::lang.ExpenseRequest'))
@section('content')
    @includeIf('expenserequest::layouts.nav')
    <section class="content-header no-print">
        <h1>@lang('expenserequest::lang.expenserequest')</h1>
    </section>
    <section class="content no-print">
        @component('components.filters', ['title' => __('report.filters')])
                <div class="col-sm-3">
        <div class="form-group">
            {!! Form::label('who_request_expense_3', __('expenserequest::lang.who_request_expense_3').':') !!}
            {!! Form::select('who_request_expense_3', $users, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('expenserequest_date_range', __('report.date_range') . ':') !!}
            {!! Form::text('date_range', null, [
                'placeholder' => __('lang_v1.select_a_date_range'),
                'class' => 'form-control',
                'id' => 'expenserequest_date_range',
                'readonly',
            ]) !!}
        </div>  
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('category_id', __('expenserequest::lang.category') . ':') !!}
            {!! Form::select('category_id', ['' => 'All Categories'] + $category, null, ['class' => 'form-control select2', 'id' => 'category_id', 'style' => 'width:100%;']) !!}
        </div>
    </div>
        @endcomponent
        @component('components.widget', ['class' => 'box-primary', 'title' => __('expenserequest::lang.all_ExpenseRequest')])
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right btn-modal" data-href="{{action([\Modules\ExpenseRequest\Http\Controllers\ExpenseRequestController::class, 'create'])}} "
                        data-container="#ExpenseRequest_modal">
                        <i class="fa fa-plus"></i> @lang('messages.add')
                    </button>
                </div>
            @endslot
            <table class="table table-bordered table-striped" id="ExpenseRequest_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('messages.action')</th>
                        <th>@lang('expenserequest::lang.category')</th>
                        <th>@lang('expenserequest::lang.create_by')</th>
                        
                        
                        
                        
                        
                        
                        
                        <th>@lang('expenserequest::lang.amount_1')</th>
                    

                        <th>@lang('expenserequest::lang.expense_for_2')</th>
                    

                        <th>@lang('expenserequest::lang.who_request_expense_3')</th>
                    
                    </tr>
                </thead>
            </table>
        @endcomponent

    </section>
    <div class="modal fade " id="ExpenseRequest_modal" tabindex="-1" role="dialog" aria-labelledby="createExpenseRequestModalLabel" ></div>
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
            
                $('#expenserequest_date_range').daterangepicker(
                    dateRangeSettings,
                    function(start, end) {
                        $('#expenserequest_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(
                            moment_date_format));
                            table.ajax.reload();
                    }
                );
                $('#expenserequest_date_range').on('cancel.daterangepicker', function(ev, picker) {
                    $('#expenserequest_date_range').val('');
                    table.ajax.reload();
                });
            
            var table = $('#ExpenseRequest_table').DataTable({
                processing: true,
                serverSide: true,
                scrollX:true,
                autoWidth: false,
                ajax: {
                    url: "{{ action([\Modules\ExpenseRequest\Http\Controllers\ExpenseRequestController::class, 'index']) }}",
                    data: function(d) {
                        d.category_id = $('#category_id').val();
                        
                                d.who_request_expense_3 = $('#who_request_expense_3').val();
                            

                if($('#expenserequest_date_range').val()) {
                    var start = $('#expenserequest_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#expenserequest_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
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
                    
                    
                    
                    
                    
                    
                    
                    
                        { data: 'amount_1', name: 'amount_1', className: 'table-ellipsis' },
                    

                        { data: 'expense_for_2', name: 'expense_for_2', className: 'table-ellipsis' },
                    

                        { data: 'who_request_expense_3', name: 'who_request_expense_3', className: 'table-ellipsis' },
                    
                    
                ],
            });
            $('#category_id').on('change', function() {
            table.ajax.reload(null, false); // Reload table without resetting the paging
        });

            
                        $(document).on('change', '#amount_1', function() {
                            table.ajax.reload();
                        });
                    

                        $(document).on('change', '#expense_for_2', function() {
                            table.ajax.reload();
                        });
                    

                        $(document).on('change', '#who_request_expense_3', function() {
                            table.ajax.reload();
                        });
                    
            $('#ExpenseRequest_modal').on('shown.bs.modal', function(e) {
                $('#ExpenseRequest_modal .select2').select2();

                $('form#add_ExpenseRequest_form #start_date, form#add_ExpenseRequest_form #end_date').datepicker({
                    autoclose: true,
                });
            });
                
            $(document).on('submit', 'form#add_ExpenseRequest_form, #edit_ExpenseRequest_form, #audit_ExpenseRequest_form', function(e) {
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
                            $('div#ExpenseRequest_modal').modal('hide');
                            table.ajax.reload();
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to save ExpenseRequest:', error);
                        toastr.error('Failed to save ExpenseRequest');
                    }
                });
            });

            $(document).on('click', '.delete-ExpenseRequest', function(e) {
                e.preventDefault();
                var url = $(this).data('href');
                if (confirm('Are you sure you want to delete this ExpenseRequest?')) {
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
                            console.error('Failed to delete ExpenseRequest:', error);
                            toastr.error('Failed to delete ExpenseRequest');
                        }
                    });
                }
            });
        });
    </script>
@endsection