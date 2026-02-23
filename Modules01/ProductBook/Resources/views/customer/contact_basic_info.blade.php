<style>
    img.profile-user-img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
    }

    #contact-info > div:first-child {
        margin-right: 10px;
    }

    .completion-button {
        position: absolute;
        top: -40px;
        left: 50%;
        transform: translateX(-50%);
        background-color: red;
        color: white;
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        font-size: 14px;
    }

    .edit_contact_button {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        height: 40px;
        padding: 8px 16px;
        background-color: black;
        color: white;
        border: none;
        border-radius: 4px;
        text-decoration: none;
        white-space: nowrap;
        font-family: Arial, sans-serif;
    }
    .edit_contact_button:hover {
        opacity: 0.9;
    }
    .edit_contact_button i.fas,
    .edit_contact_button svg.fas {
        margin-right: 8px;
        line-height: 1;
    }
    .edit_contact_button span.ml-2 {
        margin-left: 8px;
        line-height: 1;
    }
</style>

<div id="contact-info" style="display: flex; width: 100%; font-family: Arial, sans-serif; position: relative;">
    
    <div style="flex: 0 0 30%; padding: 10px; background-color: #f5f5f5; box-sizing: border-box; border-radius: 10px; border: 1px solid #ccc;">
        <div style="text-align: center; margin-bottom: 20px;">
            @php
                $img_src = $contact->media->display_url ?? 'https://ulm.webstudio.co.zw/themes/adminlte/img/user.png';
            @endphp
            <img class="profile-user-img img-fluid img-circle"
                 src="{{ $img_src }}" 
                 alt="User profile picture" 
                 style="width: 120px; height: 120px;">
            @php
                $fields = [
                    'contact_id' => $contact->contact_id,
                    'register_date' => $contact->register_date,
                    'expired_date' => $contact->expired_date,
                    'study_date' => $contact->study_date,
                    'created_at' => $contact->created_at,
                    'created_by' => \App\User::find($contact->created_by) ? true : false,
                    'assigned_to_users' => !empty($assignToNames) ? implode(', ', array_column($assignToNames, 'name')) : null,
                    'prefix' => $contact->prefix,
                    'first_name' => $contact->first_name,
                    'middle_name' => $contact->middle_name,
                    'last_name' => $contact->last_name,
                    'customer_group' => $contact->customer_group ? $contact->customer_group->name : null,
                    'gender' => $contact->gender,
                    'dob' => $contact->dob,
                    'tax_number' => $contact->tax_number,
                    'pay_term_number' => $contact->pay_term_number,
                    'pay_term_type' => $contact->pay_term_type,
                    'mobile' => $contact->mobile,
                    'alternate_number' => $contact->alternate_number,
                    'landline' => $contact->landline,
                    'email' => $contact->email,
                    'supplier_business_name' => $contact->supplier_business_name,
                    'type' => $contact->type,
                    'address_line_1' => $contact->address_line_1,
                    'address_line_2' => $contact->address_line_2,
                    'city' => $contact->city,
                    'state' => $contact->state,
                    'country' => $contact->country,
                    'zip_code' => $contact->zip_code,
                    'shipping_address' => $contact->shipping_address,
                    'id_proof_name' => $contact->id_proof_name,
                    'id_proof_number' => $contact->id_proof_number,
                ];
                for ($i = 1; $i <= 10; $i++) {
                    $fields['custom_field' . $i] = $contact->{'custom_field' . $i};
                }
                for ($i = 1; $i <= 6; $i++) {
                    $fields['export_custom_field_' . $i] = $contact->{'export_custom_field_' . $i};
                }
                $fields['shipping_custom_field_details'] = $contact->shipping_custom_field_details;

                $totalFields = count($fields);
                $filledFields = 0;
                foreach ($fields as $field) {
                    if (!is_null($field) && $field !== '' && $field !== '-' && $field !== false) {
                        $filledFields++;
                    }
                }
                $completionPercentage = $totalFields > 0 ? round(($filledFields / $totalFields) * 100) : 0;
            @endphp
            <button class="completion-button no-print" style="position: static; transform: none; margin-top: 10px; height: 40px; padding: 8px 16px;">
                {{ $completionPercentage }}% Completed
            </button>
            <button type="button" 
                    class="edit_contact_button no-print" 
                    style="margin-top: 20px;"
                    data-toggle="modal" 
                    data-target="#add_discount_modal">
                    <svg class="fas fa-tag" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 20 20"><path fill="currentColor" d="M0 10V2l2-2h8l10 10l-10 10L0 10zm4.5-4a1.5 1.5 0 1 0 0-3a1.5 1.5 0 0 0 0 3z"/></svg>
                    <span class="ml-2">@lang('Discount')</span>
            </button>
            <!-- Edit Button -->
            <a href="{{ action([\App\Http\Controllers\ContactController::class, 'edit'], [$contact->id]) }}"
                class="edit_contact_button no-print" 
                style="background-color: orange; margin-top: 10px;">
                <i class="fas fa-edit" aria-hidden="true"></i>
                <span class="ml-2">@lang('messages.edit')</span>
            </a>
            <!-- Print Button -->
            <button onclick="printContactInfo()" 
                    class="edit_contact_button no-print" 
                    style="margin-top: 10px; box-shadow: 1px 1px 5px #0f0, -1px -1px 5px #0f0;">
                <i class="fas fa-print" aria-hidden="true"></i>
                <span class="ml-2">Print</span>
            </button>
        </div>           
        <div style="margin-bottom: 20px;">
            <h2 style="border-bottom: 0.5px solid #333; padding-bottom: 5px; font-size: 18px;">@lang('customercardb1::contact.details')</h2>

            <p style="margin: 10px 0;"><strong>@lang('customercardb1::contact.contact_id'):</strong> {{ $contact->contact_id ?? '-' }}</p>
            
            <p style="margin: 10px 0;"><strong>@lang('customercardb1::contact.register_date'):</strong>{{ $contact->register_date ? \Carbon\Carbon::parse($contact->register_date)->format('d-m-Y') : '-' }}</p>
            <p style="margin: 10px 0;"><strong>@lang('customercardb1::contact.expired_at'):</strong> {{ $contact->expired_date ? \Carbon\Carbon::parse($contact->expired_date)->format('d-m-Y') : '-' }}</p>
            <p style="margin: 10px 0;"><strong>@lang('customercardb1::contact.study_date'):</strong> {{ $contact->study_date ? \Carbon\Carbon::parse($contact->study_date)->format('d-m-Y') : '-' }}</p>
            <p style="margin: 10px 0;"><strong>@lang('customercardb1::contact.created_at'):</strong> {{ $contact->created_at ? \Carbon\Carbon::parse($contact->created_at)->format('d-m-Y') : '-' }}</p>

            @php
                $user = \App\User::find($contact->created_by);
                $name = $user ? ($user->first_name . ' ' . $user->last_name) : '-';
            @endphp
            <p style="margin: 10px 0;">
                <strong>@lang('customercardb1::contact.created_by'):</strong><span style="color: red;">{{ $name }}</span>
            </p>
        </div>
        
        <div style="margin-bottom: 20px;">
            <h2 style="border-bottom: 0.5px solid #333; padding-bottom: 5px; font-size: 18px;">@lang('customercardb1::contact.working_with')</h2>
            <p style="margin: 10px 0;"><strong>@lang('customercardb1::contact.assigned_to_users'):</strong> {{ !empty($assignToNames) ? implode(', ', array_column($assignToNames, 'name')) : '-' }}</p>
        </div>
         
        <div style="margin-bottom: 20px;">
            <h2 style="border-bottom: 0.5px solid #333; padding-bottom: 5px; font-size: 18px;">@lang('customercardb1::contact.marketing')</h2>
            <p style="margin: 10px 0;"><strong>@lang('customercardb1::contact.location_coverage'):</strong> ទួលគោក</p>
            <p style="margin: 10px 0;"><strong>@lang('customercardb1::contact.interested_products'):</strong> សាប៊ូ</p>
            @include('customercardb1::customer.contact_tax_info')
        </div>
    </div>
     
    <div style="flex: 0 0 70%; padding: 10px; background-color: white; box-sizing: border-box; border-radius: 10px;">
        {{-- <div style="margin-bottom: 20px; text-align: center;">
            <h3>
                <strong>@lang('customercardb1::contact.prefix'):</strong> 
                {{ implode(' ', array_filter([
                    $contact->prefix,
                    $contact->first_name,
                    $contact->middle_name,
                    $contact->last_name
                ], fn($value) => !is_null($value) && $value !== '')) ?: '-' }}
            </h3>
        </div> --}}
        <div style="margin-bottom: 20px;">
            <h2 style="border-bottom: 0.5px solid #333; padding-bottom: 5px; font-size: 18px;">@lang('customercardb1::contact.info')</h2>
        
            <p style="margin: 10px 0;">
                <strong>@lang('customercardb1::contact.prefix'):</strong> 
                {{ implode(' ', array_filter([
                    $contact->prefix,
                    $contact->first_name,
                    $contact->middle_name,
                    $contact->last_name
                ], fn($value) => !is_null($value) && $value !== '')) ?: '-' }}
            </p>

            <p style="margin: 10px 0;"><strong>@lang('customercardb1::contact.customer_groups'):</strong> {{ $customer_groups[$contact->customer_group_id] ?? '-' }}</p>
            
            <p style="margin: 10px 0;"><strong>@lang('customercardb1::contact.gender'):</strong> {{ $contact->gender ? ucfirst($contact->gender) : '-' }}</p>

            <p style="margin: 10px 0;"><strong>@lang('customercardb1::contact.dob'):</strong> {{ $contact->dob ? @format_date($contact->dob) : '-' }}</p>
            <p style="margin: 10px 0;"><strong>@lang('customercardb1::contact.mobile'):</strong> {{ $contact->mobile ?? '-' }}</p>
            <p style="margin: 10px 0;"><strong>@lang('customercardb1::contact.alternate_contact_number'):</strong> {{ $contact->alternate_number ?? '-' }}</p>
            <p style="margin: 10px 0;"><strong>@lang('customercardb1::contact.landline'):</strong> {{ $contact->landline && $contact->landline !== '-' ? $contact->landline : '-' }}</p>
            <p style="margin: 10px 0;"><strong>@lang('business.email'):</strong> {{ $contact->email ?? '-' }}</p>
        </div>
        
        <div style="margin-bottom: 20px;">
            <h2 style="border-bottom: 0.5px solid #333; padding-bottom: 5px; font-size: 18px;">@lang('customercardb1::contact.about_company')</h2>

            <p style="margin: 10px 0;"><strong>@lang('business.email'):</strong> {{ $contact->email ?? '-' }}</p>
            <p style="margin: 10px 0;"><strong>@lang('customercardb1::contact.number_of_staff'):</strong> 55</p>
            <p style="margin: 10px 0;"><strong>@lang('customercardb1::contact.lead_source'):</strong> Facebook</p>
            <p style="margin: 10px 0;"><strong>@lang('customercardb1::contact.average_buy'):</strong> $120.45</p>
            <p style="margin: 10px 0;"><strong>@lang('customercardb1::contact.customers_under_line'):</strong> 23</p>

            <p style="margin: 10px 0;">
                <strong>@lang('customercardb1::contact.contact_type'):</strong> 
                @if($contact->type == 'both')
                    @lang('role.customer') & @lang('role.supplier')
                @elseif($contact->type != 'lead')
                    @lang('role.' . $contact->type)
                @else
                    @lang('customercardb1::contact.lead')
                @endif
            </p>
        </div>
        <div style="margin-bottom: 20px;">
            <h2 style="border-bottom: 0.5px solid #333; padding-bottom: 5px; font-size: 18px;">@lang('customercardb1::contact.address')</h2>

            <p style="margin: 10px 0;">
                <strong>@lang('business.address'):</strong> 
                {{ implode(', ', array_filter([
                    $contact->address_line_1,
                    $contact->address_line_2,
                    $contact->city,
                    $contact->state,
                    $contact->country,
                    $contact->zip_code
                ], fn($value) => !is_null($value) && $value !== '')) ?: '-' }}
            </p>

            <p style="margin: 10px 0;"><strong>@lang('customercardb1::contact.shipping_address'):</strong> {{ $contact->shipping_address ?? '-' }}</p>

            <p style="margin: 10px 0;">
                <strong>@lang('customercardb1::contact.id_proof'):</strong> 
                {{ $contact->id_proof_name && $contact->id_proof_number ? ($contact->id_proof_name . ': ' . $contact->id_proof_number) : '-' }}
            </p>
        </div>
         
        <div style="margin-bottom: 20px;">
            <h2 style="border-bottom: 0.5px solid #333; padding-bottom: 5px; font-size: 18px;">@lang('customercardb1::contact.other_info')</h2>

            @for($i = 1; $i <= 6; $i++)
                @if($contact->{'export_custom_field_' . $i})
                    <p style="margin: 10px 0;"><strong>@lang('customercardb1::contact.export_custom_field', ['number' => $i]):</strong> {{ $contact->{'export_custom_field_' . $i} }}</p>
                @endif
            @endfor
        </div>
        @include('customercardb1::customer.contact_more_info')
    </div>
</div>

<script>
    function printContactInfo() {
        const contactInfo = document.getElementById('contact-info');
        if (!contactInfo) {
            console.error('Element with ID "contact-info" not found.');
            alert('Error: Contact information section not found.');
            return;
        }

        const profileImg = contactInfo.querySelector('.profile-user-img');
        let imgSrc = profileImg ? profileImg.src : 'https://ulm.webstudio.co.zw/themes/adminlte/img/user.png';

        if (!imgSrc.startsWith('http')) {
            const baseUrl = window.location.origin;
            imgSrc = new URL(imgSrc, baseUrl).href;
        }

        const preloadImg = new Image();
        preloadImg.src = imgSrc;

        const iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        document.body.appendChild(iframe);

        const clonedContactInfo = contactInfo.cloneNode(true);
        const clonedProfileImg = clonedContactInfo.querySelector('.profile-user-img');
        if (clonedProfileImg) {
            clonedProfileImg.src = imgSrc;
            clonedProfileImg.style.width = '120px';
            clonedProfileImg.style.height = '120px';
            clonedProfileImg.style.borderRadius = '50%';
            clonedProfileImg.style.objectFit = 'cover';
            clonedProfileImg.style.display = 'block';
        }

        const styles = document.querySelector('style')?.innerHTML || '';

        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        iframeDoc.write(`
            <html>
                <head>
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
                    <style>
                        ${styles}
                        @media print {
                            @page { margin: 20px; }
                            body { margin: 0 !important; padding: 0 !important; }
                            body * { visibility: hidden; }
                            #contact-info, #contact-info * { visibility: visible; }
                            #contact-info {
                                position: static;
                                width: 100%;
                                gap: 10px !important;
                                margin: 0 !important;
                                box-sizing: border-box;
                                page-break-inside: avoid;
                                break-inside: avoid;
                            }
                            img.profile-user-img {
                                width: 120px !important;
                                height: 120px !important;
                                border-radius: 50% !important;
                                object-fit: cover !important;
                                margin: 5px auto !important;
                                display: block !important;
                                visibility: visible !important;
                            }
                            .edit_contact_button {
                                display: inline-flex !important;
                                align-items: center !important;
                                justify-content: center !important;
                                height: 40px !important;
                                padding: 8px 16px !important;
                                border-radius: 4px !important;
                                text-decoration: none !important;
                                color: white !important;
                                background-color: black !important;
                            }
                            .edit_contact_button svg.fas.fa-tag, .edit_contact_button i.fas {
                                margin-right: 8px !important;
                                line-height: 1 !important;
                                visibility: visible !important;
                            }
                            .edit_contact_button span.ml-2 {
                                margin-left: 8px !important;
                                line-height: 1 !important;
                            }
                            .no-print {
                                display: none !important;
                            }
                            h2 {
                                font-size: 13px!important;
                                font-weight: 900!important;
                                border-bottom: 0.5px solid #000!important;
                            }
                            p {
                                font-size: 11px!important;
                            }
                        }
                    </style>
                </head>
                <body>
                    ${clonedContactInfo.outerHTML}
                </body>
            </html>
        `);

        iframeDoc.close();

        preloadImg.onload = function() {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
            document.body.removeChild(iframe);
        };
        preloadImg.onerror = function() {
            console.error('Failed to load profile image:', imgSrc);
            alert('Error: Failed to load profile image.');
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
            document.body.removeChild(iframe);
        };
    }
</script>