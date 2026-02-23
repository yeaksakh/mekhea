@if(auth()->user()->can('manage_modules') && session()->has('business'))
    <div class="home-grid-tile" data-key="productcostingb11-dashboard">
        <a href="{{ action([\Modules\ProductCostingB11\Http\Controllers\ProductCostingB11Controller::class, 'index']) }}"
            title="{{ __('productcostingb11::lang.productcostingb11') }}">
            <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/modules/product_costing.svg') }}" class="home-icon" alt="">
            <span class="home-label">{{ __('productcostingb11::lang.productcostingb11') }}</span>
        </a>
    </div>
@endif
