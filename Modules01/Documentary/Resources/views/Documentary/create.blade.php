<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('documentary::lang.add_Documentary')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                {{-- Main Category --}}
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="main_category">@lang('documentary::lang.main_category'):</label>
                        <select class="form-control select2" id="main_category" name="main_category" style="width: 100%;">
                            <option value="">@lang('messages.select')</option>
                            @foreach ($main_categories as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Subcategory --}}
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="documentary_category_id">@lang('documentary::lang.subcategory'):</label>
                        <select class="form-control select2" id="documentary_category_id" name="documentary_category_id" style="width: 100%;">
                            <option value="">@lang('messages.select')</option>
                        </select>
                    </div>
                </div>

                {{-- Title --}}
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="title_1">@lang('documentary::lang.title_1'):</label>
                        <input type="text" class="form-control" id="title_1">
                    </div>
                </div>

                {{-- URL --}}
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="url_5">@lang('documentary::lang.url_5'):</label>
                        <input type="text" class="form-control" id="url_5">
                    </div>
                </div>

                {{-- Chunked File Upload --}}
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="file_6">@lang('documentary::lang.file_6'):</label>
                        <input type="file" id="file_6" accept="{{ implode(',', array_keys(config('constants.document_upload_mimes_types'))) }}">
                        <progress id="uploadProgress" value="0" max="100" style="width:100%; display:none;"></progress>
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
                <button type="button" class="btn btn-primary" onclick="submitDocumentary()">@lang('messages.save')</button>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript --}}
<script>
$(document).ready(function() {
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

    // Updated click handler
    $('.btn-primary').click(function() {
        const fileInput = document.getElementById('file_6');
        const file = fileInput.files[0];

        if (file) {
            uploadFileAndSave(file);
        } else {
            saveWithoutFile();
        }
    });
});

async function uploadFileAndSave(file) {
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

        await fetch('{{ route("Documentary.store") }}', {
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

    const response = await fetch('{{ route("Documentary.store") }}', {
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

async function saveWithoutFile() {
    const saveButton = document.querySelector('.btn-primary');
    saveButton.disabled = true;

    const finalForm = new FormData();
    finalForm.append('title_1', $('#title_1').val());
    finalForm.append('url_5', $('#url_5').val());
    finalForm.append('documentary_category_id', $('#documentary_category_id').val());

    const response = await fetch('{{ route("Documentary.store") }}', {
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
