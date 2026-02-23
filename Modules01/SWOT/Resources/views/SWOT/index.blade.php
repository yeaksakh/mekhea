@extends('layouts.app')
@section('title', __('swot::lang.SWOT'))
@section('content')
    @includeIf('swot::layouts.nav')
    <section class="content-header no-print">
        <h1>@lang('swot::lang.swot')</h1>
    </section>
    <section class="content no-print">
        @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('swot_date_range', __('report.date_range') . ':') !!}
            {!! Form::text('date_range', null, [
                'placeholder' => __('lang_v1.select_a_date_range'),
                'class' => 'form-control',
                'id' => 'swot_date_range',
                'readonly',
            ]) !!}
        </div>  
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('category_id', __('swot::lang.category') . ':') !!}
            {!! Form::select('category_id', ['' => 'All Categories'] + $category, null, ['class' => 'form-control select2', 'id' => 'category_id', 'style' => 'width:100%;']) !!}
        </div>
    </div>
        @endcomponent
        @component('components.widget', ['class' => 'box-primary', 'title' => __('swot::lang.all_SWOT')])
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right btn-modal" data-href="{{action([\Modules\SWOT\Http\Controllers\SWOTController::class, 'create'])}} "
                        data-container="#SWOT_modal">
                        <i class="fa fa-plus"></i> @lang('messages.add')
                    </button>
                </div>
            @endslot
            <table class="table table-bordered table-striped" id="SWOT_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('messages.action')</th>
                        <th>@lang('swot::lang.category')</th>
                        <th>@lang('swot::lang.create_by')</th>
                        
                        
                        
                        
                        
                        
                        
                        <th>@lang('swot::lang.Title_1')</th>
                    

                        <th>@lang('swot::lang.Strengths_5')</th>
                    

                        <th>@lang('swot::lang.Weaknesses_6')</th>
                    

                        <th>@lang('swot::lang.Opportunities_7')</th>
                    

                        <th>@lang('swot::lang.Threats_8')</th>
                    

                        <th>@lang('swot::lang.Note_9')</th>
                    
                    </tr>
                </thead>
            </table>
        @endcomponent

    </section>
    <div class="modal fade " id="SWOT_modal" tabindex="-1" role="dialog" aria-labelledby="createSWOTModalLabel" ></div>
@stop

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            
                $('#swot_date_range').daterangepicker(
                    dateRangeSettings,
                    function(start, end) {
                        $('#swot_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(
                            moment_date_format));
                            table.ajax.reload();
                    }
                );
                $('#swot_date_range').on('cancel.daterangepicker', function(ev, picker) {
                    $('#swot_date_range').val('');
                    table.ajax.reload();
                });
            
            var table = $('#SWOT_table').DataTable({
                processing: true,
                serverSide: true,
                scrollX:true,
                autoWidth: false,
                ajax: {
                    url: "{{ action([\Modules\SWOT\Http\Controllers\SWOTController::class, 'index']) }}",
                    data: function(d) {
                        d.category_id = $('#category_id').val();
                        
                if($('#swot_date_range').val()) {
                    var start = $('#swot_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#swot_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
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
                    
                    
                    
                    
                    
                    
                    
                    
                        { data: 'Title_1', name: 'Title_1', className: 'table-ellipsis' },
                    

                        { data: 'Strengths_5', name: 'Strengths_5', className: 'table-ellipsis' },
                    

                        { data: 'Weaknesses_6', name: 'Weaknesses_6', className: 'table-ellipsis' },
                    

                        { data: 'Opportunities_7', name: 'Opportunities_7', className: 'table-ellipsis' },
                    

                        { data: 'Threats_8', name: 'Threats_8', className: 'table-ellipsis' },
                    

                        { data: 'Note_9', name: 'Note_9', className: 'table-ellipsis' },
                    
                    
                ],
            });
            $('#category_id').on('change', function() {
            table.ajax.reload(null, false); // Reload table without resetting the paging
        });

            
                        $(document).on('change', '#Title_1', function() {
                            table.ajax.reload();
                        });
                    

                        $(document).on('change', '#Strengths_5', function() {
                            table.ajax.reload();
                        });
                    

                        $(document).on('change', '#Weaknesses_6', function() {
                            table.ajax.reload();
                        });
                    

                        $(document).on('change', '#Opportunities_7', function() {
                            table.ajax.reload();
                        });
                    

                        $(document).on('change', '#Threats_8', function() {
                            table.ajax.reload();
                        });
                    

                        $(document).on('change', '#Note_9', function() {
                            table.ajax.reload();
                        });
                    
            $('#SWOT_modal').on('shown.bs.modal', function(e) {
                $('#SWOT_modal .select2').select2();
                $('form#add_SWOT_form #start_date, form#add_SWOT_form #end_date').datepicker({
                    autoclose: true,
                });

                tinymce.init({
                    selector: '#SWOT_modal textarea.SWOT_description',
                    height: 250,
                    menubar: 'file edit view insert format tools table help', // Matches the menubar in the image
                    menu: {
                        file: {
                            title: 'File',
                            items: 'newdocument'
                        },
                        edit: {
                            title: 'Edit',
                            items: 'undo redo'
                        },
                        view: {
                            title: 'View',
                            items: 'visualaid'
                        },
                        insert: {
                            title: 'Insert',
                            items: 'link image'
                        },
                        format: {
                            title: 'Format',
                            items: 'bold italic underline strikethrough superscript subscript | formats blockformats fontformats'
                        },
                        tools: {
                            title: 'Tools',
                            items: 'spellchecker code'
                        },
                        table: {
                            title: 'Table',
                            items: 'inserttable tableprops deletetable'
                        },
                        help: {
                            title: 'Help',
                            items: 'help'
                        }
                    },
                    toolbar: 'undo redo | styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview | forecolor backcolor | removeformat',
                    plugins: [
                        'advlist autolink lists link image charmap print preview anchor',
                        'searchreplace visualblocks code fullscreen',
                        'insertdatetime media table paste code help wordcount'
                    ],
                    // Customize toolbar styles to match the image
                    toolbar_mode: 'floating',
                    toolbar_sticky: false,
                    skin: 'oxide', // Default skin, can be customized further
                    content_css: 'default' // Use default styling, adjust if needed
                });
            });

            $('#SWOT_modal').on('hidden.bs.modal', function() {
                    tinymce.remove('#SWOT_modal textarea.SWOT_description');
            });
                
            $(document).on('submit', 'form#add_SWOT_form, #edit_SWOT_form, #audit_SWOT_form', function(e) {
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
                            $('div#SWOT_modal').modal('hide');
                            table.ajax.reload();
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to save SWOT:', error);
                        toastr.error('Failed to save SWOT');
                    }
                });
            });

            $(document).on('click', '.delete-SWOT', function(e) {
                e.preventDefault();
                var url = $(this).data('href');
                if (confirm('Are you sure you want to delete this SWOT?')) {
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
                            console.error('Failed to delete SWOT:', error);
                            toastr.error('Failed to delete SWOT');
                        }
                    });
                }
            });
        });
    </script>
@endsection