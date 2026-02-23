<!-- Telegram Images Modal -->
<div class="modal fade" id="telegramImagesModal" tabindex="-1" role="dialog" aria-labelledby="telegramImagesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="telegramImagesModalLabel">Telegram Group Images</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="telegramImagesContainer" class="row">
                    <!-- Images will be loaded here -->
                </div>
                <div class="text-center mt-3">
                    <button id="loadMoreImages" class="btn btn-secondary d-none">Load More</button>
                    <div id="imagesLoading" class="spinner-border text-primary d-none" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div id="noImagesMessage" class="alert alert-info d-none">
                        No images found in this group.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>