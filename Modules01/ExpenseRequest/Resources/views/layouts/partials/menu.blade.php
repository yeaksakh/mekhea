@if (auth()->user()->can('expenserequest.view_expenserequest'))
    <div class="recommended-item" data-id="expenserequest">
        <a href="{{ action([\Modules\ExpenseRequest\Http\Controllers\ExpenseController::class, 'indexExpenseRequest']) }}"
        class="recommended-link {{ request()->segment(2) == 'get-expense-request' ? 'active' : '' }}">
            <img src="{{ asset('public/uploads/ExpenseRequest/icons/expense_request/expense_request.svg') }}"
                class="recommended-icon" alt="">
            <div>
                <p class="text-base font-medium text-gray-800">@lang('expenserequest::lang.expenserequest')</p>
                <p class="recommended-text text-sm text-gray-600">Expense Request</p>
            </div>
        </a>
    </div>
@endif