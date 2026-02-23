@extends('layouts.app')
@section('title', __('customerstock::lang.CustomerStock'))
@section('content')
    @includeIf('customerstock::layouts.nav')
    <section class="content-header no-print">
        <h1>@lang('customerstock::lang.customerstock')</h1>
        <p style="margin-top: 24px; width: 80%;">@lang('customerstock::lang.description_module')</p>
    </section>
    <div style="margin: 16px" class="no-print">
        @component('components.filters', ['title' => __('report.filters')])
            <!-- Updated Calculator Component with Unique Classes -->
            <div class="unit-calc-btn-container">
                <button class="unit-calc-btn" id="mainButton" onclick="openCalculator()">Calculator</button>
            </div>
            @include('customerstock::CustomerStock.calculator')
        @endcomponent
    </div>

    <section class="content no-print">
        <div
            class="tw-transition-all lg:tw-col-span-1 tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl tw-ring-1 hover:tw-shadow-md hover:tw-translate-y-0.5 tw-ring-gray-200">
            <div class="tw-p-4 sm:tw-p-5">
                <div class="tw-flex tw-gap-2.5 tw-justify-end">
                    <a class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full btn-modal pull-right"
                        data-href="{{ action([\Modules\CustomerStock\Http\Controllers\CustomerStockController::class, 'create']) }}"
                        data-container=".customerstock_modal">
                        <i class="fa fa-plus"></i> @lang('messages.add')
                    </a>
                </div>
                <div class="tw-flow-root tw-mt-5 tw-border-b tw-border-gray-200">
                    <div class="tw-mx-4 tw--my-2 tw-overflow-x-auto sm:tw--mx-5">
                        <div class="tw-inline-block tw-min-w-full tw-py-2 tw-align-middle sm:tw-px-5">
                            <table class="table table-bordered table-striped" id="CustomerStock_table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>@lang('messages.action')</th>
                                        <th>@lang('customerstock::lang.created_by')</th>
                                        <th>@lang('customerstock::lang.customer')</th>
                                        <th>@lang('customerstock::lang.invoice')</th>
                                        <th>@lang('customerstock::lang.total_items')</th>
                                        <th>@lang('customerstock::lang.total_qty_reserved')</th>
                                        <th>@lang('customerstock::lang.total_qty_delivered')</th>
                                        <th>@lang('customerstock::lang.total_qty_remaining')</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade customerstock_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>
    </section>
@stop

@section('javascript')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            // Verify Bootstrap is loaded
            if (typeof $.fn.modal === 'undefined') {
                console.error('Bootstrap modal plugin is not loaded');
                toastr.error('Bootstrap modal plugin is not available');
                return;
            }

            // Initialize DataTable
            var table = $('#CustomerStock_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ action([\Modules\CustomerStock\Http\Controllers\CustomerStockController::class, 'index']) }}",
                columns: [{
                        data: null,
                        name: 'index',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'create_by',
                        name: 'create_by'
                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name'
                    },
                    {
                        data: 'invoice_no',
                        name: 'invoice_no'
                    },
                    {
                        data: 'total_items',
                        name: 'total_items'
                    },
                    {
                        data: 'total_qty_reserved',
                        name: 'total_qty_reserved'
                    },
                    {
                        data: 'total_qty_delivered',
                        name: 'total_qty_delivered'
                    },
                    {
                        data: 'total_qty_remaining',
                        name: 'total_qty_remaining'
                    }
                ]
            });

            // Function to initialize Select2
            function initializeSelect2() {
                if ($('#invoice_id').length && !$('#invoice_id').hasClass('select2-hidden-accessible')) {
                    $('#invoice_id').select2({
                        dropdownParent: $('.customerstock_modal'), // Important: Set parent to modal
                        ajax: {
                            url: '/customerstock/invoices/search',
                            dataType: 'json',
                            delay: 250,
                            processResults: function(data) {
                                var results = [];
                                for (var item in data) {
                                    results.push({
                                        id: data[item].id,
                                        text: data[item].text,
                                    });
                                }
                                return {
                                    results: results,
                                };
                            },
                            cache: true // Add caching for better performance
                        },
                        minimumInputLength: 1,
                        closeOnSelect: false,
                        placeholder: 'Search for invoices...',
                        allowClear: true,
                        width: '100%' // Ensure full width
                    });
                    console.log('Select2 initialized successfully');
                }
            }

            // Function to destroy Select2 safely
            function destroySelect2() {
                if ($('#invoice_id').length && $('#invoice_id').hasClass('select2-hidden-accessible')) {
                    $('#invoice_id').select2('destroy');
                    console.log('Select2 destroyed');
                }
            }

            // Handle btn-modal click to load modal content
            $(document).on('click', '.btn-modal', function(e) {
                e.preventDefault();
                var container = $(this).data('container') || '.customerstock_modal';
                var href = $(this).data('href');
                console.log('Modal button clicked:', href, 'Container:', container);

                if ($(container).length === 0) {
                    console.error('Modal container not found:', container);
                    toastr.error('Modal container not found');
                    return;
                }

                // Destroy any existing Select2 before loading new content
                destroySelect2();

                $.ajax({
                    url: href,
                    dataType: 'html',
                    success: function(data) {
                        console.log('Modal content loaded (first 100 chars):', data.substring(0,
                            100));
                        $(container).html(data);

                        // Show modal
                        $(container).modal({
                            backdrop: 'static',
                            keyboard: false
                        }).modal('show');

                        console.log('Modal shown for:', href);
                    },
                    error: function(xhr, status, error) {
                        console.error('Modal load error:', xhr.responseText);
                        toastr.error('Failed to load modal content: ' + error);
                    }
                });
            });

            // Enhanced modal event handlers
            $(document).on('shown.bs.modal', '.customerstock_modal', function() {
                console.log('Modal shown event triggered');

                // Use setTimeout to ensure DOM is fully ready
                setTimeout(function() {
                    initializeSelect2();
                    console.log('Select2 initialized after modal shown');
                }, 100);
            });

            // Alternative approach using MutationObserver (modern approach)
            var modalContainer = document.querySelector('.customerstock_modal');
            if (modalContainer) {
                var observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                            var invoiceSelect = $(mutation.target).find('#invoice_id');
                            if (invoiceSelect.length > 0 && !invoiceSelect.hasClass(
                                    'select2-hidden-accessible')) {
                                setTimeout(function() {
                                    initializeSelect2();
                                    console.log('Select2 initialized via MutationObserver');
                                }, 100);
                            }
                        }
                    });
                });

                observer.observe(modalContainer, {
                    childList: true,
                    subtree: true
                });
            }

            // Clean up on modal hide
            $(document).on('hidden.bs.modal', '.customerstock_modal', function() {
                console.log('Modal hidden event triggered');
                destroySelect2();
                $(this).removeData('bs.modal'); // Clean up modal data
            });

            // Force re-initialization on focus (as a backup)
            $(document).on('focus', '#invoice_id', function() {
                if (!$(this).hasClass('select2-hidden-accessible')) {
                    console.log('Force initializing Select2 on focus');
                    initializeSelect2();
                }
            });

            // Form submission handler for add customer stock
            $(document).on('submit', '#add_CustomerStock_form', function(e) {
                e.preventDefault();
                var $form = $(this);
                var $submitBtn = $form.find('button[type="submit"]');

                $.ajax({
                    method: $form.attr('method'),
                    url: $form.attr('action'),
                    data: $form.serialize(),
                    beforeSend: function() {
                        $submitBtn.prop('disabled', true).text('Processing...');
                    },
                    success: function(result) {
                        if (result.success) {
                            $('.customerstock_modal').modal('hide');
                            table.ajax.reload();
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Failed to submit form');
                        console.error('Form submission error:', xhr.responseText);
                    },
                    complete: function() {
                        $submitBtn.prop('disabled', false).text('Save');
                    }
                });
            });

            // Form submission handler for delivery
            $(document).on('submit', '#delivery_form', function(e) {
                e.preventDefault();
                var $form = $(this);
                var $submitBtn = $form.find('#delivery_submit_btn');

                $.ajax({
                    method: $form.attr('method'),
                    url: $form.attr('action'),
                    data: $form.serialize(),
                    dataType: 'json',
                    beforeSend: function() {
                        $submitBtn.prop('disabled', true).text('Processing...');
                    },
                    success: function(result) {
                        if (result.success) {
                            $('.customerstock_modal').modal('hide');
                            table.ajax.reload();
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Failed to process delivery');
                        console.error('Delivery submission error:', xhr.responseText);
                    },
                    complete: function() {
                        $submitBtn.prop('disabled', false).text('Process Delivery');
                    }
                });
            });

            // Delivery quantity validation
            $(document).on('input', '.delivery-qty-input', function() {
                var maxValue = parseFloat($(this).attr('max'));
                var currentValue = parseFloat($(this).val());

                if (currentValue > maxValue) {
                    $(this).val(maxValue);
                    toastr.error('Delivery quantity cannot exceed remaining quantity');
                }
                if (currentValue < 0) {
                    $(this).val(0);
                    toastr.error('Delivery quantity cannot be negative');
                }
            });

            // Delete CustomerStock handler
            $(document).on('click', '.delete-CustomerStock', function() {
                var deleteUrl = $(this).data('href');

                Swal.fire({
                    title: '@lang('customerstock::lang.are_you_sure')',
                    text: "@lang('customerstock::lang.delete_confirmation_text')",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '@lang('customerstock::lang.ok')',
                    cancelButtonText: '@lang('customerstock::lang.cancel')'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            method: 'DELETE',
                            url: deleteUrl,
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(result) {
                                if (result.success) {
                                    toastr.success(result.msg);
                                    table.ajax.reload();
                                } else {
                                    toastr.error(result.msg);
                                }
                            },
                            error: function(xhr) {
                                toastr.error('@lang('messages.something_went_wrong')');
                                console.error('Delete error:', xhr.responseText);
                            }
                        });
                    }
                });
            });

            // Delete delivery handler
            $(document).on('click', '.delete-delivery', function() {
                var deleteUrl = $(this).data('href');
                var invoiceId = $(this).data('invoice-id');

                Swal.fire({
                    title: '@lang('lang_v1.are_you_sure')',
                    text: "@lang('customerstock::lang.delete_delivery_confirmation')",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '@lang('messages.ok')',
                    cancelButtonText: '@lang('messages.cancel')'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            method: 'DELETE',
                            url: deleteUrl,
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(result) {
                                if (result.success) {
                                    toastr.success(result.msg);
                                    // Reload the show modal using the invoice ID
                                    $('.btn-modal[data-href="' +
                                        "{{ route('CustomerStock.show', ':id') }}"
                                        .replace(':id', invoiceId) + '"]').trigger(
                                        'click');
                                } else {
                                    toastr.error(result.msg);
                                }
                            },
                            error: function(xhr) {
                                toastr.error('@lang('lang_v1.something_went_wrong')');
                                console.error('Delete delivery error:', xhr
                                    .responseText);
                            }
                        });
                    }
                });
            });

            // Enhanced change handler for invoice selection
            $(document).on('change', '#invoice_id', function() {
                console.log('Invoices selected:', $(this).val());
                // Add any additional logic here if needed
            });
        });
    </script>
@endsection
