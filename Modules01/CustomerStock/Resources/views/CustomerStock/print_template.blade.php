<style>
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
        body {
            margin: 0;
            padding: 0;
        }
        .report-header {
            background-color: #f8f9fa !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .page-number {
            display: block;
        }
    }
    @media screen {
        .page-number {
            display: none;
        }
    }
    @page {
        margin: 20mm 15mm 25mm 15mm;
        counter-increment: page;
        @bottom-center {
            content: "Page " counter(page);
            font-size: 8px;
        }
    }

    /* Table styling */
    .delivery-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 16px;
    }
    .delivery-table th,
    .delivery-table td {
        border: 1px solid #000;
        padding: 8px;
        text-align: left;
    }
    .delivery-table th {
        background-color: #f8f9fa;
        font-weight: bold;
    }
    .delivery-table .text-center {
        text-align: center;
    }
    .delivery-table .text-right {
        text-align: right;
    }
    .delivery-table .bold {
        font-weight: bold;
    }
</style>

<div class="report-header">
    <div class="header-left">
        <img src="{{ $receipt_details->business_logo }}" alt="Business Logo" class="business-logo" onerror="this.style.display='none';">
        <div>
            <div class="business-name">{{ $receipt_details->business_name }}</div>
            <div class="business-location">{{ $receipt_details->business_address }}</div>
            <div class="business-location">{{ $receipt_details->business_mobile }}</div>
            <div class="date-range">Customer: <span class="bold-name">{{ $receipt_details->customer_name }}</span></div>
            <div class="date-range">Invoice No: {{ $receipt_details->invoice_no }}</div>
        </div>
    </div>
    <div class="header-center">
        <div id="item_qrcode"></div>
    </div>
    <div class="header-right">
        <div class="report-name">ប័ណ្ណដឹកជញ្ជូន</div>
        <div class="date-range">Date: {{ $receipt_details->delivery_date }}</div>
        <div class="date-range">Printed by: <span class="bold-name">{{ $receipt_details->printed_by }}</span></div>
        <div class="date-range">Printed on: {{ $receipt_details->print_date }}</div>
    </div>
</div>

<table class="delivery-table">
    <thead>
        <tr>
            <th class="text-center">#</th>
            <th>ផលិតផល</th>
            <th class="text-center">ចំនួន</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($receipt_details->lines as $line)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>
                    <strong>{{ $line['name'] }}</strong>
                    @if (!empty($line['sell_line_note']))
                        <br><small>{{ $line['sell_line_note'] }}</small>
                    @endif
                </td>
                <td class="text-center">
                    <strong>{{ $line['quantity'] }} {{ $line['units'] }}</strong>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="signature-block">
    <div class="signature">
        <div class="signature-line"></div>
        <div class="signature-name">{{ $receipt_details->printed_by }}</div>
        <div class="signature-date">{{ \Carbon\Carbon::now()->format('d/m/Y') }}</div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    new QRCode(document.getElementById("item_qrcode"), {
        text: "{{ url()->current() }}",
        margin: 2,
        width: 80,
        height: 80,
        quietZone: 5,
        colorDark: "#000000",
        colorLight: "#ffffff"
    });
</script>