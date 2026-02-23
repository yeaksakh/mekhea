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
        .navbar-nav {
	    padding-top: 10px;
	}
    </style>
	    
         
    @if($type == 'customer')
    <nav class="navbar-default tw-transition-all tw-duration-5000 tw-shrink-0 tw-rounded-2xl tw-m-2 tw-border-2 !tw-bg-white">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false" style="margin-top: 3px; margin-right: 3px;">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{action([\Modules\Crm\Http\Controllers\CrmDashboardController::class, 'index'])}}"><i class="fas fa fa-users"></i> {{__('crm::lang.crm')}}</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                  <!-- Leads -->
        @if (auth()->user()->can('crm.access_all_leads') || auth()->user()->can('crm.access_own_leads'))
            <a href="{{ action([\Modules\Crm\Http\Controllers\LeadController::class, 'index']) }}?lead_view=list_view" 
               class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-primary tw-m-0.5">
                <i class="fas fa-user"></i> @lang("crm::lang.leads")
            </a>
        @endif

        <!-- Customers -->
        @if (auth()->user()->can('customer.view') || auth()->user()->can('customer.view_own'))
            <a href="{{ action([\App\Http\Controllers\ContactController::class, 'index'], ['type' => 'customer']) }}" 
               class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-info tw-m-0.5">
                <i class="fas fa-user"></i> @lang("report.customer")
            </a>
        @endif

        <!-- Customer Groups -->
        @if (auth()->user()->can('customer.view') || auth()->user()->can('customer.view_own'))
            <a href="{{ action([\App\Http\Controllers\CustomerGroupController::class, 'index']) }}" 
               class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-warning tw-m-0.5">
                <i class="fas fa-users"></i> @lang("lang_v1.customer_groups")
            </a>
        @endif

        <!-- Contact Login -->
        @if (auth()->user()->can('crm.access_contact_login'))
            <a href="{{ action([\Modules\Crm\Http\Controllers\ContactLoginController::class, 'allContactsLoginList']) }}" 
               class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-primary tw-m-0.5">
                <i class="fas fa-heartbeat"></i> @lang("crm::lang.contacts_login")
            </a>
        @endif

        <!-- Follow-ups -->
        @if (auth()->user()->can('crm.access_all_schedule') || auth()->user()->can('crm.access_own_schedule'))
            <a href="{{ action([\Modules\Crm\Http\Controllers\ScheduleController::class, 'index']) }}" 
               class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-primary tw-m-0.5">
                <i class="fas fa-calendar-check"></i> @lang("crm::lang.follow_ups")
            </a>
        @endif

        <!-- Campaigns -->
        @if (auth()->user()->can('crm.access_all_campaigns') || auth()->user()->can('crm.access_own_campaigns'))
            <a href="{{ action([\Modules\Crm\Http\Controllers\CampaignController::class, 'index']) }}" 
               class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-success tw-m-0.5">
                <i class="fas fa-bullhorn"></i> @lang("crm::lang.campaigns")
            </a>
        @endif

        <!-- Call Log -->
        @if ((auth()->user()->can('crm.view_all_call_log') || auth()->user()->can('crm.view_own_call_log')) && config('constants.enable_crm_call_log'))
            <a href="{{ action([\Modules\Crm\Http\Controllers\CallLogController::class, 'index']) }}" 
               class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-warning tw-m-0.5">
                <i class="fas fa-phone"></i> @lang("crm::lang.call_log")
            </a>
        @endif

        <!-- Reports -->
        @if (auth()->user()->can('crm.view_reports'))
            <a href="{{ action([\Modules\Crm\Http\Controllers\ReportController::class, 'index']) }}" 
               class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-purple tw-m-0.5">
                <i class="fas fa-chart-bar"></i> @lang("report.reports")
            </a>
        @endif

        <!-- Proposal Template -->
        <a href="{{ action([\Modules\Crm\Http\Controllers\ProposalTemplateController::class, 'index']) }}" 
           class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-primary tw-m-0.5">
            <i class="fas fa-file-alt"></i> @lang("crm::lang.proposal_template")
        </a>

        <!-- Proposals -->
        <a href="{{ action([\Modules\Crm\Http\Controllers\ProposalController::class, 'index']) }}" 
           class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-danger tw-m-0.5">
            <i class="fas fa-paperclip"></i> @lang("crm::lang.proposals")
        </a>

        <!-- B2B Marketplace -->
        @if (auth()->user()->can('crm.access_b2b_marketplace') && config('constants.enable_b2b_marketplace'))
            <a href="{{ action([\Modules\Crm\Http\Controllers\CrmMarketplaceController::class, 'index']) }}" 
               class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-success tw-m-0.5">
                <i class="fas fa-shopping-cart"></i> @lang("crm::lang.b2b_marketplace")
            </a>
        @endif

        <!-- Sources -->
        @if (auth()->user()->can('crm.access_sources'))
            <a href="{{ action([\App\Http\Controllers\TaxonomyController::class, 'index']) }}?type=source" 
               class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-warning tw-m-0.5">
                <i class="fas fa-plug"></i> @lang("crm::lang.sources")
            </a>
        @endif

        <!-- Life Stage -->
        @if (auth()->user()->can('crm.access_life_stage'))
            <a href="{{ action([\App\Http\Controllers\TaxonomyController::class, 'index']) }}?type=life_stage" 
               class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-primary tw-m-0.5">
                <i class="fas fa-heartbeat"></i> @lang("crm::lang.life_stage")
            </a>
        @endif

        <!-- Followup Category -->
        <a href="{{ action([\App\Http\Controllers\TaxonomyController::class, 'index']) }}?type=followup_category" 
           class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-danger tw-m-0.5">
            <i class="fas fa-tags"></i> @lang("crm::lang.followup_category")
        </a>
                </ul>

            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</section>

  @endif