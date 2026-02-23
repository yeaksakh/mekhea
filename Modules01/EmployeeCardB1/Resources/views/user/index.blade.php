@extends('layouts.app')
@section('title', __('user.users'))

@section('content')
    {{-- @includeIf('employeecardb1::layouts.nav') --}}
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('user.users')
            <small class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold">@lang('user.manage_users')</small>
        </h1>
        <!-- <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
            <li class="active">Here</li>
        </ol> -->
    </section>

    <!-- Main content -->

    <section class="content">
        {{-- @include('manage_user.partials.hrm_menu') --}}
        @component('components.filters', ['title' => __('report.filters')])
            <!-- allow_login Filter -->
            <div class="col-md-3">
                <div class="form-group">
                    <label>
                        {!! Form::checkbox('allow_login', 1, false, ['class' => 'form-check-input', 'id' => 'allow_login']) !!} <strong>@lang('lang_v1.allow_login')</strong>
                    </label>
                </div>
                <div class="form-group">
                    <label>
                        {!! Form::checkbox('not_allow_login', 1, false, ['class' => 'form-check-input', 'id' => 'not_allow_login']) !!} <strong>@lang('lang_v1.login_not_allowed')</strong>
                    </label>
                </div>
            </div>

            <!-- Add a Search input field for Username -->
            <div class="col-md-3">
                <div class="form-group">
                    <label for="role">@lang('user.name'):</label>
                    {!! Form::select('user', $usersQuery->pluck('first_name', 'id')->toArray(), null, [
                        'class' => 'form-control',
                        'id' => 'user',
                        'placeholder' => __('messages.select_user'),
                    ]) !!}
                </div>
            </div>

            <!-- Role Filter -->
            <div class="col-md-3">
                <div class="form-group">
                    <label for="role">@lang('user.role'):</label>
                    {!! Form::select('role', $roles, null, [
                        'class' => 'form-control',
                        'id' => 'role',
                        'placeholder' => __('messages.select_role'),
                    ]) !!}
                </div>
            </div>
        @endcomponent

        @component('components.widget', ['class' => 'box-primary', 'title' => __('user.all_users')])
            @can('user.create')
                @slot('tool')
                    <div class="box-tools">
                        <a class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full"
                            href="{{ action([\App\Http\Controllers\ManageUserController::class, 'create']) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 5l0 14" />
                                <path d="M5 12l14 0" />
                            </svg> @lang('messages.add')
                        </a>
                    </div>
                @endslot
            @endcan
            @can('user.view')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="users_table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('business.id')</th>
                                <th>@lang('business.username')</th>
                                <th>@lang('user.name')</th>
                                <th>@lang('user.role')</th>
                                <th>@lang('business.email')</th>
                                <th>@lang('messages.action')</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @endcan
        @endcomponent

        <div class="modal fade user_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

    </section>
    <!-- /.content -->
@stop
@section('javascript')
    <script type="text/javascript">
        $(document).ready(function () {
            const users_table = $('#users_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/employeecardb1/EmployeeCardB1-users',
                    data: d => ({
                        ...d,
                        allow_login: $('#allow_login').is(':checked') ? 1 : 0,
                        not_allow_login: $('#not_allow_login').is(':checked') ? 1 : 0,
                        role: $('#role').val(),
                        id: $('#user').val()
                    })
                },
                columnDefs: [{ targets: [4], orderable: false, searchable: false }],
                columns: [
                    {
                        data: null,
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        render: (data, type, row, meta) => meta.row + meta.settings._iDisplayStart + 1
                    },
                    { data: 'employee_id' },
                    { data: 'username' },
                    { data: 'full_name' },
                    { data: 'role' },
                    { data: 'email' },
                    { data: 'action' }
                ]
            });

            // Reload table on filter changes
            $('#allow_login, #not_allow_login, #user, #role').on('change', () => users_table.ajax.reload());

            // Handle delete user action
            $(document).on('click', 'button.delete_user_button', function () {
                swal({
                    title: LANG.sure,
                    text: LANG.confirm_delete_user,
                    icon: 'warning',
                    buttons: true,
                    dangerMode: true
                }).then(willDelete => {
                    if (willDelete) {
                        $.ajax({
                            method: 'DELETE',
                            url: $(this).data('href'),
                            dataType: 'json',
                            success: result => {
                                toastr[result.success ? 'success' : 'error'](result.msg);
                                if (result.success) users_table.ajax.reload();
                            }
                        });
                    }
                });
            });
        });
    </script>

@endsection
