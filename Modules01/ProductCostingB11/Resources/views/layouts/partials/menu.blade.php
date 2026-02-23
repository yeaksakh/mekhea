@if(auth()->user()->can('manage_modules') && session()->has('business'))
    <div class="recommended-item" data-id="productcostingb11">
        <a href="{{ action([\Modules\ProductCostingB11\Http\Controllers\ProductCostingB11Controller::class, 'index']) }}"
        class="recommended-link {{ request()->segment(2) == '/ProductCostingB11' ? 'active' : '' }}">
        <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/modules/product_costing.svg') }}" class="home-icon" alt="">
            <div>
                <p class="text-base font-medium text-gray-800">{{ __('productcostingb11::lang.productcostingb11') }}</p>
                <p class="recommended-text text-sm text-gray-600">{{ __('productcostingb11::lang.productcostingb11') }}</p>
            </div>
        </a>
    </div>
@endif