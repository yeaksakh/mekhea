@extends('layouts.app')
@section('title', __('visa::lang.visa'))
@section('content')
@include('visa::layouts.nav')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        @lang('visa::lang.appraisal_list')
    </h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary'])
    @slot('tool')
    <div class="box-tools">
        <a href="{{ route('visa.indicator.create') }}" class="btn btn-block btn-success">
            <i class="fas fa-plus-circle"></i> @lang('visa::lang.add')
        </a>
    </div>
    @endslot

    <div class="table-responsive">
        <table class="table table-bordered table-striped ajax_view" style="width: 100%;" id="indicators-table">
            <thead>
                <tr>
                    <th>@lang('messages.action')</th>
                    <th>@lang('visa::lang.visa_title')</th>
                    <th>@lang('visa::lang.created_at')</th>
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
                [2, 'desc']
            ],
            scrollY: "75vh",
            scrollX: true,
            scrollCollapse: true,
            ajax: {
                url: "{{ route('visa.indicator.index') }}",
                data: function(d) {
                    d = __datatable_ajax_callback(d);
                }
            },
            columns: [
                {
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
                    data: 'created_at',
                    name: 'created_at'
                }
            ],
            fnDrawCallback: function(oSettings) {
                __currency_convert_recursively($('#indicators-table'));
            }
        });
    });

    // Delete indicator event
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
