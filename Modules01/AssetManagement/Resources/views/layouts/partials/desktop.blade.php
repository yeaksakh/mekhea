  @if(auth()->user()->can('manage_modules') && session()->has('business')) 
<div class="home-grid-tile" data-key="accounting">
    <a href="{{action([\Modules\AssetManagement\Http\Controllers\AssetController::class, 'dashboard'])}}" title="{{__('assetmanagement::lang.asset_management')}}">
        <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/modules/asset_management.svg') }}" class="home-icon" alt="">
        <span class="home-label">{{__('assetmanagement::lang.asset_management')}}</span>
    </a>
</div>
@endif