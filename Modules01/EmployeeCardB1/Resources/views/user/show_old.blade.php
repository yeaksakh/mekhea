@extends('layouts.app')

@section('title', __('lang_v1.view_user'))

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12 no-print"
                style="background-color: #019ca3; padding: 20px; display: flex; align-items: center;">
                @php
                    $img_src = $user->media->display_url ?? 'https://ui-avatars.com/api/?name=' . $user->first_name;
                    // Determine border color based on user status
                    $border_color = $user->status == 'active' ? 'blue' : 'red';
                @endphp

                <!-- Add this within the tab-content div -->


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

        <section class="no-print">
            <nav
                class="navbar-default tw-transition-all tw-duration-5000 tw-shrink-0 tw-rounded-2xl tw-m-[16px] tw-border-2 !tw-bg-white">
                <div class="container-fluid">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                            data-target="#bs-example-navbar-collapse-1" aria-expanded="false"
                            style="margin-top: 3px; margin-right: 3px;">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand"
                            href="{{ action([\Modules\EmployeeCardB1\Http\Controllers\ManageUserController::class, 'index']) }}"><i
                                class="fas fa fa-broadcast-tower"></i> {{ __('employeecardb1::lang.users') }}</a>
                    </div>

                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav">
                            @php
                                $menu_items = [
                                    [
                                        'tab' => 'user_info_tab',
                                        'icon' => 'fas fa-user',
                                        'label' => __('lang_v1.user_info'),
                                    ],
                                    [
                                        'tab' => 'user_visa_tab',
                                        'icon' => 'fas fa-signature',
                                        'label' => __('employeecardb1::lang.user_visa'),
                                    ],
                                    [
                                        'tab' => 'certificate_tab',
                                        'icon' => 'fas fa-signature',
                                        'label' => __('user.certificate'),
                                    ],
                                    [
                                        'tab' => 'passport_card_tab',
                                        'icon' => 'fas fa-address-card',
                                        'label' => __('user.passport_card'),
                                    ],
                                    [
                                        'tab' => 'passport_page1_tab',
                                        'icon' => 'fas fa-address-card',
                                        'label' => __('user.passport_page1'),
                                    ],
                                    [
                                        'tab' => 'name_card_tab',
                                        'icon' => 'fas fa-address-card',
                                        'label' => __('user.card'),
                                    ],
                                    [
                                        'tab' => 'id_card_tab',
                                        'icon' => 'fas fa-address-card',
                                        'label' => __('user.id_card'),
                                    ],
                                    [
                                        'tab' => 'letter_worked_tab',
                                        'icon' => 'fas fa-book',
                                        'label' => __('user.letter_worked'),
                                    ],
                                ];
                                $view_type = 'some_view'; // Replace with your actual logic for active tab
                            @endphp

                            @foreach ($menu_items as $item)
                                <li class="{{ $item['tab'] === $view_type . '_tab' ? 'active' : '' }}">
                                    <a href="#{{ $item['tab'] }}" data-toggle="tab" aria-expanded="true">
                                        <i class="{{ $item['icon'] }}"></i>
                                        {{ $item['label'] }}
                                        @if (isset($item['count']))
                                            <span class="label label-primary pull-right">{{ $item['count'] }}</span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                    </div><!-- /.navbar-collapse -->
                </div><!-- /.container-fluid -->
            </nav>
        </section>

        <div class="row ">
            <div class="col-md-12">
                <div class="nav-tabs-custom">
                    <div class="tab-content">
                        <div class="tab-pane active" id="user_info_tab">
                            <div class="row">
                                <div class="col-md-12">
                                    @include('user.show_details')
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
