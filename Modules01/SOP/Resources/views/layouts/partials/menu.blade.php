    <div class="recommended-item" data-id="sop">
        <a href="{{ action([\Modules\SOP\Http\Controllers\SOPController::class, 'index']) }}"  title="{{__('sop::lang.sop')}}"
        class="recommended-link {{ request()->segment(2) == 'sop' ? 'active' : '' }}">
            <img src="{{ asset('public/uploads/SOP/1752721052_sop.svg') }}"
                class="recommended-icon" alt="">
            <div>
                <p class="text-base font-medium text-gray-800">{{__('sop::lang.sop')}}</p>
                <p class="recommended-text text-sm text-gray-600">Manage {{__('sop::lang.sop')}}</p>
            </div>
        </a>
    </div>