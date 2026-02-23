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
                {{-- <a class="navbar-brand" href="{{action([\Modules\CustomerCardB1\Http\Controllers\CustomerCardB1Controller::class, 'dashboard'])}}">
                    <i class="fa fa-address-card"  style="width: 30px; height: auto; color:#000000;" aria-hidden="true"></i>
                    @lang("customercardb1::lang.dashboard")
                </a> --}}
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <!-- Dashboard link -->
                    {{-- <li @if(request()->segment(2) == 'CustomerCardB1') class="active" @endif>
                        <a href="{{action([\Modules\CustomerCardB1\Http\Controllers\CustomerCardB1Controller::class, 'index'])}}">
                            @lang("customercardb1::lang.customercardb1")
                        </a>
                    </li> --}}

                    <!-- Categories link -->
                    {{-- <li @if(request()->segment(2) == 'CustomerCardB1-categories') class="active" @endif>
                        <a href="{{action([\Modules\CustomerCardB1\Http\Controllers\CustomerCardB1Controller::class, 'getCategories'])}}">
                            @lang("customercardb1::lang.CustomerCardB1_category")
                        </a>
                    </li> --}}

                    <!-- Permission link -->
                    {{-- <li @if(request()->segment(2) == 'CustomerCardB1-permission') class="active" @endif>
                        <a href="{{action([\Modules\CustomerCardB1\Http\Controllers\SettingController::class, 'showCustomerCardB1PermissionForm'])}}">
                            @lang("customercardb1::lang.setting")
                        </a>
                    </li> --}}

                    <!-- Customers link -->
                    <li @if(request()->segment(2) == 'CustomerCardB1-customers') class="active" @endif>
                        <a href="{{action([\Modules\CustomerCardB1\Http\Controllers\ContactController::class, 'index'], ['type' => 'customer'])}}">
                            @lang("customercardb1::lang.customers")
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</section>