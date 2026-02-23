{{-- resources/views/employeetracker/Activity/edit.blade.php --}}
<div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">Edit Department Activity Form</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="form-name">Form Name</label>
                <input type="text" id="form-name" class="form-control"
                    placeholder="e.g., Marketing Department Daily Activities" value="{{ $form->name }}">
            </div>

            <div class="form-group">
                <label for="department_id">Department</label>
                <select id="department_id" class="form-control">
                    <option value="">Select Department</option>
                    @foreach ($departments as $id => $name)
                        <option value="{{ $id }}"
                            {{ $form->department == $id || $form->department == $name ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="form-description">Description</label>
                <textarea id="form-description" class="form-control" rows="3" placeholder="Describe the purpose of this form...">{{ $form->description }}</textarea>
            </div>

            <div class="form-builder">
                <div class="form-fields">
                    <h3>Form Fields</h3>
                    <p>Add fields to your form by clicking on the field types below:</p>

                    <div class="field-types">
                        <div class="field-type" data-type="text"><i class="fas fa-font"></i>
                            <div>Text Input</div>
                        </div>
                        <div class="field-type" data-type="number"><i class="fas fa-hashtag"></i>
                            <div>Number</div>
                        </div>
                        <div class="field-type" data-type="image"><i class="fas fa-image"></i>
                            <div>Image Upload</div>
                        </div>
                        <div class="field-type" data-type="video"><i class="fas fa-video"></i>
                            <div>Video Upload</div>
                        </div>
                        <div class="field-type" data-type="checkbox"><i class="fas fa-check-square"></i>
                            <div>Checkbox</div>
                        </div>
                        <div class="field-type" data-type="textarea"><i class="fas fa-align-left"></i>
                            <div>Text Area</div>
                        </div>
                        <div class="field-type" data-type="select"><i class="fas fa-list-ul"></i>
                            <div>Select</div>
                        </div>
                    </div>
                </div>

                <div class="form-preview">
                    <h3>Form Preview</h3>
                    <div id="preview-container">
                        <div class="empty-preview">
                            <i class="fas fa-eye"></i>
                            <h3>Preview Your Form</h3>
                            <p>Add fields to see the preview here</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="update-form">
                <i class="fas fa-save"></i> Update Form
            </button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        let formFields = [];
        let fieldCounter = 1;

        // Load existing form data from Laravel
        const existingFormData = {
            id: {{ $form->id }},
            name: @json($form->name),
            description: @json($form->description),
            department: @json($form->department),
            fields: [
                @if ($form->fields && $form->fields->count() > 0)
                    @foreach ($form->fields->sortBy('field_order') as $field)
                        {
                            id: 'field-{{ $field->id }}',
                            type: @json($field->field_type),
                            label: @json($field->field_label),
                            required: {{ $field->is_required ? 'true' : 'false' }},
                            options: @json($field->config ? json_decode($field->config, true)['options'] ?? [] : [])
                        }
                        @if (!$loop->last)
                            ,
                        @endif
                    @endforeach
                @else
                    // No fields found - this might indicate a relationship or query issue
                @endif
            ]
        };

        const fieldIcons = {
            text: 'fas fa-font',
            number: 'fas fa-hashtag',
            image: 'fas fa-image',
            video: 'fas fa-video',
            checkbox: 'fas fa-check-square',
            textarea: 'fas fa-align-left',
            select: 'fas fa-list-ul'
        };

        const fieldLabels = {
            text: 'Text Input',
            number: 'Number Input',
            image: 'Image Upload',
            video: 'Video Upload',
            checkbox: 'Checkbox',
            textarea: 'Text Area',
            select: 'Select Dropdown'
        };

        // Load existing form data
        function loadExistingForm() {
            console.log('Loading existing form data:', existingFormData);

            // Set form basic info
            $('#form-name').val(existingFormData.name || '');
            $('#form-description').val(existingFormData.description || '');

            // Set department - handle both ID and name values
            if (existingFormData.department) {
                $('#department_id').val(existingFormData.department);
                // If the value didn't set (maybe it's stored as name), try to find by name
                if ($('#department_id').val() === '') {
                    $('#department_id option').each(function() {
                        if ($(this).text() === existingFormData.department) {
                            $(this).prop('selected', true);
                            return false;
                        }
                    });
                }
            }

            // Load existing fields
            if (existingFormData.fields && existingFormData.fields.length > 0) {
                formFields = [...existingFormData.fields];
                // Update field counter to be higher than existing IDs
                const maxId = Math.max(...formFields.map(f => {
                    const match = f.id.match(/field-(\d+)/);
                    return match ? parseInt(match[1]) : 0;
                }));
                fieldCounter = maxId + 1;
            }

            console.log('Loaded fields:', formFields);
            updateFormPreview();
        }

        // Add new field type click handler
        $('.field-type').off('click').on('click', function() {
            const fieldType = $(this).data('type');
            addFieldToForm(fieldType);
        });

        function addFieldToForm(type) {
            const fieldId = `field-${fieldCounter++}`;
            const fieldLabel = `${fieldLabels[type]} ${formFields.length + 1}`;

            const field = {
                id: fieldId,
                type: type,
                label: fieldLabel,
                required: false
            };

            if (type === 'select') {
                field.options = [];
            }

            formFields.push(field);
            updateFormPreview();
        }

        function updateFormPreview() {
            const previewContainer = $('#preview-container');

            if (formFields.length === 0) {
                previewContainer.html(`
            <div class="empty-preview">
                <i class="fas fa-eye"></i>
                <h3>Preview Your Form</h3>
                <p>Add fields to see the preview here</p>
            </div>
        `);
                return;
            }

            let previewHTML = '';

            formFields.forEach((field, index) => {
                let optionsHTML = '';
                if (field.type === 'select') {
                    const optionsText = field.options && field.options.length > 0 ? field.options.join(
                        ', ') : 'No options set';
                    optionsHTML = `<div class="select-options-container">
                            <small>Options: ${optionsText}</small>
                            <button class="btn btn-xs btn-info edit-options" data-field-id="${field.id}">Edit Options</button>
                        </div>`;
                }

                const requiredBadge = field.required ?
                    '<span class="badge badge-warning">Required</span>' : '';

                // Escape the field label for use in HTML attributes
                const escapedLabel = field.label.replace(/"/g, '&quot;').replace(/'/g, '&#39;');

                previewHTML += `
            <div class="preview-field" data-field-id="${field.id}">
                <div class="field-header">
                    <div class="field-type-icon"><i class="${fieldIcons[field.type]}"></i></div>
                    <input type="text" class="form-control field-label-input" value="${escapedLabel}" data-field-id="${field.id}">
                    <div class="field-controls">
                        <button class="btn btn-xs btn-default move-up" title="Move up"><i class="fas fa-arrow-up"></i></button>
                        <button class="btn btn-xs btn-default move-down" title="Move down"><i class="fas fa-arrow-down"></i></button>
                        <button class="btn btn-xs ${field.required ? 'btn-warning' : 'btn-default'} toggle-required" title="Toggle required"><i class="fas fa-asterisk"></i></button>
                        <button class="btn btn-xs btn-danger delete-field" title="Delete field"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
                <div class="field-meta">
                    <small class="text-muted">${fieldLabels[field.type]} ${requiredBadge}</small>
                    ${optionsHTML}
                </div>
            </div>
        `;
            });

            previewContainer.html(previewHTML);
        }

        // Event handlers for field management - FIXED VERSION
        // Remove existing handlers and add new ones to prevent multiple bindings
        $(document).off('input', '.field-label-input').on('input', '.field-label-input', function(e) {
            e.stopPropagation();
            
            const fieldId = $(this).data('field-id');
            const newLabel = $(this).val();
            const field = formFields.find(f => f.id === fieldId);
            if (field) {
                field.label = newLabel;
            }
        });

        $(document).off('click', '.delete-field').on('click', '.delete-field', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const $button = $(this);
            const fieldId = $button.closest('.preview-field').data('field-id');
            
            // Check if field exists
            const fieldExists = formFields.some(field => field.id === fieldId);
            if (!fieldExists) {
                return;
            }
            
            if (confirm('Are you sure you want to delete this field?')) {
                formFields = formFields.filter(field => field.id !== fieldId);
                updateFormPreview();
            }
        });

        $(document).off('click', '.toggle-required').on('click', '.toggle-required', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const fieldId = $(this).closest('.preview-field').data('field-id');
            const field = formFields.find(f => f.id === fieldId);
            if (field) {
                field.required = !field.required;
                updateFormPreview();
            }
        });

        $(document).off('click', '.move-up').on('click', '.move-up', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const fieldId = $(this).closest('.preview-field').data('field-id');
            const index = formFields.findIndex(field => field.id === fieldId);
            if (index > 0) {
                [formFields[index - 1], formFields[index]] = [formFields[index], formFields[index - 1]];
                updateFormPreview();
            }
        });

        $(document).off('click', '.move-down').on('click', '.move-down', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const fieldId = $(this).closest('.preview-field').data('field-id');
            const index = formFields.findIndex(field => field.id === fieldId);
            if (index < formFields.length - 1) {
                [formFields[index + 1], formFields[index]] = [formFields[index], formFields[index + 1]];
                updateFormPreview();
            }
        });

        $(document).off('click', '.edit-options').on('click', '.edit-options', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const fieldId = $(this).data('field-id');
            const field = formFields.find(f => f.id === fieldId);
            if (field) {
                const currentOptions = field.options || [];
                const optionsStr = prompt("Enter comma-separated options:", currentOptions.join(','));
                if (optionsStr !== null) {
                    field.options = optionsStr.split(',').map(s => s.trim()).filter(s => s);
                    updateFormPreview();
                }
            }
        });

        // Update form handler
        $('#update-form').off('click').on('click', function() {
            const formName = $('#form-name').val();
            const formDescription = $('#form-description').val();
            const department = $('#department_id').val();

            if (!formName) {
                alert('Please enter a form name');
                return;
            }

            if (formFields.length === 0) {
                alert('Please add at least one field to the form');
                return;
            }

            const formData = {
                name: formName,
                description: formDescription,
                department: $('#department_id').val(), // Get the selected value (ID)
                fields: formFields
            };

            $.ajax({
                method: 'PUT',
                url: '{{ action([\Modules\EmployeeTracker\Http\Controllers\ActivityFormController::class, 'update'], [$form->id]) }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    form_data: formData
                },
                success: function(result) {
                    if (result.success) {
                        $('#EmployeeTracker_modal').modal('hide');
                        toastr.success(result.msg);
                        if (typeof table !== 'undefined') {
                            table.ajax.reload();
                        }
                    } else {
                        toastr.error(result.msg);
                    }
                },
                error: function(xhr) {
                    toastr.error('An error occurred while updating the form.');
                    console.log(xhr.responseText);
                }
            });
        });

        // Initialize form with existing data
        console.log('Initializing form with data:', existingFormData);
        loadExistingForm();
    });
</script>

<style>
    .form-builder {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .field-types {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }

    .field-type {
        border: 1px solid #ccc;
        padding: 10px;
        text-align: center;
        cursor: pointer;
        border-radius: 4px;
        transition: all 0.2s;
    }

    .field-type:hover {
        background-color: #f0f0f0;
        border-color: #007bff;
    }

    .preview-field {
        border: 1px solid #ddd;
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 4px;
        background: #fafafa;
    }

    .field-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 5px;
    }

    .field-type-icon {
        font-size: 1.2em;
        color: #666;
        min-width: 20px;
    }

    .field-label-input {
        flex: 1;
    }

    .field-controls {
        display: flex;
        gap: 2px;
    }

    .field-controls .btn {
        padding: 2px 6px;
    }

    .field-meta {
        padding-left: 30px;
    }

    .empty-preview {
        text-align: center;
        padding: 40px 20px;
        border: 2px dashed #ccc;
        border-radius: 8px;
        color: #999;
    }

    .select-options-container {
        margin-top: 5px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .badge {
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 3px;
    }

    .badge-warning {
        background-color: #f0ad4e;
        color: white;
    }

    .btn-xs {
        font-size: 11px;
        padding: 1px 5px;
    }

    @media (max-width: 768px) {
        .form-builder {
            grid-template-columns: 1fr;
        }

        .field-types {
            grid-template-columns: repeat(2, 1fr);
        }

        .field-header {
            flex-direction: column;
            align-items: stretch;
            gap: 5px;
        }

        .field-controls {
            justify-content: center;
        }
    }
