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
    <span id="print-by">{{ $print_by ?? 'N/A' }}</span>
    <span id="assign-to">{{ $assign_to ?? 'N/A' }}</span>
    <span id="report-name">{{ $report_name }}@if ($report_name_tail)
            <span>{{ $report_name_tail }}</span>
        @endif
    </span>
    <span id="business-phone-number">{{ $businessInfo['phone_number'] ?? '' }}</span>
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
    // Helper function to format date
    function formatDate(dateStr) {
        if (!dateStr) return '';
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-GB'); // Format as DD/MM/YYYY
    }

    // Update dates when filter changes
    window.updatePrintDates = function(startDate, endDate) {
        window.selectedStartDate = startDate;
        window.selectedEndDate = endDate;
        // Update the report date range in the print view
        const dateRangeElement = document.getElementById('report-date-range');
        if (dateRangeElement) {
            if (startDate && endDate) {
                dateRangeElement.textContent = `Date: ${formatDate(startDate)} - ${formatDate(endDate)}`;
            } else {
                dateRangeElement.textContent = 'All Dates';
            }
        }
    };

    // Initialize dates from URL parameters or defaults
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const startDate = urlParams.get('start_date') || window.selectedStartDate;
        const endDate = urlParams.get('end_date') || window.selectedEndDate;
        window.updatePrintDates(startDate, endDate);
    });
</script>

<script>
    const reportLink = window.location.href;

    // Global function to update current dates (called from header script)
    window.updatePrintDates = function(startDate, endDate) {
        const currentStartElement = document.getElementById('current-start-date');
        const currentEndElement = document.getElementById('current-end-date');

        if (currentStartElement) {
            currentStartElement.innerText = startDate || '';
        }
        if (currentEndElement) {
            currentEndElement.innerText = endDate || '';
        }

      
    };

    document.addEventListener('DOMContentLoaded', function() {
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
                name: document.getElementById('business-name')?.innerText || '',
                logo: document.getElementById('business-logo')?.innerText || '',
                location: document.getElementById('business-location')?.innerText || '',
                phone_number: document.getElementById('business-phone-number')?.innerText || '',

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
                reportTitle
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

            // Remove fixed widths so all columns can shrink to fit
            tableClone.querySelectorAll('table, colgroup, col, thead th, thead td, tbody th, tbody td')
                .forEach(el => {
                    el.removeAttribute('width');
                    if (el.style) {
                        el.style.width = '';
                        el.style.minWidth = '';
                        el.style.maxWidth = '';
                        el.style.whiteSpace = '';
                    }
                });
            tableClone.classList.remove('dataTable');

            // Unhide any hidden columns from responsive/DataTables or utility classes
            tableClone.querySelectorAll('thead th, thead td, tbody th, tbody td').forEach(el => {
                const style = (el.getAttribute('style') || '').toLowerCase();
                if (style.includes('display:none') || style.includes('display: none')) {
                    el.setAttribute('style', style.replace(/display\s*:\s*none\s*;?/g, ''));
                }
                // Remove common hidden classes
                el.classList.remove('d-none', 'hidden', 'dtr-hidden', 'dt-hidden', 'hide',
                    'sr-only', 'hidden-xs', 'hidden-sm', 'hidden-md', 'hidden-lg');
                // Also strip any class that starts with hidden-
                const classesToRemove = Array.from(el.classList).filter(c => c.startsWith(
                    'hidden-'));
                classesToRemove.forEach(c => el.classList.remove(c));
            });

            // Estimate column count to decide if we should prefer landscape
            let colCount = 0;
            const headerCells = tableClone.querySelectorAll('thead th, thead td');
            if (headerCells && headerCells.length) {
                colCount = headerCells.length;
            } else {
                const firstRow = tableClone.querySelector('tr');
                if (firstRow) colCount = firstRow.children.length;
            }
            const preferLandscape = colCount >= 10;

            // Build extra fields HTML
            let extraFieldsHtml = '';
            Object.keys(extraFields).forEach(key => {
                if (extraFields[key] !== '') {
                    const fieldName = key.replace(/_/g, ' ').replace(/\b\w/g, l => l
                        .toUpperCase());
                    extraFieldsHtml +=
                        `<div class="date-range">${fieldName}: ${extraFields[key]}</div>`;
                }
            });

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
        <style>
        ${preferLandscape ? '@page { size: A4 landscape; }' : ''}
           body {
            font-family: Roboto, sans-serif;
            margin: 16px;
            padding: 0;
            color: #333;
            counter-reset: page;
        }
        .report-header {
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f8f9fa;
            padding: 12px;
            position: relative;
        }
        .header-left {
            display: flex;
            align-items: center;
            z-index: 1;
            flex: 1;
        }
        .business-logo {
            max-height: 40px;
            max-width: 40px;
            margin-right: 12px;
        }
        .business-name {
            font-size: 12.8px;
            font-weight: bold;
        }
        .business-location {
            font-size: 8.8px;
            color: #666;
        }
        .page-number {
            font-size: 8.8px;
            color: #666;
            margin-top: 2px;
        }
        .header-right {
            text-align: right;
            z-index: 1;
            flex: 1;
        }
        .report-name {
            font-size: 11.2px;
            font-weight: bold;
            margin-bottom: 4px;
        }
        .date-range {
            font-size: 8.8px;
            margin-top: 4px;
        }
        .bold-name {
            font-weight: bold;
        }
        .header-center {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            z-index: 0;
        }
        #item_qrcode img {
            display: block;
            margin: auto;
        }
        .additional-info {
            text-align: center;
            margin: 8px 0;
            font-size: 11.2px;
        }
        .print-button-window {
            display: block;
            margin: 16px auto;
            padding: 8px 16px;
            background-color: #0f8800;
            color: white;
            border: none;
            border-radius: 3.2px;
            font-size: 11.2px;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.8px;
            margin-top: 16px;
            table-layout: fixed;
        }
        table th {
            background-color: #f8f9fa;
            border: 0.8px solid #dee2e6;
            padding: 6.4px;
            text-align: left;
            font-weight: bold;
            font-size: 9.6px;
        }
        table td {
            border: 0.8px solid #dee2e6;
            padding: 6.4px;
            text-align: left;
            word-break: break-word;
            overflow-wrap: anywhere;
            white-space: normal;
            vertical-align: top;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

/* Signature Section */
.signature-block {
    margin-top: 80px;
    display: flex;
    justify-content: flex-end;
}

.signature {
    text-align: center;
    font-size: 9.6px;
    width: 200px;
}

.signature-line {
    display: block;
    margin-bottom: 4px;
}

.signature-line::before {
    content: "____________________";
}

.signature-label {
    margin-bottom: 4px;
}

.signature-name,
.signature-title,
.signature-date {
    margin: 2px 0;
}




        @media print {
            a {
                text-decoration: none;
                color: #000;
            }
            .print-button-window {
                display: none;
            }
            body {
                margin: 0;
                padding: 0;
                overflow: visible !important;
            }
            table th {
                background-color: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            table tr:nth-child(even) {
                background-color: #f9f9f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .report-header {
                background-color: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            tr {
                page-break-inside: avoid;
            }
            .no-print {
                display: none;
            }
             .page-number {
                display: block;
            }
            
            /* Ensure long content wraps in cells on print */
            table th, table td {
                word-break: break-word !important;
                overflow-wrap: anywhere !important;
                white-space: normal !important;
                vertical-align: top !important;
                line-height: 1.35 !important;
                display: table-cell !important;
            }

            /* Force common hidden utility classes to show in print window */
            .d-none, .hidden, .dtr-hidden, .dt-hidden, .hide, .sr-only,
            .hidden-xs, .hidden-sm, .hidden-md, .hidden-lg,
            [class*="hidden-"] {
                display: table-cell !important;
            }

            /* If any inline style still hides the cell, override it */
            [style*="display:none"], [style*="display: none"] {
                display: table-cell !important;
            }

            #table-wrapper { overflow: visible !important; }

            /* Normalize table display for print to prevent framework overrides */
            table { display: table !important; width: 100% !important; max-width: 100% !important; }
            thead { display: table-header-group !important; }
            tbody { display: table-row-group !important; }
            tr { display: table-row !important; }
          
        }

        @media screen {
            .page-number {
                display: none;
            }
        }

        #table-wrapper { width: 100%; }

        /* Responsive table sizing for different print page widths */
        @media print and (max-width: 148mm) {
            table { font-size: 7.2px !important; }
            table th { font-size: 8.2px !important; padding: 4px !important; }
            table td { padding: 4px !important; }
        }

        @media print and (min-width: 149mm) and (max-width: 210mm) and (orientation: portrait) {
            table { font-size: 8.2px !important; }
            table th { font-size: 9.2px !important; padding: 6px !important; }
            table td { padding: 6px !important; }
        }

        @media print and (min-width: 210mm) {
            table { font-size: 9px !important; }
            table th { font-size: 10px !important; padding: 7px !important; }
            table td { padding: 7px !important; }
        }

        @page {
            margin: 20mm 15mm 25mm 15mm;
            counter-increment: page;
            @bottom-center {
                content: "Page " counter(page);
                font-size: 8px;
            }
        }
        </style>
    </head>
    <body>
        <button class="print-button-window" onclick="handleManualPrint()">Print Report</button>

        <div class="report-header">
            <div class="header-left">
                ${businessInfo.logo ? `
                      <img src="${businessInfo.logo}"
                           alt="${businessInfo.name} Logo"
                           class="business-logo"
                           onerror="this.style.display='none';">
                  ` : ''}
               <div>
                <div class="business-name">${businessInfo.name}</div>
                <div class="business-location">${businessInfo.location}</div>
                <div class="business-location">${businessInfo.phone_number}</div>
            </div>
            </div>

            <div class="header-center">
                <div id="item_qrcode"></div>
            </div>

            <div class="header-right">
                <div class="report-name">${reportTitle}</div>
                  <div class="date-range" id="report-date-range">
                     ${dateRangeText.startsWith('Date:') ? dateRangeText : `Date: ${dateRangeText}`}
                </div>
               
                ${businessInfo.print_by && businessInfo.print_by !== 'N/A' ? 
                    `<div class="date-range">Printed by: <span class="bold-name">${businessInfo.print_by}</span></div>` : ''}
                ${businessInfo.assign_to && businessInfo.assign_to !== 'N/A' ? 
                    `<div class="date-range">Assigned to: <span class="bold-name">${businessInfo.assign_to}</span></div>` : ''}
                ${extraFieldsHtml}
                 
                <div class="date-range">Printed on: ${new Date().toLocaleDateString()} ${new Date().toLocaleTimeString()}</div>
            </div>
        </div>

        ${additionalFilters ? `<div class="additional-info">${additionalFilters}</div>` : ''}

      <div id="table-wrapper">
        ${tableClone.outerHTML
            .replace(/<table/gi, '<table style="width: 100%; border-collapse: collapse; table-layout: fixed; font-size: 8.8px;"')
            .replace(/<th/gi, '<th style="background-color: #f8f9fa; border: 0.8px solid #dee2e6; padding: 6.4px; text-align: left; font-weight: bold; font-size: 9.6px; word-break: break-word; overflow-wrap: anywhere; white-space: normal; vertical-align: top;"')
            .replace(/<td/gi, '<td style="border: 0.8px solid #dee2e6; padding: 6.4px; text-align: left; word-break: break-word; overflow-wrap: anywhere; white-space: normal; vertical-align: top;"')
        }
      </div>

<div class="signature-block">
    <div class="signature">
        <div class="signature-line"></div>
        <div class="signature-name">
              ${businessInfo.print_by !== 'N/A' ? businessInfo.print_by : 'Authorized Signature'}
        </div>
        <div class="signature-date"> ${new Date().toLocaleDateString()}</div>
    </div>
</div>



        <script>
            function updatePageCounter() {
                const pageHeight = 1056;
                const headerHeight = document.querySelector('.report-header') ? document.querySelector('.report-header').offsetHeight : 0;
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

            window.onload = function() {
                setTimeout(function() {
                    updatePageCounter();
                }, 50);

                // QR Code Generation (ensure single instance)
                try {
                    var itemLink = "${reportLink}";
                    var itemOpts = {
                        text: itemLink,
                        margin: 2,
                        width: 80,
                        height: 80,
                        quietZone: 5,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                    };

                    var qrEl = document.getElementById("item_qrcode");
                    if (qrEl) {
                        // Clear any previous content and guard against duplicate render
                        qrEl.innerHTML = '';
                        if (!window.__qrRendered) {
                            new QRCode(qrEl, itemOpts);
                            window.__qrRendered = true;
                        }
                        // Deduplicate if library inserted multiple nodes
                        while (qrEl.childNodes && qrEl.childNodes.length > 1) {
                            qrEl.removeChild(qrEl.lastChild);
                        }
                    } else {
                        console.error("QR Code element 'item_qrcode' not found.");
                    }
                } catch (e) {
                    console.error("Error generating QR Code:", e);
                }

                // Fit table to printable width if overflowing, then trigger print
                try {
                    const wrapper = document.getElementById('table-wrapper');
                    const tableEl = wrapper ? wrapper.querySelector('table') : null;
                    const fit = () => {
                        if (!wrapper || !tableEl) return;
                        const bodyWidth = document.body.clientWidth;
                        const tableWidth = tableEl.scrollWidth;
                        if (tableWidth > 0 && bodyWidth > 0 && tableWidth > bodyWidth) {
                            const scale = bodyWidth / tableWidth;
                            wrapper.style.transform = 'scale(' + scale + ')';
                            wrapper.style.transformOrigin = 'top left';
                            wrapper.style.width = (100 / scale) + '%';
                        }
                    };
                    fit();
                    setTimeout(fit, 50);
                } catch (e) { /* ignore */ }

                // Auto-trigger print dialog
                setTimeout(function() {
                    printDialogOpened = true;
                    window.print();
                }, 400);

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
