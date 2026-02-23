   @if(auth()->user()->can('manage_modules') && session()->has('business'))
    <div class="home-grid-tile" data-key="employeetracker">
        <a href="{{ action([\Modules\EmployeeTracker\Http\Controllers\EmployeeTrackerController::class, 'index']) }}"  title="{{__('employeetracker::lang.employeetracker')}}">
            <img src="{{ asset('public/uploads/EmployeeTracker/1755153909_employee_tracker.svg') }}" class="home-icon" alt="">
            <span class="home-label">{{__('employeetracker::lang.employeetracker')}}</span>
        </a>
    </div>
    @endif