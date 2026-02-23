<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action([\Modules\CustomerStock\Http\Controllers\CustomerStockController::class, 'processDelivery']), 'method' => 'post', 'id' => 'delivery_form']) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('customerstock::lang.delivery_products')</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>@lang('customerstock::lang.invoice'):  {{ $invoice_id ?? 'N/A' }}</label>
                <p class="form-control-static">
                    @if(isset($invoice_id))
                       
                    @else
                        N/A
                    @endif
                </p>
                @if(isset($invoice_id) && !empty($invoice_id))
                    <input type="hidden" name="invoice_id" value="{{ $invoice_id }}">
                @else
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-triangle"></i> Error: Invoice ID is missing.
                    </div>
                @endif
            </div>

            <!-- Delivery Date Field -->
            <div class="form-group">
                <label for="delivery_date">@lang('customerstock::lang.delivery_date'):</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="delivery_date" name="delivery_date" 
                           value="{{ date('Y-m-d') }}" required>
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                </div>
            </div>

            @if(isset($customerStockItems) && $customerStockItems->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>@lang('customerstock::lang.product')</th>
                                <th>@lang('customerstock::lang.reserved')</th>
                                <th>@lang('customerstock::lang.delivered')</th>
                                <th>@lang('customerstock::lang.remaining')</th>
                                <th>@lang('customerstock::lang.deliver_qty')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customerStockItems as $item)
                                @php
                                    $product = null;
                                    $unitName = 'N/A';
                                    $productImage = null;
                                    $hasValidImage = false;
                                    
                                    if(isset($item->product_id)) {
                                        $product = \App\Product::with(['unit', 'second_unit'])->find($item->product_id);
                                        $unitName = $product && $product->unit ? $product->unit->short_name : 'N/A';
                                        
                                        // Handle product image
                                        if($product) {
                                            $productImage = $product->image_url ?? null;
                                            if($productImage && !empty($productImage)) {
                                                // Check if it's a valid image URL (basic check)
                                                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
                                                $extension = strtolower(pathinfo($productImage, PATHINFO_EXTENSION));
                                                $hasValidImage = in_array($extension, $imageExtensions);
                                            }
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center" style="display: flex; align-items: center; gap: 8px;">
                                            <!-- Product Image -->
                                            <div class="product-image-container mr-3" style="width: 50px; height: 50px;">
                                                @if($hasValidImage)
                                                    <img src="{{ $productImage }}" alt="{{ $product->name ?? 'Product' }}" 
                                                         class="img-thumbnail" style="max-width: 100%; max-height: 50px;">
                                                @else
                                                    <div class="no-image-placeholder bg-light d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px; border-radius: 4px;">
                                                        <i class="fa fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Product Details -->
                                            <div>
                                                <strong>{{ $product ? $product->name : ('Product #' . ($item->product_id ?? 'N/A')) }}</strong>
                                                @if($unitName != 'N/A')
                                                    <small class="text-muted">({{ $unitName }})</small>
                                                @endif
                                                @if(isset($item->id))
                                                    <input type="hidden" name="stock_ids[]" value="{{ $item->id }}">
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ isset($item->qty_reserved) ? number_format($item->qty_reserved, 2) : 'N/A' }} 
                                        @if($unitName != 'N/A') {{ $unitName }} @endif
                                    </td>
                                    <td>{{ isset($item->qty_delivered) ? number_format($item->qty_delivered, 2) : 'N/A' }} 
                                        @if($unitName != 'N/A') {{ $unitName }} @endif
                                    </td>
                                    <td>{{ isset($item->qty_remaining) ? number_format($item->qty_remaining, 2) : 'N/A' }} 
                                        @if($unitName != 'N/A') {{ $unitName }} @endif
                                    </td>
                                    <td>
                                        @if(isset($item->id) && isset($item->qty_remaining))
                                            <div class="input-group">
                                                <input type="number"
                                                    name="delivery_qty[{{ $item->id }}]"
                                                    class="form-control input-sm delivery-qty-input"
                                                    min="0"
                                                    max="{{ $item->qty_remaining }}"
                                                    step="0.01"
                                                    placeholder="0.00"
                                                    data-max="{{ $item->qty_remaining }}"
                                                    value="0">
                                                <span class="input-group-addon">{{ $unitName }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-warning">
                    <i class="fa fa-warning"></i> @lang('customerstock::lang.no_items_for_delivery')
                </div>
            @endif
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
            @if((isset($invoice_id) && !empty($invoice_id)) && (isset($customerStockItems) && $customerStockItems->isNotEmpty()))
                <button type="submit" class="btn btn-primary" id="delivery_submit_btn">
                    @lang('customerstock::lang.delivery')
                </button>
            @else
                <button type="button" class="btn btn-primary" disabled>
                    @lang('customerstock::lang.delivery')
                </button>
            @endif
        </div>
        {!! Form::close() !!}
    </div>
</div>

<script>
 $(document).ready(function() {
    // Initialize date picker for delivery date
    $('#delivery_date').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true
    });
});
</script>