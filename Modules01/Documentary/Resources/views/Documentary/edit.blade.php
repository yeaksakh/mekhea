<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('documentary::lang.edit_documentary')</h4>
        </div>
        <div class="modal-body">
            <form id="edit_Documentary_form" method="POST" action="{{ route('Documentary.update', $documentary->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    {{-- Main Category --}}
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="main_category">@lang('documentary::lang.main_category'):</label>
                            <select class="form-control" id="main_category" name="main_category" style="width: 100%;">
                                <option value="">@lang('messages.select')</option>
                                @foreach ($main_categories as $id => $name)
                                    <option value="{{ $id }}" {{ $selected_main_category_id == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Subcategory --}}
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="documentary_category_id">@lang('documentary::lang.subcategory'):</label>
                            <select class="form-control" id="documentary_category_id" name="documentary_category_id" style="width: 100%;">
                                <option value="">@lang('messages.select')</option>
                                @foreach ($subcategories as $id => $name)
                                    <option value="{{ $id }}" {{ $documentary->category_id == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Title --}}
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="title_1">@lang('documentary::lang.title_1'):</label>
                            <input type="text" class="form-control" id="title_1" name="title_1" value="{{ $documentary->title_1 }}">
                        </div>
                    </div>

                    {{-- URL --}}
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="url_5">@lang('documentary::lang.url_5'):</label>
                            <input type="text" class="form-control" id="url_5" name="url_5" value="{{ $documentary->url_5 }}">
                        </div>
                    </div>

                    {{-- File Upload --}}
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('file_6', __('documentary::lang.file_6') . ':') !!}
                            {!! Form::file('file_6', [
                                'id' => 'file_6',
                                'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types'))),
                            ]) !!}
                            <progress id="uploadProgress" value="0" max="100" style="width:100%; display:none;"></progress>
                            <input type="hidden" name="remove_file_6" id="remove_file_6" value="0">
                            @if($documentary->file_6)
                                <div id="file_6_preview">
                                    @php
                                        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                                        $fileExtension = strtolower(pathinfo($documentary->file_6, PATHINFO_EXTENSION));
                                        $filePath = 'uploads/Documentary/' . basename($documentary->file_6);
                                    @endphp

                                    <div class="mt-3">
                                        @if(in_array($fileExtension, $imageExtensions))
                                            <img src="{{ asset($filePath) }}" alt="Document Image" class="mt-2" style="max-width: 100px;">
                                        @elseif($fileExtension === 'pdf')
                                            <a href="{{ asset($filePath) }}" target="_blank">
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
                                    <button type="button" class="btn btn-danger btn-xs" id="remove_file_6_button">@lang('messages.remove')</button>
                                </div>
                            @endif

                            <p class="help-block">
                                @lang('purchase.max_file_size', ['size' => config('constants.document_size_limit') / 1000000])
                                @includeIf('components.document_help_text')
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="form-group text-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
                    <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- JavaScript for dynamic subcategory loading --}}
<script>
$(document).ready(function() {
    // Handle subcategory loading
    $('#main_category').change(function() {
        let parentId = $(this).val();
        $('#documentary_category_id').html('<option value="">@lang("messages.select")</option>');

        if (parentId) {
            $.ajax({
                url: '{{ route("Documentary.getSubcategories") }}',
                type: 'GET',
                data: { parent_id: parentId },
                success: function(data) {
                    $.each(data, function(id, name) {
                        $('#documentary_category_id').append('<option value="' + id + '">' + name + '</option>');
                    });
                }
            });
        }
    });

    // Updated save handler
    $('.btn-primary').click(function(e) {
        e.preventDefault();

        const fileInput = document.getElementById('file_6');
        const file = fileInput.files[0];

        if (file) {
            uploadEditFileAndSave(file);
        } else {
            saveEditWithoutFile();
        }
    });

    // Handle file removal
    $('#remove_file_6_button').click(function() {
        $('#file_6').val('');
        $('#file_6_preview').hide();
        $('#remove_file_6').val(1);
    });
});

async function uploadEditFileAndSave(file) {
    const saveButton = document.querySelector('.btn-primary');
    const progressBar = document.getElementById('uploadProgress');
    const chunkSize = 1024 * 1024 * 5; // 5MB
    const totalChunks = Math.ceil(file.size / chunkSize);

    saveButton.disabled = true;
    progressBar.style.display = 'block';

    for (let i = 0; i < totalChunks; i++) {
        const chunk = file.slice(i * chunkSize, (i + 1) * chunkSize);
        const formData = new FormData();
        formData.append('chunk', chunk);
        formData.append('index', i);
        formData.append('filename', file.name);
        formData.append('totalChunks', totalChunks);

        await fetch('{{ route("Documentary.update", $documentary->id) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        });

        progressBar.value = Math.floor(((i + 1) / totalChunks) * 100);
    }

    // Final save after upload
    const finalForm = new FormData();
    finalForm.append('filename', file.name);
    finalForm.append('title_1', $('#title_1').val());
    finalForm.append('url_5', $('#url_5').val());
    finalForm.append('documentary_category_id', $('#documentary_category_id').val());

    const response = await fetch('{{ route("Documentary.update", $documentary->id) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: finalForm
    });

    const result = await response.json();
    saveButton.disabled = false;
    progressBar.style.display = 'none';

    if (result.success) {
        toastr.success(result.msg);
        location.reload();
    } else {
        toastr.error(result.msg);
    }
}

async function saveEditWithoutFile() {
    const saveButton = document.querySelector('.btn-primary');
    saveButton.disabled = true;

    const finalForm = new FormData();
    finalForm.append('title_1', $('#title_1').val());
    finalForm.append('url_5', $('#url_5').val());
    finalForm.append('documentary_category_id', $('#documentary_category_id').val());
    finalForm.append('remove_file_6', $('#remove_file_6').val());

    const response = await fetch('{{ route("Documentary.update", $documentary->id) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: finalForm
    });

    const result = await response.json();
    saveButton.disabled = false;

    if (result.success) {
        toastr.success(result.msg);
        location.reload();
    } else {
        toastr.error(result.msg);
    }
}
</script>
