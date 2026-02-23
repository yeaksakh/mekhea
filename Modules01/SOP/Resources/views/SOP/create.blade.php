<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action([\Modules\SOP\Http\Controllers\SOPController::class, 'store']), 'method' => 'post', 'id' => 'add_SOP_form' ]) !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('sop::lang.add_SOP')</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="sop_category_id">@lang('sop::lang.category'):</label>
                            <select class="form-control select2" id="sop_category_id" name="sop_category_id" style="width: 100%;">
                                <option value="">@lang('messages.select')</option>
                                @foreach ($sop_categories as $id => $category)
                                    <option value="{{ $id }}">{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    
                    
                    
                    
                    
                    
                    
                    
                        <div class="col-md-12">
        <div class="form-group">
            <label for="title_1">@lang('sop::lang.title_1'):</label>
            <input type="text" class="form-control" id="title_1" name="title_1">
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="description_5">@lang('sop::lang.description_5'):</label>
            <textarea class="form-control summernote" name="description_5" rows="3"></textarea>
        </div>
    </div>
                    
                </div>
                <hr>
                <div class="form-group text-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
                    <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
                </div>
            </div>
        {!! Form::close() !!}
    </div>
</div>
