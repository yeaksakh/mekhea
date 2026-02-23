@extends('layouts.app')

@section('title', __('productdoc::lang.Products'))

@section('content')
@includeIf('productdoc::layouts.nav')

<section class="content-header no-print">
    <h1>@lang('productdoc::lang.products')</h1>
</section>

<section class="content no-print">
    @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('product_name_filter', __('product.name') . ':') !!}
                {!! Form::text('product_name_filter', null, ['class' => 'form-control', 'id' => 'product_name_filter', 'placeholder' => __('product.name')]) !!}
            </div>
        </div>
       
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('product_date_range', __('report.date_range') . ':') !!}
                {!! Form::text('product_date_range', null, [
                    'placeholder' => __('lang_v1.select_a_date_range'),
                    'class' => 'form-control',
                    'id' => 'product_date_range',
                    'readonly',
                ]) !!}
            </div>
        </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-primary', 'title' => __('product.products')])
        <!-- @slot('tool')
            <div class="box-tools">
                {{-- Optional: Add New Product button (adjust route as needed) --}}
                {{-- <button type="button" class="btn btn-primary pull-right" onclick="window.location='{{ route('products.create') }}'">
                    <i class="fa fa-plus"></i> @lang('messages.add')
                </button> --}}
            </div>
        @endslot -->

        <table class="table table-bordered table-striped" id="products_table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>@lang('messages.action')</th>
                    <th>{{ __('lang_v1.product_image') }}</th>
                    <th>@lang('product.name')</th>
                    <th>@lang('product.sku')</th>
                    <th>@lang('category.category')</th>
                    <th>@lang('productdoc::lang.has_document')</th>
                    {{-- Add more product columns as needed --}}
                </tr>
            </thead>
        </table>
    @endcomponent
</section>

<!-- Reuse the same modal if needed for creating docs -->
<div class="modal fade" id="ProductDoc_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content"></div>
    </div>
</div>

@stop

@section('javascript')
<script>
$(document).ready(function () {
    // Date range picker
    $('#product_date_range').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#product_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            productsTable.ajax.reload();
        }
    );
    $('#product_date_range').on('cancel.daterangepicker', function () {
        $('#product_date_range').val('');
        productsTable.ajax.reload();
    });

    // DataTable columns for Products
    var columns = [
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
        { data: 'image', name: 'image', orderable: false, searchable: false },
        { data: 'name', name: 'name', className: 'table-ellipsis' },
        { data: 'sku', name: 'sku', className: 'table-ellipsis' },
        { data: 'category', name: 'category', className: 'table-ellipsis' },
        { data: 'has_document', name: 'has_document', className: 'table-ellipsis', orderable: false, searchable: false }
    ];

    // Initialize DataTable
    var productsTable = $('#products_table').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        autoWidth: false,
        ajax: {
            url: "{{ route('ProductDoc.index') }}", // Reusing same route; ensure it returns products now
            data: function (d) {
                d.name = $('#product_name_filter').val();
                d.category_id = $('#product_category_id').val();
                if ($('#product_date_range').val()) {
                    var start = $('#product_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#product_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    d.start_date = start;
                    d.end_date = end;
                }
            }
        },
        columns: columns,
        order: [[0, 'desc']]
    });

    // Reload on filter change
    $('#product_name_filter, #product_category_id').on('keyup change', function () {
        productsTable.ajax.reload();
    });

    // Modal handling (same as before — used for creating/editing ProductDoc)
    $('#ProductDoc_modal').on('shown.bs.modal', function () {
        $(this).find('.select2').each(function () {
            if (!$(this).data('select2')) {
                $(this).select2({ dropdownParent: $(this).closest('.modal'), width: '100%' });
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
                editor.on('change', function () { editor.save(); });
            }
        });
    });

    $('#ProductDoc_modal').on('hidden.bs.modal', function () {
        $(this).find('.select2').select2('destroy');
        tinymce.remove('textarea.ProductDoc_description');
        $(this).find('.modal-content').empty();
    });

    // Form submit (same logic)
    $(document).on('submit', 'form#add_ProductDoc_form, form#edit_ProductDoc_form', function (e) {
        e.preventDefault();
        tinymce.triggerSave();
        var formData = new FormData(this);
        $.ajax({
            method: $(this).attr('method'),
            url: $(this).attr('action'),
            dataType: 'json',
            data: formData,
            processData: false,
            contentType: false,
            success: function (result) {
                if (result.success) {
                    $('#ProductDoc_modal').modal('hide');
                    productsTable.ajax.reload();
                    toastr.success(result.msg);
                } else {
                    toastr.error(result.msg);
                }
            }
        });
    });

    // Delete ProductDoc (still valid if deleting from modal)
    $(document).on('click', '.delete-ProductDoc', function (e) {
        e.preventDefault();
        if (confirm('{{ __("messages.confirm_delete") }}')) {
            $.ajax({
                url: $(this).data('href'),
                method: 'DELETE',
                dataType: 'json',
                success: function (result) {
                    if (result.success) {
                        productsTable.ajax.reload();
                        toastr.success(result.msg);
                    } else {
                        toastr.error(result.msg);
                    }
                }
            });
        }
    });

    // Auto-open logic (still relevant)
    @if(session('auto_create_for_product'))
        (function () {
            var productId = {{ session('auto_create_for_product') }};
            var url = '{{ route("ProductDoc.create") }}?product_id=' + productId;
            $('<button class="btn-modal" data-href="' + url + '" data-container="#ProductDoc_modal">').appendTo('body').click().remove();
        })();
    @endif

    @if(session('auto_open_productdoc_id'))
        (function () {
            var docId = {{ session('auto_open_productdoc_id') }};
            var url = '{{ route("ProductDoc.edit", ["id" => "DOC_ID"]) }}'.replace('DOC_ID', docId);
            $('<button class="btn-modal" data-href="' + url + '" data-container="#ProductDoc_modal">').appendTo('body').click().remove();
        })();
    @endif
});
</script>
@endsection