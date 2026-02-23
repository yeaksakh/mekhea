<!-- business information here -->
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <!-- <link rel="stylesheet" href="style.css"> -->
        <span class="headings"> <title>Receipt-{{$receipt_details->invoice_no}}</title></span> 
    </head>
    <body>
    <div class="centered textbox-info">
				<p class="width-40 f-left">
					@if(!empty($receipt_details->logo))<img style="width: 70px;margin-bottom: 10px;" src="{{$receipt_details->logo}}" >@endif
					@if(!empty($receipt_details->letter_head))<img style="width: 70px;margin-bottom: 10px;" src="{{$receipt_details->letter_head}}"><br>@endif
					
				</p>
				
				<p class="center width-60 f-right">
					<span class="headings">{{$receipt_details->display_name}}</span><br>
					<small>{!! $receipt_details->contact !!}</small><br/>
				<span class="sub-headings">{!! $receipt_details->invoice_heading !!}</br></span> 
					
				</p>
			</div>

				<p class="width-40 f-right">
				@if($receipt_details->show_qr_code && !empty($receipt_details->qr_code_text))<img style="width: 80%;" src="data:image/png;base64,{{DNS2D::getBarcodePNG($receipt_details->qr_code_text, 'QRCODE')}}">@endif
				</p>
				<p class="width-60 f-left">
				<span class="headings"><strong>CODE: </strong> @if(!empty($receipt_details->additional_notes)){!! nl2br($receipt_details->additional_notes) !!}</br> @endif</span>	
				No.: {{$receipt_details->invoice_no}}</br>
				{{$receipt_details->invoice_date}}</br>
				<strong>Status: </strong>{{$receipt_details->shipping_status}}</br>
				@if(!empty($receipt_details->customer_rp_label)){{ $receipt_details->customer_rp_label }}  : <strong>{{ $receipt_details->customer_total_rp }}</strong>	@endif
				</p>
							
</div>			

            <table style="margin-top: 5px !important" class="border-bottom width-100 table-f-12 mb-10">
                <thead class="border-bottom-dotted">
                    <tr>
                        <th class="serial_number">#</th>
                        <th class="description" width="30%">
                        	{{$receipt_details->table_product_label}}
                        </th>
                        <th class="quantity text-right">
                        	{{$receipt_details->table_qty_label}}
                        </th>
                        <th class="quantity text-right">
                        	Total
                        </th>
                        
                    </tr>
                </thead>
                  
                <tbody>
                	@forelse($receipt_details->lines as $line)
	                    <tr>
	                        <td class="serial_number" style="vertical-align: top;">
	                        	{{$loop->iteration}}
	                        </td>
	                        <td class="description">{{$line['name']}}</td>
	                        <td class="quantity text-right">{{$line['quantity']}} </td>
	                        <td class="price text-right">{{$line['line_total']}}</td>
	                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            
			@if(empty($receipt_details->hide_price))
                

                <!-- Shipping Charges -->
				@if(!empty($receipt_details->shipping_charges))
					<div class="flex-box">
						<p class="left text-right">
							{!! $receipt_details->shipping_charges_label !!}
						</p>
						<p class="width-50 text-right">
							{{$receipt_details->shipping_charges}}
						</p>
					</div>
				@endif

				<div class="flex-box">
					<p class="width-50 text-left​ sub-heading">
						{!! $receipt_details->total_quantity_label !!}:<span class="headings">{{$receipt_details->total_quantity}}</span>
					</p>
					<p class="width-50 text-right ">
						{!! $receipt_details->total_label !!}: <span class="headings">{{$receipt_details->total}}</span>	
					</p>
				</div>

						<div class="flex-box">
							<p class="width-50 text-left">@if(!empty($receipt_details->payments))@foreach($receipt_details->payments as $payment){{$payment['method']}}{{$payment['amount']}}</p>@endforeach	@endif
							<p class="width-50 text-right">@if(!empty($receipt_details->total_paid)){!! $receipt_details->total_paid_label !!}:{{$receipt_details->total_paid}}@endif</p>
						</div>
					

				<!-- Total Due-->
				
					<div class="flex-box">
						<p class="width-50 text-left">@if(!empty($receipt_details->all_due))	{!! $receipt_details->all_bal_label !!}:	{{$receipt_details->all_due}}@endif</p>
						<p class="width-50 text-right">@if(!empty($receipt_details->total_due) && !empty($receipt_details->total_due_label)){!! $receipt_details->total_due_label !!}:{{$receipt_details->total_due}}@endif</p>
					</div>
				
			@endif
            {{-- <div class="border-bottom width-100">&nbsp;</div> --}}
    
	
			@if(!empty($receipt_details->footer_text))
				<p class="centered">
					<strong>{!! $receipt_details->footer_text !!}</strong>
				</p>
			@endif
			

        
        
        
        
 <div class="border-bottom-dotted width-100 page-break">&nbsp;</div>

      <p class="centered">
				
      <span class="headings">{!! $receipt_details->contact !!}</span> <br>
      </p>
       
   
				
             <table style="margin-top: 5px !important" class="border-bottom width-100 table-f-12 mb-10">
                <thead class="border-bottom-dotted">
                <tbody>
                	@forelse($receipt_details->lines as $line)
	                    <tr>
	                        <td class="serial_number" style="vertical-align: top;">
	                        	{{$loop->iteration}}
	                        </td>
	                        <td class="description">{{$line['name']}}</td>
	                        <td class="quantity text-right">{{$line['quantity']}} </td>
	                        <td class="price text-right">{{$line['line_total']}}</td>
	                    </tr>
                    @endforeach
                </tbody>
            </table>
                <p class="width-50 f-right"><span class="headings"><strong>CODE: </strong> @if(!empty($receipt_details->additional_notes)){!! nl2br($receipt_details->additional_notes) !!}</br> @endif</span></p>
		<p class="width-50 f-left"><span class="headings">No.: {{$receipt_details->invoice_no}}</span>	</p>
        
				<p class="width-40 f-left">
				@if($receipt_details->show_qr_code && !empty($receipt_details->qr_code_text))<img style="width: 80px;" src="data:image/png;base64,{{DNS2D::getBarcodePNG($receipt_details->qr_code_text, 'QRCODE')}}">@endif
				</p>
				<p class="width-60 f-right">
				{{$receipt_details->invoice_date}}</br>
				@if(!empty($receipt_details->total_paid))Paid:<span class="headings"> {{$receipt_details->total_paid}}</br></span>@endif
				@if(!empty($receipt_details->all_due))	{!! $receipt_details->all_bal_label !!}:	<span class="headings">{{$receipt_details->all_due}}</br></span>@endif
				@if(!empty($receipt_details->total_due) && !empty($receipt_details->total_due_label)){!! $receipt_details->total_due_label !!}:<span class="headings">{{$receipt_details->total_due}}</span></br>@endif
				@if(!empty($receipt_details->payments))@foreach($receipt_details->payments as $payment){{$payment['amount']}}@endforeach	</br>@endif
				<strong>Staff: </strong>{{$receipt_details->commission_agent}}</br>
				Cus: {!! $receipt_details->customer_info !!}
				</p>
				</div>
			 
						
					
					
					
					
			
             
		
			
				
				
        <!-- <button id="btnPrint" class="hidden-print">Print</button>
        <script src="script.js"></script> -->
    </body>
</html>

<style type="text/css">
.f-8 {
	font-size: 8px !important;
}
body {
	color: #000000;
}
@media print {
	* {
    	font-size: 12px;
    	font-family: 'Times New Roman';
    	word-break: break-all;
	}
	.f-8 {
		font-size: 8px !important;
	}

.headings{
	font-size: 16px;
	font-weight: 700;
	text-transform: uppercase;
}

.sub-headings{
	font-size: 15px;
	font-weight: 700;
}

.border-top{
    border-top: 1px solid #242424;
}
.border-bottom{
	border-bottom: 1px solid #242424;
}

.border-bottom-dotted{
	border-bottom: 1px dotted darkgray;
}

td.serial_number, th.serial_number{
	width: 5%;
    max-width: 5%;
}

td.description,
th.description {
    width: 35%;
    max-width: 35%;
}

td.quantity,
th.quantity {
    width: 15%;
    max-width: 15%;
    word-break: break-all;
}
td.unit_price, th.unit_price{
	width: 25%;
    max-width: 25%;
    word-break: break-all;
}

td.price,
th.price {
    width: 20%;
    max-width: 20%;
    word-break: break-all;
}

.centered {
    text-align: center;
    align-content: center;
}

.ticket {
    width: 100%;
    max-width: 100%;
}

img {
    max-width: inherit;
    width: auto;
}

    .hidden-print,
    .hidden-print * {
        display: none !important;
    }
}
.table-info {
	width: 100%;
}
.table-info tr:first-child td, .table-info tr:first-child th {
	padding-top: 8px;
}
.table-info th {
	text-align: left;
}
.table-info td {
	text-align: right;
}
.logo {
	float: left;
	width:35%;
	padding: 10px;
}

.text-with-image {
	float: left;
	width:65%;
}
.text-box {
	width: 100%;
	height: auto;
}
.m-0 {
	margin:0;
}
.textbox-info {
	clear: both;
}
.textbox-info p {
	margin-bottom: 0px
}
.flex-box {
	display: flex;
	width: 100%;
}
.flex-box p {
	width: 50%;
	margin-bottom: 0px;
	white-space: nowrap;
}

.table-f-12 th, .table-f-12 td {
	font-size: 12px;
	word-break: break-word;
}

.bw {
	word-break: break-word;
}
.bb-lg {
	border-bottom: 1px solid lightgray;
}
.page-break {
	/* page-break-before: always; */
	page-break-after: always;
}
</style>