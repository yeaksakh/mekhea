@extends('layouts.app')
@section('title', __('documentary::lang.Documentary'))

@section('content')
    @includeIf('documentary::layouts.nav')

    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        <h1>@lang('documentary::lang.category')</h1>
    </section>

    <!-- Main content -->
    <section class="content no-print">
        <div class="box box-solid">
            <div class="box-header with-border">
                <h5 class="box-title">@lang('documentary::lang.all_category')</h5>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-sm btn-primary btn-modal" data-href="{{ route('Documentary-categories.create') }}" data-container=".category_modal">
                        <i class="fa fa-plus"></i> @lang('messages.add')
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="categories_table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('documentary::lang.name')</th>
                                <th>@lang('documentary::lang.parent_category')</th>
                                <th>@lang('documentary::lang.image')</th>
                                <th>@lang('documentary::lang.description')</th>
                                <th>@lang('messages.action')</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade category_modal" tabindex="-1" role="dialog" aria-labelledby="createCategoryModalLabel" aria-hidden="true"></div>
@stop

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        var table = $('#categories_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ action([\Modules\Documentary\Http\Controllers\DocumentaryController::class, 'getCategories']) }}",
            },
            order: [[1, 'desc']],
            columns: [
                {
                    data: null,
                    name: 'id',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                { data: 'name', name: 'name' },
                { data: 'parent_name', name: 'parent_name' },
                { data: 'image', name: 'image' },
                { data: 'description', name: 'description',
                    className: 'table-ellipsis',
                    render: function(data, type, row) {
                        if (!data) return '';
                        var decoded = $('<textarea/>').html(data).text(); // decode HTML entities
                        return decoded.replace(/<[^>]*>/g, '');
                    }
                },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });

        $(document).on('click', '.btn-modal', function(e) {
            e.preventDefault();
            var container = $(this).data('container');
            $.ajax({
                url: $(this).data('href'),
                dataType: 'html',
                success: function(result) {
                    $(container).html(result).modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load the form:', error);
                }
            });
        });

        $(document).on('submit', '#category_add_form, #category_edit_form', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                method: $(this).attr('method'),
                url: $(this).attr('action'),
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false,
                success: function(result) {
                    if (result.success) {
                        $('.category_modal').modal('hide');
                        table.ajax.reload();
                        toastr.success(result.msg);
                    } else {
                        toastr.error(result.msg);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to save category:', error);
                    toastr.error('Failed to save category');
                }
            });
        });

        $(document).on('click', '.delete-category', function(e) {
            e.preventDefault();
            var url = $(this).data('href');
            if (confirm('Are you sure you want to delete this category?')) {
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
                        console.error('Failed to delete category:', error);
                        toastr.error('Failed to delete category');
                    }
                });
            }
        });
    });
</script>
@endsection
