<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action([\Modules\ExpenseRequest\Http\Controllers\ExpenseRequestController::class, 'store']), 'method' => 'post', 'id' => 'add_ExpenseRequest_form' ]) !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('expenserequest::lang.add_ExpenseRequest')</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="expenserequest_category_id">@lang('expenserequest::lang.category'):</label>
                            <select class="form-control select2" id="expenserequest_category_id" name="expenserequest_category_id" style="width: 100%;">
                                <option value="">@lang('messages.select')</option>
                                @foreach ($expenserequest_categories as $id => $category)
                                    <option value="{{ $id }}">{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    
                    
                    
                    
                    
                    
                    
                    
                        <div class="col-md-12">
        <div class="form-group">
            <label for="amount_1">@lang('expenserequest::lang.amount_1'):</label>
            <input type="number" class="form-control" id="amount_1" name="amount_1" step="any">
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="expense_for_2">@lang('expenserequest::lang.expense_for_2'):</label>
            <input type="text" class="form-control" id="expense_for_2" name="expense_for_2">
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="who_request_expense_3">@lang('expenserequest::lang.who_request_expense_3'):</label>
            <select class="form-control select2" id="who_request_expense_3" name="who_request_expense_3" style="width: 100%;">
                <option value="">@lang('messages.select')</option>
                @foreach ($users as $id => $userName)
                    <option value="{{ $id }}">{{ $userName }}</option>
                @endforeach
            </select>
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