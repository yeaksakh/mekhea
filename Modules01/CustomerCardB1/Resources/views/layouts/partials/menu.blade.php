@if (auth()->user()->can('customercardb1.view_customercardb1'))
    <div class="recommended-item" data-id="customer-card-b1-customers">
        <a href="{{ action([\Modules\CustomerCardB1\Http\Controllers\ContactController::class, 'index'], ['type' => 'customer']) }}"
        class="recommended-link {{ request()->segment(2) == 'CustomerCardB1-customers' ? 'active' : '' }}">
            <img src="{{ asset('public/uploads/CustomerCardB1/icons/contact/customer_card.svg') }}"
                class="recommended-icon" alt="">
            <div>
                <p class="text-base font-medium text-gray-800">@lang('customercardb1::lang.customercardb1')</p>
                <p class="recommended-text text-sm text-gray-600">Manage customers</p>
            </div>
        </a>
    </div>
@endif