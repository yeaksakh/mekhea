<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('messages.view') @lang('essentials::lang.leave')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
            	
                <div class="col-md-12">
             
                   
                    <div class="text-center">
                         @if(!empty(Session::get('business.logo')))
			                  <img src="{{ asset( 'uploads/business_logos/' . Session::get('business.logo') ) }}" alt="Logo" style="width: auto; max-height: 50px; margin: auto;">
			                @endif
			               
			                	<strong class="font-23">
			                		{{Session::get('business.name') ?? ''}}
			                	</strong>
			                	<br>
			                	{!!Session::get('business.business_address') ?? ''!!}
                  <br>
                            <strong>No:{{ $leave->ref_no }}</strong>
                       
                         <br>
                    <hr>
                    <h4 class="text-center w-100">លិខិតសុំច្បាប់សម្រាកការងារ </h4>
		    <hr>
                    <div class="row">
                    <div class="col-md-12">
                            <strong>@lang('essentials::lang.leave_type'):</strong>&nbsp;{{ $leave->leave_type }}
                    </div>
                        
                        <div class="col-md-12">                             
                            {{ $leave->user }}<br>
                            <span> <strong>@lang('essentials::lang.departments'):</strong> {{ $departments->name ?? '-' }} </span>
                            <span><strong>@lang('essentials::lang.designations'):</strong> {{ $designations->name ?? '-' }} </span
                        </div>
                    
                     <div class="col-md-12">
    <strong>@lang('essentials::lang.start_date'):</strong>
    {{ \Carbon\Carbon::parse($leave->start_date)->format('d-m-Y') }}

    <span><strong>@lang('essentials::lang.end_date'):</strong>
    {{ \Carbon\Carbon::parse($leave->end_date)->format('d-m-Y') }}</span>
</div>
<div>
    <strong>Total days:</strong> 
    {{ \Carbon\Carbon::parse($leave->start_date)->diffInDays(\Carbon\Carbon::parse($leave->end_date)) + 1 }}
</div>


                        <div class="col-md-12"> <strong>@lang('essentials::lang.reason'):</strong><br>{{ $leave->reason ?? '-' }} </div>
                        
                        <div><span class="label {{ $leave_statuses[$leave->status]['class'] }} float-end">&nbsp; &nbsp; {{ $leave_statuses[$leave->status]['name'] }}  </span>
                        <br>
    			@if($leave->status_note) <strong>@lang('essentials::lang.status_note'):</strong><br>{{ $leave->status_note }} @endif </div>
            <br>
            <br>
            <br>
            <br>
            </div>
          <table style="width: 100%; text-align: center; border-collapse: collapse;">
    <tr>
        <td style="vertical-align: middle;">
            ----------<br>
            {{ $leave->user }}
        </td>
        <td style="vertical-align: middle;">
            ----------<br>
            ប្រធានផ្នែក
        </td>
        <td style="vertical-align: middle;">
            ----------<br>
            ប្រធានផ្នែកធនធានមនុស្ស
        </td>
    </tr>
</table>


        </div>
       <div class="modal-footer no-print">
	      	<button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white" aria-label="Print" onclick="$(this).closest('div.modal-content').find('.modal-body').printThis();">
	      		<i class="fa fa-print"></i> @lang( 'messages.print' )
      		</button>
	      	<button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
	    </div>
    </div>
</div>
<script>
    function printModalContent() {
        $('.modal-body').printThis({
            importCSS: true,    // Import page CSS
            importStyle: true,  // Import page styles
            printContainer: true // Print the entire modal content
        });
    }
</script>
