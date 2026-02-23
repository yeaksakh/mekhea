<div class="modal-dialog" role="document">
	{!! Form::open(['url' => action([\Modules\AuditB1\Http\Controllers\ExpenseController::class, 'updateAudit'], [$transaction->id]), 'method' => 'put', 'id' => 'edit_audit_form' ]) !!}
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">@lang('lang_v1.edit_audit')</h4>
		</div>
		<div class="modal-body">
			<div class="row">


				<div class="col-md-4">
					<div class="form-group">
						{!! Form::label('audit_status', __('lang_v1.audit_status') . ':' ) !!}
						{!! Form::select('audit_status',$audit_statuses, $transaction->audit_status , ['class' => 'form-control','placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang('messages.update')</button>
			<button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang('messages.cancel')</button>
		</div>
		{!! Form::close() !!}
	</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->