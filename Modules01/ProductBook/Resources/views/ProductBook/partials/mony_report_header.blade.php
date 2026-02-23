@php
// Helper function to format date
if (!function_exists('formatDate')) {
    function formatDate($date) {
        if (empty($date)) {
            return '';
        }
        // Validate date string
        $timestamp = is_string($date) ? strtotime($date) : (is_numeric($date) ? $date : false);
        return date('d/m/Y', $timestamp);
    }
}

// Default values for optional parameters
$print_by = $print_by ?? null;
$assign_to = $assign_to ?? null;
$extra_fields = $extra_fields ?? [];
$report_name_tail = $report_name_tail ?? [];
@endphp

<style>
    .moul-font {
        font-family: "Moul", serif;
        font-weight: 200;
        font-style: normal;
    }
    .note {
        font-size: 10px;
    }
    .header-container {
        position: relative;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }
    .logo {
        position: static;
        align-self: flex-start;
        margin-left: 20px;
        margin-bottom: 10px;
        max-height: 100px;
        max-width: 100px;
        width: auto;
        object-fit: contain;
        object-position: center;
        page-break-inside: avoid;
    }
    .content-section {
        width: 100%;
        text-align: center;
    }
    .address-details {
        text-align: left;
        margin-left: 200px; /* Adjust this value to align with the logo */
    }
    .table-invoice {
        border: 1px solid #000; /* Ensure overall table border */
    }
    .custom-checkbox-container {
        display: inline-flex;
        align-items: center;
    }
    .custom-checkbox {
        width: 15px;
        height: 15px;
        border: 2px solid #000;
        display: inline-block;
        margin-right: 0px;
        position: relative;
        cursor: pointer;
    }
    .signature-section {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .signature-section hr {
        width: 100%;
        margin-top: 10px;
        margin-bottom: 15px;
    }
    .signature-text {
        margin-top: -10px; /* Moves text closer to the line */
    }
    .bold-text{
        font-size: 15px;
        font-weight: bold;
    }

    /* Print Styles */
    @media print {
        .header-container {
            position: relative !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: flex-start !important;
            page-break-inside: avoid !important;
            margin: 0 !important;
            padding: 10px 0 !important;
            overflow: visible !important;
        }

        .logo {
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

        .content-section {
            width: 100% !important;
            text-align: center !important;
        }

        .text-center {
            margin-left: 0 !important;
            padding-left: 0 !important;
            text-align: center !important;
        }

        .moul-font {
            font-family: "Moul", serif !important;
            font-weight: 200 !important;
            font-style: normal !important;
            color: #000 !important;
        }

        .text-xs {
            font-size: 10px !important;
            color: #000 !important;
            margin: 2px 0 !important;
        }

        h1.moul-font {
            font-size: 24px !important;
            margin: 10px 0 5px 0 !important;
            color: #000 !important;
        }

        h3.moul-font {
            font-size: 18px !important;
            margin: 15px 0 10px 0 !important;
            color: #000 !important;
        }
    }

    /* Small print formats */
    @media print and (max-width: 148mm) {
        .header-container {
            flex-direction: column !important;
            align-items: flex-start !important;
        }

        .logo {
            position: static !important;
            align-self: flex-start !important;
            margin-left: 10px !important;
            margin-bottom: 8px !important;
            max-height: 60px !important;
            max-width: 60px !important;
            width: 80px !important;
            height: 80px !important;
        }

        .content-section {
            width: 100% !important;
            text-align: center !important;
        }

        .text-center {
            margin-left: 0 !important;
        }

        h1.moul-font {
            font-size: 18px !important;
        }

        h3.moul-font {
            font-size: 14px !important;
        }

        .text-xs {
            font-size: 8px !important;
        }
    }

    /* Medium print formats */
    @media print and (min-width: 149mm) and (max-width: 210mm) {
        .logo {
            margin-left: 12px !important;
            margin-bottom: 8px !important;
            max-height: 70px !important;
            max-width: 70px !important;
            width: 70px !important;
            height: 70px !important;
        }

        h1.moul-font {
            font-size: 20px !important;
        }

        h3.moul-font {
            font-size: 16px !important;
        }

        .text-xs {
            font-size: 9px !important;
        }
    }

    /* Large print formats */
    @media print and (min-width: 210mm) {
        .logo {
            margin-left: 20px !important;
            margin-bottom: 10px !important;
            max-height: 90px !important;
            max-width: 90px !important;
            width: 90px !important;
            height: 90px !important;
        }

        h1.moul-font {
            font-size: 26px !important;
        }

        h3.moul-font {
            font-size: 20px !important;
        }

        .text-xs {
            font-size: 11px !important;
        }
    }

    /* Thermal printer styles */
    @media print and (max-width: 80mm) {
        .header-container {
            flex-direction: column !important;
            align-items: flex-start !important;
        }

        .logo {
            position: static !important;
            align-self: flex-start !important;
            margin-left: 5px !important;
            margin-bottom: 6px !important;
            max-height: 40px !important;
            max-width: 40px !important;
            width: 40px !important;
            height: 40px !important;
        }

        .content-section {
            width: 100% !important;
            text-align: center !important;
        }

        h1.moul-font {
            font-size: 14px !important;
            margin: 5px 0 !important;
        }

        h3.moul-font {
            font-size: 12px !important;
            margin: 8px 0 !important;
        }

        .text-xs {
            font-size: 7px !important;
            margin: 1px 0 !important;
        }
    }
</style>

<div class="row" style="color: #000000 !important; width: 100%; justify-content: center; align-items: center; display: flex; flex-direction: column;">
    <div class="col-xs-12">
        <div class="header-container">
            @if ($businessInfo['logo_exists'])
            <img src="{{ $businessInfo['logo_url'] }}" class="logo" alt="Logo">
            @endif
            
            <div class="content-section">
                <div class="text-center">
                    <h1 class="moul-font">
                        {{ $businessInfo['name'] ?? 'Business Name' }}
                    </h1>
                    <p class="text-xs">លេខអត្តសញ្ញាណកម្មសារពើពន្ធ: {{ $businessInfo['tax_number'] }}</p>
                    <p class="text-xs">អាសយដ្ឋាន ៖{{ $businessInfo['location'] }}</p>
                    <p class="text-xs">Address ៖{{ $businessInfo['location'] }}</p>
                    <p class="text-xs">Telephone :</p>
                </div>
              
                <h3 class="text-center moul-font">
                    {{ $report_name ?? 'Report' }}
                    @if($report_name_tail)
                    <span>{{ $report_name_tail }}</span>
                    @endif <br />
                    <p style="font-size: 22px">TAX INVOICE</p>
                </h3>
            </div>
        </div>
    </div>
</div>