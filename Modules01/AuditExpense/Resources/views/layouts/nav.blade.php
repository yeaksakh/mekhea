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
                {{-- <a class="navbar-brand" href="{{action([\Modules\AuditExpense\Http\Controllers\AuditExpenseController::class, 'dashboard'])}}">
                    <i class="fa fa-donate"  style="width: 30px; height: auto; color:#000000;" aria-hidden="true"></i>
                    @lang("auditexpense::lang.dashboard")
                </a> --}}
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <!-- Dashboard link -->
                    {{-- <li @if(request()->segment(2) == 'AuditExpense') class="active" @endif>
                        <a href="{{action([\Modules\AuditExpense\Http\Controllers\AuditExpenseController::class, 'index'])}}">
                            @lang("auditexpense::lang.auditexpense")
                        </a>
                    </li> --}}

                    <!-- Dashboard link -->
                    <li @if(request()->segment(2) == 'AuditExpense-expenses') class="active" @endif>
                        <a href="{{action([\Modules\AuditExpense\Http\Controllers\ExpenseController::class, 'index'])}}">
                            @lang("auditexpense::lang.auditexpense")
                        </a>
                    </li>

                    <!-- Categories link -->
                    {{-- <li @if(request()->segment(2) == 'AuditExpense-categories') class="active" @endif>
                        <a href="{{action([\Modules\AuditExpense\Http\Controllers\AuditExpenseController::class, 'getCategories'])}}">
                            @lang("auditexpense::lang.AuditExpense_category")
                        </a>
                    </li> --}}

                    <!-- Permission link -->
                    {{-- <li @if(request()->segment(2) == 'AuditExpense-permission') class="active" @endif>
                        <a href="{{action([\Modules\AuditExpense\Http\Controllers\SettingController::class, 'showAuditExpensePermissionForm'])}}">
                            @lang("auditexpense::lang.setting")
                        </a>
                    </li> --}}
                </ul>
            </div>
        </div>
    </nav>
</section>