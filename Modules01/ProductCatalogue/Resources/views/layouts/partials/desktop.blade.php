@if(auth()->user()->can('manage_modules') && session()->has('business'))
@endif
<div class="home-grid-tile" data-key="productcatalogue-dashboard">
    <a href="{{ action([\Modules\ProductCatalogue\Http\Controllers\ProductCatalogueController::class, 'generateQr']) }}"
        title="{{ __( 'productcatalogue::lang.catalogue_qr' ) }}">
        <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/modules/product_catalogue.svg') }}" class="home-icon" alt="">
        <span class="home-label">{{ __( 'productcatalogue::lang.catalogue_qr' ) }}</span>
    </a>
</div>
