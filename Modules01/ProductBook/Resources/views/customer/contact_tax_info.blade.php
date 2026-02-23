<h2 style="border-bottom: 0.5px solid #333; padding-bottom: 5px; font-size: 18px;"> @lang('contact.tax_no')</h2>
<p class="text-muted">
    {{ $contact->tax_number }}
</p>
@if($contact->pay_term_type)
    <strong> @lang('contact.pay_term_period')</strong>
    <p class="text-muted">
       {{ $contact->pay_term_number }} {{ __('lang_v1.' . $contact->pay_term_type) }}
    </p>
@endif