    @if(auth()->user()->can('manage_modules') && session()->has('business'))
    <div class="recommended-item" data-id="productdoc">
        <a href="{{ action([\Modules\ProductDoc\Http\Controllers\ProductDocController::class, 'index']) }}"  title="{{__('productdoc::lang.productdoc')}}"
        class="recommended-link {{ request()->segment(2) == 'productdoc' ? 'active' : '' }}">
            <img src="{{ asset('public/uploads/ProductDoc/1762414083_product-guide-svgrepo-com.svg') }}"
                class="recommended-icon" alt="">
            <div>
                <p class="text-base font-medium text-gray-800">{{__('productdoc::lang.productdoc')}}</p>
                <p class="recommended-text text-sm text-gray-600">Manage {{__('productdoc::lang.productdoc')}}</p>
            </div>
        </a>
    </div>
    @endif