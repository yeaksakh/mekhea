

<!-- 
{{-- Document Tile --}}
<div class="home-grid-tile" data-key="document">
    <a href="{{ action([\Modules\Essentials\Http\Controllers\DocumentController::class, 'index']) }}" 
       title="{{ __('essentials::lang.document') }}">
        <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/modules/document.svg') }}" 
             class="home-icon" 
             alt="{{ __('essentials::lang.document') }}">
        <span class="home-label">{{ __('essentials::lang.document') }}</span>
    </a>
</div>

{{-- Reminders Tile --}}
<div class="home-grid-tile" data-key="reminders">
    <a href="{{ action([\Modules\Essentials\Http\Controllers\ReminderController::class, 'index']) }}" 
       title="{{ __('essentials::lang.reminders') }}">
        <img src="{{ asset('public/icons/' . (session('business.icon_pack') ?: 'v1') . '/modules/calendar.svg') }}" 
             class="home-icon" 
             alt="{{ __('essentials::lang.reminders') }}">
        <span class="home-label">{{ __('essentials::lang.reminders') }}</span>
    </a>
</div>
 -->



@php
    $business_id = session()->get('user.business_id');
    $module_util = new \App\Utils\ModuleUtil();
    $commonUtil = new \App\Utils\Util();
    
    $is_essentials_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'essentials_module');
    $is_admin = $commonUtil->is_admin(auth()->user(), $business_id);
@endphp

@if ($is_essentials_enabled)

    <!-- Main hrm Button -->
    <div class="home-grid-tile" data-key="hrm" onclick="openDesktopPopup('hrmPopup')">
        <a href="#" title="{{ __('essentials::lang.essentials') }}">
            <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/modules/hrm.svg') }}" 
                 class="home-icon" 
                 alt="{{ __('essentials::lang.hrm') }}">
            <span class="home-label">{{ __('essentials::lang.hrm') }}</span>
        </a>
    </div>
 @endif
    <!-- crm Popup Container -->
    <div id="hrmPopup" class="dektop-popup-container" style="display: none;">
        <div class="dektop-popup-content">
            <div class="popup-header">
                 <h4 style="color: white; margin: 0;">@lang('essentials::lang.essentials')</h4>
                <span class="close-popup" onclick="closeDesktopPopup('essentialsPopup')">&times;</span>
            </div>
            <div class="popup-grid">

             {{-- CRM Tile --}}
              
                    <div class="home-grid-tile" data-key="todos">
                        <a href="{{ action([\Modules\Essentials\Http\Controllers\DashboardController::class, 'hrmDashboard']) }}" 
               title="{{ __('essentials::lang.hrm') }}">
                <img src="{{ asset('public/icons/' . (session('business.icon_pack') ?: 'v1') . '/modules/hrm.svg') }}" 
                     class="home-icon" 
                     alt="{{ __('essentials::lang.hrm') }}">
                <span class="home-label">{{ __('essentials::lang.hrm') }}</span>
                        </a>
                    </div>
            


               {{-- Essentials Tiles --}}
@can('essentials.crud_leave_type')
    <div class="home-grid-tile" data-key="leave_type">
        <a href="{{ action([\Modules\Essentials\Http\Controllers\EssentialsLeaveTypeController::class, 'index']) }}" 
           title="{{ __('essentials::lang.leave_type') }}">
            <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/user/leave_type.svg') }}" 
                 class="home-icon" 
                 alt="{{ __('essentials::lang.leave_type') }}">
            <span class="home-label">{{ __('essentials::lang.leave_type') }}</span>
        </a>
    </div>
@endcan

@if(auth()->user()->can('essentials.crud_all_leave') || auth()->user()->can('essentials.crud_own_leave'))
    <div class="home-grid-tile" data-key="leave">
        <a href="{{ action([\Modules\Essentials\Http\Controllers\EssentialsLeaveController::class, 'index']) }}" 
           title="{{ __('essentials::lang.leave') }}">
            <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/user/leave.svg') }}" 
                 class="home-icon" 
                 alt="{{ __('essentials::lang.leave') }}">
            <span class="home-label">{{ __('essentials::lang.leave') }}</span>
        </a>
    </div>
@endif

@if(auth()->user()->can('essentials.crud_all_attendance') || auth()->user()->can('essentials.view_own_attendance'))
    <div class="home-grid-tile" data-key="attendance">
        <a href="{{ action([\Modules\Essentials\Http\Controllers\AttendanceController::class, 'index']) }}" 
           title="{{ __('essentials::lang.attendance') }}">
            <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/user/attendance.svg') }}" 
                 class="home-icon" 
                 alt="{{ __('essentials::lang.attendance') }}">
            <span class="home-label">{{ __('essentials::lang.attendance') }}</span>
        </a>
    </div>
@endif

<div class="home-grid-tile" data-key="payroll">
    <a href="{{ action([\Modules\Essentials\Http\Controllers\PayrollController::class, 'index']) }}" 
       title="{{ __('essentials::lang.payroll') }}">
        <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/user/payroll.svg') }}" 
             class="home-icon" 
             alt="{{ __('essentials::lang.payroll') }}">
        <span class="home-label">{{ __('essentials::lang.payroll') }}</span>
    </a>
</div>

<div class="home-grid-tile" data-key="holiday">
    <a href="{{ action([\Modules\Essentials\Http\Controllers\EssentialsHolidayController::class, 'index']) }}" 
       title="{{ __('essentials::lang.holiday') }}">
        <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/user/holiday.svg') }}" 
             class="home-icon" 
             alt="{{ __('essentials::lang.holiday') }}">
        <span class="home-label">{{ __('essentials::lang.holiday') }}</span>
    </a>
</div>

@can('essentials.crud_department')
    <div class="home-grid-tile" data-key="department">
        <a href="{{ action([\App\Http\Controllers\TaxonomyController::class, 'index']) . '?type=hrm_department' }}" 
           title="{{ __('essentials::lang.departments') }}">
            <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/user/department.svg') }}" 
                 class="home-icon" 
                 alt="{{ __('essentials::lang.departments') }}">
            <span class="home-label">{{ __('essentials::lang.departments') }}</span>
        </a>
    </div>
@endcan

@can('essentials.crud_designation')
    <div class="home-grid-tile" data-key="designation">
        <a href="{{ action([\App\Http\Controllers\TaxonomyController::class, 'index']) . '?type=hrm_designation' }}" 
           title="{{ __('essentials::lang.designations') }}">
            <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/user/designation.svg') }}" 
                 class="home-icon" 
                 alt="{{ __('essentials::lang.designations') }}">
            <span class="home-label">{{ __('essentials::lang.designations') }}</span>
        </a>
    </div>
@endcan

@if(auth()->user()->can('essentials.access_sales_target'))
    <div class="home-grid-tile" data-key="sales_target">
        <a href="{{ action([\Modules\Essentials\Http\Controllers\SalesTargetController::class, 'index']) }}" 
           title="{{ __('essentials::lang.sales_target') }}">
            <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/user/sales_target.svg') }}" 
                 class="home-icon" 
                 alt="{{ __('essentials::lang.sales_target') }}">
            <span class="home-label">{{ __('essentials::lang.sales_target') }}</span>
        </a>
    </div>
@endif

@if(auth()->user()->can('edit_essentials_settings'))
    <div class="home-grid-tile" data-key="essentials_settings">
        <a href="{{ action([\Modules\Essentials\Http\Controllers\EssentialsSettingsController::class, 'edit']) }}" 
           title="{{ __('business.settings') }}">
            <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/setting/setting.svg') }}" 
                 class="home-icon" 
                 alt="{{ __('business.settings') }}">
            <span class="home-label">{{ __('business.settings') }}</span>
        </a>
    </div>
@endif



</div>
    </div>
    </div>

