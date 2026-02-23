<section class="no-print" id="main-navbar-section">
    <nav class="navbar-default tw-transition-all tw-duration-5000 tw-shrink-0 tw-rounded-xl tw-m-[16px] tw-border !tw-bg-white">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/header/small_menu.svg') }}" width="24" height="24" alt="">
                </button>
                <a class="navbar-brand" href="{{ action([\Modules\CustomerCardB1\Http\Controllers\ContactController::class, 'index'], ['type' => 'customer']) }}">
                    <img src="{{ asset('public/uploads/CustomerCardB1/icons/contact/customer_card.svg') }}" width="24" height="24" alt="">
                </a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    @php
                        $menuItems = [
                            ['icon' => 'fas fa-user', 'label' => __('customercardb1::contact.profile'), 'tab' => 'profile_tab', 'is_link' => false],
                            [
                                'icon' => 'fas fa-passport',
                                'label' => __('customercardb1::lang.customer_visa'),
                                'tab' => 'customer_visa_tab',
                                'is_link' => false,
                            ],
                            [
                                'icon' => 'fas fa-passport',
                                'label' => __('customercardb1::lang.passport_card'),
                                'tab' => 'passport_card_tab',
                                'is_link' => false,
                            ],
                            [
                                'icon' => 'fas fa-passport',
                                'label' => __('customercardb1::lang.passport_page1'),
                                'tab' => 'passport_page1_tab',
                                'is_link' => false,
                            ],
                            [
                                'icon' => 'fas fa-passport',
                                'label' => __('customercardb1::lang.passport_page2'),
                                'tab' => 'passport_page2_tab',
                                'is_link' => false,
                            ],
                            [
                                'icon' => 'fas fa-shield-alt',
                                'label' => __('customercardb1::lang.welcome_card'),
                                'tab' => 'welcome_card_tab',
                                'is_link' => false,
                            ],
                            [
                                'icon' => 'fas fa-shield-alt',
                                'label' => __('customercardb1::lang.certificate_of_yeaksa_member'),
                                'tab' => 'certificate_of_yeaksa_member_tab',
                                'is_link' => false,
                            ],
                            [
                                'icon' => 'fas fa-shield-alt',
                                'label' => __('customercardb1::lang.certificate_of_yeaksa_training'),
                                'tab' => 'certificate_of_yeaksa_training_tab',
                                'is_link' => false,
                            ],
                            [
                                'tab' => 'name_card_tab',
                                'icon' => 'fas fa-address-card',
                                'label' => __('customercardb1::lang.name_card'),
                                'is_link' => false,
                            ],
                            [
                                'tab' => 'id_card_tab',
                                'icon' => 'fas fa-address-card',
                                'label' => __('customercardb1::lang.id_card'),
                                'is_link' => false,
                            ],
                            // [
                            //     'icon' => 'fas fa-file-signature',
                            //     'label' => __('customercardb1::lang.franchise_contract'),
                            //     'tab' => 'franchise_contract_tab',
                            //     'is_link' => false,
                            // ],
                            // [
                            //     'icon' => 'fas fa-file-signature',
                            //     'label' => __('customercardb1::lang.products_contract'),
                            //     'tab' => 'products_contract_tab',
                            //     'is_link' => false,
                            // ],
                            // [
                            //     'icon' => 'fas fa-file-signature',
                            //     'label' => __('customercardb1::lang.notification_of_termination_using_franchise_contract'),
                            //     'tab' => 'notification_of_termination_using_franchise_contract_tab',
                            //     'is_link' => false,
                            // ],
                            [
                                'icon' => 'fas fa-users',
                                'label' => __('customercardb1::lang.checklist'),
                                'tab' => 'checklist_tab',
                                'is_link' => false,
                            ],
                        ];

                        // Define tabs to exclude for customers
                        $customerExcludedTabs = ['activity_tab', 'activity_tab'];

                        // Define tabs to exclude for suppliers
                        $supplierExcludedTabs = ['activity_tab', 'activity_tab'];

                        // Filter menu items based on contact type
                        if ($contact->type === 'supplier') {
                            $menuItems = array_filter($menuItems, function ($item) use ($supplierExcludedTabs) {
                                return !in_array($item['tab'], $supplierExcludedTabs);
                            });
                        } elseif ($contact->type === 'customer') {
                            $menuItems = array_filter($menuItems, function ($item) use ($customerExcludedTabs) {
                                return !in_array($item['tab'], $customerExcludedTabs);
                            });
                        }

                        $view_type = request()->get('view', 'profile');
                    @endphp

                    @foreach ($menuItems as $item)
                        <li class="{{ $item['tab'] === $view_type . '_tab' ? 'active' : '' }}">
                            <a href="#{{ $item['tab'] }}" data-toggle="tab" aria-expanded="true">
                                <i class="{{ $item['icon'] }}"></i> {{ $item['label'] }}
                            </a>
                        </li>
                    @endforeach

                    {{-- New dropdown tab with one item --}}
                    <li class="dropdown {{ $view_type === 'test_new_dropdown' ? 'active' : '' }}">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            ផ្សេងៗ <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#letter_allow_production_tab" data-toggle="tab">
                                    <i class="fas fa-file-alt"></i> លិខិតអនុញ្ញាតផលិតកម្ម
                                </a>
                            </li>
                            <li>
                                <a href="#franchise_contract_tab" data-toggle="tab">
                                    <i class="fas fa-file-alt"></i> {{ __('customercardb1::lang.franchise_contract') }}
                                </a>
                            </li>
                            <li>
                                <a href="#products_contract_tab" data-toggle="tab">
                                    <i class="fas fa-file-alt"></i> {{ __('customercardb1::lang.products_contract') }}
                                </a>
                            </li>
                            <li>
                                <a href="#notification_of_termination_using_franchise_contract_tab" data-toggle="tab">
                                    <i class="fas fa-file-alt"></i> {{ __('customercardb1::lang.notification_of_termination_using_franchise_contract') }}
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</section>