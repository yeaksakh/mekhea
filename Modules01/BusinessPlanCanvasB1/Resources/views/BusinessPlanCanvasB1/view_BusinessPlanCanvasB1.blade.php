<div class="modal-dialog modal-xl" id="yourModalId" role="document">
    <div class="modal-content">
        <div class="modal-body" id="printableArea">
        <!-- Modal Header -->
        <div class="modal-header">
            <h3 class="modal-title text-left" style="color: #024cd7;" id="modalTitle">{{$businessplancanvasb1->title}}</h3>
            
            
            <button type="button" class="close btn-dan" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        

        <!-- Modal Body -->
        <style>
            /* General Styles for Grid Layout */
            .grid-container {
                display: grid;
                grid-template-columns: repeat(5, 1fr);
                grid-template-areas:
                    "problem solution uvp unfair customer"
                    "problem metrics uvp channels customer"
                    "cost cost revenue revenue revenue";
                gap: 20px;
            }

            /* Individual Grid Items */
            .grid-item {
                padding: 5px;
                border: 1px solid #ccc;
                border-radius: 8px;
                background-color: #f9f9f9;
            }

            .section-header {
                
                font-weight: bold;
                color: #024cd7;
                margin-bottom: 10px;
            }

            .section-item {
                font-size: 1rem;
                color: #333;
            }

            /* Grid Item Areas */
            .problem { grid-area: problem; }
            .solution { grid-area: solution; }
            .uvp { grid-area: uvp; }
            .unfair { grid-area: unfair; }
            .customer { grid-area: customer; }
            .metrics { grid-area: metrics; }
            .channels { grid-area: channels; }
            .cost { grid-area: cost; }
            .revenue { grid-area: revenue; }

            /* Media Queries for Responsiveness */
            @media (max-width: 1024px) {
                .grid-container {
                    grid-template-columns: repeat(3, 1fr);
                    grid-template-areas:
                        "problem solution uvp"
                        "unfair customer metrics"
                        "channels cost revenue";
                }
            }

            @media (max-width: 768px) {
                .grid-container {
                    grid-template-columns: repeat(2, 1fr);
                    grid-template-areas:
                        "problem solution"
                        "uvp unfair"
                        "customer metrics"
                        "channels cost"
                        "revenue revenue";
                }
            }

            @media (max-width: 480px) {
                .grid-container {
                    grid-template-columns: 1fr;
                    grid-template-areas:
                        "problem"
                        "solution"
                        "uvp"
                        "unfair"
                        "customer"
                        "metrics"
                        "channels"
                        "cost"
                        "revenue";
                }
            }

            /* Modal Styles */
            .modal-dialog {
                max-width: 100%;
                margin: 30px auto;
            }

            .modal-content {
                border-radius: 10px;
                transition: transform 0.3s ease-in-out;
            }

            .modal-content:hover {
                transform: scale(1.02);
            }

            /* Print Styling */
            @media print {
                .modal-header, .modal-footer, .btn-dan {
                    display: none;
                }

                .grid-container {
                    grid-template-columns: repeat(5, 1fr);
                    gap: 5px;
                }

                .grid-item {
                    padding: 10px;
                    border: 1px solid #000;
                    background-color: transparent;
                    color: #000;
                }

                .section-header {
                    font-size: 1rem;
                    
                    color: #000;
                }

                .modal-content {
                    border: none;
                    box-shadow: none;
                }
            }

            /* Button Styles */
            .btn-dan {
                color: #024cd7;
                font-size: 1.5rem;
            }

            .btn-primary {
                background-color: #024cd7;
                border-color: #024cd7;
            }
        </style>

        <div class="grid-container">
            <!-- First Row: Problem, Solution, Unique Value Proposition, Unfair Advantage -->
            <div class="grid-item problem">
                
                <p class="section-header"><i class="fas fa-users"></i> 8.@lang('businessplancanvasb1::lang.key_partners')</p>
                <p class="section-item"style="white-space: pre-line;">{{$businessplancanvasb1->KeyPartner_8}}</p>
            </div>
            <div class="grid-item solution">
    <p class="section-header">
        <i class="fas fa-cogs" style="margin-right: 8px;"></i> 7.@lang('businessplancanvasb1::lang.key_activities')
    </p>
    <p class="section-item" style="white-space: pre-line;">{{$businessplancanvasb1->KeyActivities_7}}</p>
</div>

<div class="grid-item uvp">
    <p class="section-header">
        <i class="fas fa-gem" style="margin-right: 8px;"></i> 2.@lang('businessplancanvasb1::lang.value_propositions')
    </p>
    <p class="section-item" style="white-space: pre-line;">{{$businessplancanvasb1->ValuePropositions_2}}</p>
</div>

<div class="grid-item unfair">
    <p class="section-header">
        <i class="fas fa-handshake" style="margin-right: 8px;"></i> 4.@lang('businessplancanvasb1::lang.customer_relationaship')
    </p>
    <p class="section-item" style="white-space: pre-line;">{{$businessplancanvasb1->CustomerRelationships_4}}</p>
</div>

<div class="grid-item customer">
    <p class="section-header">
        <i class="fas fa-users" style="margin-right: 8px;"></i> 1.@lang('businessplancanvasb1::lang.customer_segments')
    </p>
    <p class="section-item" style="white-space: pre-line;">{{$businessplancanvasb1->CustomerSegments_1}}</p>
</div>

<div class="grid-item metrics">
    <p class="section-header">
        <i class="fas fa-trophy" style="margin-right: 8px;"></i> 6.@lang('businessplancanvasb1::lang.key_resources')
    </p>
    <p class="section-item" style="white-space: pre-line;">{{$businessplancanvasb1->KeyResources_6}}</p>
</div>

<div class="grid-item channels">
    <p class="section-header">
        <i class="fas fa-share-alt" style="margin-right: 8px;"></i> 3.@lang('businessplancanvasb1::lang.channels')
    </p>
    <p class="section-item" style="white-space: pre-line;">{{ $businessplancanvasb1->Channels_3 }}</p>
</div>

<div class="grid-item cost">
    <p class="section-header">
        <i class="fas fa-credit-card" style="margin-right: 8px;"></i> 9.@lang('businessplancanvasb1::lang.cost_structure')
    </p>
    <p class="section-item" style="white-space: pre-line;">{{$businessplancanvasb1->CostStructure_9}}</p>
</div>

<!-- Fourth Row: Revenue Streams -->
<div class="grid-item revenue">
    <p class="section-header">
        <i class="fas fa-dollar-sign" style="margin-right: 8px;"></i> 5.@lang('businessplancanvasb1::lang.reveneu_stream')
    </p>
    <p class="section-item" style="white-space: pre-line;">{{$businessplancanvasb1->ReveneuStreams_5}}</p>
</div>

        </div>
        </div>

        <!-- Modal Footer -->
        <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="printDiv('printableArea')">Print</button>
        </div>
    </div>
</div>

<script>
    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var modalTitle = document.getElementById('modalTitle').innerHTML; // Get the modal title
        var originalContents = document.body.innerHTML;

        // Include the title in the printable area
        var fullPrintContents = '<h1 class="modal-title text-center">' + modalTitle + '</h1>' + printContents;

        // Temporarily replace the body content with printable area
        document.body.innerHTML = fullPrintContents;
        window.print();

        // Restore the original body content after printing
        document.body.innerHTML = originalContents;

        // Close the modal after printing
        $('.modal').modal('hide');

        // Redirect to KPI appraisal list page after printing
        window.location.href = '/businessplancanvasb1/BusinessPlanCanvasB1?businessplancanvasb1_view=list_view'; // Adjust URL as needed
    }
</script>