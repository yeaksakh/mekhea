@if(auth()->user()->can('manage_modules') && session()->has('business'))
    <div class="home-grid-tile" data-key="visa-dashboard">
        <a href="{{ action([\Modules\Visa\Http\Controllers\IndicatorController::class, 'appraisal_list']) }}"
            title="{{ __( 'visa::lang.appraisal_list' ) }}">
            <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/modules/visa.svg') }}" class="home-icon" alt="">
            <span class="home-label">{{ __( 'visa::lang.appraisal_list' ) }}</span>
        </a>
    </div>
@endif
