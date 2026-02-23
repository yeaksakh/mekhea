<div class="modal-dialog modal-xl" role="document">
    <div class="modal-content" style="font-family: sans-serif;">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('documentkeeper::lang.details')</h4>
        </div>
        <div class="modal-body" id="jester_documentkeeper_print-content" style="background-color: #f7f5f3; padding: 2rem;">
            <div style="background: white; max-width: 1000px; margin: auto; padding: 2rem; color: #333;">
                <div class="jester_documentkeeper_header">
                    <div class="jester_documentkeeper_header-left">
                        @if ($businessInfo['logo_exists'])
                            <img src="{{ $businessInfo['logo_url'] }}" class="jester_documentkeeper_business-logo"
                                alt="{{ $businessInfo['name'] ?? 'Business' }} Logo">
                        @endif
                        <div>
                            <div class="jester_documentkeeper_business-name">{{ $businessInfo['name'] ?? 'Business Name' }}</div>
                            <div class="jester_documentkeeper_business-location">{{ $businessInfo['location'] }}</div>
                            <div class="jester_documentkeeper_page-number" id="jester_documentkeeper_page-display">Page 1</div>
                        </div>
                    </div>
                
                    <div class="jester_documentkeeper_header-right">
                        <div class="jester_documentkeeper_name">{{ __('documentkeeper::lang.documentkeeper') }}</div>
                        @if(!empty($date_range))
                            <div class="jester_documentkeeper_date-range"><span class="jester_documentkeeper_bold-name">{{ $date_range }}</span></div>
                        @endif
                        @if($print_by)
                            <div class="jester_documentkeeper_date-range">{{ __('Printed by') }}: <span class="jester_documentkeeper_bold-name">{{ $print_by }}</span></div>
                        @endif
                        <div class="jester_documentkeeper_date-range">{{ __('Printed on') }}: {{ now()->setTimezone(config('app.timezone'))->format('F j, Y g:i A') }}</div>
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
                                <td style="padding: 0.25rem 0;">{{ \Carbon\Carbon::parse($documentkeeper->created_at)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 0.25rem 0; font-weight: bold; width: 80px;">Subject:</td>
                                <td style="padding: 0.25rem 0;">{!! strip_tags($documentkeeper->title_1) !!}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div style="padding-top: 2rem; line-height: 1.6;">
                    @if(!empty($documentkeeper->description_2))
                        <p>{!! $documentkeeper->description_2 !!}</p>
                    @endif

                    @if($documentkeeper->category && !empty($documentkeeper->category->description))
                        <div class="form-group" id="jester_documentkeeper_category-detail">
                            <p class="form-control-static"><strong>@lang('documentkeeper::lang.category_description'):</strong> {!! $documentkeeper->category->description !!}</p>
                        </div>
                    @endif

                    @if($documentkeeper->{'file_2'})
                        @php
                            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                            $fileExtension = strtolower(pathinfo($documentkeeper->{'file_2'}, PATHINFO_EXTENSION));
                            $filePath = 'uploads/DocumentKeeper/' . basename($documentkeeper->{'file_2'});
                        @endphp

                        <div class="mt-3" id="jester_documentkeeper_file_2content">
                            @if(in_array($fileExtension, $imageExtensions))
                                <img src="{{ asset($filePath) }}" 
                                    alt="Document Image" 
                                    class="mt-2"
                                    style="max-width: 100%;">
                            @elseif($fileExtension === 'pdf')
                                <a href="{{ asset($filePath) }}" 
                                target="_blank">
                                    <iframe 
                                        src="{{ asset($filePath) }}#toolbar=0&navpanes=0&scrollbar=0"
                                        width="100%"
                                        height="600px"
                                        frameborder="0"
                                        class="pdf-viewer">
                                    </iframe>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

                <div style="padding-top: 2rem;">
                    @if($documentkeeper->{'file_2'})
                        @php
                            $fileName = basename($documentkeeper->{'file_2'});
                            $filePath = 'uploads/DocumentKeeper/' . $fileName;
                        @endphp
                        <p style="margin-top: 0.5rem;">
                            <strong>@lang('documentkeeper::lang.file_name'):</strong> {{ $fileName }}
                        </p>
                        <p style="margin-top: 0.5rem;" class="no-print">
                            <a href="{{ asset($filePath) }}" download class="tw-dw-btn tw-dw-btn-primary tw-text-white">
                                <i class="fa fa-download"></i> @lang('documentkeeper::lang.download_file')
                            </a>
                        </p>
                    @endif
                    <div style="padding-top: 3rem;">
                        <p>Best,</p>
                        <p style="margin-top: 1.5rem;">{{ $name }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white no-print" aria-label="Print"
                onclick="$('#jester_documentkeeper_print-content').printThis({ importCSS: true, importStyle: true });">
                <i class="fa fa-print"></i> @lang('messages.print')
            </button>
            <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white no-print"
                data-dismiss="modal">@lang('messages.close')</button>
        </div>
    </div>
</div>

<div class="page-footer no-screen"></div>

<style>
    .jester_documentkeeper_header {
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
    .jester_documentkeeper_header-left {
        display: flex;
        align-items: center;
        flex: 1;
        z-index: 1;
    }
    
    .jester_documentkeeper_header-right {
        flex: 1;
        text-align: right;
        z-index: 1;
    }
    .jester_documentkeeper_business-logo {
        max-height: 50px;
        max-width: 50px;
        margin-right: 15px;
    }
    .jester_documentkeeper_business-name {
        font-size: 20px;
        font-weight: 600;
    }
    .jester_documentkeeper_business-location {
        font-size: 14px;
        color: #666;
        margin-top: 2px;
    }
    .jester_documentkeeper_page-number {
        font-size: 12px;
        color: #666;
        margin-top: 2px;
    }
    .jester_documentkeeper_name {
        font-size: 22px;
        font-weight: 600;
        margin-bottom: 5px;
    }
    .jester_documentkeeper_date-range {
        font-size: 14px;
        margin-top: 5px;
    }
    .jester_documentkeeper_bold-name {
        font-weight: bold;
    }
    .no-screen {
        display: none;
    }
    

    @media print {
        #jester_documentkeeper_print-content {
            padding: 0rem !important;
        }
        #jester_documentkeeper_print-content > div {
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
        .jester_documentkeeper_header {
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
        .jester_documentkeeper_header-left {
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
        .jester_documentkeeper_header-left > div {
            text-align: left !important;
        }
        
        .jester_documentkeeper_header-right {
            flex: 1 !important;
            text-align: right !important;
            z-index: 1 !important;
        }
        .jester_documentkeeper_business-logo {
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
        .jester_documentkeeper_business-name {
            font-size: 12.8px;
            font-weight: bold;
        }
        .jester_documentkeeper_business-location {
            font-size: 8.8px !important;
            color: #000 !important;
            text-align: left !important;
            margin-top: 2px !important;
        }
        .jester_documentkeeper_page-number {
            font-size: 10px !important;
            color: #000 !important;
            text-align: left !important;
            margin-top: 2px !important;
            display: block !important;
        }
        .jester_documentkeeper_name {
            font-size: 12.8px !important;
            font-weight: bold !important;
            margin-bottom: 5px !important;
            color: #000 !important;
            text-align: right !important;
        }
        .jester_documentkeeper_date-range {
            font-size: 8.8px !important;
            margin-top: 4px !important;
            color: #000 !important;
            text-align: right !important;
        }
        .jester_documentkeeper_bold-name {
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
        const headerHeight = document.querySelector('.jester_documentkeeper_header') ? document.querySelector('.jester_documentkeeper_header').offsetHeight : 0;
        const contentHeight = document.body.scrollHeight;
        const estimatedPages = Math.ceil(contentHeight / pageHeight);

        const pageDisplay = document.getElementById('jester_documentkeeper_page-display');
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