    @if(auth()->user()->can('manage_modules') && session()->has('business'))
    <div class="recommended-item" data-id="employeetracker">
        <a href="{{ action([\Modules\EmployeeTracker\Http\Controllers\EmployeeTrackerController::class, 'index']) }}"  title="{{__('employeetracker::lang.employeetracker')}}"
        class="recommended-link {{ request()->segment(2) == 'employeetracker' ? 'active' : '' }}">
            <img src="{{ asset('public/uploads/EmployeeTracker/1755153909_employee_tracker.svg') }}"
                class="recommended-icon" alt="">
            <div>
                <p class="text-base font-medium text-gray-800">{{__('employeetracker::lang.employeetracker')}}</p>
                <p class="recommended-text text-sm text-gray-600">Manage {{__('employeetracker::lang.employeetracker')}}</p>
            </div>
        </a>
    </div>
    @endif