<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header text-center">
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-default active">
                        <input type="radio" name="ledger_format" value="format_1" checked> @lang('lang_v1.format_1')
                    </label>
                    <label class="btn btn-default">
                        <input type="radio" name="ledger_format" value="format_2"> @lang('lang_v1.format_2')
                    </label>
                    <label class="btn btn-default">
                        <input type="radio" name="ledger_format" value="format_3"> @lang('lang_v1.format_3')
                    </label>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('ledger_date_range', __('report.date_range') . ':') !!}
                            {!! Form::text('ledger_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('ledger_location', __('purchase.business_location') . ':') !!}
                            {!! Form::select('ledger_location', $business_locations, null , ['class' => 'form-control select2', 'id' => 'ledger_location']); !!}
                        </div>
                    </div>
                    <div class="col-md-2 text-right">
                        <button data-href="{{action([\App\Http\Controllers\ContactController::class, 'getLedger'])}}?contact_id={{$contact->id}}&action=pdf" class="btn btn-default btn-xs" id="print_ledger_pdf"><i class="fas fa-file-pdf"></i></button>

                        <button type="button" class="btn btn-default btn-xs" id="send_ledger"><i class="fas fa-envelope"></i></button>
                    </div>
                </div>
                <div id="contact_ledger_div"></div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        max-height: 100vh;
        overflow-y: auto;
        border: 1px solid #ddd;
        border-radius: 5px;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
    }
    .btn-group label {
        margin-right: 10px;
    }
    .card-header {
        background-color: #f8f9fa;
        padding: 10px 15px;
        border-bottom: 1px solid #ddd;
        position: sticky;
        top: 0;
        z-index: 1;
        background-color: #f8f9fa;
    }
    .card-body {
        padding: 15px;
    }
</style>