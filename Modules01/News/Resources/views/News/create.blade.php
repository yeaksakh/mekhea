<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action([\Modules\News\Http\Controllers\NewsController::class, 'store']), 'method' => 'post', 'id' => 'add_News_form' ]) !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('news::lang.add_News')</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="news_category_id">@lang('news::lang.category'):</label>
                            <select class="form-control select2" id="news_category_id" name="news_category_id" style="width: 100%;">
                                <option value="">@lang('messages.select')</option>
                                @foreach ($news_categories as $id => $category)
                                    <option value="{{ $id }}">{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    
                    
                    
                    
                    
                    
                    
                    
                        <div class="col-md-12">
        <div class="form-group">
            <label for="title_1">@lang('news::lang.title_1'):</label>
            <input type="text" class="form-control" id="title_1" name="title_1">
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            {!! Form::label('image_5', __('news::lang.image_5') . ':') !!}
            {!! Form::file('image_5', [
                'id' => 'image_5',
                'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types'))),
            ]) !!}
            <p class="help-block">
                @lang('purchase.max_file_size', ['size' => config('constants.document_size_limit') / 1000000])
                @includeIf('components.document_help_text')
            </p>
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="description_6">@lang('news::lang.description_6'):</label>
            <textarea class="form-control News_description" name="description_6" rows="3"></textarea>
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            {!! Form::label('image_2_7', __('news::lang.image_2_7') . ':') !!}
            {!! Form::file('image_2_7', [
                'id' => 'image_2_7',
                'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types'))),
            ]) !!}
            <p class="help-block">
                @lang('purchase.max_file_size', ['size' => config('constants.document_size_limit') / 1000000])
                @includeIf('components.document_help_text')
            </p>
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