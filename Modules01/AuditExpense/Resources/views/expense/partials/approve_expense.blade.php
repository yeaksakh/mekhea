<div class="modal-dialog" role="document">
	{!! Form::open(['url' => action([\App\Http\Controllers\ExpenseController::class, 'updateApproveExpenseRequest'], [$transaction->id]), 'method' => 'put', 'id' => 'edit_approve_form' ]) !!}
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">@lang('lang_v1.approve_expense')</h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-12">
				<div class="form-group">
					{!! Form::label('approve_expense', __('lang_v1.approve_expense') . ':' ) !!}
					{!! Form::select('status', [
						'draft' => __('lang_v1.non_approve'),
						'final' => __('lang_v1.approve'),
					], isset($transaction) && $transaction->status == 'draft' ? 'draft' : 'final', ['class' => 'form-control', 'placeholder' => __('messages.please_select')]); !!}
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