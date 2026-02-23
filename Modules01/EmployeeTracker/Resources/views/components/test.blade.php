<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Activity Tracker - Form Builder</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #ffffff;
            color: #000000;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #000;
        }
        
        header {
            border: 1px solid #000;
            color: #000;
            padding: 20px 0;
            margin-bottom: 30px;
            background-color: #ffffff;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo i {
            font-size: 2rem;
            border: 1px solid #000;
            padding: 10px;
        }
        
        .logo h1 {
            font-size: 1.8rem;
            font-weight: 600;
            border: 1px solid #000;
            padding: 10px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-avatar {
            width: 45px;
            height: 45px;
            border: 1px solid #000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .card {
            background: white;
            border: 1px solid #000;
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #000;
        }
        
        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #000;
            border: 1px solid #000;
            padding: 10px;
        }
        
        .btn {
            padding: 10px 20px;
            border: 1px solid #000;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: #ffffff;
            color: #000;
        }
        
        .btn:hover {
            background-color: #f0f0f0;
        }
        
        .form-group {
            margin-bottom: 20px;
            border: 1px solid #000;
            padding: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            border: 1px solid #000;
            padding: 5px;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #000;
            font-size: 1rem;
        }
        
        input:focus, select:focus, textarea:focus {
            outline: 1px solid #000;
            border: 1px solid #000;
        }
        
        .form-builder {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        .form-fields {
            border: 1px solid #000;
            padding: 20px;
        }
        
        .form-fields h3 {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #000;
        }
        
        .field-types {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 20px;
        }
        
        .field-type {
            background: white;
            border: 1px solid #000;
            padding: 15px;
            text-align: center;
            cursor: pointer;
        }
        
        .field-type:hover {
            background-color: #f0f0f0;
        }
        
        .field-type i {
            font-size: 1.8rem;
            margin-bottom: 10px;
            border: 1px solid #000;
            padding: 5px;
        }
        
        .form-preview {
            border: 1px solid #000;
            padding: 20px;
            background-color: #ffffff;
        }
        
        .preview-field {
            margin-bottom: 20px;
            padding: 15px;
            background: white;
            border: 1px solid #000;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .preview-field:last-child {
            margin-bottom: 0;
        }
        
        .field-type-icon {
            width: 30px;
            text-align: center;
            border: 1px solid #000;
            padding: 5px;
        }
        
        .field-label-input {
            flex: 1;
            font-weight: 500;
            border: 1px solid #000;
            padding: 5px;
        }
        
        .field-label-input:focus {
            outline: 1px solid #000;
            border: 1px solid #000;
        }
        
        .field-controls {
            display: flex;
            gap: 10px;
        }
        
        .field-control {
            background: none;
            border: 1px solid #000;
            color: #000;
            cursor: pointer;
            font-size: 1.1rem;
            padding: 5px;
        }
        
        .field-control:hover {
            background-color: #f0f0f0;
        }
        
        .empty-preview {
            text-align: center;
            padding: 40px 20px;
            border: 1px solid #000;
        }
        
        .empty-preview i {
            font-size: 3rem;
            margin-bottom: 15px;
            border: 1px solid #000;
            padding: 10px;
        }
        
        .instructions {
            border: 1px solid #000;
            padding: 15px;
            margin-top: 20px;
            font-size: 0.9rem;
        }
        
        @media (max-width: 992px) {
            .form-builder {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <i class="fas fa-clipboard-list"></i>
                    <h1>Staff Activity Tracker</h1>
                </div>
                <div class="user-info">
                    <div class="user-avatar">JD</div>
                    <div>
                        <div>John Doe</div>
                        <div style="font-size: 0.9rem; border: 1px solid #000; padding: 5px;">Marketing Manager</div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Create Department Activity Form</h2>
                <button class="btn btn-primary" id="save-form">
                    <i class="fas fa-save"></i> Save Form
                </button>
            </div>
            
            <div class="form-group">
                <label for="form-name">Form Name</label>
                <input type="text" id="form-name" placeholder="e.g., Marketing Department Daily Activities">
            </div>
            
            <div class="form-group">
                <label for="form-description">Description</label>
                <textarea id="form-description" rows="3" placeholder="Describe the purpose of this form..."></textarea>
            </div>
            
            <div class="form-builder">
                <div class="form-fields">
                    <h3>Form Fields</h3>
                    <p>Add fields to your form by clicking on the field types below:</p>
                    
                    <div class="field-types">
                        <div class="field-type" data-type="text">
                            <i class="fas fa-font"></i>
                            <div>Text Input</div>
                        </div>
                        <div class="field-type" data-type="number">
                            <i class="fas fa-hashtag"></i>
                            <div>Number</div>
                        </div>
                        <div class="field-type" data-type="image">
                            <i class="fas fa-image"></i>
                            <div>Image Upload</div>
                        </div>
                        <div class="field-type" data-type="video">
                            <i class="fas fa-video"></i>
                            <div>Video Upload</div>
                        </div>
                        <div class="field-type" data-type="checkbox">
                            <i class="fas fa-check-square"></i>
                            <div>Checkbox</div>
                        </div>
                        <div class="field-type" data-type="textarea">
                            <i class="fas fa-align-left"></i>
                            <div>Text Area</div>
                        </div>
                    </div>
                    
                    <div class="instructions">
                        <p><strong>Instructions:</strong></p>
                        <ul>
                            <li>Click on field types to add them to your form</li>
                            <li>Edit field titles by clicking on the text</li>
                            <li>Reorder fields using the arrow buttons</li>
                            <li>Delete fields using the trash icon</li>
                        </ul>
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
    </div>

    <script>
        // Form fields array
        let formFields = [];
        let fieldCounter = 1;
        
        // Field type icons
        const fieldIcons = {
            text: 'fas fa-font',
            number: 'fas fa-hashtag',
            image: 'fas fa-image',
            video: 'fas fa-video',
            checkbox: 'fas fa-check-square',
            textarea: 'fas fa-align-left'
        };
        
        // Field type labels
        const fieldLabels = {
            text: 'Text Input',
            number: 'Number Input',
            image: 'Image Upload',
            video: 'Video Upload',
            checkbox: 'Checkbox',
            textarea: 'Text Area'
        };
        
        // Add field to form
        document.querySelectorAll('.field-type').forEach(type => {
            type.addEventListener('click', function() {
                const fieldType = this.getAttribute('data-type');
                addFieldToForm(fieldType);
            });
        });
        
        // Function to add field to form
        function addFieldToForm(type) {
            const fieldId = `field-${fieldCounter++}`;
            const fieldLabel = `${fieldLabels[type]} ${formFields.length + 1}`;
            
            // Add to form fields array
            formFields.push({
                id: fieldId,
                type: type,
                label: fieldLabel,
                required: false
            });
            
            // Update preview
            updateFormPreview();
        }
        
        // Function to update form preview
        function updateFormPreview() {
            const previewContainer = document.getElementById('preview-container');
            
            if (formFields.length === 0) {
                previewContainer.innerHTML = `
                    <div class="empty-preview">
                        <i class="fas fa-eye"></i>
                        <h3>Preview Your Form</h3>
                        <p>Add fields to see the preview here</p>
                    </div>
                `;
                return;
            }
            
            let previewHTML = '';
            
            formFields.forEach((field, index) => {
                previewHTML += `
                    <div class="preview-field" data-field-id="${field.id}">
                        <div class="field-type-icon">
                            <i class="${fieldIcons[field.type]}"></i>
                        </div>
                        <input type="text" class="field-label-input" value="${field.label}" data-field-id="${field.id}">
                        <div class="field-controls">
                            <button class="field-control move-up" title="Move up"><i class="fas fa-arrow-up"></i></button>
                            <button class="field-control move-down" title="Move down"><i class="fas fa-arrow-down"></i></button>
                            <button class="field-control delete-field" title="Delete field"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                `;
            });
            
            previewContainer.innerHTML = previewHTML;
            
            // Add event listeners to label inputs
            document.querySelectorAll('.field-label-input').forEach(input => {
                input.addEventListener('input', function() {
                    const fieldId = this.getAttribute('data-field-id');
                    const newLabel = this.value;
                    updateFieldLabel(fieldId, newLabel);
                });
            });
            
            // Add event listeners to delete buttons
            document.querySelectorAll('.delete-field').forEach(button => {
                button.addEventListener('click', function() {
                    const fieldId = this.closest('.preview-field').getAttribute('data-field-id');
                    deleteField(fieldId);
                });
            });
            
            // Add event listeners to move buttons
            document.querySelectorAll('.move-up').forEach(button => {
                button.addEventListener('click', function() {
                    const fieldId = this.closest('.preview-field').getAttribute('data-field-id');
                    moveFieldUp(fieldId);
                });
            });
            
            document.querySelectorAll('.move-down').forEach(button => {
                button.addEventListener('click', function() {
                    const fieldId = this.closest('.preview-field').getAttribute('data-field-id');
                    moveFieldDown(fieldId);
                });
            });
        }
        
        // Function to update field label
        function updateFieldLabel(fieldId, newLabel) {
            const field = formFields.find(f => f.id === fieldId);
            if (field) {
                field.label = newLabel;
            }
        }
        
        // Function to delete a field
        function deleteField(fieldId) {
            formFields = formFields.filter(field => field.id !== fieldId);
            updateFormPreview();
        }
        
        // Function to move field up
        function moveFieldUp(fieldId) {
            const index = formFields.findIndex(field => field.id === fieldId);
            if (index > 0) {
                [formFields[index - 1], formFields[index]] = [formFields[index], formFields[index - 1]];
                updateFormPreview();
            }
        }
        
        // Function to move field down
        function moveFieldDown(fieldId) {
            const index = formFields.findIndex(field => field.id === fieldId);
            if (index < formFields.length - 1) {
                [formFields[index + 1], formFields[index]] = [formFields[index], formFields[index + 1]];
                updateFormPreview();
            }
        }
        
        // Save form
        document.getElementById('save-form').addEventListener('click', function() {
            const formName = document.getElementById('form-name').value;
            const formDescription = document.getElementById('form-description').value;
            
            if (!formName) {
                alert('Please enter a form name');
                return;
            }
            
            if (formFields.length === 0) {
                alert('Please add at least one field to the form');
                return;
            }
            
            // In a real app, this would save to a database
            const fieldSummary = formFields.map(f => f.label).join(', ');
            alert(`Form "${formName}" saved successfully!\n\nFields: ${fieldSummary}`);
            
            // Reset form
            document.getElementById('form-name').value = '';
            document.getElementById('form-description').value = '';
            formFields = [];
            fieldCounter = 1;
            updateFormPreview();
        });
    </script>
</body>
</html>