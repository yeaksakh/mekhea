@extends('layouts.app')
@section('title', __('purchase.add_purchase'))

@section('content')

    @php
        $custom_labels = json_decode(session('business.custom_labels'), true);
    @endphp
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('purchase.add_purchase') <i
                class="fa fa-keyboard-o hover-q text-muted" aria-hidden="true" data-container="body" data-toggle="popover"
                data-placement="bottom" data-content="@include('purchase.partials.keyboard_shortcuts_details')" data-html="true" data-trigger="hover"
                data-original-title="" title=""></i></h1>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Page level currency setting -->
        <input type="hidden" id="p_code" value="{{ $currency_details->code }}">
        <input type="hidden" id="p_symbol" value="{{ $currency_details->symbol }}">
        <input type="hidden" id="p_thousand" value="{{ $currency_details->thousand_separator }}">
        <input type="hidden" id="p_decimal" value="{{ $currency_details->decimal_separator }}">

        @include('layouts.partials.error')

        {!! Form::open([
            'url' => action([\App\Http\Controllers\PurchaseController::class, 'store']),
            'method' => 'post',
            'id' => 'add_purchase_form',
            'files' => true,
        ]) !!}
        @component('components.widget', ['class' => 'box-primary'])
            <div class="row">
                <div class="@if (!empty($default_purchase_status)) col-sm-4 @else col-sm-3 @endif">
                    <div class="form-group">
                        {!! Form::label('vendor_id', __('purchase.vendor') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-building"></i>
                            </span>
                            {!! Form::select('contact_id', [], null, [
                                'class' => 'form-control',
                                'placeholder' => __('messages.please_select'),
                                'required',
                                'id' => 'vendor_id',
                            ]) !!}
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default bg-white btn-flat add_new_vendor"
                                    data-name=""><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                            </span>
                        </div>
                    </div>
                    <strong>
                        @lang('business.address'):
                    </strong>
                    <div id="supplier_address_div"></div>
                </div>
                <div class="@if (!empty($default_purchase_status)) col-sm-4 @else col-sm-3 @endif">
                    <div class="form-group">
                        {!! Form::label('ref_no', __('purchase.ref_no') . ':') !!}
                        @show_tooltip(__('lang_v1.leave_empty_to_autogenerate'))
                        {!! Form::text('ref_no', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="@if (!empty($default_purchase_status)) col-sm-4 @else col-sm-3 @endif">
                    <div class="form-group">
                        {!! Form::label('transaction_date', __('purchase.purchase_date') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            {!! Form::text('transaction_date', @format_datetime($default_transaction_date), [
                                'class' => 'form-control',
                                'readonly',
                                'required',
                            ]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-3 ">
                    <div class="form-group">
                        {!! Form::label('status', __('purchase.purchase_status') . ':*') !!} @show_tooltip(__('tooltip.order_status'))
                        {!! Form::select('status', $orderStatuses, $default_purchase_status, [
                            'class' => 'form-control select2',
                            'placeholder' => __('messages.please_select'),
                            'required',
                        ]) !!}
                    </div>
                </div>
                @if (count($business_locations) == 1)
                    @php
                        $default_location = current(array_keys($business_locations->toArray()));
                        $search_disable = false;
                    @endphp
                @else
                    @php$default_location = null;
                                                                                                                                                                        $search_disable = true;
                                                                                                                                            @endphp ?>
                @endif
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('location_id', __('purchase.business_location') . ':*') !!}
                        @show_tooltip(__('tooltip.purchase_location'))
                        {!! Form::select(
                            'location_id',
                            $business_locations,
                            $default_location_id,
                            ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required'],
                            $bl_attributes,
                        ) !!}
                    </div>
                </div>

                <!-- Currency Exchange Rate -->
                <div class="col-sm-3 @if (!$currency_details->purchase_in_diff_currency) hide @endif">
                    <div class="form-group">
                        {!! Form::label('exchange_rate', __('purchase.p_exchange_rate') . ':*') !!}
                        @show_tooltip(__('tooltip.currency_exchange_factor'))
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-info"></i>
                            </span>
                            {!! Form::number('exchange_rate', $currency_details->p_exchange_rate, [
                                'class' => 'form-control',
                                'required',
                                'step' => 0.001,
                            ]) !!}
                        </div>
                        <span class="help-block text-danger">
                            @lang('purchase.diff_purchase_currency_help', ['currency' => $currency_details->name])
                        </span>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <div class="multi-input">
                            {!! Form::label('pay_term_number', __('contact.pay_term') . ':') !!} @show_tooltip(__('tooltip.pay_term'))
                            <br />
                            {!! Form::number('pay_term_number', null, [
                                'class' => 'form-control width-40 pull-left',
                                'min' => 0,
                                'placeholder' => __('contact.pay_term'),
                            ]) !!}

                            {!! Form::select('pay_term_type', ['months' => __('lang_v1.months'), 'days' => __('lang_v1.days')], null, [
                                'class' => 'form-control width-60 pull-left',
                                'placeholder' => __('messages.please_select'),
                                'id' => 'pay_term_type',
                            ]) !!}
                        </div>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('document', __('purchase.attach_document') . ':') !!}

                        {{-- Display the OCR image if it exists --}}
                        @if (!empty($ocr_image_path))
                            <div class="mb-2">
                                <p class="help-block"><strong>OCR Image:</strong></p>
                                <img src="{{ asset($ocr_image_path) }}" alt="OCR Image"
                                    style="max-width: 200px; max-height: 200px;" class="img-thumbnail">
                            </div>

                            {{-- Hidden input to pass the image path to the controller --}}
                            <input type="hidden" name="ocr_image_path_hidden" id="ocr_image_path_hidden"
                                value="{{ $ocr_image_path }}">
                        @endif

                        {!! Form::file('document', [
                            'id' => 'upload_document',
                            'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types'))),
                        ]) !!}
                        <p class="help-block">
                            @lang('purchase.max_file_size', ['size' => config('constants.document_size_limit') / 1000000])
                            @includeIf('components.document_help_text')
                        </p>
                    </div>
                </div>
            </div>
            <div class="row">
                @php
                    $custom_field_1_label = !empty($custom_labels['purchase']['custom_field_1'])
                        ? $custom_labels['purchase']['custom_field_1']
                        : '';

                    $is_custom_field_1_required =
                        !empty($custom_labels['purchase']['is_custom_field_1_required']) &&
                        $custom_labels['purchase']['is_custom_field_1_required'] == 1
                            ? true
                            : false;

                    $custom_field_2_label = !empty($custom_labels['purchase']['custom_field_2'])
                        ? $custom_labels['purchase']['custom_field_2']
                        : '';

                    $is_custom_field_2_required =
                        !empty($custom_labels['purchase']['is_custom_field_2_required']) &&
                        $custom_labels['purchase']['is_custom_field_2_required'] == 1
                            ? true
                            : false;

                    $custom_field_3_label = !empty($custom_labels['purchase']['custom_field_3'])
                        ? $custom_labels['purchase']['custom_field_3']
                        : '';

                    $is_custom_field_3_required =
                        !empty($custom_labels['purchase']['is_custom_field_3_required']) &&
                        $custom_labels['purchase']['is_custom_field_3_required'] == 1
                            ? true
                            : false;

                    $custom_field_4_label = !empty($custom_labels['purchase']['custom_field_4'])
                        ? $custom_labels['purchase']['custom_field_4']
                        : '';

                    $is_custom_field_4_required =
                        !empty($custom_labels['purchase']['is_custom_field_4_required']) &&
                        $custom_labels['purchase']['is_custom_field_4_required'] == 1
                            ? true
                            : false;
                @endphp
                @if (!empty($custom_field_1_label))
                    @php
                        $label_1 = $custom_field_1_label . ':';
                        if ($is_custom_field_1_required) {
                            $label_1 .= '*';
                        }
                    @endphp

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('custom_field_1', $label_1) !!}
                            {!! Form::text('custom_field_1', null, [
                                'class' => 'form-control',
                                'placeholder' => $custom_field_1_label,
                                'required' => $is_custom_field_1_required,
                            ]) !!}
                        </div>
                    </div>
                @endif
                @if (!empty($custom_field_2_label))
                    @php
                        $label_2 = $custom_field_2_label . ':';
                        if ($is_custom_field_2_required) {
                            $label_2 .= '*';
                        }
                    @endphp

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('custom_field_2', $label_2) !!}
                            {!! Form::text('custom_field_2', null, [
                                'class' => 'form-control',
                                'placeholder' => $custom_field_2_label,
                                'required' => $is_custom_field_2_required,
                            ]) !!}
                        </div>
                    </div>
                @endif
                @if (!empty($custom_field_3_label))
                    @php
                        $label_3 = $custom_field_3_label . ':';
                        if ($is_custom_field_3_required) {
                            $label_3 .= '*';
                        }
                    @endphp

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('custom_field_3', $label_3) !!}
                            {!! Form::text('custom_field_3', null, [
                                'class' => 'form-control',
                                'placeholder' => $custom_field_3_label,
                                'required' => $is_custom_field_3_required,
                            ]) !!}
                        </div>
                    </div>
                @endif
                @if (!empty($custom_field_4_label))
                    @php
                        $label_4 = $custom_field_4_label . ':';
                        if ($is_custom_field_4_required) {
                            $label_4 .= '*';
                        }
                    @endphp

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('custom_field_4', $label_4) !!}
                            {!! Form::text('custom_field_4', null, [
                                'class' => 'form-control',
                                'placeholder' => $custom_field_4_label,
                                'required' => $is_custom_field_4_required,
                            ]) !!}
                        </div>
                    </div>
                @endif
            </div>
            @if (!empty($common_settings['enable_purchase_order']))
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('purchase_order_ids', __('lang_v1.purchase_order') . ':') !!}
                            {!! Form::select('purchase_order_ids[]', [], null, [
                                'class' => 'form-control select2',
                                'multiple',
                                'id' => 'purchase_order_ids',
                            ]) !!}
                        </div>
                    </div>
                </div>
            @endif
        @endcomponent

        @component('components.widget', ['class' => 'box-primary'])
            <div class="row">
                <div class="col-sm-2 text-center">
                    <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm" data-toggle="modal"
                        data-target="#import_purchase_products_modal">@lang('product.import_products')</button>
                </div>
                <div class="col-sm-8">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-search"></i>
                            </span>
                            {!! Form::text('product_search', null, [
                                'class' => 'form-control mousetrap',
                                'id' => 'product_search',
                                'placeholder' => __('lang_v1.search_product_placeholder'),
                                // 'disabled' => $search_disable,
                            ]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <button tabindex="-1" type="button"
                            class="btn btn-link btn-modal"data-href="{{ action([\App\Http\Controllers\ProductController::class, 'quickAdd']) }}"
                            data-container=".quick_add_product_modal"><i class="fa fa-plus"></i> @lang('product.add_new_product') </button>
                    </div>
                </div>
            </div>
            @php
                $hide_tax = '';
                if (session()->get('business.enable_inline_tax') == 0) {
                    $hide_tax = 'hide';
                }
            @endphp
            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table table-condensed table-bordered table-th-green text-center table-striped"
                            id="purchase_entry_table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>@lang('product.product_name')</th>
                                    <th>@lang('purchase.purchase_quantity')</th>
                                    <th>@lang('lang_v1.unit_cost_before_discount')</th>
                                    <th>@lang('lang_v1.discount_percent')</th>
                                    <th>@lang('purchase.unit_cost_before_tax')</th>
                                    <th class="{{ $hide_tax }}">@lang('purchase.subtotal_before_tax')</th>
                                    <th class="{{ $hide_tax }}">@lang('purchase.product_tax')</th>
                                    <th class="{{ $hide_tax }}">@lang('purchase.net_cost')</th>
                                    <th>@lang('purchase.line_total')</th>
                                    <th class="@if (!session('business.enable_editing_product_from_purchase')) hide @endif">
                                        @lang('lang_v1.profit_margin')
                                    </th>
                                    <th>
                                        @lang('purchase.unit_selling_price')
                                        <small>(@lang('product.inc_of_tax'))</small>
                                    </th>
                                    @if (session('business.enable_lot_number'))
                                        <th>
                                            @lang('lang_v1.lot_number')
                                        </th>
                                    @endif
                                    @if (session('business.enable_product_expiry'))
                                        <th>
                                            @lang('product.mfg_date') / @lang('product.exp_date')
                                        </th>
                                    @endif
                                    <th><i class="fa fa-trash" aria-hidden="true"></i></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <hr />
                    <div class="pull-right col-md-5">
                        <table class="pull-right col-md-12">
                            <tr>
                                <th class="col-md-7 text-right">@lang('lang_v1.total_items'):</th>
                                <td class="col-md-5 text-left">
                                    <span id="total_quantity" class="display_currency" data-currency_symbol="false"></span>
                                </td>
                            </tr>
                            <tr class="hide">
                                <th class="col-md-7 text-right">@lang('purchase.total_before_tax'):</th>
                                <td class="col-md-5 text-left">
                                    <span id="total_st_before_tax" class="display_currency"></span>
                                    <input type="hidden" id="st_before_tax_input" value=0>
                                </td>
                            </tr>
                            <tr>
                                <th class="col-md-7 text-right">@lang('purchase.net_total_amount'):</th>
                                <td class="col-md-5 text-left">
                                    <span id="total_subtotal" class="display_currency"></span>
                                    <!-- This is total before purchase tax-->
                                    <input type="hidden" id="total_subtotal_input" value=0 name="total_before_tax">
                                </td>
                            </tr>
                        </table>
                    </div>

                    <input type="hidden" id="row_count" value="0">
                </div>
            </div>
        @endcomponent

        @component('components.widget', ['class' => 'box-primary'])
            <div class="row">
                <div class="col-sm-12">
                    <table class="table">
                        <tr>
                            <td class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('discount_type', __('purchase.discount_type') . ':') !!}
                                    {!! Form::select(
                                        'discount_type',
                                        ['' => __('lang_v1.none'), 'fixed' => __('lang_v1.fixed'), 'percentage' => __('lang_v1.percentage')],
                                        '',
                                        ['class' => 'form-control select2'],
                                    ) !!}
                                </div>
                            </td>
                            <td class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('discount_amount', __('purchase.discount_amount') . ':') !!}
                                    {!! Form::text('discount_amount', 0, ['class' => 'form-control input_number', 'required']) !!}
                                </div>
                            </td>
                            <td class="col-md-3">
                                &nbsp;
                            </td>
                            <td class="col-md-3">
                                <b>@lang('purchase.discount'):</b>(-)
                                <span id="discount_calculated_amount" class="display_currency">0</span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    {!! Form::label('tax_id', __('purchase.purchase_tax') . ':') !!}
                                    <select name="tax_id" id="tax_id" class="form-control select2"
                                        placeholder="'Please Select'">
                                        <option value="" data-tax_amount="0" data-tax_type="fixed" selected>
                                            @lang('lang_v1.none')</option>
                                        @foreach ($taxes as $tax)
                                            <option value="{{ $tax->id }}" data-tax_amount="{{ $tax->amount }}"
                                                data-tax_type="{{ $tax->calculation_type }}">{{ $tax->name }}</option>
                                        @endforeach
                                    </select>
                                    {!! Form::hidden('tax_amount', 0, ['id' => 'tax_amount']) !!}
                                </div>
                            </td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>
                                <b>@lang('purchase.purchase_tax'):</b>(+)
                                <span id="tax_calculated_amount" class="display_currency">0</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <div class="form-group">
                                    {!! Form::label('additional_notes', __('purchase.additional_notes')) !!}
                                    {!! Form::textarea('additional_notes', null, ['class' => 'form-control', 'rows' => 3]) !!}
                                </div>
                            </td>
                        </tr>

                    </table>
                </div>
            </div>
        @endcomponent
        @component('components.widget', ['class' => 'box-primary'])
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('shipping_details', __('purchase.shipping_details') . ':') !!}
                        {!! Form::text('shipping_details', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-4 col-md-offset-4">
                    <div class="form-group">
                        {!! Form::label('shipping_charges', '(+) ' . __('purchase.additional_shipping_charges') . ':') !!}
                        {!! Form::text('shipping_charges', 0, ['class' => 'form-control input_number', 'required']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                @php
                    $shipping_custom_label_1 = !empty($custom_labels['purchase_shipping']['custom_field_1'])
                        ? $custom_labels['purchase_shipping']['custom_field_1']
                        : '';

                    $is_shipping_custom_field_1_required =
                        !empty($custom_labels['purchase_shipping']['is_custom_field_1_required']) &&
                        $custom_labels['purchase_shipping']['is_custom_field_1_required'] == 1
                            ? true
                            : false;

                    $shipping_custom_label_2 = !empty($custom_labels['purchase_shipping']['custom_field_2'])
                        ? $custom_labels['purchase_shipping']['custom_field_2']
                        : '';

                    $is_shipping_custom_field_2_required =
                        !empty($custom_labels['purchase_shipping']['is_custom_field_2_required']) &&
                        $custom_labels['purchase_shipping']['is_custom_field_2_required'] == 1
                            ? true
                            : false;

                    $shipping_custom_label_3 = !empty($custom_labels['purchase_shipping']['custom_field_3'])
                        ? $custom_labels['purchase_shipping']['custom_field_3']
                        : '';

                    $is_shipping_custom_field_3_required =
                        !empty($custom_labels['purchase_shipping']['is_custom_field_3_required']) &&
                        $custom_labels['purchase_shipping']['is_custom_field_3_required'] == 1
                            ? true
                            : false;

                    $shipping_custom_label_4 = !empty($custom_labels['purchase_shipping']['custom_field_4'])
                        ? $custom_labels['purchase_shipping']['custom_field_4']
                        : '';

                    $is_shipping_custom_field_4_required =
                        !empty($custom_labels['purchase_shipping']['is_custom_field_4_required']) &&
                        $custom_labels['purchase_shipping']['is_custom_field_4_required'] == 1
                            ? true
                            : false;

                    $shipping_custom_label_5 = !empty($custom_labels['purchase_shipping']['custom_field_5'])
                        ? $custom_labels['purchase_shipping']['custom_field_5']
                        : '';

                    $is_shipping_custom_field_5_required =
                        !empty($custom_labels['purchase_shipping']['is_custom_field_5_required']) &&
                        $custom_labels['purchase_shipping']['is_custom_field_5_required'] == 1
                            ? true
                            : false;
                @endphp

                @if (!empty($shipping_custom_label_1))
                    @php
                        $label_1 = $shipping_custom_label_1 . ':';
                        if ($is_shipping_custom_field_1_required) {
                            $label_1 .= '*';
                        }
                    @endphp

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('shipping_custom_field_1', $label_1) !!}
                            {!! Form::text('shipping_custom_field_1', null, [
                                'class' => 'form-control',
                                'placeholder' => $shipping_custom_label_1,
                                'required' => $is_shipping_custom_field_1_required,
                            ]) !!}
                        </div>
                    </div>
                @endif
                @if (!empty($shipping_custom_label_2))
                    @php
                        $label_2 = $shipping_custom_label_2 . ':';
                        if ($is_shipping_custom_field_2_required) {
                            $label_2 .= '*';
                        }
                    @endphp

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('shipping_custom_field_2', $label_2) !!}
                            {!! Form::text('shipping_custom_field_2', null, [
                                'class' => 'form-control',
                                'placeholder' => $shipping_custom_label_2,
                                'required' => $is_shipping_custom_field_2_required,
                            ]) !!}
                        </div>
                    </div>
                @endif
                @if (!empty($shipping_custom_label_3))
                    @php
                        $label_3 = $shipping_custom_label_3 . ':';
                        if ($is_shipping_custom_field_3_required) {
                            $label_3 .= '*';
                        }
                    @endphp

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('shipping_custom_field_3', $label_3) !!}
                            {!! Form::text('shipping_custom_field_3', null, [
                                'class' => 'form-control',
                                'placeholder' => $shipping_custom_label_3,
                                'required' => $is_shipping_custom_field_3_required,
                            ]) !!}
                        </div>
                    </div>
                @endif
                @if (!empty($shipping_custom_label_4))
                    @php
                        $label_4 = $shipping_custom_label_4 . ':';
                        if ($is_shipping_custom_field_4_required) {
                            $label_4 .= '*';
                        }
                    @endphp

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('shipping_custom_field_4', $label_4) !!}
                            {!! Form::text('shipping_custom_field_4', null, [
                                'class' => 'form-control',
                                'placeholder' => $shipping_custom_label_4,
                                'required' => $is_shipping_custom_field_4_required,
                            ]) !!}
                        </div>
                    </div>
                @endif
                @if (!empty($shipping_custom_label_5))
                    @php
                        $label_5 = $shipping_custom_label_5 . ':';
                        if ($is_shipping_custom_field_5_required) {
                            $label_5 .= '*';
                        }
                    @endphp

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('shipping_custom_field_5', $label_5) !!}
                            {!! Form::text('shipping_custom_field_5', null, [
                                'class' => 'form-control',
                                'placeholder' => $shipping_custom_label_5,
                                'required' => $is_shipping_custom_field_5_required,
                            ]) !!}
                        </div>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm"
                        id="toggle_additional_expense"> <i class="fas fa-plus"></i> @lang('lang_v1.add_additional_expenses') <i
                            class="fas fa-chevron-down"></i></button>
                </div>
                <div class="col-md-8 col-md-offset-4" id="additional_expenses_div" style="display: none;">
                    <table class="table table-condensed">
                        <thead>
                            <tr>
                                <th>@lang('lang_v1.additional_expense_name')</th>
                                <th>@lang('sale.amount')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    {!! Form::text('additional_expense_key_1', null, [
                                        'class' => 'form-control',
                                        'id' => 'additional_expense_key_1',
                                    ]) !!}
                                </td>
                                <td>
                                    {!! Form::text('additional_expense_value_1', 0, [
                                        'class' => 'form-control input_number',
                                        'id' => 'additional_expense_value_1',
                                    ]) !!}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {!! Form::text('additional_expense_key_2', null, [
                                        'class' => 'form-control',
                                        'id' => 'additional_expense_key_2',
                                    ]) !!}
                                </td>
                                <td>
                                    {!! Form::text('additional_expense_value_2', 0, [
                                        'class' => 'form-control input_number',
                                        'id' => 'additional_expense_value_2',
                                    ]) !!}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {!! Form::text('additional_expense_key_3', null, [
                                        'class' => 'form-control',
                                        'id' => 'additional_expense_key_3',
                                    ]) !!}
                                </td>
                                <td>
                                    {!! Form::text('additional_expense_value_3', 0, [
                                        'class' => 'form-control input_number',
                                        'id' => 'additional_expense_value_3',
                                    ]) !!}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {!! Form::text('additional_expense_key_4', null, [
                                        'class' => 'form-control',
                                        'id' => 'additional_expense_key_4',
                                    ]) !!}
                                </td>
                                <td>
                                    {!! Form::text('additional_expense_value_4', 0, [
                                        'class' => 'form-control input_number',
                                        'id' => 'additional_expense_value_4',
                                    ]) !!}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-right">
                    {!! Form::hidden('final_total', 0, ['id' => 'grand_total_hidden']) !!}
                    <b>@lang('purchase.purchase_total'): </b><span id="grand_total" class="display_currency"
                        data-currency_symbol='true'>0</span>
                </div>
            </div>
        @endcomponent
        @component('components.widget', ['class' => 'box-primary', 'title' => __('purchase.add_payment')])
            <div class="box-body payment_row">
                <div class="row">
                    <div class="col-md-12">
                        <strong>@lang('lang_v1.advance_balance'):</strong> <span id="advance_balance_text">0</span>
                        {!! Form::hidden('advance_balance', null, [
                            'id' => 'advance_balance',
                            'data-error-msg' => __('lang_v1.required_advance_balance_not_available'),
                        ]) !!}
                    </div>
                </div>
                @include('sale_pos.partials.payment_row_form', [
                    'row_index' => 0,
                    'show_date' => true,
                    'show_denomination' => true,
                ])
                <hr>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="pull-right"><strong>@lang('purchase.payment_due'):</strong> <span id="payment_due">0.00</span></div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <button type="button" id="submit_purchase_form"
                            class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-lg tw-text-white">@lang('messages.save')</button>
                    </div>
                </div>
            </div>
        @endcomponent

        {!! Form::close() !!}
    </section>
    <!-- quick product modal -->
    <div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
    <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        @include('contact.create', ['quick_add' => true])
    </div>

    @include('purchase.partials.import_purchase_products_modal')
    <!-- /.content -->
@endsection

@section('javascript')
    <script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            __page_leave_confirmation('#add_purchase_form');
            $('.paid_on').datetimepicker({
                format: moment_date_format + ' ' + moment_time_format,
                ignoreReadonly: true,
            });

            if ($('.payment_types_dropdown').length) {
                $('.payment_types_dropdown').change();
            }
            set_payment_type_dropdown();
            $('select#location_id').change(function() {
                set_payment_type_dropdown();
            });
        });
        $(document).on('change', '.payment_types_dropdown, #location_id', function(e) {
            var default_accounts = $('select#location_id').length ?
                $('select#location_id')
                .find(':selected')
                .data('default_payment_accounts') : [];
            var payment_types_dropdown = $('.payment_types_dropdown');
            var payment_type = payment_types_dropdown.val();
            var payment_row = payment_types_dropdown.closest('.payment_row');
            var row_index = payment_row.find('.payment_row_index').val();

            var account_dropdown = payment_row.find('select#account_' + row_index);
            if (payment_type && payment_type != 'advance') {
                var default_account = default_accounts && default_accounts[payment_type]['account'] ?
                    default_accounts[payment_type]['account'] : '';
                if (account_dropdown.length && default_accounts) {
                    account_dropdown.val(default_account);
                    account_dropdown.change();
                }
            }

            if (payment_type == 'advance') {
                if (account_dropdown) {
                    account_dropdown.prop('disabled', true);
                    account_dropdown.closest('.form-group').addClass('hide');
                }
            } else {
                if (account_dropdown) {
                    account_dropdown.prop('disabled', false);
                    account_dropdown.closest('.form-group').removeClass('hide');
                }
            }
        });

        function set_payment_type_dropdown() {
            var payment_settings = $('#location_id').find(':selected').data('default_payment_accounts');
            payment_settings = payment_settings ? payment_settings : [];
            enabled_payment_types = [];
            for (var key in payment_settings) {
                if (payment_settings[key] && payment_settings[key]['is_enabled']) {
                    enabled_payment_types.push(key);
                }
            }
            if (enabled_payment_types.length) {
                $(".payment_types_dropdown > option").each(function() {
                    //skip if advance
                    if ($(this).val() && $(this).val() != 'advance') {
                        if (enabled_payment_types.indexOf($(this).val()) != -1) {
                            $(this).removeClass('hide');
                        } else {
                            $(this).addClass('hide');
                        }
                    }
                });
            }
        }


        // Function to initialize the vendor Select2
        function initVendorSelect2(preSearchTerm = '') {
            $('#vendor_id').select2({
                ajax: {
                    url: '/purchases/get_suppliers',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term, // search term
                            page: params.page,
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data,
                        };
                    },
                },
                minimumInputLength: 1,
                escapeMarkup: function(m) {
                    return m;
                },
                templateResult: function(data) {
                    if (!data.id) {
                        return data.text;
                    }
                    var html = data.text + ' - ' + data.business_name + ' (' + data.contact_id + ')';
                    return html;
                },
                language: {
                    noResults: function() {
                        var name = $('#vendor_id')
                            .data('select2')
                            .dropdown.$search.val();
                        return (
                            '<button type="button" data-name="' +
                            name +
                            '" class="btn btn-link add_new_vendor"><i class="fa fa-plus-circle fa-lg" aria-hidden="true"></i>&nbsp; ' +
                            __translate('add_name_as_new_vendor', {
                                name: name
                            }) +
                            '</button>'
                        );
                    },
                },
            }).on('select2:select', function(e) {
                var data = e.params.data;
                $('#payment_term_number').val(data.payment_term_number);
                $('#payment_term_type').val(data.payment_term_type);
                $('#vendor_balance_text').text(__currency_trans_from_en(data.balance), true);
                $('#vendor_balance').val(data.balance);
                set_vendor_address(data);
            });

            // If a pre-search term is provided, trigger the search after opening
            if (preSearchTerm) {
                $('#vendor_id').on('select2:open', function() {
                    // Get the search input
                    var searchInput = $('.select2-search__field');
                    // Set the value
                    searchInput.val(preSearchTerm);
                    // Trigger the search
                    searchInput.trigger('input');
                });

                // Open the dropdown to trigger the search
                $('#vendor_id').select2('open');
            }
        }

        // Initialize with a pre-search term
        $(document).ready(function() {
            // Initialize with "angle" as the pre-search term
            initVendorSelect2("{{ $supplier_business_name ?? '' }}");
        });


        // Function to initialize the product search autocomplete
        function initProductSearch(preSearchTerm = '') {
            if ($('#product_search').length > 0) {
                $('#product_search')
                    .autocomplete({
                        source: function(request, response) {
                            $.getJSON(
                                '/purchases/get_products', {
                                    location_id: $('#location_id').val(),
                                    term: request.term
                                },
                                response
                            );
                        },
                        minLength: 2,
                        response: function(event, ui) {
                            if (ui.content.length == 1) {
                                ui.item = ui.content[0];
                                $(this)
                                    .data('ui-autocomplete')
                                    ._trigger('select', 'autocompleteselect', ui);
                                $(this).autocomplete('close');
                            } else if (ui.content.length == 0) {
                                var term = $(this).data('ui-autocomplete').term;
                                swal({
                                    title: LANG.no_products_found,
                                    text: __translate('add_name_as_new_product', {
                                        term: term
                                    }),
                                    buttons: [LANG.cancel, LANG.ok],
                                }).then(value => {
                                    if (value) {
                                        var container = $('.quick_add_product_modal');
                                        $.ajax({
                                            url: '/products/quick_add?product_name=' + term,
                                            dataType: 'html',
                                            success: function(result) {
                                                $(container)
                                                    .html(result)
                                                    .modal('show');
                                            },
                                        });
                                    }
                                });
                            }
                        },
                        select: function(event, ui) {
                            $(this).val(null);
                            get_purchase_entry_row(ui.item.product_id, ui.item.variation_id);
                        },
                        open: function(event, ui) {
                            // If a pre-search term is provided, set it when the autocomplete opens
                            if (preSearchTerm) {
                                $(this).val(preSearchTerm);
                                $(this).autocomplete('search', preSearchTerm);
                            }
                        },
                    })
                    .autocomplete('instance')._renderItem = function(ul, item) {
                        return $('<li>')
                            .append('<div>' + item.text + '</div>')
                            .appendTo(ul);
                    };

                // If a pre-search term is provided, trigger the search
                if (preSearchTerm) {
                    $('#product_search').autocomplete('search', preSearchTerm);
                }
            }
        }

        // Initialize with a pre-search term from backend
        $(document).ready(function() {
            // Get the product name from backend (assuming it's passed as a Laravel variable)
            var productName = "{{ $product_name ?? 'L1016សាប៊ូទឹកយក្សាវ៉ាយ១លីត្រ' }}";

            // Initialize with the product name as the pre-search term
            if (productName) {
                initProductSearch(productName);
            } else {
                // Initialize without pre-search if no product name is provided
                initProductSearch();
            }
        });
    </script>
    @include('purchase.partials.keyboard_shortcuts')
@endsection
