@if (auth()->user()->can('auditincome.view_auditincome'))
    <div class="recommended-item" data-id="audit-b1">
        <a href="{{ action([\Modules\AuditIncome\Http\Controllers\SellController::class, 'index']) }}"
           class="recommended-link {{ request()->segment(2) == 'AuditIncome-sells' ? 'active' : '' }}">
           <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/modules/audit_income.svg') }}" class="home-icon" alt="">
            <div>
                <p class="text-base font-medium text-gray-800">@lang('auditincome::lang.auditincome')</p>
                <p class="recommended-text text-sm text-gray-600">@lang('auditincome::lang.auditincome')</p>
            </div>
        </a>
    </div>
@endif