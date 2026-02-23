<section class="no-print">
    <nav class="navbar-default tw-transition-all tw-duration-5000 tw-shrink-0 tw-rounded-2xl tw-m-[16px] tw-border-2 !tw-bg-white">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false" style="margin-top: 3px; margin-right: 3px;">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{action([\Modules\Essentials\Http\Controllers\DashboardController::class, 'hrmDashboard'])}}">
                    <i class="fa fas fa-users-cog"></i> {{__('essentials::lang.hrm')}}
                </a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                
                     @if(auth()->user()->can('user.view'))
                        <li @if(request()->segment(1) == 'users') class="active" @endif>
                            <a href="{{action([\App\Http\Controllers\ManageUserController::class, 'index'])}}">
                                @lang('user.users')
                            </a>
                        </li>
                    @endif
                    @if(auth()->user()->can('roles.view'))
                        <li @if(request()->segment(1) == 'roles') class="active" @endif>
                            <a href="{{action([\App\Http\Controllers\RoleController::class, 'index'])}}">
                                @lang('user.roles')
                            </a>
                        </li>
                    @endif
                    
                    @if(auth()->user()->can('user.create'))
                        <li @if(request()->segment(1) == 'sales-commission-agents') class="active" @endif>
                            <a href="{{action([\App\Http\Controllers\SalesCommissionAgentController::class, 'index'])}}">
                                @lang('lang_v1.sales_commission_agents')
                            </a>
                        </li>
                    @endif
                    
                    @can('essentials.crud_leave_type')
                        <li @if(request()->segment(2) == 'leave-type') class="active" @endif>
                            <a href="{{action([\Modules\Essentials\Http\Controllers\EssentialsLeaveTypeController::class, 'index'])}}">
                                @lang('essentials::lang.leave_type')
                            </a>
                        </li>
                    @endcan
                    @if(auth()->user()->can('essentials.crud_all_leave') || auth()->user()->can('essentials.crud_own_leave'))
                        <li @if(request()->segment(2) == 'leave') class="active" @endif>
                            <a href="{{action([\Modules\Essentials\Http\Controllers\EssentialsLeaveController::class, 'index'])}}">
                                @lang('essentials::lang.leave')
                            </a>
                        </li>
                    @endif
                    @if(auth()->user()->can('essentials.crud_all_attendance') || auth()->user()->can('essentials.view_own_attendance'))
                        <li @if(request()->segment(2) == 'attendance') class="active" @endif>
                            <a href="{{action([\Modules\Essentials\Http\Controllers\AttendanceController::class, 'index'])}}">
                                @lang('essentials::lang.attendance')
                            </a>
                        </li>
                    @endif
                    @can('essentials.crud_department')
                        <li @if(request()->get('type') == 'hrm_department') class="active" @endif>
                            <a href="{{action([\App\Http\Controllers\TaxonomyController::class, 'index']) . '?type=hrm_department'}}">
                                @lang('essentials::lang.departments')
                            </a>
                        </li>
                    @endcan
                    @can('essentials.crud_designation')
                        <li @if(request()->get('type') == 'hrm_designation') class="active" @endif>
                            <a href="{{action([\App\Http\Controllers\TaxonomyController::class, 'index']) . '?type=hrm_designation'}}">
                                @lang('essentials::lang.designations')
                            </a>
                        </li>
                    @endcan
                    @if(auth()->user()->can('essentials.access_sales_target'))
                        <li @if(request()->segment(1) == 'hrm' && request()->segment(2) == 'sales-target') class="active" @endif>
                            <a href="{{action([\Modules\Essentials\Http\Controllers\SalesTargetController::class, 'index'])}}">
                                @lang('essentials::lang.sales_target')
                            </a>
                        </li>
                    @endif
                    @if(auth()->user()->can('edit_essentials_settings'))
                        <li @if(request()->segment(1) == 'hrm' && request()->segment(2) == 'settings') class="active" @endif>
                            <a href="{{action([\Modules\Essentials\Http\Controllers\EssentialsSettingsController::class, 'edit'])}}">
                                @lang('business.settings')
                            </a>
                        </li>
                    @endif
                   
                </ul>
            </div>
        </div>
    </nav>
</section>