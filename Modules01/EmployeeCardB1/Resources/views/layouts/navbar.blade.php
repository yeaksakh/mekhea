<section class="no-print">
    <nav class="navbar-default tw-transition-all tw-duration-5000 tw-shrink-0 tw-rounded-xl tw-m-[16px] tw-border !tw-bg-white">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/header/small_menu.svg') }}" width="24" height="24" alt="">
                </button>
                <a class="navbar-brand" href="{{ action([\Modules\EmployeeCardB1\Http\Controllers\ManageUserController::class, 'index']) }}">
                    <img src="{{ asset('public/uploads/EmployeeCardB1/icons/employee/employee_card.svg') }}" width="24" height="24" alt="">
                </a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    @php
                        $menu_items = [
                            [
                                'tab' => 'user_info_tab',
                                'icon' => 'fas fa-user',
                                'label' => __('employeecardb1::lang.user_info'),
                                'is_link' => false,
                            ],
                            [
                                'tab' => 'user_visa_tab',
                                'icon' => 'fas fa-signature',
                                'label' => __('employeecardb1::lang.user_visa'),
                                'is_link' => false,
                            ],
                            [
                                'tab' => 'welcome_card_tab',
                                'icon' => 'fas fa-signature',
                                'label' => __('employeecardb1::lang.welcome_card'),
                                'is_link' => false,
                            ],
                            [
                                'tab' => 'certificate_tab',
                                'icon' => 'fas fa-signature',
                                'label' => __('employeecardb1::lang.certificate'),
                                'is_link' => false,
                            ],
                            [
                                'tab' => 'passport_card_tab',
                                'icon' => 'fas fa-address-card',
                                'label' => __('employeecardb1::lang.passport_card'),
                                'is_link' => false,
                            ],
                            [
                                'tab' => 'passport_page1_tab',
                                'icon' => 'fas fa-address-card',
                                'label' => __('employeecardb1::lang.passport_page1'),
                                'is_link' => false,
                            ],
                            [
                                'tab' => 'name_card_tab',
                                'icon' => 'fas fa-address-card',
                                'label' => __('employeecardb1::lang.name_card'),
                                'is_link' => false,
                            ],
                            [
                                'tab' => 'id_card_tab',
                                'icon' => 'fas fa-address-card',
                                'label' => __('employeecardb1::lang.id_card'),
                                'is_link' => false,
                            ],
                        ];
                        $view_type = 'some_view'; // Replace with actual logic for active tab
                    @endphp

                    @foreach ($menu_items as $item)
                        <li class="{{ $item['tab'] === $view_type . '_tab' ? 'active' : '' }}">
                            <a href="#{{ $item['tab'] }}" data-toggle="tab" aria-expanded="true">
                                <i class="{{ $item['icon'] }}"></i> {{ $item['label'] }}
                            </a>
                        </li>
                    @endforeach

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            ផ្សេងៗ <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            @php
                                $dropdown_items = [
                                    [
                                        'tab' => 'letter_worked_tab',
                                        'icon' => 'fas fa-book',
                                        'label' => __('employeecardb1::lang.letter_worked'),
                                        'is_link' => false,
                                    ],
                                    [
                                        'tab' => 'team_up_tab',
                                        'icon' => 'fas fa-users',
                                        'label' => 'Team UP',
                                        'is_link' => false,
                                    ],
                                ];
                            @endphp
                            @foreach ($dropdown_items as $item)
                                <li>
                                    <a href="#{{ $item['tab'] }}" data-toggle="tab">
                                        <i class="{{ $item['icon'] }}"></i> {{ $item['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</section>