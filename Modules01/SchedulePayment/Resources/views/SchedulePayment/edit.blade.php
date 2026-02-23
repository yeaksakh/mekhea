<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('schedulepayment::lang.edit_schedulepayment')</h4>
        </div>
        <div class="modal-body">
            <form id="edit_SchedulePayment_form" method="POST" action="{{ route('SchedulePayment.update', $schedulepayment->id) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="schedulepayment_category_id">@lang('schedulepayment::lang.category'):</label>
                            <select class="form-control" id="schedulepayment_category_id" name="schedulepayment_category_id" style="width: 100%;">
                                <option value="">@lang('messages.select')</option>
                                @foreach ($schedulepayment_categories as $id => $category)
                                    <option value="{{ $id }}" {{ $schedulepayment->category_id == $id ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    
                    
                    
                    
                    
                    
                    
                        <div class="col-md-12">
        <div class="form-group">
            <label for="title_1">@lang('schedulepayment::lang.title_1'):</label>
            <input type="text" class="form-control" id="title_1" name="title_1" value="{{ $schedulepayment->{'title_1'} }}">
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="date_paid_5">@lang('schedulepayment::lang.date_paid_5'):</label>
            <input type="date" class="form-control" id="date_paid_5" name="date_paid_5" value="{{ $schedulepayment->{'date_paid_5'} }}">
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="date_prepare_pay_6">@lang('schedulepayment::lang.date_prepare_pay_6'):</label>
            <input type="date" class="form-control" id="date_prepare_pay_6" name="date_prepare_pay_6" value="{{ $schedulepayment->{'date_prepare_pay_6'} }}">
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="status_7">@lang('schedulepayment::lang.status_7'):</label>
            <select class="form-control" id="status_7" name="status_7" style="width: 100%;">
                <option value="0" {{ $schedulepayment->status_7 == 0 ? "selected" : "" }}>@lang('schedulepayment::lang.no') </option>
                <option value="1" {{ $schedulepayment->status_7 == 1 ? "selected" : "" }}>@lang('schedulepayment::lang.yes')</option>
            </select>
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="note_8">@lang('schedulepayment::lang.note_8'):</label>
            <!-- <input type="text" class="form-control" id="note_8" name="note_8" value="{{ $schedulepayment->{'note_8'} }}"> -->
            <textarea class="form-control SchedulePayment_description" rows="7" name="note_8" value="{{ $schedulepayment->{'note_8'} }}">{!! $schedulepayment->{'note_8'} !!}</textarea>

            <!-- <textarea class="form-control summernote" rows="7" name="note_8" value="{{ $schedulepayment->{'note_8'} }}">{!! $schedulepayment->{'note_8'} !!}</textarea> -->
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
