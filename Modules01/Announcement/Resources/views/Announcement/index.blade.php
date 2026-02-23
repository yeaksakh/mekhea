@extends('layouts.app')
@section('title', __('announcement::lang.Announcement'))
@section('content')
    @includeIf('announcement::layouts.nav')
    <section class="content-header no-print">
        <h1>@lang('announcement::lang.announcement')</h1>
    </section>
    <section class="content no-print">
        @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('date_3', __('announcement::lang.date_3') . ':') !!}
            {!! Form::text('date_range', null, [
                'placeholder' => __('lang_v1.select_a_date_range'),
                'class' => 'form-control',
                'id' => 'date_3',
                'readonly',
            ]) !!}
        </div>
    </div>    
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('category_id', __('announcement::lang.category') . ':') !!}
            {!! Form::select('category_id', ['' => 'All Categories'] + $category, null, ['class' => 'form-control select2', 'id' => 'category_id', 'style' => 'width:100%;']) !!}
        </div>
    </div>
        @endcomponent
        @component('components.widget', ['class' => 'box-primary', 'title' => __('announcement::lang.all_Announcement')])
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right btn-modal" data-href="{{action([\Modules\Announcement\Http\Controllers\AnnouncementController::class, 'create'])}} "
                        data-container="#Announcement_modal">
                        <i class="fa fa-plus"></i> @lang('messages.add')
                    </button>
                </div>
            @endslot
            <table class="table table-bordered table-striped" id="Announcement_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('messages.action')</th>
                        <th>@lang('announcement::lang.category')</th>
                        <th>@lang('announcement::lang.create_by')</th>
                        
                        
                        
                        
                        
                        
                        
                        <th>@lang('announcement::lang.title_1')</th>
                    

                        <th>@lang('announcement::lang.description_2')</th>
                    

                        <th>@lang('announcement::lang.date_3')</th>
                    
                    </tr>
                </thead>
            </table>
        @endcomponent

    </section>
    <div class="modal fade " id="Announcement_modal" tabindex="-1" role="dialog" aria-labelledby="createAnnouncementModalLabel" ></div>
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
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Moul&family=Siemreap&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.js"></script>
    <style>
        .note-editable {
            font-family: 'KhmerOSBassac', 'Battambang', 'Siemreap', 'Moul', Arial, sans-serif;
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function() {

            $('#date_3').daterangepicker(
                dateRangeSettings,
                function(start, end) {
                    $('#date_3').val(start.format(moment_date_format) + ' ~ ' + end.format(
                        moment_date_format));
                    table.ajax.reload();
                }
            );
            $('#date_3').on('cancel.daterangepicker', function(ev, picker) {
                $('#date_3').val('');
                table.ajax.reload();
            });

            var table = $('#Announcement_table').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                autoWidth: false,
                ajax: {
                    url: "{{ action([\Modules\Announcement\Http\Controllers\AnnouncementController::class, 'index']) }}",
                    data: function(d) {
                        d.category_id = $('#category_id').val();

                        if ($('#date_3').val()) {
                            var start = $('#date_3').data('daterangepicker').startDate.format(
                                'YYYY-MM-DD');
                            var end = $('#date_3').data('daterangepicker').endDate.format('YYYY-MM-DD');
                            d.start_date = start;
                            d.end_date = end;
                        }
                    }
                },
                order: [
                    [1, 'desc']
                ],
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
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'category',
                        name: 'category',
                        className: 'table-ellipsis',
                        render: function(data, type, row) {
                            return data ? data.replace(/<[^>]*>/g, '') : '';
                        }
                    },
                    {
                        data: 'create_by',
                        name: 'create_by',
                        className: 'table-ellipsis',
                        render: function(data, type, row) {
                            return data ? data.replace(/<[^>]*>/g, '') : '';
                        }
                    },
                    {
                        data: 'title_1',
                        name: 'title_1',
                        className: 'table-ellipsis',
                        render: function(data, type, row) {
                            return data ? data.replace(/<[^>]*>/g, '') : '';
                        }
                    },
                    {
                        data: 'description_2',
                        name: 'description_2',
                        className: 'table-ellipsis',
                        render: function(data, type, row) {
                            if (!data) {
                                return '';
                            }
                            // Create a temporary div element to parse the HTML
                            var tempDiv = document.createElement('div');
                            tempDiv.innerHTML = data;
                            // Return the text content, which strips all HTML tags
                            return tempDiv.textContent || tempDiv.innerText || '';
                        }
                    },
                    {
                        data: 'date_3',
                        name: 'date_3',
                        className: 'table-ellipsis',
                        render: function(data, type, row) {
                            return data ? data.replace(/<[^>]*>/g, '') : '';
                        }
                    }
                ],
                columnDefs: [
                    { targets: [2, 6], visible: false }
                ],
                "drawCallback": function(settings) {
                    $('#Announcement_table .btn-modal').each(function() {
                        var href = $(this).data('href');
                        if (href) {
                            // Use a data attribute to store the original href
                            if (!$(this).data('original-href')) {
                                $(this).data('original-href', href);
                            }
                            var original_href = $(this).data('original-href');
                            
                            // Only modify view button's href (we assume edit contains 'edit' in url)
                            if (original_href && original_href.indexOf('edit') === -1) {
                                var date_range = $('#date_3').val();
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


            $('#Announcement_modal').on('shown.bs.modal', function(e) {
                $('#Announcement_modal .select2').select2();

                $('form#add_Announcement_form #start_date, form#add_Announcement_form #end_date').datepicker({
                    autoclose: true,
                });

                // Initialize Summernote
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

            $(document).on('submit', 'form#add_Announcement_form, #edit_Announcement_form, #audit_Announcement_form', function(e) {
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
                            $('div#Announcement_modal').modal('hide');
                            table.ajax.reload();
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to save Announcement:', error);
                        toastr.error('Failed to save Announcement');
                    }
                });
            });

            $(document).on('click', '.delete-Announcement', function(e) {
                e.preventDefault();
                var url = $(this).data('href');
                if (confirm('Are you sure you want to delete this Announcement?')) {
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
                            console.error('Failed to delete Announcement:', error);
                            toastr.error('Failed to delete Announcement');
                        }
                    });
                }
            });
        });
    </script>
@endsection