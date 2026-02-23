<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('customerstock::lang.edit_customerstock')</h4>
        </div>
        <div class="modal-body">
            <form id="edit_CustomerStock_form" method="POST" action="{{ route('CustomerStock.update', $customerstock->id) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="customerstock_category_id">@lang('customerstock::lang.category'):</label>
                            <select class="form-control" id="customerstock_category_id" name="customerstock_category_id" style="width: 100%;">
                                <option value="">@lang('messages.select')</option>
                                @foreach ($customerstock_categories as $id => $category)
                                    <option value="{{ $id }}" {{ $customerstock->category_id == $id ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    
                    
                    
                    
                    
                    
                    
                        <div class="col-md-12">
        <div class="form-group">
            <label for="customer_id">@lang('customerstock::lang.customer_id'):</label>
            <input type="number" class="form-control" id="customer_id" name="customer_id" value="{{ $customerstock->{'customer_id'} }}" step="any">
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="invoice_id_5">@lang('customerstock::lang.invoice_id_5'):</label>
            <input type="number" class="form-control" id="invoice_id_5" name="invoice_id_5" value="{{ $customerstock->{'invoice_id_5'} }}" step="any">
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="product_id_6">@lang('customerstock::lang.product_id_6'):</label>
            <input type="number" class="form-control" id="product_id_6" name="product_id_6" value="{{ $customerstock->{'product_id_6'} }}" step="any">
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="qty_reserved_7">@lang('customerstock::lang.qty_reserved_7'):</label>
            <input type="number" class="form-control" id="qty_reserved_7" name="qty_reserved_7" value="{{ $customerstock->{'qty_reserved_7'} }}" step="any">
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="qty_delivered_8">@lang('customerstock::lang.qty_delivered_8'):</label>
            <input type="number" class="form-control" id="qty_delivered_8" name="qty_delivered_8" value="{{ $customerstock->{'qty_delivered_8'} }}" step="any">
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="qty_remaining_9">@lang('customerstock::lang.qty_remaining_9'):</label>
            <input type="number" class="form-control" id="qty_remaining_9" name="qty_remaining_9" value="{{ $customerstock->{'qty_remaining_9'} }}" step="any">
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="status_10">@lang('customerstock::lang.status_10'):</label>
            <input type="text" class="form-control" id="status_10" name="status_10" value="{{ $customerstock->{'status_10'} }}">
        </div>
    </div>
                </div>
                
                <div class="form-group text-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
                    <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
                </div>
            </form>
        </div>
    </div>
</div>
