@if(auth()->user()->can('manage_modules') && session()->has('business'))
    <div class="recommended-item" data-id="productcatalogue">
        <a href="{{ action([\Modules\ProductCatalogue\Http\Controllers\ProductCatalogueController::class, 'generateQr']) }}"
        class="recommended-link {{ request()->segment(2) == '/catalogue-qr' ? 'active' : '' }}">
        <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/modules/product_catalogue.svg') }}" class="home-icon" alt="">
            <div>
                <p class="text-base font-medium text-gray-800">{{ __( 'productcatalogue::lang.catalogue_qr' ) }}</p>
                <p class="recommended-text text-sm text-gray-600">{{ __( 'productcatalogue::lang.catalogue_qr' ) }}</p>
            </div>
        </a>
    </div>
@endif