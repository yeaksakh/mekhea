@if(isset($user->tasks['success']) && $user->tasks['success'] && count($user->tasks['tasks']) > 0)
    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="ion ion-clipboard"></i> @lang('essentials::lang.todo_list')
            </h3>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>@lang('essentials::lang.task')</th>
                            <th>@lang('essentials::lang.priority')</th>
                            <th>@lang('sale.status')</th>
                            <th>@lang('business.start_date')</th>
                            <th>@lang('essentials::lang.end_date')</th>
                            <th>@lang('essentials::lang.assigned_by')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($user->tasks['tasks'] as $task)
                            <tr>
                                <td>
                                    <strong>{{ $task['task'] }}</strong>
                                    @if(!empty($task['description']))
                                        <p class="text-muted small">{{ $task['description'] }}</p>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $priority_colors = [
                                            'low' => 'bg-blue',
                                            'medium' => 'bg-yellow',
                                            'high' => 'bg-orange',
                                            'urgent' => 'bg-red'
                                        ];
                                        $priority_bg_color = $priority_colors[$task['priority']] ?? 'bg-gray';
                                    @endphp
                                    <span class="label {{ $priority_bg_color }}">
                                        {{ ucfirst($task['priority'] ?? '') }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $status_colors = [
                                            'new' => 'bg-blue',
                                            'in_progress' => 'bg-yellow',
                                            'on_hold' => 'bg-orange',
                                            'completed' => 'bg-green'
                                        ];
                                        $bg_color = $status_colors[$task['status']] ?? 'bg-gray';
                                    @endphp
                                    <span class="label {{ $bg_color }}">
                                        @if(isset($task['status']))
                                            {{ __('essentials::lang.' . $task['status']) }}
                                        @else
                                            {{ __('essentials::lang.new') }}
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    @if(!empty($task['date']))
                                        {{ \Carbon\Carbon::parse($task['date'])->format('M d, Y H:i') }}
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($task['end_date']))
                                        {{ \Carbon\Carbon::parse($task['end_date'])->format('M d, Y H:i') }}
                                    @endif
                                </td>
                                <td>{{ $task['assigned_by'] ?? '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    <div class="alert alert-info">
        <i class="fa fa-info-circle"></i> @lang('essentials::lang.no_tasks_found')
    </div>
@endif

<style>
    .table-responsive {
        overflow-x: auto;
    }
    .table {
        width: 100%;
        margin-bottom: 20px;
    }
    .table-bordered {
        border: 1px solid #ddd;
    }
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0,0,0,.05);
    }
    .table th, .table td {
        padding: 8px;
        vertical-align: top;
        border-top: 1px solid #dee2e6;
    }
    .label {
        display: inline;
        padding: .2em .6em .3em;
        font-size: 75%;
        font-weight: 700;
        line-height: 1;
        color: #fff;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: .25em;
    }
    .bg-blue { background-color: #007bff; }
    .bg-yellow { background-color: #ffc107; }
    .bg-orange { background-color: #fd7e14; }
    .bg-green { background-color: #28a745; }
    .bg-red { background-color: #dc3545; }
    .bg-gray { background-color: #6c757d; }
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
    }
    .alert-info {
        color: #31708f;
        background-color: #d9edf7;
        border-color: #bce8f1;
    }
    .box {
        position: relative;
        border-radius: 3px;
        background: #ffffff;
        border-top: 3px solid #d2d6de;
        margin-bottom: 20px;
        width: 100%;
        box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    }
    .box.box-solid {
        border-top: 0;
    }
    .box-header {
        color: #444;
        display: block;
        padding: 10px;
        position: relative;
    }
    .box-header.with-border {
        border-bottom: 1px solid #f4f4f4;
    }
    .box-title {
        display: inline-block;
        font-size: 18px;
        margin: 0;
        line-height: 1;
    }
    .box-body {
        border-top-left-radius: 0;
        border-top-right-radius: 0;
        border-bottom-right-radius: 3px;
        border-bottom-left-radius: 3px;
        padding: 10px;
    }
    .text-muted {
        color: #777;
    }
    .small {
        font-size: 85%;
    }
</style>