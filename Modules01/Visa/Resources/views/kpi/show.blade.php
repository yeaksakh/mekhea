<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('ddb11::lang.ddb11_details')</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="title">@lang('ddb11::lang.title'):</label>
                <input type="text" id="title" class="form-control" value="{{ $ddb11->title }}" readonly>
            </div>
            <div class="form-group">
                <label for="description">@lang('ddb11::lang.description'):</label>
                <textarea id="description" class="form-control" rows="3" readonly>{{ $ddb11->description }}</textarea>
            </div>
            
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
        </div>
    </div>
</div>
