@extends('layouts.app')
@section('title', __('bottelegrammanager::lang.BotTelegramManager'))
@section('content')
    @includeIf('bottelegrammanager::layouts.nav')
    <section class="content-header no-print">
        <h1>@lang('bottelegrammanager::lang.bottelegrammanager')</h1>
    </section>
    <section class="content no-print">
        @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('bottelegrammanager_date_range', __('report.date_range') . ':') !!}
            {!! Form::text('date_range', null, [
                'placeholder' => __('lang_v1.select_a_date_range'),
                'class' => 'form-control',
                'id' => 'bottelegrammanager_date_range',
                'readonly',
            ]) !!}
        </div>  
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('category_id', __('bottelegrammanager::lang.category') . ':') !!}
            {!! Form::select('category_id', ['' => 'All Categories'] + $category, null, ['class' => 'form-control select2', 'id' => 'category_id', 'style' => 'width:100%;']) !!}
        </div>
    </div>
        @endcomponent
        @component('components.widget', ['class' => 'box-primary', 'title' => __('bottelegrammanager::lang.all_BotTelegramManager')])
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right btn-modal" data-href="{{action([\Modules\BotTelegramManager\Http\Controllers\BotTelegramManagerController::class, 'create'])}} "
                        data-container="#BotTelegramManager_modal">
                        <i class="fa fa-plus"></i> @lang('messages.add')
                    </button>
                </div>
            @endslot
            <table class="table table-bordered table-striped" id="BotTelegramManager_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('messages.action')</th>
                        <th>@lang('bottelegrammanager::lang.category')</th>
                        <th>@lang('bottelegrammanager::lang.create_by')</th>
                        
                        
                        
                        
                        
                        
                        
                        <th>@lang('bottelegrammanager::lang.id_1')</th>
                    
                    </tr>
                </thead>
            </table>
        @endcomponent

    </section>
    <div class="modal fade " id="BotTelegramManager_modal" tabindex="-1" role="dialog" aria-labelledby="createBotTelegramManagerModalLabel" ></div>
@stop

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            
                $('#bottelegrammanager_date_range').daterangepicker(
                    dateRangeSettings,
                    function(start, end) {
                        $('#bottelegrammanager_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(
                            moment_date_format));
                            table.ajax.reload();
                    }
                );
                $('#bottelegrammanager_date_range').on('cancel.daterangepicker', function(ev, picker) {
                    $('#bottelegrammanager_date_range').val('');
                    table.ajax.reload();
                });
            
            var table = $('#BotTelegramManager_table').DataTable({
                processing: true,
                serverSide: true,
                scrollX:true,
                autoWidth: false,
                ajax: {
                    url: "{{ action([\Modules\BotTelegramManager\Http\Controllers\BotTelegramManagerController::class, 'index']) }}",
                    data: function(d) {
                        d.category_id = $('#category_id').val();
                        
                if($('#bottelegrammanager_date_range').val()) {
                    var start = $('#bottelegrammanager_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#bottelegrammanager_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
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
                    
                    
                    
                    
                    
                    
                    
                    
                        { data: 'id_1', name: 'id_1', className: 'table-ellipsis' },
                    
                    
                ],
            });
            $('#category_id').on('change', function() {
            table.ajax.reload(null, false); // Reload table without resetting the paging
        });

            
                        $(document).on('change', '#id_1', function() {
                            table.ajax.reload();
                        });
                    
            $('#BotTelegramManager_modal').on('shown.bs.modal', function(e) {
                $('#BotTelegramManager_modal .select2').select2();
                $('form#add_BotTelegramManager_form #start_date, form#add_BotTelegramManager_form #end_date').datepicker({
                    autoclose: true,
                });

                tinymce.init({
                    selector: '#BotTelegramManager_modal textarea.BotTelegramManager_description',
                });
            });

            $('#BotTelegramManager_modal').on('hidden.bs.modal', function() {
                    tinymce.remove('#BotTelegramManager_modal textarea.BotTelegramManager_description');
            });
                
            $(document).on('submit', 'form#add_BotTelegramManager_form, #edit_BotTelegramManager_form, #audit_BotTelegramManager_form', function(e) {
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
                            $('div#BotTelegramManager_modal').modal('hide');
                            table.ajax.reload();
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to save BotTelegramManager:', error);
                        toastr.error('Failed to save BotTelegramManager');
                    }
                });
            });

            $(document).on('click', '.delete-BotTelegramManager', function(e) {
                e.preventDefault();
                var url = $(this).data('href');
                if (confirm('Are you sure you want to delete this BotTelegramManager?')) {
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
                            console.error('Failed to delete BotTelegramManager:', error);
                            toastr.error('Failed to delete BotTelegramManager');
                        }
                    });
                }
            });
        });
    </script>
@endsection