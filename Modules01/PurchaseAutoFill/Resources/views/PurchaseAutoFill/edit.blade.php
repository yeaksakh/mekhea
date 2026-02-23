<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('purchaseautofill::lang.edit_purchaseautofill')</h4>
        </div>
        <div class="modal-body">
            <form id="edit_PurchaseAutoFill_form" method="POST" action="{{ route('PurchaseAutoFill.update', $purchaseautofill->id) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="purchaseautofill_category_id">@lang('purchaseautofill::lang.category'):</label>
                            <select class="form-control" id="purchaseautofill_category_id" name="purchaseautofill_category_id" style="width: 100%;">
                                <option value="">@lang('messages.select')</option>
                                @foreach ($purchaseautofill_categories as $id => $category)
                                    <option value="{{ $id }}" {{ $purchaseautofill->category_id == $id ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    
                    
                    
                    
                    
                    
                    
                        <div class="col-md-12">
        <div class="form-group">
            <label for="title_1">@lang('purchaseautofill::lang.title_1'):</label>
            <input type="text" class="form-control" id="title_1" name="title_1" value="{{ $purchaseautofill->{'title_1'} }}">
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="topic _5">@lang('purchaseautofill::lang.topic _5'):</label>
            <input type="text" class="form-control" id="topic _5" name="topic _5" value="{{ $purchaseautofill->{'topic _5'} }}">
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
