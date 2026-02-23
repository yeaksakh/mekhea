@extends('layouts.app')
@section('title', __('customercardb1::lang.CustomerCardB1'))
@section('content')
    @includeIf('customercardb1::layouts.nav')
    <section class="content-header no-print">
        <h1>@lang('customercardb1::lang.customercardb1')</h1>
    </section>
    <section class="content no-print">
        @component('components.filters', ['title' => __('report.filters')])
                <div class="col-sm-3">
        <div class="form-group">
            {!! Form::label('customer_1', __('customercardb1::lang.customer_1').':') !!}
            {!! Form::select('customer_1', $customer, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('customercardb1_date_range', __('report.date_range') . ':') !!}
            {!! Form::text('date_range', null, [
                'placeholder' => __('lang_v1.select_a_date_range'),
                'class' => 'form-control',
                'id' => 'customercardb1_date_range',
                'readonly',
            ]) !!}
        </div>  
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('category_id', __('customercardb1::lang.category') . ':') !!}
            {!! Form::select('category_id', ['' => 'All Categories'] + $category, null, ['class' => 'form-control select2', 'id' => 'category_id', 'style' => 'width:100%;']) !!}
        </div>
    </div>
        @endcomponent
        @component('components.widget', ['class' => 'box-primary', 'title' => __('customercardb1::lang.all_CustomerCardB1')])
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right btn-modal" data-href="{{action([\Modules\CustomerCardB1\Http\Controllers\CustomerCardB1Controller::class, 'create'])}} "
                        data-container="#CustomerCardB1_modal">
                        <i class="fa fa-plus"></i> @lang('messages.add')
                    </button>
                </div>
            @endslot
            <table class="table table-bordered table-striped" id="CustomerCardB1_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('messages.action')</th>
                        <th>@lang('customercardb1::lang.category')</th>
                        <th>@lang('customercardb1::lang.create_by')</th>
                        
                        
                        
                        
                        
                        
                        
                        <th>@lang('customercardb1::lang.customer_1')</th>
                    
                    </tr>
                </thead>
            </table>
        @endcomponent

    </section>
    <div class="modal fade " id="CustomerCardB1_modal" tabindex="-1" role="dialog" aria-labelledby="createCustomerCardB1ModalLabel" ></div>
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
            
                $('#customercardb1_date_range').daterangepicker(
                    dateRangeSettings,
                    function(start, end) {
                        $('#customercardb1_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(
                            moment_date_format));
                            table.ajax.reload();
                    }
                );
                $('#customercardb1_date_range').on('cancel.daterangepicker', function(ev, picker) {
                    $('#customercardb1_date_range').val('');
                    table.ajax.reload();
                });
            
            var table = $('#CustomerCardB1_table').DataTable({
                processing: true,
                serverSide: true,
                scrollX:true,
                autoWidth: false,
                ajax: {
                    url: "{{ action([\Modules\CustomerCardB1\Http\Controllers\CustomerCardB1Controller::class, 'index']) }}",
                    data: function(d) {
                        d.category_id = $('#category_id').val();
                        
                            d.customer_1 = $('#customer_1').val();
                        

                if($('#customercardb1_date_range').val()) {
                    var start = $('#customercardb1_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#customercardb1_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
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
                    
                    
                    
                    
                    
                    
                    
                    
                        { data: 'customer_1', name: 'customer_1', className: 'table-ellipsis' },
                    
                    
                ],
            });
            $('#category_id').on('change', function() {
            table.ajax.reload(null, false); // Reload table without resetting the paging
        });

            
                        $(document).on('change', '#customer_1', function() {
                            table.ajax.reload();
                        });
                    
            $('#CustomerCardB1_modal').on('shown.bs.modal', function(e) {
                $('#CustomerCardB1_modal .select2').select2();

                $('form#add_CustomerCardB1_form #start_date, form#add_CustomerCardB1_form #end_date').datepicker({
                    autoclose: true,
                });
            });
                
            $(document).on('submit', 'form#add_CustomerCardB1_form, #edit_CustomerCardB1_form, #audit_CustomerCardB1_form', function(e) {
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
                            $('div#CustomerCardB1_modal').modal('hide');
                            table.ajax.reload();
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to save CustomerCardB1:', error);
                        toastr.error('Failed to save CustomerCardB1');
                    }
                });
            });

            $(document).on('click', '.delete-CustomerCardB1', function(e) {
                e.preventDefault();
                var url = $(this).data('href');
                if (confirm('Are you sure you want to delete this CustomerCardB1?')) {
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
                            console.error('Failed to delete CustomerCardB1:', error);
                            toastr.error('Failed to delete CustomerCardB1');
                        }
                    });
                }
            });
        });
    </script>
@endsection