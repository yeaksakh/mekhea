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
                <a class="navbar-brand" href="{{action([\Modules\AutoAudit\Http\Controllers\AutoAuditController::class, 'dashboard'])}}">
                    <i class="fa fa-window-restore"  style="width: 30px; height: auto; color:#cc2828;" aria-hidden="true"></i>
                    @lang("autoaudit::lang.dashboard")
                </a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <!-- Dashboard link -->
                    <li @if(request()->segment(2) == 'AutoAudit') class="active" @endif>
                        <a href="{{action([\Modules\AutoAudit\Http\Controllers\AutoAuditController::class, 'index'])}}">
                            @lang("autoaudit::lang.autoaudit")
                        </a>
                    </li>

                    <!-- Categories link -->
                    <li @if(request()->segment(2) == 'AutoAudit-categories') class="active" @endif>
                        <a href="{{action([\Modules\AutoAudit\Http\Controllers\AutoAuditController::class, 'getCategories'])}}">
                            @lang("autoaudit::lang.AutoAudit_category")
                        </a>
                    </li>

                    <!-- Permission link -->
                    <li @if(request()->segment(2) == 'AutoAudit-permission') class="active" @endif>
                        <a href="{{action([\Modules\AutoAudit\Http\Controllers\SettingController::class, 'showAutoAuditPermissionForm'])}}">
                            @lang("autoaudit::lang.setting")
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</section>