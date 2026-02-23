@php
    $business_id = session()->get('user.business_id');
    $module_util = new \App\Utils\ModuleUtil();
    $is_documentkeeper_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'documentkeeper_module');
@endphp

@if ($is_documentkeeper_enabled && (auth()->user()->can('superadmin') || auth()->user()->can('documentkeeper.view_documentkeeper')))
    <div class="home-grid-tile" data-key="documentkeeper-dashboard">
        <a href="{{ action([\Modules\DocumentKeeper\Http\Controllers\DocumentKeeperController::class, 'index']) }}"
            title="{{ __('documentkeeper::lang.documentkeeper') }}">
            <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/modules/document_keeper.svg') }}" class="home-icon" alt="">
            <span class="home-label">{{ __('documentkeeper::lang.documentkeeper') }}</span>
        </a>
    </div>
@endif
