<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('bottelegrammanager::lang.edit_bottelegrammanager')</h4>
        </div>
        <div class="modal-body">
            <form id="edit_BotTelegramManager_form" method="POST" action="{{ route('BotTelegramManager.update', $bottelegrammanager->id) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="bottelegrammanager_category_id">@lang('bottelegrammanager::lang.category'):</label>
                            <select class="form-control" id="bottelegrammanager_category_id" name="bottelegrammanager_category_id" style="width: 100%;">
                                <option value="">@lang('messages.select')</option>
                                @foreach ($bottelegrammanager_categories as $id => $category)
                                    <option value="{{ $id }}" {{ $bottelegrammanager->category_id == $id ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
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
            @if($bottelegrammanager->{'id_1'})
                @php
                    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                    $fileExtension = strtolower(pathinfo($bottelegrammanager->{'id_1'}, PATHINFO_EXTENSION));
                    $filePath = 'uploads/BotTelegramManager/' . basename($bottelegrammanager->{'id_1'});
                @endphp

                <div class="mt-3">
                    @if(in_array($fileExtension, $imageExtensions))
                        <img src="{{ asset($filePath) }}" 
                            alt="Document Image" 
                            class="mt-2"
                            style="max-width: 100px;">
                    @elseif($fileExtension === 'pdf')
                        <a href="{{ asset($filePath) }}" 
                        target="_blank">
                            <iframe 
                                src="{{ asset($filePath) }}#toolbar=0&navpanes=0&scrollbar=0"
                                width="50%"
                                height="250px"
                                frameborder="0"
                                class="pdf-viewer">
                            </iframe>
                        </a>
                    @endif
                </div>
            @endif

            <p class="help-block">
                @lang('purchase.max_file_size', ['size' => config('constants.document_size_limit') / 1000000])
                @includeIf('components.document_help_text')
            </p>
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
