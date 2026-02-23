<!-- Employee QR Code Partial -->
<section class="content-header">
    <h1>@lang('productcatalogue::lang.employee_qr')</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-7">
            @component('components.widget', ['class' => 'box-solid'])
                <div class="form-group">
                    {!! Form::label('employee_id', __('contact.employee') . ':') !!}
                    {!! Form::select('employee_id', $employee, null, ['class' => 'form-control select2', 'id' => 'employee_id', 'placeholder' => __('messages.please_select')]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('employee_color', __('productcatalogue::lang.qr_code_color') . ':') !!}
                    {!! Form::text('employee_color', '#000000', ['class' => 'form-control', 'id' => 'employee_color']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('employee_title', __('productcatalogue::lang.title') . ':') !!}
                    {!! Form::text('employee_title', $business->name, ['class' => 'form-control', 'id' => 'employee_title']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('employee_subtitle', __('productcatalogue::lang.subtitle') . ':') !!}
                    {!! Form::text('employee_subtitle', __('productcatalogue::lang.employee_details'), ['class' => 'form-control', 'id' => 'employee_subtitle']) !!}
                </div>
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            {!! Form::checkbox('employee_add_logo', 1, true, ['id' => 'employee_show_logo', 'class' => 'input-icheck']) !!}
                            @lang('productcatalogue::lang.show_business_logo_on_qrcode')
                        </label>
                    </div>
                </div>
                <button type="button" class="btn btn-primary" id="generate_qr_employee">@lang('productcatalogue::lang.generate_qr')</button>
            @endcomponent
            @component('components.widget', ['class' => 'box-solid'])
                <div class="row">
                    <div class="col-md-12">
                        <strong>@lang('lang_v1.instruction'):</strong>
                        <table class="table table-striped">
                            <tr>
                                <td>1</td>
                                <td>@lang('productcatalogue::lang.catalogue_instruction_4')</td>
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
                    <div id="employee_qrcode"></div>
                    <span id="employee_link"></span>
                    <br>
                    <a href="#" class="btn btn-success hide" id="employee_download_image">@lang('productcatalogue::lang.download_image')</a>
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
                $('#employee_color').colorpicker();
                $('#employee_id').select2(); // Initialize Select2 for employee dropdown
            });

            $(document).on('click', '#generate_qr_employee', function(e) {
                $('#employee_qrcode').html('');
                if ($('#employee_id').val()) {
                    var employeeLink = "{{ url('/employee') }}/" + $('#employee_id').val();
                    var employeeColor = $('#employee_color').val().trim() || '#000000';
                    var employeeOpts = {
                        text: employeeLink,
                        margin: 4,
                        width: 256,
                        height: 256,
                        quietZone: 20,
                        colorDark: employeeColor,
                        colorLight: "#ffffffff",
                    };

                    if ($('#employee_title').val().trim() !== '') {
                        employeeOpts.title = $('#employee_title').val();
                        employeeOpts.titleFont = "bold 18px Arial";
                        employeeOpts.titleColor = "#004284";
                        employeeOpts.titleBackgroundColor = "#ffffff";
                        employeeOpts.titleHeight = 60;
                        employeeOpts.titleTop = 20;
                    }

                    if ($('#employee_subtitle').val().trim() !== '') {
                        employeeOpts.subTitle = $('#employee_subtitle').val();
                        employeeOpts.subTitleFont = "14px Arial";
                        employeeOpts.subTitleColor = "#4F4F4F";
                        employeeOpts.subTitleTop = 40;
                    }

                    if ($('#employee_show_logo').is(':checked')) {
                        employeeOpts.logo = "{{ asset('uploads/business_logos/' . $business->logo) }}";
                    }

                    new QRCode(document.getElementById("employee_qrcode"), employeeOpts);
                    $('#employee_link').html('<a target="_blank" href="' + employeeLink + '">Employee Link</a>');
                    $('#employee_download_image').removeClass('hide');
                    $('#employee_qrcode').find('canvas').attr('id', 'employee_qr_canvas');
                } else {
                    alert("{{ __('productcatalogue::lang.select_employee') }}");
                }
            });

            $('#employee_download_image').click(function(e) {
                e.preventDefault();
                var link = document.createElement('a');
                link.download = 'employee_qrcode.png';
                link.href = document.getElementById('employee_qr_canvas').toDataURL();
                link.click();
            });
        })(jQuery);
    </script>
@endsection