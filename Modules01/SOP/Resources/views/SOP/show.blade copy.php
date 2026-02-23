    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('sop::lang.details')</h4>
                <div class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full btn-modal" style="cursor: pointer;" onclick="toggleHideShow()">
                    @lang('sop::lang.filter')
                </div>
                <div class="hide-show no-print" style="display: none;">
                    @foreach ($checkboxes as $checkbox)
                    <div class="col-sm-2">
                        <div class="form-group">
                            <input
                                type="checkbox"
                                id="hide-show-checkbox-{{ $loop->index }}"
                                onclick="toggleCheckboxContent('{{ $checkbox['id'] }}', this.checked)"
                                checked>
                            <label for="hide-show-checkbox-{{ $loop->index }}" class="form-check-label">
                                 @lang($checkbox['label'])
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <hr style="margin: 0px;" class="no-print">
            <div class="modal-body" id="print-content">
                <label class="form-check-label" id="categorycontent">
                @if($sop->category)
                    @php
                        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                        $fileExtension = strtolower(pathinfo($sop->category->image, PATHINFO_EXTENSION));
                    @endphp
                    
                    @if(in_array($fileExtension, $imageExtensions))
                        <label class="form-check-label pt-2 mb-0" for="categoryCheck">
                            <img src="{{ asset('uploads/SOPCategory/' . basename($sop->category->image)) }}" 
                                alt="Document Image" 
                                style="max-width: 25px; max-height: 25px; vertical-align: middle;">
                            </label>
                    @elseif($fileExtension === 'pdf')
                        <span class="me-2">
                            <a href="{{ asset('uploads/SOPCategory/' . basename($sop->category->image)) }}" 
                            target="_blank" 
                            style="text-decoration: none;">
                                <i class="fas fa-file-pdf" style="font-size: 25px; color: #dc3545;"></i>
                            </a>
                        </span>
                    @endif
                    <span>{{ $sop->category->name }}</span>
                    <div class="form-group" id="category-detail">
                        <label for="categorydetail">@lang('employeecontractb1::lang.description'):</label>
                        <p id="categorydetail" class="form-control-static">{!! $sop->category->description !!}</p>
                    </div>
                    @endif
                </label>
                <!-- Modal Content Goes Here -->
                    <div class="form-group" id="title_1content">
        <label for="title_1">@lang('sop::lang.title_1'):</label>
        <p id="title_1" class="form-control-static">{{ $sop->{'title_1'} }}</p>
    </div>
    <div class="form-group" id="description_5content">
        <label for="description_5">@lang('sop::lang.description_5'):</label>
        <p id="description_5" class="form-control-static summernote">{!! $sop->{'description_5'} !!}</p>
    </div>
                <div class="form-group" id="createdbycontent">
                    <label for="createdby">@lang('silentb11::lang.created_by'):</label>
                    <p id="createdby" class="form-control-static">{{ $name }}</p>
                </div>
                <div class="form-group" id="createdatcontent">
                    <label for="createdat">@lang('silentb11::lang.created_at'):</label>
                    <p id="createdat" class="form-control-static">{{ $sop->created_at }}</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white no-print" 
                    aria-label="Print" 
                    onclick="$('#print-content').printThis();">
                    <i class="fa fa-print"></i> @lang( 'messages.print' )
                </button>
                <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white no-print" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>
        </div>
    </div>
    <script>
    // Handle checkbox-based toggle
    function toggleCheckboxContent(contentId, isChecked) {
        const content = document.getElementById(contentId);

        if (!content) {
            console.warn(`Element with ID "${contentId}" not found.`);
            return;
        }

        content.style.display = isChecked ? 'block' : 'none'; // Show or hide based on checkbox state
    }

    // Handle header click-based toggle
    function toggleHeaderContent(contentId) {
        const content = document.getElementById(contentId);
        if (!content) {
            console.warn(`Element with ID "${contentId}" not found.`);
            return;
        }
        // Toggle between hide and show
        content.style.display = content.style.display === 'none' || !content.style.display ? 'block' : 'none';
    }
    function toggleHideShow() {
        const hideShowSection = document.querySelector('.hide-show');
        if (hideShowSection) {
            // Toggle visibility
            hideShowSection.style.display = hideShowSection.style.display === 'none' ? 'block' : 'none';
        }
    }

    // Optional: Initialize all sections as hidden
    document.addEventListener('DOMContentLoaded', function () {
        const contents = document.querySelectorAll('.form-group-content');

        contents.forEach(content => {
            content.style.display = 'none'; // Initialize all content sections as hidden
        });
    });

</script>