@if (auth()->user()->can('aistudio.view_aistudio'))
    <div class="recommended-item" data-id="gemini">
        <a href="{{ action([\Modules\AiStudio\Http\Controllers\ChatController::class, 'index']) }}"
           class="recommended-link {{ request()->segment(2) == 'AiStudio-chat' ? 'active' : '' }}">
            <img src="{{ asset('public/uploads/AiStudio/icons/gemini/google_gemini.svg') }}"
                class="recommended-icon" alt="">
            <div>
                <p class="text-base font-medium text-gray-800">@lang('aistudio::lang.aistudio')</p>
                <p class="recommended-text text-sm text-gray-600">Gemini</p>
            </div>
        </a>
    </div>
@endif

@if (auth()->user()->can('aistudio.view_aistudio'))
    <div class="recommended-item" data-id="deepseek">
        <a href="{{ action([\Modules\AiStudio\Http\Controllers\DeepSeekController::class, 'index']) }}"
           class="recommended-link {{ request()->segment(2) == 'AiStudio-deepseek' ? 'active' : '' }}">
            <img src="{{ asset('public/uploads/AiStudio/icons/deepseek/deepseek.svg') }}"
                class="recommended-icon" alt="">
            <div>
                <p class="text-base font-medium text-gray-800">@lang('aistudio::lang.aistudio')</p>
                <p class="recommended-text text-sm text-gray-600">Deepseek</p>
            </div>
        </a>
    </div>
@endif