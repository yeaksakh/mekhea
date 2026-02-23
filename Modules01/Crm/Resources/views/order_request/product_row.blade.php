@php
	$common_settings = session()->get('business.common_settings');

	$product_name = $product->product_name;
	if(!empty($product->brand)){ $product_name .= ' ' . $product->brand ;}
@endphp
<tr class="product_row" data-row_index="{{$row_count}}">
    <td style="padding: 5px; vertical-align: middle;">
		<div style="display: flex; align-items: center; gap: 12px;">
			{{-- product image --}}
			@if(!empty($product->image_url) && !str_contains($product->image_url, 'default.png'))
				<img src="{{ $product->image_url }}" alt="Product image" class="img-responsive" style="height: 50px; width: 50px; object-fit: cover; border-radius: 50%">
			@else
				<div style="height: 50px; width: 50px; background-color: #eee; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
					<i class="fa fa-user" style="font-size: 30px; color: #888;"></i>
				</div>
			@endif
			<div>
				{{-- product name --}}
				<div style="font-weight: 600; font-size: 15px; margin-bottom: 4px;">{!! $product_name !!}</div>
				<input type="hidden" class="enable_sr_no" value="{{$product->enable_sr_no}}">
				<input type="hidden"
					class="product_type"
					name="products[{{$row_count}}][product_type]"
					value="{{$product->product_type}}">

				@php
					$hide_tax = 'hide';

					$tax_id = $product->tax_id;
					$item_tax = !empty($product->item_tax) ? $product->item_tax : 0;
					$unit_price_inc_tax = $product->sell_price_inc_tax;


					$discount_type = !empty($product->line_discount_type) ? $product->line_discount_type : 'fixed';
					$discount_amount = !empty($product->line_discount_amount) ? $product->line_discount_amount : 0;

					$sell_line_note = '';
					if(!empty($product->sell_line_note)){
						$sell_line_note = $product->sell_line_note;
					}
				@endphp

				@php
					$max_quantity = $product->qty_available;
					$formatted_max_quantity = $product->formatted_qty_available;
					$max_qty_rule = $max_quantity;
					$max_qty_msg = __('validation.custom-messages.quantity_not_available', ['qty'=> $formatted_max_quantity, 'unit' => $product->unit  ]);
				@endphp
				<textarea class="form-control hide" name="products[{{$row_count}}][sell_line_note]" rows="2">{{$sell_line_note}}</textarea>
				<p class="help-block hide"><small>@lang('lang_v1.sell_line_description_help')</small></p>

				{{-- qty controls --}}
				<input type="hidden" name="products[{{$row_count}}][product_id]" class="form-control product_id" value="{{$product->product_id}}">

				<input type="hidden" value="{{$product->variation_id}}"
					name="products[{{$row_count}}][variation_id]" class="row_variation_id">

				<input type="hidden" value="{{$product->enable_stock}}"
					name="products[{{$row_count}}][enable_stock]">

				@php
					$multiplier = 1;
					$allow_decimal = true;
					if($product->unit_allow_decimal != 1) {
						$allow_decimal = false;
					}
				@endphp
				@foreach($sub_units as $key => $value)
					@if(!empty($product->sub_unit_id) && $product->sub_unit_id == $key)
						@php
							$multiplier = $value['multiplier'];

							if($value['allow_decimal']) {
								$allow_decimal = true;
							}
						@endphp
					@endif
				@endforeach
				<div style="display: flex; align-items: center; margin-top: 5px;" class="input-group input-number">
					<span style="margin-right: 25px;" class="input-group-btn"><button style="background-color: #f0f0f0; border: none; width: 26px; height: 26px; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-weight: bold; font-size: 16px;" type="button" class="btn btn-default btn-flat"><i class="fa fa-minus text-danger quantity-down"></i><i class="fas fa-trash text-danger pos_remove_row" aria-hidden="true"></i></button></span>
					<input style="min-width: 50px; max-width: 65px; margin: 0 10px; text-align: center; font-weight: 500; padding: 2px 5px; border: none;" type="text" data-min="1"
						class="form-control pos_quantity input_number mousetrap input_quantity"
						value="{{@format_quantity($product->quantity_ordered)}}" name="products[{{$row_count}}][quantity]" data-allow-overselling="@if(empty($pos_settings['allow_overselling'])){{'false'}}@else{{'true'}}@endif"
						@if($allow_decimal)
							data-decimal=1
						@else
							data-decimal=0
							data-rule-abs_digit="true"
							data-msg-abs_digit="@lang('lang_v1.decimal_value_not_allowed')"
						@endif
						data-rule-required="true"
						data-msg-required="@lang('validation.custom-messages.this_field_is_required')"
					>
					<span class="input-group-btn"><button style="background-color: #f0f0f0; border: none; width: 26px; height: 26px; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-weight: bold; font-size: 16px;" type="button" class="btn btn-default btn-flat quantity-up"><i class="fa fa-plus text-success"></i></button></span>
				</div>

				<input type="hidden" name="products[{{$row_count}}][product_unit_id]" value="{{$product->unit_id}}">
				<div style="display: none;">
					@if(count($sub_units) > 0)
						<br>
						<select name="products[{{$row_count}}][sub_unit_id]" class="form-control input-sm sub_unit">
							@foreach($sub_units as $key => $value)
								<option value="{{$key}}" data-multiplier="{{$value['multiplier']}}" data-unit_name="{{$value['name']}}" data-allow_decimal="{{$value['allow_decimal']}}" @if(!empty($product->sub_unit_id) && $product->sub_unit_id == $key) selected @endif>
									{{$value['name']}}
								</option>
							@endforeach
						</select>
					@else
						{{$product->unit}}
					@endif
				</div>

				<input type="hidden" class="base_unit_multiplier" name="products[{{$row_count}}][base_unit_multiplier]" value="{{$multiplier}}">

				<input type="hidden" class="hidden_base_unit_sell_price" value="{{$product->default_sell_price / $multiplier}}">

				{{-- Hidden fields for combo products --}}
				@if($product->product_type == 'combo'&& !empty($product->combo_products))

					@foreach($product->combo_products as $k => $combo_product)

						@php
							$qty_total = $combo_product['qty_required'];
						@endphp

						<input type="hidden"
							name="products[{{$row_count}}][combo][{{$k}}][product_id]"
							value="{{$combo_product['product_id']}}">

							<input type="hidden"
							name="products[{{$row_count}}][combo][{{$k}}][variation_id]"
							value="{{$combo_product['variation_id']}}">

							<input type="hidden"
							class="combo_product_qty"
							name="products[{{$row_count}}][combo][{{$k}}][quantity]"
							data-unit_quantity="{{$combo_product['qty_required']}}"
							value="{{$qty_total}}">

					@endforeach
				@endif
			</div>
		</div>
    </td>
		@php
			$pos_unit_price = !empty($product->unit_price_before_discount) ? $product->unit_price_before_discount : $product->default_sell_price;
		@endphp
		<td class="hide">
			<input type="text" name="products[{{$row_count}}][unit_price]" class="form-control pos_unit_price input_number mousetrap" value="{{@num_format($pos_unit_price)}}" @if(!empty($pos_settings['enable_msp'])) data-rule-min-value="{{$pos_unit_price}}" data-msg-min-value="{{__('lang_v1.minimum_selling_price_error_msg', ['price' => @num_format($pos_unit_price)])}}" @endif>
		</td>
		<td class="hide">
			{!! Form::text("products[$row_count][line_discount_amount]", @num_format($discount_amount), ['class' => 'form-control input_number row_discount_amount']); !!}<br>
			{!! Form::select("products[$row_count][line_discount_type]", ['fixed' => __('lang_v1.fixed'), 'percentage' => __('lang_v1.percentage')], $discount_type , ['class' => 'form-control row_discount_type']); !!}
			@if(!empty($discount))
				<p class="help-block">{!! __('lang_v1.applied_discount_text', ['discount_name' => $discount->name, 'starts_at' => $discount->formated_starts_at, 'ends_at' => $discount->formated_ends_at]) !!}</p>
			@endif
		</td>
		<td class="text-center hide">
			{!! Form::hidden("products[$row_count][item_tax]", @num_format($item_tax), ['class' => 'item_tax']); !!}
		
			{!! Form::select("products[$row_count][tax_id]", $tax_dropdown['tax_rates'], $tax_id, ['placeholder' => 'Select', 'class' => 'form-control tax_id'], $tax_dropdown['attributes']); !!}
		</td>
	<td style="padding: 5px; vertical-align: middle;" class="text-center v-center">
		{{-- @format_currency($unit_price_inc_tax) --}}
		<input type="hidden" name="products[{{$row_count}}][unit_price_inc_tax]" class="form-control pos_unit_price_inc_tax input_number" value="{{@num_format($unit_price_inc_tax)}}" @if(!empty($pos_settings['enable_msp'])) data-rule-min-value="{{$unit_price_inc_tax}}" data-msg-min-value="{{__('lang_v1.minimum_selling_price_error_msg', ['price' => @num_format($unit_price_inc_tax)])}}" @endif>
		<input type="hidden" class="form-control pos_line_total @if(!empty($pos_settings['is_pos_subtotal_editable'])) input_number @endif" value="{{@num_format($product->quantity_ordered*$unit_price_inc_tax )}}">
		<span class="display_currency pos_line_total_text" data-currency_symbol="true">{{$product->quantity_ordered*$unit_price_inc_tax}}</span>
	</td>
</tr>
<script>
    $(document).ready(function() {
        function toggleRemoveIcon(row) {
            var quantityInput = row.find('.pos_quantity');
            var quantity = __read_number(quantityInput);
            var quantityDownIcon = row.find('.quantity-down');
            var removeIcon = row.find('.pos_remove_row');

            if (quantity <= 1) {
                quantityDownIcon.hide();
                removeIcon.show();
            } else {
                quantityDownIcon.show();
                removeIcon.hide();
            }
        }

        function initializeRow(row) {
            toggleRemoveIcon(row);
            row.find('.pos_remove_row').show();
        }

        // Initial setup for existing rows
        $('tr.product_row').each(function() {
            initializeRow($(this));
        });

        // Delegated events for existing and future rows
        $(document).on('change keyup', '.pos_quantity', function() {
            var row = $(this).closest('tr.product_row');
            // Use a short timeout to allow other scripts to update the quantity value
            setTimeout(function() {
                toggleRemoveIcon(row);
            }, 0);
        });

        $(document).on('click', '.quantity-up, .quantity-down', function() {
            var row = $(this).closest('tr.product_row');
            // Use a short timeout to allow other scripts to update the quantity value
            setTimeout(function() {
                toggleRemoveIcon(row);
            }, 0);
        });

        // Handle dynamically added rows by observing the table body
        var targetNode = document.querySelector('table#pos_table tbody');
        if (targetNode) {
            var config = { childList: true };

            var callback = function(mutationsList, observer) {
                for(var mutation of mutationsList) {
                    if (mutation.type === 'childList') {
                        mutation.addedNodes.forEach(function(node) {
                            if ($(node).hasClass('product_row')) {
                                initializeRow($(node));
                            }
                        });
                    }
                }
            };

            var observer = new MutationObserver(callback);
            observer.observe(targetNode, config);
        }
    });
</script>

<style>
	.pos_remove_row {
		display: none;
	}
</style>