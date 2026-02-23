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
                {{-- <a class="navbar-brand" href="{{action([\Modules\ExpenseAutoFill\Http\Controllers\ExpenseAutoFillController::class, 'dashboard'])}}">
                    <i class="fa "  style="width: 30px; height: auto; color:#e11414;" aria-hidden="true"></i>
                    @lang("expenseautofill::lang.dashboard")
                </a> --}}
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <!-- Dashboard link -->
                    <li @if(request()->segment(2) == 'ExpenseAutoFill') class="active" @endif>
                        <a href="{{action([\Modules\ExpenseAutoFill\Http\Controllers\ExpenseAutoFillController::class, 'index'])}}">
                            @lang("expenseautofill::lang.expenseautofill")
                        </a>
                    </li>

                    <!-- Categories link -->
                    {{-- <li @if(request()->segment(2) == 'ExpenseAutoFill-categories') class="active" @endif>
                        <a href="{{action([\Modules\ExpenseAutoFill\Http\Controllers\ExpenseAutoFillController::class, 'getCategories'])}}">
                            @lang("expenseautofill::lang.ExpenseAutoFill_category")
                        </a>
                    </li> --}}

                    <!-- Permission link -->
                    <li @if(request()->segment(2) == 'ExpenseAutoFill-permission') class="active" @endif>
                        <a href="{{action([\Modules\ExpenseAutoFill\Http\Controllers\SettingController::class, 'showExpenseAutoFillPermissionForm'])}}">
                            @lang("expenseautofill::lang.setting")
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</section>