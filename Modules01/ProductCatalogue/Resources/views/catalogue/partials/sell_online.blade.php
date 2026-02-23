<!-- Customer QR Code Partial -->
<section class="content-header">
    <h1>@lang('productcatalogue::lang.customer_qr_login')</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-7">
            @component('components.widget', ['class' => 'box-solid'])
                <div class="form-group">
                    {!! Form::label('customer_id', __('productcatalogue::lang.customer') . ':') !!}
                    {!! Form::select('customer_id', $user_customer, null, ['class' => 'form-control select2', 'id' => 'customer_id', 'placeholder' => __('messages.please_select'),  'style' => 'width: 700px' ]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('cust_qr_color', __('productcatalogue::lang.qr_code_color') . ':') !!}
                    {!! Form::text('cust_qr_color', '#000000', ['class' => 'form-control', 'id' => 'cust_qr_color']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('cust_title', __('productcatalogue::lang.title') . ':') !!}
                    {!! Form::text('cust_title', $business->name, ['class' => 'form-control', 'id' => 'cust_title']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('cust_subtitle', __('productcatalogue::lang.subtitle') . ':') !!}
                    {!! Form::text('cust_subtitle', __('productcatalogue::lang.product_catalogue'), ['class' => 'form-control', 'id' => 'cust_subtitle']) !!}
                </div>
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            {!! Form::checkbox('cust_add_logo', 1, true, ['id' => 'cust_show_logo', 'class' => 'input-icheck']) !!}
                            @lang('productcatalogue::lang.show_business_logo_on_qrcode')
                        </label>
                    </div>
                </div>
                <button type="button" class="btn btn-primary" id="generate_qr_customer">@lang('productcatalogue::lang.generate_qr')</button>
            @endcomponent
            @component('components.widget', ['class' => 'box-solid'])
                <div class="row">
                    <div class="col-md-12">
                        <strong>@lang('lang_v1.instruction'):</strong>
                        <table class="table table-striped">
                            <tr>
                                <td>1</td>
                                <td>@lang('productcatalogue::lang.catalogue_instruction_5')</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>@lang('productcatalogue::lang.catalogue_instruction_2')</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>@lang('productcatalogue::lang.catalogue_instruction_3')</td>
                            </tr>
                        </table>
                    </div>
                </div>
            @endcomponent
        </div>
        <div class="col-md-5">
            @component('components.widget', ['class' => 'box-solid'])
                <div class="text-center">
                    <div id="customer_qrcode"></div>
                    <span id="customer_link"></span>
                    <br>
                    <a href="#" class="btn btn-success hide" id="customer_download_image">@lang('productcatalogue::lang.download_image')</a>
                </div>
            @endcomponent
        </div>
    </div>
</section>

@section('javascript')
    @parent
    <script src="{{ asset('modules/productcatalogue/plugins/easy.qrcode.min.js') }}"></script>
    <script type="text/javascript">
        (function($) {
            "use strict";

            $(document).ready(function() {
                $('#cust_qr_color').colorpicker();
                $('#customer_id').select2();
            });

            $(document).on('click', '#generate_qr_customer', function(e) {
                $('#customer_qrcode').html('');
                if ($('#customer_id').val()) {
                    // Get the selected customer ID and username from the select element
                    var customerId = $('#customer_id').val();
                    var username = $('#customer_id option:selected').text().trim();
                    
                    // Construct the URL with username as query parameter
                    var custLink = "{{ url('/login') }}?username=" + encodeURIComponent(username);

                    var custColor = $('#cust_qr_color').val().trim() || '#000000';
                    var custOpts = {
                        text: custLink,
                        margin: 4,
                        width: 256,
                        height: 256,
                        quietZone: 20,
                        colorDark: custColor,
                        colorLight: "#ffffffff",
                    };

                    if ($('#cust_title').val().trim() !== '') {
                        custOpts.title = $('#cust_title').val();
                        custOpts.titleFont = "bold 18px Arial";
                        custOpts.titleColor = "#004284";
                        custOpts.titleBackgroundColor = "#ffffff";
                        custOpts.titleHeight = 60;
                        custOpts.titleTop = 20;
                    }

                    if ($('#cust_subtitle').val().trim() !== '') {
                        custOpts.subTitle = $('#cust_subtitle').val();
                        custOpts.subTitleFont = "14px Arial";
                        custOpts.subTitleColor = "#4F4F4F";
                        custOpts.subTitleTop = 40;
                    }

                    if ($('#cust_show_logo').is(':checked')) {
                        custOpts.logo = "{{ asset('uploads/business_logos/' . $business->logo) }}";
                    }

                    new QRCode(document.getElementById("customer_qrcode"), custOpts);
                    $('#customer_link').html('<a target="_blank" href="' + custLink + '">Customer Link</a>');
                    $('#customer_download_image').removeClass('hide');
                    $('#customer_qrcode').find('canvas').attr('id', 'cust_qr_canvas');
                } else {
                    alert("{{ __('lang_v1.please_select_customer') }}");
                }
            });

            $('#customer_download_image').click(function(e) {
                e.preventDefault();
                var link = document.createElement('a');
                link.download = 'customer_qrcode.png';
                link.href = document.getElementById('cust_qr_canvas').toDataURL();
                link.click();
            });
        })(jQuery);
    </script>
@endsection