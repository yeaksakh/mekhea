<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('ddb11::lang.add_DdB11')</h4>
        </div>
        <div class="modal-body">
            <form id="add_DdB11_form" method="POST" action="{{ route('DdB11.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="title">@lang('ddb11::lang.title'):</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ddb11_category_id">@lang('ddb11::lang.category'):</label>
                            <select class="form-control select2" id="ddb11_category_id" name="ddb11_category_id" style="width: 100%;">
                                <option value="">@lang('messages.select')</option>
                                @foreach ($ddb11_categories as $id => $category)
                                <option value="{{ $id }}">{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="description">@lang('ddb11::lang.description'):</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
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