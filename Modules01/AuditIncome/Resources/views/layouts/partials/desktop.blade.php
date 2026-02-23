@if (auth()->user()->can('auditincome.view_auditincome'))
    <div class="home-grid-tile" data-key="auditincome-dashboard">
        <a href="{{ action([\Modules\AuditIncome\Http\Controllers\SellController::class, 'index']) }}"
           title="{{ __('auditincome::lang.auditincome') }}">
           <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/modules/audit_income.svg') }}" class="home-icon" alt="">
            <span class="home-label">{{ __('auditincome::lang.auditincome') }}</span>
        </a>
    </div>
@endif