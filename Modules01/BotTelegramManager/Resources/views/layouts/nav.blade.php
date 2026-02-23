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
                <a class="navbar-brand" href="{{action([\Modules\BotTelegramManager\Http\Controllers\BotTelegramManagerController::class, 'dashboard'])}}">
                    <i class="fa "  style="width: 30px; height: auto; color:#9c2626;" aria-hidden="true"></i>
                    @lang("bottelegrammanager::lang.dashboard")
                </a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <!-- Dashboard link -->
                    <li @if(request()->segment(2) == 'BotTelegramManager') class="active" @endif>
                        <a href="{{action([\Modules\BotTelegramManager\Http\Controllers\BotTelegramManagerController::class, 'index'])}}">
                            @lang("bottelegrammanager::lang.bottelegrammanager")
                        </a>
                    </li>

                    <!-- Categories link -->
                    <li @if(request()->segment(2) == 'BotTelegramManager-categories') class="active" @endif>
                        <a href="{{action([\Modules\BotTelegramManager\Http\Controllers\BotTelegramManagerController::class, 'getCategories'])}}">
                            @lang("bottelegrammanager::lang.BotTelegramManager_category")
                        </a>
                    </li>

                    <!-- Permission link -->
                    <li @if(request()->segment(2) == 'BotTelegramManager-permission') class="active" @endif>
                        <a href="{{action([\Modules\BotTelegramManager\Http\Controllers\SettingController::class, 'showBotTelegramManagerPermissionForm'])}}">
                            @lang("bottelegrammanager::lang.setting")
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</section>