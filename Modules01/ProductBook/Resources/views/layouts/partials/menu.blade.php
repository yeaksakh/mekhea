    @if(auth()->user()->can('manage_modules') && session()->has('business'))
    <div class="recommended-item" data-id="productbook">
        <a href="{{ action([\Modules\ProductBook\Http\Controllers\ProductBookController::class, 'index']) }}"  title="{{__('productbook::lang.productbook')}}"
        class="recommended-link {{ request()->segment(2) == 'productbook' ? 'active' : '' }}">
            <img src="{{ asset('public/uploads/ProductBook/1754730626_book.svg') }}"
                class="recommended-icon" alt="">
            <div>
                <p class="text-base font-medium text-gray-800">{{__('productbook::lang.productbook')}}</p>
                <p class="recommended-text text-sm text-gray-600">Manage {{__('productbook::lang.productbook')}}</p>
            </div>
        </a>
    </div>
    @endif