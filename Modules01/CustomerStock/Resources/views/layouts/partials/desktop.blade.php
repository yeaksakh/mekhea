   @if(auth()->user()->can('manage_modules') && session()->has('business'))
    <div class="home-grid-tile" data-key="customerstock">
        <a href="{{ action([\Modules\CustomerStock\Http\Controllers\CustomerStockController::class, 'index']) }}"  title="{{__('customerstock::lang.customerstock')}}">
            <img src="{{ asset('public/uploads/CustomerStock/1758168084_customer_stock.svg') }}" class="home-icon" alt="">
            <span class="home-label">{{__('customerstock::lang.customerstock')}}</span>
        </a>
    </div>
    @endif