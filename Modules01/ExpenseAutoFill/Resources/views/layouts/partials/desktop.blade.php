   @if(auth()->user()->can('manage_modules') && session()->has('business'))
    <div class="home-grid-tile" data-key="expenseautofill">
        <a href="{{ action([\Modules\ExpenseAutoFill\Http\Controllers\ExpenseAutoFillController::class, 'index']) }}"  title="{{__('expenseautofill::lang.expenseautofill')}}">
            <img src="{{ asset('public/uploads/ExpenseAutoFill/1761547752_ExpenseAutoFill.svg') }}" class="home-icon" alt="">
            <span class="home-label">{{__('expenseautofill::lang.expenseautofill')}}</span>
        </a>
    </div>
    @endif