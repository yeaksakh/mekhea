@extends('layouts.app')

@section('title', __('modulecreatemodule::lang.modules_creator'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('modulecreatemodule::lang.modules_creator') }}
        <small>{{ __('modulecreatemodule::lang.manage_modules') }}</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('modulecreatemodule::lang.all_modules')])
    @slot('tool')

    <div class="box-tools">
        <a class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none btn-modal"
            data-href="{{ route('modulecreatemodule.create') }}"
            data-container=".add_module"
            style="border-radius: 5px;"> <!-- Adjust border-radius here -->
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M12 5l0 14" />
                <path d="M5 12l14 0" />
            </svg> @lang('messages.add')
        </a>
    </div>
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="module_table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('modulecreatemodule::lang.module_name') }}</th>
                    <th>{{ __('messages.action') }}</th>
                </tr>
            </thead>
        </table>
    </div>
    @endcomponent

    <div class="modal fade add_module" tabindex="-1" role="dialog" aria-labelledby="createModuleModalLabel"
        aria-hidden="true"></div>

</section>
<!-- /.content -->
@stop

@section('javascript')
<script>
    $(document).ready(function() {
        var module_table = $('#module_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('modulecreatemodule.index') }}',
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
                    data: 'module_name',
                    name: 'module_name',
                    render: function(data, type, row) {
                        // Replace 'B' + business_id from the module_name only if it exists
                        var business_id = "{{ request()->session()->get('user.business_id') }}";
                        var searchString = 'B' + business_id;

                        if (data.includes(searchString)) {
                            return data.replace(searchString, '');
                        }

                        return data; // Return the original module_name if 'B' + business_id is not found
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                },
            ]
        });
        $('.add_module').on('shown.bs.modal', function(e) {

            $('#add_module_modal_form #start_date, #add_module_modal_form #end_date').datepicker({
                autoclose: true,
            });
        });

        $(document).on('submit', '#add_module_modal_form', function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                method: $(this).attr('method'),
                url: $(this).attr('action'),
                dataType: 'json',
                data: formData,
                contentType: false,
                processData: false,
                success: function(result) {
                    if (result.success) {
                        $('.add_module').modal('hide'); // Close modal if success
                        toastr.success(result.msg); // Show success message
                        module_table.ajax.reload(); // Reload DataTable
                    } else {
                        toastr.error(result.msg); // Show error message from server
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to create module:', error);
                    toastr.error('Failed to create module. Please try again.'); // Generic error message
                }
            });
        });

        $(document).on('click', '.delete-module', function(e) {
            e.preventDefault();
            var url = $(this).data('href');
            if (confirm('Are you sure you want to delete this module?')) {
                $.ajax({
                    url: url,
                    method: 'DELETE',
                    dataType: 'json',
                    success: function(result) {
                        if (result.success) {
                            module_table.ajax.reload();
                            toastr.success(result.msg); // Show success message
                        } else {
                            toastr.error(result.msg); // Show error message
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to delete module:', error);
                        toastr.error('Failed to delete module. Please try again.'); // Generic error message
                    }
                });
            }
        });

        // Trigger Install Module Action
        $(document).on('click', '.toggle-module', function() {
            var moduleId = $(this).data('id');
            var moduleName = $(this).data('name'); // Get the module name
            var url = "{{ route('modulecreatemodule.toggle') }}";
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    id: moduleId,
                    name: moduleName,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.msg);
                        module_table.ajax.reload();
                        window.location.reload();
                    } else {
                        toastr.error('Failed to update module status');
                    }
                },
                error: function() {
                    toastr.error('Error occurred while updating module status');
                }
            });
        });
    });
</script>
@endsection