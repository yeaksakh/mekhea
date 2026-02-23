<head>
    <meta charset="UTF-8">
    <style>
        /* Modern Blade-Style CSS */
        body { font-family: 'Helvetica Neue', sans-serif; margin: 2rem; line-height: 1.6; color: #333; }
        .header { border-bottom: 3px solid #2A2A2A; padding-bottom: 1rem; margin-bottom: 2rem; }
        .name { font-size: 2.5em; font-weight: 700; margin: 0; color: #2A2A2A; }
        .position { font-size: 1.4em; color: #666; margin-top: 0.5rem; }
        .section { margin-bottom: 1.5rem; }
        .section-title { font-size: 1.2em; font-weight: 600; color: #2A2A2A; border-left: 4px solid #2A2A2A; padding-left: 0.5rem; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; }
        .text-muted { color: #777; }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header">
        <h1 class="name">{{ $contact->full_name_with_business }}</h1>
        <p class="position">{{ $contact->position }}</p>
    </div>

    <!-- Contact Details (Compact Layout) -->
    <div class="section">
        <div class="grid">
            <div>
                <p>{{ $contact->email }}</p>
                <p>{{ $contact->mobile }}</p>
            </div>
            <div>
                <p>{!! $contact->business->business_address !!}</p>
                <p>{{ $contact->supplier_business_name }}</p>
            </div>
        </div>
    </div>

    <!-- Professional & Business Summary -->
    <div class="section">
        <h2 class="section-title">Professional Summary</h2>
        <p>Associated with <strong>{{ $contact->business->name }}</strong>, specializing in [Industry/Field]. Key contributor to [Business Objective].</p>
    </div>

    <!-- Financial Overview (Simplified for Clarity) -->
    <div class="section">
        <h2 class="section-title">Financial Overview</h2>
        <div class="grid">
            <div>
                <p>Total Purchase: {{ @format_currency($contact->total_purchase) }}</p>
                <p>Outstanding Balance: {{ @format_currency($contact->balance) }}</p>
            </div>
            <div>
                <p>Tax ID: {{ $contact->tax_number }}</p>
                <p>Pay Term: {{ $contact->pay_term_number }} {{ __('lang_v1.' . $contact->pay_term_type) }}</p>
            </div>
        </div>
    </div>

    <!-- Custom Fields (Dynamic Skills/Details) -->
    <div class="section">
        <h2 class="section-title">Additional Details</h2>
        <div class="grid">
            @for ($i = 1; $i <= 10; $i++)
                @if (!empty($contact->{"custom_field$i"}))
                    <div>{{ $contact->{"custom_field$i"} }}</div>
                @endif
            @endfor
        </div>
    </div>
</body>