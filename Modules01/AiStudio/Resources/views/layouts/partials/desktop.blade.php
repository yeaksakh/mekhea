@php
    $business_id = session()->get('user.business_id');
    $module_util = new \App\Utils\ModuleUtil();
    $is_aistudio_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'aistudio_module');
@endphp

@if ($is_aistudio_enabled && (auth()->user()->can('superadmin') || auth()->user()->can('aistudio.access')))
    <div class="home-grid-tile" data-key="aistudio-dashboard">
        <a href="{{ action([\Modules\AiStudio\Http\Controllers\ChatController::class, 'index']) }}"
            title="{{ __('aistudio::lang.aistudio') }}">
            <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/modules/ai_gemini.svg') }}"
                class="home-icon"
                alt="">
            <span class="home-label">{{ __('aistudio::lang.aistudio') }}</span>
        </a>
    </div>
@endif

@if ($is_aistudio_enabled && (auth()->user()->can('superadmin') || auth()->user()->can('aistudio.access')))
    <div class="home-grid-tile" data-key="aistudio-deepseek-dashboard">
        <a href="{{ action([\Modules\AiStudio\Http\Controllers\DeepSeekController::class, 'index']) }}"
            title="{{ __('aistudio::lang.aistudio') }}">
            <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/modules/ai_deepseek.svg') }}"
                class="home-icon"
                alt="">
            <span class="home-label">{{ __('aistudio::lang.deepseek') }}</span>
        </a>
    </div>
@endif
