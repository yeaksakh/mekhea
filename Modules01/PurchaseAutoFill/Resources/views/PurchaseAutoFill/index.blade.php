@extends('layouts.app')
@section('title', __('purchaseautofill::lang.PurchaseAutoFill'))
@section('content')
    @includeIf('purchaseautofill::layouts.nav')
    <section class="content-header no-print">
        <h1>@lang('purchaseautofill::lang.purchaseautofill')</h1>
    </section>
    <section class="content no-print">
        @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('purchaseautofill_date_range', __('report.date_range') . ':') !!}
            {!! Form::text('date_range', null, [
                'placeholder' => __('lang_v1.select_a_date_range'),
                'class' => 'form-control',
                'id' => 'purchaseautofill_date_range',
                'readonly',
            ]) !!}
        </div>  
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('category_id', __('purchaseautofill::lang.category') . ':') !!}
            {!! Form::select('category_id', ['' => 'All Categories'] + $category, null, ['class' => 'form-control select2', 'id' => 'category_id', 'style' => 'width:100%;']) !!}
        </div>
    </div>
        @endcomponent
        @component('components.widget', ['class' => 'box-primary', 'title' => __('purchaseautofill::lang.all_PurchaseAutoFill')])
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right btn-modal" data-href="{{action([\Modules\PurchaseAutoFill\Http\Controllers\PurchaseAutoFillController::class, 'create'])}} "
                        data-container="#PurchaseAutoFill_modal">
                        <i class="fa fa-plus"></i> @lang('messages.add')
                    </button>
                </div>
            @endslot
            <table class="table table-bordered table-striped" id="PurchaseAutoFill_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('messages.action')</th>
                        <th>@lang('purchaseautofill::lang.category')</th>
                        <th>@lang('purchaseautofill::lang.create_by')</th>
                        
                        
                        
                        
                        
                        
                        
                        <th>@lang('purchaseautofill::lang.title_1')</th>
                    

                        <th>@lang('purchaseautofill::lang.topic _5')</th>
                    
                    </tr>
                </thead>
            </table>
        @endcomponent

    </section>
    <div class="modal fade " id="PurchaseAutoFill_modal" tabindex="-1" role="dialog" aria-labelledby="createPurchaseAutoFillModalLabel" ></div>
@stop

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            
                $('#purchaseautofill_date_range').daterangepicker(
                    dateRangeSettings,
                    function(start, end) {
                        $('#purchaseautofill_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(
                            moment_date_format));
                            table.ajax.reload();
                    }
                );
                $('#purchaseautofill_date_range').on('cancel.daterangepicker', function(ev, picker) {
                    $('#purchaseautofill_date_range').val('');
                    table.ajax.reload();
                });
            
            var table = $('#PurchaseAutoFill_table').DataTable({
                processing: true,
                serverSide: true,
                scrollX:true,
                autoWidth: false,
                ajax: {
                    url: "{{ action([\Modules\PurchaseAutoFill\Http\Controllers\PurchaseAutoFillController::class, 'index']) }}",
                    data: function(d) {
                        d.category_id = $('#category_id').val();
                        
                if($('#purchaseautofill_date_range').val()) {
                    var start = $('#purchaseautofill_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#purchaseautofill_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
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
                    

                        { data: 'topic _5', name: 'topic _5', className: 'table-ellipsis' },
                    
                    
                ],
            });
            $('#category_id').on('change', function() {
            table.ajax.reload(null, false); // Reload table without resetting the paging
        });

            
                        $(document).on('change', '#title_1', function() {
                            table.ajax.reload();
                        });
                    

                        $(document).on('change', '#topic _5', function() {
                            table.ajax.reload();
                        });
                    
            $('#PurchaseAutoFill_modal').on('shown.bs.modal', function(e) {
                $('#PurchaseAutoFill_modal .select2').select2();
                $('form#add_PurchaseAutoFill_form #start_date, form#add_PurchaseAutoFill_form #end_date').datepicker({
                    autoclose: true,
                });

                tinymce.init({
                    selector: '#PurchaseAutoFill_modal textarea.PurchaseAutoFill_description',
                });
            });

            $('#PurchaseAutoFill_modal').on('hidden.bs.modal', function() {
                    tinymce.remove('#PurchaseAutoFill_modal textarea.PurchaseAutoFill_description');
            });
                
            $(document).on('submit', 'form#add_PurchaseAutoFill_form, #edit_PurchaseAutoFill_form, #audit_PurchaseAutoFill_form', function(e) {
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
                            $('div#PurchaseAutoFill_modal').modal('hide');
                            table.ajax.reload();
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to save PurchaseAutoFill:', error);
                        toastr.error('Failed to save PurchaseAutoFill');
                    }
                });
            });

            $(document).on('click', '.delete-PurchaseAutoFill', function(e) {
                e.preventDefault();
                var url = $(this).data('href');
                if (confirm('Are you sure you want to delete this PurchaseAutoFill?')) {
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
                            console.error('Failed to delete PurchaseAutoFill:', error);
                            toastr.error('Failed to delete PurchaseAutoFill');
                        }
                    });
                }
            });
        });
    </script>
@endsection