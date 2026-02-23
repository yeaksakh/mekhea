@component('customercardb1::layouts.partials.widget', ['class' => 'pos-tab-container', 'title' => __('report.filters')])
    <div class="col-xs-12 pos-tab-menu tw-rounded-lg tw-flex tw-justify-center tw-mb-4">
        <div class="list-group tw-flex tw-flex-row tw-flex-wrap tw-justify-center">
            @php
                $menuItems = [
                    ['icon' => 'fas fa-user', 'label' => __('contact.profile'), 'tab' => 'profile_tab'],
                    ['icon' => 'fas fa-passport', 'label' => __('customercardb1::lang.customer_visa'), 'tab' => 'customer_visa_tab'],
                    ['icon' => 'fas fa-passport', 'label' => __('contact.passport_card'), 'tab' => 'passport_card_tab'],
                    ['icon' => 'fas fa-passport', 'label' => __('contact.passport_page1'), 'tab' => 'passport_page1_tab'],
                    ['icon' => 'fas fa-passport', 'label' => __('contact.passport_page2'), 'tab' => 'passport_page2_tab'],
                    ['icon' => 'fas fa-shield-alt', 'label' => __('contact.certificate_of_yeaksa_member'), 'tab' => 'certificate_of_yeaksa_member_tab'],
                    ['icon' => 'fas fa-shield-alt', 'label' => __('contact.certificate_of_yeaksa_training'), 'tab' => 'certificate_of_yeaksa_training_tab'],
                    ['icon' => 'fas fa-address-card', 'label' => __('user.name_card'), 'tab' => 'name_card_tab'],
                    ['icon' => 'fas fa-address-card', 'label' => __('user.id_card'), 'tab' => 'id_card_tab'],
                    ['icon' => 'fas fa-file-signature', 'label' => __('contact.franchise_contract'), 'tab' => 'franchise_contract_tab'],
                    ['icon' => 'fas fa-file-signature', 'label' => __('contact.products_contract'), 'tab' => 'products_contract_tab'],
                    ['icon' => 'fas fa-file-signature', 'label' => __('contact.notification_of_termination_using_franchise_contract'), 'tab' => 'notification_of_termination_using_franchise_contract_tab'],
                    ['icon' => 'fas fa-users', 'label' => __('contact.checklist'), 'tab' => 'checklist_tab'],
                ];

                // Define tabs to exclude for customers
                $customerExcludedTabs = ['activity_tab'];

                // Define tabs to exclude for suppliers
                $supplierExcludedTabs = ['activity_tab'];

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
                <a href="#{{ $item['tab'] }}" class="list-group-item text-center {{ $item['tab'] === $view_type . '_tab' ? 'active' : '' }}" data-toggle="tab" aria-expanded="true">
                    <i class="{{ $item['icon'] }}" style="margin-right: 8px;"></i>{{ $item['label'] }}
                </a>
            @endforeach
        </div>
    </div>
@endcomponent