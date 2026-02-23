<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('news::lang.edit_news')</h4>
        </div>
        <div class="modal-body">
            <form id="edit_News_form" method="POST" action="{{ route('News.update', $news->id) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="news_category_id">@lang('news::lang.category'):</label>
                            <select class="form-control" id="news_category_id" name="news_category_id" style="width: 100%;">
                                <option value="">@lang('messages.select')</option>
                                @foreach ($news_categories as $id => $category)
                                    <option value="{{ $id }}" {{ $news->category_id == $id ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    
                    
                    
                    
                    
                    
                    
                        <div class="col-md-12">
        <div class="form-group">
            <label for="title_1">@lang('news::lang.title_1'):</label>
            <input type="text" class="form-control" id="title_1" name="title_1" value="{{ $news->{'title_1'} }}">
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            {!! Form::label('image_5', __('news::lang.image_5') . ':') !!}
            {!! Form::file('image_5', [
                'id' => 'image_5',
                'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types'))),
            ]) !!}
            @if($news->{'image_5'})
                @php
                    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                    $fileExtension = strtolower(pathinfo($news->{'image_5'}, PATHINFO_EXTENSION));
                    $filePath = 'uploads/News/' . basename($news->{'image_5'});
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
    <div class="col-md-12">
        <div class="form-group">
            <label for="description_6">@lang('news::lang.description_6'):</label>
            <!-- <input type="text" class="form-control" id="description_6" name="description_6" value="{{ $news->{'description_6'} }}"> -->
            <textarea class="form-control News_description" rows="7" name="description_6" value="{{ $news->{'description_6'} }}">{!! $news->{'description_6'} !!}</textarea>

            <!-- <textarea class="form-control summernote" rows="7" name="description_6" value="{{ $news->{'description_6'} }}">{!! $news->{'description_6'} !!}</textarea> -->
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            {!! Form::label('image_2_7', __('news::lang.image_2_7') . ':') !!}
            {!! Form::file('image_2_7', [
                'id' => 'image_2_7',
                'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types'))),
            ]) !!}
            @if($news->{'image_2_7'})
                @php
                    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                    $fileExtension = strtolower(pathinfo($news->{'image_2_7'}, PATHINFO_EXTENSION));
                    $filePath = 'uploads/News/' . basename($news->{'image_2_7'});
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
