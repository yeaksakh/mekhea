<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action([\Modules\SWOT\Http\Controllers\SWOTController::class, 'store']), 'method' => 'post', 'id' => 'add_SWOT_form' ]) !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('swot::lang.add_SWOT')</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="swot_category_id">@lang('swot::lang.category'):</label>
                            <select class="form-control select2" id="swot_category_id" name="swot_category_id" style="width: 100%;">
                                <option value="">@lang('messages.select')</option>
                                @foreach ($swot_categories as $id => $category)
                                    <option value="{{ $id }}">{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    
                    
                    
                    
                    
                    
                    
                    
                        <div class="col-md-12">
        <div class="form-group">
            <label for="Title_1">@lang('swot::lang.Title_1'):</label>
            <input type="text" class="form-control" id="Title_1" name="Title_1">
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="Strengths_5">@lang('swot::lang.Strengths_5'):</label>
            <textarea class="form-control SWOT_description" name="Strengths_5" rows="3"></textarea>
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="Weaknesses_6">@lang('swot::lang.Weaknesses_6'):</label>
            <textarea class="form-control SWOT_description" name="Weaknesses_6" rows="3"></textarea>
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="Opportunities_7">@lang('swot::lang.Opportunities_7'):</label>
            <textarea class="form-control SWOT_description" name="Opportunities_7" rows="3"></textarea>
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="Threats_8">@lang('swot::lang.Threats_8'):</label>
            <textarea class="form-control SWOT_description" name="Threats_8" rows="3"></textarea>
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="Note_9">@lang('swot::lang.Note_9'):</label>
            <textarea class="form-control SWOT_description" name="Note_9" rows="3"></textarea>
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