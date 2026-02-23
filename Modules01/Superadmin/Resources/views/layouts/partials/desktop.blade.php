   @if(auth()->user()->can('superadmin') && session()->has('business'))   <!-- Main report Button -->
    <div class="home-grid-tile" data-key="franchisor" onclick="openDesktopPopup('franchisorPopup')">
        <a href="#" title="{{ __('report::lang.report') }}">
            <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/modules/franchisor.svg') }}" class="home-icon" alt="">
            <span class="home-label">{{ __('superadmin::lang.superadmin') }}</span>
        </a>
    </div>

    <!-- report Popup Container -->
    <div id="franchisorPopup" class="dektop-popup-container" style="display: none;">
        <div class="dektop-popup-content">
            <div class="popup-header">
                 <h4 style="color: white; margin: 0;">@lang('superadmin::lang.superadmin')</h4>
                <span class="close-popup" onclick="closeDesktopPopup('franchisorPopup')">&times;</span>
            </div>
            <div class="popup-grid">
              
            
            
           
        <div class="home-grid-tile @if(request()->segment(1) == 'superadmin' && request()->segment(2) == 'business') active @endif" data-key="all_business">
    <a href="{{action([Modules\Superadmin\Http\Controllers\BusinessController::class, 'index'])}}" title="@lang('superadmin::lang.all_business')">
        <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/franchisor/business.svg') }}" class="home-icon" alt="">
        <span class="home-label">@lang('superadmin::lang.all_business')</span>
    </a>
</div>

<div class="home-grid-tile @if(request()->segment(1) == 'superadmin' && request()->segment(2) == 'superadmin-subscription') active @endif" data-key="subscription">
    <a href="{{action([\Modules\Superadmin\Http\Controllers\SuperadminSubscriptionsController::class, 'index'])}}" title="@lang('superadmin::lang.subscription')">
        <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/franchisor/subscription.svg') }}" class="home-icon" alt="">
        <span class="home-label">@lang('superadmin::lang.subscription')</span>
    </a>
</div>

<div class="home-grid-tile @if(request()->segment(1) == 'superadmin' && request()->segment(2) == 'packages') active @endif" data-key="packages">
    <a href="{{action([\Modules\Superadmin\Http\Controllers\PackagesController::class, 'index'])}}" title="@lang('superadmin::lang.subscription_packages')">
        <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/franchisor/package.svg') }}" class="home-icon" alt="">
        <span class="home-label">@lang('superadmin::lang.subscription_packages')</span>
    </a>
</div>

<div class="home-grid-tile @if(request()->segment(1) == 'superadmin' && request()->segment(2) == 'coupons') active @endif" data-key="coupons">
    <a href="{{action([\Modules\Superadmin\Http\Controllers\CouponController::class, 'index'])}}" title="@lang('superadmin::lang.all_coupons')">
        <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/franchisor/coupon.svg') }}" class="home-icon" alt="">
        <span class="home-label">@lang('superadmin::lang.all_coupons')</span>
    </a>
</div>

<div class="home-grid-tile @if(request()->segment(1) == 'superadmin' && request()->segment(2) == 'settings') active @endif" data-key="settings">
    <a href="{{action([\Modules\Superadmin\Http\Controllers\SuperadminSettingsController::class, 'edit'])}}" title="@lang('superadmin::lang.super_admin_settings')">
        <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/setting/setting.svg') }}" class="home-icon" alt="">
        <span class="home-label">@lang('superadmin::lang.super_admin_settings')</span>
    </a>
</div>

<div class="home-grid-tile @if(request()->segment(1) == 'superadmin' && request()->segment(2) == 'communicator') active @endif" data-key="communicator">
    <a href="{{action([\Modules\Superadmin\Http\Controllers\CommunicatorController::class, 'index'])}}" title="@lang('superadmin::lang.communicator')">
        <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/franchisor/communicator.svg') }}" class="home-icon" alt="">
        <span class="home-label">@lang('superadmin::lang.communicator')</span>
    </a>
</div>


       </div>
        </div>
    </div>

  @endif  