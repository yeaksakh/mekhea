    @if(auth()->user()->can('manage_modules') && session()->has('business'))
    <div class="recommended-item" data-id="bottelegrammanager">
        <a href="{{ action([\Modules\BotTelegramManager\Http\Controllers\BotTelegramManagerController::class, 'index']) }}"  title="{{__('bottelegrammanager::lang.bottelegrammanager')}}"
        class="recommended-link {{ request()->segment(2) == 'bottelegrammanager' ? 'active' : '' }}">
            <img src="{{ asset('public/uploads/BotTelegramManager/') }}"
                class="recommended-icon" alt="">
            <div>
                <p class="text-base font-medium text-gray-800">{{__('bottelegrammanager::lang.bottelegrammanager')}}</p>
                <p class="recommended-text text-sm text-gray-600">Manage {{__('bottelegrammanager::lang.bottelegrammanager')}}</p>
            </div>
        </a>
    </div>
    @endif