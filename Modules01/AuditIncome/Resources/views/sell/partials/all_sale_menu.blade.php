<section class="no-print">
    <style type="text/css">
        #contacts_login_dropdown::after {
            display: inline-block;
            width: 0;
            height: 0;
            margin-left: 0.255em;
            vertical-align: 0.255em;
            content: "";
            border-top: 0.3em solid;
            border-right: 0.3em solid transparent;
            border-bottom: 0;
            border-left: 0.3em solid transparent;
        }
    </style>
    <nav class="navbar-default tw-mb-4  tw-transition-all tw-duration-5000 tw-shrink-0 tw-ring-1 tw-ring-gray-200 tw-rounded-xl  !tw-bg-white tw-w-full">

        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false" style="margin-top: 3px; margin-right: 3px;">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{action([\Modules\Crm\Http\Controllers\CrmDashboardController::class, 'index'])}}">
                <i class="fas fa fa-users"></i> 
                {{__('crm::lang.crm')}}
                </a>
            </div>

            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    @php
                        $is_admin = auth()->user()->hasRole('Admin');
                        $enabled_modules = []; // Add your enabled modules array here
                    @endphp


                    <!-- Sales Menu Items -->
                    @if ($is_admin || auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping']))
                        <li @if(request()->segment(1) == 'sells' && request()->segment(2) == null) class="active" @endif>
                            <a href="{{action([\App\Http\Controllers\SellController::class, 'index'])}}">{{__('lang_v1.all_sales')}}</a>
                        </li>
                    @endif

                    @if (auth()->user()->can('sell.create') && in_array('pos_sale', $enabled_modules) && auth()->user()->can('sell.view'))
                        <li @if(request()->segment(1) == 'pos' && request()->segment(2) == null) class="active" @endif>
                            <a href="{{action([\App\Http\Controllers\SellPosController::class, 'index'])}}">{{__('sale.list_pos')}}</a>
                        </li>
                    @endif

                    @if (in_array('add_sale', $enabled_modules) && ($is_admin || auth()->user()->hasAnyPermission(['draft.view_all', 'draft.view_own'])))
                        <li @if(request()->segment(1) == 'sells' && request()->segment(2) == 'drafts') class="active" @endif>
                            <a href="{{action([\App\Http\Controllers\SellController::class, 'getDrafts'])}}">{{__('lang_v1.list_drafts')}}</a>
                        </li>
                    @endif

                    @if (in_array('add_sale', $enabled_modules) && ($is_admin || auth()->user()->hasAnyPermission(['quotation.view_all', 'quotation.view_own'])))
                        <li @if(request()->segment(1) == 'sells' && request()->segment(2) == 'quotations') class="active" @endif>
                            <a href="{{action([\App\Http\Controllers\SellController::class, 'getQuotations'])}}">{{__('lang_v1.list_quotations')}}</a>
                        </li>
                    @endif

                    @if (in_array('add_sale', $enabled_modules) && ($is_admin || auth()->user()->hasAnyPermission(['consignment.view_all', 'consignment.view_own'])))
                        <li @if(request()->segment(1) == 'sells' && request()->segment(2) == 'consignments') class="active" @endif>
                            <a href="{{action([\App\Http\Controllers\SellController::class, 'getConsignments'])}}">{{__('sale.list_consignments')}}</a>
                        </li>
                    @endif

                    @if (auth()->user()->can('access_sell_return') || auth()->user()->can('access_own_sell_return'))
                        <li @if(request()->segment(1) == 'sell-return' && request()->segment(2) == null) class="active" @endif>
                            <a href="{{action([\App\Http\Controllers\SellReturnController::class, 'index'])}}">{{__('lang_v1.list_sell_return')}}</a>
                        </li>
                    @endif

                    @if ($is_admin || auth()->user()->hasAnyPermission(['access_shipping', 'access_own_shipping', 'access_commission_agent_shipping']))
                        <li @if(request()->segment(1) == 'shipments') class="active" @endif>
                            <a href="{{action([\App\Http\Controllers\SellController::class, 'shipments'])}}">{{__('lang_v1.shipments')}}</a>
                        </li>
                    @endif

                    @if (auth()->user()->can('discount.access'))
                        <li @if(request()->segment(1) == 'discount') class="active" @endif>
                            <a href="{{action([\App\Http\Controllers\DiscountController::class, 'index'])}}">{{__('lang_v1.discounts')}}</a>
                        </li>
                    @endif

                    @if (in_array('subscription', $enabled_modules) && auth()->user()->can('direct_sell.access'))
                        <li @if(request()->segment(1) == 'subscriptions') class="active" @endif>
                            <a href="{{action([\App\Http\Controllers\SellPosController::class, 'listSubscriptions'])}}">{{__('lang_v1.subscriptions')}}</a>
                        </li>
                    @endif

                    <!-- Customers -->
                    @if (auth()->user()->can('customer.view') || auth()->user()->can('customer.view_own'))
                        <li @if(request()->input('type') == 'customer') class="active" @endif>
                            <a href="{{ action([\App\Http\Controllers\ContactController::class, 'index'], ['type' => 'customer']) }}">{{__('report.customer')}}</a>
                        </li>
                    @endif

                    <!-- Exchange Rate -->
                    <li @if(request()->segment(1) == 'exchange-rate') class="active" @endif>
                        <a href="{{action([\App\Http\Controllers\ExchangeRateController::class, 'index'])}}">{{__('exchangerate.exchangerate')}}</a>
                    </li>

                    <!-- Existing CRM Menu Items -->
                    @if(auth()->user()->can('crm.access_all_leads') || auth()->user()->can('crm.access_own_leads'))
                        <li @if(request()->segment(2) == 'leads') class="active" @endif>
                            <a href="{{action([\Modules\Crm\Http\Controllers\LeadController::class, 'index']). '?lead_view=list_view'}}">@lang('crm::lang.leads')</a>
                        </li>
                    @endif

                    @if(auth()->user()->can('crm.access_all_schedule') || auth()->user()->can('crm.access_own_schedule'))
                        <li @if(request()->segment(2) == 'follow-ups') class="active" @endif>
                            <a href="{{action([\Modules\Crm\Http\Controllers\ScheduleController::class, 'index'])}}">@lang('crm::lang.follow_ups')</a>
                        </li>
                    @endif

                    <li @if(request()->segment(2) == 'settings') class="active" @endif>
                        <a href="{{action([\Modules\Crm\Http\Controllers\OrderRequestController::class, 'listOrderRequests'])}}">
                        
                            @lang('crm::lang.enable_order_online')
                        </a>
                    </li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</section>