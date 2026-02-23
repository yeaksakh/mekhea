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
                <a class="navbar-brand" href="{{action([\Modules\BusinessPlanCanvasB1\Http\Controllers\BusinessPlanCanvasB1Controller::class, 'dashboard'])}}">
                   
                    @lang("businessplancanvasb1::lang.dashboard")
                </a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li @if(request()->segment(2) == 'BusinessPlanCanvasB1') class="active" @endif>
                        <a href="{{action([\Modules\BusinessPlanCanvasB1\Http\Controllers\BusinessPlanCanvasB1Controller::class, 'index'])}}">
                            @lang("businessplancanvasb1::lang.businessplancanvasb1")
                        </a>
                    </li>
                   
                    <li @if(request()->segment(2) == 'BusinessPlanCanvasB1-permission') class="active" @endif>
                        <a href="{{action([\Modules\BusinessPlanCanvasB1\Http\Controllers\SettingController::class, 'showBusinessPlanCanvasB1PermissionForm'])}}">
                            @lang("businessplancanvasb1::lang.setting")
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</section>