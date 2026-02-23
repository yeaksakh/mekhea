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
                return '';
            }
            return date('d/m/Y', $timestamp);
        }
    }

    $days_between = 0;
    $start_date = $start_date ?? null;
    $end_date = $end_date ?? null;

    if ($start_date && $end_date) {
        $start_timestamp = strtotime($start_date);
        $end_timestamp = strtotime($end_date);
        if ($start_timestamp !== false && $end_timestamp !== false) {
            $days_between = round(($end_timestamp - $start_timestamp) / (60 * 60 * 24)) + 1;
        }
    }

    // Debug information (can be removed in production)
    // echo "<!-- Debug: start_date = " . ($start_date ?? 'null') . ", end_date = " . ($end_date ?? 'null') . ", days_between = " . $days_between . " -->";

    // Default values for optional parameters
    $print_by = $print_by ?? null;
    $assign_to = $assign_to ?? null;
    $extra_fields = $extra_fields ?? [];
    $report_name_tail = $report_name_tail ?? [];
    $businessInfo = $businessInfo ?? [];
@endphp

<div class="report-header">
    <div class="header-left">
        @if ($businessInfo['logo_exists'])
            <img src="{{ $businessInfo['logo_url'] }}" class="business-logo"
                alt="{{ $businessInfo['name'] ?? 'Business' }} Logo"
                style="height: 50px; width: 50px; object-fit: contain;">
        @endif
        <div>
            <div class="business-name">{{ $businessInfo['name'] ?? 'Business Name' }}</div>
            <div class="business-location">{{ $businessInfo['location'] }}</div>
           <div class="business-location">{{ $businessInfo['phone_number'] ?? 'Phone not available' }}</div>

        </div>
    </div>

    <div class="header-center">
        <div id="item_qrcode"></div>
    </div>

    <div class="header-right">
        <div class="report-name">{{ $report_name ?? 'Report' }}
            @if ($report_name_tail)
                <span>{{ $report_name_tail }}</span>
            @endif
        </div>
        <div class="date-range" id="report-date-range">
            @if (isset($start_date) && isset($end_date) && $start_date && $end_date)
                {{ __('Date') }}: {{ formatDate($start_date) }} - {{ formatDate($end_date) }} ({{ $days_between }}
                days)
            @else
                All Dates
            @endif
        </div>
        @if ($print_by)
            <div class="date-range">{{ __('Printed by') }}: <span class="bold-name">{{ $print_by }}</span></div>
        @endif
        @if ($assign_to)
            <div class="date-range">{{ __('Assigned to') }}: <span class="bold-name">{{ $assign_to }}</span></div>
        @endif
        @foreach ($extra_fields as $key => $value)
            <div class="report-meta extra-field">{{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $value }}</div>
        @endforeach

        <div class="date-range">{{ __('Printed on') }}: {{ date('Y-m-d H:i:s') }}</div>

    </div>
</div>

{{-- Page footer for print page numbers --}}
<div class="page-footer no-screen"></div>

<style>
    .report-header {
        margin-bottom: 20px;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 4px;
        position: relative;
    }

    .header-left {
        display: flex;
        align-items: center;
        flex: 1;
        z-index: 1;
    }

    .header-center {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        z-index: 0;
    }

    .header-right {
        flex: 1;
        text-align: right;
        z-index: 1;
    }

    .business-logo {
        max-height: 50px;
        max-width: 50px;
        margin-right: 15px;
    }

    .business-name {
        font-size: 20px;
        font-weight: 600;
    }

    .business-location {
        font-size: 14px;
        color: #666;
        margin-top: 2px;
    }

    .page-number {
        font-size: 12px;
        color: #666;
        margin-top: 2px;
        display: none;
    }

    .report-name {
        font-size: 22px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .date-range {
        font-size: 14px;
        margin-top: 5px;
    }

    .date-printed {
        font-style: italic;
        color: #666;
    }

    .report-meta {
        font-size: 14px;
        margin-top: 5px;
    }

    .print-by,
    .assign-to,
    .extra-field {
        color: #333;
    }

    .bold-name {
        font-weight: bold;
    }

    .no-screen {
        display: none;
    }

    #item_qrcode img {
        display: block;
        margin: auto;
    }

    /* Print Styles */
    @media print {

        /* Page setup with page numbers */
        @page {
            margin: 20mm 15mm 25mm 15mm;
        }

        /* Page counter */
        body {
            counter-reset: page;
        }

        a {
            text-decoration: none;
            color: #000;
        }

        .print-button-window {
            display: none;
        }

        .no-print {
            display: none;
        }

        .page-number {
            display: block;
        }

        .report-header {
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

        .header-left {
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

        .header-left>div {
            text-align: left !important;
        }

        .header-center {
            position: absolute !important;
            left: 50% !important;
            top: 50% !important;
            transform: translate(-50%, -50%) !important;
            text-align: center !important;
            z-index: 0 !important;
        }

        .header-right {
            flex: 1 !important;
            text-align: right !important;
            z-index: 1 !important;
        }

        .business-logo {
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

        .business-name {
            font-size: 18px !important;
            font-weight: 600 !important;
            color: #000 !important;
            text-align: left !important;
            margin-bottom: 2px !important;
        }

        .business-location {
            font-size: 12px !important;
            color: #000 !important;
            text-align: left !important;
            margin-top: 2px !important;
        }

        .page-number {
            font-size: 10px !important;
            color: #000 !important;
            text-align: left !important;
            margin-top: 2px !important;
            display: block !important;
        }

        .report-name {
            font-size: 20px !important;
            font-weight: 600 !important;
            margin-bottom: 5px !important;
            color: #000 !important;
            text-align: right !important;
        }

        .date-range {
            font-size: 12px !important;
            margin-top: 3px !important;
            color: #000 !important;
            text-align: right !important;
        }

        .date-printed {
            font-style: italic !important;
            color: #000 !important;
            text-align: right !important;
        }

        .report-meta {
            font-size: 12px !important;
            margin-top: 3px !important;
            color: #000 !important;
            text-align: right !important;
        }

        .print-by,
        .assign-to,
        .extra-field {
            color: #000 !important;
            text-align: right !important;
        }

        .bold-name {
            font-weight: bold !important;
            color: #000 !important;
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
    }

    /* Responsive Print Styles */

    /* Small print area (A5, Receipt printers) */
    @media print and (max-width: 148mm) {
        @page {
            size: portrait;
            margin: 5mm;
        }

        .report-header {
            flex-direction: column !important;
            align-items: center !important;
            text-align: center !important;
            padding: 6px !important;
            gap: 8px !important;
        }

        .header-left,
        .header-right {
            flex: none !important;
            width: 100% !important;
            text-align: center !important;
            max-width: none !important;
        }

        .header-left {
            flex-direction: column !important;
            align-items: center !important;
        }

        .header-center {
            position: static !important;
            transform: none !important;
            margin: 5px 0 !important;
        }

        .business-logo {
            max-height: 22px !important;
            max-width: 22px !important;
            width: 22px !important;
            height: 22px !important;
            margin: 2px 0 4px 0 !important;
            object-fit: contain !important;
            object-position: center !important;
            display: block !important;
            page-break-inside: avoid !important;
        }

        .business-name {
            font-size: 12px !important;
            margin-bottom: 2px !important;
        }

        .business-location {
            font-size: 8px !important;
            margin-top: 1px !important;
        }

        .page-number {
            font-size: 7px !important;
            margin-top: 1px !important;
        }

        .report-name {
            font-size: 14px !important;
            margin-bottom: 3px !important;
        }

        .date-range,
        .report-meta {
            font-size: 8px !important;
            margin-top: 2px !important;
        }

        #item_qrcode {
            width: 30px !important;
            height: 30px !important;
        }

        #item_qrcode img {
            width: 30px !important;
            height: 30px !important;
        }
    }

    /* Medium print area (A4 Portrait) */
    @media print and (min-width: 149mm) and (max-width: 210mm) and (orientation: portrait) {
        @page {
            size: A4 portrait;
            margin: 8mm;
        }

        .report-header {
            padding: 8px !important;
            flex-wrap: wrap !important;
        }

        .header-left {
            flex: 1 1 45% !important;
            min-width: 200px !important;
        }

        .header-right {
            flex: 1 1 45% !important;
            min-width: 200px !important;
        }

        .header-center {
            flex: 1 1 10% !important;
            min-width: 60px !important;
        }

        .business-logo {
            max-height: 28px !important;
            max-width: 28px !important;
            width: 28px !important;
            height: 28px !important;
            margin-right: 8px !important;
            margin-top: 2px !important;
            margin-bottom: 2px !important;
            object-fit: contain !important;
            object-position: center !important;
            display: block !important;
            page-break-inside: avoid !important;
        }

        .business-name {
            font-size: 14px !important;
        }

        .business-location {
            font-size: 10px !important;
        }

        .page-number {
            font-size: 8px !important;
        }

        .report-name {
            font-size: 16px !important;
        }

        .date-range,
        .report-meta {
            font-size: 10px !important;
        }

        #item_qrcode {
            width: 40px !important;
            height: 40px !important;
        }

        #item_qrcode img {
            width: 40px !important;
            height: 40px !important;
        }
    }

    /* Large print area (A4 Landscape, A3) */
    @media print and (min-width: 210mm) {
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        .report-header {
            padding: 12px !important;
        }

        .header-left {
            flex: 1 1 40% !important;
        }

        .header-right {
            flex: 1 1 40% !important;
        }

        .header-center {
            flex: 1 1 20% !important;
        }

        .business-logo {
            max-height: 38px !important;
            max-width: 38px !important;
            width: 38px !important;
            height: 38px !important;
            margin-right: 12px !important;
            margin-top: 2px !important;
            margin-bottom: 2px !important;
            object-fit: contain !important;
            object-position: center !important;
            display: block !important;
            page-break-inside: avoid !important;
        }

        .business-name {
            font-size: 18px !important;
        }

        .business-location {
            font-size: 12px !important;
        }

        .page-number {
            font-size: 10px !important;
        }

        .report-name {
            font-size: 20px !important;
        }

        .date-range,
        .report-meta {
            font-size: 12px !important;
        }

        #item_qrcode {
            width: 50px !important;
            height: 50px !important;
        }

        #item_qrcode img {
            width: 50px !important;
            height: 50px !important;
        }
    }

    /* Extra large print area (A3 Landscape, larger formats) */
    @media print and (min-width: 297mm) {
        @page {
            size: A3 landscape;
            margin: 15mm;
        }

        .report-header {
            padding: 15px !important;
        }

        .business-logo {
            max-height: 45px !important;
            max-width: 45px !important;
            width: 45px !important;
            height: 45px !important;
            margin-right: 15px !important;
            margin-top: 3px !important;
            margin-bottom: 3px !important;
            object-fit: contain !important;
            object-position: center !important;
            display: block !important;
            page-break-inside: avoid !important;
        }

        .business-name {
            font-size: 22px !important;
        }

        .business-location {
            font-size: 14px !important;
        }

        .page-number {
            font-size: 12px !important;
        }

        .report-name {
            font-size: 24px !important;
        }

        .date-range,
        .report-meta {
            font-size: 14px !important;
        }

        #item_qrcode {
            width: 60px !important;
            height: 60px !important;
        }

        #item_qrcode img {
            width: 60px !important;
            height: 60px !important;
        }
    }

    /* Fallback for very small print areas (thermal printers, labels) */
    @media print and (max-width: 80mm) {
        .report-header {
            flex-direction: column !important;
            align-items: center !important;
            text-align: center !important;
            padding: 4px !important;
        }

        .header-left,
        .header-right,
        .header-center {
            width: 100% !important;
            text-align: center !important;
            margin: 2px 0 !important;
        }

        .header-left {
            flex-direction: column !important;
            align-items: center !important;
        }

        .header-center {
            position: static !important;
            transform: none !important;
        }

        .business-logo {
            max-height: 18px !important;
            max-width: 18px !important;
            width: 18px !important;
            height: 18px !important;
            margin: 1px 0 2px 0 !important;
            object-fit: contain !important;
            object-position: center !important;
            display: block !important;
            page-break-inside: avoid !important;
        }

        .business-name {
            font-size: 10px !important;
            font-weight: bold !important;
        }

        .business-location {
            font-size: 7px !important;
        }

        .page-number {
            font-size: 6px !important;
        }

        .report-name {
            font-size: 12px !important;
            font-weight: bold !important;
        }

        .date-range,
        .report-meta {
            font-size: 7px !important;
            line-height: 1.2 !important;
        }

        #item_qrcode {
            width: 25px !important;
            height: 25px !important;
        }

        #item_qrcode img {
            width: 25px !important;
            height: 25px !important;
        }
    }

    /* High DPI print adjustments */
    @media print and (min-resolution: 300dpi) {
        .report-header {
            border: 0.5pt solid #ddd !important;
        }

        .business-logo,
        #item_qrcode img {
            image-rendering: -webkit-optimize-contrast !important;
            image-rendering: crisp-edges !important;
        }
    }

    @media screen {
        .page-number {
            display: none;
        }
    }

    /* Remove page footer styles */
    .page-footer {
        display: none !important;
    }

    @media print {

        /* Remove page footer in print */
        .page-footer {
            display: none !important;
        }

        .page-footer:after {
            content: none;
        }
    }
</style>

<script>
    function formatDate(dateStr) {
        if (!dateStr) return '';
        const date = typeof dateStr === 'string' ? new Date(dateStr) : dateStr;
        if (isNaN(date.getTime())) return 'Invalid Date';
        return date.getDate().toString().padStart(2, '0') + '/' +
            (date.getMonth() + 1).toString().padStart(2, '0') + '/' +
            date.getFullYear();
    }

    // Update header date range
    function calculateDaysBetween(start, end) {
        const startDate = new Date(start);
        const endDate = new Date(end);
        const diffTime = Math.abs(endDate - startDate);
        return Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
    }

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
            const daysBetween = calculateDaysBetween(start, end);
            dateRangeElement.textContent = `Date: ${startFormatted} - ${endFormatted} (${daysBetween} days)`;
        } else {
            dateRangeElement.textContent = 'All Dates';
        }
    }

    // Listen for date filter changes
    document.addEventListener('dateRangeChanged', function(e) {
        console.log('dateRangeChanged event:', e.detail); // Debug log
        updateHeaderDateRange(e.detail.startDate, e.detail.endDate);
    });

    // Listen for AJAX response updates
    document.addEventListener('ajaxDateRangeUpdated', function(e) {
        if (e.detail && e.detail.start_date && e.detail.end_date) {
            // Convert DD/MM/YYYY to YYYY-MM-DD for proper date calculation
            const [day, month, year] = e.detail.start_date.split('/');
            const startDate = `${year}-${month}-${day}`;
            const [endDay, endMonth, endYear] = e.detail.end_date.split('/');
            const endDate = `${endYear}-${endMonth}-${endDay}`;
            updateHeaderDateRange(startDate, endDate);
        }
    });

    // Initial date range setup
    document.addEventListener('DOMContentLoaded', function() {
        const startDate = window.selectedStartDate || localStorage.getItem('selectedStartDate') || '';
        const endDate = window.selectedEndDate || localStorage.getItem('selectedEndDate') || '';
        console.log('Initial date range:', {
            startDate,
            endDate
        }); // Debug log
        updateHeaderDateRange(startDate, endDate);
    });

    // Global function to update date range display
    window.updateDateRangeDisplay = function(startDate, endDate) {
        console.log('updateDateRangeDisplay called with:', startDate, endDate);
        const dateRangeElement = document.getElementById('report-date-range');
        if (!dateRangeElement) {
            console.log('Date range element not found');
            return;
        }

        // Check if moment.js is available
        if (typeof moment === 'undefined') {
            console.error('Moment.js is not available in reportheader');
            // Fallback to simple display
            if (startDate && endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                const daysBetween = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
                const formattedStart = start.toLocaleDateString('en-GB');
                const formattedEnd = end.toLocaleDateString('en-GB');
                dateRangeElement.innerHTML = `Date: ${formattedStart} - ${formattedEnd} (${daysBetween} days)`;
            } else {
                dateRangeElement.innerHTML = 'All Dates';
            }
            return;
        }

        if (startDate && endDate) {
            // Calculate days between dates
            const start = moment(startDate);
            const end = moment(endDate);
            const daysBetween = end.diff(start, 'days') + 1;

            // Format dates as DD/MM/YYYY
            const formattedStart = start.format('DD/MM/YYYY');
            const formattedEnd = end.format('DD/MM/YYYY');

            const dateText = `Date: ${formattedStart} - ${formattedEnd} (${daysBetween} days)`;
            console.log('Setting date range to:', dateText);
            dateRangeElement.innerHTML = dateText;
        } else {
            console.log('Setting date range to: All Dates');
            dateRangeElement.innerHTML = 'All Dates';
        }
    };

    // Helper function to handle AJAX response date updates
    window.updateDateRangeFromAjaxResponse = function(response) {
        console.log('updateDateRangeFromAjaxResponse called with:', response);

        if (response && response.start_date && response.end_date) {
            // Convert DD/MM/YYYY to YYYY-MM-DD for proper moment parsing
            let startDate = response.start_date;
            let endDate = response.end_date;

            // Check if dates are in DD/MM/YYYY format and convert to YYYY-MM-DD
            if (startDate.includes('/')) {
                const [day, month, year] = startDate.split('/');
                startDate = `${year}-${month}-${day}`;
            }
            if (endDate.includes('/')) {
                const [day, month, year] = endDate.split('/');
                endDate = `${year}-${month}-${day}`;
            }

            window.updateDateRangeDisplay(startDate, endDate);
        } else {
            // Use the current filter values if server doesn't provide dates
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');

            if (startDateInput && endDateInput) {
                const currentStartDate = startDateInput.value;
                const currentEndDate = endDateInput.value;
                window.updateDateRangeDisplay(currentStartDate, currentEndDate);
            } else {
                window.updateDateRangeDisplay('', '');
            }
        }
    };

    // Expose a global function to update the date range (backward compatibility)
    window.updateReportHeaderDateRange = function(startDate, endDate) {
        window.updateDateRangeDisplay(startDate, endDate);
    };

    // QR Code and Page Number functionality
    function updatePageCounter() {
        const pageHeight = 1056;
        const headerHeight = document.querySelector('.report-header') ? document.querySelector('.report-header')
            .offsetHeight : 0;
        const contentHeight = document.body.scrollHeight;
        const estimatedPages = Math.ceil(contentHeight / pageHeight);

        const pageDisplay = document.getElementById('page-display');
        if (pageDisplay) {
            pageDisplay.textContent = `Total Pages: ${estimatedPages}`;
        }
    }

    // Initialize QR Code and Page Counter
    document.addEventListener('DOMContentLoaded', function() {
        // Update page counter
        setTimeout(function() {
            updatePageCounter();
        }, 100);

        // Generate QR Code if QRCode library is available
        if (typeof QRCode !== 'undefined') {
            try {
                const reportLink = window.location.href;
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
    });

    // Update page counter on window resize
    window.addEventListener('resize', function() {
        updatePageCounter();
    });
</script>
