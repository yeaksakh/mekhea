  @if(auth()->user()->can('manage_modules') && session()->has('business')) 
<div class="home-grid-tile" data-key="aiassistance">
    <a href="{{action([\Modules\AiAssistance\Http\Controllers\AiAssistanceController::class, 'index'])}}" title="{{__('aiassistance::lang.aiassistance')}}">
        <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/modules/ai.svg') }}" class="home-icon" alt="">
        <span class="home-label">{{__('aiassistance::lang.aiassistance')}}</span>
    </a>
</div>
@endif