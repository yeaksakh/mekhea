
@if(auth()->user()->can('user.view'))
<div class="home-grid-tile" data-key="accounting">
    <a href="{{action([\Modules\Accounting\Http\Controllers\AccountingController::class, 'dashboard'])}}" title="{{__('accounting::lang.accounting')}}">
        <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/modules/accounting.svg') }}" class="home-icon" alt="">
        <span class="home-label">{{__('accounting::lang.accounting')}}</span>
    </a>


</div>
@endif