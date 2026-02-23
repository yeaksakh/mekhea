<table style="width:100%;">
	<thead>
		<tr>
			<td>
				<p class="text-right color-555 font-30">
					<b>@lang('lang_v1.delivery_note')</b>
				</p>
			</td>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>
				<!-- business information here -->
				<div class="row invoice-info">
					<div class="col-md-6 invoice-col width-50 color-555">
						<!-- Logo -->
						@if(!empty($receipt_details->logo))
							<img style="max-height: 120px; width: auto;" src="{{$receipt_details->logo}}" class="img">
							<br />
						@endif

						<!-- Shop & Location Name  -->
						@if(!empty($receipt_details->display_name))
							<p><span style="font-size:24px; font-weight:900; color:black;">
									{{$receipt_details->display_name}}</span>
								@if(!empty($receipt_details->address))
									<br />{!! $receipt_details->address !!}
								@endif

								@if(!empty($receipt_details->contact))
									<br />{!! $receipt_details->contact !!}
								@endif

								@if(!empty($receipt_details->website))
									<br />{{ $receipt_details->website }}
								@endif

								@if(!empty($receipt_details->tax_info1))
									<br />{{ $receipt_details->tax_label1 }} {{ $receipt_details->tax_info1 }}
								@endif

								@if(!empty($receipt_details->tax_info2))
									<br />{{ $receipt_details->tax_label2 }} {{ $receipt_details->tax_info2 }}
								@endif

								@if(!empty($receipt_details->location_custom_fields))
									<br />{{ $receipt_details->location_custom_fields }}
								@endif
							</p>
						@endif
					</div>

					<div class="col-md-6 invoice-col width-50">
						<p class="text-right font-17">
							@if(!empty($receipt_details->invoice_no_prefix))
								<span class="pull-left">{!! $receipt_details->invoice_no_prefix !!}</span>
							@endif
							{{$receipt_details->invoice_no}}
						</p>
						<!-- Date-->
						@if(!empty($receipt_details->date_label))
							<p class="text-right font-17">
								<span class="pull-left">
									{{$receipt_details->date_label}}
								</span>
								{{$receipt_details->invoice_date}}
							</p>
						@endif
					</div>
					<div class="col-md-6 invoice-col width-50 word-wrap">
						@if(!empty($receipt_details->customer_label))
							<b>{{ $receipt_details->customer_label }}</b><br />
						@endif

						@if(!empty($receipt_details->customer_info))
							{!! $receipt_details->customer_info !!}
						@endif
						@if(!empty($receipt_details->client_id_label))
							<br />
							<strong>{{ $receipt_details->client_id_label }}</strong> {{ $receipt_details->client_id }}
						@endif
						@if(!empty($receipt_details->customer_tax_number))
							<br />
							<strong>{{ $receipt_details->customer_tax_label }}</strong>
							{{ $receipt_details->customer_tax_number }}
						@endif
						@if(!empty($receipt_details->customer_custom_fields))
							<br />{!! $receipt_details->customer_custom_fields !!}
						@endif
						@if(!empty($receipt_details->sales_person_label))
							<br />
							<strong>{{ $receipt_details->sales_person_label }}</strong> {{ $receipt_details->sales_person }}
						@endif
					</div>
				</div>

				<div class="row color-555">
					<div class="col-xs-12">
						<br />
						<table class="table table-bordered table-no-top-cell-border" id="product-table">
							<thead>
								<tr style="background-color: white !important; color: black !important; font-size: 20px !important"
									class="table-no-side-cell-border table-no-top-cell-border text-center">
									<td style="background-color: white !important; color: black !important; width: 5% !important">
										#
									</td>
									<td style="background-color: white !important; color: black !important; width: 65% !important">
										{{$receipt_details->table_product_label}}
									</td>
									<td style="background-color: white !important; color: black !important; width: 30% !important;">
										{{$receipt_details->table_qty_label}}
									</td>
								</tr>
							</thead>
							<tbody>
								@foreach($receipt_details->lines as $line)
									<tr>
										<td class="text-center">{{ $loop->iteration }}</td>
										<td>
											<strong>{{ $line['name'] }}</strong>
											@if($line['variation'] != 'DUMMY' && !empty($line['variation']))
												- {{ $line['variation'] }}
											@endif
											@if(!empty($line['sub_sku']))
												({{ $line['sub_sku'] }})
											@endif
											@if(!empty($line['brand']))
												, {{ $line['brand'] }}
											@endif
											@if(!empty($line['product_custom_fields']))
												, {{ $line['product_custom_fields'] }}
											@endif
											@if(!empty($line['sell_line_note']))
												<br><small>{{ $line['sell_line_note'] }}</small>
											@endif
										</td>
										<td class="text-right">
											<strong>{{ $line['quantity'] }} {{ $line['units'] }}</strong>
										</td>
									</tr>

									@if(!empty($line['combo_components']))
										<!-- Combo Components -->
										@foreach($line['combo_components'] as $component)
											<tr class="combo-component">
												<td class="text-center">⋄</td>
												<td style="padding-left: 20px;">
													<span class="combo-item">
														<strong>▸</strong> {{ $component['name'] }}
														@if($component['variation'] != 'DUMMY' && !empty($component['variation']))
															- {{ $component['variation'] }}
														@endif
														@if(!empty($component['sub_sku']))
															({{ $component['sub_sku'] }})
														@endif
														@if(!empty($component['brand']))
															, {{ $component['brand'] }}
														@endif
														@if(!empty($component['custom_fields']))
															, {{ $component['custom_fields'] }}
														@endif
													</span>
												</td>
												<td class="text-right">
													<span class="combo-item">
														{{ $component['quantity'] }} {{ $component['unit'] }}
													</span>
												</td>
											</tr>
										@endforeach
									@endif

									@if(!empty($line['modifiers']))
										<!-- Modifiers -->
										@foreach($line['modifiers'] as $modifier)
											<tr class="modifier">
												<td class="text-center">⋄</td>
												<td style="padding-left: 20px;">
													<span class="modifier-item">
														<strong>+</strong> {{ $modifier['name'] }}
														@if($modifier['variation'] != 'DUMMY' && !empty($modifier['variation']))
															- {{ $modifier['variation'] }}
														@endif
														@if(!empty($modifier['sub_sku']))
															({{ $modifier['sub_sku'] }})
														@endif
													</span>
												</td>
												<td class="text-right">
													<span class="modifier-item">
														{{ $modifier['quantity'] }} {{ $modifier['unit'] }}
													</span>
												</td>
											</tr>
										@endforeach
									@endif
								@endforeach
							</tbody>
						</table>
					</div>
				</div>

				<div class="row invoice-info color-555" style="page-break-inside: avoid !important">
					<div class="col-md-6 invoice-col width-50">
						<b class="pull-left">@lang('lang_v1.above_mentioned_items_received_in_good_condition')</b>
					</div>
				</div>
				</br>
				<div class="row invoice-info color-555" style="page-break-inside: avoid !important">
					<div class="col-md-6 invoice-col width-80">
						<b class="pull-left">@lang('lang_v1.received_by') : </b>
					</div>
				</div>
				</br>
				<div class="row invoice-info color-555" style="page-break-inside: avoid !important">
					<div class="col-md-6 invoice-col width-50">
						<b class="pull-left">@lang('lang_v1.date'):</b>
					</div>
				</div>
				</br>
				<div class="row invoice-info color-555" style="page-break-inside: avoid !important">
					<div class="col-md-6 invoice-col width-50">
						<b class="pull-left">@lang('lang_v1.authorized_signatory')</b>
					</div>
				</div>

				{{-- Barcode --}}
				@if($receipt_details->show_barcode)
					<br>
					<div class="row">
						<div class="col-xs-12">
							<img class="center-block"
								src="data:image/png;base64,{{DNS1D::getBarcodePNG($receipt_details->invoice_no, 'C128', 2, 30, array(39, 48, 54), true)}}">
						</div>
					</div>
				@endif

				@if(!empty($receipt_details->footer_text))
					<div class="row color-555">
						<div class="col-xs-12">
							{!! $receipt_details->footer_text !!}
						</div>
					</div>
				@endif
			</td>
		</tr>
	</tbody>
</table>

<style type="text/css">
    /* General styling */
    body {
        font-family: Arial, sans-serif;
    }
    
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .table-bordered td, .table-bordered th {
        border: 1px solid #000;
    }
    
    .table-no-top-cell-border tr:first-child td {
        border-top: none;
    }
    
    .text-center {
        text-align: center;
    }
    
    .text-right {
        text-align: right;
    }
    
    .pull-left {
        float: left;
    }
    
    .color-555 {
        color: #555;
    }
    
    .font-30 {
        font-size: 30px;
    }
    
    .font-17 {
        font-size: 17px;
    }
    
    .width-50 {
        width: 50%;
    }
    
    .width-80 {
        width: 80%;
    }
</style>

<style type="text/css" media="print">
    /* Printer-friendly overrides */
    * {
        color: black !important;
        background: transparent !important;
        box-shadow: none !important;
        text-shadow: none !important;
    }
    
    body {
        background: white !important;
    }
    
    /* Make all text black for print */
    .color-555, 
    .font-30, 
    .font-17,
    p, span, td, th, div, b, strong, small {
        color: black !important;
    }
    
    /* Table styling for print */
    .table {
        border-collapse: collapse !important;
        width: 100% !important;
    }
    
    .table td, .table th {
        border: 1px solid black !important;
        padding: 5px !important;
        background-color: transparent !important;
    }
    
    /* Header row - ensure visibility with !important */
    #product-table thead tr td {
        background-color: black !important;
        color: white !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        font-weight: bold !important;
    }
    
    /* Product rows */
    #product-table tbody tr td {
        border: 1px solid black !important;
        color: black !important;
        font-weight: normal !important;
    }
    
    /* Make sure product names are bold and clearly visible */
    #product-table tbody tr td strong {
        font-weight: bold !important;
        color: black !important;
    }
    
    /* Enhanced combo and modifier items */
    .combo-item, .modifier-item {
        font-size: 14px !important;
        color: black !important;
        font-weight: normal !important;
        display: block !important;
        line-height: 1.5 !important;
    }
    
    .combo-component td, .modifier td {
        border: 1px solid black !important;
        border-top: 1px dashed black !important;
        padding: 5px !important;
    }
    
    /* Make the arrows and plus signs very visible */
    .combo-item strong, .modifier-item strong {
        font-size: 16px !important;
        font-weight: bold !important;
        color: black !important;
    }
    
    /* Ensure barcode is visible */
    img.center-block {
        filter: contrast(200%) brightness(0%);
        max-width: 100%;
    }
    
    /* Logo handling */
    .img {
        filter: grayscale(100%) contrast(120%);
        max-width: 100%;
    }
    
    /* Make important text elements bold */
    .invoice-info b, 
    .invoice-info strong,
    .pull-left {
        font-weight: bold !important;
        color: black !important;
    }
}
</style>