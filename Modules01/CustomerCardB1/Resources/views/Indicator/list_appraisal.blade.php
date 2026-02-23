@extends('layouts.app')
@section('title', __('customercardb1::visa.visa'))
@section('content')
@include('customercardb1::layouts.nav_visa')
<section class="content-header no-print">
    <h1>@lang('customercardb1::visa.visa')</h1>
</section>

<section class="content no-print">
    @component('components.filters', ['title' => __('report.filters')])
    <div class="row">
        <!-- Month and Year Picker -->
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('month_year_picker', __('Select Month') . ':') !!}
                {!! Form::text('month_year_picker', null, [
                'class' => 'form-control',
                'id' => 'month_year_picker',
                'placeholder' => __('Select Month'),
                'readonly'
                ]) !!}
            </div>
        </div>
    </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-primary', 'title' => __('customercardb1::visa.list_appraisal')])
    @slot('tool')
    <div class="box-tools">
        <a class="btn btn-block btn-primary" href="{{ action([Modules\CustomerCardB1\Http\Controllers\IndicatorController::class, 'appraisal']) }}">
            <i class="fa fa-plus"></i> @lang('messages.add')</a>
    </div>
    @endslot
    <div class="table-responsive">
    <table class="table table-bordered table-striped" id="appraisal_table" style="width: 100%;">
        <thead>
            <tr>
                <th>#</th>
                <th>@lang('customercardb1::visa.action')</th>
                <th>@lang('customercardb1::contact.contact')</th>
                <!-- <th>@lang('customercardb1::visa.department')</th> -->
                <th>@lang('customercardb1::visa.month')</th>
                <th>@lang('customercardb1::visa.actual_value')</th>
                <!-- <th>@lang('customercardb1::visa.actual_score')</th> -->
                <th>@lang('customercardb1::contact.created_by')</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    @endcomponent
</section>

<div class="modal fade visa_modal" id="viewAppraisalModal" tabindex="-1" role="dialog" aria-labelledby="viewAppraisalModalLabel" aria-hidden="true">
</div>
@stop

@section('javascript')
<script type="text/javascript">
    var table;

    $(document).ready(function() {
        // Initialize month-year picker
        $('#month_year_picker').datepicker({
            format: "MM yyyy", // Format to display full month name and year
            viewMode: "months",
            minViewMode: "months", // Restrict view to only months
            autoclose: true
        });

        // Destroy existing DataTable instance if it exists
        if ($.fn.DataTable.isDataTable('#appraisal_table')) {
            $('#appraisal_table').DataTable().destroy();
        }

        // Initialize the DataTable
        table = $('#appraisal_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('customercardb1.visa.appraisal.list') }}",
                data: function(d) {
                    var monthYear = $('#month_year_picker').val().split(' ');
                    if (monthYear.length == 2) {
                        d.year = monthYear[1];
                        d.month = new Date(Date.parse(monthYear[0] + " 1")).getMonth() + 1;
                    }
                },
                error: function(xhr, text, error) {
                    console.error('AJAX Error:', xhr.status, text, error);
                    alert('Error: ' + xhr.responseText); 
                }
            },
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
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'contact',
                    name: 'contact'
                },
                // {
                //     data: 'department',
                //     name: 'department'
                // },
                {
                    data: 'appraisal_month',
                    name: 'appraisal_month'
                },
                {
                    data: 'actual_value',
                    name: 'actual_value'
                },
                // {
                //     data: 'actual_score',
                //     name: 'actual_score'
                // },
                {
                    data: 'created_by',
                    name: 'created_by'
                }
            ]
        });

        // Reload DataTable when a new month and year are selected, with debounce
        $('#month_year_picker').on('changeDate', function() {
            clearTimeout(window.reloadTimeout);
            window.reloadTimeout = setTimeout(function() {
                table.ajax.reload();
            }, 300);
        });

        // Handle delete action
        $('#appraisal_table').on('click', '.delete-visa', function() {
            var url = $(this).data('href');
            if (confirm('Are you sure you want to delete this appraisal?')) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(result) {
                        table.ajax.reload();
                        alert('Appraisal deleted successfully');
                    },
                    error: function(xhr) {
                        alert('Error deleting appraisal');
                    }
                });
            }
        });

        // Handle modal view
        $('#appraisal_table').on('click', '.btn-modal', function(e) {
            e.preventDefault();
            var url = $(this).attr('data-href');
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    $('#viewAppraisalModal .modal-content').html(response);
                    $('#viewAppraisalModal').modal('show');
                },
                error: function(xhr) {
                    alert('Failed to load appraisal details: ' + xhr.statusText);
                }
            });
        });
    });
</script>
@endsection