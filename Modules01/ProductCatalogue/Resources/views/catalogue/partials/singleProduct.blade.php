{{-- <!-- Product QR Code Partial -->
<section class="content-header">
    <h1>@lang('productcatalogue::lang.product_qr')</h1> <!-- Unique header for products -->
</section>
<section class="content">
    <div class="row">
        <div class="col-md-7">
            @component('components.widget', ['class' => 'box-solid'])
                <div class="form-group">
                    {!! Form::label('item_id', __('lang_v1.product') . ':') !!} <!-- Unique 'item_id' for product selection -->
                    {!! Form::select('item_id', $products, null, ['class' => 'form-control select2', 'id' => 'item_id', 'placeholder' => __('messages.please_select')]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('item_qr_color', __('productcatalogue::lang.qr_code_color') . ':') !!} <!-- Unique 'item_qr_color' -->
                    {!! Form::text('item_qr_color', '#000000', ['class' => 'form-control', 'id' => 'item_qr_color']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('item_title', __('productcatalogue::lang.title') . ':') !!} <!-- Unique 'item_title' -->
                    {!! Form::text('item_title', $business->name, ['class' => 'form-control', 'id' => 'item_title']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('item_subtitle', __('productcatalogue::lang.subtitle') . ':') !!} <!-- Unique 'item_subtitle' -->
                    {!! Form::text('item_subtitle', __('productcatalogue::lang.product_catalogue'), ['class' => 'form-control', 'id' => 'item_subtitle']) !!}
                </div>
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            {!! Form::checkbox('item_include_logo', 1, true, ['id' => 'item_display_logo', 'class' => 'input-icheck']) !!} <!-- Unique 'item_include_logo' and 'item_display_logo' -->
                            @lang('productcatalogue::lang.show_business_logo_on_qrcode')
                        </label>
                    </div>
                </div>
                <button type="button" class="btn btn-primary" id="generate_qr_item">@lang('productcatalogue::lang.generate_qr')</button> <!-- Unique 'generate_qr_item' -->
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
                    <div id="item_qrcode"></div> <!-- Unique 'item_qrcode' -->
                    <span id="item_link"></span> <!-- Unique 'item_link' -->
                    <br>
                    <a href="#" class="btn btn-success hide" id="item_download_image">@lang('productcatalogue::lang.download_image')</a> <!-- Unique 'item_download_image' -->
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
                $('#item_qr_color').colorpicker(); // Unique 'item_qr_color'
                $('#item_id').select2(); // Unique 'item_id' with Select2
            });

            $(document).on('click', '#generate_qr_item', function(e) { // Unique 'generate_qr_item'
                $('#item_qrcode').html(''); // Unique 'item_qrcode'
                if ($('#item_id').val()) { // Unique 'item_id'
                    var itemLink = "{{ url('catalogue/' . session('business.id')) }}/" + $('#item_id').val();
                    var itemColor = $('#item_qr_color').val().trim() || '#000000'; // Unique 'item_qr_color'
                    var itemOpts = {
                        text: itemLink,
                        margin: 4,
                        width: 256,
                        height: 256,
                        quietZone: 20,
                        colorDark: itemColor,
                        colorLight: "#ffffffff",
                    };

                    if ($('#item_title').val().trim() !== '') { // Unique 'item_title'
                        itemOpts.title = $('#item_title').val();
                        itemOpts.titleFont = "bold 18px Arial";
                        itemOpts.titleColor = "#004284";
                        itemOpts.titleBackgroundColor = "#ffffff";
                        itemOpts.titleHeight = 60;
                        itemOpts.titleTop = 20;
                    }

                    if ($('#item_subtitle').val().trim() !== '') { // Unique 'item_subtitle'
                        itemOpts.subTitle = $('#item_subtitle').val();
                        itemOpts.subTitleFont = "14px Arial";
                        itemOpts.subTitleColor = "#4F4F4F";
                        itemOpts.subTitleTop = 40;
                    }

                    if ($('#item_display_logo').is(':checked')) { // Unique 'item_display_logo'
                        itemOpts.logo = "{{ asset('uploads/business_logos/' . $business->logo) }}";
                    }

                    new QRCode(document.getElementById("item_qrcode"), itemOpts); // Unique 'item_qrcode'
                    $('#item_link').html('<a target="_blank" href="' + itemLink + '">Product Link</a>'); // Unique 'item_link'
                    $('#item_download_image').removeClass('hide'); // Unique 'item_download_image'
                    $('#item_qrcode').find('canvas').attr('id', 'item_qr_canvas'); // Unique 'item_qr_canvas'
                } else {
                    alert("{{ __('lang_v1.please_select_product') }}"); // Product-specific alert
                }
            });

            $('#item_download_image').click(function(e) { // Unique 'item_download_image'
                e.preventDefault();
                var link = document.createElement('a');
                link.download = 'product_qrcode.png'; // Unique filename
                link.href = document.getElementById('item_qr_canvas').toDataURL(); // Unique 'item_qr_canvas'
                link.click();
            });
        })(jQuery);
    </script>
@endsection --}}