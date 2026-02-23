@extends('layouts.app')
@section('title', __('news::lang.News'))
@section('content')
    @includeIf('news::layouts.nav')
    <section class="content-header no-print">
        <h1>@lang('news::lang.news')</h1>
    </section>
    <section class="content no-print">
        @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('news_date_range', __('report.date_range') . ':') !!}
            {!! Form::text('date_range', null, [
                'placeholder' => __('lang_v1.select_a_date_range'),
                'class' => 'form-control',
                'id' => 'news_date_range',
                'readonly',
            ]) !!}
        </div>  
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('category_id', __('news::lang.category') . ':') !!}
            {!! Form::select('category_id', ['' => 'All Categories'] + $category, null, ['class' => 'form-control select2', 'id' => 'category_id', 'style' => 'width:100%;']) !!}
        </div>
    </div>
        @endcomponent
        @component('components.widget', ['class' => 'box-primary', 'title' => __('news::lang.all_News')])
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right btn-modal" data-href="{{action([\Modules\News\Http\Controllers\NewsController::class, 'create'])}} "
                        data-container="#News_modal">
                        <i class="fa fa-plus"></i> @lang('messages.add')
                    </button>
                </div>
            @endslot
            <table class="table table-bordered table-striped" id="News_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('messages.action')</th>
                        <th>@lang('news::lang.category')</th>
                        <th>@lang('news::lang.create_by')</th>
                        
                        
                        
                        
                        
                        
                        
                        <th>@lang('news::lang.title_1')</th>
                    

                        <th>@lang('news::lang.image_5')</th>
                    

                        <th>@lang('news::lang.description_6')</th>
                    

                        <th>@lang('news::lang.image_2_7')</th>
                    
                    </tr>
                </thead>
            </table>
        @endcomponent

    </section>
    <div class="modal fade " id="News_modal" tabindex="-1" role="dialog" aria-labelledby="createNewsModalLabel" ></div>
@stop

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            
                $('#news_date_range').daterangepicker(
                    dateRangeSettings,
                    function(start, end) {
                        $('#news_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(
                            moment_date_format));
                            table.ajax.reload();
                    }
                );
                $('#news_date_range').on('cancel.daterangepicker', function(ev, picker) {
                    $('#news_date_range').val('');
                    table.ajax.reload();
                });
            
            var table = $('#News_table').DataTable({
                processing: true,
                serverSide: true,
                scrollX:true,
                autoWidth: false,
                ajax: {
                    url: "{{ action([\Modules\News\Http\Controllers\NewsController::class, 'index']) }}",
                    data: function(d) {
                        d.category_id = $('#category_id').val();
                        
                if($('#news_date_range').val()) {
                    var start = $('#news_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#news_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
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
                    

                        { data: 'image_5', name: 'image_5', className: 'table-ellipsis' },
                    

                        { data: 'description_6', name: 'description_6', className: 'table-ellipsis' },
                    

                        { data: 'image_2_7', name: 'image_2_7', className: 'table-ellipsis' },
                    
                    
                ],
            });
            $('#category_id').on('change', function() {
            table.ajax.reload(null, false); // Reload table without resetting the paging
        });

            
                        $(document).on('change', '#title_1', function() {
                            table.ajax.reload();
                        });
                    

                        $(document).on('change', '#image_5', function() {
                            table.ajax.reload();
                        });
                    

                        $(document).on('change', '#description_6', function() {
                            table.ajax.reload();
                        });
                    

                        $(document).on('change', '#image_2_7', function() {
                            table.ajax.reload();
                        });
                    
            $('#News_modal').on('shown.bs.modal', function(e) {
                $('#News_modal .select2').select2();
                $('form#add_News_form #start_date, form#add_News_form #end_date').datepicker({
                    autoclose: true,
                });

                tinymce.init({
                    selector: '#News_modal textarea.News_description',
                });
            });

            $('#News_modal').on('hidden.bs.modal', function() {
                    tinymce.remove('#News_modal textarea.News_description');
            });
                
            $(document).on('submit', 'form#add_News_form, #edit_News_form, #audit_News_form', function(e) {
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
                            $('div#News_modal').modal('hide');
                            table.ajax.reload();
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to save News:', error);
                        toastr.error('Failed to save News');
                    }
                });
            });

            $(document).on('click', '.delete-News', function(e) {
                e.preventDefault();
                var url = $(this).data('href');
                if (confirm('Are you sure you want to delete this News?')) {
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
                            console.error('Failed to delete News:', error);
                            toastr.error('Failed to delete News');
                        }
                    });
                }
            });
        });
    </script>
@endsection