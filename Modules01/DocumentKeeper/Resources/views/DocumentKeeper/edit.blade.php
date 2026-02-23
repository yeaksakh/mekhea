<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('documentkeeper::lang.edit_documentkeeper')</h4>
        </div>
        <div class="modal-body">
            <form id="edit_DocumentKeeper_form" method="POST" action="{{ route('DocumentKeeper.update', $documentkeeper->id) }}">
                @csrf
                @method('PUT')
                <div class="row">
                

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="documentkeeper_category_id">@lang('documentkeeper::lang.category'):</label>
                            <select class="form-control" id="documentkeeper_category_id" name="documentkeeper_category_id" style="width: 100%;">
                                <option value="">@lang('messages.select')</option>
                                @foreach ($documentkeeper_categories as $id => $category)
                                    <option value="{{ $id }}" {{ $documentkeeper->category_id == $id ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    
                    
                    
                    
                    
                    
                    
                    
                        <div class="col-md-12">
        <div class="form-group">
            <label for="title_1">@lang('documentkeeper::lang.title_1'):</label>
            <input type="text" class="form-control" id="title_1" name="title_1" value="{{ $documentkeeper->{'title_1'} }}">
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            {!! Form::label('file_2', __('documentkeeper::lang.file_2') . ':') !!}
            {!! Form::file('file_2', [
                'id' => 'file_2',
                'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types'))),
            ]) !!}
            @if($documentkeeper->{'file_2'})
                @php
                    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                    $fileExtension = strtolower(pathinfo($documentkeeper->{'file_2'}, PATHINFO_EXTENSION));
                    $filePath = 'uploads/DocumentKeeper/' . basename($documentkeeper->{'file_2'});
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
<link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Moul&family=Siemreap&display=swap" rel="stylesheet">

