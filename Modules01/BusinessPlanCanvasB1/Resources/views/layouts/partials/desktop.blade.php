@php
    $business_id = session()->get('user.business_id');
    $module_util = new \App\Utils\ModuleUtil();
    $is_businessplancanvasb1_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'businessplancanvasb1_module');
@endphp

@if ($is_businessplancanvasb1_enabled && (auth()->user()->can('superadmin') || auth()->user()->can('businessplancanvasb1.access')))
    <div class="home-grid-tile" data-key="businessplancanvasb1-dashboard">
        <a href="{{ action([\Modules\BusinessPlanCanvasB1\Http\Controllers\BusinessPlanCanvasB1Controller::class, 'dashboard']) }}"
           title="{{ __('businessplancanvasb1::lang.business_plan_canvas_b1') }}">
           <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/modules/business_plan_canvas.svg') }}"
                class="home-icon"
                alt="">
           <span class="home-label">{{ __('businessplancanvasb1::lang.businessplancanvasb1') }}</span>
        </a>
    </div>
@endif