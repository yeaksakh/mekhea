@extends('layouts.app')
@section('title', __('auditexpense::lang.AuditExpense'))
@section('content')
    @includeIf('auditexpense::layouts.nav')
    <section class="content-header no-print">
        <h1>@lang('auditexpense::lang.auditexpense')</h1>
    </section>
    <section class="content no-print">
        @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('auditexpense_date_range', __('report.date_range') . ':') !!}
            {!! Form::text('date_range', null, [
                'placeholder' => __('lang_v1.select_a_date_range'),
                'class' => 'form-control',
                'id' => 'auditexpense_date_range',
                'readonly',
            ]) !!}
        </div>  
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('category_id', __('auditexpense::lang.category') . ':') !!}
            {!! Form::select('category_id', ['' => 'All Categories'] + $category, null, ['class' => 'form-control select2', 'id' => 'category_id', 'style' => 'width:100%;']) !!}
        </div>
    </div>
        @endcomponent
        @component('components.widget', ['class' => 'box-primary', 'title' => __('auditexpense::lang.all_AuditExpense')])
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right btn-modal" data-href="{{action([\Modules\AuditExpense\Http\Controllers\AuditExpenseController::class, 'create'])}} "
                        data-container="#AuditExpense_modal">
                        <i class="fa fa-plus"></i> @lang('messages.add')
                    </button>
                </div>
            @endslot
            <table class="table table-bordered table-striped" id="AuditExpense_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('messages.action')</th>
                        <th>@lang('auditexpense::lang.category')</th>
                        <th>@lang('auditexpense::lang.create_by')</th>
                        
                        
                        
                        
                        
                        
                        
                        <th>@lang('auditexpense::lang.ExpenseSource_1')</th>
                    

                        <th>@lang('auditexpense::lang.Amount_2')</th>
                    
                    </tr>
                </thead>
            </table>
        @endcomponent

    </section>
    <div class="modal fade " id="AuditExpense_modal" tabindex="-1" role="dialog" aria-labelledby="createAuditExpenseModalLabel" ></div>
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
            
                $('#auditexpense_date_range').daterangepicker(
                    dateRangeSettings,
                    function(start, end) {
                        $('#auditexpense_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(
                            moment_date_format));
                            table.ajax.reload();
                    }
                );
                $('#auditexpense_date_range').on('cancel.daterangepicker', function(ev, picker) {
                    $('#auditexpense_date_range').val('');
                    table.ajax.reload();
                });
            
            var table = $('#AuditExpense_table').DataTable({
                processing: true,
                serverSide: true,
                scrollX:true,
                autoWidth: false,
                ajax: {
                    url: "{{ action([\Modules\AuditExpense\Http\Controllers\AuditExpenseController::class, 'index']) }}",
                    data: function(d) {
                        d.category_id = $('#category_id').val();
                        
                if($('#auditexpense_date_range').val()) {
                    var start = $('#auditexpense_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#auditexpense_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
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
                    
                    
                    
                    
                    
                    
                    
                    
                        { data: 'ExpenseSource_1', name: 'ExpenseSource_1', className: 'table-ellipsis' },
                    

                        { data: 'Amount_2', name: 'Amount_2', className: 'table-ellipsis' },
                    
                    
                ],
            });
            $('#category_id').on('change', function() {
            table.ajax.reload(null, false); // Reload table without resetting the paging
        });

            
                        $(document).on('change', '#ExpenseSource_1', function() {
                            table.ajax.reload();
                        });
                    

                        $(document).on('change', '#Amount_2', function() {
                            table.ajax.reload();
                        });
                    
            $('#AuditExpense_modal').on('shown.bs.modal', function(e) {
                $('#AuditExpense_modal .select2').select2();

                $('form#add_AuditExpense_form #start_date, form#add_AuditExpense_form #end_date').datepicker({
                    autoclose: true,
                });
            });
                
            $(document).on('submit', 'form#add_AuditExpense_form, #edit_AuditExpense_form, #audit_AuditExpense_form', function(e) {
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
                            $('div#AuditExpense_modal').modal('hide');
                            table.ajax.reload();
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to save AuditExpense:', error);
                        toastr.error('Failed to save AuditExpense');
                    }
                });
            });

            $(document).on('click', '.delete-AuditExpense', function(e) {
                e.preventDefault();
                var url = $(this).data('href');
                if (confirm('Are you sure you want to delete this AuditExpense?')) {
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
                            console.error('Failed to delete AuditExpense:', error);
                            toastr.error('Failed to delete AuditExpense');
                        }
                    });
                }
            });
        });
    </script>
@endsection