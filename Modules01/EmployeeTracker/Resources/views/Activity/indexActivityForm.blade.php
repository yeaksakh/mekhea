@extends('layouts.app')
@section('title', __('employeetracker::lang.form_employeetracker'))
@section('content')
    @includeIf('employeetracker::layouts.nav')

    <section class="content no-print" style="border: 1px solid #000; padding: 15px; margin: 10px;">
        <section class="content-header no-print" style="padding: 15px; margin: 10px;">
            <h1 style="border-bottom: 1px solid #000; padding-bottom: 10px;">@lang('employeetracker::lang.form_employeetracker')</h1>
        </section>

        @component('components.widget', [
            'class' => 'box-primary',
            'title' => __('employeetracker::lang.all_EmployeeTracker_form'),
        ])
            @slot('tool')
                <div class="box-tools" style="border: 1px solid #000; padding: 10px; margin: 5px;">
                    <button type="button" class="btn btn-primary btn-modal"
                        data-href="{{ action([\Modules\EmployeeTracker\Http\Controllers\ActivityFormController::class, 'create']) }}"
                        data-container="#EmployeeTracker_modal">
                        <i class="fa fa-plus"></i> @lang('messages.add')
                    </button>
                </div>
            @endslot

            <div style="border: 1px solid #000; padding: 10px; margin: 5px;">
                <table class="table" id="EmployeeTracker_table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="border: 1px solid #000; padding: 8px;">#</th>
                            <th style="border: 1px solid #000; padding: 8px;">@lang('messages.action')</th>
                            <th style="border: 1px solid #000; padding: 8px;">@lang('employeetracker::lang.title')</th>
                            <th style="border: 1px solid #000; padding: 8px;">@lang('employeetracker::lang.description')</th>
                            <th style="border: 1px solid #000; padding: 8px;">@lang('employeetracker::lang.department')</th>
                            <th style="border: 1px solid #000; padding: 8px;">@lang('employeetracker::lang.created_by')</th>
                            <th style="border: 1px solid #000; padding: 8px;">@lang('employeetracker::lang.number_tasks')</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcomponent
    </section>

    <div class="modal fade" id="EmployeeTracker_modal" tabindex="-1" role="dialog"
        aria-labelledby="createEmployeeTrackerModalLabel" style="border: 1px solid #000;"></div>
@stop

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('#EmployeeTracker_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ action([\Modules\EmployeeTracker\Http\Controllers\ActivityFormController::class, 'fetchForms']) }}",
                },
                columns: [{
                        data: null,
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
                        data: 'title',
                        name: 'name'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'department',
                        name: 'department'
                    },
                    {
                        data: 'created_by',
                        name: 'created_by'
                    },
                    {
                        data: 'field_count',
                        name: 'field_count',
                        className: 'text-center'
                    },
                ],
            });

            // Modal handler
            $(document).on('click', '.btn-modal', function(e) {
                e.preventDefault();
                var container = $($(this).data('container'));
                var url = $(this).data('href');

                // Show loading
                container.html(
                    '<div class="modal-dialog"><div class="modal-content"><div class="modal-body text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div></div></div>'
                    ).modal('show');

                $.ajax({
                    url: url,
                    dataType: 'html',
                    success: function(result) {
                        container.html(result);
                        // Initialize select2 after content is loaded
                        container.find('.select2').select2();
                        // Initialize the edit form scripts
                        initializeEditFormScripts();
                    },
                    error: function() {
                        container.html(
                            '<div class="modal-dialog"><div class="modal-content"><div class="modal-body"><div class="alert alert-danger">Error loading content. Please try again.</div></div></div></div>'
                            );
                    }
                });
            });

            // Form submission handler
            $(document).on('submit', 'form#add_activity_form, form#edit_activity_form', function(e) {
                e.preventDefault();
                var form = $(this);
                var formData = new FormData(this);
                var submitBtn = form.find('button[type="submit"]');

                // Disable submit button
                submitBtn.prop('disabled', true).html(
                '<i class="fa fa-spinner fa-spin"></i> Processing...');

                $.ajax({
                    method: form.attr('method'),
                    url: form.attr('action'),
                    dataType: 'json',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        if (result.success) {
                            $('#EmployeeTracker_modal').modal('hide');
                            table.ajax.reload();
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                    error: function(xhr) {
                        var errorMsg = 'An error occurred. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        toastr.error(errorMsg);
                    },
                    complete: function() {
                        // Re-enable submit button
                        submitBtn.prop('disabled', false).html(form.attr('id') ===
                            'add_activity_form' ? '@lang('messages.save')' : '@lang('messages.update')'
                            );
                    }
                });
            });

            // Delete handler
            $(document).on('click', '.delete-form', function(e) {
                e.preventDefault();
                var url = $(this).data('href');

                swal({
                    title: LANG.sure,
                    text: "You won't be able to recover this form!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url: url,
                            method: 'DELETE',
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(result) {
                                if (result.success) {
                                    table.ajax.reload();
                                    toastr.success(result.msg);
                                } else {
                                    toastr.error(result.msg);
                                }
                            },
                            error: function() {
                                toastr.error('Error deleting form. Please try again.');
                            }
                        });
                    }
                });
            });

            // Function to initialize edit form scripts
            function initializeEditFormScripts() {
                var fieldIndex = $('#form-fields-container .field-group').length;

                // Show/hide config input based on field type
                $('#form-fields-container').off('change', '.field-type-select').on('change', '.field-type-select',
                    function() {
                        var fieldType = $(this).val();
                        var configContainer = $(this).closest('.row').find('.config-container');
                        if (['select', 'radio', 'checkbox'].includes(fieldType)) {
                            configContainer.show();
                        } else {
                            configContainer.hide();
                        }
                    });

                // Add new field
                $('#add-field-btn').off('click').on('click', function() {
                    var newField = `
                <div class="field-group panel panel-default" data-index="${fieldIndex}">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('employeetracker::lang.field_label')</label>
                                    <input type="text" name="fields[${fieldIndex}][field_label]" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('employeetracker::lang.field_type')</label>
                                    <select name="fields[${fieldIndex}][field_type]" class="form-control field-type-select" required>
                                        <option value="text">Text</option>
                                        <option value="textarea">Textarea</option>
                                        <option value="number">Number</option>
                                        <option value="select">Select</option>
                                        <option value="checkbox">Checkbox</option>
                                        <option value="radio">Radio</option>
                                        <option value="image">Image</option>
                                        <option value="video">Video</option>
                                        <option value="file">File</option>
                                        <option value="date">Date</option>
                                        <option value="time">Time</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 config-container" style="display:none;">
                                <div class="form-group">
                                    <label>@lang('employeetracker::lang.options_comma_separated')</label>
                                    <input type="text" name="fields[${fieldIndex}][config]" class="form-control" placeholder="Option 1, Option 2">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>@lang('employeetracker::lang.is_required')</label>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="fields[${fieldIndex}][is_required]" value="1">
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 text-right">
                                <button type="button" class="btn btn-danger btn-xs remove-field-btn">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`;
                    $('#form-fields-container').append(newField);
                    fieldIndex++;
                });

                // Remove field
                $('#form-fields-container').off('click', '.remove-field-btn').on('click', '.remove-field-btn',
                    function() {
                        $(this).closest('.field-group').remove();
                    });
            }
        });
    </script>
@endsection
