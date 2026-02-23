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
                <a class="navbar-brand" href="{{action([\Modules\SWOT\Http\Controllers\SWOTController::class, 'dashboard'])}}">
                    <i class="fa "  style="width: 30px; height: auto; color:#000000;" aria-hidden="true"></i>
                    @lang("swot::lang.dashboard")
                </a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <!-- Dashboard link -->
                    <li @if(request()->segment(2) == 'SWOT') class="active" @endif>
                        <a href="{{action([\Modules\SWOT\Http\Controllers\SWOTController::class, 'index'])}}">
                            @lang("swot::lang.swot")
                        </a>
                    </li>

                    <!-- Categories link -->
                    <li @if(request()->segment(2) == 'SWOT-categories') class="active" @endif>
                        <a href="{{action([\Modules\SWOT\Http\Controllers\SWOTController::class, 'getCategories'])}}">
                            @lang("swot::lang.SWOT_category")
                        </a>
                    </li>

                    <!-- Permission link -->
                    <li @if(request()->segment(2) == 'SWOT-permission') class="active" @endif>
                        <a href="{{action([\Modules\SWOT\Http\Controllers\SettingController::class, 'showSWOTPermissionForm'])}}">
                            @lang("swot::lang.setting")
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</section>