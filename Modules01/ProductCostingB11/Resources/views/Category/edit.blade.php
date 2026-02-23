<div class="modal-dialog" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'messages.edit' )</h4>
    </div>
      <div class="modal-body">
          {!! Form::model($category, ['url' => action([\Modules\ProductCostingB11\Http\Controllers\ProductCostingB11Controller::class, 'updateCategory'], $category->id), 'method' => 'put', 'id' => 'category_edit_form' ]) !!}
          <div class="form-group">
              {!! Form::label('name', __('productcostingb11::lang.name') . ':*') !!}
              {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __('productcostingb11::lang.category_name')]) !!}
          </div>
          <div class="form-group">
              {!! Form::label('description', __('productcostingb11::lang.description') . ':') !!}
              {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => __('productcostingb11::lang.description'), 'rows' => 3]) !!}
          </div>
          <div class="modal-footer">
              <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
              <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
          </div>
          {!! Form::close() !!}
      </div>
  </div>
</div>

