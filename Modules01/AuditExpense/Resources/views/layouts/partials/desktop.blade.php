@php
    $business_id = session()->get('user.business_id');
    $module_util = new \App\Utils\ModuleUtil();
    $is_auditexpense_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'auditexpense_module');
@endphp

@if ($is_auditexpense_enabled && (auth()->user()->can('superadmin') || auth()->user()->can('auditexpense.access')))
    <div class="home-grid-tile" data-key="auditexpense-dashboard">
        <a href="{{ action([\Modules\AuditExpense\Http\Controllers\ExpenseController::class, 'index']) }}"
           title="{{ __('auditexpense::lang.auditexpense') }}">
           <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/modules/audit_expense.svg') }}"
                class="home-icon"
                alt="">
            <span class="home-label">{{ __('auditexpense::lang.auditexpense') }}</span>
        </a>
    </div>
@endif