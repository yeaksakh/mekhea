@php
    $business_id = session()->get('user.business_id');
    $module_util = new \App\Utils\ModuleUtil();
    $commonUtil = new \App\Utils\Util();
    
    $is_crm_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'crm_module');
    $is_admin = $commonUtil->is_admin(auth()->user(), $business_id);
    
    // Check CRM settings for order request if needed
    $business = \App\Business::find($business_id);
    $crm_settings = !empty($business->crm_settings) ? json_decode($business->crm_settings, true) : [];
@endphp


    <!-- Main CRM Button -->
    <div class="home-grid-tile" data-key="crm" onclick="openDesktopPopup('crmPopup')">
        <a href="#" title="{{ __('crm::lang.crm') }}">
            <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/modules/crm.svg') }}" 
                 class="home-icon" 
                 alt="{{ __('crm::lang.crm') }}">
            <span class="home-label">{{ __('crm::lang.crm') }}</span>
        </a>
    </div>

    <!-- CRM Popup Container -->
    <div id="crmPopup" class="dektop-popup-container" style="display: none;">
        <div class="dektop-popup-content">
            <div class="popup-header">
                 <h4 style="color: white; margin: 0;">@lang('crm::lang.crm')</h4>
                <span class="close-popup" onclick="closeDesktopPopup('crmPopup')">&times;</span>
            </div>
            <div class="popup-grid">
             
                 @if(auth()->user()->can('crm.access_all_leads') || auth()->user()->can('crm.access_own_leads'))
        <div class="home-grid-tile" data-key="leads">
            <a href="{{action([\Modules\Crm\Http\Controllers\LeadController::class, 'index']). '?lead_view=list_view'}}" 
               title="@lang('crm::lang.leads')">
                <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/contact/leads.svg') }}" 
                     class="home-icon" 
                     alt="@lang('crm::lang.leads')">
                <span class="home-label">@lang('crm::lang.leads')</span>
            </a>
        </div>
        @endif

        @if(auth()->user()->can('crm.access_all_schedule') || auth()->user()->can('crm.access_own_schedule'))
        <div class="home-grid-tile" data-key="follow-ups">
            <a href="{{action([\Modules\Crm\Http\Controllers\ScheduleController::class, 'index'])}}" 
               title="@lang('crm::lang.follow_ups')">
                <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/contact/follow_ups.svg') }}" 
                     class="home-icon" 
                     alt="@lang('crm::lang.follow_ups')">
                <span class="home-label">@lang('crm::lang.follow_ups')</span>
            </a>
        </div>
        @endif

        @if(auth()->user()->can('crm.access_all_campaigns') || auth()->user()->can('crm.access_own_campaigns'))
        <div class="home-grid-tile" data-key="campaigns">
            <a href="{{action([\Modules\Crm\Http\Controllers\CampaignController::class, 'index'])}}" 
               title="@lang('crm::lang.campaigns')">
                <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/contact/campaigns.svg') }}" 
                     class="home-icon" 
                     alt="@lang('crm::lang.campaigns')">
                <span class="home-label">@lang('crm::lang.campaigns')</span>
            </a>
        </div>
        @endif

        @can('crm.access_contact_login')
        <div class="home-grid-tile" data-key="contacts_login">
            <a href="{{action([\Modules\Crm\Http\Controllers\ContactLoginController::class, 'allContactsLoginList'])}}" 
               title="@lang('crm::lang.contacts_login')">
                <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/contact/contacts_login.svg') }}" 
                     class="home-icon" 
                     alt="@lang('crm::lang.contacts_login')">
                <span class="home-label">@lang('crm::lang.contacts_login')</span>
            </a>
        </div>
        @endcan

        @if((auth()->user()->can('crm.view_all_call_log') || auth()->user()->can('crm.view_own_call_log')) && config('constants.enable_crm_call_log'))
        <div class="home-grid-tile" data-key="call-log">
            <a href="{{action([\Modules\Crm\Http\Controllers\CallLogController::class, 'index'])}}" 
               title="@lang('crm::lang.call_log')">
                <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/contact/call_log.svg') }}" 
                     class="home-icon" 
                     alt="@lang('crm::lang.call_log')">
                <span class="home-label">@lang('crm::lang.call_log')</span>
            </a>
        </div>
        @endif

        @can('crm.view_reports')
        <div class="home-grid-tile" data-key="reports">
            <a href="{{action([\Modules\Crm\Http\Controllers\ReportController::class, 'index'])}}" 
               title="@lang('report.reports')">
                <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/report/report.svg') }}" 
                     class="home-icon" 
                     alt="@lang('report.reports')">
                <span class="home-label">@lang('report.reports')</span>
            </a>
        </div>
        @endcan

        <div class="home-grid-tile" data-key="proposal-template">
            <a href="{{action([\Modules\Crm\Http\Controllers\ProposalTemplateController::class, 'index'])}}" 
               title="@lang('crm::lang.proposal_template')">
                <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/contact/proposal_template.svg') }}" 
                     class="home-icon" 
                     alt="@lang('crm::lang.proposal_template')">
                <span class="home-label">@lang('crm::lang.proposal_template')</span>
            </a>
        </div>

        <div class="home-grid-tile" data-key="proposals">
            <a href="{{action([\Modules\Crm\Http\Controllers\ProposalController::class, 'index'])}}" 
               title="@lang('crm::lang.proposals')">
                <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/contact/proposals.svg') }}" 
                     class="home-icon" 
                     alt="@lang('crm::lang.proposals')">
                <span class="home-label">@lang('crm::lang.proposals')</span>
            </a>
        </div>

        @if(auth()->user()->can('crm.access_b2b_marketplace') && config('constants.enable_b2b_marketplace'))
        <div class="home-grid-tile" data-key="b2b-marketplace">
            <a href="{{action([\Modules\Crm\Http\Controllers\CrmMarketplaceController::class, 'index'])}}" 
               title="@lang('crm::lang.b2b_marketplace')">
                <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/contact/b2b_marketplace.svg') }}" 
                     class="home-icon" 
                     alt="@lang('crm::lang.b2b_marketplace')">
                <span class="home-label">@lang('crm::lang.b2b_marketplace')</span>
            </a>
        </div>
        @endif

        @can('crm.access_sources')
        <div class="home-grid-tile" data-key="sources">
            <a href="{{action([\App\Http\Controllers\TaxonomyController::class, 'index']) . '?type=source'}}" 
               title="@lang('crm::lang.sources')">
                <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/contact/sources.svg') }}" 
                     class="home-icon" 
                     alt="@lang('crm::lang.sources')">
                <span class="home-label">@lang('crm::lang.sources')</span>
            </a>
        </div>
        @endcan

        @can('crm.access_life_stage')
        <div class="home-grid-tile" data-key="life_stage">
            <a href="{{action([\App\Http\Controllers\TaxonomyController::class, 'index']) . '?type=life_stage'}}" 
               title="@lang('crm::lang.life_stage')">
                <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/contact/life_stage.svg') }}" 
                     class="home-icon" 
                     alt="@lang('crm::lang.life_stage')">
                <span class="home-label">@lang('crm::lang.life_stage')</span>
            </a>
        </div>

        <div class="home-grid-tile" data-key="followup_category">
            <a href="{{action([\App\Http\Controllers\TaxonomyController::class, 'index']) . '?type=followup_category'}}" 
               title="@lang('crm::lang.followup_category')">
                <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/contact/followup_category.svg') }}" 
                     class="home-icon" 
                     alt="@lang('crm::lang.followup_category')">
                <span class="home-label">@lang('crm::lang.followup_category')</span>
            </a>
        </div>
        @endcan

        <div class="home-grid-tile" data-key="crm_settings">
            <a href="{{action([\Modules\Crm\Http\Controllers\CrmSettingsController::class, 'index'])}}" 
               title="@lang('business.settings')">
                <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/setting/setting.svg') }}" 
                     class="home-icon" 
                     alt="@lang('business.settings')">
                <span class="home-label">@lang('business.settings')</span>
            </a>
        </div>
        
            </div>
        </div>
    </div>


    <script>
    function openDesktopPopup(popupId) {
        document.getElementById(popupId).style.display = 'flex';
        // Scroll to top when opening
        document.getElementById(popupId).querySelector('.dektop-popup-content').scrollTop = 0;
    }
    
    function closeDesktopPopup(popupId) {
        document.getElementById(popupId).style.display = 'none';
    }
    
    // Close popup when clicking outside the content
    window.addEventListener('click', function(event) {
        const popups = document.querySelectorAll('.dektop-popup-container');
        popups.forEach(popup => {
            if (event.target === popup) {
                popup.style.display = 'none';
            }
        });
    });
</script>
