@extends('layouts.app')
@section('title', __('documentary::lang.Documentary'))

@section('content')
    @includeIf('documentary::layouts.nav')

    <section class="content-header no-print">
        <h1>@lang('documentary::lang.documentary')</h1>
    </section>

    <section class="content no-print">
        @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('documentary_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range', null, [
                        'placeholder' => __('lang_v1.select_a_date_range'),
                        'class' => 'form-control',
                        'id' => 'documentary_date_range',
                        'readonly',
                    ]) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('category_id', __('documentary::lang.category') . ':') !!}
                    {!! Form::select('category_id', ['' => 'All Categories'] + $category, null, ['class' => 'form-control select2', 'id' => 'category_id', 'style' => 'width:100%;']) !!}
                </div>
            </div>
        @endcomponent

        @component('components.widget', ['class' => 'box-primary', 'title' => __('documentary::lang.all_Documentary')])
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="btn btn-primary btn-modal" data-href="{{ action([\Modules\Documentary\Http\Controllers\DocumentaryController::class, 'create']) }}" data-container="#Documentary_modal">
                        <i class="fa fa-plus"></i> @lang('messages.add')
                    </button>
                </div>
            @endslot

            <table class="table table-bordered table-striped" id="Documentary_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('messages.action')</th>
                        <th>@lang('documentary::lang.subcategory')</th>
                        <th>@lang('documentary::lang.category')</th>
                        <th>@lang('documentary::lang.create_by')</th>
                        <th>@lang('documentary::lang.title_1')</th>
                        <th>@lang('documentary::lang.url_5')</th>
                        <th>@lang('documentary::lang.file_6')</th>
                    </tr>
                </thead>
            </table>
        @endcomponent
    </section>

    <div class="modal fade" id="Documentary_modal" tabindex="-1" role="dialog" aria-labelledby="createDocumentaryModalLabel"></div>
@stop

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        $('#documentary_date_range').daterangepicker(
            dateRangeSettings,
            function(start, end) {
                $('#documentary_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                table.ajax.reload();
            }
        );
        $('#documentary_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#documentary_date_range').val('');
            table.ajax.reload();
        });

        var table = $('#Documentary_table').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            autoWidth: false,
            ajax: {
                url: "{{ action([\Modules\Documentary\Http\Controllers\DocumentaryController::class, 'index']) }}",
                data: function(d) {
                    d.category_id = $('#category_id').val();
                    if ($('#documentary_date_range').val()) {
                        var start = $('#documentary_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        var end = $('#documentary_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        d.start_date = start;
                        d.end_date = end;
                    }
                }
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
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'subcategory', name: 'subcategory', className: 'table-ellipsis' },
                { data: 'category', name: 'category', className: 'table-ellipsis' },
                { data: 'create_by', name: 'create_by', className: 'table-ellipsis' },
                { data: 'title_1', name: 'title_1', className: 'table-ellipsis' },
                { data: 'url_5', name: 'url_5', className: 'table-ellipsis' },
                { data: 'file_6', name: 'file_6', className: 'table-ellipsis' },
            ],
        });

        $('#category_id').on('change', function() {
            table.ajax.reload(null, false);
        });

        $('#Documentary_modal').on('shown.bs.modal', function(e) {
            $('#Documentary_modal .select2').select2();
            $('form#add_Documentary_form #start_date, form#add_Documentary_form #end_date').datepicker({ autoclose: true });
            tinymce.init({ selector: '#Documentary_modal textarea.Documentary_description' });
        });

        $('#Documentary_modal').on('hidden.bs.modal', function() {
            tinymce.remove('#Documentary_modal textarea.Documentary_description');
        });

        $(document).on('submit', 'form#add_Documentary_form, #edit_Documentary_form, #audit_Documentary_form', function(e) {
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
                success: function(result) {
                    if (result.success) {
                        $('div#Documentary_modal').modal('hide');
                        table.ajax.reload();
                        toastr.success(result.msg);
                    } else {
                        toastr.error(result.msg);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to save Documentary:', error);
                    toastr.error('Failed to save Documentary');
                }
            });
        });

        $(document).on('click', '.delete-Documentary', function(e) {
            e.preventDefault();
            var url = $(this).data('href');
            if (confirm('Are you sure you want to delete this Documentary?')) {
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
                        console.error('Failed to delete Documentary:', error);
                        toastr.error('Failed to delete Documentary');
                    }
                });
            }
        });
    });
</script>
@endsection
