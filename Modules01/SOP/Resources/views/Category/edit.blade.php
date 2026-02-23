<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">
    <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">@lang( 'messages.edit' )</h4>
    </div>
    <div class="modal-body">
        {!! Form::model($category, ['url' => action([\Modules\SOP\Http\Controllers\SOPController::class, 'updateCategory'], $category->id), 'method' => 'put', 'id' => 'category_edit_form' ]) !!}
        <div class="form-group">
            {!! Form::label('name', __('sop::lang.name') . ':*') !!}
            {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __('sop::lang.category_name')]) !!}
        </div>
        <div class="form-group">
            {!! Form::label('image', __('sop::lang.image') . ':') !!}
            {!! Form::file('image', [
                'id' => 'image',
                'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types'))),
            ]) !!}
            
            @if($category->{'image'})
                @php
                    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                    $fileExtension = strtolower(pathinfo($category->{'image'}, PATHINFO_EXTENSION));
                    $filePath = 'uploads/SOPCategory/' . basename($category->{'image'});
                @endphp

                <div class="mt-3">
                    @if(in_array($fileExtension, $imageExtensions))
                        <img src="{{ asset($filePath) }}" 
                            alt="Document Image" 
                            class="mt-2"
                            style="max-width: 100px;">
                    @elseif($fileExtension === 'pdf')
                        <a href="{{ asset($filePath) }}" 
                        target="_blank" 
                        class="btn btn-link p-0">
                            <i class="fas fa-file-pdf" style="font-size: 30px; color: #dc3545;"></i>
                        </a>
                    @endif
                </div>
            @endif

            <p class="help-block">
                @lang('purchase.max_file_size', ['size' => config('constants.document_size_limit') / 1000000])
                @includeIf('components.document_help_text')
            </p>
        </div>
        <div class="form-group">
            {!! Form::label('description', __('sop::lang.description') . ':') !!}
            {!! Form::textarea('description', null, ['class' => 'form-control','id'=>'summernote', 'placeholder' => __('sop::lang.description'), 'rows' => 3]) !!}
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>
</div>
<link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Moul&family=Siemreap&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.js"></script>
<style>
    .note-editable {
    font-family: 'KhmerOSBassac', 'Battambang', 'Siemreap', 'Moul', Arial, sans-serif;
    }
</style>
<script>
$(document).ready(function() {
$('#summernote').summernote({
        placeholder: 'សូមសរសេរនៅទីនេះ...',
        tabsize: 2,
        height: 300,
        toolbar: [
        ['style', ['style']],
        ['font', ['bold', 'underline', 'clear', 'fontname', 'fontsize']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['table', ['table']],
        ['insert', ['link', 'picture', 'video']],
        ['view', ['fullscreen', 'codeview', 'help']],
        ],
        fontNames: ['KhmerOSBassac', 'Moul', 'Siemreap', 'Battambang', 'Arial', 'Courier New', 'Tahoma'],
        fontNamesIgnoreCheck: ['KhmerOSBassac', 'Moul', 'Siemreap', 'Battambang'],
    });
});
</script>