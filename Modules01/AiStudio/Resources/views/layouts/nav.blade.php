<section class="no-print">
    <nav class="navbar navbar-default bg-white m-4">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{action([\Modules\AiStudio\Http\Controllers\AiStudioController::class, 'dashboard'])}}">
                    <i class="fa fa-ad"  style="width: 30px; height: auto; color:#000000;" aria-hidden="true"></i>
                    @lang("aistudio::lang.dashboard")
                </a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <!-- Dashboard link -->
                    {{-- <li @if(request()->segment(2) == 'AiStudio') class="active" @endif>
                        <a href="{{action([\Modules\AiStudio\Http\Controllers\AiStudioController::class, 'index'])}}">
                            @lang("aistudio::lang.aistudio")
                        </a>
                    </li> --}}

                    <!-- Categories link -->
                    {{-- <li @if(request()->segment(2) == 'AiStudio-categories') class="active" @endif>
                        <a href="{{action([\Modules\AiStudio\Http\Controllers\AiStudioController::class, 'getCategories'])}}">
                            @lang("aistudio::lang.AiStudio_category")
                        </a>
                    </li> --}}

                    <!-- Permission link -->
                    {{-- <li @if(request()->segment(2) == 'AiStudio-permission') class="active" @endif>
                        <a href="{{action([\Modules\AiStudio\Http\Controllers\SettingController::class, 'showAiStudioPermissionForm'])}}">
                            @lang("aistudio::lang.setting")
                        </a>
                    </li> --}}

                    <!-- Ai Chat Bot link -->
                    <li @if(request()->segment(2) == 'AiStudio-chat') class="active" @endif>
                        <a href="{{action([\Modules\AiStudio\Http\Controllers\ChatController::class, 'index'])}}">
                            @lang("aistudio::lang.aistudio")
                        </a>
                    </li>

                    <!-- Ai Deepseek Chat Bot link -->
                    <li @if(request()->segment(2) == 'AiStudio-deepseek') class="active" @endif>
                        <a href="{{action([\Modules\AiStudio\Http\Controllers\DeepSeekController::class, 'index'])}}">
                            @lang("aistudio::lang.deepseek")
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</section>