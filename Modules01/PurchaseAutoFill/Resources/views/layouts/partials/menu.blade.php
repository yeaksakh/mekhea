    @if(auth()->user()->can('manage_modules') && session()->has('business'))
    <div class="recommended-item" data-id="purchaseautofill">
        <a href="{{ action([\Modules\PurchaseAutoFill\Http\Controllers\PurchaseAutoFillController::class, 'index']) }}"  title="{{__('purchaseautofill::lang.purchaseautofill')}}"
        class="recommended-link {{ request()->segment(2) == 'purchaseautofill' ? 'active' : '' }}">
            <img src="{{ asset('public/uploads/PurchaseAutoFill/1761547752_PurchaseAutoFill.svg') }}"
                class="recommended-icon" alt="">
            <div>
                <p class="text-base font-medium text-gray-800">{{__('purchaseautofill::lang.purchaseautofill')}}</p>
                <p class="recommended-text text-sm text-gray-600">Manage {{__('purchaseautofill::lang.purchaseautofill')}}</p>
            </div>
        </a>
    </div>
    @endif