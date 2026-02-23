<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        {!! Form::open([
        'url' => action([\Modules\ModuleCreateModule\Http\Controllers\ModuleCreateModuleController::class, 'store']),
        'method' => 'post',
        'id' => 'add_module_modal_form',
        'files' => true,
        ]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
            <h4 class="modal-title" style="text-align: center; margin: 0 auto;">@lang('modulecreatemodule::lang.new_mini_app')</h4>
        </div>
        <div class="modal-body" id="moduleModalBody">
            @csrf
            <div class="card" style="background-color: #f5f5f5; border: 0.5px solid #007bff; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); margin-bottom: 20px;">
                <div class="card-body" style="background-color: #f5f5f5; padding: 10px 10px; border-radius: 10px;">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                <label for="color_code">@lang('modulecreatemodule::lang.select_color')</label>
                                <input type="color" id="color_code" name="color_code" class="form-control"
                                    style="border-radius: 5px; padding: 5px; height: 40px;">
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                <label for="svg_icon_select">@lang('modulecreatemodule::lang.select_icon')</label>
                                <input id="svg_icon_select" type="file" name="svg_file" class="form-control" accept="image/svg+xml">
                                <input type="hidden" id="svg_content" name="svg_content">

                                <div id="icon-preview" style="margin-top: 10px; text-align: center; min-height: 60px; border: 1px solid #ddd; padding: 10px; border-radius: 5px; background-color: #f9f9f9;">
                                    <span style="color: #666;">Icon preview will appear here</span>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-12 mb-3">
                                <label for="module_name"><span style="color: red;">*</span> @lang('modulecreatemodule::lang.module_name')</label>
                                {!! Form::text('module_name', null, ['class' => 'form-control', 'required' => true, 'style' => 'border-radius: 5px; padding: 5px; height: 40px;', 'placeholder' => 'BusinessCanvas']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card" style="background-color: #f5f5f5; border: 0.5px solid #007bff; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); margin-bottom: 20px;">
                <div class="card-body" style="background-color: #f5f5f5; padding: 10px 10px; border-radius: 10px;">
                    <div class="form-group">
                        <div class="row" style="display: flex; align-items: center; flex-wrap: wrap;">
                            <div class="col-lg-3 col-md-4 col-sm-12" id="orderInput">
                                <label for="menu_visible">@lang('modulecreatemodule::lang.menu_visible')</label>
                                <div class="input-group" style="display: flex; align-items: center; gap: 10px;">
                                    <label class="switch">
                                        <input type="checkbox" id="menu_visible" value="0" name="menu_visible">
                                        <span class="slider round">
                                            <span class="on">ON</span>
                                            <span class="off">OFF</span>
                                        </span>
                                    </label>
                                    <input type="hidden" id="menu_visible_value" name="menu_visible" value="0">
                                </div>
                            </div>
                            <div class="col-lg-9 col-md-4 col-sm-12" id="orderInput">
                                <label for="input_number"><span style="color: red;">*</span> @lang('modulecreatemodule::lang.order')</label>
                                <div class="input-group" style="display: flex; align-items: center; gap: 10px;">
                                    <input type="number" id="input_number" name="input_number" class="form-control"
                                        placeholder="100" style="border-radius: 5px; padding: 5px;">
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-12" id="orderInput">
                                <label for="submenu_visible">@lang('modulecreatemodule::lang.submenu_visible')</label>
                                <div class="input-group" style="display: flex; align-items: center; gap: 10px;">
                                    <label class="switch">
                                        <input type="checkbox" id="submenu_visible" value="0" name="submenu_visible">
                                        <span class="slider round">
                                            <span class="on">ON</span>
                                            <span class="off">OFF</span>
                                        </span>
                                    </label>
                                    <input type="hidden" id="submenu_visible_value" name="submenu_visible" value="0">
                                </div>
                            </div>
                            <div class="col-lg-9 col-md-4 col-sm-12" id="availableMenu">
                                <label for="menu_location"><span style="color: red;">*</span>@lang('modulecreatemodule::lang.available_menu')</label>
                                <div class="input-group" style="display: flex; align-items: center; gap: 10px;">
                                    @php
                                    $menu_lists = [
                                    "home.home",
                                    "home.sales",
                                    "home.purchase",
                                    "home.expenses",
                                    "home.customers_and_leads",
                                    "home.reports",
                                    "home.employee",
                                    "home.budget",
                                    "home.taxes",
                                    "home.my_accountant",
                                    "home.apps",
                                    "home.inventory",
                                    "home.others",
                                    "home.project",
                                    "home.business",
                                    "home.commerce",
                                    "home.investment",
                                    "home.settings",
                                    ];
                                    @endphp
                                    <select class="form-control select2" name="menu_location" id="menu_location">
                                        <option value="">@lang('modulecreatemodule::lang.select_menu')</option>
                                        @foreach ($menu_lists as $menu_item)
                                        <option value="{{ $menu_item }}">{{ $menu_item }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card" style="background-color: #807d7d; border: 0.5px solid green; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); margin-bottom: 20px;">
                <div class="card-header" style="background-color: #807d7d; color: black; font-size: 1rem; padding: 10px 15px; border-radius: 5px 5px 0 0; text-align: center;">
                    @lang('modulecreatemodule::lang.elements')
                </div>
                <div class="card-body" style="background-color: #f5f5f5; padding: 10px 25px;">
                    <div class="form-group mt-3">
                        <div class="row align-items-center" id="inputsContainer" style="display: none; width: 100%;">
                            <div class="col-sm-7">
                                <input type="text" name="title[]" placeholder="@lang('modulecreatemodule::lang.title')" class="input-underline" style="border-radius: 5px; padding: 5px;">
                                <input type="hidden" name="prefix[]" value="1_">
                            </div>
                            <div class="col-sm-5">
                                <select name="type[]" class="form-control type-select">
                                    <option value="">{{ __('modulecreatemodule::lang.select_type') }}</option>
                                    <optgroup style="background-color: #807d7d; color: black;" label="{{ __('modulecreatemodule::lang.business_element') }}">
                                        <option value="business_location">{{ __('modulecreatemodule::lang.type_business_location') }}</option>
                                        <option value="users">{{ __('modulecreatemodule::lang.type_employees') }}</option>
                                        <option value="designations">{{ __('modulecreatemodule::lang.type_designation') }}</option>
                                        <option value="departments">{{ __('modulecreatemodule::lang.type_department') }}</option>
                                        <option value="customer">{{ __('modulecreatemodule::lang.type_customer') }}</option>
                                        <option value="product">{{ __('modulecreatemodule::lang.type_product') }}</option>
                                        <option value="supplier">{{ __('modulecreatemodule::lang.type_supplier') }}</option>
                                        <option value="asset">{{ __('modulecreatemodule::lang.type_asset') }}</option>
                                        <option value="lead">{{ __('modulecreatemodule::lang.type_lead') }}</option>
                                        <option value="customergroup">{{ __('modulecreatemodule::lang.type_customer_group') }}</option>
                                        <option value="account">{{ __('modulecreatemodule::lang.type_account') }}</option>
                                        <option value="bank">{{ __('modulecreatemodule::lang.type_bank') }}</option>
                                        <option value="type_payment_account">{{ __('modulecreatemodule::lang.type_payment_account') }}</option>
                                    </optgroup>
                                    <optgroup style="background-color: #807d7d; color: black;" label="{{ __('modulecreatemodule::lang.status_related') }}">
                                        <option value="status_true_false">{{ __('modulecreatemodule::lang.type_status_true_false') }}</option>
                                        <option value="status_authorize">{{ __('modulecreatemodule::lang.type_status_authorize') }}</option>
                                        <option value="status_priority">{{ __('modulecreatemodule::lang.type_status_priority') }}</option>
                                        <option value="status_payment">{{ __('modulecreatemodule::lang.type_status_payment') }}</option>
                                        <option value="status_delivery">{{ __('modulecreatemodule::lang.type_status_delivery') }}</option>
                                    </optgroup>
                                    <optgroup style="background-color: #807d7d; color: black;" label="{{ __('modulecreatemodule::lang.type_default') }}">
                                        <option value="boolean">{{ __('modulecreatemodule::lang.type_checkbox') }}</option>
                                        <option value="boolean">{{ __('modulecreatemodule::lang.type_radio') }}</option>
                                        <option value="boolean">{{ __('modulecreatemodule::lang.type_color') }}</option>
                                        <option value="boolean">{{ __('modulecreatemodule::lang.type_line_break') }}</option>
                                        <option value="boolean">{{ __('modulecreatemodule::lang.type_star_rate') }}</option>
                                        <option value="boolean">{{ __('modulecreatemodule::lang.type_slidebar') }}</option>
                                        <option value="boolean">{{ __('modulecreatemodule::lang.type_draw_board') }}</option>
                                        <option value="boolean">{{ __('modulecreatemodule::lang.type_week') }}</option>
                                        <option value="boolean">{{ __('modulecreatemodule::lang.type_month') }}</option>
                                        <option value="boolean">{{ __('modulecreatemodule::lang.type_year') }}</option>
                                        <option value="date">{{ __('modulecreatemodule::lang.type_date') }}</option>
                                        <option value="string">{{ __('modulecreatemodule::lang.type_short_text') }}</option>
                                        <option value="text">{{ __('modulecreatemodule::lang.type_text') }}</option>
                                        <option value="float">{{ __('modulecreatemodule::lang.type_number') }}</option>
                                        <option value="file">{{ __('modulecreatemodule::lang.type_image') }}</option>
                                        <option value="file">{{ __('modulecreatemodule::lang.type_file') }}</option>
                                    </optgroup>
                                    <optgroup style="background-color: #807d7d; color: black;" label="{{ __('modulecreatemodule::lang.qr_and_audit') }}">
                                        <option value="qrcode">{{ __('modulecreatemodule::lang.type_qrcode') }}</option>
                                        <option value="audit">{{ __('modulecreatemodule::lang.type_audit') }}</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="col-sm-2 text-center">
                                <button type="button" class="btn btn-circle btn-custom" style="border-radius: 50%;">+</button>
                            </div>
                        </div>
                        <div id="additionalInputsContainer"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {!! Form::submit(__('messages.save'), ['class' => 'tw-dw-btn tw-dw-btn-primary tw-text-white']) !!}
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize Select2 for SVG icon selection
        $('#svg_icon_select').on('change', function(e) {
            const file = e.target.files[0];
            const $preview = $('#icon-preview');

            if (!file) {
                $preview.html('<span style="color: #666;">Icon preview will appear here</span>');
                $('#svg_content').val('');
                return;
            }

            if (file.type !== 'image/svg+xml') {
                $preview.html('<span style="color: #red;">Please select a valid SVG file</span>');
                $('#svg_content').val('');
                return;
            }

            const reader = new FileReader();

            reader.onload = function(e) {
                try {
                    let svgContent = e.target.result;

                    // Clean SVG content
                    let cleanedSvg = svgContent
                        .replace(/<\?xml[^>]*>/i, '') // Remove XML declaration
                        .replace(/<!DOCTYPE[^>]*>/i, '') // Remove DOCTYPE
                        .replace(/(\r\n|\n|\r)/gm, '') // Remove line breaks
                        .replace(/fill="[^"]*"/g, '') // Remove fill attributes
                        .replace(/width="[^"]*"/g, '') // Remove width
                        .replace(/height="[^"]*"/g, ''); // Remove height

                    // Ensure SVG has proper namespace if missing
                    if (!cleanedSvg.includes('xmlns="http://www.w3.org/2000/svg"')) {
                        cleanedSvg = cleanedSvg.replace(
                            '<svg',
                            '<svg xmlns="http://www.w3.org/2000/svg"'
                        );
                    }

                    // Add styling for preview
                    const previewSvg = cleanedSvg.replace(
                        '<svg',
                        '<svg style="width: 48px; height: 48px; fill: currentColor; vertical-align: middle;"'
                    );

                    // Update preview
                    $preview.html(`
                    <div style="display: flex; flex-direction: column; align-items: center; gap: 5px;">
                        ${previewSvg}
                        <small style="color: #666;">${file.name}</small>
                    </div>
                `);

                    // Store SVG content in hidden input
                    $('#svg_content').val(svgContent);
                } catch (error) {
                    console.error('Error processing SVG:', error);
                    $preview.html('<span style="color: #red;">Error displaying SVG preview</span>');
                    $('#svg_content').val('');
                }
            };

            reader.onerror = function() {
                $preview.html('<span style="color: #red;">Error reading SVG file</span>');
                $('#svg_content').val('');
            };

            reader.readAsText(file);
        });

        // Clear preview when input is cleared
        $('#svg_icon_select').on('click', function(e) {
            if (e.target.value) {
                $(this).val('');
                $('#icon-preview').html('<span style="color: #666;">Icon preview will appear here</span>');
                $('#svg_content').val('');
            }
        });

        // Initialize Select2 for menu_location
        $('#menu_location').select2({
            placeholder: "@lang('modulecreatemodule::lang.select_menu')",
            allowClear: true,
            width: '100%'
        });

        // Dynamic input rows
        var inputCounter = 4;
        var inputsContainer = $('#inputsContainer');
        var isVisible = inputsContainer.is(':visible');

        if (!isVisible) {
            inputsContainer.show();
            $('#inputsContainer input[name="title[]"]').attr('required', true);
            $('#inputsContainer select[name="type[]"]').attr('required', true);
        }

        function addNewInputRow() {
            inputCounter++;
            var additionalInputsContainer = $('#additionalInputsContainer');
            var newInputRow = `
                <div class="row align-items-center mt-2 new-input-row">
                    <div class="col-sm-7">
                        <input type="text" name="title[]" placeholder="@lang('modulecreatemodule::lang.title')" class="input-underline" style="border-radius: 5px; padding: 5px;" required>
                        <input type="hidden" name="prefix[]" value="_${inputCounter}">
                    </div>
                    <div class="col-sm-5">
                        <select name="type[]" class="form-control type-select" required>
                            <option value="">{{ __('modulecreatemodule::lang.select_type') }}</option>
                            <optgroup style="background-color: #807d7d; color: black;" label="{{ __('modulecreatemodule::lang.business_element') }}">
                                <option value="business_location">{{ __('modulecreatemodule::lang.type_business_location') }}</option>
                                <option value="users">{{ __('modulecreatemodule::lang.type_employees') }}</option>
                                <option value="designations">{{ __('modulecreatemodule::lang.type_designation') }}</option>
                                <option value="departments">{{ __('modulecreatemodule::lang.type_department') }}</option>
                                <option value="customer">{{ __('modulecreatemodule::lang.type_customer') }}</option>
                                <option value="product">{{ __('modulecreatemodule::lang.type_product') }}</option>
                                <option value="supplier">{{ __('modulecreatemodule::lang.type_supplier') }}</option>
                                <option value="asset">{{ __('modulecreatemodule::lang.type_asset') }}</option>
                                <option value="lead">{{ __('modulecreatemodule::lang.type_lead') }}</option>
                                <option value="customergroup">{{ __('modulecreatemodule::lang.type_customer_group') }}</option>
                                <option value="account">{{ __('modulecreatemodule::lang.type_account') }}</option>
                                <option value="bank">{{ __('modulecreatemodule::lang.type_bank') }}</option>
                                <option value="type_payment_account">{{ __('modulecreatemodule::lang.type_payment_account') }}</option>
                            </optgroup>
                            <optgroup style="background-color: #807d7d; color: black;" label="{{ __('modulecreatemodule::lang.status_related') }}">
                                <option value="status_true_false">{{ __('modulecreatemodule::lang.type_status_true_false') }}</option>
                                <option value="status_authorize">{{ __('modulecreatemodule::lang.type_status_authorize') }}</option>
                                <option value="status_priority">{{ __('modulecreatemodule::lang.type_status_priority') }}</option>
                                <option value="status_payment">{{ __('modulecreatemodule::lang.type_status_payment') }}</option>
                                <option value="status_delivery">{{ __('modulecreatemodule::lang.type_status_delivery') }}</option>
                            </optgroup>
                            <optgroup style="background-color: #807d7d; color: black;" label="{{ __('modulecreatemodule::lang.type_default') }}">
                                <option value="boolean">{{ __('modulecreatemodule::lang.type_checkbox') }}</option>
                                <option value="boolean">{{ __('modulecreatemodule::lang.type_radio') }}</option>
                                <option value="boolean">{{ __('modulecreatemodule::lang.type_color') }}</option>
                                <option value="boolean">{{ __('modulecreatemodule::lang.type_line_break') }}</option>
                                <option value="boolean">{{ __('modulecreatemodule::lang.type_star_rate') }}</option>
                                <option value="boolean">{{ __('modulecreatemodule::lang.type_slidebar') }}</option>
                                <option value="boolean">{{ __('modulecreatemodule::lang.type_draw_board') }}</option>
                                <option value="boolean">{{ __('modulecreatemodule::lang.type_week') }}</option>
                                <option value="boolean">{{ __('modulecreatemodule::lang.type_month') }}</option>
                                <option value="boolean">{{ __('modulecreatemodule::lang.type_year') }}</option>
                                <option value="date">{{ __('modulecreatemodule::lang.type_date') }}</option>
                                <option value="string">{{ __('modulecreatemodule::lang.type_short_text') }}</option>
                                <option value="text">{{ __('modulecreatemodule::lang.type_text') }}</option>
                                <option value="float">{{ __('modulecreatemodule::lang.type_number') }}</option>
                                <option value="file">{{ __('modulecreatemodule::lang.type_image') }}</option>
                                <option value="file">{{ __('modulecreatemodule::lang.type_file') }}</option>
                            </optgroup>
                            <optgroup style="background-color: #807d7d; color: black;" label="{{ __('modulecreatemodule::lang.qr_and_audit') }}">
                                <option value="qrcode">{{ __('modulecreatemodule::lang.type_qrcode') }}</option>
                                <option value="audit">{{ __('modulecreatemodule::lang.type_audit') }}</option>
                            </optgroup>
                        </select>
                    </div>
                    <div class="col-sm-2 text-center">
                        <button type="button" class="btn btn-circle btn-custom">+</button>
                        <button type="button" class="btn btn-circle btn-danger removeInputBtn mt-2">-</button>
                    </div>
                </div>
            `;
            additionalInputsContainer.append(newInputRow);
        }

        $('#moduleModalBody').on('click', '.btn-custom', function() {
            addNewInputRow();
        });

        $('#moduleModalBody').on('click', '.removeInputBtn', function() {
            $(this).closest('.new-input-row').remove();
            inputCounter--;
            $('#additionalInputsContainer .new-input-row').each(function(index, element) {
                var newPrefix = "_" + (index + 2);
                $(element).find('input[name="prefix[]"]').val(newPrefix);
            });
        });

        $('#add_module_modal_form').submit(function(event) {
            if (!$('#inputsContainer').is(':visible')) {
                $('#inputsContainer input[name="title[]"]').removeAttr('required');
                $('#inputsContainer select[name="type[]"]').removeAttr('required');
            }

            $('#inputsContainer input[name="title[]"]').first().each(function() {
                var $input = $(this);
                var value = $input.val();
                if (!value.includes('_') && value !== '') {
                    $input.val(value + '_1');
                }
            });

            $('#moduleModalBody .new-input-row').each(function() {
                var $titleInput = $(this).find('input[name="title[]"]');
                var prefix = $(this).find('input[name="prefix[]"]').val();
                var value = $titleInput.val();
                if (value !== '' && !value.includes(prefix)) {
                    $titleInput.val(value + prefix);
                }
            });

            $('#moduleModalBody input[name="title[]"]').each(function() {
                if ($(this).val().trim() === '') {
                    $(this).remove();
                }
            });

            $('#moduleModalBody select[name="type[]"]').each(function() {
                if ($(this).val().trim() === '') {
                    $(this).remove();
                }
            });
        });

        // Checkbox logic for menu_visible and submenu_visible
        const menuVisibleCheckbox = document.getElementById('menu_visible');
        const menuVisibleHiddenInput = document.getElementById('menu_visible_value');

        menuVisibleCheckbox.addEventListener('change', function() {
            menuVisibleHiddenInput.value = this.checked ? '1' : '0';
        });
        menuVisibleHiddenInput.value = menuVisibleCheckbox.checked ? '1' : '0';

        const submenuVisibleCheckbox = document.getElementById('submenu_visible');
        const submenuVisibleHiddenInput = document.getElementById('submenu_visible_value');

        submenuVisibleCheckbox.addEventListener('change', function() {
            submenuVisibleHiddenInput.value = this.checked ? '1' : '0';
        });
        submenuVisibleHiddenInput.value = submenuVisibleCheckbox.checked ? '1' : '0';
    });
</script>

<style>
    .select2-container--default .select2-results__option {
        display: flex;
        align-items: center;
        padding: 6px;
        width: 100%;
    }

    .select2-container--default .select2-results__option svg {
        width: 24px;
        height: 24px;
        margin-right: 8px;
        fill: currentColor;
    }

    .select2-container--default .select2-results__option--highlighted svg {
        fill: #fff;
    }

    .select2-container--default .select2-selection__rendered {
        display: flex;
        align-items: center;
    }

    .select2-container--default .select2-selection__rendered svg {
        width: 20px;
        height: 20px;
        margin-right: 8px;
        fill: currentColor;
    }

    #icon-preview svg {
        width: 48px;
        height: 48px;
        fill: currentColor;
    }

    .select2-container {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--single {
        height: 40px;
        border-radius: 5px;
        border: 1px solid #ccc;
        display: flex;
        align-items: center;
    }

    .select2-container--default .select2-selection__arrow {
        height: 38px;
    }

    .select2-container--default .select2-search__field {
        color: #4a4a4a;
        background-color: #f5f5f5;
    }

    .select2-search__field {
        font-family: 'Arial', sans-serif;
        font-size: 16px;
    }

    .input-underline {
        border: none;
        border-bottom: 1px solid #000;
        outline: none;
        width: 100%;
        box-sizing: border-box;
        background-color: transparent;
        color: #000;
    }

    .input-underline:focus {
        border-bottom: 1px solid #007bff;
        background-color: #f1f1f1;
    }

    .input-underline:hover {
        border-bottom: 1px solid #0056b3;
    }

    .form-group.d-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    #inputsContainer,
    #additionalInputsContainer .row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
    }

    .form-group.mt-3 {
        margin-top: 20px;
    }

    .btn-circle {
        width: 35px;
        height: 35px;
        padding: 6px 0;
        border-radius: 50%;
        text-align: center;
        font-size: 18px;
        line-height: 1.42857;
    }

    .btn-custom {
        background-color: #007bff;
        color: white;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
    }

    .new-input-row {
        margin-top: 10px;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 90px;
        height: 34px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 34px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 10px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked+.slider {
        background-color: #2196F3;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        transform: translateX(56px);
    }

    .on,
    .off {
        color: white;
        font-size: 12px;
        font-weight: bold;
    }

    .on {
        display: none;
    }

    input:checked+.slider .on {
        display: inline;
    }

    input:checked+.slider .off {
        display: none;
    }
</style>