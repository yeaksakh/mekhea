/**
 * Auto-initialization script for Multiselect Dropdown components
 * This script automatically initializes all multiselect dropdowns on the page
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all multiselect components
    initializeMultiselectDropdowns();
});

function initializeMultiselectDropdowns() {
    if (typeof MultiselectDropdown === 'undefined') {
        console.warn('MultiselectDropdown class not found. Make sure to include multiselect-dropdown.js');
        return;
    }

    // Find all multiselect configuration scripts
    const configScripts = document.querySelectorAll('script.multiselect-config');
    
    configScripts.forEach(script => {
        try {
            const config = JSON.parse(script.textContent);
            const componentId = script.dataset.componentId;
            
            if (!componentId) {
                console.warn('Multiselect component ID not found');
                return;
            }

            // Create instance with enhanced configuration
            const instance = new MultiselectDropdown({
                ...config,
                onSelectionChange: function(selectedValues) {
                    // Trigger custom event for external handling
                    const event = new CustomEvent('multiselectChange', {
                        detail: {
                            componentId: componentId,
                            selectedValues: selectedValues,
                            instance: this
                        }
                    });
                    document.dispatchEvent(event);
                    
                    // Handle required field validation
                    if (config.required) {
                        const requiredField = document.querySelector(`input[name="${componentId}_required"]`);
                        if (requiredField) {
                            requiredField.value = selectedValues.length > 0 ? 'valid' : '';
                            requiredField.setCustomValidity(selectedValues.length > 0 ? '' : 'Please select at least one item');
                        }
                    }
                }
            });

            // Store instance globally for external access
            window[`${componentId}_instance`] = instance;
            
        } catch (error) {
            console.error('Error initializing multiselect dropdown:', error);
        }
    });
}

// Helper function to get multiselect instance by ID
function getMultiselectInstance(componentId) {
    return window[`${componentId}_instance`] || null;
}

// Helper function to get selected values from a multiselect
function getMultiselectValues(componentId) {
    const instance = getMultiselectInstance(componentId);
    return instance ? instance.getSelectedValues() : [];
}

// Helper function to set selected values for a multiselect
function setMultiselectValues(componentId, values) {
    const instance = getMultiselectInstance(componentId);
    if (instance) {
        instance.setSelectedValues(values);
    }
}

// Make helper functions globally available
window.getMultiselectInstance = getMultiselectInstance;
window.getMultiselectValues = getMultiselectValues;
window.setMultiselectValues = setMultiselectValues; 