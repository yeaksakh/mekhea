    
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" style="font-family: sans-serif;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('expenseautofill::lang.details')</h4>
            </div>
                
            <div class="modal-body" id="print-content" style="background-color: #f7f5f3; padding: 2rem;">
                <div style="background: white; max-width: 1000px; margin: auto; padding: 2rem; color: #333;">
                    <div class="expenseautofill_header">
                        <div class="expenseautofill_header-left">
                            @if ($businessInfo['logo_exists'])
                            <img src="{{ $businessInfo['logo_url'] }}" class="expenseautofill_business-logo"
                                alt="{{ $businessInfo['name'] ?? 'Business' }} Logo">
                            @endif
                            <div>
                                <div class="expenseautofill_business-name">{{ $businessInfo['name'] ?? 'Business Name' }}</div>
                                <div class="expenseautofill_business-location">{{ $businessInfo['location'] }}</div>
                                <div class="expenseautofill_page-number" id="expenseautofill_page-display">Page 1</div>
                            </div>
                        </div>

                        <div class="expenseautofill_header-right">
                            <div class="expenseautofill_name">@lang('expenseautofill::lang.expenseautofill')</div>
                            @if(!empty($date_range))
                            <div class="expenseautofill_date-range"><span class="expenseautofill_bold-name">{{ $date_range }}</span></div>
                            @endif
                            @if($print_by)
                            <div class="expenseautofill_date-range">{{ __('Printed by') }}: <span class="expenseautofill_bold-name">{{ $print_by }}</span></div>
                            @endif
                            <div class="expenseautofill_date-range">{{ __('Printed on') }}: {{ now()->setTimezone(config('app.timezone'))->format('F j, Y g:i A') }}</div>
                        </div>
                    </div>
                    <div style="padding-top: 2rem; padding-bottom: 2rem; border-top: 1px solid #eee; border-bottom: 1px solid #eee;">
                        <table style="border-collapse: collapse; width: 100%;">
                            <tbody>
                                <tr>
                                    <td style="padding: 0.25rem 0; font-weight: bold; width: 80px;">From:</td>
                                    <td style="padding: 0.25rem 0;">{{ $name }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 0.25rem 0; font-weight: bold; width: 80px;">Date:</td>
                                    <td style="padding: 0.25rem 0;">{{ \Carbon\Carbon::parse($expenseautofill->created_at)->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 0.25rem 0; font-weight: bold; width: 80px;">Subject:</td>
                                    <td style="padding: 0.25rem 0;">{{ $first_field }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                
                
                
                    <label class="form-check-label" id="categorycontent">
                    @if($expenseautofill->category)
                        @php
                            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                            $fileExtension = strtolower(pathinfo($expenseautofill->category->image, PATHINFO_EXTENSION));
                        @endphp
                        
                        @if(in_array($fileExtension, $imageExtensions))
                            <label class="form-check-label pt-2 mb-0" for="categoryCheck">
                                <img src="{{ asset('uploads/ExpenseAutoFillCategory/' . basename($expenseautofill->category->image)) }}" 
                                    alt="Document Image" 
                                    style="max-width: 25px; max-height: 25px; vertical-align: middle;">
                                </label>
                        @elseif($fileExtension === 'pdf')
                            <span class="me-2">
                                <a href="{{ asset('uploads/ExpenseAutoFillCategory/' . basename($expenseautofill->category->image)) }}" 
                                target="_blank" 
                                style="text-decoration: none;">
                                    <i class="fas fa-file-pdf" style="font-size: 25px; color: #dc3545;"></i>
                                </a>
                            </span>
                        @endif
                        <span>{{ $expenseautofill->category->name }}</span>
                        <div class="form-group" id="category-detail">
                            <label for="categorydetail">@lang('employeecontractb1::lang.description'):</label>
                            <p id="categorydetail" class="form-control-static">{!! $expenseautofill->category->description !!}</p>
                        </div>
                        @endif
                    </label>

                    <!-- Modal Content Goes Here -->
                        <div class="form-group" style="padding-top: 2rem; line-height: 1.6;">
        <label for="title_1">@lang('expenseautofill::lang.title_1'):</label>
        <p id="title_1" class="form-control-static" >{{ $expenseautofill->{'title_1'} }}</p>
    </div>
    <div class="form-group" style="padding-top: 2rem; line-height: 1.6;">
        <label for="topic _5">@lang('expenseautofill::lang.topic _5'):</label>
        <p id="topic _5" class="form-control-static" >{{ $expenseautofill->{'topic _5'} }}</p>
    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white no-print" aria-label="Print"
                    onclick="$('#print-content').printThis({ importCSS: true, importStyle: true });">
                    <i class="fa fa-print"></i> @lang( 'messages.print' )
                </button>
                <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white no-print" data-dismiss="modal">@lang('messages.close')</button>
            </div>
        </div>
    </div>
    <style>
    .expenseautofill_header {
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

    .expenseautofill_header-left {
        display: flex;
        align-items: center;
        flex: 1;
        z-index: 1;
    }

    .expenseautofill_header-right {
        flex: 1;
        text-align: right;
        z-index: 1;
    }

    .expenseautofill_business-logo {
        max-height: 50px;
        max-width: 50px;
        margin-right: 15px;
    }

    .expenseautofill_business-name {
        font-size: 20px;
        font-weight: 600;
    }

    .expenseautofill_business-location {
        font-size: 14px;
        color: #666;
        margin-top: 2px;
    }

    .expenseautofill_page-number {
        font-size: 12px;
        color: #666;
        margin-top: 2px;
    }

    .expenseautofill_name {
        font-size: 22px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .expenseautofill_date-range {
        font-size: 14px;
        margin-top: 5px;
    }

    .expenseautofill_bold-name {
        font-weight: bold;
    }

    .no-screen {
        display: none;
    }


    @media print {
        #expenseautofill_print-content {
            padding: 0rem !important;
        }

        #expenseautofill_print-content>div {
            padding: 0.25rem !important;
        }

        @page {
            margin: 10mm;

            @bottom-center {
                content: "Page " counter(page) " of " counter(pages);
                font-size: 10px;
                color: #000;
            }
        }

        body {
            counter-reset: page;
        }

        a {
            text-decoration: none;
            color: #000;
        }

        .no-print {
            display: none;
        }

        .expenseautofill_header {
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

        .expenseautofill_header-left {
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

        .expenseautofill_header-left>div {
            text-align: left !important;
        }

        .expenseautofill_header-right {
            flex: 1 !important;
            text-align: right !important;
            z-index: 1 !important;
        }

        .expenseautofill_business-logo {
            max-height: 40px;
            max-width: 40px;
            margin-right: 12px;
            width: 35px !important;
            height: 35px !important;
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

        .expenseautofill_business-name {
            font-size: 12.8px;
            font-weight: bold;
        }

        .expenseautofill_business-location {
            font-size: 8.8px !important;
            color: #000 !important;
            text-align: left !important;
            margin-top: 2px !important;
        }

        .expenseautofill_page-number {
            font-size: 10px !important;
            color: #000 !important;
            text-align: left !important;
            margin-top: 2px !important;
            display: block !important;
        }

        .expenseautofill_name {
            font-size: 12.8px !important;
            font-weight: bold !important;
            margin-bottom: 5px !important;
            color: #000 !important;
            text-align: right !important;
        }

        .expenseautofill_date-range {
            font-size: 8.8px !important;
            margin-top: 4px !important;
            color: #000 !important;
            text-align: right !important;
        }

        .expenseautofill_bold-name {
            font-weight: bold !important;
            color: #000 !important;
        }

        .no-screen {
            display: block !important;
        }

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
</style>
<script>
    function updatePageCounter() {
        const pageHeight = 1056;
        const headerHeight = document.querySelector('.expenseautofill_header') ? document.querySelector('.expenseautofill_header').offsetHeight : 0;
        const contentHeight = document.body.scrollHeight;
        const estimatedPages = Math.ceil(contentHeight / pageHeight);

        const pageDisplay = document.getElementById('expenseautofill_page-display');
        if (pageDisplay) {
            pageDisplay.textContent = estimatedPages > 1 ? 'Page 1 of ' + estimatedPages : 'Page 1';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Update page counter
        setTimeout(function() {
            updatePageCounter();
        }, 100);


    });

    // Update page counter on window resize
    window.addEventListener('resize', function() {
        updatePageCounter();
    });

</script>