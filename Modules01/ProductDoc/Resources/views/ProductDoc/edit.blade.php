{!! Form::open([
    'url' => route('ProductDoc.update', $productdoc->id),
    'method' => 'PUT',
    'id' => 'edit_ProductDoc_form',
    'files' => true,
    'class' => 'needs-validation',
    'novalidate' => true
]) !!}

<div class="modal-content">
    {{--  HEADER  --}}
    <div class="modal-header bg-light border-bottom py-3">
        <h5 class="modal-title font-weight-bold d-flex align-items-center">
            <i class="fa fa-edit mr-2 text-primary"></i> @lang('productdoc::lang.edit_productdoc')
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <div class="modal-body py-3">
        {{--  UNIVERSAL  MESSAGE  HOLDER  --}}
        <div id="form-msg" class="alert d-none" role="alert">
            <h6 class="alert-heading mb-2"><i class="fa fa-info-circle"></i> <span class="alert-title"></span></h6>
            <div class="alert-body"></div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        {{--  LARAVEL  ERROR  BAG  --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <h6 class="alert-heading mb-2"><i class="fa fa-exclamation-circle"></i> @lang('messages.error')</h6>
                <ul class="mb-0 pl-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="row g-3">
            <!-- Category -->
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('productdoc_category_id', __('productdoc::lang.category') . ':', ['class' => 'form-label font-weight-bold']) !!}
                    {!! Form::select('productdoc_category_id', $productdoc_categories, $productdoc->category_id, [
                        'class' => 'form-control select2',
                        'id' => 'productdoc_category_id',
                        'placeholder' => __('messages.please_select'),
                        'data-allow-clear' => 'true'
                    ]) !!}
                    <small class="form-text text-muted">@lang('productdoc::lang.select_category_help')</small>
                </div>
            </div>

            <!-- Product -->
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('Product_1', __('productdoc::lang.Product_1') . ':', ['class' => 'form-label font-weight-bold required']) !!}
                    {!! Form::select('Product_1', $product, $productdoc->Product_1, [
                        'class' => 'form-control select2',
                        'id' => 'Product_1',
                        'placeholder' => __('messages.please_select'),
                        'required' => true,
                        'data-allow-clear' => 'true'
                    ]) !!}
                    <small class="form-text text-muted">@lang('productdoc::lang.select_product_help')</small>
                </div>
            </div>

            <!-- Source tabs -->
            <div class="col-md-12">
                <div class="form-group">
                    <label class="form-label font-weight-bold"><i class="fa fa-exchange-alt mr-2"></i> @lang('productdoc::lang.document_source')</label>
                    <ul class="nav nav-tabs" id="sourceTabs" role="tablist">
                        <li class="nav-item"><a class="nav-link {{ !in_array($productdoc->file_type,['telegram','text','link']) ? 'active' : '' }}" id="file-tab" data-toggle="tab" href="#file-content" role="tab"><i class="fa fa-file mr-1"></i> @lang('messages.file')</a></li>
                        <li class="nav-item"><a class="nav-link {{ $productdoc->file_type==='text' ? 'active' : '' }}" id="text-tab" data-toggle="tab" href="#text-content" role="tab"><i class="fa fa-file-text mr-1"></i> Text</a></li>
                        <li class="nav-item"><a class="nav-link {{ $productdoc->file_type==='link' ? 'active' : '' }}" id="link-tab" data-toggle="tab" href="#link-content" role="tab"><i class="fa fa-link mr-1"></i> Link</a></li>
                        <li class="nav-item"><a class="nav-link {{ $productdoc->file_type==='telegram' ? 'active' : '' }}" id="telegram-tab" data-toggle="tab" href="#telegram-content" role="tab"><i class="fab fa-telegram-plane mr-1"></i> Telegram</a></li>
                    </ul>
                    {!! Form::hidden('source_type', $productdoc->file_type ?: 'file', ['id' => 'source_type']) !!}
                </div>
            </div>

            <!-- Tab panes -->
            <div class="col-md-12">
                <div class="tab-content" id="sourceTabContent">
                    <!-- FILE -->
                    <div class="tab-pane fade {{ !in_array($productdoc->file_type,['telegram','text','link']) ? 'show active' : '' }}" id="file-content" role="tabpanel">
                        <h6 class="border-bottom pb-2 mt-2 mb-3"><i class="fa fa-paperclip mr-2 text-info"></i> @lang('productdoc::lang.upload_documents')</h6>
                        @php
                            $fileFields = ['productFile1_5' => 'File 1'];
                            $maxFileSize = (config('constants.document_size_limit') ?? 2000000) / 10000;
                            $allowedMimes = array_keys(config('constants.document_upload_mimes_types') ?: []);
                            $imageExtensions = ['jpg','jpeg','png','gif','bmp','webp'];
                        @endphp
                        @foreach ($fileFields as $fieldName => $fieldLabel)
                            <div class="form-group">
                                <label for="{{ $fieldName }}" class="form-label font-weight-bold"><i class="fa fa-file mr-1 text-muted"></i> {{ $fieldLabel }}</label>
                                <div class="custom-file">
                                    {!! Form::file($fieldName, [
                                          'id' => $fieldName,
                                          'accept' => implode(',', $allowedMimes),
                                          'class' => 'custom-file-input document-input',
                                          'data-max-size' => config('constants.document_size_limit') ?? 2000
                                    ]) !!}
                                    {!! Form::label($fieldName, __('messages.choose_file'), ['class' => 'custom-file-label']) !!}
                                </div>
                                <div class="invalid-feedback" id="file-error" style="display:none;">Please select a file</div>

                                @if ($productdoc->{$fieldName} && $productdoc->file_type !== 'telegram')
                                    @php
                                        $ext  = strtolower(pathinfo($productdoc->{$fieldName}, PATHINFO_EXTENSION));
                                        $path = 'uploads/ProductDoc/' . basename($productdoc->{$fieldName});
                                    @endphp
                                    <div class="mt-3 p-3 bg-light border rounded">
                                        <small class="text-muted d-block mb-2">@lang('messages.current_file'):</small>
                                        <div class="d-flex align-items-center justify-content-between">
                                            @if (in_array($ext, $imageExtensions))
                                                <img src="{{ asset($path) }}" class="img-thumbnail" style="max-width:100px;max-height:100px;">
                                            @elseif($ext === 'pdf')
                                                <a href="{{ asset($path) }}" target="_blank" class="btn btn-sm btn-info"><i class="fa fa-file-pdf"></i> @lang('messages.view_pdf')</a>
                                            @else
                                                <a href="{{ asset($path) }}" target="_blank" class="btn btn-sm btn-info"><i class="fa fa-download"></i> @lang('messages.download_file')</a>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- TEXT -->
                    <div class="tab-pane fade {{ $productdoc->file_type==='text' ? 'show active' : '' }}" id="text-content" role="tabpanel">
                        <h6 class="border-bottom pb-2 mt-2 mb-3"><i class="fa fa-file-text mr-2 text-info"></i> Enter Text Content</h6>
                        <div class="form-group">
                            <label class="form-label font-weight-bold"><i class="fa fa-keyboard mr-1"></i> Text Content</label>
                            {!! Form::textarea('text_content', old('text_content', $productdoc->productFile1_5 ?? ''), [
                                  'id' => 'text_content',
                                  'class' => 'form-control summernote-text',
                                  'rows'  => 8,
                                  'placeholder' => 'Enter your text content here...'
                            ]) !!}
                            <div class="invalid-feedback text-error" style="display:none;">Please enter some text content</div>
                            <small class="form-text text-muted mt-2"><i class="fa fa-info-circle mr-1"></i> Paste or type your text content here</small>
                        </div>
                    </div>

                    <!-- LINK -->
                    <div class="tab-pane fade {{ $productdoc->file_type==='link' ? 'show active' : '' }}" id="link-content" role="tabpanel">
                        <h6 class="border-bottom pb-2 mt-2 mb-3"><i class="fa fa-link mr-2 text-info"></i> Enter Link URL</h6>
                        <div class="form-group">
                            <label for="link_url" class="form-label font-weight-bold"><i class="fa fa-globe mr-1"></i> URL</label>
                            {!! Form::url('link_url', old('link_url', $productdoc->productFile1_5 ?? ''), [
                                  'id'          => 'link_url',
                                  'class'       => 'form-control',
                                  'placeholder' => 'https://example.com'
                            ]) !!}
                            <div class="invalid-feedback" id="link-error" style="display:none;">Please enter a valid URL</div>
                            <small class="form-text text-muted mt-2"><i class="fa fa-info-circle mr-1"></i> Enter a valid URL starting with http:// or https://</small>
                        </div>
                    </div>

                    <!-- TELEGRAM -->
                    <div class="tab-pane fade {{ $productdoc->file_type==='telegram' ? 'show active' : '' }}" id="telegram-content" role="tabpanel">
                        <h6 class="border-bottom pb-2 mt-2 mb-3"><i class="fab fa-telegram-plane mr-2 text-info"></i> Telegram Messages</h6>
                        <div class="form-group">
                            <label for="telegram_message_id" class="form-label font-weight-bold"><i class="fab fa-telegram-plane mr-1"></i> Select Telegram Message</label>
                            {!! Form::select('telegram_message_id', [], null, [
                                  'id' => 'telegram_message_id',
                                  'class' => 'form-control select2',
                                  'style' => 'width:100%;',
                                  'placeholder' => '-- Select a Telegram Message --'
                            ]) !!}
                            <div id="telegram-loading" style="display:none;" class="text-muted mt-2"><i class="fa fa-spinner fa-spin mr-1"></i> Loading messages...</div>
                            <small class="form-text text-muted"><i class="fa fa-info-circle mr-1"></i> Choose a message from your Telegram history</small>
                            <div class="invalid-feedback" id="telegram-error" style="display:none;">Please select a Telegram message</div>
                        </div>

                        @if ($productdoc->file_type === 'telegram' && $productdoc->productFile1_5)
                            <div class="alert alert-success mb-3">
                                <i class="fab fa-telegram-plane mr-2"></i>
                                <strong>Current Message:</strong> Telegram Message #{{ $productdoc->productFile1_5 }}
                                <a href="https://t.me/c/3332101476/{{ $productdoc->productFile1_5 }}" target="_blank" class="btn btn-sm btn-success ml-2">
                                    <i class="fab fa-telegram-plane"></i> View
                                </a>
                            </div>
                        @endif
                        <div class="alert alert-info mb-0"><small><i class="fa fa-lightbulb mr-1"></i> Messages are loaded from your Telegram history file</small></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--  FOOTER  --}}
    <div class="modal-footer bg-light border-top py-2">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times mr-1"></i> @lang('messages.close')</button>
        <button type="submit" class="btn btn-primary"><i class="fa fa-save mr-1"></i> @lang('messages.save')</button>
    </div>
</div>
{!! Form::close() !!}

{{--  CSS  --}}
<style>
    .required::after { content:" *"; color:#dc3545; }
    .form-label { margin-bottom:.4rem; font-size:.95rem; }
    .custom-file-label::after { content:"@lang('messages.browse')"; }
    .nav-tabs { border-bottom:2px solid #dee2e6; }
    .nav-tabs .nav-link.active { color:#495057; background:#fff; border-color:#dee2e6 #dee2e6 #fff; }
    .tab-content { padding-top:1rem; }
    .select2-container--default .select2-selection--single.is-invalid { border-color:#dc3545 !important; }
    #text_content.is-invalid { border-color:#dc3545; }
    #text_content.is-invalid ~ .invalid-feedback { display:block !important; }
</style>

{{--  SCRIPTS  --}}
<link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Hanuman:wght@100..900&family=Siemreap&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Bayon&family=Hanuman:wght@100..900&family=Siemreap&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Moul&family=Siemreap&display=swap" rel="stylesheet">

<script>
$(function(){
    /* summernote */
    $('.summernote-text').summernote({
        placeholder: 'Enter text here...',
        height: 300,
        fontNames: ['KhmerOSBassac','Moul','Siemreap','Battambang','Bayon','Hanuman'],
        fontNamesIgnoreCheck: ['KhmerOSBassac','Moul','Siemreap','Battambang','Bayon','Hanuman']
    });

    const fileInput = $('#productFile1_5');
    const textInput = $('#text_content');
    const linkUrlInput = $('#link_url');
    const telegramInput = $('#telegram_message_id');
    const sourceTypeInput = $('#source_type');
    const form = $('#edit_ProductDoc_form');

    /* select2 */
    $('#productdoc_category_id, #Product_1, #telegram_message_id').select2({
        width:'100%', allowClear:true, placeholder:'{{ __("messages.please_select") }}'
    });

    /* tab switch */
    $('#sourceTabs a').on('click', function(e){
        e.preventDefault(); $(this).tab('show');
        /* clear previous errors */
        fileInput.removeClass('is-invalid'); $('#file-error').hide();
        textInput.removeClass('is-invalid'); $('.text-error').hide();
        linkUrlInput.removeClass('is-invalid'); $('#link-error').hide();
        telegramInput.next('.select2-container').find('.select2-selection').removeClass('is-invalid');
        $('#telegram-error').hide(); form.removeClass('was-validated');
        /* update hidden source_type */
        const href = $(this).attr('href');
        if (href === '#file-content')       sourceTypeInput.val('file');
        else if (href === '#text-content')  sourceTypeInput.val('text');
        else if (href === '#link-content')  sourceTypeInput.val('link');
        else if (href === '#telegram-content') sourceTypeInput.val('telegram');
    });

    /* file label + size check */
    fileInput.on('change', function(){
        const name = this.files[0]?.name || '{{ __("messages.choose_file") }}';
        $(this).next('.custom-file-label').text(name).attr('title', name);
    }).on('change', function(){
        const max = parseInt($(this).data('max-size'));
        if(this.files[0] && this.files[0].size > max){
            alert('{{ __("messages.file_size_exceeded") }}'); this.value='';
            $(this).next('.custom-file-label').text('{{ __("messages.choose_file") }}');
        }
    });

    /* real-time url fix */
    linkUrlInput.on('input', function(){
        if(isValidURL($(this).val().trim())){
            $(this).removeClass('is-invalid');
            $('#link-error').hide();
        }
    });

    /* telegram loader */
    $('#telegram-tab').on('shown.bs.tab', function(){
        if(telegramInput.find('option').length <= 1){
            telegramInput.prop('disabled', true).empty().append('<option value="">Loading messages...</option>').trigger('change');
            $.ajax({
                url:'{{ route("ProductDoc.telegramMessages") }}', method:'GET', dataType:'json',
                success:function(res){
                    telegramInput.empty().append('<option value="">-- Select a Telegram Message --</option>');
                    if(res.messages && res.messages.length){
                        res.messages.forEach(m=>{
                            const txt = (m.text||m.caption||'Message ID: '+m.message_id).substring(0,50)+'...';
                            telegramInput.append($('<option>',{value:m.message_id,text:`ID: ${m.message_id} - ${txt} (${m.from})`}));
                        });
                    }else{
                        telegramInput.append('<option value="">-- No messages found --</option>');
                    }
                    telegramInput.prop('disabled', false).trigger('change');
                },
                error:function(){
                    telegramInput.prop('disabled', false).empty().append('<option value="">-- Error loading messages --</option>').trigger('change');
                    alert('Failed to load Telegram messages. Please try again.');
                }
            });
        }
    });

    /* helper */
    function isValidURL(str){
        try{ new URL(str); return true; }catch{
            try{ new URL('https://' + str); return true; }catch{ return false; }
        }
    }
});
</script>