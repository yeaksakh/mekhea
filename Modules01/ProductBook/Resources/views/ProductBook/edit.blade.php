<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('productbook::lang.edit_productbook')</h4>
        </div>
        <div class="modal-body">
            <form id="edit_ProductBook_form" method="POST" action="{{ route('ProductBook.update', $productbook->id) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="productbook_category_id">@lang('productbook::lang.category'):</label>
                            <select class="form-control" id="productbook_category_id" name="productbook_category_id" style="width: 100%;">
                                <option value="">@lang('messages.select')</option>
                                @foreach ($productbook_categories as $id => $category)
                                    <option value="{{ $id }}" {{ $productbook->category_id == $id ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    
                    
                    
                    
                    
                    
                    
                        <div class="col-md-12">
        <div class="form-group">
            <label for="title_1">@lang('productbook::lang.title_1'):</label>
            <input type="text" class="form-control" id="title_1" name="title_1" value="{{ $productbook->{'title_1'} }}">
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="description_5">@lang('productbook::lang.description_5'):</label>
            <!-- <input type="text" class="form-control" id="description_5" name="description_5" value="{{ $productbook->{'description_5'} }}"> -->
            <textarea class="form-control ProductBook_description" rows="7" name="description_5" value="{{ $productbook->{'description_5'} }}">{!! $productbook->{'description_5'} !!}</textarea>

            <!-- <textarea class="form-control summernote" rows="7" name="description_5" value="{{ $productbook->{'description_5'} }}">{!! $productbook->{'description_5'} !!}</textarea> -->
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
