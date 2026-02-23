@extends('layouts.app')

@section('title', __('productdoc::lang.ProductDoc'))

@section('content')
@includeIf('productdoc::layouts.nav')

<section class="content-header no-print">
    <h1>@lang('productdoc::lang.productdoc')</h1>
</section>

<section class="content no-print">
    @component('components.filters', ['title' => __('report.filters')])
    <div class="col-sm-3">
        <div class="form-group">
            {!! Form::label('Product_1', __('productdoc::lang.Product_1').':') !!}
            {!! Form::select('Product_1', $product, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('productdoc_date_range', __('report.date_range') . ':') !!}
            {!! Form::text('date_range', null, [
                'placeholder' => __('lang_v1.select_a_date_range'),
                'class' => 'form-control',
                'id' => 'productdoc_date_range',
                'readonly',
            ]) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('category_id', __('productdoc::lang.category') . ':') !!}
            {!! Form::select('category_id', ['' => 'All Categories'] + $category, null, ['class' => 'form-control select2', 'id' => 'category_id', 'style' => 'width:100%;']) !!}
        </div>
    </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-primary', 'title' => __('productdoc::lang.all_ProductDoc')])
    @slot('tool')
    <div class="box-tools">
        <button type="button"
            class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right btn-modal"
            data-href="{{ route('ProductDoc.create' , ['product_id' => request()->query('product_id') ] ?? nul ) }}"
            data-container="#ProductDoc_modal">
            <i class="fa fa-plus"></i> @lang('messages.add')
        </button>
    </div>
    <!-- <button type="button"
        class="tw-dw-btn tw-bg-gradient-to-r tw-from-green-600 tw-to-teal-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right ml-2 btn-modal"
        data-href="{{ route('ProductDoc.telegramMessages') }}"
        data-container="#TelegramMessages_modal">
        <i class="fa fa-comments"></i> View Telegram Messages
    </button> -->
    @endslot

    <table class="table table-bordered table-striped" id="ProductDoc_table">
        <thead>
            <tr>
                <th>#</th>
                <th>@lang('messages.action')</th>
                <th>{{__('lang_v1.product_image')}} </th>
                <th>@lang('productdoc::lang.category')</th>
                <th>@lang('productdoc::lang.create_by')</th>
                <th>@lang('productdoc::lang.Product_1')</th>
                <th>@lang('productdoc::lang.productFile1_5')</th>
                {{-- 👇 Dynamic columns --}}
                @foreach($dynamicFields as $fieldKey => $label)
                    <th>{{ is_string($label) ? __($label) : $label }}</th>
                @endforeach
            </tr>
        </thead>
    </table>
    @endcomponent
</section>

<!-- Modal placeholder -->
<div class="modal fade" id="ProductDoc_modal" tabindex="-1" role="dialog" aria-labelledby="ProductDocModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <!-- Content loaded via AJAX -->
        </div>
    </div>
</div>
<div class="modal fade" id="TelegramMessages_modal" tabindex="-1" role="dialog" aria-labelledby="TelegramMessagesModalLabel">
</div>
@stop

@section('javascript')

<script>
    // Pass dynamic fields to JS
    const dynamicFields = @json($dynamicFields);
    
</script>

<script type="text/javascript">
    
    $(document).ready(function () {
        
        // Date range picker
        $('#productdoc_date_range').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#productdoc_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                table.ajax.reload();
            }
        );
        $('#productdoc_date_range').on('cancel.daterangepicker', function (ev, picker) {
            $('#productdoc_date_range').val('');
            table.ajax.reload();
        });

        // Build columns dynamically
        let columns = [
            {
                data: null,
                name: 'id',
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'action', name: 'action', orderable: false, searchable: false },
            {
                data: 'image', name: 'image'
            },
            { data: 'category', name: 'category', className: 'table-ellipsis' },
            { data: 'create_by', name: 'create_by', className: 'table-ellipsis', visible: false },
            { data: 'Product_1', name: 'Product_1', className: 'table-ellipsis' },
            { data: 'productFile', name: 'productFile1_5' }
        ];

        // // 👇 Add dynamic field columns
        // Object.keys(dynamicFields).forEach(fieldKey => {
        //     columns.push({
        //         data: fieldKey,
        //         name: fieldKey,
        //         className: 'table-ellipsis'
        //     });
        // });

        // Initialize DataTable
        var table = $('#ProductDoc_table').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            autoWidth: false,
            ajax: {
                url: "{{ route('ProductDoc.productDoc' ,  ['product_id' => request()->query('product_id')]) }}",
                data: function (d) {
                    d.category_id = $('#category_id').val();
                    d.Product_1 = $('#Product_1').val();
                    if ($('#productdoc_date_range').val()) {
                        var start = $('#productdoc_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        var end = $('#productdoc_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        d.start_date = start;
                        d.end_date = end;
                    }
                }
            },
            order: [[0, 'desc']],
            columns: columns
        });

        // Reload on filter change
        $('#category_id, #Product_1').on('change', function () {
            table.ajax.reload();
        });

        // Modal shown
        $('#ProductDoc_modal').on('shown.bs.modal', function (e) {
            $(this).find('.select2').each(function () {
                if (!$(this).data('select2')) {
                    $(this).select2({
                        dropdownParent: $('#ProductDoc_modal'),
                        width: '100%'
                    });
                }
            });

            $(this).find('#start_date, #end_date').datepicker({ autoclose: true });

            tinymce.init({
                selector: 'textarea.ProductDoc_description',
                menubar: false,
                height: 200,
                plugins: 'lists link',
                toolbar: 'bold italic underline | alignleft aligncenter alignright | bullist numlist | link',
                setup: function (editor) {
                    editor.on('change', function () {
                        editor.save();
                    });
                }
            });
        });

        // Modal hidden
        $('#ProductDoc_modal').on('hidden.bs.modal', function () {
            $(this).find('.select2').each(function () {
                if ($(this).data('select2')) {
                    $(this).select2('destroy');
                }
            });
            tinymce.remove('textarea.ProductDoc_description');
            $(this).find('.modal-content').empty();
        });

        // Form submit
        $(document).on('submit', 'form#add_ProductDoc_form, form#edit_ProductDoc_form, form#audit_ProductDoc_form', function (e) {
            e.preventDefault();
            tinymce.triggerSave();

            var formData = new FormData(this);
            var $form = $(this);

            $.ajax({
                method: $form.attr('method'),
                url: $form.attr('action'),
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false,
                success: function (result) {
                    $('#ProductDoc_modal').modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                    $('body').css('padding-right', '');
                    table.ajax.reload();

                    if (result.success === true || result.success === 'true') {
                        toastr.success(result.msg || '{{ __("messages.success") }}');
                    } else {
                        toastr.error(result.msg || '{{ __("messages.something_went_wrong") }}');
                    }
                },
                error: function (xhr) {
                    let msg = '{{ __("messages.something_went_wrong") }}';
                    const errors = [];

                    // Laravel validation errors (422)
                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        $.each(xhr.responseJSON.errors, function (key, messages) {
                            errors.push(messages[0]);
                        });
                        msg = errors.join('<br>');
                        toastr.error(msg);
                    }
                    // Custom error message from backend (e.g., { "msg": "..." })
                    else if (xhr.responseJSON?.msg) {
                        toastr.error(xhr.responseJSON.msg);
                    }
                    // Fallback
                    else {
                        toastr.error(msg);
                    }
                }
            });
        });

        // Delete
        $(document).on('click', '.delete-ProductDoc', function (e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete this ProductDoc?')) {
                $.ajax({
                    url: $(this).data('href'),
                    method: 'DELETE',
                    dataType: 'json',
                    success: function (result) {
                        if (result.success) {
                            table.ajax.reload();
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                    error: function () {
                        toastr.error('Failed to delete ProductDoc');
                    }
                });
            }
        });

        // Auto-open modals from session
        @if(session('auto_create_for_product'))
            (function () {
                var productId = {{ session('auto_create_for_product') }};
                var url = '{{ route("ProductDoc.create") }}?product_id=' + productId;
                var $tempBtn = $('<button type="button" class="btn-modal" data-href="' + url + '" data-container="#ProductDoc_modal">').appendTo('body');
                $tempBtn.trigger('click');
                $tempBtn.remove();
            })();
        @endif

        @if(session('auto_open_productdoc_id'))
            (function () {
                var docId = {{ session('auto_open_productdoc_id') }};
                var url = '{{ route("ProductDoc.edit", ["id" => "DOC_ID"]) }}'.replace('DOC_ID', docId);
                var $tempBtn = $('<button type="button" class="btn-modal" data-href="' + url + '" data-container="#ProductDoc_modal">').appendTo('body');
                $tempBtn.trigger('click');
                $tempBtn.remove();
            })();
        @endif
        
    });
</script>
@endsection