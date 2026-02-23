<div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="modalTitle">{{$product->name}}</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <!-- Product Image -->
                        <img class="card-img-top" src="{{ $product->image_url }}" alt="Product image">

                        <div class="card-block">
                            <!-- Product Title -->
                            <h4 class="card-title mt-3">{{ $product->name }}</h4>

                            <!-- SKU as Meta -->
                            <div class="meta">
                                <strong>SKU:</strong> {{ $product->sku }}
                            </div>

                            <!-- Price Display -->
                            @if($product->type == 'single' || $product->type == 'combo')
                                <div class="mt-2">
                                    <p>
                                        <strong>Price:</strong> 
                                        <span class="display_currency" data-currency_symbol="true">{{ $product->variations->first()->sell_price_inc_tax }}</span>
                                    </p>
                                </div>
                            @endif
                        </div>

                        <!-- Tabs for Detailed Info -->
                        <div class="card-footer tab-card-header">
                            <ul class="nav nav-tabs card-header-tabs" id="productTab" role="tablist">
                                @if($product->type == 'combo' && !empty($combo_variations))
                                    <li class="nav-item">
                                        <a class="nav-link active" data-tab="combo" href="javascript:void(0)">Combo Items</a>
                                    </li>
                                @endif
                                <li class="nav-item">
                                    <a class="nav-link @if($product->type != 'combo' || empty($combo_variations)) active @endif" data-tab="details" href="javascript:void(0)">Details</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-tab="description" href="javascript:void(0)">Description</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-tab="images" href="javascript:void(0)">Image</a>
                                </li>
                                <!-- QR Code Tab -->
                                <li class="nav-item">
                                    <a class="nav-link" data-tab="qrcode" href="javascript:void(0)">QR Code</a>
                                </li>
                            </ul>
                        </div>

                        <!-- Tab Content -->
                        <div class="tab-content-wrapper">
                            <!-- Combo Items Tab -->
                            @if($product->type == 'combo' && !empty($combo_variations))
                                <div class="tab-content-pane @if($product->type == 'combo' && !empty($combo_variations)) active @endif" data-tab-content="combo">
                                    <h5 class="card-title">Combo Package Includes</h5>
                                    
                                    @php
                                        // Group combo items by category
                                        $grouped_items = [];
                                        foreach($combo_variations as $item) {
                                            $category_name = $item['variation']['product']['category']['name'] ?? 'Uncategorized';
                                            $grouped_items[$category_name][] = $item;
                                        }
                                    @endphp

                                    @foreach($grouped_items as $category_name => $items)
                                        <div class="combo-category-section mb-4">
                                            <h6 class="combo-category-title">{{ $category_name }}</h6>
                                            
                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover combo-items-table">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th style="width: 60px;">Image</th>
                                                            <th>Product</th>
                                                            <th style="width: 80px;" class="text-center">Quantity</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($items as $item)
                                                            @php
                                                                $variation = $item['variation'];
                                                                $product_item = $variation['product'];
                                                                $quantity = $item['quantity'] ?? 0;
                                                            @endphp
                                                            <tr class="combo-item">
                                                                <td class="text-center">
                                                                    @if(!empty($product_item['image']))
                                                                        <img src="{{ asset('uploads/img/' . $product_item['image']) }}" 
                                                                             alt="{{ $product_item['name'] }}"
                                                                             class="combo-item-image">
                                                                    @else
                                                                        <div class="combo-no-image">
                                                                            <i class="fa fa-image"></i>
                                                                        </div>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <div class="combo-product-name">{{ $product_item['name'] ?? 'N/A' }}</div>
                                                                    @if(!empty($variation['product_variation']['name']) && $variation['product_variation']['name'] != 'DUMMY')
                                                                        <small class="text-muted combo-variation-name">
                                                                            {{ $variation['product_variation']['name'] }}
                                                                            @if(!empty($variation['name']) && $variation['name'] != 'DUMMY')
                                                                                : {{ $variation['name'] }}
                                                                            @endif
                                                                        </small>
                                                                    @endif
                                                                    <br>
                                                                    <small class="text-muted">SKU: {{ $variation['sub_sku'] ?? 'N/A' }}</small>
                                                                </td>
                                                                <td class="text-center">
                                                                    <span class="badge badge-primary combo-quantity">{{ number_format($quantity, 0) }}</span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endforeach

                                    <!-- Total Items Summary -->
                                    <div class="combo-summary mt-3 p-3 bg-light rounded">
                                        <strong>Total Items in Package:</strong> 
                                        <span class="badge badge-success">{{ count($combo_variations) }}</span>
                                    </div>
                                </div>
                            @endif

                            <!-- Details Tab -->
                            <div class="tab-content-pane @if($product->type != 'combo' || empty($combo_variations)) active @endif" data-tab-content="details">
                                <h5 class="card-title">Product Details</h5>
                                <div class="row"  style="padding: 16px;">
                                    <div class="col-6">
                                        <p><strong>@lang('product.brand'):</strong> {{ $product->brand->name ?? '--' }}</p>
                                        <p><strong>@lang('product.unit'):</strong> {{ $product->unit->short_name ?? '--' }}</p>
                                        <p><strong>@lang('product.barcode_type'):</strong> {{ $product->barcode_type ?? '--' }}</p>
                                        <p><strong>@lang('product.category'):</strong> {{ $product->category->name ?? '--' }}</p>
                                        <p><strong>@lang('product.sub_category'):</strong> {{ $product->sub_category->name ?? '--' }}</p>
                                    </div>
                                    <div class="col-6">
                                        <p><strong>@lang('product.expires_in'):</strong>
                                            @php
                                                $expiry_array = ['months' => __('product.months'), 'days' => __('product.days'), '' => __('product.not_applicable')];
                                            @endphp
                                            {{ $product->expiry_period }} {{ $expiry_array[$product->expiry_period_type] ?? $expiry_array[''] }}
                                        </p>
                                        @if ($product->weight)
                                            <p><strong>@lang('lang_v1.weight'):</strong> {{ $product->weight }}</p>
                                        @endif
                                        <p><strong>@lang('product.applicable_tax'):</strong> {{ $product->product_tax->name ?? __('lang_v1.none') }}</p>
                                        <p><strong>@lang('product.product_type'):</strong> @lang('lang_v1.' . $product->type)</p>
                                    </div>
                                </div>

                                <!-- Custom Fields -->
                                @if (collect(range(1, 20))->some(fn($i) => !empty($product->{'product_custom_field' . $i})))
                                    <hr>
                                    <h6>Custom Fields</h6>
                                    @for ($i = 1; $i <= 20; $i++)
                                        @php
                                            $db_field = 'product_custom_field' . $i;
                                            $label = 'custom_field_' . $i;
                                        @endphp
                                        @if (!empty($product->$db_field))
                                            <p><strong>{{ $custom_labels['product'][$label] ?? '' }}:</strong> {{ $product->$db_field }}</p>
                                        @endif
                                    @endfor
                                @endif

                                <!-- Locations -->
                                <hr>
                                <p><strong>@lang('lang_v1.available_in_locations'):</strong>
                                    @if (count($product->product_locations) > 0)
                                        {{ implode(', ', $product->product_locations->pluck('name')->toArray()) }}
                                    @else
                                        @lang('lang_v1.none')
                                    @endif
                                </p>

                                <!-- Brochure -->
                                @if (!empty($product->media->first()))
                                    <p><strong>@lang('lang_v1.product_brochure'):</strong>
                                        <a href="{{ $product->media->first()->display_url }}" download="{{ $product->media->first()->display_name }}">
                                            {{ $product->media->first()->display_name }}
                                        </a>
                                    </p>
                                @endif
                            </div>

                            <!-- Description Tab -->
                            <div class="tab-content-pane" data-tab-content="description">
                                <div class="card-text">
                                    {!! $product->product_description !!}
                                </div>
                            </div>

                            <!-- Image Tab -->
                            <div class="tab-content-pane" data-tab-content="images">
                                @if(isset($product->variations[0]))
                                    @foreach($product->variations[0]->media as $media)
                                        <div class="mb-2">
                                            {!! $media->thumbnail([100, 100], 'img-thumbnail') !!}
                                        </div>
                                    @endforeach
                                @else
                                    <p>No additional images available.</p>
                                @endif
                            </div>

                            <!-- QR Code Tab -->
                            <div class="tab-content-pane" data-tab-content="qrcode">
                                <div class="text-center">
                                    <h5 class="card-title mb-4">Product QR Code</h5>
                                    
                                    <!-- QR Code Display Area -->
                                    <div id="qrcode_container" class="mb-3"></div>
                                    <div id="qr_link"></div>
                                    <br>
                                    <a href="#" class="btn btn-success" id="download_qr">Download QR Code</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
    </div>
</div>

<!-- QR Code Library -->
<script src="{{ asset('modules/productcatalogue/plugins/easy.qrcode.min.js') }}"></script>

<!-- Custom JavaScript for Tab Switching and QR Code Generation -->
<script>
 $(document).ready(function() {
    // Tab click handler
    $('.nav-link[data-tab]').on('click', function(e) {
        e.preventDefault();
        
        var targetTab = $(this).data('tab');
        
        // Remove active class from all tabs and content
        $('.nav-link').removeClass('active');
        $('.tab-content-pane').removeClass('active');
        
        // Add active class to clicked tab and corresponding content
        $(this).addClass('active');
        $('.tab-content-pane[data-tab-content="' + targetTab + '"]').addClass('active');
        
        // Auto-generate QR code when QR tab is clicked
        if (targetTab === 'qrcode') {
            generateQRCode();
        }
    });
    
    // Function to generate QR code
    function generateQRCode() {
        $('#qrcode_container').html('');
        
        // Get current page URL
        var pageUrl = window.location.href;
        
        var qrOpts = {
            text: pageUrl,
            margin: 4,
            width: 256,
            height: 256,
            quietZone: 20,
            colorDark: "#000000",
            colorLight: "#ffffffff",
            title: "{{ $product->name }}",
            titleFont: "bold 18px Arial",
            titleColor: "#004284",
            titleBackgroundColor: "#ffffff",
            titleHeight: 60,
            titleTop: 20,
            subTitle: "Product Details",
            subTitleFont: "14px Arial",
            subTitleColor: "#4F4F4F",
            subTitleTop: 40,
        };

        // Add business logo if available
        qrOpts.logo = "{{ asset('uploads/business_logos/' . ($business->logo ?? 'default.png')) }}";

        new QRCode(document.getElementById("qrcode_container"), qrOpts);
        $('#qr_link').html('<a target="_blank" href="' + pageUrl + '">Product Link</a>');
        $('#qrcode_container').find('canvas').attr('id', 'qr_canvas');
    }

    // Download QR Code
    $('#download_qr').click(function(e) {
        e.preventDefault();
        var link = document.createElement('a');
        link.download = 'product_qrcode.png';
        link.href = document.getElementById('qr_canvas').toDataURL();
        link.click();
    });
});
</script>

<style>
    html {
        font-family: Lato, 'Helvetica Neue', Arial, Helvetica, sans-serif;
        font-size: 14px;
    }

    h5 {
        font-size: 1.28571429em;
        font-weight: 700;
        line-height: 1.2857em;
        margin: 0 0 10px 0;
    }

    .card {
        font-size: 1em;
        overflow: hidden;
        padding: 0;
        border: none;
        border-radius: .28571429rem;
        box-shadow: 0 1px 3px 0 #d4d4d5, 0 0 0 1px #d4d5;
        max-width: 100%;
    }

    .card-block {
        font-size: 1em;
        position: relative;
        margin: 0;
        padding: 1.5em;
        border: none;
        border-top: 1px solid rgba(34, 36, 38, .1);
        box-shadow: none;
    }

    .card-img-top {
        display: block;
        width: 100%;
        height: auto;
        max-height: 300px;
        object-fit: cover;
    }

    .card-title {
        font-size: 1.5em;
        font-weight: 700;
        line-height: 1.2857em;
        margin-bottom: 10px;
    }

    .card-text {
        clear: both;
        margin-top: .5em;
        color: rgba(0, 0, 0, .68);
        font-size: 1em;
    }

    .card-footer {
        font-size: 1em;
        position: static;
        padding: .75em 1.5em;
        color: rgba(0, 0, 0, .4);
        border-top: 1px solid rgba(0, 0, 0, .05) !important;
        background: #fff;
    }

    .meta {
        font-size: 1em;
        color: rgba(0, 0, 0, .4);
        margin-bottom: 10px;
    }

    .tab-card-header {
        background: none;
        overflow-x: auto;
        overflow-y: hidden;
    }

    .tab-card-header > .nav-tabs {
        border: none;
        margin: 0;
        flex-wrap: nowrap;
    }

    .tab-card-header > .nav-tabs > li {
        margin-right: 5px;
    }

    .tab-card-header > .nav-tabs > li > a {
        border: 0;
        border-bottom: 2px solid transparent;
        margin-right: 0;
        color: #737373;
        padding: 5px 15px;
        font-size: 1em;
        white-space: nowrap;
        cursor: pointer;
    }

    .tab-card-header > .nav-tabs > li > a.active {
        border-bottom: 2px solid #007bff;
        color: #007bff;
    }

    .tab-card-header > .nav-tabs > li > a:hover {
        color: #007bff;
    }

    /* Tab Content Wrapper */
    .tab-content-wrapper {
        position: relative;
        min-height: 200px;
    }

    .tab-content-pane {
        display: none;
        padding: 20px;
        max-height: 400px;
        overflow-y: auto;
    }

    .tab-content-pane.active {
        display: block;
    }

    .variation-item {
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }

    .text-danger {
        color: #dc3545;
    }

    /* Combo Items Styles */
    .combo-category-section {
        margin-bottom: 1.5rem;
    }

    .combo-category-title {
        font-size: 1.1em;
        font-weight: 600;
        color: #333;
        padding-bottom: 8px;
        border-bottom: 2px solid #007bff;
        margin-bottom: 12px;
    }

    .combo-items-table {
        font-size: 0.9em;
        margin-bottom: 0;
    }

    .combo-items-table thead th {
        border-top: none;
        font-weight: 600;
        font-size: 0.85em;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .combo-item-image {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #ddd;
    }

    .combo-no-image {
        width: 50px;
        height: 50px;
        background: #f5f5f5;
        border: 1px dashed #ddd;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #aaa;
        font-size: 1.2em;
    }

    .combo-product-name {
        font-weight: 500;
        color: #333;
        font-size: 0.95em;
    }

    .combo-variation-name {
        display: block;
        font-size: 0.85em;
        color: #666;
        margin-top: 2px;
    }

    .combo-quantity {
        font-size: 1em;
        padding: 5px 12px;
    }

    .combo-summary {
        border-left: 3px solid #28a745;
    }

    .combo-item:hover {
        background-color: #f8f9fa;
    }

    /* QR Code Styles */
    #qrcode_container {
        display: inline-block;
        padding: 10px;
        border: 1px solid #eee;
        border-radius: 4px;
        margin: 10px 0;
    }

    /* Modal specific styles */
    .modal-dialog.modal-xl {
        max-width: 95%;
    }
    
    .modal-body {
        padding: 0;
    }
    
    .modal-content {
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,.5);
    }
</style>