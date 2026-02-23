@extends('layouts.app')
@section('title', __('productbook::lang.ProductBook'))
@section('content')
    @includeIf('productbook::layouts.nav')
    <section class="content-header no-print">
        <h1>@lang('productbook::lang.productbook')</h1>
    </section>
    <section class="content no-print">
        @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('productbook_date_range', __('report.date_range') . ':') !!}
            {!! Form::text('date_range', null, [
                'placeholder' => __('lang_v1.select_a_date_range'),
                'class' => 'form-control',
                'id' => 'productbook_date_range',
                'readonly',
            ]) !!}
        </div>  
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('category_id', __('productbook::lang.category') . ':') !!}
            {!! Form::select('category_id', ['' => 'All Categories'] + $category, null, ['class' => 'form-control select2', 'id' => 'category_id', 'style' => 'width:100%;']) !!}
        </div>
    </div>
        @endcomponent
        @component('components.widget', ['class' => 'box-primary', 'title' => __('productbook::lang.all_ProductBook')])
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right btn-modal" data-href="{{action([\Modules\ProductBook\Http\Controllers\ProductBookController::class, 'create'])}} "
                        data-container="#ProductBook_modal">
                        <i class="fa fa-plus"></i> @lang('messages.add')
                    </button>
                </div>
            @endslot
            <table class="table table-bordered table-striped" id="ProductBook_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('messages.action')</th>
                        <th>@lang('productbook::lang.category')</th>
                        <th>@lang('productbook::lang.create_by')</th>
                        
                        
                        
                        
                        
                        
                        
                        <th>@lang('productbook::lang.title_1')</th>
                    

                        <th>@lang('productbook::lang.description_5')</th>
                    
                    </tr>
                </thead>
            </table>
        @endcomponent

    </section>
    <div class="modal fade " id="ProductBook_modal" tabindex="-1" role="dialog" aria-labelledby="createProductBookModalLabel" ></div>
@stop

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            
                $('#productbook_date_range').daterangepicker(
                    dateRangeSettings,
                    function(start, end) {
                        $('#productbook_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(
                            moment_date_format));
                            table.ajax.reload();
                    }
                );
                $('#productbook_date_range').on('cancel.daterangepicker', function(ev, picker) {
                    $('#productbook_date_range').val('');
                    table.ajax.reload();
                });
            
            var table = $('#ProductBook_table').DataTable({
                processing: true,
                serverSide: true,
                scrollX:true,
                autoWidth: false,
                ajax: {
                    url: "{{ action([\Modules\ProductBook\Http\Controllers\ProductBookController::class, 'index']) }}",
                    data: function(d) {
                        d.category_id = $('#category_id').val();
                        
                if($('#productbook_date_range').val()) {
                    var start = $('#productbook_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#productbook_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
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
                    

                        { data: 'description_5', name: 'description_5', className: 'table-ellipsis' },
                    
                    
                ],
            });
            $('#category_id').on('change', function() {
            table.ajax.reload(null, false); // Reload table without resetting the paging
        });

            
                        $(document).on('change', '#title_1', function() {
                            table.ajax.reload();
                        });
                    

                        $(document).on('change', '#description_5', function() {
                            table.ajax.reload();
                        });
                    
            $('#ProductBook_modal').on('shown.bs.modal', function(e) {
                $('#ProductBook_modal .select2').select2();
                $('form#add_ProductBook_form #start_date, form#add_ProductBook_form #end_date').datepicker({
                    autoclose: true,
                });

                tinymce.init({
                    selector: '#ProductBook_modal textarea.ProductBook_description',
                });
            });

            $('#ProductBook_modal').on('hidden.bs.modal', function() {
                    tinymce.remove('#ProductBook_modal textarea.ProductBook_description');
            });
                
            $(document).on('submit', 'form#add_ProductBook_form, #edit_ProductBook_form, #audit_ProductBook_form', function(e) {
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
                            $('div#ProductBook_modal').modal('hide');
                            table.ajax.reload();
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to save ProductBook:', error);
                        toastr.error('Failed to save ProductBook');
                    }
                });
            });

            $(document).on('click', '.delete-ProductBook', function(e) {
                e.preventDefault();
                var url = $(this).data('href');
                if (confirm('Are you sure you want to delete this ProductBook?')) {
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
                            console.error('Failed to delete ProductBook:', error);
                            toastr.error('Failed to delete ProductBook');
                        }
                    });
                }
            });
        });
    </script>
@endsection