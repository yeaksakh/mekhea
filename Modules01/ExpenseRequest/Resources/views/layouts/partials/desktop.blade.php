@php
    $business_id = session()->get('user.business_id');
    $module_util = new \App\Utils\ModuleUtil();
    $is_expenserequest_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'expenserequest_module');
@endphp

@if ($is_expenserequest_enabled && (auth()->user()->can('superadmin') || auth()->user()->can('expenserequest.access')))
    <div class="home-grid-tile" data-key="expenserequest-dashboard">
        <a href="{{ action([\Modules\ExpenseRequest\Http\Controllers\ExpenseController::class, 'indexExpenseRequest']) }}"
            title="{{ __('expenserequest::lang.expenserequest') }}">
            <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/modules/expense_request.svg') }}"
                class="home-icon"
                alt="">
            <span class="home-label">{{ __('expense.expenses_request') }}</span>
        </a>
    </div>
@endif