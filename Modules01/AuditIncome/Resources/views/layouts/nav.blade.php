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
                {{-- <a class="navbar-brand" href="{{action([\Modules\AuditIncome\Http\Controllers\AuditIncomeController::class, 'dashboard'])}}">
                    <i class="fa fa-hand-holding-usd"  style="width: 30px; height: auto; color:#000000;" aria-hidden="true"></i>
                    @lang("auditincome::lang.dashboard")
                </a> --}}
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <!-- Dashboard link -->
                    {{-- <li @if(request()->segment(2) == 'AuditIncome') class="active" @endif>
                        <a href="{{action([\Modules\AuditIncome\Http\Controllers\AuditIncomeController::class, 'index'])}}">
                            @lang("auditincome::lang.auditincome")
                        </a>
                    </li>
                     --}}
                    <!-- Dashboard link -->
                    <li @if(request()->segment(2) == 'AuditIncome-sells') class="active" @endif>
                        <a href="{{action([\Modules\AuditIncome\Http\Controllers\SellController::class, 'index'])}}">
                            @lang("auditincome::lang.auditincome")
                        </a>
                    </li>

                    <!-- Categories link -->
                    {{-- <li @if(request()->segment(2) == 'AuditIncome-categories') class="active" @endif>
                        <a href="{{action([\Modules\AuditIncome\Http\Controllers\AuditIncomeController::class, 'getCategories'])}}">
                            @lang("auditincome::lang.AuditIncome_category")
                        </a>
                    </li> --}}

                    <!-- Permission link -->
                    {{-- <li @if(request()->segment(2) == 'AuditIncome-permission') class="active" @endif>
                        <a href="{{action([\Modules\AuditIncome\Http\Controllers\SettingController::class, 'showAuditIncomePermissionForm'])}}">
                            @lang("auditincome::lang.setting")
                        </a>
                    </li> --}}
                </ul>
            </div>
        </div>
    </nav>
</section>