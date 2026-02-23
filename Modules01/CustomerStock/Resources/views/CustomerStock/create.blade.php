<div class="modal-dialog" role="document">
    <div class="modal-content">
        {!! Form::open([
            'url' => action([\Modules\CustomerStock\Http\Controllers\CustomerStockController::class, 'store']),
            'method' => 'post',
            'id' => 'add_CustomerStock_form'
        ]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('customerstock::lang.add_invoice')</h4>
    </div>
        <div class="modal-body">
            <div class="form-group">
                <label>@lang('customerstock::lang.invoice')</label>
                <select id="invoice_id" class="form-control" name="invoice_id[]" multiple></select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>