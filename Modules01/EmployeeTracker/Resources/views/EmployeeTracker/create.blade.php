<div class="modal-dialog" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action([\Modules\EmployeeTracker\Http\Controllers\EmployeeTrackerController::class, 'store']), 'method' => 'post', 'id' => 'add_EmployeeTracker_form', 'files' => true]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('employeetracker::lang.add_EmployeeTracker')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('department_select_for_form', __('employeetracker::lang.department_1') . ':*') !!}
                        {!! Form::select('department_1', $departments, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'required', 'id' => 'department_select_for_form', 'placeholder' => __('lang_v1.select_option')]) !!}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('employee_2', __('employeetracker::lang.employee_2') . ':*') !!}
                        <select name="employee_2" id="employee_2_select" class="form-control select2" style="width:100%;" required>
                            <option value="">@lang('lang_v1.select_option')</option>
                        </select>
                    </div>
                </div>
            </div>
            <div id="dynamic-fields-container-for-form" class="row">
                <!-- Dynamic form fields will be loaded here -->
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
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
        failed_to_load_users: "{{ Js::from(__('employeetracker::lang.failed_to_load_users')) }}",
        failed_to_load_fields: "{{ Js::from(__('employeetracker::lang.failed_to_load_fields')) }}",
        no_fields_configured: "{{ Js::from(__('employeetracker::lang.no_fields_configured')) }}",
        unsupported_field: "{{ Js::from(__('employeetracker::lang.unsupported_field')) }}",
        add_more: "Add More",
        remove: "Remove"
    };

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

    // Enhanced function to get options from a field - handles multiple formats
    function getSafeOptions(field) {
        if (!field || !field.config) {
            return [];
        }
        
        // Handle comma-separated string (most common case)
        if (typeof field.config === 'string') {
            if (field.config.includes(',')) {
                return field.config.split(',').map(opt => opt.trim()).filter(opt => opt);
            } else {
                // Single option
                return [field.config.trim()].filter(opt => opt);
            }
        }
        
        // Handle JSON config
        try {
            var config = (typeof field.config === 'string') ? JSON.parse(field.config) : field.config;
            if (config && typeof config === 'object' && config !== null) {
                if (Array.isArray(config)) {
                    return config;
                } else if (Array.isArray(config.options)) {
                    return config.options;
                }
            }
        } catch (e) {
            console.warn('Could not parse options for field:', field.field_label, e);
        }
        
        return [];
    }

    // Function to create file input with add more functionality
    function createFileInput(fieldId, fieldType, required, index = 0) {
        var accept = fieldType === 'image' ? 'image/*' : (fieldType === 'video' ? 'video/*' : '');
        var acceptAttr = accept ? `accept="${accept}"` : '';
        var fieldName = `dynamic_fields[${fieldId}][]`;
        
        return `
            <div class="file-input-group" data-field-id="${fieldId}" data-index="${index}">
                <div class="input-group" style="margin-bottom: 10px;">
                    <input type="file" name="${fieldName}" class="form-control" ${required && index === 0 ? 'required' : ''} ${acceptAttr}>
                    <span class="input-group-btn">
                        ${index === 0 ? 
                            `<button type="button" class="btn btn-success add-more-file" data-field-id="${fieldId}" data-field-type="${fieldType}">
                                <i class="fa fa-plus"></i> ${lang.add_more}
                            </button>` : 
                            `<button type="button" class="btn btn-danger remove-file">
                                <i class="fa fa-minus"></i> ${lang.remove}
                            </button>`
                        }
                    </span>
                </div>
            </div>
        `;
    }

    // Function to get next available index for file inputs
    function getNextFileIndex(fieldId) {
        return $(`.file-input-group[data-field-id="${fieldId}"]`).length;
    }

    // --- Event Handlers for File Management ---
    $(document).on('click', '.add-more-file', function() {
        var fieldId = $(this).data('field-id');
        var fieldType = $(this).data('field-type');
        var nextIndex = getNextFileIndex(fieldId);
        var newFileInput = createFileInput(fieldId, fieldType, false, nextIndex);
        
        // Find the container for this field and append the new input
        var container = $(this).closest('.form-group').find('.file-inputs-container');
        container.append(newFileInput);
    });

    $(document).on('click', '.remove-file', function() {
        $(this).closest('.file-input-group').remove();
    });

    // --- Main Logic ---
    initializeSelect2('#add_EmployeeTracker_form .select2');

    $('#department_select_for_form').on('change', function() {
        var departmentId = $(this).val();
        var userSelect = $('#employee_2_select');
        var fieldsContainer = $('#dynamic-fields-container-for-form');

        userSelect.empty().append(`<option value="">${lang.select_option}</option>`).trigger('change');
        fieldsContainer.empty();

        if (!departmentId) return;

        // Fetch users
        $.ajax({
            url: '{{ route("getUsersByDepartment") }}',
            type: 'GET',
            data: { department_id: departmentId },
            success: function(users) {
                if (users && typeof users === 'object') {
                    $.each(users, function(id, name) {
                        userSelect.append(new Option(name, id, false, false));
                    });
                }
                userSelect.trigger('change');
            },
            error: function() { if(typeof toastr !== 'undefined') toastr.error(lang.failed_to_load_users); }
        });

        // Fetch and build dynamic form fields
        $.ajax({
            url: '/employeetracker/get-form-fields/' + departmentId,
            type: 'GET',
            beforeSend: function() {
                fieldsContainer.html('<div class="col-sm-12 text-center"><i class="fa fa-spinner fa-spin"></i></div>');
            },
            success: function(response) {
                fieldsContainer.empty();
                
                // Handle both array response and object response
                var fields = Array.isArray(response) ? response : (response.fields || []);
                
                if (fields && Array.isArray(fields) && fields.length > 0) {
                    // Sort fields by field_order if available
                    fields.sort((a, b) => (a.field_order || 0) - (b.field_order || 0));
                    
                    fields.forEach(function(field) {
                        var fieldName = `dynamic_fields[${field.id}]`;
                        var required = field.is_required ? 'required' : '';
                        var fieldHtml = `<div class="form-group col-sm-12">
                            <label>${escapeHtml(field.field_label)}${field.is_required ? ' <span class="text-danger">*</span>' : ''}</label>`;

                        var fieldType = field.field_type;

                        // Handle all the field types
                        switch(fieldType) {
                            case 'text':
                                fieldHtml += `<input type="text" name="${fieldName}" class="form-control" ${required}>`;
                                break;
                                
                            case 'number':
                                fieldHtml += `<input type="number" name="${fieldName}" class="form-control" ${required}>`;
                                break;
                                
                            case 'textarea':
                                fieldHtml += `<textarea name="${fieldName}" class="form-control" rows="3" ${required}></textarea>`;
                                break;
                                
                            case 'image':
                            case 'video':
                            case 'file':
                                fieldHtml += `<div class="file-inputs-container">`;
                                fieldHtml += createFileInput(field.id, fieldType, field.is_required, 0);
                                fieldHtml += `</div>`;
                                break;
                                
                            case 'checkbox':
                                fieldHtml += `<div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="${fieldName}" value="1" ${required}> 
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
                                        fieldHtml += `<option value="${escapeHtml(opt)}">${escapeHtml(opt)}</option>`;
                                    });
                                    fieldHtml += '</select>';
                                }
                                break;
                                
                            // Handle other common field types
                            case 'email':
                                fieldHtml += `<input type="email" name="${fieldName}" class="form-control" ${required}>`;
                                break;
                                
                            case 'date':
                                fieldHtml += `<input type="date" name="${fieldName}" class="form-control" ${required}>`;
                                break;
                                
                            case 'time':
                                fieldHtml += `<input type="time" name="${fieldName}" class="form-control" ${required}>`;
                                break;
                                
                            default:
                                // Fallback for unsupported field types
                                fieldHtml += `<input type="text" class="form-control" value="${lang.unsupported_field}: ${escapeHtml(fieldType)}" readonly>`;
                        }
                        
                        fieldHtml += '</div>';
                        fieldsContainer.append(fieldHtml);
                    });
                    
                    // Initialize Select2 for any select fields
                    initializeSelect2('.dynamic-select2');
                } else {
                    fieldsContainer.html(`<div class="col-sm-12"><p class="text-center text-muted">${lang.no_fields_configured}</p></div>`);
                }
            },
            error: function() {
                fieldsContainer.html(`<div class="col-sm-12"><p class="text-center text-danger">${lang.failed_to_load_fields}</p></div>`);
            }
        });
    });

    // --- Form Submission ---
    $(document).on('submit', 'form#add_EmployeeTracker_form', function(e) {
        e.preventDefault();
        var form = $(this);
        var data = new FormData(this);
        var url = form.attr('action');
        var submitButton = form.find('button[type="submit"]');
        var originalButtonText = submitButton.html();

        // Clear previous errors
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
                submitButton.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> ' + "{{ Js::from(__('messages.saving')) }}");
            },
            success: function(result) {
                submitButton.prop('disabled', false).html(originalButtonText);
                
                if (result.success) {
                    $('div.modal').modal('hide');
                    if(typeof toastr !== 'undefined') toastr.success(result.msg);
                    
                    // Assuming a datatable with the ID 'employee_tracker_table' exists on the parent page
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
                        // Convert dot notation (e.g., "dynamic_fields.1") to bracket notation (e.g., "dynamic_fields[1]")
                        var elementName = key.replace(/\.(\d+)/, '[$1]');
                        var element = form.find(`[name="${elementName}"]`);
                        
                        if (element.length > 0) {
                            element.closest('.form-group').addClass('has-error');
                            var container = element.parent();
                            // Adjust container for select2 elements
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