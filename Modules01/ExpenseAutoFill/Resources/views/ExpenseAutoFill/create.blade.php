<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action([\Modules\ExpenseAutoFill\Http\Controllers\ExpenseAutoFillController::class, 'store']), 'method' => 'post', 'id' => 'add_ExpenseAutoFill_form' ]) !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('expenseautofill::lang.add_ExpenseAutoFill')</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="expenseautofill_category_id">@lang('expenseautofill::lang.category'):</label>
                            <select class="form-control select2" id="expenseautofill_category_id" name="expenseautofill_category_id" style="width: 100%;">
                                <option value="">@lang('messages.select')</option>
                                @foreach ($expenseautofill_categories as $id => $category)
                                    <option value="{{ $id }}">{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    
                    
                    
                    
                    
                    
                    
                    
                        <div class="col-md-12">
        <div class="form-group">
            <label for="title_1">@lang('expenseautofill::lang.title_1'):</label>
            <input type="text" class="form-control" id="title_1" name="title_1">
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="topic _5">@lang('expenseautofill::lang.topic _5'):</label>
            <input type="text" class="form-control" id="topic _5" name="topic _5">
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