/**
 * Multiselect Dropdown Component
 * Reusable dropdown with checkboxes, search, and filtering
 * 
 * Usage:
 * new MultiselectDropdown({
 *     containerId: 'my-dropdown',
 *     placeholder: 'Select items...',
 *     searchPlaceholder: 'Search items...',
 *     onSelectionChange: function(selectedValues) {
 *         console.log('Selected:', selectedValues);
 *     }
 * });
 */

class MultiselectDropdown {
    constructor(options) {
        this.options = {
            containerId: '',
            placeholder: 'Select items...',
            searchPlaceholder: 'Search items...',
            selectAllText: 'Select All',
            clearAllText: 'Clear All',
            noResultsText: 'No items found',
            onSelectionChange: null,
            debounceDelay: 150,
            maxDisplayItems: 3,
            ...options
        };

        this.container = null;
        this.isLoading = false;
        this.selectedValues = [];

        this.init();
    }

    init() {
        this.container = document.getElementById(this.options.containerId);
        if (!this.container) {
            console.error('Multiselect container not found:', this.options.containerId);
            return;
        }

        this.bindEvents();
        this.updateDisplay();
        this.initializeSearch();
    }

    // Debounce function for performance
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    updateDisplay() {
        const checkedBoxes = this.container.querySelectorAll('.multiselect-checkbox:checked');
        const count = checkedBoxes.length;
        const displayElement = this.container.querySelector('.multiselect-display');
        let display = '';

        if (count === 0) {
            display = this.options.placeholder;
            displayElement.classList.add('placeholder');
        } else {
            displayElement.classList.remove('placeholder');
            if (count === 1) {
                display = checkedBoxes[0].nextElementSibling.textContent;
            } else if (count <= this.options.maxDisplayItems) {
                const names = Array.from(checkedBoxes).map(cb => cb.nextElementSibling.textContent);
                display = names.join(', ');
            } else {
                display = `${count} items selected`;
            }
        }

        displayElement.textContent = display;
        
        const countElement = this.container.querySelector('.multiselect-selected-count');
        if (countElement) {
            countElement.textContent = `${count} selected`;
        }

        // Update toggle state
        const toggle = this.container.querySelector('.multiselect-dropdown-toggle');
        toggle.classList.toggle('active', count > 0);

        // Update selected values array
        this.selectedValues = Array.from(checkedBoxes).map(cb => cb.value);

        // Trigger callback
        if (this.options.onSelectionChange && typeof this.options.onSelectionChange === 'function') {
            this.options.onSelectionChange(this.selectedValues);
        }
    }

    toggleDropdown(show) {
        const menu = this.container.querySelector('.multiselect-dropdown-menu');
        const toggle = this.container.querySelector('.multiselect-dropdown-toggle');
        const arrow = this.container.querySelector('.multiselect-dropdown-arrow');

        if (show === undefined) {
            show = !menu.classList.contains('show');
        }

        if (show) {
            menu.classList.add('show');
            arrow.classList.add('up');
            toggle.classList.add('active');
            
            // Focus search input after a small delay
            setTimeout(() => {
                const searchInput = this.container.querySelector('.multiselect-search input');
                if (searchInput) {
                    searchInput.focus();
                }
            }, 100);
        } else {
            menu.classList.remove('show');
            arrow.classList.remove('up');
            if (this.selectedValues.length === 0) {
                toggle.classList.remove('active');
            }
            
            // Clear search and show all options
            const searchInput = this.container.querySelector('.multiselect-search input');
            if (searchInput) {
                searchInput.value = '';
                this.filterOptions('');
            }
        }
    }

    filterOptions(searchTerm) {
        const options = this.container.querySelectorAll('.multiselect-option');
        const menuContent = this.container.querySelector('.multiselect-menu-content');
        let visibleCount = 0;

        // Remove any existing no-results message
        const existingNoResults = this.container.querySelector('.multiselect-no-results');
        if (existingNoResults) {
            existingNoResults.remove();
        }

        if (!searchTerm || searchTerm.trim() === '') {
            // Show all options if search is empty
            options.forEach(option => {
                option.style.display = 'flex';
            });
            return;
        }

        const searchLower = searchTerm.toLowerCase().trim();

        options.forEach(option => {
            const dataName = option.dataset.itemName || '';
            const labelText = option.querySelector('label').textContent.toLowerCase();

            // Check both data attribute and label text
            const isVisible = dataName.includes(searchLower) || labelText.includes(searchLower);

            if (isVisible) {
                option.style.display = 'flex';
                visibleCount++;
            } else {
                option.style.display = 'none';
            }
        });

        // Show no results message if no matches found
        if (visibleCount === 0) {
            const noResultsDiv = document.createElement('div');
            noResultsDiv.className = 'multiselect-no-results';
            noResultsDiv.textContent = `${this.options.noResultsText} matching "${searchTerm}"`;
            menuContent.appendChild(noResultsDiv);
        }
    }

    selectAll() {
        const visibleCheckboxes = this.container.querySelectorAll('.multiselect-option:not([style*="display: none"]) .multiselect-checkbox');
        visibleCheckboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
        this.updateDisplay();
    }

    clearAll() {
        const checkboxes = this.container.querySelectorAll('.multiselect-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        this.updateDisplay();
    }

    setLoading(loading) {
        this.isLoading = loading;
        const toggle = this.container.querySelector('.multiselect-dropdown-toggle');
        toggle.classList.toggle('loading', loading);
    }

    getSelectedValues() {
        return this.selectedValues;
    }

    setSelectedValues(values) {
        const checkboxes = this.container.querySelectorAll('.multiselect-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = values.includes(checkbox.value);
        });
        this.updateDisplay();
    }

    initializeSearch() {
        this.filterOptions('');
    }

    bindEvents() {
        const container = this.container;

        // Toggle dropdown
        const toggle = container.querySelector('.multiselect-dropdown-toggle');
        if (toggle) {
            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.toggleDropdown();
            });

            toggle.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    e.stopPropagation();
                    this.toggleDropdown();
                }
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!container.contains(e.target)) {
                this.toggleDropdown(false);
            }
        });

        // Prevent dropdown from closing when clicking inside menu
        const menu = container.querySelector('.multiselect-dropdown-menu');
        if (menu) {
            menu.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        }

        // Search functionality
        const searchInput = container.querySelector('.multiselect-search input');
        if (searchInput) {
            const debouncedFilter = this.debounce((searchTerm) => {
                this.filterOptions(searchTerm);
            }, this.options.debounceDelay);

            searchInput.addEventListener('input', (e) => {
                const searchTerm = e.target.value;
                debouncedFilter(searchTerm);
            });

            searchInput.addEventListener('keyup', (e) => {
                const searchTerm = e.target.value;
                debouncedFilter(searchTerm);
            });

            // Prevent search input from closing dropdown
            searchInput.addEventListener('click', (e) => {
                e.stopPropagation();
            });

            searchInput.addEventListener('focus', (e) => {
                e.stopPropagation();
            });
        }

        // Checkbox change events
        container.addEventListener('change', (e) => {
            if (e.target.classList.contains('multiselect-checkbox')) {
                this.updateDisplay();
            }
        });

        // Select All button
        const selectAllBtn = container.querySelector('.multiselect-select-all-btn');
        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.selectAll();
            });
        }

        // Clear All button
        const clearAllBtn = container.querySelector('.multiselect-clear-all-btn');
        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.clearAll();
            });
        }

        // Keyboard navigation
        container.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.toggleDropdown(false);
                toggle.focus();
            }
        });
    }

    // Public method to destroy the component
    destroy() {
        // Remove event listeners and clean up
        this.container = null;
        this.selectedValues = [];
    }
}

// Export for use in modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = MultiselectDropdown;
}

// Global assignment for direct script inclusion
if (typeof window !== 'undefined') {
    window.MultiselectDropdown = MultiselectDropdown;
} 