@php
    // Get the passed parameters or use defaults
    $reportName = $reportName ?? $report_name ?? 'Default Report';
    $printBy = $printBy ?? $print_by ?? 'System User';
    $assignTo = $assignTo ?? $assign_to ?? null;
    $extraFields = $extraFields ?? $extra_fields ?? [];
    $reportNameTail = $reportNameTail ?? $report_name_tail ?? null;
    
    // Ensure businessInfo is available
    $businessInfo = $businessInfo ?? [
        'name' => 'Default Business',
        'location' => 'Default Location',
        'logo_exists' => false,
        'logo_url' => ''
    ];
@endphp

<!-- Header Toggle Component -->
<div class="header-toggle-container no-print" style="margin-right: 8px !important; ">
    <button id="headerToggleBtn" class="header-toggle-btn" type="button" ">
        <i style="font-size: 12px !important; "class="fas fa-exchange-alt"></i>
        <span id="currentHeaderText" style="font-size: 12px !important; ">Header</span>
    </button>
</div>

<!-- Modal for Header Selection -->
<div id="headerSelectionModal" class="header-modal-overlay" style="display: none;">
    <div class="header-modal-content">
        <div class="header-modal-header">
            <h3>Choose Header Layout</h3>
            <button id="closeModalBtn" class="header-modal-close" type="button">&times;</button>
        </div>
        
        <div class="header-modal-body">
            <div class="header-preview-container">
                <div class="header-preview-item" data-header="1">
                    <div class="header-preview-label">Header Layout 1</div>
                    <div class="header-preview-content" id="headerPreview1">
                        <!-- Header 1 Preview will be loaded here -->
                    </div>
                </div>
                
                <div class="header-preview-item" data-header="2">
                    <div class="header-preview-label">Header Layout 2</div>
                    <div class="header-preview-content" id="headerPreview2">
                        <!-- Header 2 Preview will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dynamic Header Include -->
<div id="dynamicHeaderContainer">
    @php
        $selectedHeader = 1; // Default
    @endphp
    
    <div id="header1Container" style="display: block;">
        @include('employeetracker::components.reportheader1', [
            'report_name' => $reportName,
            'print_by' => $printBy,
            'assign_to' => $assignTo,
            'extra_fields' => $extraFields,
            'report_name_tail' => $reportNameTail,
            'businessInfo' => $businessInfo,
            'start_date' => $start_date ?? null,
            'end_date' => $end_date ?? null,
        ])
    </div>
    
    <div id="header2Container" style="display: none;">
        @include('employeetracker::components.reportheader2', [
            'report_name' => $reportName,
            'print_by' => $printBy,
            'assign_to' => $assignTo,
            'extra_fields' => $extraFields,
            'report_name_tail' => $reportNameTail,
            'businessInfo' => $businessInfo,
            'start_date' => $start_date ?? null,
            'end_date' => $end_date ?? null,
        ])
    </div>
</div>

<style>
/* Header Toggle Button Styles */
.header-toggle-container {
    margin-bottom: 15px;
    text-align: right;
}

.header-toggle-btn {
    background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
}

.header-toggle-btn:hover {
    background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.header-toggle-btn:active {
    transform: translateY(0);
}

.header-toggle-btn i {
    font-size: 16px;
}

/* Modal Styles */
.header-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(4px);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.header-modal-overlay.show {
    opacity: 1;
    visibility: visible;
}

.header-modal-content {
    background: white;
    border-radius: 12px;
    width: 100%;
    max-width: 1200px;
    max-height: 90vh;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    transform: scale(0.9) translateY(50px);
    transition: all 0.3s ease;
    margin: auto;
    position: relative;
}

.header-modal-overlay.show .header-modal-content {
    transform: scale(1) translateY(0);
}

.header-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
}

.header-modal-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #111827;
}

.header-modal-close {
    background: none;
    border: none;
    font-size: 24px;
    font-weight: bold;
    color: #6b7280;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.header-modal-close:hover {
    background: #f3f4f6;
    color: #374151;
}

.header-modal-body {
    padding: 24px;
    overflow-y: auto;
    max-height: calc(80vh - 80px);
}

/* Header Preview Styles */
.header-preview-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
}

.header-preview-item {
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
}

.header-preview-item:hover {
    border-color: #3B82F6;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
    transform: translateY(-2px);
}

.header-preview-item.selected {
    border-color: #10B981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.header-preview-label {
    background: #f9fafb;
    padding: 12px 16px;
    font-weight: 600;
    font-size: 14px;
    color: #374151;
    border-bottom: 1px solid #e5e7eb;
    text-align: center;
}

.header-preview-item.selected .header-preview-label {
    background: #10B981;
    color: white;
}

.header-preview-content {
    padding: 16px;
    transform: scale(0.75);
    transform-origin: top left;
    width: 133.33%;
    height: auto;
    overflow: hidden;
    min-height: 120px;
    background: #f8f9fa;
    border-radius: 4px;
}

.header-preview-content .report-header {
    margin-bottom: 0 !important;
    background-color: #f8f9fa !important;
}

.header-preview-content * {
    pointer-events: none;
}

/* Ensure QR code containers don't break layout */
.header-preview-content #item_qrcode {
    display: none !important;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .header-modal-overlay {
        padding: 10px;
    }
    
    .header-modal-content {
        width: 100%;
        margin: 0;
        max-height: 95vh;
    }
    
    .header-preview-container {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .header-modal-body {
        padding: 16px;
        max-height: calc(95vh - 80px);
    }
    
    .header-toggle-btn {
        padding: 10px 16px;
        font-size: 13px;
    }
    
    .header-preview-content {
        transform: scale(0.65);
        width: 153.8%;
    }
}

@media (max-width: 480px) {
    .header-modal-overlay {
        padding: 5px;
    }
    
    .header-modal-content {
        border-radius: 8px;
    }
    
    .header-preview-content {
        transform: scale(0.6);
        width: 166.7%;
        padding: 12px;
    }
    
    .header-modal-header {
        padding: 15px 20px;
    }
    
    .header-modal-header h3 {
        font-size: 16px;
    }
}

/* Print Styles */
@media print {
    .header-toggle-container,
    .header-modal-overlay {
        display: none !important;
    }
}

/* Loading Animation */
.loading-preview {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100px;
    color: #6b7280;
}

.loading-preview i {
    animation: spin 1s linear infinite;
    margin-right: 8px;
}

.preview-error {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100px;
    color: #374151;
    text-align: center;
    font-weight: 600;
}

.preview-error small {
    color: #6b7280;
    font-weight: normal;
    margin-top: 4px;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

<script>
window.addEventListener('load', function() {
    // Elements
    const toggleBtn = document.getElementById('headerToggleBtn');
    const modal = document.getElementById('headerSelectionModal');
    const closeBtn = document.getElementById('closeModalBtn');
    const currentHeaderText = document.getElementById('currentHeaderText');
    const header1Container = document.getElementById('header1Container');
    const header2Container = document.getElementById('header2Container');
    const headerPreview1 = document.getElementById('headerPreview1');
    const headerPreview2 = document.getElementById('headerPreview2');
    
    // Get current selection from localStorage or default to 1
    let selectedHeader = localStorage.getItem('selectedReportHeader') || '1';
    
    // Initialize header display
    function initializeHeader() {
        
        if (selectedHeader === '2') {
            header1Container.style.display = 'none';
            header2Container.style.display = 'block';
            currentHeaderText.textContent = 'Header 2';
        } else {
            header1Container.style.display = 'block';
            header2Container.style.display = 'none';
            currentHeaderText.textContent = 'Header 1';
            selectedHeader = '1'; // Ensure default
            // Update localStorage if it was undefined
            localStorage.setItem('selectedReportHeader', selectedHeader);
        }
        
        // Dispatch event to notify other components
        document.dispatchEvent(new CustomEvent('headerInitialized', {
            detail: { selectedHeader: selectedHeader }
        }));
    }
    
    // Load header previews
    function loadHeaderPreviews() {
        // Load Header 1 Preview
        if (headerPreview1.children.length === 0) {
            headerPreview1.innerHTML = '<div class="loading-preview"><i class="fas fa-spinner"></i>Loading Layout 1...</div>';
            
            setTimeout(() => {
                try {
                    // Get the visible header content
                    const header1Content = header1Container.querySelector('.report-header');
                    if (header1Content) {
                        const header1Clone = header1Content.cloneNode(true);
                        header1Clone.id = 'header1Preview';
                        
                        // Remove any interactive elements
                        const scripts = header1Clone.querySelectorAll('script');
                        scripts.forEach(script => script.remove());
                        
                        headerPreview1.innerHTML = '';
                        headerPreview1.appendChild(header1Clone);
                    } else {
                        // Fallback to full container
                        const header1Clone = header1Container.cloneNode(true);
                        header1Clone.id = 'header1Preview';
                        
                        const scripts = header1Clone.querySelectorAll('script');
                        scripts.forEach(script => script.remove());
                        
                        headerPreview1.innerHTML = '';
                        headerPreview1.appendChild(header1Clone);
                    }
                } catch (e) {
                    console.error('Error loading header 1 preview:', e);
                    headerPreview1.innerHTML = '<div class="preview-error">Header Layout 1<br><small>Classic Layout</small></div>';
                }
            }, 150);
        }
        
        // Load Header 2 Preview
        if (headerPreview2.children.length === 0) {
            headerPreview2.innerHTML = '<div class="loading-preview"><i class="fas fa-spinner"></i>Loading Layout 2...</div>';
            
            setTimeout(() => {
                try {
                    // First, temporarily show header2 to get its content
                    const wasHidden = header2Container.style.display === 'none';
                    console.log('Header2 was hidden:', wasHidden);
                    
                    if (wasHidden) {
                        header2Container.style.display = 'block';
                        header2Container.style.visibility = 'hidden';
                        header2Container.style.position = 'absolute';
                        header2Container.style.top = '-9999px';
                        header2Container.style.left = '-9999px';
                    }
                    
                    // Force reflow to ensure content is rendered
                    header2Container.offsetHeight;
                    
                    // Wait a moment for rendering
                    setTimeout(() => {
                        console.log('Attempting to load header 2 preview...');
                        console.log('Header2Container innerHTML length:', header2Container.innerHTML.length);
                        console.log('Header2Container children:', header2Container.children.length);
                        
                        // Try multiple selectors since mony_report_header might have different structure
                        let header2Content = header2Container.querySelector('.report-header');
                        console.log('Found .report-header:', !!header2Content);
                        
                        // If .report-header not found, try other common selectors
                        if (!header2Content) {
                            header2Content = header2Container.querySelector('div:first-child');
                            console.log('Found div:first-child:', !!header2Content);
                        }
                        
                        // If still not found, use the entire container content
                        if (!header2Content) {
                            header2Content = header2Container;
                            console.log('Using entire container as fallback');
                        }
                        
                        if (header2Content && header2Content !== header2Container) {
                            const header2Clone = header2Content.cloneNode(true);
                            header2Clone.id = 'header2Preview';
                            
                            // Remove scripts and problematic elements
                            const scripts = header2Clone.querySelectorAll('script');
                            scripts.forEach(script => script.remove());
                            
                            // Hide problematic elements in preview
                            const problematicElements = header2Clone.querySelectorAll('#item_qrcode, .header-center, script, style');
                            problematicElements.forEach(el => {
                                if (el.tagName === 'SCRIPT' || el.tagName === 'STYLE') {
                                    el.remove();
                                } else {
                                    el.style.display = 'none';
                                }
                            });
                            
                            headerPreview2.innerHTML = '';
                            headerPreview2.appendChild(header2Clone);
                        } else {
                            // Use the entire container as fallback
                            const header2Clone = header2Container.cloneNode(true);
                            header2Clone.id = 'header2Preview';
                            
                            // Remove scripts and problematic elements
                            const scripts = header2Clone.querySelectorAll('script');
                            scripts.forEach(script => script.remove());
                            
                            // Remove style tags to prevent conflicts
                            const styles = header2Clone.querySelectorAll('style');
                            styles.forEach(style => style.remove());
                            
                            // Hide problematic elements
                            const problematicElements = header2Clone.querySelectorAll('#item_qrcode, .header-center');
                            problematicElements.forEach(el => el.style.display = 'none');
                            
                            headerPreview2.innerHTML = '';
                            headerPreview2.appendChild(header2Clone);
                        }
                        
                        // Restore header2 visibility state
                        if (wasHidden) {
                            header2Container.style.display = 'none';
                            header2Container.style.visibility = '';
                            header2Container.style.position = '';
                            header2Container.style.top = '';
                            header2Container.style.left = '';
                        }
                        
                        console.log('Header 2 preview loaded successfully');
                    }, 150);
                    
                } catch (e) {
                    console.error('Error loading header 2 preview:', e);
                    
                    // Last resort fallback - create a simple preview
                    try {
                        headerPreview2.innerHTML = `
                            <div style="padding: 20px; text-align: center; border: 2px dashed #e5e7eb; border-radius: 8px; background: #f9fafb;">
                                <h4 style="margin: 0; color: #374151;">Header Layout 2</h4>
                                <p style="margin: 5px 0; color: #6b7280; font-size: 14px;">mony_report_header Component</p>
                                <small style="color: #9ca3af;">Preview will be shown when selected</small>
                            </div>
                        `;
                        console.log('Header 2 fallback preview displayed');
                    } catch (fallbackError) {
                        console.error('Fallback also failed:', fallbackError);
                        headerPreview2.innerHTML = '<div class="preview-error">Header Layout 2<br><small>mony_report_header Layout</small></div>';
                    }
                    
                    // Restore header2 state if error occurs
                    try {
                        header2Container.style.display = 'none';
                        header2Container.style.visibility = '';
                        header2Container.style.position = '';
                        header2Container.style.top = '';
                        header2Container.style.left = '';
                    } catch (restoreError) {
                        console.error('Error restoring header2 state:', restoreError);
                    }
                }
            }, 200);
        }
    }
    
    // Update preview selection states
    function updatePreviewSelection() {
        const previewItems = document.querySelectorAll('.header-preview-item');
        previewItems.forEach(item => {
            if (item.dataset.header === selectedHeader) {
                item.classList.add('selected');
            } else {
                item.classList.remove('selected');
            }
        });
    }
    
    // Open modal
    function openModal() {
        loadHeaderPreviews();
        updatePreviewSelection();
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    
    // Close modal
    function closeModal() {
        modal.classList.remove('show');
        document.body.style.overflow = '';
    }
    
    // Switch header
    function switchHeader(headerNumber) {
        const previousHeader = selectedHeader;
        selectedHeader = headerNumber;
        
        // Save to localStorage with error handling
        try {
            localStorage.setItem('selectedReportHeader', selectedHeader);
            console.log('Header selection saved to localStorage:', selectedHeader);
        } catch (e) {
            console.error('Failed to save header selection to localStorage:', e);
        }
        
        if (selectedHeader === '2') {
            header1Container.style.display = 'none';
            header2Container.style.display = 'block';
            currentHeaderText.textContent = 'Header 2';
        } else {
            header1Container.style.display = 'block';
            header2Container.style.display = 'none';
            currentHeaderText.textContent = 'Header 1';
        }
        
        updatePreviewSelection();
        closeModal();
        
        // Show confirmation message if header changed
        if (previousHeader !== selectedHeader) {
            showNotification(`Header Layout ${selectedHeader} selected! This will apply to all reports.`);
        }
        
        // Dispatch custom event for other components to listen to
        document.dispatchEvent(new CustomEvent('headerChanged', {
            detail: { 
                selectedHeader: selectedHeader,
                previousHeader: previousHeader
            }
        }));
    }
    
    // Event listeners
    toggleBtn.addEventListener('click', openModal);
    closeBtn.addEventListener('click', closeModal);
    
    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('show')) {
            closeModal();
        }
    });
    
    // Header preview selection
    document.addEventListener('click', function(e) {
        const previewItem = e.target.closest('.header-preview-item');
        if (previewItem) {
            const headerNumber = previewItem.dataset.header;
            switchHeader(headerNumber);
        }
    });
    
    // Initialize on load
    initializeHeader();
    
    // Listen for date range changes and update both headers
    document.addEventListener('dateRangeChanged', function(e) {
        const { startDate, endDate } = e.detail;
        
        // Update both header containers
        const dateRangeElements = document.querySelectorAll('#report-date-range');
        dateRangeElements.forEach(element => {
            if (startDate && endDate) {
                const startFormatted = formatDate(startDate);
                const endFormatted = formatDate(endDate);
                element.textContent = `Date: ${startFormatted} - ${endFormatted}`;
            } else {
                element.textContent = 'All Dates';
            }
        });
    });
    
    // Helper function to format date
    function formatDate(dateStr) {
        if (!dateStr) return '';
        const date = new Date(dateStr);
        if (isNaN(date.getTime())) return 'Invalid Date';
        return date.getDate().toString().padStart(2, '0') + '/' +
            (date.getMonth() + 1).toString().padStart(2, '0') + '/' +
            date.getFullYear();
    }
    
    // Show notification function
    function showNotification(message) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = 'header-toggle-notification';
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-check-circle"></i>
                <span>${message}</span>
            </div>
        `;
        
        // Add notification styles
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #10B981;
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            z-index: 10000;
            font-size: 14px;
            font-weight: 500;
            max-width: 350px;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Remove after 4 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 4000);
    }
    
    // Check if this is a page load and show status
    const isPageLoad = !window.headerToggleLoaded;
    window.headerToggleLoaded = true;
    
    if (isPageLoad && selectedHeader) {
        setTimeout(() => {
            const layoutName = selectedHeader === '2' ? 'Header Layout 2' : 'Header Layout 1';
            console.log(`Page loaded with saved header selection: ${layoutName}`);
        }, 500);
    }
});
</script> 