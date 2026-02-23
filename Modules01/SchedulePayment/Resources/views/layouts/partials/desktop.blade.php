   @if(auth()->user()->can('manage_modules') && session()->has('business'))
    <div class="home-grid-tile" data-key="schedulepayment">
        <a href="{{ action([\Modules\SchedulePayment\Http\Controllers\SchedulePaymentController::class, 'index']) }}"  title="{{__('schedulepayment::lang.schedulepayment')}}">
            <img src="{{ asset('public/uploads/SchedulePayment/1754388194_calendar.svg') }}" class="home-icon" alt="">
            <span class="home-label">{{__('schedulepayment::lang.schedulepayment')}}</span>
        </a>
    </div>
    @endif