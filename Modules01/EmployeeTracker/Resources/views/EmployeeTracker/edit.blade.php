<div class="modal-dialog" role="document">
    <div class="modal-content">
        {!! Form::open([
            'url' => action([\Modules\EmployeeTracker\Http\Controllers\EmployeeTrackerController::class, 'update'], $employee->id),
            'method' => 'put',
            'id' => 'edit_EmployeeTracker_form',
            'files' => true
        ]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">
                @lang('employeetracker::lang.edit_employee_tracker'): 
                {{ $employee->first_name }} {{ $employee->last_name }}
            </h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        {!! Form::label('department_select_for_form', __('employeetracker::lang.department_1') . ':*') !!}
                        {!! Form::select(
                            'department_1',
                            $departments,
                            $current_department_id, // Pre-select department
                            [
                                'class' => 'form-control select2',
                                'style' => 'width:100%',
                                'required',
                                'id' => 'department_select_for_form',
                                'placeholder' => __('lang_v1.select_option')
                            ]
                        ) !!}
                    </div>
                </div>
            </div>

            <div id="dynamic-fields-container-for-form" class="row">
                <!-- Dynamic fields will be loaded here -->
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang('messages.update')</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>

<script>
$(document).ready(function() {
    // --- Translations ---
    var lang = {
        select_option: "{{ Js::from(__('lang_v1.select_option')) }}",
        failed_to_load_fields: "{{ Js::from(__('employeetracker::lang.failed_to_load_fields')) }}",
        no_fields_configured: "{{ Js::from(__('employeetracker::lang.no_fields_configured')) }}",
        unsupported_field: "{{ Js::from(__('employeetracker::lang.unsupported_field')) }}"
    };

    // --- Existing Data (from backend) ---
    var existingValues = @json($activities->mapWithKeys(function ($activity) {
        return [$activity->field_id => $activity->value];
    })->toArray());

    // --- Helper Functions ---
    function escapeHtml(text) {
        if (text === null || typeof text === 'undefined') return '';
        return String(text).replace(/[&<>"']/g, function(m) { 
            return {'&': '&amp;','<': '<','>': '>','"': '&quot;',"'": '&#039;'}[m];
        });
    }

    function initializeSelect2(selector) {
        try {
            if ($(selector).hasClass('select2-hidden-accessible')) {
                $(selector).select2('destroy');
            }
            $(selector).select2({ width: '100%' });
        } catch (e) {
            console.error("Error initializing select2:", e);
        }
    }

    function getSafeOptions(field) {
        if (!field || !field.config) return [];
        
        if (typeof field.config === 'string') {
            if (field.config.includes(',')) {
                return field.config.split(',').map(opt => opt.trim()).filter(opt => opt);
            } else {
                return [field.config.trim()].filter(opt => opt);
            }
        }
        
        try {
            var config = (typeof field.config === 'string') ? JSON.parse(field.config) : field.config;
            if (Array.isArray(config)) return config;
            if (Array.isArray(config.options)) return config.options;
        } catch (e) {
            console.warn('Could not parse options for field:', field.field_label, e);
        }
        return [];
    }

    // --- Load Dynamic Fields ---
    function loadDynamicFields(departmentId) {
        var fieldsContainer = $('#dynamic-fields-container-for-form');
        fieldsContainer.empty();

        if (!departmentId) return;

        $.ajax({
            url: '/employeetracker/get-form-fields/' + departmentId,
            type: 'GET',
            beforeSend: function() {
                fieldsContainer.html('<div class="col-sm-12 text-center"><i class="fa fa-spinner fa-spin"></i></div>');
            },
            success: function(response) {
                fieldsContainer.empty();
                
                var fields = Array.isArray(response) ? response : (response.fields || []);
                
                if (fields && fields.length > 0) {
                    fields.sort((a, b) => (a.field_order || 0) - (b.field_order || 0));
                    
                    fields.forEach(function(field) {
                        var fieldName = `dynamic_fields[${field.id}]`;
                        var required = field.is_required ? 'required' : '';
                        var currentValue = existingValues[field.id] || ''; // Pull saved value

                        var fieldHtml = `<div class="form-group col-sm-12">
                            <label>${escapeHtml(field.field_label)}${field.is_required ? ' <span class="text-danger">*</span>' : ''}</label>`;

                        var fieldType = field.field_type;

                        switch(fieldType) {
                            case 'text':
                                fieldHtml += `<input type="text" name="${fieldName}" class="form-control" ${required} value="${escapeHtml(currentValue)}">`;
                                break;
                                
                            case 'number':
                                fieldHtml += `<input type="number" name="${fieldName}" class="form-control" ${required} value="${escapeHtml(currentValue)}">`;
                                break;
                                
                            case 'textarea':
                                fieldHtml += `<textarea name="${fieldName}" class="form-control" rows="3" ${required}>${escapeHtml(currentValue)}</textarea>`;
                                break;
                                
                            case 'image':
                                fieldHtml += `<input type="file" name="${fieldName}" class="form-control" ${required} accept="image/*">`;
                                if (currentValue) {
                                    fieldHtml += `<p class="help-block">Current: <a href="${currentValue}" target="_blank">View Image</a></p>`;
                                }
                                break;
                                
                            case 'video':
                                fieldHtml += `<input type="file" name="${fieldName}" class="form-control" ${required} accept="video/*">`;
                                if (currentValue) {
                                    fieldHtml += `<p class="help-block">Current: <a href="${currentValue}" target="_blank">View Video</a></p>`;
                                }
                                break;
                                
                            case 'checkbox':
                                var checked = currentValue === '1' || currentValue === 1 ? 'checked' : '';
                                fieldHtml += `<div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="${fieldName}" value="1" ${required} ${checked}> 
                                        Check if applicable
                                    </label>
                                </div>`;
                                break;
                                
                            case 'select':
                                var options = getSafeOptions(field);
                                if (options.length === 0) {
                                    fieldHtml += '<p class="text-warning"><small>No options configured.</small></p>';
                                } else {
                                    fieldHtml += `<select name="${fieldName}" class="form-control dynamic-select2" ${required} style="width:100%;">
                                        <option value="">${lang.select_option}</option>`;
                                    options.forEach(opt => {
                                        var selected = (currentValue == opt) ? 'selected' : ''; // Loose comparison
                                        fieldHtml += `<option value="${escapeHtml(opt)}" ${selected}>${escapeHtml(opt)}</option>`;
                                    });
                                    fieldHtml += '</select>';
                                }
                                break;
                                
                            case 'email':
                                fieldHtml += `<input type="email" name="${fieldName}" class="form-control" ${required} value="${escapeHtml(currentValue)}">`;
                                break;
                                
                            case 'date':
                                fieldHtml += `<input type="date" name="${fieldName}" class="form-control" ${required} value="${escapeHtml(currentValue)}">`;
                                break;
                                
                            case 'time':
                                fieldHtml += `<input type="time" name="${fieldName}" class="form-control" ${required} value="${escapeHtml(currentValue)}">`;
                                break;
                                
                            case 'file':
                                fieldHtml += `<input type="file" name="${fieldName}" class="form-control" ${required}>`;
                                if (currentValue) {
                                    fieldHtml += `<p class="help-block">Current: <a href="${currentValue}" target="_blank">Download File</a></p>`;
                                }
                                break;
                                
                            default:
                                fieldHtml += `<input type="text" class="form-control" value="${lang.unsupported_field}: ${escapeHtml(fieldType)}" readonly>`;
                        }
                        
                        fieldHtml += '</div>';
                        fieldsContainer.append(fieldHtml);
                    });
                    
                    initializeSelect2('.dynamic-select2');
                } else {
                    fieldsContainer.html(`<div class="col-sm-12"><p class="text-center text-muted">${lang.no_fields_configured}</p></div>`);
                }
            },
            error: function() {
                fieldsContainer.html(`<div class="col-sm-12"><p class="text-center text-danger">${lang.failed_to_load_fields}</p></div>`);
            }
        });
    }

    // --- Initialize Select2 ---
    initializeSelect2('#edit_EmployeeTracker_form .select2');

    // --- Load fields on page load if department is already selected ---
    var initialDepartment = $('#department_select_for_form').val();
    if (initialDepartment) {
        loadDynamicFields(initialDepartment);
    }

    // --- Load fields when department changes ---
    $('#department_select_for_form').on('change', function() {
        loadDynamicFields($(this).val());
    });

    // --- Form Submission ---
    $(document).on('submit', 'form#edit_EmployeeTracker_form', function(e) {
        e.preventDefault();
        var form = $(this);
        var data = new FormData(this);
        var url = form.attr('action');
        var submitButton = form.find('button[type="submit"]');
        var originalButtonText = submitButton.html();

        form.find('span.text-danger').remove();
        form.find('.has-error').removeClass('has-error');

        $.ajax({
            method: 'POST',
            url: url,
            data: data,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function() {
                submitButton.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> ' + "{{ Js::from(__('messages.updating')) }}");
            },
            success: function(result) {
                submitButton.prop('disabled', false).html(originalButtonText);
                
                if (result.success) {
                    $('div.modal').modal('hide');
                    if(typeof toastr !== 'undefined') toastr.success(result.msg);
                    
                    if (typeof employee_tracker_table !== 'undefined') {
                        employee_tracker_table.ajax.reload();
                    } else {
                        window.location.reload();
                    }
                } else {
                    if(typeof toastr !== 'undefined') toastr.error(result.msg);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                submitButton.prop('disabled', false).html(originalButtonText);

                if (jqXHR.status === 422) {
                    var errors = jqXHR.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        var elementName = key.replace(/\.(\d+)/, '[$1]');
                        var element = form.find(`[name="${elementName}"]`);
                        
                        if (element.length > 0) {
                            element.closest('.form-group').addClass('has-error');
                            var container = element.parent();
                            if (element.hasClass('select2-hidden-accessible')) {
                                container = element.next('.select2-container').parent();
                            }
                            container.append('<span class="text-danger">' + value[0] + '</span>');
                        }
                    });
                    if(typeof toastr !== 'undefined') toastr.error("{{ Js::from(__('lang_v1.something_went_wrong')) }}");
                } else {
                    if(typeof toastr !== 'undefined') toastr.error('Error: ' + errorThrown);
                }
            }
        });
    });
});
</script>