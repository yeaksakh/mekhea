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
                <a class="navbar-brand" href="{{action([\Modules\Announcement\Http\Controllers\AnnouncementController::class, 'dashboard'])}}">
                    <i class="fa fa-comment-alt"  style="width: 30px; height: auto; color:#000000;" aria-hidden="true"></i>
                    @lang("announcement::lang.dashboard")
                </a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <!-- Dashboard link -->
                    <li @if(request()->segment(2) == 'Announcement') class="active" @endif>
                        <a href="{{action([\Modules\Announcement\Http\Controllers\AnnouncementController::class, 'index'])}}">
                            @lang("announcement::lang.announcement")
                        </a>
                    </li>

                    <!-- Categories link -->
                    <li @if(request()->segment(2) == 'Announcement-categories') class="active" @endif>
                        <a href="{{action([\Modules\Announcement\Http\Controllers\AnnouncementController::class, 'getCategories'])}}">
                            @lang("announcement::lang.Announcement_category")
                        </a>
                    </li>

                    <!-- Permission link -->
                    <li @if(request()->segment(2) == 'Announcement-permission') class="active" @endif>
                        <a href="{{action([\Modules\Announcement\Http\Controllers\SettingController::class, 'showAnnouncementPermissionForm'])}}">
                            @lang("announcement::lang.setting")
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</section>