@component('employeecardb1::layouts.partials.widget', ['class' => 'pos-tab-container', 'title' => __('report.filters')])
    <div class="col-xs-12 pos-tab-menu tw-rounded-lg tw-flex tw-justify-center tw-mb-4">
        <div class="list-group tw-flex tw-flex-row tw-flex-wrap tw-justify-center">
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
                $view_type = request()->get('view', 'user_info');
            @endphp

            @foreach ($menu_items as $item)
                <a href="#{{ $item['tab'] }}" class="list-group-item text-center {{ $item['tab'] === $view_type . '_tab' ? 'active' : '' }}" data-toggle="tab">
                    <i class="{{ $item['icon'] }}"></i> {{ $item['label'] }}
                </a>
            @endforeach
        </div>
    </div>
@endcomponent