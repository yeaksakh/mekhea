   @if(auth()->user()->can('manage_modules') && session()->has('business'))
    <div class="home-grid-tile" data-key="productbook">
        <a href="{{ action([\Modules\ProductBook\Http\Controllers\ProductBookController::class, 'index']) }}"  title="{{__('productbook::lang.productbook')}}">
            <img src="{{ asset('public/uploads/ProductBook/1754730626_book.svg') }}" class="home-icon" alt="">
            <span class="home-label">{{__('productbook::lang.productbook')}}</span>
        </a>
    </div>
    @endif