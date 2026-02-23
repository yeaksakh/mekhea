<div class="home-grid-tile" data-key="employeecardb1-dashboard">
    <a href="{{ action([\Modules\EmployeeCardB1\Http\Controllers\ManageUserController::class, 'index']) }}"
        title="{{ __('employeecardb1::lang.employeecardb1') }}">
        <img src="{{ asset('public/uploads/EmployeeCardB1/icons/employee/employee_card.svg') }}"
            class="home-icon" alt="">
        <span class="home-label">{{ __('employeecardb1::lang.employeecardb1') }}</span>
    </a>
</div>