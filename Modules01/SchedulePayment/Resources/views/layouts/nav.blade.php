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
                <a class="navbar-brand" href="{{action([\Modules\SchedulePayment\Http\Controllers\SchedulePaymentController::class, 'dashboard'])}}">
                    <i class="fa "  style="width: 30px; height: auto; color:#000000;" aria-hidden="true"></i>
                    @lang("schedulepayment::lang.dashboard")
                </a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <!-- Dashboard link -->
                    <li @if(request()->segment(2) == 'SchedulePayment') class="active" @endif>
                        <a href="{{action([\Modules\SchedulePayment\Http\Controllers\SchedulePaymentController::class, 'index'])}}">
                            @lang("schedulepayment::lang.schedulepayment")
                        </a>
                    </li>

                    <!-- Categories link -->
                    <li @if(request()->segment(2) == 'SchedulePayment-categories') class="active" @endif>
                        <a href="{{action([\Modules\SchedulePayment\Http\Controllers\SchedulePaymentController::class, 'getCategories'])}}">
                            @lang("schedulepayment::lang.SchedulePayment_category")
                        </a>
                    </li>

                    <!-- Permission link -->
                    <li @if(request()->segment(2) == 'SchedulePayment-permission') class="active" @endif>
                        <a href="{{action([\Modules\SchedulePayment\Http\Controllers\SettingController::class, 'showSchedulePaymentPermissionForm'])}}">
                            @lang("schedulepayment::lang.setting")
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</section>