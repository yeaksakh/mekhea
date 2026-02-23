
    <div class="modal-content">
        {!! Form::open([
            'url' => route('CustomerStock.updateDelivery', ['delivery_id' => $deliveryItems->first()->delivery_id]),
            'method' => 'post',
            'id' => 'edit_delivery_form',
            'class' => 'ajax-submit',
        ]) !!}
        @csrf
        <input type="hidden" name="_method" value="POST"> <!-- Hidden input for method spoofing -->
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('customerstock::lang.edit_delivery')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <h4>@lang('customerstock::lang.invoice'):
                        @if (isset($transaction))
                            {{ $transaction->invoice_no ?? 'N/A' }}
                        @else
                            N/A
                        @endif
                    </h4>
                    @if (isset($invoice_id) && !empty($invoice_id))
                        <input type="hidden" name="invoice_id" value="{{ $invoice_id }}">
                        <input type="hidden" name="delivery_id" value="{{ $deliveryItems->first()->delivery_id }}">
                    @else
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-triangle"></i> Error: Invoice ID is missing.
                        </div>
                    @endif
                </div>
            </div>

            @if (isset($deliveryItems) && $deliveryItems->isNotEmpty())
                <div class="row" style="font-weight: bold; border-bottom: 2px solid #333; padding: 5px 0;">
                    <div class="col-md-4">@lang('customerstock::lang.product')</div>
                    <div class="col-md-2">@lang('customerstock::lang.reserved')</div>
                    <div class="col-md-2">@lang('customerstock::lang.current_delivered')</div>
                    <div class="col-md-2">@lang('customerstock::lang.remaining')</div>
                    <div class="col-md-2">@lang('customerstock::lang.new_deliver_qty')</div>
                </div>

                @foreach ($deliveryItems as $item)
                    <div class="row"
                        style="margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #eee;">
                        <div class="col-md-4">
                            @php
                                $product = \DB::table('products')->where('id', $item->product_id)->first();
                            @endphp
                            <strong>{{ $product ? $product->name : 'Product #' . $item->product_id }}</strong>
                            <input type="hidden" name="stock_ids[]" value="{{ $item->id }}">
                        </div>
                        <div class="col-md-2">{{ number_format($item->qty_reserved, 2) }}</div>
                        <div class="col-md-2">{{ number_format($item->qty_delivered, 2) }}</div>
                        <div class="col-md-2">{{ number_format($item->qty_remaining, 2) }}</div>
                        <div class="col-md-2">
                            <input type="number" name="delivery_qty[{{ $item->id }}]"
                                class="form-control input-sm delivery-qty-input" min="0"
                                max="{{ $item->qty_reserved }}" step="0.01" placeholder="0.00"
                                data-max="{{ $item->qty_reserved }}" value="{{ $item->qty_delivered }}">
                        </div>
                    </div>
                @endforeach

               
            @else
                <div class="alert alert-warning">
                    <i class="fa fa-warning"></i> No items available for editing.
                </div>
            @endif
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
            @if (isset($invoice_id) && !empty($invoice_id) && (isset($deliveryItems) && $deliveryItems->isNotEmpty()))
                <button type="submit" class="btn btn-primary" id="edit_delivery_submit_btn">
                    @lang('customerstock::lang.update_delivery')
                </button>
            @else
                <button type="button" class="btn btn-primary" disabled>
                    @lang('customerstock::lang.update_delivery')
                </button>
            @endif
        </div>
        {!! Form::close() !!}
    </div>


<script>
    // Use document ready to ensure DOM is loaded
    $(document).ready(function() {
        // Delivery quantity validation
        $(document).on('input', '.delivery-qty-input', function() {
            var maxValue = parseFloat($(this).data('max'));
            var currentValue = parseFloat($(this).val());

            if (currentValue > maxValue) {
                $(this).val(maxValue);
                toastr.warning('@lang('customerstock::lang.delivery_qty_cannot_exceed_reserved')');
            }

            if (currentValue < 0) {
                $(this).val(0);
            }
        });

        // Form submission - use document event delegation to ensure it works even if script loads before form
        $(document).off('submit', '#edit_delivery_form').on('submit', '#edit_delivery_form', function(e) {
            e.preventDefault(); // This is crucial to prevent normal form submission

            var $submitBtn = $(this).find('button[type="submit"]');

            $.ajax({
                method: 'POST',
                url: $(this).attr('action'),
                data: $(this).serialize(),
                beforeSend: function() {
                    $submitBtn.prop('disabled', true).text('@lang('customerstock::lang.processing')');
                },
                success: function(result) {
                    if (result.success) {
                        // Show success message
                        toastr.success(result.msg);

                        // Close modal and reload after a delay
                        setTimeout(function() {
                            $('#editDeliveryModal').modal('hide');

                            // Wait for modal to be hidden, then reload
                            $('#editDeliveryModal').on('hidden.bs.modal',
                        function() {
                                $(this).remove(); // Clean up the DOM
                                location.reload(); // Reload the main page
                            });
                        }, 800); // Small delay to ensure message is seen
                    } else {
                        toastr.error(result.msg);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to update delivery:', error);
                    console.error('Response:', xhr.responseText);
                    toastr.error('@lang('customerstock::lang.failed_to_update_delivery')');
                },
                complete: function() {
                    $submitBtn.prop('disabled', false).text('@lang('customerstock::lang.update_delivery')');
                }
            });
        });
    });
</script>
