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
                {{-- <a class="navbar-brand" href="{{action([\Modules\EmployeeCardB1\Http\Controllers\EmployeeCardB1Controller::class, 'dashboard'])}}">
                    <i class="fa fa-address-card"  style="width: 30px; height: auto; color:#000000;" aria-hidden="true"></i>
                    @lang("employeecardb1::lang.dashboard")
                </a> --}}
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <!-- Dashboard link -->
                    {{-- <li @if(request()->segment(2) == 'EmployeeCardB1') class="active" @endif>
                        <a href="{{action([\Modules\EmployeeCardB1\Http\Controllers\EmployeeCardB1Controller::class, 'index'])}}">
                            @lang("employeecardb1::lang.employeecardb1")
                        </a>
                    </li> --}}

                    <!-- Categories link -->
                    {{-- <li @if(request()->segment(2) == 'EmployeeCardB1-categories') class="active" @endif>
                        <a href="{{action([\Modules\EmployeeCardB1\Http\Controllers\EmployeeCardB1Controller::class, 'getCategories'])}}">
                            @lang("employeecardb1::lang.EmployeeCardB1_category")
                        </a>
                    </li> --}}

                    <!-- Permission link -->
                    {{-- <li @if(request()->segment(2) == 'EmployeeCardB1-permission') class="active" @endif>
                        <a href="{{action([\Modules\EmployeeCardB1\Http\Controllers\SettingController::class, 'showEmployeeCardB1PermissionForm'])}}">
                            @lang("employeecardb1::lang.setting")
                        </a>
                    </li> --}}

                    <!-- Employees link -->
                    <li @if(request()->segment(2) == 'EmployeeCardB1-users') class="active" @endif>
                        <a href="{{action([\Modules\EmployeeCardB1\Http\Controllers\ManageUserController::class, 'index'])}}">
                            @lang("employeecardb1::lang.users")
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</section>