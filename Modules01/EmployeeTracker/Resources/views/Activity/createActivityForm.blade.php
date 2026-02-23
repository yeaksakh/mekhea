<div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Create Department Activity Form</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="form-name">Form Name</label>
                <input type="text" id="form-name" class="form-control" placeholder="e.g., Marketing Department Daily Activities">
            </div>
            
            <div class="form-group">
                <label for="department_id">Department</label>
                <select id="department_id" class="form-control">
                    <option value="">Select Department</option>
                    @foreach ($departments as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="form-description">Description</label>
                <textarea id="form-description" class="form-control" rows="3" placeholder="Describe the purpose of this form..."></textarea>
            </div>
            
            <div class="form-builder">
                <div class="form-fields">
                    <h3>Form Fields</h3>
                    <p>Add fields to your form by clicking on the field types below:</p>
                    
                    <div class="field-types">
                        <div class="field-type" data-type="text"><i class="fas fa-font"></i><div>Text Input</div></div>
                        <div class="field-type" data-type="number"><i class="fas fa-hashtag"></i><div>Number</div></div>
                        <div class="field-type" data-type="image"><i class="fas fa-image"></i><div>Image Upload</div></div>
                        <div class="field-type" data-type="video"><i class="fas fa-video"></i><div>Video Upload</div></div>
                        <div class="field-type" data-type="checkbox"><i class="fas fa-check-square"></i><div>Checkbox</div></div>
                        <div class="field-type" data-type="textarea"><i class="fas fa-align-left"></i><div>Text Area</div></div>
                        <div class="field-type" data-type="select"><i class="fas fa-list-ul"></i><div>Select</div></div>
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
            <button type="button" class="btn btn-primary" id="save-form"><i class="fas fa-save"></i> Save Form</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        let formFields = [];
        let fieldCounter = 1;
        
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

        $('.field-type').on('click', function() {
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
                    optionsHTML = `<div class="select-options-container">
                                    <small>Options: ${field.options.join(', ')}</small>
                                    <button class="btn btn-xs btn-info edit-options" data-field-id="${field.id}">Edit Options</button>
                                </div>`;
                }

                previewHTML += `
                    <div class="preview-field" data-field-id="${field.id}">
                        <div class="field-type-icon"><i class="${fieldIcons[field.type]}"></i></div>
                        <input type="text" class="form-control field-label-input" value="${field.label}" data-field-id="${field.id}">
                        <div class="field-controls">
                            <button class="btn btn-xs btn-default move-up" title="Move up"><i class="fas fa-arrow-up"></i></button>
                            <button class="btn btn-xs btn-default move-down" title="Move down"><i class="fas fa-arrow-down"></i></button>
                            <button class="btn btn-xs btn-danger delete-field" title="Delete field"><i class="fas fa-trash"></i></button>
                        </div>
                        ${optionsHTML}
                    </div>
                `;
            });
            
            previewContainer.html(previewHTML);
        }

        $(document).on('input', '.field-label-input', function() {
            const fieldId = $(this).data('field-id');
            const newLabel = $(this).val();
            const field = formFields.find(f => f.id === fieldId);
            if (field) {
                field.label = newLabel;
            }
        });

        $(document).on('click', '.delete-field', function() {
            const fieldId = $(this).closest('.preview-field').data('field-id');
            formFields = formFields.filter(field => field.id !== fieldId);
            updateFormPreview();
        });

        $(document).on('click', '.move-up', function() {
            const fieldId = $(this).closest('.preview-field').data('field-id');
            const index = formFields.findIndex(field => field.id === fieldId);
            if (index > 0) {
                [formFields[index - 1], formFields[index]] = [formFields[index], formFields[index - 1]];
                updateFormPreview();
            }
        });

        $(document).on('click', '.move-down', function() {
            const fieldId = $(this).closest('.preview-field').data('field-id');
            const index = formFields.findIndex(field => field.id === fieldId);
            if (index < formFields.length - 1) {
                [formFields[index + 1], formFields[index]] = [formFields[index], formFields[index + 1]];
                updateFormPreview();
            }
        });

        $(document).on('click', '.edit-options', function() {
            const fieldId = $(this).data('field-id');
            const field = formFields.find(f => f.id === fieldId);
            if (field) {
                const optionsStr = prompt("Enter comma-separated options:", field.options.join(','));
                if (optionsStr !== null) {
                    field.options = optionsStr.split(',').map(s => s.trim()).filter(s => s);
                    updateFormPreview();
                }
            }
        });
        
        $('#save-form').on('click', function() {
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
                department: department,
                fields: formFields
            };

            $.ajax({
                method: 'POST',
                url: '{{ action([\Modules\EmployeeTracker\Http\Controllers\ActivityFormController::class, 'store']) }}',
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
                error: function() {
                    toastr.error('An error occurred.');
                }
            });
        });
    });
</script>
<style>
.form-builder { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.field-types { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
.field-type { border: 1px solid #ccc; padding: 10px; text-align: center; cursor: pointer; }
.field-type:hover { background-color: #f0f0f0; }
.preview-field { border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; display: flex; flex-wrap: wrap; align-items: center; gap: 10px; }
.field-type-icon { font-size: 1.5em; }
.field-label-input { flex: 1; }
.empty-preview { text-align: center; padding: 20px; border: 1px dashed #ccc; }
.select-options-container { width: 100%; margin-top: 5px; }
</style>
