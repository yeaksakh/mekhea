<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action([\Modules\Backup\Http\Controllers\BackupController::class, 'store']), 'method' => 'post', 'id' => 'add_Backup_form' ]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('backup::lang.add_Backup')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="backup_category_id">@lang('backup::lang.category'):</label>
                        <select class="form-control select2" id="backup_category_id" name="backup_category_id" style="width: 100%;">
                            <option value="">@lang('messages.select')</option>
                            @foreach ($backup_categories as $id => $category)
                            <option value="{{ $id }}">{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>









                <div class="col-md-12">
                    <div class="form-group">
                        <label for="name_1">@lang('backup::lang.name_1'):</label>
                        <select class="form-control select2" id="name_1" name="name_1" style="width: 100%;">
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