{!! Form::open([
    'url' => route('ProductDoc.store'),
    'method' => 'post',
    'id' => 'add_ProductDoc_form',
    'files' => true,
    'class' => 'needs-validation',
    'novalidate' => true
]) !!}

<div class="modal-content">
    <div class="modal-header bg-light border-bottom py-3">
        <h5 class="modal-title font-weight-bold d-flex align-items-center">
            <i class="fa fa-file-alt mr-2 text-primary"></i>
            @lang('productdoc::lang.add_ProductDoc')
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <div class="modal-body py-3">

        {{--  UNIVERSAL  MESSAGE  HOLDER  --}}
        <div id="form-msg" class="alert d-none" role="alert">
            <h6 class="alert-heading mb-2">
                <i class="fa fa-info-circle"></i> <span class="alert-title"></span>
            </h6>
            <div class="alert-body"></div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="row g-3">
            <!-- Category -->
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('productdoc_category_id', __('productdoc::lang.category') . ':', ['class' => 'form-label font-weight-bold']) !!}
                    {!! Form::select('productdoc_category_id', $productdoc_categories, null, [
                        'class' => 'form-control select2',
                        'id' => 'productdoc_category_id',
                        'placeholder' => __('messages.please_select'),
                        'data-allow-clear' => 'true',
                        'required' => true
                    ]) !!}
                    <small class="form-text text-muted">@lang('productdoc::lang.select_category_help')</small>
                </div>
            </div>

            <!-- Product -->
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('Product_1', __('productdoc::lang.Product_1') . ':', ['class' => 'form-label font-weight-bold']) !!}
                    {!! Form::select('Product_1', $product, request('product_id'), [
                        'class' => 'form-control select2',
                        'id' => 'Product_1',
                        'placeholder' => __('messages.please_select'),
                        'required' => true,
                        'data-allow-clear' => 'true'
                    ]) !!}
                    <small class="form-text text-muted">@lang('productdoc::lang.select_product_help')</small>
                </div>
            </div>

            <!-- Source Selection as Tabs -->
            <div class="col-md-12">
                <div class="form-group">
                    <label class="form-label font-weight-bold">
                        <i class="fa fa-exchange-alt mr-2"></i> @lang('productdoc::lang.document_source')
                    </label>
                    <ul class="nav nav-tabs" id="sourceTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="file-tab" data-toggle="tab" href="#file-content" role="tab" aria-controls="file-content" aria-selected="true">
                                <i class="fa fa-file mr-1"></i> @lang('messages.file')
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="text-tab" data-toggle="tab" href="#text-content" role="tab" aria-controls="text-content" aria-selected="false">
                                <i class="fa fa-file-text mr-1"></i> Text
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="link-tab" data-toggle="tab" href="#link-content" role="tab" aria-controls="link-content" aria-selected="false">
                                <i class="fa fa-link mr-1"></i> Link
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="telegram-tab" data-toggle="tab" href="#telegram-content" role="tab" aria-controls="telegram-content" aria-selected="false">
                                <i class="fab fa-telegram-plane mr-1"></i> Telegram
                            </a>
                        </li>
                    </ul>
                    <input type="hidden" name="source_type" id="source_type" value="file">
                </div>
            </div>

            <!-- Tab Content -->
            <div class="col-md-12">
                <div class="tab-content" id="sourceTabContent">
                    <!-- File Upload Section (Default visible) -->
                    <div class="tab-pane fade show active" id="file-content" role="tabpanel" aria-labelledby="file-tab">
                        <h6 class="border-bottom pb-2 mt-2 mb-3 d-flex align-items-center">
                            <i class="fa fa-paperclip mr-2 text-info"></i> @lang('productdoc::lang.upload_documents')
                        </h6>

                        @php
                            $fileFields = ['productFile1_5' => 'File 1'];
                            $maxFileSize = (config('constants.document_size_limit') ?? 2000000) / 10000;
                            $allowedMimes = array_keys(config('constants.document_upload_mimes_types') ?: []);
                        @endphp

                        @foreach ($fileFields as $fieldName => $fieldLabel)
                            <div class="form-group">
                                <label for="{{ $fieldName }}" class="form-label font-weight-bold d-flex align-items-center">
                                    <i class="fa fa-file mr-1 text-muted"></i> {{ $fieldLabel }}
                                </label>
                                <div class="custom-file">
                                    {!! Form::file($fieldName, [
                                        'id' => $fieldName,
                                        'accept' => implode(',', $allowedMimes),
                                        'class' => 'custom-file-input document-input',
                                        'data-max-size' => config('constants.document_size_limit') ?? 2000
                                    ]) !!}
                                    {!! Form::label($fieldName, __('messages.choose_file'), ['class' => 'custom-file-label']) !!}
                                </div>
                                <div class="invalid-feedback" id="file-error" style="display: none;">
                                    Please select a file to upload
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Text Content Section -->
                    <div class="tab-pane fade" id="text-content" role="tabpanel" aria-labelledby="text-tab">
                        <h6 class="border-bottom pb-2 mt-2 mb-3 d-flex align-items-center">
                            <i class="fa fa-file-text mr-2 text-info"></i> Enter Text Content
                        </h6>

                        <div class="form-group">
                            <label class="form-label font-weight-bold">
                                <i class="fa fa-keyboard mr-1"></i> Text Content
                            </label>
                            <textarea name="text_content" id="text_content" class="form-control summernote-text" rows="8" placeholder="Enter your text content here..."></textarea>
                            <div class="invalid-feedback text-error" style="display: none;">Please enter some text content</div>
                            <small class="form-text text-muted mt-2">
                                <i class="fa fa-info-circle mr-1"></i> Paste or type your text content here
                            </small>
                        </div>
                    </div>

                    <!-- Link Content Section -->
                    <div class="tab-pane fade" id="link-content" role="tabpanel" aria-labelledby="link-tab">
                        <h6 class="border-bottom pb-2 mt-2 mb-3 d-flex align-items-center">
                            <i class="fa fa-link mr-2 text-info"></i> Enter Link URL
                        </h6>

                        <div class="form-group">
                            <label for="link_url" class="form-label font-weight-bold">
                                <i class="fa fa-globe mr-1"></i> URL
                            </label>
                            <input type="url" name="link_url" id="link_url" class="form-control" placeholder="https://example.com">
                            <div class="invalid-feedback" id="link-error" style="display: none;">
                                Please enter a valid URL (e.g., https://example.com)
                            </div>
                            <small class="form-text text-muted mt-2">
                                <i class="fa fa-info-circle mr-1"></i> Enter a valid URL starting with http:// or https://
                            </small>
                        </div>
                    </div>

                    <!-- Telegram Message ID Section -->
                    <div class="tab-pane fade" id="telegram-content" role="tabpanel" aria-labelledby="telegram-tab">
                        <h6 class="border-bottom pb-2 mt-2 mb-3 d-flex align-items-center">
                            <i class="fab fa-telegram-plane mr-2 text-info"></i> Telegram Messages
                        </h6>

                        <div class="form-group">
                            <label for="telegram_message_id" class="form-label font-weight-bold">
                                <i class="fab fa-telegram-plane mr-1"></i> Select Telegram Message
                            </label>
                            <select name="telegram_message_id" id="telegram_message_id" class="form-control select2" style="width: 100%;">
                                <option value="">-- Select a Telegram Message --</option>
                            </select>
                            <div id="telegram-loading" style="display: none;" class="text-muted mt-2">
                                <i class="fa fa-spinner fa-spin mr-1"></i> Loading messages...
                            </div>
                            <small class="form-text text-muted">
                                <i class="fa fa-info-circle mr-1"></i> Choose a message from your Telegram history
                            </small>
                            <div class="invalid-feedback" id="telegram-error" style="display: none;">
                                Please select a Telegram message
                            </div>
                        </div>

                        <div class="alert alert-info mb-0">
                            <small><i class="fa fa-lightbulb mr-1"></i> Messages are loaded from your Telegram history file</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer bg-light border-top py-2">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fa fa-times mr-1"></i> @lang('messages.close')
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-save mr-1"></i> @lang('messages.save')
        </button>
    </div>
</div>

{!! Form::close() !!}

<style>
    .required::after {
        content: " *";
        color: #dc3545;
    }
    .form-label {
        margin-bottom: 0.4rem;
        font-size: 0.95rem;
    }
    .custom-file-label::after {
        content: "@lang('messages.browse')";
    }
    .nav-tabs {
        border-bottom: 2px solid #dee2e6;
    }
    .nav-tabs .nav-link {
        border: 1px solid transparent;
        border-top-left-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
    }
    .nav-tabs .nav-link.active {
        color: #495057;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
    }
    .nav-tabs .nav-link:hover,
    .nav-tabs .nav-link:focus {
        border-color: #e9ecef #e9ecef #dee2e6;
    }
    .tab-content {
        padding-top: 1rem;
    }
    .select2-container--default .select2-selection--single.is-invalid {
        border-color: #dc3545 !important;
    }
    #text_content.is-invalid {
        border-color: #dc3545;
    }
    #text_content.is-invalid ~ .invalid-feedback {
        display: block !important;
    }
</style>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Hanuman:wght@100..900&family=Siemreap&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Bayon&family=Hanuman:wght@100..900&family=Siemreap&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Moul&family=Siemreap&display=swap" rel="stylesheet">

<script>
/* helper for your validation routine */
$(document).ready(function() {
  $('.summernote-text').summernote({
    placeholder: 'សូមសរសេរនៅទីនេះ...',
        tabsize: 2,
        height: 300,
    popover: {
      image: [

        // This is a Custom Button in a new Toolbar Area
        ['custom', ['examplePlugin']],
        ['imagesize', ['imageSize100', 'imageSize50', 'imageSize25']],
        ['float', ['floatLeft', 'floatRight', 'floatNone']],
        ['remove', ['removeMedia']]
      ]
    },
    fontNames: ['KhmerOSBassac', 'Moul', 'Siemreap', 'Battambang', 'Bayon', 'Hanuman'],
    fontNamesIgnoreCheck: ['KhmerOSBassac', 'Moul', 'Siemreap', 'Battambang', 'Bayon', 'Hanuman'],
  });
});
    $(document).ready(function () {
        const fileInput = $('#productFile1_5');
        const textInput = $('#text_content');
        const linkUrlInput = $('#link_url');
        const telegramInput = $('#telegram_message_id');
        const sourceTypeInput = $('#source_type');
        const form = $('#add_ProductDoc_form');

        // initialise select2
        $('#productdoc_category_id, #Product_1, #telegram_message_id').select2({
            width: '100%',
            allowClear: true,
            placeholder: '{{ __("messages.please_select") }}'
        });

        // tab switch
        $('#sourceTabs a').on('click', function (e) {
            e.preventDefault();
            $(this).tab('show');

            // clear previous inline errors
            fileInput.removeClass('is-invalid');
            $('#file-error').hide();
            textInput.removeClass('is-invalid');
            $('#text-error').hide();
            linkUrlInput.removeClass('is-invalid');
            $('#link-error').hide();
            telegramInput.next('.select2-container').find('.select2-selection').removeClass('is-invalid');
            $('#telegram-error').hide();
            form.removeClass('was-validated');

            // update hidden source_type
            const href = $(this).attr('href');
            if (href === '#file-content')       sourceTypeInput.val('file');
            else if (href === '#text-content')  sourceTypeInput.val('text');
            else if (href === '#link-content')  sourceTypeInput.val('link');
            else if (href === '#telegram-content') sourceTypeInput.val('telegram');
        });

        // file input label
        fileInput.on('change', function () {
            const name = this.files[0]?.name || '{{ __("messages.choose_file") }}';
            $(this).next('.custom-file-label').text(name).attr('title', name);
        });

        // file size check
        fileInput.on('change', function () {
            const max = parseInt($(this).data('max-size'));
            if (this.files[0] && this.files[0].size > max) {
                alert('{{ __("messages.file_size_exceeded") }}');
                this.value = '';
                $(this).next('.custom-file-label').text('{{ __("messages.choose_file") }}');
            }
        });

        // real-time url fix
        linkUrlInput.on('input', function () {
            if (isValidURL($(this).val().trim())) {
                $(this).removeClass('is-invalid');
                $('#link-error').hide();
            }
        });

        // telegram loader
        $('#telegram-tab').on('shown.bs.tab', function () {
            if (telegramInput.find('option').length <= 1) {
                telegramInput.prop('disabled', true).empty().append('<option value="">-- Loading messages... --</option>').trigger('change');
                $('#telegram-loading').show();

                $.ajax({
                    url: '{{ route("ProductDoc.telegramMessages") }}',
                    method: 'GET',
                    dataType: 'json',
                    success: function (res) {
                        $('#telegram-loading').hide();
                        telegramInput.prop('disabled', false).empty().append('<option value="">-- Select a Telegram Message --</option>');

                        if (res.messages && res.messages.length) {
                            res.messages.forEach(function (m) {
                                const txt = (m.text || m.caption || 'Message ID: ' + m.message_id).substring(0, 50) + '...';
                                telegramInput.append($('<option>', { value: m.message_id, text: `ID: ${m.message_id} - ${txt} (${m.from})` }));
                            });
                        } else {
                            telegramInput.append('<option value="">-- No messages found --</option>');
                        }
                        telegramInput.trigger('change');
                    },
                    error: function () {
                        $('#telegram-loading').hide();
                        telegramInput.prop('disabled', false).empty().append('<option value="">-- Error loading messages --</option>').trigger('change');
                        alert('Failed to load Telegram messages. Please try again.');
                    }
                });
            }
        });
    });

    // helper
    function isValidURL(str) {
        try { new URL(str); return true; } catch {
            try { new URL('https://' + str); return true; } catch { return false; }
        }
    }
</script>