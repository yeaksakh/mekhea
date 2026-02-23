    
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" style="font-family: sans-serif;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('news::lang.details')</h4>
            </div>
                
            <div class="modal-body" id="print-content" style="background-color: #f7f5f3; padding: 2rem;">
                <div style="background: white; max-width: 1000px; margin: auto; padding: 2rem; color: #333;">
                    <div class="news_header">
                        <div class="news_header-left">
                            @if ($businessInfo['logo_exists'])
                            <img src="{{ $businessInfo['logo_url'] }}" class="news_business-logo"
                                alt="{{ $businessInfo['name'] ?? 'Business' }} Logo">
                            @endif
                            <div>
                                <div class="news_business-name">{{ $businessInfo['name'] ?? 'Business Name' }}</div>
                                <div class="news_business-location">{{ $businessInfo['location'] }}</div>
                                <div class="news_page-number" id="news_page-display">Page 1</div>
                            </div>
                        </div>

                        <div class="news_header-right">
                            <div class="news_name">@lang('news::lang.news')</div>
                            @if(!empty($date_range))
                            <div class="news_date-range"><span class="news_bold-name">{{ $date_range }}</span></div>
                            @endif
                            @if($print_by)
                            <div class="news_date-range">{{ __('Printed by') }}: <span class="news_bold-name">{{ $print_by }}</span></div>
                            @endif
                            <div class="news_date-range">{{ __('Printed on') }}: {{ now()->setTimezone(config('app.timezone'))->format('F j, Y g:i A') }}</div>
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
                                    <td style="padding: 0.25rem 0;">{{ \Carbon\Carbon::parse($news->created_at)->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 0.25rem 0; font-weight: bold; width: 80px;">Subject:</td>
                                    <td style="padding: 0.25rem 0;">{{ $first_field }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                
                
                
                    <label class="form-check-label" id="categorycontent">
                    @if($news->category)
                        @php
                            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                            $fileExtension = strtolower(pathinfo($news->category->image, PATHINFO_EXTENSION));
                        @endphp
                        
                        @if(in_array($fileExtension, $imageExtensions))
                            <label class="form-check-label pt-2 mb-0" for="categoryCheck">
                                <img src="{{ asset('uploads/NewsCategory/' . basename($news->category->image)) }}" 
                                    alt="Document Image" 
                                    style="max-width: 25px; max-height: 25px; vertical-align: middle;">
                                </label>
                        @elseif($fileExtension === 'pdf')
                            <span class="me-2">
                                <a href="{{ asset('uploads/NewsCategory/' . basename($news->category->image)) }}" 
                                target="_blank" 
                                style="text-decoration: none;">
                                    <i class="fas fa-file-pdf" style="font-size: 25px; color: #dc3545;"></i>
                                </a>
                            </span>
                        @endif
                        <span>{{ $news->category->name }}</span>
                        <div class="form-group" id="category-detail">
                            <label for="categorydetail">@lang('employeecontractb1::lang.description'):</label>
                            <p id="categorydetail" class="form-control-static">{!! $news->category->description !!}</p>
                        </div>
                        @endif
                    </label>

                    <!-- Modal Content Goes Here -->
                        <div class="form-group" style="padding-top: 2rem; line-height: 1.6;">
        <label for="title_1">@lang('news::lang.title_1'):</label>
        <p id="title_1" class="form-control-static" >{{ $news->{'title_1'} }}</p>
    </div>
    @if($news->{'image_5'})
        @php
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
            $fileExtension = strtolower(pathinfo($news->{'image_5'}, PATHINFO_EXTENSION));
            $filePath = 'uploads/News/' . basename($news->{'image_5'});
        @endphp

        <div class="mt-3">
            @if(in_array($fileExtension, $imageExtensions))
                <img src="{{ asset($filePath) }}" 
                    alt="Document Image" 
                    class="mt-2"
                    style="max-width: 100px;">
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

    <div class="form-group" style="padding-top: 2rem; line-height: 1.6;">
        <label for="description_6">@lang('news::lang.description_6'):</label>
        <p id="description_6" class="form-control-static" >{!! $news->{'description_6'} !!}</p>
    </div>
    @if($news->{'image_2_7'})
        @php
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
            $fileExtension = strtolower(pathinfo($news->{'image_2_7'}, PATHINFO_EXTENSION));
            $filePath = 'uploads/News/' . basename($news->{'image_2_7'});
        @endphp

        <div class="mt-3">
            @if(in_array($fileExtension, $imageExtensions))
                <img src="{{ asset($filePath) }}" 
                    alt="Document Image" 
                    class="mt-2"
                    style="max-width: 100px;">
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
    .news_header {
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

    .news_header-left {
        display: flex;
        align-items: center;
        flex: 1;
        z-index: 1;
    }

    .news_header-right {
        flex: 1;
        text-align: right;
        z-index: 1;
    }

    .news_business-logo {
        max-height: 50px;
        max-width: 50px;
        margin-right: 15px;
    }

    .news_business-name {
        font-size: 20px;
        font-weight: 600;
    }

    .news_business-location {
        font-size: 14px;
        color: #666;
        margin-top: 2px;
    }

    .news_page-number {
        font-size: 12px;
        color: #666;
        margin-top: 2px;
    }

    .news_name {
        font-size: 22px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .news_date-range {
        font-size: 14px;
        margin-top: 5px;
    }

    .news_bold-name {
        font-weight: bold;
    }

    .no-screen {
        display: none;
    }


    @media print {
        #news_print-content {
            padding: 0rem !important;
        }

        #news_print-content>div {
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

        .news_header {
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

        .news_header-left {
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

        .news_header-left>div {
            text-align: left !important;
        }

        .news_header-right {
            flex: 1 !important;
            text-align: right !important;
            z-index: 1 !important;
        }

        .news_business-logo {
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

        .news_business-name {
            font-size: 12.8px;
            font-weight: bold;
        }

        .news_business-location {
            font-size: 8.8px !important;
            color: #000 !important;
            text-align: left !important;
            margin-top: 2px !important;
        }

        .news_page-number {
            font-size: 10px !important;
            color: #000 !important;
            text-align: left !important;
            margin-top: 2px !important;
            display: block !important;
        }

        .news_name {
            font-size: 12.8px !important;
            font-weight: bold !important;
            margin-bottom: 5px !important;
            color: #000 !important;
            text-align: right !important;
        }

        .news_date-range {
            font-size: 8.8px !important;
            margin-top: 4px !important;
            color: #000 !important;
            text-align: right !important;
        }

        .news_bold-name {
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
        const headerHeight = document.querySelector('.news_header') ? document.querySelector('.news_header').offsetHeight : 0;
        const contentHeight = document.body.scrollHeight;
        const estimatedPages = Math.ceil(contentHeight / pageHeight);

        const pageDisplay = document.getElementById('news_page-display');
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