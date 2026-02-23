   @if(auth()->user()->can('manage_modules') && session()->has('business'))
    <div class="home-grid-tile" data-key="documentary">
        <a href="{{ action([\Modules\Documentary\Http\Controllers\DocumentaryController::class, 'index']) }}"  title="{{__('documentary::lang.documentary')}}">
            <img src="{{ asset('public/uploads/Documentary/1759399102_documentary.svg') }}" class="home-icon" alt="">
            <span class="home-label">{{__('documentary::lang.documentary')}}</span>
        </a>
    </div>
    @endif