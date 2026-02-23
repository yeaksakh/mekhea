<div class="recommended-item" data-id="employee-card-b1-users">
    <a href="{{ action([\Modules\EmployeeCardB1\Http\Controllers\ManageUserController::class, 'index']) }}"
       class="recommended-link {{ request()->segment(2) == 'EmployeeCardB1-users' ? 'active' : '' }}">
        <img src="{{ asset('public/uploads/EmployeeCardB1/icons/employee/employee_card.svg') }}"
            class="recommended-icon" alt="">
        <div>
            <p class="text-base font-medium text-gray-800">@lang('employeecardb1::lang.employeecardb1')</p>
            <p class="recommended-text text-sm text-gray-600">Manage users</p>
        </div>
    </a>
</div>