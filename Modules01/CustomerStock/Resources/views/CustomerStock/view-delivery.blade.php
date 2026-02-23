<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('customerstock::lang.delivery_details') #{{ $deliveryItems->first()->delivery_id ?? 'N/A' }}</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-6">
                    <p><b>@lang('messages.date'):</b> {{ @format_date($deliveryItems->first()->created_at) }}</p>
                    <b>@lang('customerstock::lang.invoice'):</b> #{{ $transaction->invoice_no ?? 'N/A' }}<br>
                </div>
                <div class="col-sm-6">
                    <strong>@lang('customerstock::lang.created_by'):</strong> {{ $name }}<br>
                    <strong>@lang('customerstock::lang.printed_by'):</strong> {{ $print_by }}
                </div>
            </div>

            <br>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr class="bg-gray">
                            <th>@lang('customerstock::lang.product')</th>
                            <th class="text-right">@lang('customerstock::lang.qty_reserved')</th>
                            <th class="text-right">@lang('customerstock::lang.qty_delivered')</th>
                            <th class="text-right">@lang('customerstock::lang.qty_remaining')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deliveryItems as $item)
                            @php
                                $product = \App\Product::with(['unit', 'second_unit'])->find($item->product_id);
                                $unitName = $product && $product->unit ? $product->unit->short_name : 'N/A';
                            @endphp
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center;">
                                        
                                        <div>
                                            <span>{{ $product ? $product->name : 'Product #' . $item->product_id }}</span>
                                            @if($unitName != 'N/A')
                                                <small class="text-muted">({{ $unitName }})</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-right">
                                    {{ number_format($item->qty_reserved, 2) }}
                                    @if($unitName != 'N/A')
                                        <small class="text-muted">{{ $unitName }}</small>
                                    @endif
                                </td>
                                <td class="text-right">
                                    {{ number_format($item->qty_delivered, 2) }}
                                    @if($unitName != 'N/A')
                                        <small class="text-muted">{{ $unitName }}</small>
                                    @endif
                                </td>
                                <td class="text-right">
                                    {{ number_format($item->qty_remaining, 2) }}
                                    @if($unitName != 'N/A')
                                        <small class="text-muted">{{ $unitName }}</small>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        <tr class="bg-gray">
                            <td><strong>@lang('customerstock::lang.total'):</strong></td>
                            <td class="text-right"><strong>{{ number_format($deliveryItems->sum('qty_reserved'), 2) }}
                                @if($unitName != 'N/A')
                                    <small class="text-muted">{{ $unitName }}</small>
                                @endif
                            </strong></td>
                            <td class="text-right"><strong>{{ number_format($deliveryItems->sum('qty_delivered'), 2) }}
                                @if($unitName != 'N/A')
                                    <small class="text-muted">{{ $unitName }}</small>
                                @endif
                            </strong></td>
                            <td class="text-right"><strong>{{ number_format($deliveryItems->sum('qty_remaining'), 2) }}
                                @if($unitName != 'N/A')
                                    <small class="text-muted">{{ $unitName }}</small>
                                @endif
                            </strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
            <button type="button" class="btn btn-primary" onclick="printDelivery({{ $deliveryItems->first()->delivery_id ?? 0 }})">
                <i class="fa fa-print"></i> @lang('messages.print')
            </button>
        </div>
    </div>
</div>

<script>
function printDelivery(deliveryId) {
    // Use the same route as your existing printRecord method
    var url = "{{ route('CustomerStock.printdelivery', ['id' => '__ID__']) }}";
    url = url.replace('__ID__', deliveryId);
    
    // Open in new window/tab and print
    var printWindow = window.open(url, '_blank');
    
    // Wait for content to load then print and close
    printWindow.onload = function() {
        // Small delay to ensure content is fully loaded
        setTimeout(function() {
            printWindow.print();
            printWindow.close();
        }, 1000);
    };
    
    // Close the current modal
    $('.modal').modal('hide');
}
</script>