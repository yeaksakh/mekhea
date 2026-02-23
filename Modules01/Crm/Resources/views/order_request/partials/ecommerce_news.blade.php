<style>
    *::-webkit-scrollbar {
        display: none;
    }
    #news-details-modal {
        position: fixed;
        z-index: 1003; 
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        padding: 0;
    }
    @media (min-width: 1024px) {
        #news-details-modal {
            top: 0;
            right: 0;
            left: auto;
            bottom: 0;
            width: 400px;
        }
    }
    .jester_ecommerce_news_tabs-container {
        position: sticky;
        top: -5px;
        z-index: 100;
        background-color: #fff;
        padding: 5px 0;
        margin-top: -10px;
    }
</style>
@if(isset($news_categories) && !$news_categories->isEmpty() && isset($news_articles) && !$news_articles->isEmpty())
    <div class="row" style="width: 103%; padding-left: 15px; padding-right: 0px;">
        <div class="col-md-12">
            <!-- CATEGORY TABS -->
            <div class="jester_ecommerce_news_tabs-container">
                <a href="#news_tab_all" data-toggle="tab" class="jester_ecommerce_news_tab active">
                    @lang('lang_v1.all')
                </a>
                @foreach($news_categories as $category)
                    <a href="#news_tab_{{ $category->id }}" data-toggle="tab" class="jester_ecommerce_news_tab">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
            <div class="tab-content">
                <div class="tab-pane active" id="news_tab_all">
                    <div class="jester_ecommerce_news_grid-container">
                        @forelse($news_articles as $item)
                            <div class="jester_ecommerce_news_card" data-title="{{ $item->title_1 }}" data-description="{!! htmlspecialchars($item->description_6) !!}">
                                @if(!empty($item->image_5))
                                    @php
                                        $imagePath = 'uploads/News/' . basename($item->image_5);
                                    @endphp
                                    <div class="jester_ecommerce_news_card-image">
                                        <img src="{{ asset($imagePath) }}" alt="News Image">
                                    </div>
                                @endif
                                <div class="jester_ecommerce_news_card-body">
                                    <h4>{{ $item->title_1 }}</h4>
                                </div>
                            </div>
                        @empty
                            <p>@lang('crm::lang.no_notification_yet')</p>
                        @endforelse
                    </div>
                </div>
                @foreach($news_categories as $category)
                    <div class="tab-pane" id="news_tab_{{ $category->id }}">
                        <div class="jester_ecommerce_news_grid-container">
                            @forelse($news_articles->where('category_id', $category->id) as $item)
                                <div class="jester_ecommerce_news_card" data-title="{{ $item->title_1 }}" data-description="{!! htmlspecialchars($item->description_6) !!}">
                                    @if(!empty($item->image_5))
                                        @php
                                            $imagePath = 'uploads/News/' . basename($item->image_5);
                                        @endphp
                                        <div class="jester_ecommerce_news_card-image">
                                            <img src="{{ asset($imagePath) }}" alt="News Image">
                                        </div>
                                    @endif
                                    <div class="jester_ecommerce_news_card-body">
                                        <h4>{{ $item->title_1 }}</h4>
                                    </div>
                                </div>
                            @empty
                                <p>@lang('crm::lang.no_notification_yet')</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- News Details Modal -->
    <div id="news-details-modal" class="jester_ecommerce_profile-details-modal-container">
        <div class="jester_ecommerce_profile-details-modal-content">
            <div class="jester_ecommerce_profile-modal-header">
                <h3 id="news-modal-title" class="jester_ecommerce_profile-modal-title"></h3>
                <a href="#" id="close-news-modal-btn" class="close-perk-modal-btn"><i class="fas fa-times"></i></a>
            </div>
            <div id="news-modal-body" class="jester_ecommerce_profile-modal-body">
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const newsCards = document.querySelectorAll('.jester_ecommerce_news_card');
        const newsModal = document.getElementById('news-details-modal');
        const closeModalBtn = document.getElementById('close-news-modal-btn');
        const modalTitle = document.getElementById('news-modal-title');
        const modalBody = document.getElementById('news-modal-body');
        newsCards.forEach(card => {
            card.addEventListener('click', function() {
                const title = this.getAttribute('data-title');
                const description = this.getAttribute('data-description');
                modalTitle.textContent = title;
                modalBody.innerHTML = description;
                newsModal.classList.add('active');
            });
        });
        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', function(e) {
                e.preventDefault();
                newsModal.classList.add('closing');
                newsModal.classList.remove('active');
                setTimeout(function() {
                    newsModal.classList.remove('closing');
                }, 300);
            });
        }
        // Tab functionality
        const tabLinks = document.querySelectorAll('.jester_ecommerce_news_tab');
        const tabPanes = document.querySelectorAll('.tab-content .tab-pane');
        tabLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                // Deactivate all tabs and panes
                tabLinks.forEach(tab => tab.classList.remove('active'));
                tabPanes.forEach(pane => pane.classList.remove('active'));
                // Activate clicked tab
                this.classList.add('active');
                // Activate corresponding pane
                const targetPaneId = this.getAttribute('href');
                if (targetPaneId && targetPaneId.startsWith('#')) {
                    const targetPane = document.querySelector(targetPaneId);
                    if (targetPane) {
                        targetPane.classList.add('active');
                    }
                }
            });
        });
    });
    </script>
@else
    <p>@lang('crm::lang.no_notification_yet')</p>
@endif