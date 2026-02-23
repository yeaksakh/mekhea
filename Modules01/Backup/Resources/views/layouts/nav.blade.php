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
                <a class="navbar-brand" href="{{action([\Modules\Backup\Http\Controllers\BackupController::class, 'dashboard'])}}">
                    <i class="fa "  style="width: 30px; height: auto; color:#000000;" aria-hidden="true"></i>
                    @lang("backup::lang.dashboard")
                </a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <!-- Dashboard link -->
                    <li @if(request()->segment(2) == 'Backup') class="active" @endif>
                        <a href="{{action([\Modules\Backup\Http\Controllers\BackupController::class, 'index'])}}">
                            @lang("backup::lang.backup")
                        </a>
                    </li>

                    <!-- Categories link -->
                    <li @if(request()->segment(2) == 'Backup-categories') class="active" @endif>
                        <a href="{{action([\Modules\Backup\Http\Controllers\BackupController::class, 'getCategories'])}}">
                            @lang("backup::lang.Backup_category")
                        </a>
                    </li>

                    <!-- Permission link -->
                    <li @if(request()->segment(2) == 'Backup-permission') class="active" @endif>
                        <a href="{{action([\Modules\Backup\Http\Controllers\SettingController::class, 'showBackupPermissionForm'])}}">
                            @lang("backup::lang.setting")
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</section>