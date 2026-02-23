<!-- Customer QR Code Partial -->
<section class="content-header">
    <h1>@lang('productcatalogue::lang.customer_qr')</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-7">
            @component('components.widget', ['class' => 'box-solid'])
                <div class="form-group">
                    {!! Form::label('client_id', __('productcatalogue::lang.customer') . ':') !!}
                    {!! Form::select('client_id[]', $customer, null, [
                        'class' => 'form-control select2',
                        'id' => 'client_id',
                        'placeholder' => __('messages.please_select'),
                        'style' => 'width: 700px',
                        'multiple' => 'multiple',
                    ]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('client_qr_color', __('productcatalogue::lang.qr_code_color') . ':') !!}
                    {!! Form::text('client_qr_color', '#000000', ['class' => 'form-control', 'id' => 'client_qr_color']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('client_title', __('productcatalogue::lang.title') . ':') !!}
                    {!! Form::text('client_title', $business->name, ['class' => 'form-control', 'id' => 'client_title']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('client_subtitle', __('productcatalogue::lang.subtitle') . ':') !!}
                    {!! Form::text('client_subtitle', __('productcatalogue::lang.product_catalogue'), [
                        'class' => 'form-control',
                        'id' => 'client_subtitle',
                    ]) !!}
                </div>
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            {!! Form::checkbox('client_add_logo', 1, true, ['id' => 'client_show_logo', 'class' => 'input-icheck']) !!}
                            @lang('productcatalogue::lang.show_business_logo_on_qrcode')
                        </label>
                    </div>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" id="generate_qr_client">@lang('productcatalogue::lang.generate_qr')</button>
                    <button type="button" class="btn btn-success" id="export_excel">Excel File</button>
                </div>
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
                    <div id="client_qrcode"></div>
                    <span id="client_link"></span>
                    <br>
                    <a href="#" class="btn btn-success hide" id="client_download_image">@lang('productcatalogue::lang.download_image')</a>
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
                $('#client_qr_color').colorpicker();

                // Initialize select2 with multiple selection
                $('#client_id').select2({
                    placeholder: __('messages.please_select'),
                    allowClear: true
                });
            });

            $(document).on('click', '#generate_qr_client', function(e) {
                $('#client_qrcode').html('');

                // Get selected customer IDs
                var selectedCustomers = $('#client_id').val();

                if (selectedCustomers && selectedCustomers.length > 0) {
                    // For multiple customers, we'll use the first one for QR code
                    // You might want to modify this logic based on your requirements
                    var customerId = selectedCustomers[0];

                    // Include business ID in the URL
                    var clientLink = "{{ url('/business/' . $business_id . '/customer') }}/" + customerId;

                    var clientColor = $('#client_qr_color').val().trim() || '#000000';
                    var clientOpts = {
                        text: clientLink,
                        margin: 4,
                        width: 256,
                        height: 256,
                        quietZone: 20,
                        colorDark: clientColor,
                        colorLight: "#ffffffff",
                    };

                    if ($('#client_title').val().trim() !== '') {
                        clientOpts.title = $('#client_title').val();
                        clientOpts.titleFont = "bold 18px Arial";
                        clientOpts.titleColor = "#004284";
                        clientOpts.titleBackgroundColor = "#ffffff";
                        clientOpts.titleHeight = 60;
                        clientOpts.titleTop = 20;
                    }

                    if ($('#client_subtitle').val().trim() !== '') {
                        clientOpts.subTitle = $('#client_subtitle').val();
                        clientOpts.subTitleFont = "14px Arial";
                        clientOpts.subTitleColor = "#4F4F4F";
                        clientOpts.subTitleTop = 40;
                    }

                    if ($('#client_show_logo').is(':checked')) {
                        clientOpts.logo =
                            "{{ $business->logo ? asset('uploads/business_logos/' . $business->logo) : '' }}";
                    }

                    new QRCode(document.getElementById("client_qrcode"), clientOpts);
                    $('#client_link').html('<a target="_blank" href="' + clientLink + '">Customer Link</a>');
                    $('#client_download_image').removeClass('hide');
                    $('#client_qrcode').find('canvas').attr('id', 'client_qr_canvas');
                } else {
                    alert("{{ __('lang_v1.please_select_customer') }}");
                }
            });

            $(document).on('click', '#export_excel', function(e) {
                e.preventDefault();

                // Get selected customer IDs
                var selectedCustomers = $('#client_id').val();

                if (selectedCustomers && selectedCustomers.length > 0) {
                    var url = "{{ route('export_customer_qr_excel') }}";

                    // Add customer IDs to URL
                    for (var i = 0; i < selectedCustomers.length; i++) {
                        url += (i === 0 ? '?' : '&') + 'customer_id[]=' + selectedCustomers[i];
                    }

                    window.location.href = url;
                } else {
                    alert("{{ __('lang_v1.please_select_customer') }}");
                }
            });

            $('#client_download_image').click(function(e) {
                e.preventDefault();
                var link = document.createElement('a');
                var selectedCustomers = $('#client_id').val();
                var customerId = selectedCustomers ? selectedCustomers[0] : 'unknown';
                link.download = 'customer_qrcode_' + customerId + '.png';
                link.href = document.getElementById('client_qr_canvas').toDataURL();
                link.click();
            });
        })(jQuery);
    </script>
@endsection
