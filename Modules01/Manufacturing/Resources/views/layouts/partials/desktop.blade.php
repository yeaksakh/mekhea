@can('manufacturing.access_recipe')
    <div class="home-grid-tile" data-key="manufacturing">
        <a class="navbar-brand" href="{{action([\Modules\Manufacturing\Http\Controllers\RecipeController::class, 'index'])}}" title="{{ __('manufacturing::lang.manufacturing') }}">
            <img src="{{ asset('public/icons/' . (session('business.icon_pack') ?: 'v1') . '/manufacturing/manufacturing.svg') }}" class="home-icon" alt="{{ __('manufacturing::lang.manufacturing') }}">
            <span class="home-label">{{ __('manufacturing::lang.manufacturing') }}</span>
        </a>
       
       
    </div>

@endcan