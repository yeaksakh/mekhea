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
                    @lang("customercardb1::visa.dashboard")
                </a> -->
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li @if(request()->segment(2) == 'indicator') class="active" @endif>
                        <a href="{{action([\Modules\CustomerCardB1\Http\Controllers\IndicatorController::class, 'index'])}}">
                            @lang("customercardb1::visa.indicator")
                        </a>
                    </li>

                    <li @if(request()->segment(2) == 'appraisal-list') class="active" @endif>
                        <a href="{{action([\Modules\CustomerCardB1\Http\Controllers\IndicatorController::class, 'appraisal_list'])}}">
                            @lang("customercardb1::visa.appraisal_list")
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>
</section>