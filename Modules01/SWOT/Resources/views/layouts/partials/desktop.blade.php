@php
    $business_id = session()->get('user.business_id');
    $module_util = new \App\Utils\ModuleUtil();
    $is_swot_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'swot_module');
@endphp

@if ($is_swot_enabled && (auth()->user()->can('superadmin') || auth()->user()->can('swot.access')))
    <div class="home-grid-tile" data-key="swot-dashboard">
        <a href="{{ action([\Modules\SWOT\Http\Controllers\SWOTController::class, 'index']) }}"
            title="{{ __('swot::lang.swot') }}">
            <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/modules/swot.svg') }}"
                class="home-icon"
                alt="">
            <span class="home-label">{{ __('swot::lang.swot') }}</span>
        </a>
    </div>
@endif