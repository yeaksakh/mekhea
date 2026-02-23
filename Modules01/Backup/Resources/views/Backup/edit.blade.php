<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('backup::lang.edit_backup')</h4>
        </div>
        <div class="modal-body">
            <form id="edit_Backup_form" method="POST" action="{{ route('Backup.update', $backup->id) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="backup_category_id">@lang('backup::lang.category'):</label>
                            <select class="form-control" id="backup_category_id" name="backup_category_id" style="width: 100%;">
                                <option value="">@lang('messages.select')</option>
                                @foreach ($backup_categories as $id => $category)
                                    <option value="{{ $id }}" {{ $backup->category_id == $id ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
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
                    <option value="{{ $id }}" {{ $backup->name_1 == $id ? "selected" : "" }}>{{ $userName }}</option>
                @endforeach
            </select>
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
