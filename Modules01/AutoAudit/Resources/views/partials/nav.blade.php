<nav
    class="navbar-default tw-mb-4  tw-transition-all tw-duration-5000 tw-shrink-0 tw-ring-1 tw-ring-gray-200 tw-rounded-xl  !tw-bg-white tw-w-full">

    <div class="container-fluid">
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                @php
                    $is_admin = auth()->user()->hasRole('Admin');
                    $enabled_modules = []; // Add your enabled modules array here
                @endphp


                <!-- Sales Menu Items -->
                @if (
                    $is_admin ||
                        auth()->user()->hasAnyPermission([
                                'sell.view',
                                'sell.create',
                                'direct_sell.access',
                                'direct_sell.view',
                                'view_own_sell_only',
                                'view_commission_agent_sell',
                                'access_shipping',
                                'access_own_shipping',
                                'access_commission_agent_shipping',
                            ]))
                @endif

                <li @if (request()->segment(1) == 'sells' && request()->segment(2) == null) class="active" @endif>
                    <a
                        href="{{ action([\Modules\AutoAudit\Http\Controllers\AuditController::class, 'index']) }}">{{ 'Invoice Audit' }}</a>
                </li>


                <li @if (request()->segment(1) == 'sells' && request()->segment(2) == null) class="active" @endif>
                    <a
                        href="{{ action([\Modules\AutoAudit\Http\Controllers\AuditController::class, 'botAudit']) }}">{{ 'Bot Audited' }}</a>
                </li>

                <li @if (request()->segment(1) == 'sells' && request()->segment(2) == null) class="active" @endif>
                    <a
                        href="{{ action([\Modules\AutoAudit\Http\Controllers\AuditController::class, 'botNotAudit']) }}">{{ 'Not Audited' }}</a>
                </li>

                <li @if (request()->segment(1) == 'sells' && request()->segment(2) == null) class="active" @endif>
                    <a
                        href="{{ action([\Modules\AutoAudit\Http\Controllers\AuditController::class, 'getinvoices']) }}">{{ 'All Invoices' }}</a>
                </li>


            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>
