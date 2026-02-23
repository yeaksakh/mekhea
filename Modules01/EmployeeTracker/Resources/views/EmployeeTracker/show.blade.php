<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">
                @lang('employeetracker::lang.employee_details'): 
                {{ $employee->first_name }} {{ $employee->last_name }}
            </h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-12">
                    <h4>@lang('employeetracker::lang.activities')</h4>
                    
                    @if($grouped_activities->count() > 0)
                        @foreach($grouped_activities as $form_id => $activities)
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">
                                        @lang('employeetracker::lang.form'): 
                                        {{ $activities->first()->form->name ?? 'Form ' . $form_id }}
                                    </h3>
                                </div>
                                <div class="panel-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>@lang('employeetracker::lang.field')</th>
                                                    <th>@lang('employeetracker::lang.value')</th>
                                                    <th>@lang('employeetracker::lang.date')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($activities as $activity)
                                                    <tr>
                                                        <td>{{ $activity->field->field_label ?? 'Field ' . $activity->field_id }}</td>
                                                        <td>{{ $activity->value }}</td>
                                                        <td>{{ $activity->created_at->format('Y-m-d H:i:s') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-info">
                            @lang('employeetracker::lang.no_activities_found')
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
        </div>
    </div>
</div>