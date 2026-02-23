<table class="table table-bordered" 
id="contact_payments_table">
    <thead>
        <tr>
        <th>#</th>
            <th>@lang('lang_v1.paid_on')</th>
            <th>@lang('purchase.ref_no')</th>
            <th>@lang('sale.amount')</th>
            <th>@lang('lang_v1.payment_method')</th>
            <th>@lang('account.payment_for')</th>
            <th>@lang('messages.action')</th>
        </tr>
    </thead>
    <tbody>
        @forelse($payments as $payment)
            @php
                $count_child_payments = count($payment->child_payments);
            @endphp
           <tr>
                <td>{{ $loop->iteration }}</td> <!-- Moved here -->
                @include('contact.partials.payment_row', compact('payment', 'count_child_payments', 'payment_types'))
            </tr>

            @if($count_child_payments > 0)
                @foreach($payment->child_payments as $child_payment)
                   <tr>
                        <td>{{ $loop->parent->iteration }}.{{ $loop->iteration }}</td> <!-- Optional child numbering -->
                        @include('contact.partials.payment_row', ['payment' => $child_payment, 'count_child_payments' => 0, 'payment_types' => $payment_types, 'parent_payment_ref_no' => $payment->payment_ref_no])
                    </tr>
                @endforeach
            @endif
        @empty
            <tr>
                <td colspan="6" class="text-center">@lang('purchase.no_records_found')</td>
            </tr>
        @endforelse
    </tbody>
</table>
<div class="text-right" style="width: 100%; height: 100vh;" id="contact_payments_pagination">
    {{ $payments->links() }}
</div>