/**
 * Reusable Print System for DataTables Reports
 * Works with recipe-table-combined.css for consistent styling
 * Usage: Include this file and call window.ReusablePrint.init(options)
 */

window.ReusablePrint = (function() {
    'use strict';

    // Default configuration
    const defaultConfig = {
        tableSelector: '#recipe-table',
        reportName: 'Report',
        printBy: 'System User',
        businessInfo: {},
        dateRange: null
    };

    let config = {};

    /**
     * Initialize the print system
     * @param {Object} options - Configuration options
     */
    function init(options = {}) {
        config = Object.assign({}, defaultConfig, options);
        
        // Make printReport function globally available
        window.printReport = printReport;
        
        // Only override DataTables print functionality if explicitly requested
        if (options.overrideDataTables !== false) {
            // overrideDataTablesPrint(); // Commented out to allow default DataTables print
        }
        
        console.log('ReusablePrint initialized with config:', config);
    }

    /**
     * Override DataTables default print functionality
     */
    function overrideDataTablesPrint() {
        // Override DataTables print button functionality
        $(document).on('click', '.dt-button.buttons-print, .dt-print', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('DataTables print button intercepted - using custom print system');
            
            // Use our custom print system instead
            printReport(e);
            
            return false;
        });

        // Override any other print buttons that might exist
        $(document).on('click', '[data-action="print"], .print-btn, .btn-print', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Generic print button intercepted - using custom print system');
            
            // Use our custom print system instead
            printReport(e);
            
            return false;
        });

        // Also override if DataTables is initialized after our script
        if (typeof $.fn.dataTable !== 'undefined') {
            // Hook into DataTables button creation
            const originalButton = $.fn.dataTable.ext.buttons.print;
            if (originalButton) {
                $.fn.dataTable.ext.buttons.print.action = function(e, dt, button, config) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    console.log('DataTables print action intercepted - using custom print system');
                    
                    // Use our custom print system
                    printReport(e);
                    
                    return false;
                };
            }
        }
    }

    /**
     * Main print function that shows orientation selection dialog
     * @param {Event} event - Click event from print button
     */
    function printReport(event) {
        // Prevent default behavior
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        // Get the print button position for dialog placement
        const printButton = event ? event.target : document.querySelector('.print-button');
        const buttonRect = printButton ? printButton.getBoundingClientRect() : { bottom: 100, left: 300 };
        
        // Create orientation selection dialog
        const orientationDiv = createOrientationDialog(buttonRect);
        document.body.appendChild(orientationDiv);
        
        // Setup dialog close handler
        setupDialogCloseHandler(orientationDiv);
    }

    /**
     * Create the orientation selection dialog
     * @param {DOMRect} buttonRect - Button position for dialog placement
     * @returns {HTMLElement} The dialog element
     */
    function createOrientationDialog(buttonRect) {
        const orientationDiv = document.createElement('div');
        orientationDiv.innerHTML = `
            <div style="position: fixed; 
                       top: ${buttonRect.bottom + 10}px; 
                       left: ${Math.max(buttonRect.left - 300, 20)}px; 
                       background: white; 
                       padding: 20px; 
                       border: none; 
                       border-radius: 12px; 
                       box-shadow: 0 8px 32px rgba(0,0,0,0.15); 
                       z-index: 9999;
                       min-width: 200px;
                       backdrop-filter: blur(10px);
                       animation: slideIn 0.2s ease-out;">
                <p style="margin: 0 0 16px 0; font-weight: 600; color: #2d3748; font-size: 14px;">
                    Select Print Orientation
                </p>
                <div style="display: flex; gap: 10px;">
                    <button onclick="window.ReusablePrint.selectOrientation('landscape')" 
                           style="flex: 1; padding: 12px 20px; 
                                  background: linear-gradient(135deg, #28a745, #20c997); 
                                  color: white; 
                                  border: none; 
                                  border-radius: 8px; 
                                  cursor: pointer;
                                  font-weight: 500;
                                  font-size: 13px;
                                  transition: all 0.2s ease;
                                  box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);"
                           onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(40, 167, 69, 0.4)'"
                           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(40, 167, 69, 0.3)'">
                        📄 Landscape
                    </button>
                    <button onclick="window.ReusablePrint.selectOrientation('portrait')" 
                           style="flex: 1; padding: 12px 20px; 
                                  background: linear-gradient(135deg, #17a2b8, #28a745); 
                                  color: white; 
                                  border: none; 
                                  border-radius: 8px; 
                                  cursor: pointer;
                                  font-weight: 500;
                                  font-size: 13px;
                                  transition: all 0.2s ease;
                                  box-shadow: 0 2px 8px rgba(23, 162, 184, 0.3);"
                           onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(23, 162, 184, 0.4)'"
                           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(23, 162, 184, 0.3)'">
                        📱 Portrait
                    </button>
                </div>
            </div>
            <style>
                @keyframes slideIn {
                    from {
                        opacity: 0;
                        transform: translateY(-10px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
            </style>
        `;
        return orientationDiv;
    }

    /**
     * Setup dialog close handler for clicking outside
     * @param {HTMLElement} orientationDiv - The dialog element
     */
    function setupDialogCloseHandler(orientationDiv) {
        const closeDialog = function(e) {
            if (!orientationDiv.contains(e.target)) {
                if (document.body.contains(orientationDiv)) {
                    document.body.removeChild(orientationDiv);
                }
                document.removeEventListener('click', closeDialog);
            }
        };
        
        // Add delay to prevent immediate closure
        setTimeout(() => document.addEventListener('click', closeDialog), 100);
    }

    /**
     * Handle orientation selection
     * @param {string} orientation - 'landscape' or 'portrait'
     */
    function selectOrientation(orientation) {
        // Remove any existing dialog
        const existingDialog = document.querySelector('[style*="position: fixed"]');
        if (existingDialog && existingDialog.parentNode) {
            existingDialog.parentNode.removeChild(existingDialog);
        }

        // Execute print based on orientation
        if (orientation === 'landscape') {
            printLandscape();
        } else {
            printPortrait();
        }
    }

    /**
     * Print in landscape orientation
     */
    function printLandscape() {
        // Add landscape class to body - this works with recipe-table-combined.css
        document.body.classList.add('print-landscape');
        document.body.classList.remove('print-portrait');
        
        // Print after a short delay to ensure CSS is applied
        setTimeout(() => {
            window.print();
            
            // Clean up after printing
            setTimeout(() => {
                document.body.classList.remove('print-landscape');
            }, 1000);
        }, 100);
    }

    /**
     * Print in portrait orientation
     */
    function printPortrait() {
        // Add portrait class to body - this works with recipe-table-combined.css
        document.body.classList.add('print-portrait');
        document.body.classList.remove('print-landscape');
        
        // Print after a short delay to ensure CSS is applied
        setTimeout(() => {
            window.print();
            
            // Clean up after printing
            setTimeout(() => {
                document.body.classList.remove('print-portrait');
            }, 1000);
        }, 100);
    }

    /**
     * Get current configuration
     * @returns {Object} Current configuration
     */
    function getConfig() {
        return config;
    }

    /**
     * Update configuration
     * @param {Object} newConfig - New configuration options
     */
    function updateConfig(newConfig) {
        config = Object.assign({}, config, newConfig);
    }

    /**
     * Get a DataTables-compatible print button configuration
     * @param {Object} options - Button configuration options
     * @returns {Object} DataTables button configuration
     */
    function getDataTablesButton(options = {}) {
        return {
            text: options.text || '<i class="fa fa-print"></i> Print',
            className: options.className || 'btn btn-secondary btn-sm',
            action: function(e, dt, button, config) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('Custom DataTables print button clicked');
                
                // Use our custom print system
                printReport(e);
                
                return false;
            },
            init: function(dt, button, config) {
                // Override any default print functionality
                button.off('click.dt').on('click.dt', function(e) {
                    config.action.call(this, e, dt, button, config);
                });
            }
        };
    }

    /**
     * Add print button to existing DataTable
     * @param {Object} table - DataTable instance
     * @param {Object} options - Button options
     */
    function addPrintButtonToTable(table, options = {}) {
        if (table && typeof table.button === 'function') {
            const buttonConfig = getDataTablesButton(options);
            table.button().add(0, buttonConfig);
        }
    }

    // Public API
    return {
        init: init,
        printReport: printReport,
        selectOrientation: selectOrientation,
        printLandscape: printLandscape,
        printPortrait: printPortrait,
        getConfig: getConfig,
        updateConfig: updateConfig,
        getDataTablesButton: getDataTablesButton,
        addPrintButtonToTable: addPrintButtonToTable,
        overrideDataTablesPrint: overrideDataTablesPrint
    };
})();

// Auto-initialize with basic configuration if not already done
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.printReport === 'undefined') {
        window.ReusablePrint.init();
    }
    
    // Disable automatic DataTables watching to allow default print
    // watchForDataTables(); // Commented out to allow default DataTables print
});

/**
 * Watch for DataTables initialization and override print functionality
 */
function watchForDataTables() {
    // If jQuery is available, watch for DataTables
    if (typeof $ !== 'undefined') {
        // Override when DataTables is ready
        $(document).ready(function() {
            // Watch for DataTables initialization
            if (typeof $.fn.dataTable !== 'undefined') {
                overrideDataTablesButtons();
            }
            
            // Also watch for dynamic DataTable creation
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes.length > 0) {
                        $(mutation.addedNodes).find('.dt-button.buttons-print').each(function() {
                            overrideDataTablesButtons();
                        });
                    }
                });
            });
            
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        });
    }
}

/**
 * Override DataTables button functionality
 */
function overrideDataTablesButtons() {
    if (typeof $.fn.dataTable !== 'undefined' && $.fn.dataTable.ext && $.fn.dataTable.ext.buttons) {
        // Override the print button action
        if ($.fn.dataTable.ext.buttons.print) {
            $.fn.dataTable.ext.buttons.print.action = function(e, dt, button, config) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('DataTables print button action overridden');
                
                // Use our custom print system
                if (typeof window.printReport === 'function') {
                    window.printReport(e);
                } else if (typeof window.ReusablePrint !== 'undefined') {
                    window.ReusablePrint.printReport(e);
                }
                
                return false;
            };
        }
        
        // Also override any existing print buttons
        $('.dt-button.buttons-print').off('click.dt').on('click.dt', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Existing DataTables print button overridden');
            
            if (typeof window.printReport === 'function') {
                window.printReport(e);
            } else if (typeof window.ReusablePrint !== 'undefined') {
                window.ReusablePrint.printReport(e);
            }
            
            return false;
        });
    }
} 