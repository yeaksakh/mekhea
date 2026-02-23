<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('businessplancanvasb1::lang.edit_businessplancanvasb1')</h4>
        </div>
        <div class="modal-body">
            <form id="edit_BusinessPlanCanvasB1_form" method="POST" action="{{ route('BusinessPlanCanvasB1.update', $businessplancanvasb1->id) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="title">@lang('businessplancanvasb1::lang.title'):</label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ $businessplancanvasb1->title }}" required>
                        </div>
                    </div>
                    
                   
                    
                    
        
    <div class="col-md-6">
    <div class="form-group">
        <label for="CustomerSegments_1">@lang('businessplancanvasb1::lang.CustomerSegments_1'):</label>
        <textarea rows="10" class="form-control" id="CustomerSegments_1" name="CustomerSegments_1" >{{ $businessplancanvasb1->CustomerSegments_1 }}</textarea>
    </div>
</div>

    
                   
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ValuePropositions_2">@lang('businessplancanvasb1::lang.ValuePropositions_2'):</label>
                            <textarea rows="10" class="form-control" id="ValuePropositions_2" name="ValuePropositions_2"> {{ $businessplancanvasb1->ValuePropositions_2 }}</textarea>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Channels_3">@lang('businessplancanvasb1::lang.Channels_3'):</label>
                            <textarea rows="10" class="form-control" id="Channels_3" name="Channels_3" >{{ $businessplancanvasb1->Channels_3 }}</textarea>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="CustomerRelationships_4">@lang('businessplancanvasb1::lang.CustomerRelationships_4'):</label>
                            <textarea rows="10" class="form-control" id="CustomerRelationships_4" name="CustomerRelationships_4"> {{ $businessplancanvasb1->CustomerRelationships_4 }}</textarea>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ReveneuStreams_5">@lang('businessplancanvasb1::lang.ReveneuStreams_5'):</label>
                            <textarea rows="10" class="form-control" id="ReveneuStreams_5" name="ReveneuStreams_5" >{{ $businessplancanvasb1->ReveneuStreams_5 }}</textarea>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="KeyResources_6">@lang('businessplancanvasb1::lang.KeyResources_6'):</label>
                            <textarea rows="10" class="form-control" id="KeyResources_6" name="KeyResources_6"> {{ $businessplancanvasb1->KeyResources_6 }}</textarea>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="KeyActivities_7">@lang('businessplancanvasb1::lang.KeyActivities_7'):</label>
                            <textarea rows="10" class="form-control" id="KeyActivities_7" name="KeyActivities_7"> {{ $businessplancanvasb1->KeyActivities_7 }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
    <div class="form-group">
        <label for="KeyPartner_8">@lang('businessplancanvasb1::lang.KeyPartner_8'):</label>
        <textarea rows="10" class="form-control" id="KeyPartner_8" name="KeyPartner_8">{{ $businessplancanvasb1->KeyPartner_8 }}</textarea>
    </div>
</div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="CostStructure_9">@lang('businessplancanvasb1::lang.CostStructure_9'):</label>
                            <textarea rows="10" class="form-control" id="CostStructure_9" name="CostStructure_9"> {{ $businessplancanvasb1->CostStructure_9 }}</textarea>

                        </div>
                    </div>
                    
                    <!-- Tips -->
<div class="col-md-6">
    <div class="form-group">
        <label for="CostStructure_9" style="color: red;">
    <i class="fas fa-info-circle"></i>  <!-- Info Icon -->
    ការធ្វើផែនការអាជីវកម្ម 1 សន្លឹក
</label>
        <p class=" form-control-lg" style="white-space: pre-line;">
        
        @lang('businessplancanvasb1::lang.tips')
        
       
        
        
        </p>
    </div>
</div>


                </div>
                <hr>
                <div class="form-group text-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
                    <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
                </div>
            </form>
        </div>
    </div>
</div>