{{-- @php
    use Illuminate\Support\Facades\DB;

    // Check if $user is available (passed to the view)
    if (!isset($user) || !$user) {
        dd('Error: User not available in the view');
    }

    // Fetch visa appraisals for the specific user
    $visa_appraisals = DB::table('visa_appraisals')
        ->leftJoin('users', 'visa_appraisals.created_by', '=', 'users.id')
        ->where('visa_appraisals.created_by', $user->id)
        ->get();

    // Debug the results
    dd($user, $visa_appraisals);
@endphp --}}

<section class="content no-print">
    @component('components.filters', ['title' => __('report.filters')])
        <div class="row">
            <!-- Month and Year Picker -->
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('month_year_picker', __('report.select_month') . ':') !!}
                    {!! Form::text('month_year_picker', null, [
                        'class' => 'form-control',
                        'id' => 'month_year_picker',
                        'placeholder' => __('report.select_month'),
                        'readonly'
                    ]) !!}
                </div>
            </div>
        </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-primary', 'title' => __('visa::lang.list_appraisal')])
        @slot('tool')
            <div class="box-tools">
                @if (auth()->user()->can('visa.create'))
                    <a class="btn btn-block btn-primary" href="{{ action([\Modules\EmployeeCardB1\Http\Controllers\IndicatorController::class, 'appraisal']) }}">
                        <i class="fa fa-plus"></i> @lang('messages.add')
                    </a>
                @endif
            </div>
        @endslot
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="user_visa_table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('visa::lang.action')</th>
                        <th>@lang('visa::lang.contact')</th>
                        <th>@lang('visa::lang.month')</th>
                        <th>@lang('visa::lang.actual_value')</th>
                        <th>@lang('visa::lang.created_by')</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    @endcomponent
</section>

<div class="modal fade visa_modal" id="viewAppraisalModal" tabindex="-1" role="dialog" aria-labelledby="viewAppraisalModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <!-- Modal content will be loaded via AJAX -->
        </div>
    </div>
</div>

@section('javascript')
    <script type="text/javascript">
        var table;

        $(document).ready(function() {
            // Initialize month-year picker
            $('#month_year_picker').datepicker({
                format: "MM yyyy",
                viewMode: "months",
                minViewMode: "months",
                autoclose: true
            });

            // Destroy existing DataTable instance if it exists
            if ($.fn.DataTable.isDataTable('#user_visa_table')) {
                $('#user_visa_table').DataTable().destroy();
            }

            // Initialize the DataTable
            table = $('#user_visa_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('employeecardb1.visa_appraisals', $user->id) }}",
                    data: function(d) {
                        var monthYear = $('#month_year_picker').val().split(' ');
                        if (monthYear.length === 2) {
                            d.year = monthYear[1];
                            d.month = new Date(Date.parse(monthYear[0] + " 1")).getMonth() + 1;
                        }
                    },
                    error: function(xhr) {
                        console.error('AJAX Error:', xhr.status, xhr.responseText);
                        alert('Error loading data: ' + (xhr.responseText || 'Request failed'));
                    }
                },
                columns: [
                    {
                        data: null,
                        name: 'row_number',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                    { data: 'contact', name: 'contact' },
                    { data: 'appraisal_month', name: 'appraisal_month' },
                    { data: 'actual_value', name: 'actual_value' },
                    { data: 'created_by', name: 'created_by' }
                ],
                // Optional: Add debug to inspect the data
                initComplete: function(settings, json) {
                    console.log('DataTable initialized with data:', json);
                }
            });

            // Reload DataTable on month/year change
            $('#month_year_picker').on('changeDate', function() {
                clearTimeout(window.reloadTimeout);
                window.reloadTimeout = setTimeout(function() {
                    table.ajax.reload();
                }, 300);
            });

            // Handle modal view
            $('#user_visa_table').on('click', '.btn-modal', function(e) {
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
                        alert('Failed to load appraisal details: ' + (xhr.statusText || 'Request failed'));
                    }
                });
            });

            // Handle delete action
            $('#user_visa_table').on('click', '.delete-visa', function() {
                var url = $(this).data('href');
                if (confirm('Are you sure you want to delete this appraisal?')) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(result) {
                            if (result.success) {
                                table.ajax.reload();
                                alert('Appraisal deleted successfully');
                            } else {
                                alert('Delete failed: ' + (result.message || 'Unknown error'));
                            }
                        },
                        error: function(xhr) {
                            alert('Error deleting appraisal: ' + (xhr.responseText || 'Request failed'));
                        }
                    });
                }
            });
        });
    </script>
@endsection