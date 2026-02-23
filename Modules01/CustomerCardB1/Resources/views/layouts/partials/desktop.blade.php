@php
    $business_id = session()->get('user.business_id');
    $module_util = new \App\Utils\ModuleUtil();
    $is_customercardb1_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'customercardb1_module');
@endphp

@if ($is_customercardb1_enabled && (auth()->user()->can('superadmin') || auth()->user()->can('customercardb1.view_customercardb1')))
<div class="home-grid-tile" data-key="customercardb1-dashboard">
    <a href="{{ action([\Modules\CustomerCardB1\Http\Controllers\ContactController::class, 'index'], ['type' => 'customer']) }}"
        title="{{ __('customercardb1::lang.customercardb1') }}">
        <img src="{{ asset('public/uploads/CustomerCardB1/icons/contact/customer_card.svg') }}" 
             class="home-icon"
             alt="{{ __('customercardb1::lang.customercardb1') }}">
        <span class="home-label">{{ __('customercardb1::lang.customercardb1') }}</span>
    </a>
</div>
@endif