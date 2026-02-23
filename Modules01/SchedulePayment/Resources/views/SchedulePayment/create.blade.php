<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action([\Modules\SchedulePayment\Http\Controllers\SchedulePaymentController::class, 'store']), 'method' => 'post', 'id' => 'add_SchedulePayment_form' ]) !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('schedulepayment::lang.add_SchedulePayment')</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="schedulepayment_category_id">@lang('schedulepayment::lang.category'):</label>
                            <select class="form-control select2" id="schedulepayment_category_id" name="schedulepayment_category_id" style="width: 100%;">
                                <option value="">@lang('messages.select')</option>
                                @foreach ($schedulepayment_categories as $id => $category)
                                    <option value="{{ $id }}">{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    
                    
                    
                    
                    
                    
                    
                    
                        <div class="col-md-12">
        <div class="form-group">
            <label for="title_1">@lang('schedulepayment::lang.title_1'):</label>
            <input type="text" class="form-control" id="title_1" name="title_1">
        </div>
    </div>
    
    <div class="col-sm-12 individual">
        <div class="form-group">
            {!! Form::label('date_paid_5', __('schedulepayment::lang.date_paid_5') . ':') !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </span>
                {!! Form::date('date_paid_5', Carbon::now()->format('Y-m-d'), ['class' => 'form-control date_paid_5-date-picker','placeholder' => Carbon::now()->format('Y-m-d')]); !!}
            </div>
        </div>
    </div>

    
    <div class="col-sm-12 individual">
        <div class="form-group">
            {!! Form::label('date_prepare_pay_6', __('schedulepayment::lang.date_prepare_pay_6') . ':') !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </span>
                {!! Form::date('date_prepare_pay_6', Carbon::now()->format('Y-m-d'), ['class' => 'form-control date_prepare_pay_6-date-picker','placeholder' => Carbon::now()->format('Y-m-d')]); !!}
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label for="status_7">@lang('schedulepayment::lang.status_7'):</label>
            <select class="form-control" id="status_7" name="status_7" style="width: 100%;">
                <option value="">@lang('messages.select')</option>
                <option value="0">@lang('schedulepayment::lang.no')</option>
                <option value="1">@lang('schedulepayment::lang.yes')</option>
            </select>
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="note_8">@lang('schedulepayment::lang.note_8'):</label>
            <textarea class="form-control SchedulePayment_description" name="note_8" rows="3"></textarea>
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