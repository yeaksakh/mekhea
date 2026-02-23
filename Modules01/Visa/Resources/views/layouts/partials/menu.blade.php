@if(auth()->user()->can('manage_modules') && session()->has('business'))
    <div class="recommended-item" data-id="visa">
        <a href="{{ action([\Modules\Visa\Http\Controllers\IndicatorController::class, 'appraisal_list']) }}"
        class="recommended-link {{ request()->segment(2) == '/appraisal-list' ? 'active' : '' }}">
        <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/modules/visa.svg') }}" class="home-icon" alt="">
            <div>
                <p class="text-base font-medium text-gray-800">{{ __( 'visa::lang.appraisal_list' ) }}</p>
                <p class="recommended-text text-sm text-gray-600">{{ __( 'visa::lang.appraisal_list' ) }}</p>
            </div>
        </a>
    </div>
@endif