<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Activity Report - {{ $employee->first_name }} {{ $employee->last_name }}</title>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <style>
        @page {
            size: A4;
            margin: 20mm 15mm 25mm 15mm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.4;
            font-size: 10pt;
        }

        .report-container {
            width: 100%;
            margin: 0 auto;
        }

        .report-header {
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f8f9fa;
            padding: 12px;
            position: relative;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
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
            object-fit: contain;
        }

        .business-name {
            font-size: 12.8px;
            font-weight: bold;
        }

        .business-location {
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

        .employee-details {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #eee;
            border-radius: 5px;
            background-color: #f9f9f9;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .employee-details h2 {
            margin: 0 0 10px 0;
            font-size: 16px;
            color: #333;
        }

        .employee-details p {
            margin: 4px 0;
            font-size: 11px;
        }

        .activity-group {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .activity-group h3 {
            background-color: #f2f2f2;
            padding: 8px;
            margin: 0;
            border: 1px solid #ddd;
            border-bottom: none;
            font-size: 12px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            word-break: break-word;
        }

        th {
            background-color: #f8f8f8;
            font-weight: bold;
            font-size: 9pt;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .no-activities {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #666;
        }

        .summary-stats {
            margin-top: 20px;
            padding: 10px;
            background-color: #f0f8ff;
            border: 1px solid #ddd;
            border-radius: 5px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .summary-stats h4 {
            margin: 0 0 8px 0;
            font-size: 12px;
        }

        .summary-stats p {
            margin: 2px 0;
            font-size: 10px;
        }

        .no-print {
            display: none;
        }

        @media print {
            body {
                margin: 0;
            }

            .report-header,
            .activity-group h3,
            th,
            .employee-details,
            .summary-stats {
                background-color: #f8f9fa !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>

<body>
    <div class="report-container">
        <div class="report-header">
            <div class="header-left">
                @if (!empty($businessInfo['logo_url']))
                    <img src="{{ $businessInfo['logo_url'] }}" alt="Business Logo" class="business-logo">
                @endif
                <div>
                    <div class="business-name">{{ $businessInfo['name'] ?? 'Your Business' }}</div>
                    <div class="business-location">{{ $businessInfo['location'] ?? '' }}</div>
                    <div class="business-location">{{ $businessInfo['phone_number'] ?? 'Phone not available' }}</div>
                </div>
            </div>
            <div class="header-center">
                <div id="item_qrcode"></div>
            </div>
            <div class="header-right">
                <div class="report-name">{{ $reportName }}</div>
                <div class="date-range" id="report-date-range">
                    Date: {{$start_date}}
                </div>
                <div class="date-range">Printed by: <span class="bold-name">{{ $businessInfo['user_name'] ?? 'N/A' }}</span></div>
                <div class="date-range">Printed on: {{ now()->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        <!-- Employee Details Section -->
        <div class="employee-details">
            <h2>{{ $employee->first_name }} {{ $employee->last_name }}</h2>
            {{-- <p><strong>Employee ID:</strong> {{ $employee->id }}</p>
            <p><strong>Email:</strong> {{ $employee->email ?? 'N/A' }}</p>
            @if($department)
                <p><strong>Department:</strong> {{ $department->name }}</p>
            @endif
            @if(isset($start_date) && isset($end_date) && $start_date && $end_date)
                <p><strong>Report Period:</strong> {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} to {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}</p>
            @else
                <p><strong>Report Period:</strong> All Time</p>
            @endif
            <p><strong>Total Activities:</strong> {{ $total_activities ?? $grouped_activities->flatten()->count() }}</p> --}}
        </div>

        <!-- Activities Section -->
        <div class="activities">
            @forelse($grouped_activities as $form_name => $activities)
                <div class="activity-group">
                    <h3>{{ $form_name }} ({{ $activities->count() }} activities)</h3>
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 30%;">Field</th>
                                <th style="width: 40%;">Value</th>
                                <th style="width: 30%;">Date & Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($activities as $activity)
                                <tr>
                                    <td>{{ $activity->field_label }}</td>
                                    <td>{{ $activity->value }}</td>
                                    <td>{{ \Carbon\Carbon::parse($activity->created_at)->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @empty
                <div class="no-activities">
                    <p>No activities found for the selected criteria.</p>
                    @if(isset($start_date) && isset($end_date) && $start_date && $end_date)
                        <p>Period: {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} to {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}</p>
                    @endif
                    @if($department)
                        <p>Department: {{ $department->name }}</p>
                    @endif
                </div>
            @endforelse
        </div>

        <!-- Summary Statistics -->
        {{-- @if($grouped_activities->isNotEmpty())
            <div class="summary-stats">
                <h4>Summary Statistics</h4>
                <p><strong>Total Forms:</strong> {{ $grouped_activities->count() }}</p>
                <p><strong>Total Activities:</strong> {{ $grouped_activities->flatten()->count() }}</p>
                @if(isset($start_date) && isset($end_date) && $start_date && $end_date)
                    @php
                        $firstActivity = $grouped_activities->flatten()->sortBy('created_at')->first();
                        $lastActivity = $grouped_activities->flatten()->sortByDesc('created_at')->first();
                    @endphp
                    @if($firstActivity && $lastActivity)
                        <p><strong>First Activity:</strong> {{ \Carbon\Carbon::parse($firstActivity->created_at)->format('d/m/Y H:i') }}</p>
                        <p><strong>Last Activity:</strong> {{ \Carbon\Carbon::parse($lastActivity->created_at)->format('d/m/Y H:i') }}</p>
                    @endif
                @endif
            </div>
        @endif --}}
    </div>

    <script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function() {
        try {
            var itemLink = window.location.href;
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
                qrEl.innerHTML = '';
                new QRCode(qrEl, itemOpts);
            }
        } catch (e) {
            console.error("Error generating QR Code:", e);
        }

        // Variables to track print dialog state
        let printDialogShown = false;
        let navigatedBack = false;

        // Function to navigate back
        function navigateBack() {
            if (!navigatedBack) {
                navigatedBack = true;
                if (window.history.length > 1) {
                    window.history.back();
                } else {
                    window.close();
                }
            }
        }

        // Auto-print after QR code generation
        setTimeout(function() {
            printDialogShown = true;
            window.print();
            
            // Start checking if user canceled after a short delay
            setTimeout(function() {
                if (printDialogShown && !navigatedBack) {
                    // Use a combination of focus and visibility events to detect dialog dismissal
                    let checkCancelTimer = setInterval(function() {
                        if (document.hasFocus() && document.visibilityState === 'visible') {
                            clearInterval(checkCancelTimer);
                            // Give a small delay to allow onafterprint to fire if printing occurred
                            setTimeout(function() {
                                if (!navigatedBack) {
                                    console.log('Print dialog was likely canceled');
                                    navigateBack();
                                }
                            }, 100);
                        }
                    }, 100);
                    
                    // Fallback: if dialog is dismissed without focus events (some browsers)
                    setTimeout(function() {
                        if (printDialogShown && !navigatedBack) {
                            console.log('Print dialog timeout - assuming canceled');
                            clearInterval(checkCancelTimer);
                            navigateBack();
                        }
                    }, 10000); // 10 second fallback
                }
            }, 500);
        }, 1000);

        // Handle successful print completion
        window.onafterprint = function() {
            console.log('Print completed successfully');
            navigateBack();
        };

        // Handle page visibility changes (additional detection method)
        document.addEventListener('visibilitychange', function() {
            if (printDialogShown && !document.hidden && !navigatedBack) {
                setTimeout(function() {
                    if (!navigatedBack) {
                        console.log('Page became visible - print dialog likely canceled');
                        navigateBack();
                    }
                }, 200);
            }
        });

        // Handle window focus events (additional detection method)
        window.addEventListener('focus', function() {
            if (printDialogShown && !navigatedBack) {
                setTimeout(function() {
                    if (!navigatedBack) {
                        console.log('Window focused - print dialog likely canceled');
                        navigateBack();
                    }
                }, 200);
            }
        });

       
    });
</script>
    
</body>

</html>