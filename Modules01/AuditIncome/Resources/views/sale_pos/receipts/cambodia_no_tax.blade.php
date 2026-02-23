<style>
    .moul-font {
        font-family: "Moul", serif;
        font-weight: 200;
        font-style: normal;
    }
    .note {
        font-size: 10px;
    }
    .header-container {
        position: relative;
        text-align: center;
    }
    .logo {
        position: absolute;
        left: 20px;
        top: 40px;
        max-height: 120px;
        width: auto;
    }
    .address-details {
        text-align: left;
        margin-left: 200px; /* Adjust this value to align with the logo */
    }
    .table-invoice {
        border: 1px solid #000; /* Ensure overall table border */
    }
    /* .custom-checkbox {
            width: 15px;
            height: 15px;
            border: 2px solid #000;
            display: inline-block;
            margin-right: 0px;
            position: relative;
            cursor: pointer;
        } */
         .custom-checkbox-container {
        display: inline-flex;
        align-items: center;
    }
    .custom-checkbox {
        width: 15px;
        height: 15px;
        border: 2px solid #000;
        display: inline-block;
        margin-right: 0px;
        position: relative;
        cursor: pointer;
    }
    .signature-section {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .signature-section hr {
        width: 100%;
        margin-top: 10px;
        margin-bottom: 15px;
    }
    .signature-text {
        margin-top: -10px; /* Moves text closer to the line */
    }
    .bold-text{
        font-size: 15px;
        font-weight: bold;
    }
    
    .text-center {
    	flex-direction: row;

    }
    
    @media print and (max-width: 5.8in) { /* A5 width is approximately 5.8 inches */
        .header-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
        }
        .header-logo {
         position: relative;
        }
        .header-company-details {
            text-align: center;
            margin-left: 0; /* Reset any margins applied for larger screens */
        }
        
        .text-xs {
         	font-size:11px;
         	}
        
</style>



<div class="row" style="color: #000000 !important;">
    <div class="col-xs-12">
        <div class="header-container">
            <!-- Logo on the left -->
            @if(!empty(Session::get('business.logo')))
                <div class="header-logo">
                    <img src="{{ asset('uploads/business_logos/' . Session::get('business.logo')) }}" alt="Logo" style="width: auto; max-height: 100px;">
                </div>
            @endif

            <!-- Company name and details in the center -->
            <div class="header-company-details">
                <h1 class="moul-font">
                    @if(!empty($receipt_details->display_name))
                        {{$receipt_details->display_name}}
                    @endif
                </h1>
                <p class="text-xs">{{ $receipt_details->sub_heading_line1 }}</p>
                <p class="text-xs">{{ $receipt_details->sub_heading_line2 }}</p>
                <p class="text-xs">{{ $receipt_details->sub_heading_line3 }}</p>
                <p class="text-xs">{{ $receipt_details->sub_heading_line4 }}-{{ $receipt_details->sub_heading_line5 }}</p>
            </div>
            <div></div>  <!-- Empty div to push the company name to the center if no logo -->
        </div>
    </div>
</div>
        </div>
        <h3 class="text-center moul-font">
                វិក្កយបត្រ  <br/>
                <p style="font-size: 22px; margin-top: 8px;">INVOICE</p>
            </h3>

        <!-- Rest of the template remains the same -->
        <div class="row">
            <div class="col-xs-6">
                <p>
                    @if(!empty($receipt_details->customer_info))
                        អតិថិជន / Customer: {!! $receipt_details->customer_info !!} <br/>
                    @endif
                  
				
                </p>
                
                
            </div>
            <div class="col-xs-6 text-right">
                <p>
                    @if(!empty($receipt_details->invoice_no_prefix))
                    លេខរៀងវិក្កយបត្រ / Invoice №: <strong>{{ $receipt_details->invoice_no }}</strong>

                    @endif
                    
                </p>
                <p>
                    កាលបរិច្ឆេទ / Date: <strong>{{ $receipt_details->invoice_date }}</strong>

                </p>
                 <p>
                    អត្រាប្តូរប្រាក់ / Exchange Rate: <strong>{{ $receipt_details->exchange_rate }}</strong>

                </p>
                <div class="row" style="color: #000000 !important;">
	@if(!empty($receipt_details->footer_text))
	<div class="@if($receipt_details->show_barcode || $receipt_details->show_qr_code) col-xs-8 @else col-xs-12 @endif">
		{!! $receipt_details->footer_text !!}
	</div>
	@endif
	@if($receipt_details->show_barcode || $receipt_details->show_qr_code)
		<div class="@if(!empty($receipt_details->footer_text)) col-xs-4 @else col-xs-12 @endif text-center">
			@if($receipt_details->show_barcode)
				{{-- Barcode --}}
				<img class="center-block" src="data:image/png;base64,{{DNS1D::getBarcodePNG($receipt_details->invoice_no, 'C128', 2,30,array(39, 48, 54), true)}}">
			@endif
			
			@if($receipt_details->show_qr_code && !empty($receipt_details->qr_code_text))
				<img class="center-block mt-5" src="data:image/png;base64,{{DNS2D::getBarcodePNG($receipt_details->qr_code_text, 'QRCODE', 3, 3, [39, 48, 54])}}">
			@endif
		</div>
	@endif
</div>
            </div>
        </div>

        <table class="table table-bordered table-invoice" style="font-size: 12px;">
            <thead>
                <tr>
                    <th style="text-align: center;"> ល.រ <br/> №</th>
                    <th style="text-align: center;">បរិយាយមុខទំនិញ <br/> Description</th>
                    <th style="text-align: center;">ឯកតា<br/> Unit</th>
                    <th style="text-align: center;">បរិមាណ <br/> Quantity</th>
                    <th style="text-align: center;">ថ្លៃឯកតា <br/> Unit Price</th>
                    <th style="text-align: center;">ថ្លៃទំនិញ <br/> Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($receipt_details->lines as $index => $line)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td style="text-align: left;">
                            {{$line['name']}} {{$line['product_variation']}} {{$line['variation']}} 
                            @if(!empty($line['sub_sku'])), {{$line['sub_sku']}} @endif 
                            @if(!empty($line['brand'])), {{$line['brand']}} @endif
                        </td>
                         <td style="text-align: center;"> {{$line['units']}}</td>
                        <td style="text-align: center;">{{$line['quantity']}}</td>
                        <td style="text-align: center;" >{{$line['unit_price_inc_tax']}} </td>
                        <td style="text-align: center;">{{$line['line_total']}}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No items found</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-right">សរុប / Sub Total</td>
                    <td class="bold-text">{{$receipt_details->subtotal}}</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right">​អាករលើតម្លៃបន្ថែម / VAT</td>
                    <td class="bold-text">{{$receipt_details->tax}}</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right">​បញ្ចុះតម្លៃ / Discount </td>
                    <td class="bold-text"> {{$receipt_details->discount}}</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right">សរុបរួម / Grand Total<b></b></td>
                    <td class="bold-text">{{$receipt_details->total}}</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right">ទឹកប្រាក់រៀល / Net Amount KHR<b></b></td>
                    
                  @php
    setlocale(LC_NUMERIC, 'en_US'); 

    $total = $receipt_details->total;
    $exchange_rate = $receipt_details->exchange_rate ?? 0;

    // Sanitize the input and cast to float.
    $sanitized_total = (float) filter_var($total, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    // Calculate converted total and round to nearest integer.
    $converted_total = round($sanitized_total * $exchange_rate);
@endphp

<td class="bold-text">
    KHR {{ $total ? number_format($converted_total, 0, '.', ',') : 0 }} 
</td>
		
                </tr>
            </tfoot>
        </table>
      <div class="table-responsive">
      
      
    <table class="table table-borderless" style="width: 100%;">
        <tr>
            <td class="text-center" style="width: 50%; vertical-align: top;">
               
            </td>
            <td class="text-center" style="width: 50%; vertical-align: top; padding-left: 50px;">
                <br/><br/><br/>
                {{$receipt_details->display_name}}
                <hr/>
                <div class="signature-text">
                    <p>ហត្ថលេខានិងឈ្មោះអ្នកលក់</p>
                    <p>Seller's Signature & Name</p>
                </div>
            </td>
        </tr>
    </table>
</div>