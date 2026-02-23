    @if(auth()->user()->can('manage_modules') && session()->has('business'))
    <div class="recommended-item" data-id="news">
        <a href="{{ action([\Modules\News\Http\Controllers\NewsController::class, 'index']) }}"  title="{{__('news::lang.news')}}"
        class="recommended-link {{ request()->segment(2) == 'news' ? 'active' : '' }}">
            <img src="{{ asset('public/uploads/News/1754559330_news.svg') }}"
                class="recommended-icon" alt="">
            <div>
                <p class="text-base font-medium text-gray-800">{{__('news::lang.news')}}</p>
                <p class="recommended-text text-sm text-gray-600">Manage {{__('news::lang.news')}}</p>
            </div>
        </a>
    </div>
    @endif