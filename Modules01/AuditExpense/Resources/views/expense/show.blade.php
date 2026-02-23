<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header no-print">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
            <h4 class="modal-title no-print">@lang('expense.expense')</h4>
        </div>

        <div class="modal-body">
            <div class="container-fluid mt-4 mb-0" style="font-family: 'Khmer OS', 'Battambang', sans-serif;">
                <!-- Business Logo Section -->
                <section class="header">
                    <div class="row mb-4"
                        style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
                        <div class="col-sm-6 text-center logo-section d-flex justify-center align-items-center"
                            style="display: flex; justify-content: center; align-items: center; gap: 0.5rem;">
                            @if (!empty($business_logo))
                                <img src="{{ asset('/uploads/business_logos/' . $business_logo) }}"
                                    class="img img-thumbnail img-logo me-2" alt="Business Logo"
                                    style="max-height: 80px;">
                            @endif
                            @if (!empty($transaction->business->name))
                                <h5 class="text-primary mb-0">{{ $transaction->business->name }}</h5>
                            @endif
                        </div>
                        <div class="col-sm-9 text-center title-section">
                            <h3 style="font-family: 'Moul', 'Khmer OS Muol'; font-weight: 400; font-size: 22px;">
                                បណ្ណ័ចំណាយ</h3>
                            <h4 class="mt-2">Expense Voucher</h4>
                        </div>
                    </div>
                </section>
                <!-- Content Section -->
                <section class="content">
                    <div class="mb-3 d-flex justify-content-end" style="font-size: 14px;">
                        <div class="text-end request_voucher_date">
                            @php
                                $khmer_months = [
                                    1 => 'មករា',
                                    2 => 'កុម្ភៈ',
                                    3 => 'មីនា',
                                    4 => 'មេសា',
                                    5 => 'ឧសភា',
                                    6 => 'មិថុនា',
                                    7 => 'កក្កដា',
                                    8 => 'សីហា',
                                    9 => 'កញ្ញា',
                                    10 => 'តុលា',
                                    11 => 'វិច្ឆិកា',
                                    12 => 'ធ្នូ',
                                ];
                                $month_number = date('n', strtotime($transaction->transaction_date)); // 1-12 without leading zero
                                $khmer_month = $khmer_months[$month_number];
                            @endphp
                            <div>ថ្ងៃទី {{ date('d', strtotime($transaction->transaction_date)) }} ខែ
                                {{ $khmer_month }} ឆ្នាំ {{ date('Y', strtotime($transaction->transaction_date)) }}
                            </div>
                            <div><strong>ទីតាំងស្នើសុំ: </strong> <span>{{ $business_locations ?? '---' }}</span></div>
                        </div>
                    </div><br>
                    <!-- Request Information Grid -->
                    <div class="request-info mt-4 mb-2">
                        <div class="info-grid">
                            
                            <div class="info-pair">
                                <div class="info-label bg-light"><strong>ឈ្មោះអ្នកផ្គត់ផ្គង់:</strong>
                                    <span>{{ $transaction->contact->name ?? '---'}}</span>
                                </div>
                            </div>
                           
                            <div class="info-row combined-row">
                                <div class="info-pair">
                                    <div class="info-label bg-light"><strong>ឈ្មោះអ្នកស្នើសុំ:</strong>
                                        <span>{{ $user->first_name ?? '' }} {{ $user->last_name ?? '' }}</span>
                                    </div>
                                </div>
                                <div class="info-pair role-pair">
                                    <div class="info-label bg-light"><strong>តួនាទី:</strong>
                                        <span>{{ $designations->name ?? '---' }}</span>
                                    </div>
                                </div>
                                <div class="info-pair">
                                    <div class="info-label bg-light"><strong>នាយកដ្ថាន:</strong>
                                        <span>{{ $departments->name ?? '---' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label bg-light"><strong>ស្នើរសុំទឹកប្រាក់សរុប(KHR/USD):
                                    </strong>{{ number_format($transaction->final_total, 2) }}
                                    {{ $transaction->currency ?? 'USD' }}</div>
                            </div>
                            @if (!empty($total_in_words))
                                <div class="info-row">
                                    <div class="info-label bg-light"><strong>@lang('sale.amount_as_letter'):</strong></div>
                                    <div class="info-value"><small>({{ $total_in_words }})</small></div>
                                </div>
                            @endif
                            <div class="card notes-card">
                                <div class="card-header bg-light">
                                    <p><strong>កត់សម្គាល់:</strong>
                                        @if ($transaction->additional_notes)
                                            <span>{{ $transaction->additional_notes }}</span>
                                        @else
                                            <span
                                                class="text-muted font-italic">...................................................................................................................</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Footer/Signatures Section -->
                <section class="footer mt-4 pt-3">
                    <div class="row text-center">
                        <div class="col-sm-4">
                            <div class="signature-box">
                                <p class="mb-5">គណនេយ្យករ</p>
                                <br>
                                <p class="signature-name">_________________________</p>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="signature-box">
                                <p class="mb-5">អ្នកត្រួតពិនិត្យ</p>
                                <br>
                                <p class="signature-name">_________________________</p>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="signature-box">
                                <p class="mb-5">អ្នកស្នើ</p>
                                <br>
                                <p class="signature-name">_________________________</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <div class="modal-footer no-print">
            <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white no-print" aria-label="Print"
                onclick="$(this).closest('div.modal').printThis();">
                <i class="fa fa-print"></i> @lang('messages.print')
            </button>
            <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white no-print"
                data-dismiss="modal">@lang('messages.close')</button>
        </div>
    </div>
</div>

<style>
    /* Grid layout for request info */
    .info-grid {
        display: grid;
        gap: 8px;
    }

    .info-row.combined-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        align-items: center;
    }

    .info-pair {
        display: grid;
        grid-template-columns: 1fr;
        gap: 5px;
    }

    .info-row {
        display: grid;
        grid-template-columns: 60% 40%;
        align-items: center;
        margin-bottom: 5px;
    }

    .text-end {
        text-align: right !important;
    }

    /* Print styles */
    @media print {
        /* General settings for all paper sizes and orientations */
        @page {
            margin: 0; /* No padding/margin for page */
        }

        .container-fluid {
            width: 100%;
            padding: 0; /* Remove padding for print */
            margin: 0;
            box-sizing: border-box;
            font-size: 12pt; /* Reverted to original base size */
        }

        /* Header adjustments */
        .header .row {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            margin: 0 0 0.5cm 0; /* Reduced from implicit large margin */
        }

        .logo-section {
            flex: 0 0 100%;
            max-width: 100%;
            text-align: center;
            margin-bottom: 0.2cm; /* Reduced spacing */
        }

        .logo-section img {
            max-height: 60px; /* Slightly smaller for print */
            width: auto;
        }

        .title-section {
            flex: 0 0 100%;
            max-width: 100%;
            text-align: center;
        }

        .title-section h3 {
            font-size: clamp(16px, 2.5vw, 22px); /* Reverted to original */
            margin: 0.2cm 0; /* Reduced from 0.5rem */
        }

        .title-section h4 {
            font-size: clamp(14px, 2vw, 18px); /* Reverted to original */
            margin: 0.1cm 0; /* Reduced from 0.25rem */
        }

        /* Content adjustments */
        .request_voucher_date {
            padding-right: 0.5cm;
            font-size: clamp(10px, 1.5vw, 14px); /* Reverted to original */
            margin-bottom: 0.2cm; /* Reduced spacing */
        }

        .info-grid {
            gap: 0.1cm; /* Reduced from 0.25cm */
            width: 100%;
            margin: 0.2cm 0; /* Reduced from larger implicit margins */
        }

        .info-row.combined-row {
            display: grid;
            grid-template-columns: 50% 50%; /* Split into two equal columns */
            gap: 0.2cm; /* Reduced from 0.5cm */
            page-break-inside: avoid;
            margin: 0.1cm 0; /* Reduced spacing */
        }

        /* Specific alignment for print */
        .info-row.combined-row .info-pair {
            grid-column: 1; /* Default to left column */
        }

        .info-row.combined-row .role-pair {
            grid-column: 2; /* Move 'តួនាទី' (Role) to right column */
            text-align: left; /* Keep text alignment left within the right column */
        }

        .info-pair {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.05cm; /* Reduced from 0.1cm */
        }

        .info-row {
            display: grid;
            grid-template-columns: 60% 40%;
            align-items: center;
            margin: 0.1cm 0; /* Reduced from 0.25cm */
            page-break-inside: avoid;
        }

        .info-label, .info-value {
            font-size: clamp(10px, 1.5vw, 12px); /* Reverted to original */
        }

        .card.notes-card {
            grid-column: 1; /* Ensure card stays in left column */
            width: 100%;
            margin: 0.1cm 0; /* Reduced spacing */
        }

        .card-header {
            padding: 0.1cm; /* Reduced from 0.25cm */
        }

        .card-header p {
            font-size: clamp(10px, 1.5vw, 12px); /* Match info-label and info-value */
            margin: 0; /* Remove default margin */
        }

        .card-header p strong {
            font-size: clamp(10px, 1.5vw, 12px); /* Ensure strong tag matches */
        }

        .card-header p span {
            font-size: clamp(10px, 1.5vw, 12px); /* Ensure span matches */
        }

        /* Footer adjustments */
        .footer {
            margin-top: 0.2cm; /* Reduced from 0.4cm (implicit via mt-4) */
            padding-top: 0.1cm; /* Reduced from 0.3cm (implicit via pt-3) */
        }

        .footer .row {
            display: flex;
            flex-wrap: nowrap; /* Keep all signature boxes in one line */
            justify-content: space-between; /* Distribute space evenly */
            width: 100%;
            margin: 0;
            padding: 0;
        }

        .signature-box {
            flex: 1; /* Allow shrinking proportionally */
            min-width: 0; /* Prevent overflow */
            text-align: center;
            page-break-inside: avoid;
            padding: 0 0.2cm; /* Minimal padding for spacing */
        }

        .signature-box p {
            font-size: clamp(8px, 1.2vw, 12px); /* Reverted to original */
            margin: 0.2cm 0 0 0; /* Reduced from 0.5cm */
            white-space: nowrap; /* Prevent text wrapping */
            overflow: hidden; /* Hide overflow if too small */
            text-overflow: ellipsis; /* Add ellipsis if text is cut off */
        }

        .signature-box .signature-name {
            font-size: clamp(8px, 1.2vw, 12px); /* Reverted to original */
            margin: 0.1cm 0 0 0; /* Reduced from 0.3cm */
        }

        /* Hide non-printable elements */
        .modal-content {
            border: none;
            box-shadow: none;
        }

        .modal-header,
        .modal-footer,
        .no-print {
            display: none;
        }

        .modal-body {
            padding: 0;
        }

        /* Ensure background colors print */
        .bg-light {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Responsive adjustments for portrait */
        @media print and (orientation: portrait) {
            .container-fluid {
                font-size: 11pt; /* Reverted to original */
            }

            .info-row.combined-row {
                grid-template-columns: 50% 50%; /* Maintain 50/50 split */
            }

            .signature-box {
                flex: 1;
                min-width: 0;
            }
        }

        /* Responsive adjustments for landscape */
        @media print and (orientation: landscape) {
            .container-fluid {
                font-size: 12pt; /* Reverted to original */
            }

            .info-row.combined-row {
                grid-template-columns: 50% 50%; /* Maintain 50/50 split */
            }

            .signature-box {
                flex: 1;
                min-width: 0;
            }
        }
    }
</style>