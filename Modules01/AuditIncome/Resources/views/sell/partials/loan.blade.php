@component('components.widget', ['class' => 'box-solid'])

                        
                        <div class="col-md-4">
		        <div class="form-group">
		            {!! Form::label('interest_rate', __('lang_v1.interest_rate') . ':' ) !!}
		            {!! Form::text('interest_rate', null, ['class' => 'form-control','placeholder' => __('lang_v1.interest_rate')]); !!}
		        </div>
		    </div>
		    
		      
                        <div class="col-md-4">
		        <div class="form-group">
		            {!! Form::label('interest_rate', __('lang_v1.interest_rate') . ':' ) !!}
		            {!! Form::text('interest_rate', null, ['class' => 'form-control','placeholder' => __('lang_v1.interest_rate')]); !!}
		        </div>
		    </div>
		    
		    
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="multi-input">
                                    @php
                                    $is_pay_term_required = !empty($pos_settings['is_pay_term_required']);
                                    @endphp
                                    {!! Form::label('pay_term_number', __('contact.pay_term') . ':') !!} @show_tooltip(__('tooltip.pay_term'))
                                    <br />
                                    {!! Form::number('pay_term_number', $walk_in_customer['pay_term_number'], [
                                    'class' => 'form-control width-40 pull-left',
                                    'placeholder' => __('30'),
                                    'required' => $is_pay_term_required,
                                    ]) !!}

                                    {!! Form::select(
                                    'pay_term_type',
                                    ['months' => __('lang_v1.months'), 'days' => __('lang_v1.days')],
                                    $walk_in_customer['pay_term_type'],
                                    [
                                    'class' => 'form-control width-60 pull-left',
                                    'placeholder' => __('messages.please_select'),
                                    'required' => $is_pay_term_required,
                                    ],
                                    ) !!}
                                </div>
                            </div>
                        </div>                       
                       
                    
                    
                    
                    
                    
                     @endcomponent