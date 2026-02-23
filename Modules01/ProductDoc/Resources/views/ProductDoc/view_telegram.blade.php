<div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
        <div class="modal-header bg-primary text-white py-3">
            <h5 class="modal-title font-weight-bold d-flex align-items-center">
                <i class="fab fa-telegram-plane mr-2"></i>
                Foxest’s Products – Media Gallery
            </h5>
            <button type="button" class="close text-white" data-dismiss="modal">
                <span>&times;</span>
            </button>
        </div>

        <div class="modal-body py-4">

            <!-- Search -->
            <div class="mb-4">
                <div class="input-group">
                    <input type="text" id="search-telegram-messages" class="form-control form-control-lg"
                           placeholder="Search by name or date..." onkeyup="searchTelegramMessages()">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="button"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </div>

            <!-- Grid -->
            <div id="messages-list-container">
                <div id="images-grid">
                    @foreach ($messages as $message)
                        @if(!empty($message['media']))
                            @php
                                $msgId  = $message['message_id'];
                                $sender = $message['from'] ?? 'Unknown';
                                $date   = \Carbon\Carbon::parse($message['date'])->format('d/m/Y H:i');
                                $url    = "https://t.me/c/3332101476/{$msgId}";
                                $type   = $message['media']['type'] ?? 'document';
                                $isVideo = in_array($type, ['video', 'video_note']);
                                $isPhoto = $type === 'photo';
                            @endphp

                            <div class="grid-item message-item">
                                <a href="{{ $url }}" target="_blank" class="media-link">
                                    @if($isVideo || $isPhoto)
                                        <div class="thumb">
                                            <img src="{{ $url }}" loading="lazy">
                                        </div>
                                    @else
                                        <div class="thumb doc-thumb">
                                            <!-- no extra content needed -->
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Telegram icon overlay on EVERY media type -->
                                    <div class="tg-overlay">
                                        <i class="fab fa-telegram-plane"></i>
                                    </div>

                                    <div class="info">
                                        <div class="sender">{{ $sender }}</div>
                                        <div class="date">{{ $date }}</div>
                                    </div>
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>

<script>
function searchTelegramMessages() {
    const q = document.getElementById('search-telegram-messages').value.toLowerCase();
    document.querySelectorAll('.message-item').forEach(item => {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(q) ? '' : 'none';
    });
}
</script>

<style>
#images-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 20px;
    padding: 10px;
}
.grid-item { width: 100%; }
.media-link {
    position: relative;
    display: block;
    background: #111;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    transition: transform 0.3s;
    text-decoration: none;
    color: inherit;
}
.media-link:hover {
    transform: translateY(-8px);
}
.thumb {
    position: relative;
    width: 100%;
    height: 280px;
    background: #000;
}
.thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.doc-thumb {
    display: flex;
    align-items: center;
    justify-content: center;
    background: #222;
    height: 280px;
}
.tg-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 70px;
    color: rgba(255,255,255,0.85);
    background: rgba(0,0,0,0.5);
    width: 120px;
    height: 120px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    pointer-events: none;
}
.info {
    padding: 12px;
    color: #fff;
    text-align: center;
}
.sender {
    font-weight: bold;
    font-size: 15px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.date {
    font-size: 13px;
    opacity: 0.7;
    margin-top: 4px;
}
@media (max-width: 768px) {
    #images-grid { 
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); 
        gap: 15px;
    }
    .thumb, .doc-thumb { height: 200px; }
    .tg-overlay { font-size: 50px; width: 90px; height: 90px; }
}
</style>