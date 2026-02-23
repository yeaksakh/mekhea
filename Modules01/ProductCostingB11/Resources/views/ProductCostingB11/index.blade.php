@extends('layouts.app')
@section('title', __('productcostingb11::lang.ProductCostingB11'))
@section('content')
@includeIf('productcostingb11::layouts.nav')
<section class="content-header no-print">
    <h1>@lang('productcostingb11::lang.productcostingb11')</h1>
</section>
<section class="content no-print">
    @component('components.filters', ['title' => __('report.filters')])
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('productcostingb11_date_range', __('report.date_range') . ':') !!}
            {!! Form::text('date_range', null, [
            'placeholder' => __('lang_v1.select_a_date_range'),
            'class' => 'form-control',
            'id' => 'productcostingb11_date_range',
            'readonly',
            ]) !!}
        </div>
    </div>
    @endcomponent
    @component('components.widget', ['class' => 'box-primary', 'title' => __('productcostingb11::lang.all_ProductCostingB11')])
    @slot('tool')
    <div class="box-tools">
        <button type="button" class="btn btn-block btn-primary btn-modal" data-href="{{ route('ProductCostingB11.create') }}"
            data-container=".ProductCostingB11_modal">
            <i class="fa fa-plus"></i> @lang('messages.add')
        </button>
    </div>
    @endslot
    <table class="table table-bordered table-striped" id="ProductCostingB11_table">
        <thead>
            <tr>
                <th>#</th>
                <th>@lang('productcostingb11::lang.category')</th>
                <th>@lang('productcostingb11::lang.create_by')</th>
                <th>@lang('productcostingb11::lang.product_1')</th>
                <th>@lang('productcostingb11::lang.cost_2')</th>
                <th>@lang('productcostingb11::lang.qty_3')</th>
                <th>@lang('productcostingb11::lang.cost_per_unit')</th>
                <th>@lang('messages.action')</th>
            </tr>
        </thead>
    </table>
    @endcomponent

</section>
<div class="modal fade ProductCostingB11_modal" tabindex="-1" role="dialog" aria-labelledby="createProductCostingB11ModalLabel" aria-hidden="true"></div>
@stop

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {

        $('#productcostingb11_date_range').daterangepicker(
            dateRangeSettings,
            function(start, end) {
                $('#productcostingb11_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(
                    moment_date_format));
                table.ajax.reload();
            }
        );
        $('#productcostingb11_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#productcostingb11_date_range').val('');
            table.ajax.reload();
        });

        var table = $('#ProductCostingB11_table').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            autoWidth: false,
            ajax: {
                url: "{{ action([\Modules\ProductCostingB11\Http\Controllers\ProductCostingB11Controller::class, 'index']) }}",
                data: function(d) {

                    if ($('#productcostingb11_date_range').val()) {
                        var start = $('#productcostingb11_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        var end = $('#productcostingb11_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        d.start_date = start;
                        d.end_date = end;
                    }

                }
            },
            order: [
                [1, 'desc']
            ],
            columns: [{
                    data: null,
                    name: 'id',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'category_id',
                    name: 'category_id'
                },
                {
                    data: 'create_by',
                    name: 'create_by'
                },
                {
                    data: 'product_1',
                    name: 'product_1'
                },

                {
                    data: 'total_value',
                    name: 'total_value'
                },

                {
                    data: 'total_qty',
                    name: 'total_qty'
                },
                {
                    data: 'cost_per_unit',
                    name: 'cost_per_unit'
                },

                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
        });


        $(document).on('change', '#product_1', function() {
            table.ajax.reload();
        });


        $(document).on('change', '#cost_2', function() {
            table.ajax.reload();
        });


        $(document).on('change', '#qty_3', function() {
            table.ajax.reload();
        });


        function initializeSelect2InModal() {
            $('.ProductCostingB11_modal .select2').select2({
                width: '100%',
                dropdownParent: $(".ProductCostingB11_modal")
            });
        }

        $(document).on('click', '.btn-modal', function(e) {
            e.preventDefault();
            var href = $(this).data('href'); // Correctly define the href variable
            var container = $(this).data('container');
            $.ajax({
                url: href,
                type: 'GET',
                success: function(data) {
                    $(container).html(data);
                    $(container).modal('show');
                    initializeSelect2InModal(); // Initialize Select2 after loading content into modal
                },
                error: function(xhr) {
                    alert('An error occurred while loading the modal.');
                }
            });
        });

        $(document).on('submit', '#add_ProductCostingB11_form, #edit_ProductCostingB11_form, #audit_ProductCostingB11_form', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                method: $(this).attr('method'),
                url: $(this).attr('action'),
                dataType: 'json',
                data: formData, // Use FormData object
                processData: false, // Prevent jQuery from processing the data
                contentType: false, // Prevent jQuery from setting the content type
                success: function(result) {
                    if (result.success) {
                        $('.ProductCostingB11_modal').modal('hide');
                        table.ajax.reload();
                        toastr.success(result.msg);
                    } else {
                        toastr.error(result.msg);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to save ProductCostingB11:', error);
                    toastr.error('Failed to save ProductCostingB11');
                }
            });
        });

        $(document).on('click', '.delete-ProductCostingB11', function(e) {
            e.preventDefault();
            var url = $(this).data('href');
            if (confirm('Are you sure you want to delete this ProductCostingB11?')) {
                $.ajax({
                    url: url,
                    method: 'DELETE',
                    dataType: 'json',
                    success: function(result) {
                        if (result.success) {
                            table.ajax.reload();
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to delete ProductCostingB11:', error);
                        toastr.error('Failed to delete ProductCostingB11');
                    }
                });
            }
        });
    });
</script>
@endsection