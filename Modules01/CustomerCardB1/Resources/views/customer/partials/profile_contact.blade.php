<div class="info_col">
    @include('customercardb1::customer.contact_basic_info')
</div>
<hr>

@if( $contact->type != 'customer')
<div class="info_col">
    @include('customercardb1::customer.contact_tax_info')
</div>
@endif
<hr>
<div class="info_col">
    @include('contact.contact_payment_info')
</div>