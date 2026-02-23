<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <br>


        <div class="table-responsive">
            <table class="table ">
                <tr>
                    <td colspan="1">
                        @if (!empty(Session::get('business.logo')))
                            <img src="{{ asset('uploads/business_logos/' . Session::get('business.logo')) }}"
                                alt="Logo" style="width: auto; max-height: 80px; margin: auto;">
                        @endif

                    </td>
                    <td>

                        <strong class="font-23">
                            {{ Session::get('business.name') ?? '' }}
                        </strong>
                        <br>
                        {!! Session::get('business.business_address') ?? '' !!}


                    </td>
                    <td>



                        <div class="header text-center mt-8">
                            {{-- Header --}}
                            <h3>
                                @if ($allowance->type === 'deduction')
                                    <strong>@lang('essentials::lang.deductions')</strong>
                                @else
                                    <strong>@lang('essentials::lang.allowances')</strong>
                                @endif
                            </h3>

                           @lang('sale.amount'): <strong style="font-size: 24px;">{{ number_format($allowance->amount, 2) }}</strong>
 

                          
                           

                            
                        </div>

                    </td>
                </tr>

            </table>
        
        
        

 <div class="text-center fw-bold fst-italic" style="font-size: 20px;">
    <strong>@lang('user.name'): {{ $selected_user->surname ?? '' }} {{ $selected_user->first_name ?? '' }}
    {{ $selected_user->last_name ?? '' }}</strong>
</div>
        

<br>

            <div class="table-responsive">
                <table class="table table-bordered" id="payroll-view">

                    <tr>
                        
                        <td>
                             {{ ($allowance->description)}}
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($allowance->applicable_date)->format('d-m-Y') }}
                        </td>
                        
                    </tr>

                </table>
                <div>


<hr>



                    <p class="text-center">
    @if ($allowance->type === 'deduction')
        @lang('essentials::lang.deductions_help')
    @else
        @lang('essentials::lang.allowances_help')
    @endif
</p>

                    {{-- Footer --}}



                    <div class="table-responsive">
                        <table class="table" >

                            <tr>
                                <td>
                                <br><br><br>
                                     {{ $selected_user->surname ?? '' }}
                                    {{ $selected_user->first_name ?? '' }} {{ $selected_user->last_name ?? '' }}
                                    <br>Date:
                                </td>
                                <td>
                                <br><br><br>
                                    <strong>@lang('essentials::lang.head_department'):</strong>
                                    <br>Date:
                                </td>
                                
                                <td>
                                <br><br><br>
                                    <strong>@lang('essentials::lang.accountant'):</strong>
                                    <br>Date:

                                </td>
                            </tr>

                        </table>
                        <div>

                        </div>
                        {{-- Button  --}}
                        <div class="modal-footer no-print">
                            <button type="button" onclick="$(this).closest('div.modal').printThis();"
                                class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang('messages.print')</button>
                            <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white"
                                data-dismiss="modal">@lang('messages.close')</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->

                {{-- Style for customization content --}}
                <style type="text/css">
                   

                    h1,
                    h2,
                    h3 {
                        font-family: "Moul", serif;
                        font-weight: 200;
                        font-style: normal;
                    }

                    p {
                        font-family: "Battambang", serif;
                        font-weight: 100;
                        font-style: normal;
                        margin-bottom: 10px;
                        font-size: 17px;
                    }

                   
                   
                </style>