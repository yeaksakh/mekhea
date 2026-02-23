<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('businessplancanvasb1::lang.businessplancanvasb1_details')</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="title">@lang('businessplancanvasb1::lang.title'):</label>
                <input type="text" id="title" class="form-control" value="{{ $businessplancanvasb1->title }}" readonly>
            </div>
            <div class="form-group">
                <label for="description">@lang('businessplancanvasb1::lang.description'):</label>
                <textarea id="description" class="form-control" rows="3" readonly>{{ $businessplancanvasb1->description }}</textarea>
            </div>
            <div class="form-group">
                <label for="CustomerSegments_1">@lang('businessplancanvasb1::lang.CustomerSegments_1'):</label>
                <textarea id="CustomerSegments_1" class="form-control" rows="3" readonly>{{ $businessplancanvasb1->CustomerSegments_1 }}</textarea>
            </div>
            <div class="form-group">
                <label for="ValuePropositions_2">@lang('businessplancanvasb1::lang.ValuePropositions_2'):</label>
                <textarea id="ValuePropositions_2" class="form-control" rows="3" readonly>{{ $businessplancanvasb1->ValuePropositions_2 }}</textarea>
            </div>
            <div class="form-group">
                <label for="Channels_3">@lang('businessplancanvasb1::lang.Channels_3'):</label>
                <textarea id="Channels_3" class="form-control" rows="3" readonly>{{ $businessplancanvasb1->Channels_3 }}</textarea>
            </div>
            <div class="form-group">
                <label for="CustomerRelationships_4">@lang('businessplancanvasb1::lang.CustomerRelationships_4'):</label>
                <textarea id="CustomerRelationships_4" class="form-control" rows="3" readonly>{{ $businessplancanvasb1->CustomerRelationships_4 }}</textarea>
            </div>
            <div class="form-group">
                <label for="ReveneuStreams_5">@lang('businessplancanvasb1::lang.ReveneuStreams_5'):</label>
                <textarea id="ReveneuStreams_5" class="form-control" rows="3" readonly>{{ $businessplancanvasb1->ReveneuStreams_5 }}</textarea>
            </div>
            <div class="form-group">
                <label for="KeyResources_6">@lang('businessplancanvasb1::lang.KeyResources_6'):</label>
                <textarea id="KeyResources_6" class="form-control" rows="3" readonly>{{ $businessplancanvasb1->KeyResources_6 }}</textarea>
            </div>
            <div class="form-group">
                <label for="KeyActivities_7">@lang('businessplancanvasb1::lang.KeyActivities_7'):</label>
                <textarea id="KeyActivities_7" class="form-control" rows="3" readonly>{{ $businessplancanvasb1->KeyActivities_7 }}</textarea>
            </div>
            <div class="form-group">
                <label for="KeyPartner_8">@lang('businessplancanvasb1::lang.KeyPartner_8'):</label>
                <textarea id="KeyPartner_8" class="form-control" rows="3" readonly>{{ $businessplancanvasb1->KeyPartner_8 }}</textarea>
            </div>
            <div class="form-group">
                <label for="CostStructure_9">@lang('businessplancanvasb1::lang.CostStructure_9'):</label>
                <textarea id="CostStructure_9" class="form-control" rows="3" readonly>{{ $businessplancanvasb1->CostStructure_9 }}</textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
        </div>
    </div>
</div>