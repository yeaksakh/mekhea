@php
    $custom_labels = json_decode(session('business.custom_labels'), true);
@endphp

@if(!empty($contact->custom_field1))
<p class="text-muted">
    <strong>{{ $custom_labels['contact']['custom_field_1'] ?? __('lang_v1.contact_custom_field1') }}: </strong>
        {{ $contact->custom_field1 }}
    </p>
@endif

@if(!empty($contact->custom_field2))
<p class="text-muted">
    <strong>{{ $custom_labels['contact']['custom_field_2'] ?? __('lang_v1.contact_custom_field2') }}:</strong>
        {{ $contact->custom_field2 }}
    </p>
@endif

@if(!empty($contact->custom_field3))
<p class="text-muted">
    <strong>{{ $custom_labels['contact']['custom_field_3'] ?? __('lang_v1.contact_custom_field3') }}:</strong>
        {{ $contact->custom_field3 }}
    </p>
@endif

@if(!empty($contact->custom_field4))
<p class="text-muted">
    <strong>{{ $custom_labels['contact']['custom_field_4'] ?? __('lang_v1.contact_custom_field4') }}:</strong>
        {{ $contact->custom_field4 }}
    </p>
@endif

@if(!empty($contact->custom_field5))
<p class="text-muted">
    <strong>{{ $custom_labels['contact']['custom_field_5'] ?? __('lang_v1.custom_field', ['number' => 5]) }}:</strong>
        {{ $contact->custom_field5 }}
    </p>
@endif

@if(!empty($contact->custom_field6))
<p class="text-muted">
    <strong>{{ $custom_labels['contact']['custom_field_6'] ?? __('lang_v1.custom_field', ['number' => 6]) }}:</strong>
        {{ $contact->custom_field6 }}
    </p>
@endif

@if(!empty($contact->custom_field7))
<p class="text-muted">
    <strong>{{ $custom_labels['contact']['custom_field_7'] ?? __('lang_v1.custom_field', ['number' => 7]) }}:</strong>
        {{ $contact->custom_field7 }}
    </p>
@endif
@if(!empty($contact->custom_field8))
<p class="text-muted">
    <strong>{{ $custom_labels['contact']['custom_field_8'] ?? __('lang_v1.custom_field', ['number' => 8]) }}:</strong>
        {{ $contact->custom_field8 }}
    </p>
@endif
@if(!empty($contact->custom_field9))
<p class="text-muted">
    <strong>{{ $custom_labels['contact']['custom_field_9'] ?? __('lang_v1.custom_field', ['number' => 9]) }}:</strong>
        {{ $contact->custom_field9 }}
    </p>
@endif

@if(!empty($contact->custom_field10))
<p class="text-muted">
    <strong>{{ $custom_labels['contact']['custom_field_10'] ?? __('lang_v1.custom_field', ['number' => 10]) }}:</strong>
        {{ $contact->custom_field10 }}
    </p>
@endif