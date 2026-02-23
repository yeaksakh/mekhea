@extends('layouts.app')
@section('title', __('sop::lang.SOP'))
@section('content')
    @includeIf('sop::layouts.nav')
    <section class="content-header no-print">
        <h1>@lang('sop::lang.sop')</h1>
    </section>
    <section class="content no-print">
        @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('sop_date_range', __('report.date_range') . ':') !!}
            {!! Form::text('date_range', null, [
                'placeholder' => __('lang_v1.select_a_date_range'),
                'class' => 'form-control',
                'id' => 'sop_date_range',
                'readonly',
            ]) !!}
        </div>  
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('category_id', __('sop::lang.category') . ':') !!}
            {!! Form::select('category_id', ['' => 'All Categories'] + $category, null, ['class' => 'form-control select2', 'id' => 'category_id', 'style' => 'width:100%;']) !!}
        </div>
    </div>
        @endcomponent
        @component('components.widget', ['class' => 'box-primary', 'title' => __('sop::lang.all_SOP')])
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right btn-modal" data-href="{{action([\Modules\SOP\Http\Controllers\SOPController::class, 'create'])}} "
                        data-container="#SOP_modal">
                        <i class="fa fa-plus"></i> @lang('messages.add')
                    </button>
                </div>
            @endslot
            <table class="table table-bordered table-striped" id="SOP_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('messages.action')</th>
                        <th>@lang('sop::lang.category')</th>
                        <th>@lang('sop::lang.create_by')</th>
                        
                        
                        
                        
                        
                        
                        
                        <th>@lang('sop::lang.title_1')</th>
                    

                        <th>@lang('sop::lang.description_5')</th>
                    
                    </tr>
                </thead>
            </table>
        @endcomponent

    </section>
    <div class="modal fade " id="SOP_modal" tabindex="-1" role="dialog" aria-labelledby="createSOPModalLabel" ></div>
@stop

@section('javascript')
    <link href="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Moul&family=Siemreap&display=swap" rel="stylesheet">
    <style>
        .note-editable {
            font-family: 'KhmerOSBassac', 'Battambang', 'Siemreap', 'Moul', Arial, sans-serif;
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function() {
            
                $('#sop_date_range').daterangepicker(
                    dateRangeSettings,
                    function(start, end) {
                        $('#sop_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(
                            moment_date_format));
                            table.ajax.reload();
                    }
                );
                $('#sop_date_range').on('cancel.daterangepicker', function(ev, picker) {
                    $('#sop_date_range').val('');
                    table.ajax.reload();
                });
            
            var table = $('#SOP_table').DataTable({
                processing: true,
                serverSide: true,
                scrollX:true,
                autoWidth: false,
                ajax: {
                    url: "{{ action([\Modules\SOP\Http\Controllers\SOPController::class, 'index']) }}",
                    data: function(d) {
                        d.category_id = $('#category_id').val();
                        
                if($('#sop_date_range').val()) {
                    var start = $('#sop_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#sop_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
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
                "drawCallback": function(settings) {
                    $('#SOP_table .btn-modal').each(function() {
                        var href = $(this).data('href');
                        if (href) {
                            if (!$(this).data('original-href')) {
                                $(this).data('original-href', href);
                            }
                            var original_href = $(this).data('original-href');
                            
                            if (original_href && original_href.indexOf('edit') === -1) {
                                var date_range = $('#sop_date_range').val();
                                if (date_range) {
                                    var new_href = original_href + (original_href.includes('?') ? '&' : '?') + 'date_range=' + encodeURIComponent(date_range);
                                    $(this).data('href', new_href);
                                } else {
                                    $(this).data('href', original_href);
                                }
                            }
                        }
                    });
                }
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
                    
            $('#SOP_modal').on('shown.bs.modal', function(e) {
                $('#SOP_modal .select2').select2();
                
                $('.summernote').summernote({
                    placeholder: 'សូមសរសេរនៅទីនេះ...',
                    tabsize: 2,
                    height: 300,
                    toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear', 'fontname', 'fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']],
                    ],
                    fontNames: ['KhmerOSBassac', 'Moul', 'Siemreap', 'Battambang', 'Arial', 'Courier New', 'Tahoma'],
                    fontNamesIgnoreCheck: ['KhmerOSBassac', 'Moul', 'Siemreap', 'Battambang'],
                });
            });

            $('#SOP_modal').on('hidden.bs.modal', function() {
                $('.summernote').summernote('destroy');
            });
                
            $(document).on('submit', 'form#add_SOP_form, #edit_SOP_form, #audit_SOP_form', function(e) {
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
                            $('div#SOP_modal').modal('hide');
                            table.ajax.reload();
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to save SOP:', error);
                        toastr.error('Failed to save SOP');
                    }
                });
            });

            $(document).on('click', '.delete-SOP', function(e) {
                e.preventDefault();
                var url = $(this).data('href');
                if (confirm('Are you sure you want to delete this SOP?')) {
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
                            console.error('Failed to delete SOP:', error);
                            toastr.error('Failed to delete SOP');
                        }
                    });
                }
            });
        });
    </script>
@endsection
