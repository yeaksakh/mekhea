<div class="modal-dialog modal-xl no-print" role="document">
    <div class="modal-content" style="margin-bottom: 36px;">
        <div class="modal-header">
            <div class="flex modal-header d-flex justify-content-end align-items-center" style="display: flex; justify-content: flex-end; align-items: center;">
                <!-- Close Button -->
                <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white no-print" data-dismiss="modal"
                    style="border-radius: 10px 10px 10px 10px !important;">
                    @lang('messages.close')
                </button>
            </div>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-sm-4">
                    <p><b>@lang('messages.date'):</b>
                        @if (isset($customerStockItems) && $customerStockItems->isNotEmpty())
                            {{ @format_date($customerStockItems->first()->created_at) }}
                        @else
                            {{ @format_date(now()) }}
                        @endif
                    </p>
                    <b>@lang('customerstock::lang.invoice'):</b> #{{ $transaction->invoice_no ?? 'N/A' }}<br>
                    <b>@lang('customerstock::lang.status'):</b> @lang('customerstock::lang.active')
                    <br>
                    <br>
                    <strong>@lang('customerstock::lang.created_by'):</strong>
                    @if (!empty($name))
                        {{ $name }}
                    @endif
                </div>

                <div class="col-sm-4">
                    <b>@lang('customerstock::lang.customer'):</b>
                    @if (isset($transaction) && $transaction->contact)
                        {{ $transaction->contact->name }}<br>
                        <b>@lang('business.address'):</b><br>
                        {!! $transaction->contact->contact_address ?? '--' !!}
                        @if ($transaction->contact->mobile)
                            <br>{{ __('contact.mobile') }}: {{ $transaction->contact->mobile }}
                        @endif
                    @else
                        N/A
                    @endif
                </div>

                <div class="col-sm-4">
                    <strong>@lang('customerstock::lang.printed_by'):</strong>
                    {{ $print_by }}<br>
                    <strong>@lang('customerstock::lang.printed_on'):</strong>
                    {{ now()->format('d/m/Y H:i:s') }}<br>
                    @if (!empty($date_range))
                        <strong>@lang('report.date_range'):</strong> {{ $date_range }}
                    @endif
                </div>
            </div>

            <br>

            <div class="row">
                <div class="col-sm-12 col-xs-12">
                    <h4>@lang('customerstock::lang.product_summary_latest'):</h4>
                </div>

                <div class="col-sm-12 col-xs-12">
                    <div class="table-responsive">
                        @if (isset($latestProductSummary) && !empty($latestProductSummary))
                            <table class="table table-bordered table-striped" style="margin-bottom: 36px;">
                                <thead>
                                    <tr class="bg-gray" style="background-color: #f5f5f5;">
                                        <th>@lang('customerstock::lang.product_name')</th>
                                        <th class="text-right">@lang('customerstock::lang.total_qty_reserved')</th>
                                        <th class="text-right">@lang('customerstock::lang.total_qty_delivered')</th>
                                        <th class="text-right">@lang('customerstock::lang.total_qty_remaining')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($latestProductSummary as $product)
                                        @php
                                            // Get unit information for the product
                                            $productModel = \App\Product::with(['unit', 'second_unit'])->find($product['product_id']);
                                            $unitName = $productModel && $productModel->unit ? $productModel->unit->short_name : 'N/A';
                                            
                                            // Handle product image with better validation
                                            $imageSrc = null;
                                            $hasValidImage = false;
                                            
                                            if (isset($product['image_url']) && !empty($product['image_url'])) {
                                                // Check if it's a valid image URL
                                                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
                                                $extension = strtolower(pathinfo($product['image_url'], PATHINFO_EXTENSION));
                                                
                                                // Check if it's a URL or a relative path
                                                if (filter_var($product['image_url'], FILTER_VALIDATE_URL)) {
                                                    // It's a URL
                                                    $hasValidImage = in_array($extension, $imageExtensions);
                                                    if ($hasValidImage) {
                                                        $imageSrc = $product['image_url'];
                                                    }
                                                } else {
                                                    // It's a relative path
                                                    $hasValidImage = in_array($extension, $imageExtensions);
                                                    if ($hasValidImage) {
                                                        $imageSrc = asset($product['image_url']);
                                                    }
                                                }
                                            }
                                        @endphp
                                        <tr>
                                            <td>
                                                <div style="display: flex; align-items: center;">
                                                    <div class="product-image-container mr-3" style="width: 50px; height: 50px; flex-shrink: 0;">
                                                        @if($hasValidImage)
                                                            <img src="{{ $imageSrc }}"
                                                                alt="{{ $product['name'] }}"
                                                                width="50" height="50"
                                                                style="border: 1px solid #ddd; object-fit: cover; border-radius: 4px;"
                                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                            <div class="no-image-placeholder bg-light d-flex align-items-center justify-content-center" 
                                                                 style="width: 50px; height: 50px; border: 1px dashed #ccc; border-radius: 4px; display: none;">
                                                                <i class="fa fa-image text-muted"></i>
                                                            </div>
                                                        @else
                                                            <div class="no-image-placeholder bg-light d-flex align-items-center justify-content-center" 
                                                                 style="width: 50px; height: 50px; border: 1px dashed #ccc; border-radius: 4px;">
                                                                <i class="fa fa-image text-muted"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <span>{{ $product['name'] }}</span>
                                                        @if($unitName != 'N/A')
                                                            <small class="text-muted">({{ $unitName }})</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-right">
                                                {{ number_format($product['qty_reserved'], 2) }}
                                                @if($unitName != 'N/A')
                                                    <small class="text-muted">{{ $unitName }}</small>
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                {{ number_format($product['qty_delivered'], 2) }}
                                                @if($unitName != 'N/A')
                                                <small class="text-muted">{{ $unitName }}</small>
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                {{ number_format($product['qty_remaining'], 2) }}
                                                @if($unitName != 'N/A')
                                                <small class="text-muted">{{ $unitName }}</small>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="alert alert-info">
                                <p>@lang('customerstock::lang.no_products_found')</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <br>

            <div class="row">
                <div class="col-sm-12 col-xs-12">
                    <h4>@lang('customerstock::lang.delivery_data'):</h4>
                </div>

                <div class="col-sm-12 col-xs-12">
                    <div class="table-responsive">
                        @if (isset($deliverySummary) && !empty($deliverySummary))
                            <table class="table table-bordered table-striped" style="margin-bottom: 36px;">
                                <thead>
                                    <tr class="bg-gray" style="background-color: #f5f5f5;">
                                        <th>@lang('customerstock::lang.index')</th>
                                        <th>@lang('customerstock::lang.delivery_date')</th>
                                        <th class="text-right">@lang('customerstock::lang.total_qty_delivered')</th>
                                        <th style="text-align: center;" class="no-print">@lang('messages.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($deliverySummary as $index => $delivery)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ \Carbon\Carbon::parse($delivery['delivery_date'])->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="text-right">
                                                {{ number_format($delivery['totals']['qty_delivered'], 2) }}
                                            </td>
                                            <td style="text-align: center;" class="no-print">
                                                <button type="button" class="btn btn-primary btn-xs"
                                                    onclick="viewDelivery({{ $delivery['delivery_id'] }})">
                                                    <i class="fa fa-eye"></i> @lang('messages.view')
                                                </button>
                                                <button type="button" class="btn btn-info btn-xs"
                                                    onclick="editDelivery({{ $delivery['delivery_id'] }})"
                                                    style="margin-left: 5px;">
                                                    <i class="fa fa-edit"></i> @lang('messages.edit')
                                                </button>
                                                <button type="button" class="btn btn-danger btn-xs"
                                                    onclick="deleteDelivery({{ $delivery['delivery_id'] }})"
                                                    style="margin-left: 5px;">
                                                    <i class="fa fa-trash"></i> @lang('messages.delete')
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="alert alert-info">
                                <p>@lang('customerstock::lang.no_deliveries_found')</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        var element = $('div.modal-xl');
        __currency_convert_recursively(element);
    });
</script>

<script>
    function viewDelivery(deliveryId) {
        // Open delivery details in a new modal or page
        var url = "{{ route('CustomerStock.showdelivery', ['id' => '__ID__']) }}";
        url = url.replace('__ID__', deliveryId);
        
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                // Create new modal for delivery details
                var modalHtml = `
                    <div class="modal fade" id="viewDeliveryModal" tabindex="-1" role="dialog">
                        ${response}
                    </div>
                `;
                $('body').append(modalHtml);
                $('#viewDeliveryModal').modal('show');
                
                // Cleanup when closed
                $('#viewDeliveryModal').on('hidden.bs.modal', function() {
                    $(this).remove();
                });
            },
            error: function() {
                toastr.error('Failed to load delivery details');
            }
        });
    }

   function deleteDelivery(deliveryId) {
    Swal.fire({
        title: "@lang('customerstock::lang.are_you_sure')",
        text: "@lang('customerstock::lang.delete_delivery_confirmation')",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "@lang('customerstock::lang.ok')",
        cancelButtonText: "@lang('customerstock::lang.cancel')"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/customerstock/delete-delivery/${deliveryId}`,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    if (result.success) {
                        Swal.fire(
                            "@lang('customerstock::lang.success')",
                            result.msg,
                            "success"
                        );
                        // Reload the modal or redirect
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        Swal.fire(
                            "@lang('customerstock::lang.error')",
                            result.msg,
                            "error"
                        );
                    }
                },
                error: function(xhr) {
                    Swal.fire(
                        "@lang('customerstock::lang.error')",
                        "@lang('lang_v1.something_went_wrong')",
                        "error"
                    );
                }
            });
        }
    });
}
</script>

<script>
function editDelivery(deliveryId) {
    // Open edit form in a new modal - USE THE EDIT ROUTE, NOT UPDATE ROUTE
    var url = "{{ route('CustomerStock.edit-delivery', ['delivery_id' => '__ID__']) }}"; // Changed this line
    url = url.replace('__ID__', deliveryId);
    
    $.ajax({
        url: url,
        type: 'GET',
        beforeSend: function() {
            // Show loading indicator
            $('body').append('<div id="loading-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center;"><div style="color: white; font-size: 20px;">Loading...</div></div>');
        },
        success: function(response) {
            // Remove loading indicator
            $('#loading-overlay').remove();
            
            // Create new modal for edit form
            var modalHtml = `
                <div class="modal fade" id="editDeliveryModal" tabindex="-1" role="dialog" aria-labelledby="editDeliveryModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            ${response}
                        </div>
                    </div>
                </div>
            `;
            $('body').append(modalHtml);
            $('#editDeliveryModal').modal('show');
            
            // Cleanup when closed
            $('#editDeliveryModal').on('hidden.bs.modal', function() {
                $(this).remove();
            });
        },
        error: function(xhr, status, error) {
            // Remove loading indicator
            $('#loading-overlay').remove();
            
            // Show error in modal
            var errorModalHtml = `
                <div class="modal fade" id="editDeliveryModal" tabindex="-1" role="dialog" aria-labelledby="editDeliveryModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Error</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-danger">
                                    <h4>Failed to load edit form</h4>
                                    <p>Error: ${error}</p>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('body').append(errorModalHtml);
            $('#editDeliveryModal').modal('show');
            
            // Cleanup when closed
            $('#editDeliveryModal').on('hidden.bs.modal', function() {
                $(this).remove();
            });
        }
    });
}
</script>