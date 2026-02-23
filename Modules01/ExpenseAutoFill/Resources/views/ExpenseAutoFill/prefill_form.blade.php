@extends('layouts.app')
@section('title', __('expense.add_expense'))

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('expense.add_expense')</h1>
</section>

<!-- Main content -->
<section class="content">
    <!-- Modal for initial upload prompt -->
    <div class="modal fade" id="uploadInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="uploadInvoiceModalLabel" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="uploadInvoiceModalLabel">Upload Invoice</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                   <div class="form-group">
                            {!! Form::label('document', __('purchase.attach_document') . ':') !!}
                            {!! Form::file('document', [
                                'id' => 'upload_document',
                                'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types'))),
                            ]) !!}
                        </div>
                        <button type="button" class="btn btn-info" id="view_telegram_docs">
                            <i class="fa fa-telegram"></i> Telegram
                        </button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="close_modal_button" data-dismiss="modal">@lang('messages.close')</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Image Preview Modal -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Preview Image</h4>
                </div>
                <div class="modal-body text-center">
                    <img src="" id="preview_image" class="img-responsive" style="width: 100%; height: auto;">
                </div>
            </div>
        </div>
    </div>

    {!! Form::open(['url' => action([\App\Http\Controllers\ExpenseController::class, 'store']), 'method' => 'post', 'id' => 'add_expense_form', 'files' => true ]) !!}
    <div class="box box-solid">
        <div class="box-body">
            <div class="row">
                @if(count($business_locations) == 1)
                    @php 
                        $default_location = current(array_keys($business_locations->toArray())) 
                    @endphp
                @else
                    @php
                        // Use the location from prefill if available, otherwise null
                        $default_location = !empty($location) ? null : null;

                        // Find the location ID that matches the prefill location
                        if (!empty($location)) {
                            foreach ($business_locations as $id => $name) {
                                if (stripos($name, $location) !== false) {
                                    $default_location = $id;
                                    break;
                                }
                            }
                        }
                    @endphp
                @endif
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('location_id', __('purchase.business_location').':*') !!}
                        {!! Form::select('location_id', $business_locations, $default_location, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'id' => 'location_id']); !!}
                    </div>
                </div>
                

                <input type="hidden" name="telegram_image_id"  id="telegram_image_id" value="{{ $image_id ?? '' }}">


                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('expense_category_id', __('expense.expense_category').':') !!}
                        @php
                            // Find the category ID that matches the prefill expense_group
                            $default_category = null;
                            if (!empty($expense_group)) {
                                foreach ($expense_categories as $id => $name) {
                                    if (stripos($name, $expense_group) !== false) {
                                        $default_category = $id;
                                        break;
                                    }
                                }
                            }
                        @endphp
                        {!! Form::select('expense_category_id', $expense_categories, $default_category, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('expense_sub_category_id', __('product.sub_category') . ':') !!}
                        {!! Form::select('expense_sub_category_id', [], null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2']); !!}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('ref_no', __('purchase.ref_no').':') !!}
                        {!! Form::text('ref_no', null, ['class' => 'form-control']); !!}
                        <p class="help-block">
                            @lang('lang_v1.leave_empty_to_autogenerate')
                        </p>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('transaction_date', __('messages.date') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            @php
                                // Use the transaction_date from prefill if available, otherwise current date
                                $default_date = \Carbon\Carbon::now()->format('d/m/Y'); // Default to current date

                                if (!empty($transaction_date)) {
                                    try {
                                        // Try to parse the date from the prefill data
                                        $parsedDate = \Carbon\Carbon::createFromFormat('d/m/Y', $transaction_date);
                                        $default_date = $parsedDate->format('d/m/Y');
                                    } catch (\Exception $e) {
                                        // If parsing fails, try other common formats
                                        try {
                                            $parsedDate = \Carbon\Carbon::parse($transaction_date);
                                            $default_date = $parsedDate->format('d/m/Y');
                                        } catch (\Exception $e) {
                                            // If all parsing fails, stick with current date
                                            $default_date = \Carbon\Carbon::now()->format('d/m/Y');
                                        }
                                    }
                                }
                            @endphp
                            {!! Form::text('transaction_date', $default_date, ['class' => 'form-control', 'readonly', 'required', 'id' => 'expense_transaction_date']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('expense_for', __('expense.expense_for').':') !!} @show_tooltip(__('tooltip.expense_for'))
                        @php
                            // Find the user ID that matches the prefill who_expense or employee_name
                            $default_user = null;
                            if (!empty($who_expense)) {
                                foreach ($users as $id => $name) {
                                    if (stripos($name, $who_expense) !== false) {
                                        $default_user = $id;
                                        break;
                                    }
                                }
                            } elseif (!empty($employee_name)) {
                                foreach ($users as $id => $name) {
                                    if (stripos($name, $employee_name) !== false) {
                                        $default_user = $id;
                                        break;
                                    }
                                }
                            }
                        @endphp
                        {!! Form::select('expense_for', $users, $default_user, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'id' => 'expense_for']); !!}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('contact_id', __('lang_v1.expense_for_contact').':') !!} 
                        @php
                            // Find the contact ID that matches the prefill supplier
                            $default_contact = null;
                            if (!empty($supplier)) {
                                foreach ($contacts as $id => $name) {
                                    if (stripos($name, $supplier) !== false) {
                                        $default_contact = $id;
                                        break;
                                    }
                                }
                            }
                        @endphp
                        {!! Form::select('contact_id', $contacts, $default_contact, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
                    </div>
                </div>
                
                <!-- Document Upload Section (hidden by default) -->
                <div class="col-sm-4" id="second_upload_document" style="display: none;">
                    <div class="form-group">
                        {{-- Display the OCR image if it exists --}}
                        @if (!empty($image_path))
                            <div class="mb-2">
                                <p class="help-block"><strong>OCR Image:</strong></p>
                                <a href="#" class="image-popup-link" data-image="{{ asset($image_path) }}">
                                    <img src="{{ asset($image_path) }}" alt="OCR Image"
                                        style="max-width: 200px; max-height: 200px; cursor: pointer;"
                                        class="img-thumbnail">
                                </a>
                            </div>

                            {{-- Hidden input to pass the image path to the controller --}}
                            <input type="hidden" name="ocr_image_path_hidden" id="ocr_image_path_hidden"
                                value="{{ $image_path }}">
                        @endif

                        {{-- Image preview for uploaded files --}}
                        <div id="uploaded_image_preview" style="display: none; margin: 10px 0;">
                            <p class="help-block"><strong>Uploaded Image:</strong></p>
                            <a href="#" class="uploaded-image-popup-link" data-image="">
                                <img src="" alt="Uploaded Image"
                                    style="max-width: 200px; max-height: 200px; cursor: pointer;" class="img-thumbnail">
                            </a>
                        </div>

                        {!! Form::label('document', __('purchase.attach_document') . ':') !!}
                        {!! Form::file('document', ['id' => 'upload_document_2', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
                        <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                            @includeIf('components.document_help_text')</p></small>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('tax_id', __('product.applicable_tax') . ':' ) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-info"></i>
                            </span>
                            {!! Form::select('tax_id', $taxes['tax_rates'], null, ['class' => 'form-control'], $taxes['attributes']); !!}
                            <input type="hidden" name="tax_calculation_amount" id="tax_calculation_amount" value="0">
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('final_total', __('sale.total_amount') . ':*') !!}
                        @php
                            // Use the total from prefill if available
                            $default_total = !empty($total) ? $total : null;
                        @endphp
                        {!! Form::text('final_total', $default_total, ['class' => 'form-control input_number', 'placeholder' => __('sale.total_amount'), 'required']); !!}
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('additional_notes', __('expense.expense_note') . ':') !!}
                        {!! Form::textarea('additional_notes', $note ?? null, ['class' => 'form-control', 'rows' => 3]); !!}
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <br>
                    <label>
                        {!! Form::checkbox('is_refund', 1, false, ['class' => 'input-icheck', 'id' => 'is_refund']); !!} @lang('lang_v1.is_refund')?
                    </label>@show_tooltip(__('lang_v1.is_refund_help'))
                </div>
                <input type="hidden" name="expense" value="expense">
                <input type="hidden" name="status" value="final">
                @if(!empty($emp_primary_location))
                    <input type="hidden" id="emp_primary_location" value="{{ $emp_primary_location }}">
                @endif
            </div>
        </div>
    </div> <!--box end-->
    @include('expense.recur_expense_form_part')
    @component('components.widget', ['class' => 'box-solid', 'id' => "payment_rows_div", 'title' => __('purchase.add_payment')])
    <div class="payment_row">
        @include('sale_pos.partials.payment_row_form', ['row_index' => 0, 'show_date' => true])
        <hr>
        <div class="row">
            <div class="col-sm-12">
                <div class="pull-right">
                    <strong>@lang('purchase.payment_due'):</strong>
                    <span id="payment_due">{{@num_format(0)}}</span>
                </div>
            </div>
        </div>
    </div>
    @endcomponent
    <div class="col-sm-12 text-center">
        <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
    </div>
{!! Form::close() !!}
</section>
@endsection

@section('css')
<style>
    .img-thumbnail {
        cursor: pointer;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
</style>
@endsection

@include('expense.telegram-docs-modal')

@section('javascript')
<script type="text/javascript">
    $(document).ready(function(){
        // Show the upload modal on page load
        $('#uploadInvoiceModal').modal('show');

        $('.select2').select2({
            matcher: function(params, data) {
                if ($.trim(params.term) === '') {
                    return data;
                }
                if (typeof data.text === 'undefined' || typeof data.id === 'undefined') {
                    return null;
                }
                var term = params.term.toLowerCase();
                var text = data.text.toLowerCase();
                var id = data.id.toString();
                if (text.indexOf(term) > -1 || id === params.term) {
                    return data;
                }
                return null;
            }
        });

        var empPrimaryLocation = $('#emp_primary_location').val();
        if (empPrimaryLocation && $('#location_id').length) {
            $('#location_id').val(empPrimaryLocation).trigger('change');
        }

        $('.paid_on').datetimepicker({
            format: moment_date_format + ' ' + moment_time_format,
            ignoreReadonly: true,
        });

        // File input change handler for initial upload modal
        $('#upload_document').on('change', function() {
            var file = this.files[0];
            if (!file) return;

            var validImageTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (validImageTypes.includes(file.type)) {
                
                // Hide the initial upload modal
                $('#uploadInvoiceModal').modal('hide');

                // Transfer the file to the form's file input for submission
                var fileInput = document.getElementById('upload_document_2');
                if (fileInput && file) {
                    var dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    fileInput.files = dataTransfer.files;
                }

                // Show the preview in the main form
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#ocr_preview_image').attr('src', e.target.result);
                    $('#ocr_image_preview_section').show();
                };
                reader.readAsDataURL(file);

                // Show the document section in the main form
                $('#second_upload_document').show();
            }
        });

        // Handle file upload preview for the second upload
        $('#upload_document_2').on('change', function() {
            var file = this.files[0];
            if (!file) return;

            // Check if file is an image
            var validImageTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!validImageTypes.includes(file.type)) {
                // Not an image, don't show preview
                $('#uploaded_image_preview').hide();
                return;
            }

            // Create a URL for the uploaded file
            var fileURL = URL.createObjectURL(file);

            // Update the preview image
            $('.uploaded-image-popup-link').attr('data-image', fileURL);
            $('.uploaded-image-popup-link img').attr('src', fileURL);
            $('#uploaded_image_preview').show();
        });

        // Handle image popup for pre-filled images
        $('.image-popup-link').on('click', function(e) {
            e.preventDefault();
            var imageSrc = $(this).data('image');
            showImageModal(imageSrc);
        });

        // Handle image popup for uploaded images
        $(document).on('click', '.uploaded-image-popup-link', function(e) {
            e.preventDefault();
            var imageSrc = $(this).data('image');
            showImageModal(imageSrc);
        });

        // Function to show image modal
        function showImageModal(imageSrc) {
            $('#preview_image').attr('src', imageSrc);
            $('#imagePreviewModal').modal('show');
        }

        __page_leave_confirmation('#add_expense_form');
        $(document).on('change', 'input#final_total, input.payment-amount', function() {
            calculateExpensePaymentDue();
        });

        function calculateExpensePaymentDue() {
            var final_total = __read_number($('input#final_total'));
            var payment_amount = __read_number($('input.payment-amount'));
            var payment_due = final_total - payment_amount;
            $('#payment_due').text(__currency_trans_from_en(payment_due, true, false));
        }

        $(document).on('change', '#recur_interval_type', function() {
            if ($(this).val() == 'months') {
                $('.recur_repeat_on_div').removeClass('hide');
            } else {
                $('.recur_repeat_on_div').addClass('hide');
            }
        });

        $('#is_refund').on('ifChecked', function(event) {
            $('#recur_expense_div').addClass('hide');
        });
        $('#is_refund').on('ifUnchecked', function(event) {
            $('#recur_expense_div').removeClass('hide');
        });

        $(document).on('change', '.payment_types_dropdown, #location_id', function(e) {
            var default_accounts = $('select#location_id').length ? 
                $('select#location_id').find(':selected').data('default_payment_accounts') : [];
            var payment_types_dropdown = $('.payment_types_dropdown');
            var payment_type = payment_types_dropdown.val();
            if (payment_type) {
                var default_account = default_accounts && default_accounts[payment_type]['account'] ? 
                    default_accounts[payment_type]['account'] : '';
                var payment_row = payment_types_dropdown.closest('.payment_row');
                var row_index = payment_row.find('.payment_row_index').val();

                var account_dropdown = payment_row.find('select#account_' + row_index);
                if (account_dropdown.length && default_accounts) {
                    account_dropdown.val(default_account);
                    account_dropdown.change();
                }
            }
        });

        // Show the upload section if the modal is closed without a file
        $('#close_modal_button').on('click', function() {
            $('#second_upload_document').show();
        });

        // Image preview click handlers to open modal
        $(document).on('click', '#ocr_preview_image', function() {
            var imageSrc = $(this).attr('src');
            $('#preview_image').attr('src', imageSrc);
            $('#imagePreviewModal').modal('show');
        });
    });


    $(document).ready(function(){
    // Check if we have a pre-filled image from backend
    var ocrImagePath = $('#ocr_image_path_hidden').val();
    var baseUrl = '{{ url("/") }}';
    
    if (ocrImagePath) {
        // We have an image from the backend, let's fetch it and populate the file input
        fetch(baseUrl + '/' + ocrImagePath)
            .then(response => response.blob())
            .then(blob => {
                // Extract filename from path
                var filename = ocrImagePath.split('/').pop();
                
                // Create a File object from the blob
                var file = new File([blob], filename, { type: blob.type });
                
                // Create DataTransfer to add file to input
                var dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                
                // Set the file to the actual upload input
                var fileInput = document.getElementById('upload_document_2');
                if (fileInput) {
                    fileInput.files = dataTransfer.files;
                }
                
                // Show preview
                $('#ocr_preview_image').attr('src', baseUrl + '/' + ocrImagePath);
                $('#ocr_image_preview_section').show();
                $('#second_upload_document').show();
                
                console.log('Image pre-loaded successfully from backend');
            })
            .catch(error => {
                console.error('Error loading image from backend:', error);
                // Fallback: just show the preview without file input
                $('#ocr_preview_image').attr('src', baseUrl + '/' + ocrImagePath);
                $('#ocr_image_preview_section').show();
                $('#second_upload_document').show();
            });
        
        // Hide the initial upload modal since we already have an image
        $('#uploadInvoiceModal').modal('hide');
    } else {
        // No pre-filled image, show the upload modal
        $('#uploadInvoiceModal').modal('show');
    }

    $('.select2').select2({
        matcher: function(params, data) {
            if ($.trim(params.term) === '') {
                return data;
            }
            if (typeof data.text === 'undefined' || typeof data.id === 'undefined') {
                return null;
            }
            var term = params.term.toLowerCase();
            var text = data.text.toLowerCase();
            var id = data.id.toString();
            if (text.indexOf(term) > -1 || id === params.term) {
                return data;
            }
            return null;
        }
    });

    var empPrimaryLocation = $('#emp_primary_location').val();
    if (empPrimaryLocation && $('#location_id').length) {
        $('#location_id').val(empPrimaryLocation).trigger('change');
    }

    $('.paid_on').datetimepicker({
        format: moment_date_format + ' ' + moment_time_format,
        ignoreReadonly: true,
    });

    // File input change handler for initial upload modal
    $('#upload_document').on('change', function() {
        var file = this.files[0];
        if (!file) return;

        var validImageTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (validImageTypes.includes(file.type)) {
            
            // Hide the initial upload modal
            $('#uploadInvoiceModal').modal('hide');

            // Transfer the file to the second input
            var fileInput = document.getElementById('upload_document_2');
            if (fileInput) {
                var dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;
            }

            // Show the preview in the main form
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#ocr_preview_image').attr('src', e.target.result);
                $('#ocr_image_preview_section').show();
            };
            reader.readAsDataURL(file);

            // Show the document section in the main form
            $('#second_upload_document').show();
        }
    });

    // Handle file upload preview for the second upload
    $('#upload_document_2').on('change', function() {
        var file = this.files[0];
        if (!file) {
            $('#uploaded_image_preview').hide();
            return;
        }

        // Check if file is an image
        var validImageTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!validImageTypes.includes(file.type)) {
            $('#uploaded_image_preview').hide();
            return;
        }

        // Create a URL for the uploaded file
        var fileURL = URL.createObjectURL(file);

        // Update the preview image
        $('.uploaded-image-popup-link').attr('data-image', fileURL);
        $('.uploaded-image-popup-link img').attr('src', fileURL);
        $('#uploaded_image_preview').show();
    });

    // Handle image popup for pre-filled images
    $('.image-popup-link').on('click', function(e) {
        e.preventDefault();
        var imageSrc = $(this).data('image');
        showImageModal(imageSrc);
    });

    // Handle image popup for uploaded images
    $(document).on('click', '.uploaded-image-popup-link', function(e) {
        e.preventDefault();
        var imageSrc = $(this).data('image');
        showImageModal(imageSrc);
    });

    // Function to show image modal
    function showImageModal(imageSrc) {
        $('#preview_image').attr('src', imageSrc);
        $('#imagePreviewModal').modal('show');
    }

    __page_leave_confirmation('#add_expense_form');
    
    $(document).on('change', 'input#final_total, input.payment-amount', function() {
        calculateExpensePaymentDue();
    });

    function calculateExpensePaymentDue() {
        var final_total = __read_number($('input#final_total'));
        var payment_amount = __read_number($('input.payment-amount'));
        var payment_due = final_total - payment_amount;
        $('#payment_due').text(__currency_trans_from_en(payment_due, true, false));
    }

    $(document).on('change', '#recur_interval_type', function() {
        if ($(this).val() == 'months') {
            $('.recur_repeat_on_div').removeClass('hide');
        } else {
            $('.recur_repeat_on_div').addClass('hide');
        }
    });

    $('#is_refund').on('ifChecked', function(event) {
        $('#recur_expense_div').addClass('hide');
    });
    
    $('#is_refund').on('ifUnchecked', function(event) {
        $('#recur_expense_div').removeClass('hide');
    });

    $(document).on('change', '.payment_types_dropdown, #location_id', function(e) {
        var default_accounts = $('select#location_id').length ? 
            $('select#location_id').find(':selected').data('default_payment_accounts') : [];
        var payment_types_dropdown = $('.payment_types_dropdown');
        var payment_type = payment_types_dropdown.val();
        if (payment_type) {
            var default_account = default_accounts && default_accounts[payment_type]['account'] ? 
                default_accounts[payment_type]['account'] : '';
            var payment_row = payment_types_dropdown.closest('.payment_row');
            var row_index = payment_row.find('.payment_row_index').val();

            var account_dropdown = payment_row.find('select#account_' + row_index);
            if (account_dropdown.length && default_accounts) {
                account_dropdown.val(default_account);
                account_dropdown.change();
            }
        }
    });

    // Show the upload section if the modal is closed without a file
    $('#close_modal_button').on('click', function() {
        $('#second_upload_document').show();
    });

    // Image preview click handlers to open modal
    $(document).on('click', '#ocr_preview_image', function() {
        var imageSrc = $(this).attr('src');
        $('#preview_image').attr('src', imageSrc);
        $('#imagePreviewModal').modal('show');
    });
});
</script>
@endsection