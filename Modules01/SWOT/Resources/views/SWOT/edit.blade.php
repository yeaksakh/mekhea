<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('swot::lang.edit_swot')</h4>
        </div>
        <div class="modal-body">
            <form id="edit_SWOT_form" method="POST" action="{{ route('SWOT.update', $swot->id) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="swot_category_id">@lang('swot::lang.category'):</label>
                            <select class="form-control" id="swot_category_id" name="swot_category_id" style="width: 100%;">
                                <option value="">@lang('messages.select')</option>
                                @foreach ($swot_categories as $id => $category)
                                    <option value="{{ $id }}" {{ $swot->category_id == $id ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    
                    
                    
                    
                    
                    
                    
                        <div class="col-md-12">
        <div class="form-group">
            <label for="Title_1">@lang('swot::lang.Title_1'):</label>
            <input type="text" class="form-control" id="Title_1" name="Title_1" value="{{ $swot->{'Title_1'} }}">
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="Strengths_5">@lang('swot::lang.Strengths_5'):</label>
            <!-- <input type="text" class="form-control" id="Strengths_5" name="Strengths_5" value="{{ $swot->{'Strengths_5'} }}"> -->
            <textarea class="form-control SWOT_description" rows="7" name="Strengths_5" value="{{ $swot->{'Strengths_5'} }}">{!! $swot->{'Strengths_5'} !!}</textarea>

            <!-- <textarea class="form-control summernote" rows="7" name="Strengths_5" value="{{ $swot->{'Strengths_5'} }}">{!! $swot->{'Strengths_5'} !!}</textarea> -->
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="Weaknesses_6">@lang('swot::lang.Weaknesses_6'):</label>
            <!-- <input type="text" class="form-control" id="Weaknesses_6" name="Weaknesses_6" value="{{ $swot->{'Weaknesses_6'} }}"> -->
            <textarea class="form-control SWOT_description" rows="7" name="Weaknesses_6" value="{{ $swot->{'Weaknesses_6'} }}">{!! $swot->{'Weaknesses_6'} !!}</textarea>

            <!-- <textarea class="form-control summernote" rows="7" name="Weaknesses_6" value="{{ $swot->{'Weaknesses_6'} }}">{!! $swot->{'Weaknesses_6'} !!}</textarea> -->
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="Opportunities_7">@lang('swot::lang.Opportunities_7'):</label>
            <!-- <input type="text" class="form-control" id="Opportunities_7" name="Opportunities_7" value="{{ $swot->{'Opportunities_7'} }}"> -->
            <textarea class="form-control SWOT_description" rows="7" name="Opportunities_7" value="{{ $swot->{'Opportunities_7'} }}">{!! $swot->{'Opportunities_7'} !!}</textarea>

            <!-- <textarea class="form-control summernote" rows="7" name="Opportunities_7" value="{{ $swot->{'Opportunities_7'} }}">{!! $swot->{'Opportunities_7'} !!}</textarea> -->
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="Threats_8">@lang('swot::lang.Threats_8'):</label>
            <!-- <input type="text" class="form-control" id="Threats_8" name="Threats_8" value="{{ $swot->{'Threats_8'} }}"> -->
            <textarea class="form-control SWOT_description" rows="7" name="Threats_8" value="{{ $swot->{'Threats_8'} }}">{!! $swot->{'Threats_8'} !!}</textarea>

            <!-- <textarea class="form-control summernote" rows="7" name="Threats_8" value="{{ $swot->{'Threats_8'} }}">{!! $swot->{'Threats_8'} !!}</textarea> -->
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="Note_9">@lang('swot::lang.Note_9'):</label>
            <!-- <input type="text" class="form-control" id="Note_9" name="Note_9" value="{{ $swot->{'Note_9'} }}"> -->
            <textarea class="form-control SWOT_description" rows="7" name="Note_9" value="{{ $swot->{'Note_9'} }}">{!! $swot->{'Note_9'} !!}</textarea>

            <!-- <textarea class="form-control summernote" rows="7" name="Note_9" value="{{ $swot->{'Note_9'} }}">{!! $swot->{'Note_9'} !!}</textarea> -->
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
