@extends('layouts.app')
@section('title', __('customercardb1::visa.visa'))
@section('content')
@include('customercardb1::layouts.nav_visa')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        @lang('customercardb1::visa.appraisal_list')
    </h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary'])
    @slot('tool')
    <div class="box-tools">
        <a href="{{ route('customercardb1.visa.indicator.create') }}" class="btn btn-block btn-success">
            <i class="fas fa-plus-circle"></i> @lang('customercardb1::visa.add')
        </a>
    </div>
    @endslot

    <div class="table-responsive">
        <table class="table table-bordered table-striped ajax_view" style="width: 100%;" id="indicators-table">
            <thead>
                <tr>
                    <th>@lang('messages.action')</th>
                    <th>@lang('customercardb1::visa.title')</th>
                    <th>@lang('customercardb1::visa.department')</th>
                    <th>@lang('customercardb1::contact.created_at')</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    @endcomponent
</section>
@stop

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        indicators_table = $('#indicators-table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [
                [3, 'desc']
            ],
            scrollY: "75vh",
            scrollX: true,
            scrollCollapse: true,
            ajax: {
                url: "{{ route('customercardb1.visa.indicator.index') }}",
                data: function(d) {
                    d = __datatable_ajax_callback(d);
                }
            },
            columns: [{
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'department',
                    name: 'department.name'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                }
            ],
            columnDefs: [{targets: [2], visible: false}],
            fnDrawCallback: function(oSettings) {
                __currency_convert_recursively($('#indicators-table'));
            }
        });
    });
    // Add this to your JavaScript section
    $(document).on('click', '.delete_indicator', function(e) {
        e.preventDefault();
        var href = $(this).data('href');
        swal({
            title: LANG.sure,
            text: LANG.confirm_delete_indicator,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: href,
                    method: 'DELETE',
                    dataType: 'json',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(result) {
                        if (result.success) {
                            toastr.success(result.msg);
                            indicators_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            }
        });
    });
</script>
@endsection