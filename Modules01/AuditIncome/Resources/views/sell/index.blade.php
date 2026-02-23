@extends('layouts.app')
@section('title', __('lang_v1.all_sales'))

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        {{-- <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('sale.sells')</h1> --}}
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang("auditincome::lang.auditincome")</h1>
    </section>



    <!-- Main content -->
    <section class="content no-print">
        @include('auditb1::sell.partials.how_to_use')
        @component('components.filters', ['title' => __('report.filters')])
            @include('auditb1::sell.partials.sell_list_filters')

            @if ($payment_types)
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('payment_method', __('lang_v1.payment_method') . ':') !!}
                        {!! Form::select('payment_method', $payment_types, null, [
                            'class' => 'form-control select2',
                            'style' => 'width:100%',
                            'placeholder' => __('lang_v1.all'),
                        ]) !!}
                    </div>
                </div>
            @endif

            @if (!empty($sources))
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('sell_list_filter_source', __('lang_v1.sources') . ':') !!}

                        {!! Form::select('sell_list_filter_source', $sources, null, [
                            'class' => 'form-control select2',
                            'style' => 'width:100%',
                            'placeholder' => __('lang_v1.all'),
                        ]) !!}
                    </div>
                </div>
            @endif

            <div>
                <button onclick="printAllInvoices()" class="btn btn-primary">
                    <i class="fa fa-print" aria-hidden="true"></i> Print All Invoices
                </button>

                <button id="printAllPaymentsBtn" onclick="printAllPaymentDetails()" class="btn btn-success">
                    <i class="fas fa-comment-dollar"></i> Print All Payment Details
                </button>
            </div>
        @endcomponent

        <img src="{{ asset('modules/auditincome/images/audit_income.jpg') }}" alt="Audit Income Icon" style="width:100%; height: 150px; margin-bottom: 10px;">

        @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.all_sales')])
            @can('direct_sell.access')
                @slot('tool')
                    <div class="box-tools">
                        <a class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right"
                            href="{{ action([\Modules\AuditIncome\Http\Controllers\SellController::class, 'create']) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 5l0 14" />
                                <path d="M5 12l14 0" />
                            </svg> @lang('messages.add')
                        </a>
                    </div>
                @endslot
            @endcan



            @if (auth()->user()->can('direct_sell.view') ||
                    auth()->user()->can('view_own_sell_only') ||
                    auth()->user()->can('view_commission_agent_sell'))
                @php
                    $custom_labels = json_decode(session('business.custom_labels'), true);
                @endphp
                <table class="table table-bordered table-striped ajax_view" id="sell_table">
                    <thead>
                        <tr>
                            <th>@lang('messages.action')</th>
                            <th>@lang('messages.date')</th>
                            <th>@lang('sale.invoice_no')</th>
                            <th>@lang('sale.customer_name')</th>
                            <th>@lang('sale.customer_group')</th>
                            <th>@lang('sale.customer_address')</th>
                            <th>@lang('lang_v1.contact_no')</th>
                            <th>@lang('sale.location')</th>
                            <th>@lang('lang_v1.audit_status')</th>
                            <th>@lang('sale.payment_status')</th>
                            <th>@lang('lang_v1.payment_method')</th>
                            <th>@lang('sale.total_amount')</th>
                            <th>@lang('sale.total_paid')</th>
                            <th>@lang('lang_v1.sell_due')</th>
                            <th>@lang('lang_v1.sell_return_due')</th>
                            <th>@lang('lang_v1.shipping_status')</th>
                            <th>@lang('lang_v1.total_items')</th>
                            <th>@lang('lang_v1.types_of_service')</th>
                            <th>{{ $custom_labels['types_of_service']['custom_field_1'] ?? __('lang_v1.service_custom_field_1') }}
                            </th>
                            <th>{{ $custom_labels['sell']['custom_field_1'] ?? '' }}</th>
                            <th>{{ $custom_labels['sell']['custom_field_2'] ?? '' }}</th>
                            <th>{{ $custom_labels['sell']['custom_field_3'] ?? '' }}</th>
                            <th>{{ $custom_labels['sell']['custom_field_4'] ?? '' }}</th>
                            <th>@lang('lang_v1.added_by')</th>
                            <th>@lang('sale.sell_note')</th>
                            <th>@lang('sale.staff_note')</th>
                            <th>@lang('sale.shipping_details')</th>
                            <th>@lang('restaurant.table')</th>
                            <th>@lang('restaurant.service_staff')</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr class="bg-gray font-17 footer-total text-center">

                            <td colspan="7"><strong>@lang('sale.total'):</strong></td>
                            <td class="footer_payment_status_count"></td>
                            <td class="payment_method_count"></td>
                            <td class="footer_sale_total"></td>
                            <td class="footer_total_paid"></td>
                            <td class="footer_total_remaining"></td>
                            <td class="footer_total_sell_return_due"></td>
                            <td colspan="2"></td>
                            <td class="service_type_count"></td>
                            <td colspan="7"></td>
                        </tr>
                    </tfoot>
                </table>
            @endif
        @endcomponent
    </section>
    <!-- /.content -->
    <div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <!-- This will be printed -->
    <section class="invoice print_section" id="receipt_section">
    </section>

@stop

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            //Date range as a button
            $('#sell_list_filter_date_range').daterangepicker(
                dateRangeSettings,
                function(start, end) {
                    $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(
                        moment_date_format));
                    sell_table.ajax.reload();
                }
            );
            $('#sell_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#sell_list_filter_date_range').val('');
                sell_table.ajax.reload();
            });

            sell_table = $('#sell_table').DataTable({
                processing: true,
                serverSide: true,
                fixedHeader: false,
                aaSorting: [
                    [1, 'desc']
                ],
                "ajax": {
                    "url": "/auditincome/AuditIncome-sells",
                    "data": function(d) {
                        if ($('#sell_list_filter_date_range').val()) {
                            var start = $('#sell_list_filter_date_range').data('daterangepicker')
                                .startDate.format('YYYY-MM-DD');
                            var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate
                                .format('YYYY-MM-DD');
                            d.start_date = start;
                            d.end_date = end;
                        }
                        d.is_direct_sale = 1;

                        d.location_id = $('#sell_list_filter_location_id').val();
                        d.customer_id = $('#sell_list_filter_customer_id').val();
                        d.payment_status = $('#sell_list_filter_payment_status').val();
                        d.created_by = $('#created_by').val();
                        d.sales_cmsn_agnt = $('#sales_cmsn_agnt').val();
                        d.service_staffs = $('#service_staffs').val();
                        d.audit_status = $('#audit_status').val();

                        if ($('#shipping_status').length) {
                            d.shipping_status = $('#shipping_status').val();
                        }
                        if ($('#audit_status').length) {
                            d.audit_status = $('#audit_status').val();
                        }
                        if ($('#address').length) {
                            d.address = $('#address').val();
                        }

                        if ($('#sell_list_filter_source').length) {
                            d.source = $('#sell_list_filter_source').val();
                        }

                        if ($('#only_subscriptions').is(':checked')) {
                            d.only_subscriptions = 1;
                        }

                        if ($('#payment_method').length) {
                            d.payment_method = $('#payment_method').val();
                        }

                        d = __datatable_ajax_callback(d);
                    }
                },
                scrollY: "75vh",
                scrollX: true,
                scrollCollapse: true,
                columns: [{
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        "searchable": false
                    },
                    {
                        data: 'transaction_date',
                        name: 'transaction_date'
                    },
                    {
                        data: 'invoice_no',
                        name: 'invoice_no'
                    },
                    {
                        data: 'conatct_name',
                        name: 'conatct_name'
                    },
                    {
                        data: 'contact_group',
                        name: 'contact_group'
                    },
                    {
                        data: 'contact_address',
                        name: 'contact_address',
                        visible: false
                    },
                    {
                        data: 'mobile',
                        name: 'contacts.mobile'
                    },
                    {
                        data: 'business_location',
                        name: 'bl.name'
                    },
                    {
                        data: 'audit_status',
                        name: 'audit_status'
                    },
                    {
                        data: 'payment_status',
                        name: 'payment_status'
                    },
                    {
                        data: 'payment_methods',
                        orderable: false,
                        "searchable": false
                    },

                    {
                        data: 'final_total',
                        name: 'final_total'
                    },
                    {
                        data: 'total_paid',
                        name: 'total_paid',
                        "searchable": false
                    },
                    {
                        data: 'total_remaining',
                        name: 'total_remaining'
                    },
                    {
                        data: 'return_due',
                        orderable: false,
                        "searchable": false
                    },
                    {
                        data: 'shipping_status',
                        name: 'shipping_status'
                    },
                    {
                        data: 'total_items',
                        name: 'total_items',
                        "searchable": false
                    },
                    {
                        data: 'types_of_service_name',
                        name: 'tos.name',
                        @if (empty($is_types_service_enabled))
                            visible: false
                        @endif
                    },
                    {
                        data: 'service_custom_field_1',
                        name: 'service_custom_field_1',
                        @if (empty($is_types_service_enabled))
                            visible: false
                        @endif
                    },
                    {
                        data: 'custom_field_1',
                        name: 'transactions.custom_field_1',
                        @if (empty($custom_labels['sell']['custom_field_1']))
                            visible: false
                        @endif
                    },
                    {
                        data: 'custom_field_2',
                        name: 'transactions.custom_field_2',
                        @if (empty($custom_labels['sell']['custom_field_2']))
                            visible: false
                        @endif
                    },
                    {
                        data: 'custom_field_3',
                        name: 'transactions.custom_field_3',
                        @if (empty($custom_labels['sell']['custom_field_3']))
                            visible: false
                        @endif
                    },
                    {
                        data: 'custom_field_4',
                        name: 'transactions.custom_field_4',
                        @if (empty($custom_labels['sell']['custom_field_4']))
                            visible: false
                        @endif
                    },
                    {
                        data: 'added_by',
                        name: 'u.first_name'
                    },
                    {
                        data: 'additional_notes',
                        name: 'additional_notes'
                    },
                    {
                        data: 'staff_note',
                        name: 'staff_note'
                    },
                    {
                        data: 'shipping_details',
                        name: 'shipping_details'
                    },
                    {
                        data: 'table_name',
                        name: 'tables.name',
                        @if (empty($is_tables_enabled))
                            visible: false
                        @endif
                    },
                    {
                        data: 'waiter',
                        name: 'ss.first_name',
                        @if (empty($is_service_staff_enabled))
                            visible: false
                        @endif
                    },
                ],
                "fnDrawCallback": function(oSettings) {
                    __currency_convert_recursively($('#sell_table'));
                },
                "footerCallback": function(row, data, start, end, display) {
                    var footer_sale_total = 0;
                    var footer_total_paid = 0;
                    var footer_total_remaining = 0;
                    var footer_total_sell_return_due = 0;
                    for (var r in data) {
                        footer_sale_total += $(data[r].final_total).data('orig-value') ? parseFloat($(
                            data[r].final_total).data('orig-value')) : 0;
                        footer_total_paid += $(data[r].total_paid).data('orig-value') ? parseFloat($(
                            data[r].total_paid).data('orig-value')) : 0;
                        footer_total_remaining += $(data[r].total_remaining).data('orig-value') ?
                            parseFloat($(data[r].total_remaining).data('orig-value')) : 0;
                        footer_total_sell_return_due += $(data[r].return_due).find('.sell_return_due')
                            .data('orig-value') ? parseFloat($(data[r].return_due).find(
                                '.sell_return_due').data('orig-value')) : 0;
                    }

                    $('.footer_total_sell_return_due').html(__currency_trans_from_en(
                        footer_total_sell_return_due));
                    $('.footer_total_remaining').html(__currency_trans_from_en(footer_total_remaining));
                    $('.footer_total_paid').html(__currency_trans_from_en(footer_total_paid));
                    $('.footer_sale_total').html(__currency_trans_from_en(footer_sale_total));

                    $('.footer_payment_status_count').html(__count_status(data, 'payment_status'));
                    $('.service_type_count').html(__count_status(data, 'types_of_service_name'));
                    $('.payment_method_count').html(__count_status(data, 'payment_methods'));
                },
                createdRow: function(row, data, dataIndex) {
                    $(row).find('td:eq(6)').attr('class', 'clickable_td');
                }
            });

            $(document).on('change',
                '#sell_list_filter_location_id, #sell_list_filter_customer_id, #address,#sell_list_filter_payment_status, #created_by, #sales_cmsn_agnt, #service_staffs, #shipping_status, #audit_status, #sell_list_filter_source, #payment_method',
                function() {
                    sell_table.ajax.reload();
                });

            $('#only_subscriptions').on('ifChanged', function(event) {
                sell_table.ajax.reload();
            });
        });
    </script>
    <script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>

    <script>
        function printAllInvoices() {
            // Select all print invoice buttons
            const printButtons = document.querySelectorAll('.print-invoice');

            // Create loading indicator
            const loadingIndicator = document.createElement('div');
            loadingIndicator.innerHTML = `
                            <div style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(255,255,255,0.8);z-index:9999;display:flex;justify-content:center;align-items:center;">
                                <div>
                                    <h3>Preparing invoices for printing...</h3>
                                    <div class="progress-text" style="text-align:center;font-size:20px;">0/${printButtons.length} invoices loaded</div>
                                    <div class="error-log" style="color:red;"></div>
                                </div>
                            </div>
                        `;
            document.body.appendChild(loadingIndicator);
            const errorLog = loadingIndicator.querySelector('.error-log');

            // Create a container for print content
            const printContainer = document.createElement('div');
            printContainer.id = 'print-container';
            printContainer.style.display = 'none';
            document.body.appendChild(printContainer);

            // Group print buttons by transaction ID
            const transactionGroups = {};
            printButtons.forEach(button => {
                const url = new URL(button.getAttribute('data-href'));
                const transactionId = url.pathname.split('/')[2];

                if (!transactionGroups[transactionId]) {
                    transactionGroups[transactionId] = [];
                }
                transactionGroups[transactionId].push(button);
            });

            // Function to fetch invoice content via AJAX
            function fetchInvoiceContent(printUrl) {
                return new Promise((resolve, reject) => {
                    fetch(printUrl, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            credentials: 'same-origin'
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success === 1) {
                                // Handle different possible response structures
                                if (data.receipt && data.receipt.html_content) {
                                    resolve(data.receipt.html_content);
                                } else if (data.receipt) {
                                    resolve(data.receipt);
                                } else {
                                    reject(new Error('No receipt content found'));
                                }
                            } else {
                                reject(new Error('Failed to fetch invoice content'));
                            }
                        })
                        .catch(error => reject(error));
                });
            }

            // Load invoices for each transaction
            async function loadTransactionInvoices(transactionId, buttons) {
                // Prefer main invoice first, then others
                const preferredOrder = [
                    button => !button.getAttribute('data-href').includes('package_slip') &&
                    !button.getAttribute('data-href').includes('delivery_note'),
                    button => true
                ];

                // Sort buttons to prioritize main invoice
                const sortedButtons = buttons.sort((a, b) => {
                    const aPreference = preferredOrder.findIndex(pref => pref(a));
                    const bPreference = preferredOrder.findIndex(pref => pref(b));
                    return aPreference - bPreference;
                });

                // Take the first button after sorting
                const selectedButton = sortedButtons[0];

                try {
                    // Fetch invoice content
                    const invoiceContent = await fetchInvoiceContent(selectedButton.getAttribute('data-href'));

                    // Create a div for this invoice
                    const invoiceDiv = document.createElement('div');

                    // Add the HTML content
                    invoiceDiv.innerHTML = invoiceContent;
                    invoiceDiv.classList.add('invoice-print-page');
                    printContainer.appendChild(invoiceDiv);
                } catch (error) {
                    console.error(`Error loading invoice for transaction ${transactionId}:`, error);
                }
            }

            // Prepare content for printing
            async function prepareAndPrintInvoices() {
                // Process each transaction group
                for (const [transactionId, buttons] of Object.entries(transactionGroups)) {
                    await loadTransactionInvoices(transactionId, buttons);
                }

                // Remove loading indicator
                loadingIndicator.remove();

                // If no invoices, show an error
                if (printContainer.children.length === 0) {
                    alert('No invoices could be loaded for printing.');
                    return;
                }

                // Add print-specific styles
                const styleElement = document.createElement('style');
                styleElement.innerHTML = `
                                @media print {
                                    body * {
                                        visibility: hidden;
                                    }
                                    #print-container, 
                                    #print-container * {
                                        visibility: visible !important;
                                        position: static !important;
                                    }
                                    #print-container {
                                        position: absolute;
                                        left: 0;
                                        top: 0;
                                        width: 100%;
                                    }
                                    .invoice-print-page {
                                        page-break-after: always;
                                        margin: 0;
                                        padding: 0;
                                    }
                                    .invoice-print-page:last-child {
                                        page-break-after: avoid;
                                    }
                                }
                            `;
                document.head.appendChild(styleElement);

                // Append all print content directly to body for better rendering
                document.body.appendChild(printContainer);
                printContainer.style.display = 'block';

                // Print the content
                window.print();

                // Clean up
                printContainer.remove();
                styleElement.remove();
            }

            // Start the process
            prepareAndPrintInvoices();
        }

        // Add a button to trigger printing all invoices
        function addPrintAllInvoicesButton() {
            const containerDiv = document.createElement('div');

            // Find a suitable location to add the button (adjust selector as needed)
            const invoiceTableOrContainer = document.querySelector('.invoice-table, .table-responsive');
            if (invoiceTableOrContainer) {
                invoiceTableOrContainer.insertBefore(containerDiv, invoiceTableOrContainer.firstChild);
            } else {
                document.body.insertBefore(containerDiv, document.body.firstChild);
            }
        }

        // Call this function when the page loads
        document.addEventListener('DOMContentLoaded', addPrintAllInvoicesButton);
    </script>

    <script>
        function printAllPaymentDetails() {
            // Get all possible payment buttons using more specific selector
            const allButtons = document.querySelectorAll(
                'a[href*="transaction-payment/show"], a.view_payment_modal, [onclick*="showPayments"]');

            // Filter to only unique buttons by comparing their URLs
            const uniqueButtons = [];
            const seenUrls = new Set();

            allButtons.forEach(button => {
                let url = button.getAttribute('href') || button.getAttribute('data-href');

                // Skip buttons without URLs
                if (!url) return;

                // Normalize URL by removing query parameters and hash
                url = url.split('?')[0].split('#')[0];

                if (!seenUrls.has(url)) {
                    seenUrls.add(url);
                    uniqueButtons.push(button);
                }
            });

            if (uniqueButtons.length === 0) {
                alert('No unique payment buttons found. Please check the page for view payment links.');
                return;
            }

            // Create loading indicator
            const loadingIndicator = document.createElement('div');
            loadingIndicator.innerHTML = `
            <div style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(255,255,255,0.8);z-index:9999;display:flex;justify-content:center;align-items:center;">
                <div>
                    <h3>Preparing payment details for printing...</h3>
                    <div class="progress-text" style="text-align:center;font-size:20px;">0/${uniqueButtons.length} payments loaded</div>
                    <div class="error-log" style="color:red;"></div>
                </div>
            </div>
        `;
            document.body.appendChild(loadingIndicator);
            const progressText = loadingIndicator.querySelector('.progress-text');
            const errorLog = loadingIndicator.querySelector('.error-log');

            // Create print container
            const printContainer = document.createElement('div');
            printContainer.id = 'print-payment-container';
            printContainer.style.display = 'none';
            document.body.appendChild(printContainer);

            // Function to fetch payment content
            function fetchPaymentContent(paymentUrl) {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: paymentUrl,
                        method: 'GET',
                        dataType: 'html',
                        success: function(response) {
                            if (response) {
                                resolve(response);
                            } else {
                                reject(new Error('No payment content found'));
                            }
                        },
                        error: function(xhr, status, error) {
                            reject(new Error('Failed to fetch payment content: ' + error));
                        }
                    });
                });
            }

            // Prepare content for printing
            async function prepareAndPrintPayments() {
                const successfulLoads = [];

                for (let i = 0; i < uniqueButtons.length; i++) {
                    const button = uniqueButtons[i];

                    try {
                        progressText.textContent = `${i + 1}/${uniqueButtons.length} payments loaded`;

                        let paymentUrl = button.getAttribute('href') || button.getAttribute('data-href');

                        if (!paymentUrl.startsWith('http')) {
                            paymentUrl = window.location.origin + (paymentUrl.startsWith('/') ? '' : '/') + paymentUrl;
                        }

                        const paymentContent = await fetchPaymentContent(paymentUrl);

                        const paymentDiv = document.createElement('div');
                        paymentDiv.innerHTML = paymentContent;

                        const modalContent = paymentDiv.querySelector('.modal-content') ||
                            paymentDiv.querySelector('.modal-dialog') ||
                            paymentDiv;

                        if (modalContent) {
                            // Check if we already have this content by comparing transaction IDs
                            const transactionIdMatch = paymentUrl.match(/show\/(\d+)/);
                            if (transactionIdMatch) {
                                const existing = printContainer.querySelector(
                                    `[data-transaction-id="${transactionIdMatch[1]}"]`);
                                if (existing) continue;
                                modalContent.setAttribute('data-transaction-id', transactionIdMatch[1]);
                            }

                            modalContent.classList.add('payment-print-page');
                            printContainer.appendChild(modalContent);
                            successfulLoads.push(true);
                        }
                    } catch (error) {
                        console.error(`Error loading payment ${i + 1}:`, error);
                        errorLog.innerHTML += `<p>Error loading payment ${i + 1}: ${error.message}</p>`;
                    }
                }

                loadingIndicator.remove();

                if (successfulLoads.length === 0) {
                    alert('Could not load any payment details. Please check console for errors.');
                    printContainer.remove();
                    return;
                }

                const styleElement = document.createElement('style');
                styleElement.innerHTML = `
                @media print {
                    body * {
                        visibility: hidden;
                    }
                    #print-payment-container, 
                    #print-payment-container * {
                        visibility: visible !important;
                        position: static !important;
                    }
                    #print-payment-container {
                        position: absolute;
                        left: 0;
                        top: 0;
                        width: 100%;
                    }
                    .payment-print-page {
                        page-break-after: always;
                        margin: 20px 0;
                        padding: 0;
                    }
                    .payment-print-page:last-child {
                        page-break-after: avoid;
                    }
                    .modal-dialog {
                        width: 100% !important;
                        margin: 0 !important;
                        max-width: none !important;
                    }
                    .modal-content {
                        border: none !important;
                        box-shadow: none !important;
                        page-break-inside: avoid;
                    }
                    .no-print {
                        display: none !important;
                    }
                    .print-only {
                        display: block !important;
                    }
                }
            `;
                document.head.appendChild(styleElement);

                document.body.appendChild(printContainer);
                printContainer.style.display = 'block';

                // Delay print to ensure content is rendered
                setTimeout(() => {
                    window.print();
                    setTimeout(() => {
                        printContainer.remove();
                        styleElement.remove();
                    }, 500);
                }, 500);
            }

            prepareAndPrintPayments();
        }

        // Add print button (with check for existing button)
        function addPrintAllPaymentsButton() {
            if (document.getElementById('printAllPaymentsBtn')) return;

            const buttonContainer = document.createElement('div');
            buttonContainer.style.display = 'inline-block';
            buttonContainer.style.marginLeft = '10px';

            const locations = [
                document.querySelector('.box-tools'),
                document.querySelector('.content-header .pull-right'),
                document.querySelector('.box-header .box-title'),
                document.querySelector('.filter-box')
            ].filter(el => el);

            if (locations.length > 0) {
                locations[0].appendChild(buttonContainer);
            } else {
                document.body.insertBefore(buttonContainer, document.body.firstChild);
            }
        }

        // Initialize
        if (document.readyState !== 'loading') {
            addPrintAllPaymentsButton();
        } else {
            document.addEventListener('DOMContentLoaded', addPrintAllPaymentsButton);
        }
    </script>
@endsection
