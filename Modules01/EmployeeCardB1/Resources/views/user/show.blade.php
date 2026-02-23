@extends('layouts.app')

@section('title', __('employeecardb1::lang.view_user'))

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row small_hide">
            <div class="col-md-12 no-print"
                style="background-color: #019ca3; padding: 20px; display: flex; align-items: center;">
                @php
                    $img_src = $user->media->display_url ?? 'https://ui-avatars.com/api/?name=' . $user->first_name;
                    // Determine border color based on user status
                    $border_color = $user->status == 'active' ? 'blue' : 'red';
                @endphp

                <!-- Profile Image -->
                <div style="flex: 1; text-align: center;">
                    <img class="profile-user-img img-responsive img-circle" src="{{ $img_src }}"
                        alt="User profile picture"
                        style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 2px solid {{ $border_color }};">
                </div>

                <!-- User Name and Role -->
                <div style="flex: 2; padding-left: 20px;">
                    <h3 class="profile-username text-white">
                        {{ $user->user_full_name }} <small style="color: white;">{{ $user->role_name }}</small>
                    </h3>
                    <p class="text-muted text-white">
                        @lang('business.username'): {{ $user->username }} - @lang('business.email'): {{ $user->email }}
                    </p>
                    <p class="text-muted text-white">
                        @lang('lang_v1.cmmsn_percent'): {{ $user->cmmsn_percent }}% -
                        @if ($user->status == 'active')
                            <span class="label label-success pull-center">
                                @lang('business.is_active')
                            </span>
                        @else
                            <span class="label label-danger pull-center">
                                @lang('lang_v1.inactive')
                            </span>
                        @endif
                    </p>
                </div>

                @can('user.update')
                    <a href="{{ action([\App\Http\Controllers\ManageUserController::class, 'edit'], [$user->id]) }}"
                        class="edit_contact_button btn btn-sm btn-warning no-print">
                        <i class="glyphicon glyphicon-edit"></i>
                        @lang('messages.edit')
                    </a>
                @endcan

                <!-- Print Buttons -->
                <button onclick="printCV('portrait')" class="btn btn-sm btn-success no-print">
                    <i class="fas fa-print"></i> @lang('messages.print')
                </button>
            </div>
        </div>

        <!-- Navbar -->
        @includeIf('employeecardb1::layouts.navbar')
        {{-- @includeIf('employeecardb1::layouts.navbarwidget') --}}

        <div class="row">
            <div class="col-md-12">
                <div class="nav-tabs-custom">
                    <div class="tab-content">
                        <div class="tab-pane active" id="user_info_tab">
                            <div class="row">
                                <div class="col-md-12">
                                    @include('employeecardb1::user.show_details')
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="user_visa_tab">
                            <div class="row">
                                <div class="col-md-12">
                                    @include('employeecardb1::user.partials.card.user_visa')
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="letter_worked_tab">
                            <div class="row">
                                <div class="col-md-12">
                                    @include('employeecardb1::user.partials.card.letter_worked')
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="team_up_tab">
                            <div class="row">
                                <div class="col-md-12" align="center">
                                    @include('employeecardb1::user.partials.card.team_up')
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="passport_card_tab">
                            <div class="row">
                                <div class="col-md-12 " align="center">
                                    @include('employeecardb1::user.partials.card.passport_card')
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="passport_page1_tab">
                            <div class="row">
                                <div class="col-md-12 " align="center">
                                    @include('employeecardb1::user.partials.card.passport_page1')
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="name_card_tab">
                            <div class="row">
                                <div class="col-md-12 " align="center">
                                    @include('employeecardb1::user.partials.card.name_card')
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="id_card_tab">
                            <div class="row">
                                <div class="col-md-12 " align="center">
                                    @include('employeecardb1::user.partials.card.id_card')
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="welcome_card_tab">
                            <div class="row">
                                <div class="col-md-12 " align="center">
                                    @include('employeecardb1::user.partials.card.welcome_card')
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="certificate_tab">
                            <div class="row">
                                <div class="col-md-12 " align="center">
                                    @include('employeecardb1::user.partials.card.certificate')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

        <div class="modal fade schedule" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
        <div class="modal fade edit_schedule" tabindex="-1" role="dialog"></div>

        <div class="modal fade schedule_log_modal" tabindex="-1" role="dialog"></div>
    </section>
@endsection
