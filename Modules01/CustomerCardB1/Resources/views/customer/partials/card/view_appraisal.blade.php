<div class="modal-dialog modal-lg" id="yourModalId" role="document">
    <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
            <h4 class="modal-title text-center" style="color: #024cd7;">@lang('visa::lang.view_appraisal')</h4>
            <button type="button" class="close btn-dan" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body flex justify-center items-center" id="printableArea">
            @include('customercardb1::customer.partials.card.visa')
        </div>

        <!-- Modal Footer -->
        <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="printDiv('printableArea')">Print</button>
        </div>
    </div>
</div>

<!-- JavaScript Print Function -->
<script>
    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        $('.modal').modal('hide');

        // Redirect to visa appraisal list page after printing
        window.location.href = '/visa/appraisal-list'; // Update this URL based on your routing
    }
</script>
