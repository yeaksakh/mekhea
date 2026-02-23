    @if(auth()->user()->can('manage_modules') && session()->has('business'))
    <div class="recommended-item" data-id="documentary">
        <a href="{{ action([\Modules\Documentary\Http\Controllers\DocumentaryController::class, 'index']) }}"  title="{{__('documentary::lang.documentary')}}"
        class="recommended-link {{ request()->segment(2) == 'documentary' ? 'active' : '' }}">
            <img src="{{ asset('public/uploads/Documentary/1759399102_documentary.svg') }}"
                class="recommended-icon" alt="">
            <div>
                <p class="text-base font-medium text-gray-800">{{__('documentary::lang.documentary')}}</p>
                <p class="recommended-text text-sm text-gray-600">Manage {{__('documentary::lang.documentary')}}</p>
            </div>
        </a>
    </div>
    @endif