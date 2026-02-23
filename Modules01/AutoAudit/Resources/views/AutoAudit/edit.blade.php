<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('autoaudit::lang.edit_autoaudit')</h4>
        </div>
        <div class="modal-body">
            <form id="edit_AutoAudit_form" method="POST" action="{{ route('AutoAudit.update', $autoaudit->id) }}">
                @csrf
                @method('PUT')
                <div class="row">
                

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="autoaudit_category_id">@lang('autoaudit::lang.category'):</label>
                            <select class="form-control" id="autoaudit_category_id" name="autoaudit_category_id" style="width: 100%;">
                                <option value="">@lang('messages.select')</option>
                                @foreach ($autoaudit_categories as $id => $category)
                                    <option value="{{ $id }}" {{ $autoaudit->category_id == $id ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    
                    
                    
                    
                    
                    
                    
                    
                        <div class="col-md-12">
        <div class="form-group">
            <label for="transaction_id">@lang('autoaudit::lang.transaction_id'):</label>
            <input type="number" class="form-control" id="transaction_id" name="transaction_id" value="{{ $autoaudit->{'transaction_id'} }}" step="any">
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="audit_status_2">@lang('autoaudit::lang.audit_status_2'):</label>
            <input type="text" class="form-control" id="audit_status_2" name="audit_status_2" value="{{ $autoaudit->{'audit_status_2'} }}">
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
<link href="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.js"></script>
<style>
    .note-editable {
    font-family: 'KhmerOSBassac', 'Battambang', 'Siemreap', 'Moul', Arial, sans-serif;
    }
</style>
<script>
$(document).ready(function() {
$('.summernote').summernote({
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
