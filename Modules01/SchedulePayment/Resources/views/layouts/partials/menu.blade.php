    @if(auth()->user()->can('manage_modules') && session()->has('business'))
    <div class="recommended-item" data-id="schedulepayment">
        <a href="{{ action([\Modules\SchedulePayment\Http\Controllers\SchedulePaymentController::class, 'index']) }}"  title="{{__('schedulepayment::lang.schedulepayment')}}"
        class="recommended-link {{ request()->segment(2) == 'schedulepayment' ? 'active' : '' }}">
            <img src="{{ asset('public/uploads/SchedulePayment/1754388194_calendar.svg') }}"
                class="recommended-icon" alt="">
            <div>
                <p class="text-base font-medium text-gray-800">{{__('schedulepayment::lang.schedulepayment')}}</p>
                <p class="recommended-text text-sm text-gray-600">Manage {{__('schedulepayment::lang.schedulepayment')}}</p>
            </div>
        </a>
    </div>
    @endif