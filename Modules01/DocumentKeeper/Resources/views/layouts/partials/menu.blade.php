@if(auth()->user()->can('manage_modules') && session()->has('business'))
    <div class="recommended-item" data-id="documentkeeper">
        <a href="{{ action([\Modules\DocumentKeeper\Http\Controllers\DocumentKeeperController::class, 'index']) }}"
        class="recommended-link {{ request()->segment(2) == '/DocumentKeeper' ? 'active' : '' }}">
        <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/modules/document_keeper.svg') }}" class="home-icon" alt="">
            <div>
                <p class="text-base font-medium text-gray-800">{{ __('documentkeeper::lang.documentkeeper') }}</p>
                <p class="recommended-text text-sm text-gray-600">{{ __('documentkeeper::lang.documentkeeper') }}</p>
            </div>
        </a>
    </div>
@endif