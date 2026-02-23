<div class="modal-dialog modal-lg" role="document">
    {!! Form::open([
        'action' => '\Modules\Project\Http\Controllers\TaskController@store',
        'id' => 'project_task_form',
        'method' => 'post',
    ]) !!}
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">
                @lang('project::lang.create_task')
            </h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('subject', __('project::lang.subject') . ':*') !!}
                        {!! Form::text('subject', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>
            </div>
            {!! Form::hidden('project_id', $project_id, ['class' => 'form-control']) !!}
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('description', __('lang_v1.description') . ':') !!}
                        {!! Form::textarea('description', null, ['class' => 'form-control summernote']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('start_date', __('business.start_date') . ':') !!}
                        {!! Form::text('start_date', '', [
                            'class' => 'form-control datetimepicker',
                            'readonly' => true,
                            'placeholder' => __('messages.select_date_time'),
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('due_date', __('project::lang.due_date') . ':') !!}
                        {!! Form::text('due_date', '', [
                            'class' => 'form-control datetimepicker',
                            'readonly' => true,
                            'placeholder' => __('messages.select_date_time'),
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('priority', __('project::lang.priority') . ':*') !!}
                        {!! Form::select('priority', $priorities, null, [
                            'class' => 'form-control select2',
                            'placeholder' => __('messages.please_select'),
                            'required',
                            'style' => 'width: 100%;',
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('status', __('sale.status') . ':*') !!}
                        {!! Form::select('status', $statuses, null, [
                            'class' => 'form-control select2',
                            'placeholder' => __('messages.please_select'),
                            'required',
                            'style' => 'width: 100%;',
                        ]) !!}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('user_id', __('project::lang.members') . ':*') !!}
                        {!! Form::select('user_id[]', $project_members, null, [
                            'class' => 'form-control select2',
                            'multiple',
                            'required',
                            'style' => 'width: 100%;',
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('custom_field_1', __('project::lang.task_custom_field_1') . ':') !!}
                        {!! Form::text('custom_field_1', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('custom_field_2', __('project::lang.task_custom_field_2') . ':') !!}
                        {!! Form::text('custom_field_2', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('custom_field_3', __('project::lang.task_custom_field_3') . ':') !!}
                        {!! Form::text('custom_field_3', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('custom_field_4', __('project::lang.task_custom_field_4') . ':') !!}
                        {!! Form::text('custom_field_4', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white ladda-button"
                data-style="expand-right">
                <span class="ladda-label">@lang('messages.save')</span>
            </button>

            <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">
                @lang('messages.close')
            </button>
        </div>
    </div>
    {!! Form::close() !!}
</div>
<link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Moul&family=Siemreap&display=swap"
    rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.js"></script>
<style>
    .note-editable {
        font-family: 'KhmerOSBassac', 'Battambang', 'Siemreap', 'Moul', Arial, sans-serif;
    }

    /* Hide the calendar icon from input group */
    .bootstrap-datetimepicker-widget {
        padding: 0;
    }

    /* Make time picker appear below calendar */
    .bootstrap-datetimepicker-widget .timepicker {
        border-top: 1px solid #eee;
        margin-top: 0;
        padding: 4px;
    }

    /* Style the clock icon in picker */
    .bootstrap-datetimepicker-widget .timepicker-picker .btn {
        padding: 6px 10px;
    }
</style>
<script>
    $(document).ready(function() {
        // Retrieve session date and time formats
        var dateFormat = @json(session('business.date_format', 'Y-m-d')); // Fallback to Y-m-d
        var timeFormat = @json(session('business.time_format', 24)); // Fallback to 24-hour

        // Convert PHP date format to Moment.js format
        var momentDateFormat = dateFormat
            .replace('d', 'DD')
            .replace('m', 'MM')
            .replace('Y', 'YYYY');

        // Append time format based on session('business.time_format')
        var momentFormat = momentDateFormat;
        if (timeFormat == '12') {
            momentFormat += ' hh:mm A'; // e.g., DD/MM/YYYY hh:mm A
        } else {
            momentFormat += ' HH:mm'; // e.g., DD/MM/YYYY HH:mm
        }

        // Initialize datetimepicker with dynamic format
        $('.datetimepicker').datetimepicker({
            format: momentFormat,
            useCurrent: false,
            ignoreReadonly: true,
            widgetPositioning: {
                horizontal: 'auto',
                vertical: 'bottom'
            },
            icons: {
                time: 'fas fa-clock',
                date: 'fas fa-calendar',
                up: 'fa fa-chevron-up',
                down: 'fa fa-chevron-down',
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right',
                today: 'fa fa-calendar-check-o',
                clear: 'fa fa-trash-o'
            }
        });

        // Log form data on submit for debugging
        $('#project_task_form').on('submit', function(e) {
            console.log('Date Format:', dateFormat);
            console.log('Time Format:', timeFormat);
            console.log('Moment Format:', momentFormat);
            console.log('Start Date:', $('#start_date').val());
            console.log('Due Date:', $('#due_date').val());
        });

        // Initialize Summernote and Select2 (unchanged)
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
            fontNames: ['KhmerOSBassac', 'Moul', 'Siemreap', 'Battambang', 'Arial', 'Courier New',
                'Tahoma'
            ],
            fontNamesIgnoreCheck: ['KhmerOSBassac', 'Moul', 'Siemreap', 'Battambang'],
        });

        $('.select2').select2();
    });
</script>
