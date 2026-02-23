<section class="no-print" id="main-navbar-section">
    <nav class="navbar-default tw-transition-all tw-duration-5000 tw-shrink-0 tw-rounded-xl tw-m-[16px] tw-border !tw-bg-white">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/header/small_menu.svg') }}" width="24" height="24" alt="">
                </button>
                <a class="navbar-brand" href="{{ action([\Modules\CustomerCardB1\Http\Controllers\ContactController::class, 'index'], ['type' => 'customer']) }}">
                    <img src="{{ asset('public/uploads/CustomerCardB1/icons/contact/customer_card.svg') }}" width="24" height="24" alt="">
                </a>
            </div>

            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    @php
                        $menuItems = [
                            [
                                'icon' => 'fas fa-passport',
                                'label' => __('productbook::lang.inner_page'),
                                'tab' => 'inner_page_tab',
                            ],
                            [
                                'icon' => 'fas fa-passport',
                                'label' => __('productbook::lang.cover_page'),
                                'tab' => 'cover_page_tab',
                            ],
                            [
                                'icon' => 'fas fa-file',
                                'label' => __('productbook::lang.package_xs'),
                                'tab' => 'package_xs_tab',
                            ],
                            [
                                'icon' => 'fas fa-file',
                                'label' => __('productbook::lang.package_b'),
                                'tab' => 'package_b_tab',
                            ],
                            [
                                'icon' => 'fas fa-file',
                                'label' => __('productbook::lang.package_a'),
                                'tab' => 'package_a_tab',
                            ],
                            [
                                'icon' => 'fas fa-file',
                                'label' => __('productbook::lang.24page'),
                                'tab' => 'package_24page_tab',
                            ],
                            [
                                'icon' => 'fas fa-file',
                                'label' => __('productbook::lang.20page'),
                                'tab' => 'package_20page_tab',
                            ],
                            [
                                'icon' => 'fas fa-file',
                                'label' => __('productbook::lang.16page'),
                                'tab' => 'package_16page_tab',
                            ],
                            [
                                'icon' => 'fas fa-file',
                                'label' => __('productbook::lang.12page'),
                                'tab' => 'package_12page_tab',
                            ],
                            [
                                'icon' => 'fas fa-file',
                                'label' => __('productbook::lang.8page'),
                                'tab' => 'package_8page_tab',
                            ],
                            [
                                'icon' => 'fas fa-file',
                                'label' => __('productbook::lang.4page'),
                                'tab' => 'package_4page_tab',
                            ],
                            [
                                'icon' => 'fas fa-shield-alt',
                                'label' => __('productbook::lang.package_franchise'),
                                'tab' => 'package_franchise_tab',
                            ],
                        ];
                        $view_type = request()->get('view', isset($view_type) ? $view_type : 'inner_page');
                    @endphp

                    @foreach ($menuItems as $item)
                        <li class="{{ $item['tab'] === $view_type . '_tab' ? 'active' : '' }}">
                            @if($item['tab'] === 'package_franchise_tab')
                                <a href="{{ route('sr_productPackagePrice1') }}" target="_blank" rel="noopener noreferrer">
                                    <i class="{{ $item['icon'] }}"></i> {{ $item['label'] }}
                                </a>
                            @else
                                <a href="#{{ $item['tab'] }}" data-toggle="tab" aria-expanded="true">
                                    <i class="{{ $item['icon'] }}"></i> {{ $item['label'] }}
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </nav>
</section>
