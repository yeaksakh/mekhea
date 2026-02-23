@php
    $business_id = session()->get('user.business_id');
    $module_util = new \App\Utils\ModuleUtil();
    $is_repair_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'repair_module');
@endphp

@if ($is_repair_enabled && (auth()->user()->can('superadmin') || auth()->user()->can('repair.access')))
    <div class="home-grid-tile" data-key="repair-dashboard">
        <a href="{{ action([\Modules\Repair\Http\Controllers\DashboardController::class, 'index']) }}"
            title="{{ __('repair::lang.repair') }}">
            <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/modules/repair.svg') }}"
                class="home-icon"
                alt="">
            <span class="home-label">{{ __('repair::lang.repair') }}</span>
        </a>
    </div>
@endif