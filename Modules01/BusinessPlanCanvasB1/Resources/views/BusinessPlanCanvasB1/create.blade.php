<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('businessplancanvasb1::lang.add_BusinessPlanCanvasB1')</h4>
        </div>
        <div class="modal-body">
            <form id="add_BusinessPlanCanvasB1_form" method="POST" action="{{ route('BusinessPlanCanvasB1.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-row">

                    <!-- Title Field -->
                    <div class="form-group col-md-6 mb-4">
                        <label for="title">@lang('businessplancanvasb1::lang.title')</label>
                        <input type="text" class="form-control form-control-lg" id="title" name="title" placeholder="@lang('businessplancanvasb1::lang.placeholder_name')"​ required>
                    </div>
                    
                    

                    <!-- Customer Segments -->
                    <div class="col-md-6">
                        <div class="form-group">
                        <label for="CustomerSegments_1">@lang('businessplancanvasb1::lang.CustomerSegments_1')</label>
                        <textarea rows="10" class="form-control form-control-lg" id="CustomerSegments_1" name="CustomerSegments_1" placeholder="@lang('businessplancanvasb1::lang.placeholder_1')"></textarea>
                    </div>
                    
                    
                                       
                </div>


                    <!-- Value Propositions -->
                    <div class="col-md-6">
                        <div class="form-group">
                        <label for="ValuePropositions_2">@lang('businessplancanvasb1::lang.ValuePropositions_2')</label>
                        <textarea rows="10" class="form-control form-control-lg" id="ValuePropositions_2" name="ValuePropositions_2" placeholder="@lang('businessplancanvasb1::lang.placeholder_2')
"></textarea>
                    </div>
                    </div>

                    <!-- Channels -->
                    <div class="col-md-6">
                        <div class="form-group">
                        <label for="Channels_3">@lang('businessplancanvasb1::lang.Channels_3')</label>
                        <textarea rows="10" class="form-control form-control-lg" id="Channels_3" name="Channels_3" placeholder="@lang('businessplancanvasb1::lang.placeholder_3')"></textarea>
                    </div>
                    </div>

                    <!-- Customer Relationships -->
                    <div class="col-md-6">
                        <div class="form-group">
                        <label for="CustomerRelationships_4">@lang('businessplancanvasb1::lang.CustomerRelationships_4')</label>
                        <textarea rows="10" class="form-control form-control-lg" id="CustomerRelationships_4" name="CustomerRelationships_4" placeholder="@lang('businessplancanvasb1::lang.placeholder_4')"></textarea>
                    </div>
                    </div>

                    <!-- Revenue Streams -->
                    <div class="col-md-6">
                        <div class="form-group">
                        <label for="ReveneuStreams_5">@lang('businessplancanvasb1::lang.ReveneuStreams_5')</label>
                        <textarea rows="10" class="form-control form-control-lg" id="ReveneuStreams_5" name="ReveneuStreams_5" placeholder="@lang('businessplancanvasb1::lang.placeholder_5')"></textarea>
                    </div>
                    </div>

                    <!-- Key Resources -->
                    <div class="col-md-6">
                        <div class="form-group">
                        <label for="KeyResources_6">@lang('businessplancanvasb1::lang.KeyResources_6')</label>
                        <textarea rows="10" class="form-control form-control-lg" id="KeyResources_6" name="KeyResources_6" placeholder="@lang('businessplancanvasb1::lang.placeholder_6')"></textarea>
                    </div>
                    </div>

                    <!-- Key Activities -->
                    <div class="col-md-6">
                        <div class="form-group">
                        <label for="KeyActivities_7">@lang('businessplancanvasb1::lang.KeyActivities_7')</label>
                        <textarea rows="10" class="form-control form-control-lg" id="KeyActivities_7" name="KeyActivities_7" placeholder="@lang('businessplancanvasb1::lang.placeholder_7')"></textarea>
                    </div>
                    </div>

                    <!-- Key Partners -->
                    <div class="col-md-6">
                        <div class="form-group">
                        <label for="KeyPartner_8">@lang('businessplancanvasb1::lang.KeyPartner_8')</label>
                        <textarea rows="10" class="form-control form-control-lg" id="KeyPartner_8" name="KeyPartner_8" placeholder="@lang('businessplancanvasb1::lang.placeholder_8')"></textarea>
                    </div>
                    </div>

                    <!-- Cost Structure -->
                    <div class="col-md-6">
                        <div class="form-group">
                        <label for="CostStructure_9">@lang('businessplancanvasb1::lang.CostStructure_9')</label>
                        <textarea rows="10" class="form-control form-control-lg" id="CostStructure_9" name="CostStructure_9" placeholder="@lang('businessplancanvasb1::lang.placeholder_9')"></textarea>
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

                <!-- Buttons -->
                <div class="form-group text-right">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('messages.close')</button>
                    <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Modal styling */
    .modal-content {
        padding: 25px;
        background-color: #fff;
        border-radius: 10px;
        border: 1px solid #ddd;
    }

    .modal-header {
        background-color: #f7f7f7;
        padding: 15px 20px;
        border-bottom: 1px solid #ddd;
    }

    .modal-header h4 {
        font-size: 20px;
        color: #333;
    }

    .modal-body {
        padding: 25px;
    }

    .form-row {
        display: flex;
        flex-wrap: wrap;
    }

    .form-group {
        margin-bottom: 20px;
        width: 100%;
    }

    .form-group label {
        font-size: 16px;
        font-weight: bold;
        color: #444;
    }

    .form-control {
        font-size: 16px;
        border-radius: 8px;
        padding: 12px;
    }

    textarea.form-control {
        resize: vertical;
    }

    textarea.form-control-lg {
        padding: 15px;
        font-size: 16px;
        border-radius: 8px;
    }

    .select2 {
        width: 100% !important;
    }

    button.btn {
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 5px;
        margin-left: 10px;
    }

    /* Buttons styles */
    button.btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    button.btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }

    button:focus {
        outline: none;
    }

    /* Modal bottom space */
    .modal-footer {
        padding: 15px;
        background-color: #f7f7f7;
    }
</style>