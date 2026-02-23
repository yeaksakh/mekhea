<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action([\Modules\BotTelegramManager\Http\Controllers\BotTelegramManagerController::class, 'store']), 'method' => 'post', 'id' => 'add_BotTelegramManager_form' ]) !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('bottelegrammanager::lang.add_BotTelegramManager')</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="bottelegrammanager_category_id">@lang('bottelegrammanager::lang.category'):</label>
                            <select class="form-control select2" id="bottelegrammanager_category_id" name="bottelegrammanager_category_id" style="width: 100%;">
                                <option value="">@lang('messages.select')</option>
                                @foreach ($bottelegrammanager_categories as $id => $category)
                                    <option value="{{ $id }}">{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    
                    
                    
                    
                    
                    
                    
                    
                        <div class="col-md-12">
        <div class="form-group">
            {!! Form::label('id_1', __('bottelegrammanager::lang.id_1') . ':') !!}
            {!! Form::file('id_1', [
                'id' => 'id_1',
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