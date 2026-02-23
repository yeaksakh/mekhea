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
                {{-- <a class="navbar-brand" href="{{action([\Modules\ExpenseRequest\Http\Controllers\ExpenseRequestController::class, 'dashboard'])}}">
                    <i class="fa fa-dollar-sign"  style="width: 30px; height: auto; color:#000000;" aria-hidden="true"></i>
                    @lang("expenserequest::lang.dashboard")
                </a> --}}
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <!-- Dashboard link -->
                    {{-- <li @if(request()->segment(2) == 'ExpenseRequest') class="active" @endif>
                        <a href="{{action([\Modules\ExpenseRequest\Http\Controllers\ExpenseRequestController::class, 'index'])}}">
                            @lang("expenserequest::lang.expenserequest")
                        </a>
                    </li> --}}

                    <!-- Categories link -->
                    {{-- <li @if(request()->segment(2) == 'ExpenseRequest-categories') class="active" @endif>
                        <a href="{{action([\Modules\ExpenseRequest\Http\Controllers\ExpenseRequestController::class, 'getCategories'])}}">
                            @lang("expenserequest::lang.ExpenseRequest_category")
                        </a>
                    </li> --}}

                    <!-- Permission link -->
                    {{-- <li @if(request()->segment(2) == 'ExpenseRequest-permission') class="active" @endif>
                        <a href="{{action([\Modules\ExpenseRequest\Http\Controllers\SettingController::class, 'showExpenseRequestPermissionForm'])}}">
                            @lang("expenserequest::lang.setting")
                        </a>
                    </li> --}}

                    <!-- Expense Request link -->
                    <li @if(request()->segment(2) == 'ExpenseRequest-expense_request') class="active" @endif>
                        <a href="{{action([\Modules\ExpenseRequest\Http\Controllers\ExpenseController::class, 'indexExpenseRequest'])}}">
                            @lang("expenserequest::lang.expense_request")
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</section>