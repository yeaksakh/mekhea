    @if(auth()->user()->can('manage_modules') && session()->has('business'))
    <div class="recommended-item" data-id="customerstock">
        <a href="{{ action([\Modules\CustomerStock\Http\Controllers\CustomerStockController::class, 'index']) }}"  title="{{__('customerstock::lang.customerstock')}}"
        class="recommended-link {{ request()->segment(2) == 'customerstock' ? 'active' : '' }}">
            <img src="{{ asset('public/uploads/CustomerStock/1758168084_customer_stock.svg') }}"
                class="recommended-icon" alt="">
            <div>
                <p class="text-base font-medium text-gray-800">{{__('customerstock::lang.customerstock')}}</p>
                <p class="recommended-text text-sm text-gray-600">Manage {{__('customerstock::lang.customerstock')}}</p>
            </div>
        </a>
    </div>
    @endif