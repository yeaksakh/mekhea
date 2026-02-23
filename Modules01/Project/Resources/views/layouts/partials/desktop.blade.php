@php
    $business_id = session()->get('user.business_id');
    $module_util = new \App\Utils\ModuleUtil();
    $is_project_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'project_module');
    $is_active = request()->segment(1) == 'project' && request()->get('project_view') == 'list_view';
@endphp

@if ($is_project_enabled && (auth()->user()->can('superadmin') || auth()->user()->can('project.create_project') || auth()->user()->can('project.edit_project') || auth()->user()->can('project.delete_project')))
    <div class="home-grid-tile" data-key="project">
    <a href="{{ action([\Modules\Project\Http\Controllers\ProjectController::class, 'index']) . '?project_view=list_view' }}"
       title="{{ __('project::lang.project') }}">
       <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/modules/project.svg') }}"
                class="home-icon"
                alt="">
       <span class="home-label">{{ __('project::lang.project') }}</span>
    </a>
</div>
@endif


