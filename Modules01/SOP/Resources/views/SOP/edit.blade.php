<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('sop::lang.edit_sop')</h4>
        </div>
        <div class="modal-body">
            <form id="edit_SOP_form" method="POST" action="{{ route('SOP.update', $sop->id) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="sop_category_id">@lang('sop::lang.category'):</label>
                            <select class="form-control" id="sop_category_id" name="sop_category_id" style="width: 100%;">
                                <option value="">@lang('messages.select')</option>
                                @foreach ($sop_categories as $id => $category)
                                    <option value="{{ $id }}" {{ $sop->category_id == $id ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    
                    
                    
                    
                    
                    
                    
                        <div class="col-md-12">
        <div class="form-group">
            <label for="title_1">@lang('sop::lang.title_1'):</label>
            <input type="text" class="form-control" id="title_1" name="title_1" value="{{ $sop->{'title_1'} }}">
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="description_5">@lang('sop::lang.description_5'):</label>
            <textarea class="form-control summernote" rows="7" name="description_5">{!! $sop->{'description_5'} !!}</textarea>
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