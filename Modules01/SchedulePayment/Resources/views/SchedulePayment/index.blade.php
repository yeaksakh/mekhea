@extends('layouts.app')
@section('title', __('schedulepayment::lang.SchedulePayment'))
@section('content')
    @includeIf('schedulepayment::layouts.nav')
    <section class="content-header no-print">
        <h1>@lang('schedulepayment::lang.schedulepayment')</h1>
    </section>
    <section class="content no-print">
        @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('date_paid_5', __('schedulepayment::lang.date_paid_5') . ':') !!}
            {!! Form::text('date_range', null, [
                'placeholder' => __('lang_v1.select_a_date_range'),
                'class' => 'form-control',
                'id' => 'date_paid_5',
                'readonly',
            ]) !!}
        </div>
    </div>    
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('date_prepare_pay_6', __('schedulepayment::lang.date_prepare_pay_6') . ':') !!}
            {!! Form::text('date_range', null, [
                'placeholder' => __('lang_v1.select_a_date_range'),
                'class' => 'form-control',
                'id' => 'date_prepare_pay_6',
                'readonly',
            ]) !!}
        </div>
    </div>    
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('category_id', __('schedulepayment::lang.category') . ':') !!}
            {!! Form::select('category_id', ['' => 'All Categories'] + $category, null, ['class' => 'form-control select2', 'id' => 'category_id', 'style' => 'width:100%;']) !!}
        </div>
    </div>
        @endcomponent
        @component('components.widget', ['class' => 'box-primary', 'title' => __('schedulepayment::lang.all_SchedulePayment')])
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right btn-modal" data-href="{{action([\Modules\SchedulePayment\Http\Controllers\SchedulePaymentController::class, 'create'])}} "
                        data-container="#SchedulePayment_modal">
                        <i class="fa fa-plus"></i> @lang('messages.add')
                    </button>
                </div>
            @endslot
            <table class="table table-bordered table-striped" id="SchedulePayment_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('messages.action')</th>
                        <th>@lang('schedulepayment::lang.category')</th>
                        <th>@lang('schedulepayment::lang.create_by')</th>
                        
                        
                        
                        
                        
                        
                        
                        <th>@lang('schedulepayment::lang.title_1')</th>
                    

                        <th>@lang('schedulepayment::lang.date_paid_5')</th>
                    

                        <th>@lang('schedulepayment::lang.date_prepare_pay_6')</th>
                    

                        <th>@lang('schedulepayment::lang.status_7')</th>
                    

                        <th>@lang('schedulepayment::lang.note_8')</th>
                    
                    </tr>
                </thead>
            </table>
        @endcomponent

    </section>
    <div class="modal fade " id="SchedulePayment_modal" tabindex="-1" role="dialog" aria-labelledby="createSchedulePaymentModalLabel" ></div>
@stop

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            
                            $('#date_paid_5').daterangepicker(
                                dateRangeSettings,
                                function(start, end) {
                                    $('#date_paid_5').val(start.format(moment_date_format) + ' ~ ' + end.format(
                                        moment_date_format));
                                        table.ajax.reload();
                                }
                            );
                            $('#date_paid_5').on('cancel.daterangepicker', function(ev, picker) {
                                $('#date_paid_5').val('');
                                table.ajax.reload();
                            });
                        

                            $('#date_prepare_pay_6').daterangepicker(
                                dateRangeSettings,
                                function(start, end) {
                                    $('#date_prepare_pay_6').val(start.format(moment_date_format) + ' ~ ' + end.format(
                                        moment_date_format));
                                        table.ajax.reload();
                                }
                            );
                            $('#date_prepare_pay_6').on('cancel.daterangepicker', function(ev, picker) {
                                $('#date_prepare_pay_6').val('');
                                table.ajax.reload();
                            });
                        
            var table = $('#SchedulePayment_table').DataTable({
                processing: true,
                serverSide: true,
                scrollX:true,
                autoWidth: false,
                ajax: {
                    url: "{{ action([\Modules\SchedulePayment\Http\Controllers\SchedulePaymentController::class, 'index']) }}",
                    data: function(d) {
                        d.category_id = $('#category_id').val();
                        
                            if($('#date_paid_5').val()) {
                                var start = $('#date_paid_5').data('daterangepicker').startDate.format('YYYY-MM-DD');
                                var end = $('#date_paid_5').data('daterangepicker').endDate.format('YYYY-MM-DD');
                                d.start_date = start;
                                d.end_date = end;
                            }
                        

                            if($('#date_prepare_pay_6').val()) {
                                var start = $('#date_prepare_pay_6').data('daterangepicker').startDate.format('YYYY-MM-DD');
                                var end = $('#date_prepare_pay_6').data('daterangepicker').endDate.format('YYYY-MM-DD');
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
                    
                    
                    
                    
                    
                    
                    
                    
                        { data: 'title_1', name: 'title_1', className: 'table-ellipsis' },
                    

                        { data: 'date_paid_5', name: 'date_paid_5', className: 'table-ellipsis' },
                    

                        { data: 'date_prepare_pay_6', name: 'date_prepare_pay_6', className: 'table-ellipsis' },
                    

                        { data: 'status_7', name: 'status_7', className: 'table-ellipsis' },
                    

                        { data: 'note_8', name: 'note_8', className: 'table-ellipsis' },
                    
                    
                ],
                columnDefs:[{targets: [2], visible: false}],
            });
            $('#category_id').on('change', function() {
            table.ajax.reload(null, false); // Reload table without resetting the paging
        });

            
                        $(document).on('change', '#title_1', function() {
                            table.ajax.reload();
                        });
                    

                        $(document).on('change', '#date_paid_5', function() {
                            table.ajax.reload();
                        });
                    

                        $(document).on('change', '#date_prepare_pay_6', function() {
                            table.ajax.reload();
                        });
                    

                        $(document).on('change', '#status_7', function() {
                            table.ajax.reload();
                        });
                    

                        $(document).on('change', '#note_8', function() {
                            table.ajax.reload();
                        });
                    
            $('#SchedulePayment_modal').on('shown.bs.modal', function(e) {
                $('#SchedulePayment_modal .select2').select2();
                $('form#add_SchedulePayment_form #start_date, form#add_SchedulePayment_form #end_date').datepicker({
                    autoclose: true,
                });

                tinymce.init({
                    selector: '#SchedulePayment_modal textarea.SchedulePayment_description',
                });
            });

            $('#SchedulePayment_modal').on('hidden.bs.modal', function() {
                    tinymce.remove('#SchedulePayment_modal textarea.SchedulePayment_description');
            });
                
            $(document).on('submit', 'form#add_SchedulePayment_form, #edit_SchedulePayment_form, #audit_SchedulePayment_form', function(e) {
                e.preventDefault();
                tinymce.triggerSave();
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
                            $('div#SchedulePayment_modal').modal('hide');
                            table.ajax.reload();
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to save SchedulePayment:', error);
                        toastr.error('Failed to save SchedulePayment');
                    }
                });
            });

            $(document).on('click', '.delete-SchedulePayment', function(e) {
                e.preventDefault();
                var url = $(this).data('href');
                if (confirm('Are you sure you want to delete this SchedulePayment?')) {
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
                            console.error('Failed to delete SchedulePayment:', error);
                            toastr.error('Failed to delete SchedulePayment');
                        }
                    });
                }
            });
        });
    </script>
@endsection