    @if(auth()->user()->can('manage_modules') && session()->has('business'))
    <div class="recommended-item" data-id="expenseautofill">
        <a href="{{ action([\Modules\ExpenseAutoFill\Http\Controllers\ExpenseAutoFillController::class, 'index']) }}"  title="{{__('expenseautofill::lang.expenseautofill')}}"
        class="recommended-link {{ request()->segment(2) == 'expenseautofill' ? 'active' : '' }}">
            <img src="{{ asset('public/uploads/ExpenseAutoFill/1761547752_ExpenseAutoFill.svg') }}"
                class="recommended-icon" alt="">
            <div>
                <p class="text-base font-medium text-gray-800">{{__('expenseautofill::lang.expenseautofill')}}</p>
                <p class="recommended-text text-sm text-gray-600">Manage {{__('expenseautofill::lang.expenseautofill')}}</p>
            </div>
        </a>
    </div>
    @endif