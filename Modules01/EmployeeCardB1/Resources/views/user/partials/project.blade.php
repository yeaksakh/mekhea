@if(isset($user->project_data['success']) && $user->project_data['success'] && count($user->project_data['projects']) > 0)
    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fas fa-project-diagram"></i> @lang('project::lang.projects')
            </h3>
        </div>
        <div class="box-body">
            <div class="row">
                @foreach($user->project_data['projects'] as $project)
                    <div class="col-md-4 col-sm-6">
                        <div class="panel panel-default">
                            <div class="panel-heading" style="background-color: #f5f5f5;">
                                <h4 class="panel-title">
                                    {{ $project['name'] }}
                                    {{-- <span class="pull-right label label-{{ $project['status'] }}" 
                                          style="background-color: {{ getStatusColor($project['status']) }};">
                                        {{ ucfirst($project['status']) }}
                                    </span> --}}
                                </h4>
                            </div>
                            <div class="panel-body">
                                @if(!empty($project['description']))
                                    <p>{{ $project['description'] }}</p>
                                @endif
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>@lang('project::lang.start_date'):</strong></p>
                                        <p>{{ \Carbon\Carbon::parse($project['start_date'])->format('M d, Y') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>@lang('project::lang.end_date'):</strong></p>
                                        <p>{{ \Carbon\Carbon::parse($project['end_date'])->format('M d, Y') }}</p>
                                    </div>
                                </div>
                                
                                @if(!empty($project['customer']))
                                    <p><strong>@lang('contact.customer'):</strong> {{ $project['customer'] }}</p>
                                @endif
                                
                                @if(!empty($project['lead']))
                                    <p><strong>@lang('project::lang.project_lead'):</strong> {{ $project['lead'] }}</p>
                                @endif
                                
                                @if(count($project['categories']) > 0)
                                    <p><strong>@lang('project::lang.categories'):</strong></p>
                                    <div>
                                        @foreach($project['categories'] as $category)
                                            <span class="label label-default">{{ $category }}</span>
                                        @endforeach
                                    </div>
                                @endif
                                
                                @if(count($project['members']) > 0)
                                    <p class="mt-10"><strong>@lang('project::lang.team_members'):</strong></p>
                                    <ul class="list-unstyled">
                                        @foreach($project['members'] as $member)
                                            <li>{{ $member }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                                
                                <div class="text-center mt-15">
                                    <a href="{{ action([\Modules\Project\Http\Controllers\ProjectController::class, 'show'], [$project['id']]) }}" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i> @lang('messages.view')
                                    </a>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@else
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> @lang('project::lang.no_projects_found')
    </div>
@endif

@push('css')
<style>
    .panel {
        margin-bottom: 15px;
        box-shadow: 0 1px 1px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    .panel:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .panel-heading {
        padding: 10px 15px;
    }
    .panel-body {
        padding: 15px;
    }
    .label-default {
        background-color: #f0f0f0;
        color: #333;
        margin-right: 5px;
        margin-bottom: 5px;
        display: inline-block;
    }
    .btn-sm {
        padding: 5px 10px;
        font-size: 12px;
        margin: 0 3px;
    }
</style>
@endpush

@php
function getStatusColor($status) {
    $colors = [
        'active' => '#00a65a',
        'completed' => '#555',
        'pending' => '#f39c12',
        'overdue' => '#dd4b39',
        'on_hold' => '#00c0ef'
    ];
    return $colors[strtolower($status)] ?? '#777';
}
@endphp