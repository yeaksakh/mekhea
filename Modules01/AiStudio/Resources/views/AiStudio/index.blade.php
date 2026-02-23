@extends('layouts.app')
@section('title', __('aistudio::lang.AiStudio'))
@section('content')
    @includeIf('aistudio::layouts.nav')
    <section class="content-header no-print">
        <h1>@lang('aistudio::lang.aistudio')</h1>
    </section>
    <section class="content no-print">
        @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('aistudio_date_range', __('report.date_range') . ':') !!}
            {!! Form::text('date_range', null, [
                'placeholder' => __('lang_v1.select_a_date_range'),
                'class' => 'form-control',
                'id' => 'aistudio_date_range',
                'readonly',
            ]) !!}
        </div>  
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('category_id', __('aistudio::lang.category') . ':') !!}
            {!! Form::select('category_id', ['' => 'All Categories'] + $category, null, ['class' => 'form-control select2', 'id' => 'category_id', 'style' => 'width:100%;']) !!}
        </div>
    </div>
        @endcomponent
        @component('components.widget', ['class' => 'box-primary', 'title' => __('aistudio::lang.all_AiStudio')])
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right btn-modal" data-href="{{action([\Modules\AiStudio\Http\Controllers\AiStudioController::class, 'create'])}} "
                        data-container="#AiStudio_modal">
                        <i class="fa fa-plus"></i> @lang('messages.add')
                    </button>
                </div>
            @endslot
            <table class="table table-bordered table-striped" id="AiStudio_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('messages.action')</th>
                        <th>@lang('aistudio::lang.category')</th>
                        <th>@lang('aistudio::lang.create_by')</th>
                        
                        
                        
                        
                        
                        
                        
                        <th>@lang('aistudio::lang.message_1')</th>
                    
                    </tr>
                </thead>
            </table>
        @endcomponent

    </section>
    <div class="modal fade " id="AiStudio_modal" tabindex="-1" role="dialog" aria-labelledby="createAiStudioModalLabel" ></div>
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
            
                $('#aistudio_date_range').daterangepicker(
                    dateRangeSettings,
                    function(start, end) {
                        $('#aistudio_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(
                            moment_date_format));
                            table.ajax.reload();
                    }
                );
                $('#aistudio_date_range').on('cancel.daterangepicker', function(ev, picker) {
                    $('#aistudio_date_range').val('');
                    table.ajax.reload();
                });
            
            var table = $('#AiStudio_table').DataTable({
                processing: true,
                serverSide: true,
                scrollX:true,
                autoWidth: false,
                ajax: {
                    url: "{{ action([\Modules\AiStudio\Http\Controllers\AiStudioController::class, 'index']) }}",
                    data: function(d) {
                        d.category_id = $('#category_id').val();
                        
                if($('#aistudio_date_range').val()) {
                    var start = $('#aistudio_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#aistudio_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
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
                    
                    
                    
                    
                    
                    
                    
                    
                        { data: 'message_1', name: 'message_1', className: 'table-ellipsis' },
                    
                    
                ],
            });
            $('#category_id').on('change', function() {
            table.ajax.reload(null, false); // Reload table without resetting the paging
        });

            
                        $(document).on('change', '#message_1', function() {
                            table.ajax.reload();
                        });
                    
            $('#AiStudio_modal').on('shown.bs.modal', function(e) {
                $('#AiStudio_modal .select2').select2();

                $('form#add_AiStudio_form #start_date, form#add_AiStudio_form #end_date').datepicker({
                    autoclose: true,
                });
            });
                
            $(document).on('submit', 'form#add_AiStudio_form, #edit_AiStudio_form, #audit_AiStudio_form', function(e) {
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
                            $('div#AiStudio_modal').modal('hide');
                            table.ajax.reload();
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to save AiStudio:', error);
                        toastr.error('Failed to save AiStudio');
                    }
                });
            });

            $(document).on('click', '.delete-AiStudio', function(e) {
                e.preventDefault();
                var url = $(this).data('href');
                if (confirm('Are you sure you want to delete this AiStudio?')) {
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
                            console.error('Failed to delete AiStudio:', error);
                            toastr.error('Failed to delete AiStudio');
                        }
                    });
                }
            });
        });
    </script>
@endsection