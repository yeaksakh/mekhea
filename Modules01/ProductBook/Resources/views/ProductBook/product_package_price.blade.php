@extends('layouts.app')
@section('title', __('minireportb1::minireportb1.product-015'))
@include('productbook::ProductBook.partials.linkforinclude')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="{{ asset('modules/minireportb1/css/module.css') }}">

    <style>
        button.print-button {
            width: 50px;
            height: 50px;
            margin-top: 8px;
            padding: 0;
            border: 0;
            background: transparent;
            cursor: pointer;
        }

        span.print-icon,
        span.print-icon::before,
        span.print-icon::after {
            box-sizing: border-box;
            background-color: #fff;
            border: solid 2px #0f8800;
        }

        span.print-icon::after {
            border-width: 1px;
        }

        button.print-button:hover .print-icon::after {
            border: solid 2px #0f8800;
        }

        span.print-icon {
            position: relative;
            display: inline-block;
            margin-top: 20%;
            width: 60%;
            height: 35%;
            background: #fff;
            border-radius: 20% 20% 0 0;
        }

        span.print-icon::before {
            content: "";
            position: absolute;
            bottom: 100%;
            left: 12%;
            right: 12%;
            height: 110%;
            transition: height .2s .15s;
        }

        span.print-icon::after {
            content: "";
            position: absolute;
            top: 55%;
            left: 12%;
            right: 12%;
            height: 0%;
            background: #fff;
            background-image: linear-gradient(to top,
                    #fff 0, #fff 14%,
                    #0f8800 14%, #0f8800 28%,
                    #fff 28%, #fff 42%,
                    #0f8800 42%, #0f8800 56%,
                    #fff 56%, #fff 70%,
                    #0f8800 70%, #0f8800 84%,
                    #fff 84%, #fff 100%);
            background-repeat: no-repeat;
            background-size: 70% 90%;
            background-position: center;
            transition: height .2s, border-width 0s .2s, width 0s .2s;
        }

        button.print-button:hover .print-icon::before {
            height: 0;
            transition: height .2s;
        }

        button.print-button:hover .print-icon::after {
            height: 120%;
            transition: height .2s .15s, border-width 0s .2s;
        }

        /* Print Styles */
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                margin: 0;
                font-family: Arial, sans-serif;
            }

            .print-header {
                text-align: center;
                border-bottom: 2px solid #000;
                padding-bottom: 10px;
                margin-bottom: 20px;
            }

            .print-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }

            .print-table th,
            .print-table td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }

            .print-table th {
                background-color: #f2f2f2;
                font-weight: bold;
            }

            .print-section-title {
                margin-top: 20px;
                margin-bottom: 10px;
                font-size: 1.2em;
                font-weight: bold;
            }
        }
    </style>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('productbook::ProductBook.partials.back_to_dashboard_button')

    <div style="margin: 16px" class="no-print">
        @component('components.filters', ['title' => __('report.filters')])
            @include('productbook::ProductBook.partials.printbutton1', [
                'report_name' => __('minireportb1::minireportb1.product-015'),
                'print_by' => $businessInfo['user_name'] ?? 'System User',
            ])
        @endcomponent
    </div>

    @include('productbook::ProductBook.partials.header-toggle', [
        'report_name' => __('minireportb1::minireportb1.product-015'),
        'print_by' => $businessInfo['user_name'] ?? 'System User',
        'start_date' => $start_date ?? null,
        'end_date' => $end_date ?? null,
    ])

    <div class="reusable-table-container">
        <table class="reusable-table" id="package_price">
            <thead>
                <tr>
                    <th>#</th>
                    <th>@lang('sale.product')</th>
                    <th>@lang('purchase.business_location')</th>
                    @can('view_purchase_price')
                        <th>@lang('lang_v1.unit_purchase_price')</th> {{-- Fixed typo: 'perchase' → 'purchase' --}}
                    @endcan
                    @can('access_default_selling_price')
                        <th>@lang('lang_v1.selling_price')</th>
                    @endcan
                    <th>@lang('report.current_stock')</th>
                    <th>@lang('product.product_type')</th>
                    <th>@lang('product.category')</th>
                    <th>@lang('product.brand')</th>
                    <th>@lang('product.tax')</th>
                    <th>@lang('product.sku')</th>
                    <th id="cf_1">{{ $custom_labels['product']['custom_field_1'] ?? '' }}</th>
                    <th id="cf_2">{{ $custom_labels['product']['custom_field_2'] ?? '' }}</th>
                    <th id="cf_3">{{ $custom_labels['product']['custom_field_3'] ?? '' }}</th>
                    <th id="cf_4">{{ $custom_labels['product']['custom_field_4'] ?? '' }}</th>
                    <th id="cf_5">{{ $custom_labels['product']['custom_field_5'] ?? '' }}</th>
                    <th id="cf_6">{{ $custom_labels['product']['custom_field_6'] ?? '' }}</th>
                    <th id="cf_7">{{ $custom_labels['product']['custom_field_7'] ?? '' }}</th>
                    <th>@lang('messages.action')</th>
                </tr>
            </thead>
        </table>
    </div>
@endsection

@section('javascript')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            const canViewPurchasePrice = @json(auth()->user()->can('view_purchase_price'));
            const canAccessSellingPrice = @json(auth()->user()->can('access_default_selling_price'));
            const userName = "{{ $businessInfo['user_name'] ?? 'System User' }}";

            const package_price = $('#package_price').DataTable({
                processing: true,
                serverSide: true,
                lengthMenu: [
                    [10, 25, 50, 100, 500, -1],
                    [10, 25, 50, 100, 500, "All"]
                ],
                pageLength: 10,
                ajax: {
                    url: "{{ route('sr_productPackagePrice1') }}",
                    type: 'GET',
                    dataSrc: function(json) {
                        if (json.error) {
                            console.error('Server error:', json.error);
                            alert('Error loading data: ' + json.error);
                            return [];
                        }
                        if (window.updateDateRangeDisplay && json.start_date && json.end_date) {
                            window.updateDateRangeDisplay(json.start_date, json.end_date);
                        }
                        return json.data || [];
                    },
                    error: function(xhr) {
                        console.error('DataTables Ajax error:', xhr);
                        alert('Failed to load data. Check your connection and try again.');
                    }
                },
                columns: [{
                        data: null,
                        name: 'id',
                        orderable: false,
                        render: (data, type, row, meta) => meta.row + meta.settings._iDisplayStart + 1
                    },
                    {
                        data: 'name',
                        name: 'products.name'
                    },
                    {
                        data: 'product_locations',
                        name: 'product_locations',
                        orderable: false,
                        searchable: false
                    },
                    @can('view_purchase_price')
                        {
                            data: 'max_purchase_price',
                            name: 'max_purchase_price',
                            orderable: false,
                            searchable: false
                        },
                    @endcan
                    @can('access_default_selling_price')
                        {
                            data: 'max_price',
                            name: 'max_price',
                            orderable: false,
                            searchable: false
                        },
                    @endcan {
                        data: 'current_stock',
                        name: 'current_stock',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'type',
                        name: 'products.type'
                    },
                    {
                        data: 'category',
                        name: 'c1.name'
                    },
                    {
                        data: 'brand',
                        name: 'brands.name'
                    },
                    {
                        data: 'tax',
                        name: 'tax_rates.name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'sku',
                        name: 'products.sku'
                    },
                    {
                        data: 'product_custom_field1',
                        name: 'products.product_custom_field1',
                        orderable: false,
                        visible: $('#cf_1').text().length > 0
                    },
                    {
                        data: 'product_custom_field2',
                        name: 'products.product_custom_field2',
                        orderable: false,
                        visible: $('#cf_2').text().length > 0
                    },
                    {
                        data: 'product_custom_field3',
                        name: 'products.product_custom_field3',
                        orderable: false,
                        visible: $('#cf_3').text().length > 0
                    },
                    {
                        data: 'product_custom_field4',
                        name: 'products.product_custom_field4',
                        orderable: false,
                        visible: $('#cf_4').text().length > 0
                    },
                    {
                        data: 'product_custom_field5',
                        name: 'products.product_custom_field5',
                        orderable: false,
                        visible: $('#cf_5').text().length > 0
                    },
                    {
                        data: 'product_custom_field6',
                        name: 'products.product_custom_field6',
                        orderable: false,
                        visible: $('#cf_6').text().length > 0
                    },
                    {
                        data: 'product_custom_field7',
                        name: 'products.product_custom_field7',
                        orderable: false,
                        visible: $('#cf_7').text().length > 0
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [1, 'asc']
                ],
                dom: '<"row top-controls-container"<"col-md-6"B><"col-md-6 top-right-controls"fl>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
                buttons: [{
                        extend: 'colvis',
                        text: 'Columns <i class="fas fa-caret-down"></i>'
                    },
                    {
                        extend: 'csv',
                        text: 'CSV <i class="fa fa-file-csv"></i>'
                    },
                    {
                        extend: 'excel',
                        text: 'Excel <i class="fa fa-file-excel"></i>'
                    },
                    {
                        extend: 'pdf',
                        text: 'PDF <i class="fa fa-file-pdf"></i>'
                    },
                    {
                        extend: 'print',
                        text: 'Print <i class="fa fa-print"></i>'
                    }
                ]
            });

            // Reload table on date filter change
            $('#date_range_filter').on('apply.daterangepicker', () => package_price.ajax.reload());

            // Handle individual product print
            $('#package_price tbody').on('click', '.print-product-btn', function() {
                const data = package_price.row($(this).parents('tr')).data();
                if (data) printProduct(data);
            });

            function printProduct(data) {
                const productId = data?.id || data?.product_id;
                if (!productId) {
                    alert('Product ID not found.');
                    console.error('Missing product ID:', data);
                    return;
                }

                const printWindow = window.open('', '_blank');
                printWindow.document.write(`
                <html>
                    <head><title>Loading...</title></head>
                    <body style="font-family: Arial, sans-serif; padding: 20px; text-align: center;">
                        <h3>Loading product details...</h3>
                    </body>
                </html>
    `);
                printWindow.document.close();
                printWindow.focus();

                fetch(`/productbook/standardreport/products/${productId}/details`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                            'Accept': 'application/json',
                        },
                    })
                    .then(response => {
                        if (!response.ok) throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            throw new Error('Expected JSON response');
                        }
                        return response.json();
                    })
                    .then(freshData => {
                        if (!freshData?.success || !freshData.data) {
                            throw new Error('Invalid or failed response');
                        }
                        generatePrintContent(freshData.data, printWindow);
                    })
                    .catch(err => {
                        console.error('❌ Error:', err);
                        printWindow.document.write(`
            <html>
                <head><title>Error</title></head>
                <body style="color: red; font-family: Arial, sans-serif; padding: 20px;">
                    <h2>Failed to Load Product</h2>
                    <p>${err.message}</p>
                    <p>Check console (F12) for details.</p>
                </body>
            </html>
        `);
                        printWindow.document.close();
                    });
            }


            function generatePrintContent(data, printWindow) {
                if (!data || typeof data !== 'object') {
                    printWindow.close();
                    alert('Invalid product data received.');
                    return;
                }

                // Business and user info from Blade
                const businessInfo = {
                    name: `{!! addslashes($businessInfo['name'] ?? 'Business Name') !!}`,
                    logo: `{!! $businessInfo['logo_url'] ?? '' !!}`,
                    location: `{!! addslashes($businessInfo['location'] ?? 'N/A') !!}`,
                    print_by: `{!! addslashes($businessInfo['user_name'] ?? 'System User') !!}`
                };
                const canViewPurchasePrice = @json(auth()->user()->can('view_purchase_price'));
                const canAccessSellingPrice = @json(auth()->user()->can('access_default_selling_price'));

                // Helper functions
                function formatCurrency(amount) {
                    if (isNaN(amount) || amount === null || amount === '') return '0.00';
                    return new Intl.NumberFormat('en', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(parseFloat(amount));
                }

                function getImageHtml(mediaArray) {
                    if (!Array.isArray(mediaArray) || mediaArray.length === 0) return 'N/A';
                    return mediaArray.map(media =>
                        `<img src="${media.image_url || '/img/product-default.png'}" width="60" height="60" style="margin:2px; border:1px solid #ddd;">`
                    ).join('');
                }

                  const v = data.variations[0];

                // === 1. Basic Product Info ===
                let basicHtml = `
                    <div class="print-section-title">Product Information</div>
                        <table class="print-table">
                            <tr><th>Product Name</th><td>${data.name || 'N/A'}</td></tr>
                            <!--
                            <tr><th>SKU</th><td>${data.sku || 'N/A'}</td></tr>
                            <tr><th>Location(s)</th><td>${data.product_locations || 'N/A'}</td></tr>
                            <tr><th>Category</th><td>${data.category || 'N/A'}</td></tr>
                            <tr><th>Brand</th><td>${data.brand || 'N/A'}</td></tr>
                            <tr><th>Type</th><td>${data.type || 'N/A'}</td></tr>
                            <tr><th>Tax</th><td>${data.tax || 'N/A'}</td></tr>
                            <tr><th>Current Stock</th><td>${data.current_stock || 'N/A'}</td></tr>

                            -->
                            <tr><th>Price</th><td>${v.sell_price_inc_tax || 'N/A'}</td></tr>

                        </table>
                    </div>
                    `;
                ['product_custom_field1', 'prodct_custom_field2', 'product_custom_field3',
                    'product_custom_field4', 'product_custom_field5', 'product_custom_field6',
                    'product_custom_field7'
                ]
                .forEach((field, i) => {
                    const label = $(`#cf_${i+1}`).text().trim();
                    if (label && data[field]) {
                        basicHtml += `<tr><th>${label}</th><td>${data[field]}</td></tr>`;
                    }
                });
                basicHtml += `</table>`;

                // === 2. Pricing Table ===
                let pricingHtml = '';
                if (canViewPurchasePrice || canAccessSellingPrice) {
                    let showPricingTable = true;
                    let p_exc_tax, p_inc_tax, s_exc_tax, s_inc_tax;

                    // Handle product types with correct case ('Single', 'Variable', 'Combo')
                    if (data.type === 'Variable') {
                        // For variable products, prices are shown in the variations table, so hide this main pricing table.
                        showPricingTable = false;
                    } else if ((data.type === 'Single' || data.type === 'Combo') && Array.isArray(data
                        .variations) && data.variations.length > 0) {
                        // For single and combo products, the price is taken from the first variation.
                        const v = data.variations[0];
                        p_exc_tax = v.default_purchase_price;
                        p_inc_tax = v.dpp_inc_tax;
                        s_exc_tax = v.default_sell_price;
                        s_inc_tax = v.sell_price_inc_tax;
                    } else {
                        // Fallback to original logic if the structure is unexpected.
                        p_exc_tax = data.max_purchase_price;
                        p_inc_tax = data.dpp_inc_tax;
                        s_exc_tax = data.max_price;
                        s_inc_tax = data.sell_price_inc_tax;
                    }

                    if (showPricingTable) {
                        pricingHtml = `
            <div class="print-section-title">Pricing Details</div>
           
                         <table class="print-table">
                <thead>
                    <tr>
                        ${canViewPurchasePrice ? '<th>Purchase (Exc Tax)</th><th>Purchase (Inc Tax)</th>' : ''}
                        ${canAccessSellingPrice ? '<th>Sell (Exc Tax)</th><th>Sell (Inc Tax)</th>' : ''}
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        ${canViewPurchasePrice ? `
                                                <td>${formatCurrency(p_exc_tax)}</td>
                                                <td>${formatCurrency(p_inc_tax || p_exc_tax)}</td>
                                            ` : ''}
                        ${canAccessSellingPrice ? `
                                                <td>${s_exc_tax}</td>
                                                <td>${s_inc_tax || s_exc_tax}</td>
                                            ` : ''}
                    </tr>
                </tbody>
            </table>

                    </div>
        `;
                    }
                }

                // === 3. Combo Items Table ===
           let comboHtml = '';
            if (data.type === 'Combo' && data.combo_variations) {
                Object.entries(data.combo_variations).forEach(([categoryId, items]) => {
                    if (!Array.isArray(items) || items.length === 0) return;

                    const categoryName = items[0]?.variation?.product?.category?.name || 'Uncategorized';

                    comboHtml += `
                        <div class="print-section-title">${categoryName}</div>
                        <table class="print-table">
                            <thead>
                                <tr>
                        <th>Item</th>
                        ${canViewPurchasePrice ? '<th>Quantity</th>' : ''}
                        <th>Image</th>
                    </tr>
                </thead>
                <tbody>
        `;

        items.forEach(item => {
            const variation = item.variation;
            const product = variation.product;
            const quantity = parseFloat(item.quantity) || 0;
            const costExc = parseFloat(variation.default_purchase_price) || 0;
            const totalCost = (costExc * quantity).toFixed(2);

            // Generate tiny image with badge ONLY when no image
            function getTinyImageHtml(imageName) {
                if (!imageName) {
                    return `
                        <div style="
                            width: auto;
                            min-width: 24px;
                            height: 30px;
                            background: #f5f5f5;
                            border: 1px solid #ddd;
                            border-radius: 4px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            color: #aaa;
                            font-size: 9px;
                            padding: 0;
                            margin: 0;
                            overflow: hidden;
                        ">
                            No
                        </div>
                    `;
                }
                const imgUrl = '/uploads/img/' + encodeURIComponent(imageName);
                return `
                    <div style="
                        position: relative;
                        width: auto;
                        min-width: 24px;
                        height: 30px;
                        border: 1px solid #ddd;
                        border-radius: 4px;
                        overflow: hidden;
                        padding: 0;
                        margin: 0;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    ">
                        <img src="${imgUrl}" alt="Item"
                             style="
                                 height: 30px;
                                 width: auto;
                                 max-width: 60px;
                                 object-fit: fill;
                                 padding: 0;
                                 margin: 0;
                             ">
                        <!-- ✅ Badge is REMOVED when image exists -->
                    </div>
                `;
            }

           comboHtml += `
                <tr>
                    <td style="padding: 2px 4px; line-height: 1.2;">
                        <div style="font-size:12px;">${product?.name || 'N/A'}</div>
                        <div style="font-size:10px; color:#555;">
                            ${variation.is_variation ? variation.variation_name : ''}
                            (${variation.sub_sku || 'N/A'})
                        </div>
                    </td>
                    ${canViewPurchasePrice ? `
                        <td style="padding: 2px 4px; text-align:right; font-size:12px;">
                            ${quantity}
                        </td>
                    ` : ''}
                    <td style="padding: 2px; text-align:center;">
                        ${getTinyImageHtml(product?.image)}
                    </td>
                </tr>
            `;
        });

        comboHtml += `</tbody></table>`;
    });
}

                // === 4. Variations Table ===
                let variationHtml = '';
                if (Array.isArray(data.variations) && data.variations.length > 0) {
                    variationHtml = `
            <div class="print-section-title">Variations (${data.variations.length})</div>
            <table class="print-table">
                <thead>
                    <tr>
                        ${canViewPurchasePrice ? '<th>Cost (Exc)</th><th>Cost (Inc)</th>' : ''}
                        ${canAccessSellingPrice ? '<th>Sell (Exc)</th><th>Sell (Inc)</th>' : ''}
                        ${Object.keys(data.allowed_group_prices || {}).length > 0 ? '<th>Group Prices</th>' : ''}
                        <th>Image</th>
                    </tr>
                </thead>
                <tbody>
        `;
                    data.variations.forEach(v => {
                        variationHtml += `<tr>`;
                        if (canViewPurchasePrice) {
                            variationHtml += `
                    <td>${formatCurrency(v.default_purchase_price)}</td>
                    <td>${formatCurrency(v.dpp_inc_tax)}</td>
                `;
                        }
                        if (canAccessSellingPrice) {
                            variationHtml += `
                    <td>${formatCurrency(v.default_sell_price)}</td>
                    <td>${formatCurrency(v.sell_price_inc_tax)}</td>
                `;
                        }
                        if (Object.keys(data.allowed_group_prices || {}).length > 0) {
                            variationHtml += `<td>`;
                            Object.entries(data.allowed_group_prices).forEach(([id, name]) => {
                                const gp = data.group_price_details?.[v.id]?.[id];
                                variationHtml +=
                                    `<strong>${name}:</strong> ${gp ? formatCurrency(gp.calculated_price) : '0.00'}<br>`;
                            });
                            variationHtml += `</td>`;
                        }
                        variationHtml += `<td>${getImageHtml(v.media || [])}</td>`;
                        variationHtml += `</tr>`;
                    });
                    variationHtml += `</tbody></table>`;
                }

                // === Final HTML structure from printbutton.blade.php ===
                const productContent = `
                <div class="image-with-dimensions">
                @if(!empty($data->image_url))
                    <img src="{{ $data->image_url }}" alt="Product Image">
                @else
                    <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#aaa;">
                        No Image
                    </div>
                @endif
                <div class="image-dimension-badge">100px × 100%</div>
            </div>
        ${basicHtml}
        ${comboHtml}
        ${variationHtml}
    `;

                const reportTitle = `PRODUCT-015-product package price`;
                const reportLink = window.location.href;

                const printContent = `
    <!DOCTYPE html>
    <html>
    <head>
        <title>${reportTitle}</title>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"><\/script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"><\/script>
        <style>
           body {
                font-family: Roboto, sans-serif; margin: 16px; padding: 0; color: #333; counter-reset: page;
           }
           .report-header {
                margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;
                background-color: #f8f9fa; padding: 12px; position: relative;
           }
        .product-image {
                width: 100%;
                height: 100px;
                object-fit: fill;
            }
                .image-with-dimensions {
    position: relative;
    width: 100%;
    height: 100px;
    overflow: hidden;
    border: 2px dashed #ddd;
    border-radius: 8px;
    background-color: #f5f5f5;
}

.image-with-dimensions img {
    width: 100%;
    height: 100px;
    object-fit: fill;
    display: block;
}

.image-dimension-badge {
    position: absolute;
    bottom: 4px;
    right: 6px;
    background-color: rgba(0, 0, 0, 0.6);
    color: #fff;
    font-size: 11px;
    font-family: monospace;
    padding: 2px 6px;
    border-radius: 4px;
    white-space: nowrap;
    pointer-events: none; /* Prevent interfering with clicks */
    z-index: 10;
}
           .header-left { display: flex; align-items: center; z-index: 1; flex: 1; }
           .business-logo { max-height: 40px; max-width: 40px; margin-right: 12px; }
           .business-name { font-size: 12.8px; font-weight: bold; }
           .business-location { font-size: 8.8px; color: #666; }
           .page-number { font-size: 8.8px; color: #666; margin-top: 2px; }
           .header-right { text-align: right; z-index: 1; flex: 1; }
           .report-name { font-size: 11.2px; font-weight: bold; margin-bottom: 4px; }
           .date-range { font-size: 8.8px; margin-top: 4px; }
           .bold-name { font-weight: bold; }
           .header-center { position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); text-align: center; z-index: 0; }
           #item_qrcode img { display: block; margin: auto; }
           .additional-info { text-align: center; margin: 8px 0; font-size: 11.2px; }
           .print-button-window {
                display: block; margin: 16px auto; padding: 8px 16px; background-color: #0f8800;
                color: white; border: none; border-radius: 3.2px; font-size: 11.2px; cursor: pointer;
           }
           table, .print-table {
                width: 100%; border-collapse: collapse; font-size: 8.8px; margin-top: 16px;
           }
           table th, .print-table th {
                background-color: #f8f9fa; border: 0.8px solid #dee2e6; padding: 6.4px;
                text-align: left; font-weight: bold; font-size: 9.6px;
           }
           table td, .print-table td {
                border: 0.8px solid #dee2e6; padding: 6.4px; text-align: left; vertical-align: top;
           }
           table tr:nth-child(even), .print-table tr:nth-child(even) { background-color: #f9f9f9; }
           .print-section-title { margin-top: 20px; margin-bottom: 10px; font-size: 1.2em; font-weight: bold; }

           @media print {
               a { text-decoration: none; color: #000; }
               .print-button-window { display: none; }
               body { margin: 0; padding: 0; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
               table th, .print-table th { background-color: #f8f9fa !important; }
               table tr:nth-child(even), .print-table tr:nth-child(even) { background-color: #f9f9f9 !important; }
               .report-header { background-color: #f8f9fa !important; }
               tr, .print-table tr { page-break-inside: avoid; }
               .no-print { display: none; }
               .page-number { display: block; }
           }
           @media screen { .page-number { display: none; } }
           @page { margin: 20mm 15mm 25mm 15mm; counter-increment: page; @bottom-center { content: "Page " counter(page); font-size: 20px; } }
        </style>
    </head>
    <body>
        <button class="print-button-window" onclick="handleManualPrint()">Print Details</button>

       

        ${productContent}

        <script>
            function handleManualPrint() { window.print(); }

            window.onload = function() {
                try {
                    new QRCode(document.getElementById("item_qrcode"), {
                        text: "${reportLink}", margin: 2, width: 80, height: 80,
                        quietZone: 5, colorDark: "#000000", colorLight: "#ffffff",
                    });
                } catch (e) {
                    console.error("Error generating QR Code:", e);
                }

                setTimeout(() => window.print(), 500);
            };

            let printCompleted = false;
            window.onafterprint = function() {
                printCompleted = true;
                setTimeout(() => window.close(), 100);
            };
            
            setTimeout(function() {
                if (!printCompleted) {
                    window.close();
                }
            }, 30000);
        <\/script>
    </body>
    </html>
    `;
                printWindow.document.open();
                printWindow.document.write(printContent);
                printWindow.document.close();
            }
        });
    </script>
@endsection
