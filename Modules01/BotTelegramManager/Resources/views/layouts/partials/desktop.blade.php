   @if(auth()->user()->can('manage_modules') && session()->has('business'))
    <div class="home-grid-tile" data-key="bottelegrammanager">
        <a href="{{ action([\Modules\BotTelegramManager\Http\Controllers\BotTelegramManagerController::class, 'index']) }}"  title="{{__('bottelegrammanager::lang.bottelegrammanager')}}">
            <img src="{{ asset('public/uploads/BotTelegramManager/') }}" class="home-icon" alt="">
            <span class="home-label">{{__('bottelegrammanager::lang.bottelegrammanager')}}</span>
        </a>
    </div>
    @endif