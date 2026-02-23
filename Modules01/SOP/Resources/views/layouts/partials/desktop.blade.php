   
    <div class="home-grid-tile" data-key="sop">
        <a href="{{ action([\Modules\SOP\Http\Controllers\SOPController::class, 'index']) }}"  title="{{__('sop::lang.sop')}}">
            <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/modules/sop.svg') }}" width="40" height="40" alt="">
            <span class="home-label">{{__('sop::lang.sop')}}</span>
        </a>
    </div>