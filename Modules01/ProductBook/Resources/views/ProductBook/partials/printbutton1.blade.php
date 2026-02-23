@php
    // Helper function to format date
    if (!function_exists('formatDate')) {
        function formatDate($date)
        {
            if (empty($date)) {
                return '';
            }
            // Validate date string
            $timestamp = is_string($date) ? strtotime($date) : (is_numeric($date) ? $date : false);
            if ($timestamp === false) {
                return 'Invalid Date';
            }
            return date('d/m/Y', $timestamp);
        }
    }

    // Default values for optional parameters
    $print_by = $print_by ?? null;
    $assign_to = $assign_to ?? null;
    $extra_fields = $extra_fields ?? [];
    $report_name_tail = $report_name_tail ?? [];
    $report_name = $report_name ?? 'Report';
@endphp

<style>
    button.print-button {
        width: 50px;
        height: 50px;
        margin-top: 8px;
        position: relative;
        padding: 0;
        border: 0;
        background: transparent;
        cursor: pointer;
    }

    span.print-icon,
    span.print-icon::before,
    span.print-icon::after {
        box-sizing: border-box;
        background-color: #fff;
        border: solid 2px #0f8800;
    }

    span.print-icon::after {
        border-width: 1px;
    }

    button.print-button:hover .print-icon::after {
        border: solid 2px #0f8800;
    }

    span.print-icon {
        position: relative;
        display: inline-block;
        padding: 0;
        margin-top: 20%;
        width: 60%;
        height: 35%;
        background: #fff;
        border-radius: 20% 20% 0 0;
    }

    span.print-icon::before {
        content: "";
        position: absolute;
        bottom: 100%;
        left: 12%;
        right: 12%;
        height: 110%;
        transition: height .2s .15s;
    }

    span.print-icon::after {
        content: "";
        position: absolute;
        top: 55%;
        left: 12%;
        right: 12%;
        height: 0%;
        background: #fff;
        background-repeat: no-repeat;
        background-size: 70% 90%;
        background-position: center;
        background-image: linear-gradient(to top,
                #fff 0, #fff 14%,
                #0f8800 14%, #0f8800 28%,
                #fff 28%, #fff 42%,
                #0f8800 42%, #0f8800 56%,
                #fff 56%, #fff 70%,
                #0f8800 70%, #0f8800 84%,
                #fff 84%, #fff 100%);
        transition: height .2s,
            border-width 0s .2s,
            width 0s .2s;
    }

    button.print-button:hover .print-icon::before {
        height: 0px;
        transition: height .2s;
    }

    button.print-button:hover .print-icon::after {
        height: 120%;
        transition: height .2s .15s, border-width 0s .2s;
    }
</style>

<div id="business-info" style="display: none;">
    <span id="business-name">{{ $businessInfo['name'] ?? 'Business Name' }}</span>
    <span id="business-logo">{{ $businessInfo['logo_url'] ?? '' }}</span>
    <span id="business-location">{{ $businessInfo['location'] ?? 'N/A' }}</span>
    <span id="business-tax-number">{{ $businessInfo['tax_number'] ?? '' }}</span>
    <span id="business-logo-exists">{{ $businessInfo['logo_exists'] ?? false ? 'true' : 'false' }}</span>
    <span id="print-by">{{ $print_by ?? 'N/A' }}</span>
    <span id="assign-to">{{ $assign_to ?? 'N/A' }}</span>
    <span id="report-name">{{ $report_name }}@if ($report_name_tail)
            <span>{{ $report_name_tail }}</span>
        @endif
    </span>
    <!-- Store initial PHP dates -->
    <span id="php-start-date">{{ isset($start_date) ? formatDate($start_date) : '' }}</span>
    <span id="php-end-date">{{ isset($end_date) ? formatDate($end_date) : '' }}</span>
    <!-- These will be updated by JavaScript -->
    <span id="current-start-date">{{ isset($start_date) ? formatDate($start_date) : '' }}</span>
    <span id="current-end-date">{{ isset($end_date) ? formatDate($end_date) : '' }}</span>
    @foreach ($extra_fields as $key => $value)
        <span id="extra-{{ $key }}">{{ $value }}</span>
    @endforeach
</div>

<!-- Print Button -->
<button class="print-button" id="print-button" title="Print Report">
    <span class="print-icon"></span>
</button>



<script>
    const reportLink = window.location.href;

    function formatDate(dateStr) {
        if (!dateStr) return '';
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-GB'); // Format as DD/MM/YYYY
    }

    // Global function to update current dates (called from header script)
    window.updatePrintDates = function(startDate, endDate) {
        // Update selected dates
        window.selectedStartDate = startDate;
        window.selectedEndDate = endDate;

        // Update the report date range in the print view
        const dateRangeElement = document.getElementById('report-date-range');
        if (dateRangeElement) {
            if (startDate && endDate) {
                dateRangeElement.textContent = 'Date: ' + formatDate(startDate) + ' - ' + formatDate(endDate);
            } else {
                dateRangeElement.textContent = 'All Dates';
            }
        }

        // Update current dates in hidden spans
        const currentStartElement = document.getElementById('current-start-date');
        const currentEndElement = document.getElementById('current-end-date');

        if (currentStartElement) {
            currentStartElement.innerText = startDate || '';
        }
        if (currentEndElement) {
            currentEndElement.innerText = endDate || '';
        }

        console.log('Print dates updated:', {
            startDate,
            endDate
        });
    };

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize dates from URL parameters or defaults
        const urlParams = new URLSearchParams(window.location.search);
        const startDate = urlParams.get('start_date') || window.selectedStartDate;
        const endDate = urlParams.get('end_date') || window.selectedEndDate;
        window.updatePrintDates(startDate, endDate);
        
        const printButton = document.getElementById('print-button');
        if (!printButton) return;

        // Listen for date range changes from header
        document.addEventListener('dateRangeChanged', function(e) {
            const startDate = e.detail.startDate;
            const endDate = e.detail.endDate;
            const formattedStart = startDate ? formatDate(startDate) : '';
            const formattedEnd = endDate ? formatDate(endDate) : '';
            window.updatePrintDates(formattedStart, formattedEnd);
        });
        
        // Get business info from hidden elements
        function getBusinessInfo() {
            return {
                mobile: document.getElementById('business-phone_number')?.innerText || '',
                name: document.getElementById('business-name')?.innerText || '',
                logo: document.getElementById('business-logo')?.innerText || '',
                location: document.getElementById('business-location')?.innerText || '',
                tax_number: document.getElementById('business-tax-number')?.innerText || '',
                logo_exists: document.getElementById('business-logo-exists')?.innerText === 'true',
                print_by: document.getElementById('print-by')?.innerText || 'N/A',
                assign_to: document.getElementById('assign-to')?.innerText || 'N/A',
                report_name: document.getElementById('report-name')?.innerText || 'Report',
                // Use current dates (updated by JS) or fall back to PHP dates
                start_date: document.getElementById('current-start-date')?.innerText ||
                    document.getElementById('php-start-date')?.innerText || '',
                end_date: document.getElementById('current-end-date')?.innerText ||
                    document.getElementById('php-end-date')?.innerText || ''
            };
        }

        // Get extra fields
        function getExtraFields() {
            const extraFields = {};
            const hiddenDiv = document.getElementById('business-info');
            if (hiddenDiv) {
                const extraSpans = hiddenDiv.querySelectorAll('[id^="extra-"]');
                extraSpans.forEach(span => {
                    const key = span.id.replace('extra-', '');
                    extraFields[key] = span.innerText;
                });
            }
            return extraFields;
        }

        // Format date helper
        function formatDate(dateStr) {
            if (!dateStr) return '';
            const date = typeof dateStr === 'string' ? new Date(dateStr) : dateStr;
            if (isNaN(date.getTime())) return 'Invalid Date';
            return date.getDate().toString().padStart(2, '0') + '/' +
                (date.getMonth() + 1).toString().padStart(2, '0') + '/' +
                date.getFullYear();
        }

        // Get report title from various possible sources, fallback to hidden element
        function getReportTitle() {
            const businessInfo = getBusinessInfo();
            if (businessInfo.report_name && businessInfo.report_name !== 'Report') {
                return businessInfo.report_name;
            }

            const titleSources = [
                '.report-subtitle b',
                '.normal-view-title:nth-child(2)',
                '.card-title',
                'h1',
                '.page-title',
                '.report-name'
            ];

            for (const selector of titleSources) {
                const element = document.querySelector(selector);
                if (element && element.innerText.trim()) {
                    return element.innerText.trim();
                }
            }

            return document.title || 'Report';
        }

        // Get date range information with duration calculation
        function getDateRangeText() {
            const businessInfo = getBusinessInfo();

            // Helper function to calculate days between dates
            function calculateDaysBetween(startDateStr, endDateStr) {
                if (!startDateStr || !endDateStr) return null;
                
                // Parse DD/MM/YYYY format
                const parseDate = (dateStr) => {
                    const parts = dateStr.split('/');
                    if (parts.length === 3) {
                        // DD/MM/YYYY -> new Date(YYYY, MM-1, DD)
                        return new Date(parseInt(parts[2]), parseInt(parts[1]) - 1, parseInt(parts[0]));
                    }
                    // Fallback to standard Date parsing
                    return new Date(dateStr);
                };
                
                const startDate = parseDate(startDateStr);
                const endDate = parseDate(endDateStr);
                
                if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
                    return null;
                }
                
                // Calculate difference in days (include both start and end dates)
                const timeDiff = endDate.getTime() - startDate.getTime();
                const daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1;
                
                return daysDiff > 0 ? daysDiff : 1; // Minimum 1 day
            }

            // Use the dates from business info (which now includes current dates)
            if (businessInfo.start_date && businessInfo.end_date) {
                const days = calculateDaysBetween(businessInfo.start_date, businessInfo.end_date);
                if (days && days > 1) {
                    return `${businessInfo.start_date} - ${businessInfo.end_date} (${days} days)`;
                } else if (days === 1) {
                    return `${businessInfo.start_date} - ${businessInfo.end_date} (1 day)`;
                } else {
                    return `${businessInfo.start_date} - ${businessInfo.end_date}`;
                }
            } else if (businessInfo.start_date) {
                return `From: ${businessInfo.start_date}`;
            } else if (businessInfo.end_date) {
                return `Until: ${businessInfo.end_date}`;
            }

            // Fallback to reading from report header
            const reportDateElement = document.querySelector('#report-date-range');
            if (reportDateElement && reportDateElement.innerText) {
                let dateText = reportDateElement.innerText.replace('Date: ', '');
                if (dateText && dateText !== 'All Dates') {
                    // Check if it already has duration, if not, try to add it
                    if (!dateText.includes('(') && dateText.includes(' - ')) {
                        const [startStr, endStr] = dateText.split(' - ');
                        const days = calculateDaysBetween(startStr.trim(), endStr.trim());
                        if (days && days > 1) {
                            dateText = `${dateText} (${days} days)`;
                        } else if (days === 1) {
                            dateText = `${dateText} (1 day)`;
                        }
                    }
                    return dateText;
                }
            }

            return 'All Dates';
        }

        // Get additional filters
        function getAdditionalFilters() {
            let filters = '';
            const usernameFilter = document.querySelector('#username_filter');
            if (usernameFilter && usernameFilter.value) {
                const selectedOption = usernameFilter.options[usernameFilter.selectedIndex];
                if (selectedOption && selectedOption.text.trim()) {
                    filters += `<p>Employee: ${selectedOption.text.trim()}</p>`;
                }
            }
            return filters;
        }

        // Get the selected header type from localStorage
        function getSelectedHeaderType() {
            return localStorage.getItem('selectedReportHeader') || '1';
        }

        // Generate header HTML based on selected type
        function generateHeaderHTML(businessInfo, reportTitle, dateRangeText, extraFields) {
            const selectedHeader = getSelectedHeaderType();
            
            if (selectedHeader === '2') {
                // Generate mony_report_header style
                return generateMonyReportHeader(businessInfo, reportTitle, dateRangeText, extraFields);
            } else {
                // Generate default reportheader style
                return generateDefaultReportHeader(businessInfo, reportTitle, dateRangeText, extraFields);
            }
        }

        // Generate default report header (exact copy of reportheader.blade.php structure)
        function generateDefaultReportHeader(businessInfo, reportTitle, dateRangeText, extraFields) {
            let extraFieldsHtml = '';
            Object.keys(extraFields).forEach(key => {
                if (extraFields[key] !== '') {
                    const fieldName = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                    extraFieldsHtml += `<div class="print-meta-field" style="font-size: 14px; margin-top: 5px; color: #333;">${fieldName}: ${extraFields[key]}</div>`;
                }
            });

            return `
                <div class="print-header" style="margin-bottom: 20px; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; background-color: #f8f9fa; padding: 15px; border-radius: 4px; position: relative;">
                    <div class="print-header-left" style="display: flex; align-items: center; flex: 1; z-index: 1;">
                        ${businessInfo.logo && businessInfo.logo_exists ? `
                            <img src="${businessInfo.logo}"
                                 alt="${businessInfo.name} Logo"
                                 class="print-logo"
                                 style="max-height: 50px; max-width: 50px; margin-right: 15px; height: 50px; width: 50px; object-fit: contain;"
                                 onerror="this.style.display='none';">
                        ` : ''}
                        <div class="print-business-info">
                            <div class="print-business-name" style="font-size: 20px; font-weight: 600;">${businessInfo.name}</div>
                            <div class="print-business-location" style="font-size: 14px; color: #666; margin-top: 2px;">${businessInfo.location}</div>
                            <div class="print-business-location" style="font-size: 14px; color: #666; margin-top: 2px;">${businessInfo.mobile}</div>
                        </div>
                    </div>

                    <div class="print-header-center" style="position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); text-align: center; z-index: 0;">
                        <div id="item_qrcode"></div>
                    </div>

                    <div class="print-header-right" style="flex: 1; text-align: right; z-index: 1;">
                        <div class="print-report-title" style="font-size: 22px; font-weight: 600; margin-bottom: 5px;">${reportTitle}</div>
                        <div class="print-date-range" style="font-size: 14px; margin-top: 5px;" id="report-date-range">
                            ${dateRangeText.includes('Date:') ? dateRangeText : (dateRangeText === 'All Dates' ? 'Date: All Dates' : `Date: ${dateRangeText}`)}
                        </div>
                        ${businessInfo.print_by && businessInfo.print_by !== 'N/A' ? 
                            `<div class="print-meta-field" style="font-size: 14px; margin-top: 5px;">Printed by: <span style="font-weight: bold;">${businessInfo.print_by}</span></div>` : ''}
                        ${businessInfo.assign_to && businessInfo.assign_to !== 'N/A' ? 
                            `<div class="print-meta-field" style="font-size: 14px; margin-top: 5px;">Assigned to: <span style="font-weight: bold;">${businessInfo.assign_to}</span></div>` : ''}
                        ${extraFieldsHtml}
                        <div class="print-meta-field" style="font-size: 14px; margin-top: 5px;">Printed on: ${new Date().getFullYear()}-${String(new Date().getMonth() + 1).padStart(2, '0')}-${String(new Date().getDate()).padStart(2, '0')} ${new Date().toLocaleTimeString()}</div>
                    </div>
                </div>
                
                <!-- Page footer for print page numbers -->
                <div style="display: none;" class="page-footer no-screen"></div>
            `;
        }

        // Generate mony report header (exact copy of mony_report_header.blade.php structure with print classes)
        function generateMonyReportHeader(businessInfo, reportTitle, dateRangeText, extraFields) {
            return `
                <div class="mony-header-row" style="color: #000000 !important; width: 100%; justify-content: center; align-items: center; display: flex; flex-direction: column;">
                    <div class="mony-header-col" style="width: 100%;">
                        <div class="mony-header-container" style="position: relative; text-align: center; display: flex; flex-direction: column; align-items: flex-start;">
                            ${businessInfo.logo && businessInfo.logo_exists ? `
                                <img src="${businessInfo.logo}" 
                                     class="mony-logo"
                                     style="position: static; align-self: flex-start; margin-left: 20px; margin-bottom: 10px; max-height: 100px; max-width: 100px; width: auto; object-fit: contain; object-position: center; page-break-inside: avoid;" 
                                     alt="Logo">
                            ` : ''}
                            
                            <div class="mony-content-section" style="width: 100%; text-align: center;">
                                <div class="mony-text-center" style="text-align: center;">
                                    <h1 class="mony-business-name" style="font-family: 'Moul', serif; font-weight: 400; font-style: normal; font-size: 26px; margin: 10px 0 5px 0; color: #000;">
                                        ${businessInfo.name}
                                    </h1>
                                    ${businessInfo.tax_number ? `<p class="mony-text-xs" style="font-size: 10px; color: #000; margin: 2px 0;">លេខអត្តសញ្ញាណកម្មសារពើពន្ធ: ${businessInfo.tax_number}</p>` : ''}
                                    <p class="mony-text-xs" style="font-size: 10px; color: #000; margin: 2px 0;">អាសយដ្ឋាន ៖${businessInfo.location}</p>
                                    <p class="mony-text-xs" style="font-size: 10px; color: #000; margin: 2px 0;">Address ៖${businessInfo.location}</p>
                                    <p class="mony-text-xs" style="font-size: 10px; color: #000; margin: 2px 0;">Telephone :</p>
                                </div>
                              
                                <h3 class="mony-report-title" style="text-align: center; font-family: 'Moul', serif; font-weight: 400; font-style: normal; font-size: 20px; margin: 15px 0 10px 0; color: #000;">
                                    ${reportTitle} <br />
                                    <p class="mony-tax-invoice" style="font-size: 20px; margin: 5px 0 0 0;">TAX INVOICE</p>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Main print function
        printButton.addEventListener('click', function() {
            const businessInfo = getBusinessInfo();
            const extraFields = getExtraFields();
            const mainTable = document.querySelector('.dataTable, .reusable-table, table.table');

            if (!mainTable) {
                window.print();
                return;
            }

            let reportTitle = getReportTitle();

            // Remove business name from title if present
            if (reportTitle.endsWith(businessInfo.name) && businessInfo.name !== '') {
                reportTitle = reportTitle.substring(0, reportTitle.length - businessInfo.name.length)
                    .trim().replace(/\s*[-—–]\s*$/, '');
            }

            const dateRangeText = getDateRangeText();
            const additionalFilters = getAdditionalFilters();

            console.log('Print data:', {
                businessInfo,
                dateRangeText,
                reportTitle,
                selectedHeader: getSelectedHeaderType()
            });

            // Clone and clean table
            let tableClone = mainTable.cloneNode(true);
            if (tableClone.tagName.toLowerCase() === 'div') {
                const tableElement = tableClone.querySelector('table');
                if (tableElement) {
                    tableClone = tableElement.cloneNode(true);
                }
            }

            // Remove hidden rows
            const hiddenRows = tableClone.querySelectorAll(
                'tbody tr[style*="display: none"], tbody tr.hidden');
            hiddenRows.forEach(row => row.remove());

            // Generate dynamic header HTML
            const headerHTML = generateHeaderHTML(businessInfo, reportTitle, dateRangeText, extraFields);

            // Create print window
            const printWindow = window.open('', '_blank');
            if (!printWindow) {
                alert('Please allow pop-ups to print the report');
                return;
            }

            printWindow.document.write(`
    <!DOCTYPE html>
    <html>
    <head>
        <title>${reportTitle}</title>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"><\/script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"><\/script>
        <link href="https://fonts.googleapis.com/css2?family=Moul:wght@400&display=swap" rel="stylesheet">
        <style>
        #item_qrcode img {
            display: block;
            margin: auto;
        }
        
        .no-screen {
            display: none;
        }
        
        .page-footer {
            display: none;
        }
        
        /* Print Styles - exact copy from reportheader.blade.php */
        @media print {
            /* Page setup with page numbers */
            @page {
                margin: 10mm;
                @bottom-center {
                    content: "Page " counter(page) " of " counter(pages);
                    font-size: 10px;
                    color: #000;
                }
            }
            
            /* Page counter */
            body {
            counter-reset: page;
        }
        
            a {
                text-decoration: none;
                color: #000;
            }
            
            button {
                display: none;
            }
            
            .no-print {
                display: none;
            }
            
            #page-display {
                display: block !important;
            }

            /* Print Header Styles - smaller fonts for print */
            .print-header {
                margin-bottom: 16px !important;
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                background-color: #f8f9fa !important;
                padding: 12px !important;
                position: relative !important;
                border-radius: 0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                page-break-after: avoid !important;
                width: 100% !important;
        }

            .print-header-left {
                display: flex !important;
                align-items: center !important;
                justify-content: flex-start !important;
                text-align: left !important;
                z-index: 1 !important;
                flex: 1 !important;
                overflow: hidden !important;
                min-height: 40px !important;
                padding: 2px 0 !important;
        }

            .print-header-center {
                position: absolute !important;
                left: 50% !important;
                top: 50% !important;
                transform: translate(-50%, -50%) !important;
                text-align: center !important;
                z-index: 0 !important;
        }

            .print-header-right {
                flex: 1 !important;
                text-align: right !important;
                z-index: 1 !important;
        }

            .print-logo {
                max-height: 35px !important;
                max-width: 35px !important;
                width: 35px !important;
                height: 35px !important;
                margin-right: 10px !important;
                margin-top: 2px !important;
                margin-bottom: 2px !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                flex-shrink: 0 !important;
                object-fit: contain !important;
                object-position: center !important;
                display: block !important;
                page-break-inside: avoid !important;
            }

            .print-business-name {
                font-size: 18px !important;
                font-weight: 600 !important;
                color: #000 !important;
                text-align: left !important;
                margin-bottom: 2px !important;
            }

            .print-business-location {
                font-size: 12px !important;
                color: #000 !important;
                text-align: left !important;
                margin-top: 2px !important;
        }

            .print-page-number {
                font-size: 10px !important;
                color: #000 !important;
                text-align: left !important;
                margin-top: 2px !important;
                display: block !important;
        }

            .print-report-title {
                font-size: 20px !important;
                font-weight: 600 !important;
                margin-bottom: 5px !important;
                color: #000 !important;
                text-align: right !important;
        }

            .print-date-range {
                font-size: 12px !important;
                margin-top: 3px !important;
                color: #000 !important;
                text-align: right !important;
            }

            .print-meta-field {
                font-size: 12px !important;
                margin-top: 3px !important;
                color: #000 !important;
                text-align: right !important;
        }

            /* Show page footer in print */
            .no-screen {
                display: block !important;
        }

            /* Add page number at bottom */
            .page-footer {
                position: fixed;
                bottom: 10mm;
                left: 0;
                right: 0;
                text-align: center;
                font-size: 10px;
                color: #000;
                padding: 5px;
                z-index: 999;
            }
            
            .page-footer:after {
                content: "Page " counter(page);
            }
            
            tr {
                page-break-inside: avoid;
            }
            
            /* Mony Header Print Styles - exact copy from mony_report_header.blade.php */
            .mony-header-container {
                position: relative !important;
                display: flex !important;
                flex-direction: column !important;
                align-items: flex-start !important;
                page-break-inside: avoid !important;
                margin: 0 !important;
                padding: 10px 0 !important;
                overflow: visible !important;
            }

            .mony-logo {
                position: static !important;
                align-self: flex-start !important;
                margin-left: 15px !important;
                margin-bottom: 10px !important;
                max-height: 100px !important;
                max-width: 100px !important;
                width: 100px !important;
                height: 100px !important;
                object-fit: contain !important;
                object-position: center !important;
                page-break-inside: avoid !important;
                z-index: 1 !important;
            }

            .mony-content-section {
                width: 100% !important;
                text-align: center !important;
            }

            .mony-text-center {
                margin-left: 0 !important;
                padding-left: 0 !important;
                text-align: center !important;
            }

            .mony-business-name {
                font-family: "Moul", serif !important;
                font-weight: 400 !important;
                font-style: normal !important;
                color: #000 !important;
                font-size: 22px !important;
                margin: 10px 0 5px 0 !important;
            }

            .mony-report-title {
                font-family: "Moul", serif !important;
                font-weight: 400 !important;
                font-style: normal !important;
                color: #000 !important;
                font-size: 16px !important;
                margin: 15px 0 10px 0 !important;
            }

            .mony-text-xs {
                font-size: 9px !important;
                color: #000 !important;
                margin: 2px 0 !important;
            }

            .mony-tax-invoice {
                font-size: 16px !important;
                color: #000 !important;
                margin: 3px 0 0 0 !important;
            }
        }

        @media screen {
            #page-display {
                display: none;
            }
        }

        /* Responsive Print Styles - exact copy from reportheader.blade.php */
        
        /* Small print area (A5, Receipt printers) */
        @media print and (max-width: 148mm) {
        @page {
                size: portrait;
                margin: 5mm;
            }
            
            .print-header {
                padding: 6px !important;
                flex-direction: column !important;
                align-items: center !important;
                text-align: center !important;
            }

            .print-header-left,
            .print-header-right {
                flex: none !important;
                width: 100% !important;
                text-align: center !important;
            }

            .print-header-center {
                position: static !important;
                transform: none !important;
                margin: 5px 0 !important;
            }

            .print-logo {
                max-height: 22px !important;
                max-width: 22px !important;
                width: 22px !important;
                height: 22px !important;
                margin: 2px 0 4px 0 !important;
            }

            .print-business-name {
                font-size: 12px !important;
            }

            .print-business-location {
                font-size: 8px !important;
            }

            .print-page-number {
                font-size: 7px !important;
            }

            .print-report-title {
                font-size: 14px !important;
            }

            .print-date-range,
            .print-meta-field {
                font-size: 8px !important;
            }

            #item_qrcode {
                width: 30px !important;
                height: 30px !important;
            }

            /* Mony Header Small Print Styles */
            .mony-header-container {
                flex-direction: column !important;
                align-items: flex-start !important;
            }

            .mony-logo {
                position: static !important;
                align-self: flex-start !important;
                margin-left: 10px !important;
                margin-bottom: 8px !important;
                max-height: 60px !important;
                max-width: 60px !important;
                width: 80px !important;
                height: 80px !important;
            }

            .mony-content-section {
                width: 100% !important;
                text-align: center !important;
            }

            .mony-text-center {
                margin-left: 0 !important;
            }

            .mony-business-name {
                font-size: 16px !important;
            }

            .mony-report-title {
                font-size: 12px !important;
            }

            .mony-text-xs {
                font-size: 7px !important;
            }

            .mony-tax-invoice {
                font-size: 12px !important;
            }
        }

        /* Medium print area (A4 Portrait) */
        @media print and (min-width: 149mm) and (max-width: 210mm) and (orientation: portrait) {
            @page {
                size: A4 portrait;
                margin: 8mm;
            }
            
            .print-header {
                padding: 8px !important;
            }

            .print-logo {
                max-height: 28px !important;
                max-width: 28px !important;
                width: 28px !important;
                height: 28px !important;
                margin-right: 8px !important;
            }

            .print-business-name {
                font-size: 14px !important;
            }

            .print-business-location {
                font-size: 10px !important;
            }

            .print-page-number {
                font-size: 8px !important;
            }

            .print-report-title {
                font-size: 16px !important;
            }

            .print-date-range,
            .print-meta-field {
                font-size: 10px !important;
            }

            #item_qrcode {
                width: 40px !important;
                height: 40px !important;
            }

            /* Mony Header Medium Print Styles */
            .mony-logo {
                margin-left: 12px !important;
                margin-bottom: 8px !important;
                max-height: 70px !important;
                max-width: 70px !important;
                width: 70px !important;
                height: 70px !important;
            }

            .mony-business-name {
                font-size: 18px !important;
            }

            .mony-report-title {
                font-size: 14px !important;
            }

            .mony-text-xs {
                font-size: 8px !important;
            }

            .mony-tax-invoice {
                font-size: 14px !important;
            }
        }

        /* Large print area (A4 Landscape, A3) */
        @media print and (min-width: 210mm) {
            @page {
                size: A4 landscape;
                margin: 10mm;
            }
            
            .print-header {
                padding: 12px !important;
            }

            .print-logo {
                max-height: 38px !important;
                max-width: 38px !important;
                width: 38px !important;
                height: 38px !important;
                margin-right: 12px !important;
            }

            .print-business-name {
                font-size: 18px !important;
            }

            .print-business-location {
                font-size: 12px !important;
            }

            .print-page-number {
                font-size: 10px !important;
            }

            .print-report-title {
                font-size: 20px !important;
            }

            .print-date-range,
            .print-meta-field {
                font-size: 12px !important;
            }

            #item_qrcode {
                width: 50px !important;
                height: 50px !important;
            }

            /* Mony Header Large Print Styles */
            .mony-logo {
                margin-left: 20px !important;
                margin-bottom: 10px !important;
                max-height: 90px !important;
                max-width: 90px !important;
                width: 90px !important;
                height: 90px !important;
            }

            .mony-business-name {
                font-size: 22px !important;
            }

            .mony-report-title {
                font-size: 16px !important;
            }

            .mony-text-xs {
                font-size: 9px !important;
            }

            .mony-tax-invoice {
                font-size: 16px !important;
            }
        }

        /* Extra large print area (A3 Landscape, larger formats) */
        @media print and (min-width: 297mm) {
            @page {
                size: A3 landscape;
                margin: 15mm;
            }
            
            .print-header {
                padding: 15px !important;
            }

            .print-logo {
                max-height: 45px !important;
                max-width: 45px !important;
                width: 45px !important;
                height: 45px !important;
                margin-right: 15px !important;
            }

            .print-business-name {
                font-size: 22px !important;
            }

            .print-business-location {
                font-size: 14px !important;
            }

            .print-page-number {
                font-size: 12px !important;
            }

            .print-report-title {
                font-size: 24px !important;
            }

            .print-date-range,
            .print-meta-field {
                font-size: 14px !important;
            }

            #item_qrcode {
                width: 60px !important;
                height: 60px !important;
            }

            /* Mony Header Extra Large Print Styles */
            .mony-logo {
                margin-left: 25px !important;
                margin-bottom: 12px !important;
                max-height: 110px !important;
                max-width: 110px !important;
                width: 110px !important;
                height: 110px !important;
            }

            .mony-business-name {
                font-size: 26px !important;
            }

            .mony-report-title {
                font-size: 18px !important;
            }

            .mony-text-xs {
                font-size: 10px !important;
            }

            .mony-tax-invoice {
                font-size: 18px !important;
            }
        }

        /* Fallback for very small print areas (thermal printers, labels) */
        @media print and (max-width: 80mm) {
            @page {
                size: portrait;
                margin: 2mm;
            }
            
            .print-header {
                padding: 4px !important;
                flex-direction: column !important;
                align-items: center !important;
                text-align: center !important;
            }

            .print-header-left,
            .print-header-right,
            .print-header-center {
                width: 100% !important;
                text-align: center !important;
                margin: 2px 0 !important;
            }

            .print-header-center {
                position: static !important;
                transform: none !important;
            }

            .print-logo {
                max-height: 18px !important;
                max-width: 18px !important;
                width: 18px !important;
                height: 18px !important;
                margin: 1px 0 2px 0 !important;
            }

            .print-business-name {
                font-size: 10px !important;
                font-weight: bold !important;
            }

            .print-business-location {
                font-size: 7px !important;
            }

            .print-page-number {
                font-size: 6px !important;
            }

            .print-report-title {
                font-size: 12px !important;
                font-weight: bold !important;
            }

            .print-date-range,
            .print-meta-field {
                font-size: 7px !important;
                line-height: 1.2 !important;
            }

            #item_qrcode {
                width: 25px !important;
                height: 25px !important;
            }

            /* Mony Header Thermal Print Styles */
            .mony-header-container {
                flex-direction: column !important;
                align-items: flex-start !important;
            }

            .mony-logo {
                position: static !important;
                align-self: flex-start !important;
                margin-left: 5px !important;
                margin-bottom: 6px !important;
                max-height: 40px !important;
                max-width: 40px !important;
                width: 40px !important;
                height: 40px !important;
            }

            .mony-content-section {
                width: 100% !important;
                text-align: center !important;
            }

            .mony-business-name {
                font-size: 14px !important;
                margin: 5px 0 !important;
            }

            .mony-report-title {
                font-size: 12px !important;
                margin: 8px 0 !important;
            }

            .mony-text-xs {
                font-size: 7px !important;
                margin: 1px 0 !important;
            }

            .mony-tax-invoice {
                font-size: 10px !important;
            }
        }

        /* High DPI print adjustments */
        @media print and (min-resolution: 300dpi) {
            #item_qrcode img {
                image-rendering: -webkit-optimize-contrast !important;
                image-rendering: crisp-edges !important;
            }
        }
        </style>
    </head>
    <body style="font-family: Roboto, sans-serif; margin: 16px; padding: 0; color: #333; counter-reset: page;">
        <button style="display: block; margin: 16px auto; padding: 8px 16px; background-color: #0f8800; color: white; border: none; border-radius: 3.2px; font-size: 11.2px; cursor: pointer;" onclick="handleManualPrint()">Print Report</button>

        ${headerHTML}

        ${additionalFilters ? `<div style="text-align: center; margin: 8px 0; font-size: 11.2px;">${additionalFilters}</div>` : ''}

        <div style="width: 100%; border-collapse: collapse; font-size: 8.8px; margin-top: 16px;">
            ${tableClone.outerHTML.replace(/<table/g, '<table style="width: 100%; border-collapse: collapse; font-size: 8.8px;"').replace(/<th/g, '<th style="background-color: #f8f9fa; border: 0.8px solid #dee2e6; padding: 6.4px; text-align: left; font-weight: bold; font-size: 9.6px;"').replace(/<td/g, '<td style="border: 0.8px solid #dee2e6; padding: 6.4px; text-align: left;"')}
        </div>

        <script>
            function updatePageCounter() {
                const pageHeight = 1056;
                const headerHeight = document.querySelector('.report-header, .header-container') ? 
                    document.querySelector('.report-header, .header-container').offsetHeight : 0;
                const contentHeight = document.body.scrollHeight;
                const estimatedPages = Math.ceil(contentHeight / pageHeight);

                const pageDisplay = document.getElementById('page-display');
                if (pageDisplay) {
                    pageDisplay.textContent = estimatedPages > 1 ? 'Page 1 of ' + estimatedPages : 'Page 1';
                }
            }

            // Handle manual print button click
            function handleManualPrint() {
                printDialogOpened = true;
                window.print();
            }

            // Variables to track print status
            let printDialogOpened = false;
            let printCompleted = false;

            // Helper functions from reportheader.blade.php
            function formatDate(dateStr) {
                if (!dateStr) return '';
                const date = typeof dateStr === 'string' ? new Date(dateStr) : dateStr;
                if (isNaN(date.getTime())) return 'Invalid Date';
                return date.getDate().toString().padStart(2, '0') + '/' +
                    (date.getMonth() + 1).toString().padStart(2, '0') + '/' +
                    date.getFullYear();
            }

            function calculateDaysBetween(start, end) {
                const startDate = new Date(start);
                const endDate = new Date(end);
                const diffTime = Math.abs(endDate - startDate);
                return Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            }

            function updateHeaderDateRange(start, end) {
                const dateRangeElement = document.getElementById('report-date-range');
                if (!dateRangeElement) return;
                
                if (start && end) {
                    const startFormatted = formatDate(start);
                    const endFormatted = formatDate(end);
                    const daysBetween = calculateDaysBetween(start, end);
                    dateRangeElement.textContent = 'Date: ' + startFormatted + ' - ' + endFormatted + ' (' + daysBetween + ' days)';
                } else {
                    dateRangeElement.textContent = 'Date: All Dates';
                }
            }

            window.onload = function() {
                setTimeout(function() {
                    updatePageCounter();
                }, 50);

                // QR Code Generation - exact copy from reportheader.blade.php
                if (typeof QRCode !== 'undefined') {
                try {
                        const reportLink = "${reportLink}";
                        const qrCodeElement = document.getElementById("item_qrcode");
                    
                        if (qrCodeElement) {
                            const qrCodeOptions = {
                                text: reportLink,
                        margin: 2,
                                width: 50,
                                height: 50,
                        quietZone: 5,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                    };

                            new QRCode(qrCodeElement, qrCodeOptions);
                    }
                } catch (e) {
                        console.log("QR Code generation failed:", e);
                    }
                }

                // Auto-trigger print dialog
                setTimeout(function() {
                    printDialogOpened = true;
                    window.print();
                }, 200);

                // Backup close mechanism - close window after 30 seconds if still open
                setTimeout(function() {
                    if (!printCompleted) {
                        window.close();
                    }
                }, 30000);
            };

            window.onresize = function() {
                updatePageCounter();
            };

            // Handle print completion (when user clicks Print or Cancel)
            window.onafterprint = function() {
                printCompleted = true;
                setTimeout(function() {
                    window.close();
                }, 100);
            };

            // Handle window focus to detect print dialog closure
            let windowFocused = false;
            window.onfocus = function() {
                if (printDialogOpened && !windowFocused) {
                    windowFocused = true;
                    // Give a small delay to ensure print dialog has properly closed
                    setTimeout(function() {
                        if (!printCompleted) {
                            // If we regain focus but print wasn't completed, user likely cancelled
                            window.close();
                        }
                    }, 500);
                }
            };

            // Additional fallback - detect if user navigates away or closes
            window.onbeforeunload = function() {
                printCompleted = true;
            };

            // Detect print dialog cancellation through media query changes
            if (window.matchMedia) {
                var mediaQueryList = window.matchMedia('print');
                mediaQueryList.addListener(function(mql) {
                    if (!mql.matches && printDialogOpened) {
                        // Print dialog was closed
                        setTimeout(function() {
                            if (!printCompleted) {
                                window.close();
                            }
                        }, 100);
                    }
                });
            }
        <\/script>
    </body>
    </html>
`);

            function updateHeaderDateRange(start, end) {
                console.log('Updating date range:', {
                    start,
                    end
                }); // Debug log
                const dateRangeElement = document.getElementById('report-date-range');
                if (!dateRangeElement) {
                    console.error('report-date-range element not found');
                    return;
                }
                if (start && end) {
                    const startFormatted = formatDate(start);
                    const endFormatted = formatDate(end);
                    dateRangeElement.textContent = `Date: ${startFormatted} - ${endFormatted}`;
                } else {
                    dateRangeElement.textContent = 'All Dates';
                }
            }

            printWindow.document.close();
            // Listen for date filter changes
            document.addEventListener('dateRangeChanged', function(e) {
                console.log('dateRangeChanged event:', e.detail); // Debug log
                updateHeaderDateRange(e.detail.startDate, e.detail.endDate);
            });

            // Initial date range setup
            document.addEventListener('DOMContentLoaded', function() {
                const startDate = window.selectedStartDate || localStorage.getItem(
                    'selectedStartDate') || '';
                const endDate = window.selectedEndDate || localStorage.getItem(
                    'selectedEndDate') || '';
                console.log('Initial date range:', {
                    startDate,
                    endDate
                }); // Debug log
                updateHeaderDateRange(startDate, endDate);
            });
        });
    });
</script>