<div class="modal-dialog modal-xl" role="document">
    <div class="modal-content" style="font-family: sans-serif;">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('productdoc::lang.details')</h4>
        </div>

        <div class="modal-body" id="print-content" style="background-color: #f7f5f3; padding: 2rem;">
            <div id="doc-content" style="background: white; max-width: 1000px; margin: auto; padding: 2rem; color: #333;">
                <!-- Header -->
                <div class="productdoc_header">
                    <div class="productdoc_header-left">
                        @if ($businessInfo['logo_exists'])
                            <img src="{{ $businessInfo['logo_url'] }}" class="productdoc_business-logo"
                                alt="{{ $businessInfo['name'] ?? 'Business' }} Logo">
                        @endif
                        <div>
                            <div class="productdoc_business-name">{{ $businessInfo['name'] ?? 'Business Name' }}</div>
                            <div class="productdoc_business-location">{{ $businessInfo['location'] }}</div>
                        </div>
                    </div>

                    <div class="productdoc_header-right">
                        <div class="productdoc_name">@lang('productdoc::lang.productdoc')</div>
                        @if(!empty($date_range))
                            <div class="productdoc_date-range">
                                <span class="productdoc_bold-name">{{ $date_range }}</span>
                            </div>
                        @endif
                        @if($print_by)
                            <div class="productdoc_date-range">
                                {{ __('Printed by') }}: <span class="productdoc_bold-name">{{ $print_by }}</span>
                            </div>
                        @endif
                        <div class="productdoc_date-range">
                            {{ __('Printed on') }}: {{ now()->setTimezone(config('app.timezone'))->format('F j, Y g:i A') }}
                        </div>
                    </div>
                </div>

                <!-- Basic Info -->
                <div style="padding-top: 2rem; padding-bottom: 2rem; border-top: 1px solid #eee; border-bottom: 1px solid #eee;">
                    <table style="border-collapse: collapse; width: 100%;">
                        <tbody>
                            <tr>
                                <td style="padding: 0.25rem 0; font-weight: bold; width: 80px;">From:</td>
                                <td style="padding: 0.25rem 0;">{{ $name }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 0.25rem 0; font-weight: bold; width: 80px;">Date:</td>
                                <td style="padding: 0.25rem 0;">
                                    {{ \Carbon\Carbon::parse($productdoc->created_at)->format('d/m/Y') }}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 0.25rem 0; font-weight: bold; width: 80px;">Subject:</td>
                                <td style="padding: 0.25rem 0;">{{ $productdoc->Product1->name }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Category -->
                @if($productdoc->category)
                    <div class="form-group mt-3">
                        <label class="font-weight-bold">@lang('productdoc::lang.category'):</label>
                        <div class="d-flex align-items-center">
                            @php
                                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                                $fileExtension = strtolower(pathinfo($productdoc->category->image, PATHINFO_EXTENSION));
                            @endphp

                            @if(in_array($fileExtension, $imageExtensions))
                                <img src="{{ asset('uploads/ProductDocCategory/' . basename($productdoc->category->image)) }}"
                                    alt="Category Image"
                                    style="max-width: 25px; max-height: 25px; margin-right: 8px;">
                            @elseif($fileExtension === 'pdf')
                                <a href="{{ asset('uploads/ProductDocCategory/' . basename($productdoc->category->image)) }}"
                                   target="_blank" style="margin-right: 8px;">
                                    <i class="fas fa-file-pdf" style="font-size: 25px; color: #dc3545;"></i>
                                </a>
                            @endif
                            <span>{{ $productdoc->category->name }}</span>
                        </div>

                        @if($productdoc->category->description)
                            <div class="mt-2">
                                <label>@lang('employeecontractb1::lang.description'):</label>
                                <p class="form-control-static">{!! $productdoc->category->description !!}</p>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Product -->
                <div class="form-group mt-3">
                    <label>@lang('productdoc::lang.Product_1'):</label>
                    <p class="form-control-static">{{ $productdoc->Product1->name ?? __('messages.na') }}</p>
                </div>

                <!-- File Attachments with Download -->
                @php
                    $fileFields = [
                        'productFile1_5' => 'File 1',
                    ];
                    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                @endphp

                @foreach($fileFields as $field => $label)
                    @if($productdoc->{$field})
                        @php
                            // Check the file type
                            $fileType = $productdoc->file_type ?? 'upload';
                            $isTelegram = ($fileType === 'telegram' && $field === 'productFile1_5');
                            $isText = ($fileType === 'text' && $field === 'productFile1_5');
                            $isLink = ($fileType === 'link' && $field === 'productFile1_5');
                            
                            if ($isTelegram) {
                                $messageId = $productdoc->{$field};
                                $telegramUrl = "https://t.me/c/3332101476/{$messageId}";
                            } elseif ($isText) {
                                $filename = $productdoc->{$field};
                            } elseif ($isLink) {
                                $linkUrl = $productdoc->{$field};
                            } else {
                                $filename = basename($productdoc->{$field});
                                $filePath = 'uploads/ProductDoc/' . $filename;
                                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                                $fullUrl = asset($filePath);
                            }
                        @endphp
                        
                        <div class="mt-3">
                            @if($isTelegram)
                                {{-- Telegram Message Display --}}
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>
                                        <i class="fab fa-telegram-plane text-primary mr-2"></i>
                                        {{ $label }}: Telegram Message #{{ $messageId }}
                                    </strong>
                                    <a href="{{ $telegramUrl }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-primary no-print">
                                        <i class="fab fa-telegram-plane"></i> @lang('View on Telegram')
                                    </a>
                                </div>
                                
                                <div class="mt-2 p-3 bg-light border rounded">
                                    <i class="fas fa-info-circle text-info mr-2"></i>
                                    <small class="text-muted">
                                        This document was imported from Telegram. Click "View on Telegram" to see the original message.
                                    </small>
                                </div>

                            @elseif($isText)
                                {{-- Text Content Display --}}
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 p-3 border rounded bg-light" style="gap: 0.5rem;">
                                    <div>
                                        <strong class="d-flex align-items-center text-dark">
                                            <i class="fa fa-file-text text-info mr-2"></i>
                                            {{ $label }}: <span class="text-muted ml-1">Text Content</span>
                                        </strong>
                                    </div>
                                    <div class="w-100 w-md-auto text-break" style="max-width: 100%;">
                                        {!! $productdoc->$field !!}
                                    </div>
                                </div>

                            @elseif($isLink)
                                {{-- Link/URL Display --}}
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>
                                        <i class="fa fa-link text-success mr-2"></i>
                                        {{ $label }}: URL Link
                                    </strong>
                                    <a href="{{ $linkUrl }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-info no-print">
                                        <i class="fa fa-external-link-alt"></i> @lang('Open Link')
                                    </a>
                                </div>

                                <div class="mt-2 p-3 bg-light border rounded">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle text-info mr-2"></i>
                                        URL: <a href="{{ $linkUrl }}" target="_blank">{{ $linkUrl }}</a>
                                    </small>
                                </div>

                            @else
                                {{-- Regular File Display --}}
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>{{ $label }}: {{ $filename }}</strong>
                                    <a href="{{ $fullUrl }}" download="{{ $filename }}" class="btn btn-sm btn-outline-success no-print">
                                        <i class="fa fa-download"></i> @lang('Download')
                                    </a>
                                </div>

                                <!-- Preview for images and PDFs -->
                                @if(in_array($ext, $imageExtensions))
                                    <div class="mt-2">
                                        <img src="{{ $fullUrl }}" 
                                             alt="{{ $label }}" 
                                             style="max-height: 150px; max-width: 150px; border: 1px solid #ddd; padding: 2px;">
                                    </div>
                                @elseif($ext === 'pdf')
                                    <div class="mt-2">
                                        <iframe src="{{ $fullUrl }}#toolbar=0&navpanes=0"
                                                width="100%" height="500px" frameborder="0"></iframe>
                                        <small class="text-muted d-block mt-1">
                                            @lang('Click "Download" above to save this PDF')
                                        </small>
                                    </div>
                                @else
                                    <small class="text-muted d-block mt-1">
                                        @lang('File type: :type', ['type' => strtoupper($ext)])
                                    </small>
                                @endif
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-primary no-print" onclick="printContent()">
                <i class="fa fa-print"></i> @lang('messages.print')
            </button>
            <button type="button" class="btn btn-secondary no-print" data-dismiss="modal">
                @lang('messages.close')
            </button>
        </div>
    </div>
</div>

<style>
    .productdoc_header {
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

    .productdoc_header-left {
        display: flex;
        align-items: center;
        flex: 1;
        z-index: 1;
    }

    .productdoc_header-right {
        flex: 1;
        text-align: right;
        z-index: 1;
    }

    .productdoc_business-logo {
        max-height: 50px;
        max-width: 50px;
        margin-right: 15px;
    }

    .productdoc_business-name {
        font-size: 20px;
        font-weight: 600;
    }

    .productdoc_business-location,
    .productdoc_page-number {
        font-size: 14px;
        color: #666;
        margin-top: 2px;
    }

    .productdoc_name {
        font-size: 22px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .productdoc_date-range {
        font-size: 14px;
        margin-top: 5px;
    }

    .productdoc_bold-name {
        font-weight: bold;
    }

    .no-print {
        display: inline-block;
    }

    @media print {
        .no-print {
            display: none !important;
        }
        .productdoc_header {
            page-break-after: avoid;
        }
    }
</style>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Hanuman:wght@100..900&family=Siemreap&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Bayon&family=Hanuman:wght@100..900&family=Siemreap&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Moul&family=Siemreap&display=swap" rel="stylesheet">

<script>
    function printContent() {
        if (typeof $('#print-content').printThis === 'function') {
            $('#print-content').printThis({
                importCSS: true,
                importStyle: true
            });
        } else {
            window.print();
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const pageDisplay = document.getElementById('productdoc_page-display');
        if (pageDisplay) pageDisplay.textContent = 'Page 1';
    });
    $(document).ready(function() {
  $('.summernote-text').summernote({
    placeholder: 'សូមសរសេរនៅទីនេះ...',
        tabsize: 2,
        height: 300,
    popover: {
      image: [

        // This is a Custom Button in a new Toolbar Area
        ['custom', ['examplePlugin']],
        ['imagesize', ['imageSize100', 'imageSize50', 'imageSize25']],
        ['float', ['floatLeft', 'floatRight', 'floatNone']],
        ['remove', ['removeMedia']]
      ]
    },
    fontNames: ['KhmerOSBassac', 'Moul', 'Siemreap', 'Battambang', 'Bayon', 'Hanuman'],
    fontNamesIgnoreCheck: ['KhmerOSBassac', 'Moul', 'Siemreap', 'Battambang', 'Bayon', 'Hanuman'],
  });
});
</script>
