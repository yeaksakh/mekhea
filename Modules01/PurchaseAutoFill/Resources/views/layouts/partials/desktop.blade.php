   @if(auth()->user()->can('manage_modules') && session()->has('business'))
    <div class="home-grid-tile" data-key="purchaseautofill">
        <a href="{{ action([\Modules\PurchaseAutoFill\Http\Controllers\PurchaseAutoFillController::class, 'index']) }}"  title="{{__('purchaseautofill::lang.purchaseautofill')}}">
            <img src="{{ asset('public/uploads/PurchaseAutoFill/1761547752_PurchaseAutoFill.svg') }}" class="home-icon" alt="">
            <span class="home-label">{{__('purchaseautofill::lang.purchaseautofill')}}</span>
        </a>
    </div>
    @endif