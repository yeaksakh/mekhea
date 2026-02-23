     
       
<div class="home-grid-tile" data-key="mini_app">
    <a href="{{ action([\Modules\ModuleCreateModule\Http\Controllers\ModuleCreateModuleController::class, 'index']) }}"  title="{{__('Mini App')}}">
        <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/desktop/mini_app.svg') }}" class="home-icon" alt="">
        <span class="home-label">{{__('Mini App')}}</span>
    </a>
</div>
