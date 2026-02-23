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
                <!-- <a class="navbar-brand" href="#">
                    <i><img src="" alt="Logo" style="width: 30px; height: auto;"></i>
                    @lang("visa::lang.dashboard")
                </a> -->
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li @if(request()->segment(2) == 'indicator') class="active" @endif>
                        <a href="{{action([\Modules\Visa\Http\Controllers\IndicatorController::class, 'index'])}}">
                            @lang("visa::lang.indicator")
                        </a>
                    </li>

                    <li @if(request()->segment(2) == 'appraisal-list') class="active" @endif>
                        <a href="{{action([\Modules\Visa\Http\Controllers\IndicatorController::class, 'appraisal_list'])}}">
                            @lang("visa::lang.appraisal_list")
                        </a>
                    </li>

                    <li @if(request()->segment(2) == 'report') class="active" @endif>
                        <a href="{{action([\Modules\Visa\Http\Controllers\IndicatorController::class, 'appraisal_report'])}}">
                            @lang("visa::lang.report")
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</section>