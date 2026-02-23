<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('employeetracker::lang.view_form'): {{ $form->name }}</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label><strong>@lang('employeetracker::lang.form_name'):</strong></label>
                        <p>{{ $form->name }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label><strong>@lang('employeetracker::lang.department'):</strong></label>
                        <p>{{ $form->department ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label><strong>@lang('employeetracker::lang.description'):</strong></label>
                        <p>{{ $form->description ?? 'No description provided' }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label><strong>@lang('employeetracker::lang.created_by'):</strong></label>
                        <p>{{ $form->createdBy->first_name ?? 'Unknown' }} {{ $form->createdBy->last_name ?? '' }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label><strong>@lang('employeetracker::lang.created_at'):</strong></label>
                        <p>{{ $form->created_at->format('M d, Y H:i') ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            @if($form->fields && count($form->fields) > 0)
                <hr>
                <h4>@lang('employeetracker::lang.form_fields') ({{ count($form->fields) }} fields)</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('employeetracker::lang.field_label')</th>
                                <th>@lang('employeetracker::lang.field_type')</th>
                                <th>@lang('employeetracker::lang.is_required')</th>
                                <th>@lang('employeetracker::lang.options')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($form->fields->sortBy('field_order') as $index => $field)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $field->field_label }}</td>
                                    <td>
                                        <span class="label label-info">{{ ucfirst($field->field_type) }}</span>
                                    </td>
                                    <td>
                                        @if($field->is_required)
                                            <span class="label label-danger">Required</span>
                                        @else
                                            <span class="label label-default">Optional</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($field->config)
                                            {{ $field->config }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> No fields have been added to this form yet.
                </div>
            @endif
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal" data-href="{{ action([\Modules\EmployeeTracker\Http\Controllers\ActivityFormController::class, 'edit'], [$form->id]) }}" class="btn-modal" data-container="#EmployeeTracker_modal">
                <i class="fa fa-edit"></i> @lang('messages.edit')
            </button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
        </div>
    </div>
</div>