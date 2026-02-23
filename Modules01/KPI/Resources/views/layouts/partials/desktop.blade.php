@php
    $business_id = session()->get('user.business_id');
    $module_util = new \App\Utils\ModuleUtil();
    $is_kpi_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'kpi_module');
@endphp

@if ($is_kpi_enabled && (auth()->user()->can('superadmin') || auth()->user()->can('kpi.access')))
    <div class="home-grid-tile" data-key="kpi-dashboard">
        <a href="{{ action([\Modules\KPI\Http\Controllers\IndicatorController::class, 'index']) . '?kpi_view=list_view' }}"
           title="{{ __('kpi::lang.kpi_indicators') }}">
           <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/modules/kpi.svg') }}"
                class="home-icon"
                alt="">
           <span class="home-label">{{ __('kpi::lang.indicator') }}</span>
        </a>
    </div>
@endif