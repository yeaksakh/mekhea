    <div class="recommended-item" data-id="swot">
        <a href="{{ action([\Modules\SWOT\Http\Controllers\SWOTController::class, 'index']) }}"  title="{{__('swot::lang.swot')}}"
        class="recommended-link {{ request()->segment(2) == 'swot' ? 'active' : '' }}">
            <img src="{{ asset('public/uploads/SWOT/1752759170_swot.svg') }}"
                class="recommended-icon" alt="">
            <div>
                <p class="text-base font-medium text-gray-800">{{__('swot::lang.swot')}}</p>
                <p class="recommended-text text-sm text-gray-600">Manage {{__('swot::lang.swot')}}</p>
            </div>
        </a>
    </div>