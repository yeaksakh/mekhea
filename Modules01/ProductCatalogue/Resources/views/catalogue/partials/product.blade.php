<!-- Product Catalogue QR Code Partial -->
<section class="content-header">
    <h1>@lang('productcatalogue::lang.catalogue_qr')</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-7">
            @component('components.widget', ['class' => 'box-solid'])
            <div class="form-group">
                {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'id' => 'location_id', 'placeholder' => __('messages.please_select'), 'style' => 'width: 700px']) !!}
            </div>
                <div class="form-group">
                    {!! Form::label('product_color', __('productcatalogue::lang.qr_code_color') . ':') !!}
                    {!! Form::text('product_color', '#000000', ['class' => 'form-control', 'id' => 'product_color']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('product_title', __('productcatalogue::lang.title') . ':') !!}
                    {!! Form::text('product_title', $business->name, ['class' => 'form-control', 'id' => 'product_title']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('product_subtitle', __('productcatalogue::lang.subtitle') . ':') !!}
                    {!! Form::text('product_subtitle', __('productcatalogue::lang.product_catalogue'), ['class' => 'form-control', 'id' => 'product_subtitle']) !!}
                </div>
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            {!! Form::checkbox('product_add_logo', 1, true, ['id' => 'product_show_logo', 'class' => 'input-icheck']) !!}
                            @lang('productcatalogue::lang.show_business_logo_on_qrcode')
                        </label>
                    </div>
                </div>
                <button type="button" class="btn btn-primary" id="generate_qr_product">@lang('productcatalogue::lang.generate_qr')</button>
            @endcomponent
            @component('components.widget', ['class' => 'box-solid'])
                <div class="row">
                    <div class="col-md-12">
                        <strong>@lang('lang_v1.instruction'):</strong>
                        <table class="table table-striped">
                            <tr>
                                <td>1</td>
                                <td>@lang('productcatalogue::lang.catalogue_instruction_1')</td>
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
                    <div id="product_qrcode"></div>
                    <span id="product_link"></span>
                    <br>
                    <a href="#" class="btn btn-success hide" id="product_download_image">@lang('productcatalogue::lang.download_image')</a>
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
                $('#product_color').colorpicker();
                $('#location_id').select2(); // Initialize Select2 for location dropdown
            });

            $(document).on('click', '#generate_qr_product', function(e) {
                $('#product_qrcode').html('');
                if ($('#location_id').val()) {
                    var productLink = "{{ url('catalogue/' . session('business.id')) }}/" + $('#location_id').val();
                    var productColor = $('#product_color').val().trim() || '#000000';
                    var productOpts = {
                        text: productLink,
                        margin: 4,
                        width: 256,
                        height: 256,
                        quietZone: 20,
                        colorDark: productColor,
                        colorLight: "#ffffffff",
                    };

                    if ($('#product_title').val().trim() !== '') {
                        productOpts.title = $('#product_title').val();
                        productOpts.titleFont = "bold 18px Arial";
                        productOpts.titleColor = "#004284";
                        productOpts.titleBackgroundColor = "#ffffff";
                        productOpts.titleHeight = 60;
                        productOpts.titleTop = 20;
                    }

                    if ($('#product_subtitle').val().trim() !== '') {
                        productOpts.subTitle = $('#product_subtitle').val();
                        productOpts.subTitleFont = "14px Arial";
                        productOpts.subTitleColor = "#4F4F4F";
                        productOpts.subTitleTop = 40;
                    }

                    if ($('#product_show_logo').is(':checked')) {
                        productOpts.logo = "{{ asset('uploads/business_logos/' . $business->logo) }}";
                    }

                    new QRCode(document.getElementById("product_qrcode"), productOpts);
                    $('#product_link').html('<a target="_blank" href="' + productLink + '">Product Link</a>');
                    $('#product_download_image').removeClass('hide');
                    $('#product_qrcode').find('canvas').attr('id', 'product_qr_canvas');
                } else {
                    alert("{{ __('productcatalogue::lang.select_business_location') }}");
                }
            });

            $('#product_download_image').click(function(e) {
                e.preventDefault();
                var link = document.createElement('a');
                link.download = 'product_qrcode.png';
                link.href = document.getElementById('product_qr_canvas').toDataURL();
                link.click();
            });
        })(jQuery);
    </script>
@endsection