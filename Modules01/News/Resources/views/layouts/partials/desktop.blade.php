   @if(auth()->user()->can('manage_modules') && session()->has('business'))
    <div class="home-grid-tile" data-key="news">
        <a href="{{ action([\Modules\News\Http\Controllers\NewsController::class, 'index']) }}"  title="{{__('news::lang.news')}}">
            <img src="{{ asset('public/uploads/News/1754559330_news.svg') }}" class="home-icon" alt="">
            <span class="home-label">{{__('news::lang.news')}}</span>
        </a>
    </div>
    @endif