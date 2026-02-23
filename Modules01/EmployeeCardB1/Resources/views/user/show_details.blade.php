<?php
$custom_labels = json_decode(session('user.custom_labels'), true);

// Fields to check for completion
$fields = [
    $user->image_url,
    $user->sign_image,
    $user->dob,
    $user->gender,
    $user->marital_status,
    $user->blood_group,
    $user->hieght,
    $user->weight,
    $user->guardien_type,
    $user->uniform_size,
    $user->member_date,
    $user->id_proof_name,
    $user->id_proof_number,
    $user->insurance_number,
    $user->ss_number,
    !empty($user->bank_details) ? json_decode($user->bank_details, true)['tax_payer_id'] : null,
    $user->first_name,
    $user->last_name,
    $user->name_in_khmer,
    $user->contact_number,
    $user->alt_number,
    $user->family_number,
    $user->email,
    $user->fb_link,
    $user->twitter_link,
    $user->social_media_1,
    $user->social_media_2,
    $user->education,
    $user->permanent_address,
    $user->current_address,
    $user->hobby,
    !empty($user->bank_details) ? json_decode($user->bank_details, true)['account_holder_name'] : null

    ,
    !empty($user->bank_details) ? json_decode($user->bank_details, true)['account_number'] : null,
    !empty($user->bank_details) ? json_decode($user->bank_details, true)['bank_name'] : null,
    !empty($user->bank_details) ? json_decode($user->bank_details, true)['bank_code'] : null,
    !empty($user->bank_details) ? json_decode($user->bank_details, true)['branch'] : null,
    $user->custom_field_1,
    $user->custom_field_2,
    $user->custom_field_3,
    $user->custom_field_4,
    $user->job_history,
    $user->date_left_job,
    $user->reason,
    $user->job_description
];

$totalFields = count($fields);
$completedFields = count(array_filter($fields, function ($field) {
    return !empty($field) && $field !== 'No Photo' && $field !== __('user.no_data');
}));
$completionPercentage = $totalFields > 0 ? round(($completedFields / $totalFields) * 100) : 0;
$incompletePercentage = 100 - $completionPercentage;

// Helper function to determine if a field is incomplete
function isIncomplete($field)
{
    return empty($field) || $field === 'No Photo' || $field === __('user.no_data');
}
?>



<div class="cv-container"
    style="display: flex; width: 100%; min-height: 100vh; position: relative; box-sizing: border-box;">

    <!-- Left Side (30%) -->
    <div class="left-side"
        style="width: 30%; background-color: #f5f5f5; padding: 30px; position: relative; float: left; box-sizing: border-box;">
        <div style="text-align: center; margin-bottom: 30px;">
            @if($user->image_url)
                <img src="{{ $user->image_url }}"
                    style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 3px solid #fff;">
            @else
                <div
                    style="width: 150px; height: 150px; border-radius: 50%; background-color: #ddd; margin: 0 auto; display: flex; align-items: center; justify-content: center; border: 3px solid #fff;">
                    <span style="color: #777;">No Photo</span>
                </div>
            @endif
            @if($user->sign_image)
                <div style="margin-top: 20px; text-align: center;">
                    <p style="margin-bottom: 5px;"><strong>@lang('user.signature')</strong></p>
                    <img src="{{ asset('Uploads/sign_images/' . $user->sign_image) }}"
                        style="width: 120px; height: 60px; object-fit: contain;">
                </div>
            @else
                <div style="margin-top: 20px; text-align: center;">
                    <p style="margin-bottom: 5px; color: red;"><strong>@lang('user.signature')</strong></p>
                    <p style="color: red;">@lang('user.no_data')</p>
                </div>
            @endif
        </div>
        <div class="progress no-print" style="height: 25px; margin: 20px 0; background-color: #f5f5f5;">
            <div class="progress-bar bg-success" role="progressbar"
                style="width: <?php echo $completionPercentage; ?>%;"
                aria-valuenow="<?php echo $completionPercentage; ?>" aria-valuemin="0" aria-valuemax="100">
                <span style="color: #fff; padding-left: 5px;">
                    <?php echo $completionPercentage; ?>% Done
                </span>
            </div>
        </div>
        <!-- Personal Information -->
        <div style="margin-bottom: 20px;">
            <h2 style="border-bottom: 2px solid #333; padding-bottom: 5px; color: #333; font-size: 18px;">PERSONAL INFO
            </h2>
            <p><strong>@lang('lang_v1.dob'):</strong> <span
                    style="<?php echo isIncomplete($user->dob) ? 'color: red;' : ''; ?>">{{ $user->dob ? \Carbon\Carbon::parse($user->dob)->format('d/m/Y') : __('user.no_data') }}</span>
            </p>
            <p><strong>@lang('lang_v1.gender'):</strong> <span
                    style="<?php echo isIncomplete($user->gender) ? 'color: red;' : ''; ?>">{{ $user->gender ?? __('user.no_data') }}</span>
            </p>
            <p><strong>@lang('lang_v1.marital_status'):</strong> <span
                    style="<?php echo isIncomplete($user->marital_status) ? 'color: red;' : ''; ?>">{{ $user->marital_status ?? __('user.no_data') }}</span>
            </p>
            <p><strong>@lang('lang_v1.blood_group'):</strong> <span
                    style="<?php echo isIncomplete($user->blood_group) ? 'color: red;' : ''; ?>">{{ $user->blood_group ?? __('user.no_data') }}</span>
            </p>
            <p><strong>@lang('user.hieght'):</strong> <span
                    style="<?php echo isIncomplete($user->hieght) ? 'color: red;' : ''; ?>">{{ $user->hieght ?? __('user.no_data') }}Cm</span>
            </p>
            <p><strong>@lang('user.weight'):</strong> <span
                    style="<?php echo isIncomplete($user->weight) ? 'color: red;' : ''; ?>">{{ $user->weight ?? __('user.no_data') }}Kg</span>
            </p>
            <p><strong>@lang('user.guardien_type'):</strong> <span
                    style="<?php echo isIncomplete($user->guardien_type) ? 'color: red;' : ''; ?>">{{ $user->guardien_type ?? __('user.no_data') }}</span>
            </p>
            <p><strong>@lang('user.uniform_size'):</strong> <span
                    style="<?php echo isIncomplete($user->uniform_size) ? 'color: red;' : ''; ?>">{{ $user->uniform_size ?? __('user.no_data') }}</span>
            </p>
            <p><strong>@lang('user.member_date'):</strong> <span
                    style="<?php echo isIncomplete($user->member_date) ? 'color: red;' : ''; ?>">{{ $user->member_date ? \Carbon\Carbon::parse($user->member_date)->format('d/m/Y') : __('user.no_data') }}</span>
            </p>
            <p><strong>@lang('user.child_count'):</strong> <span
                    style="<?php echo isIncomplete($user->child_count) ? 'color: red;' : ''; ?>">{{ $user->child_count ?? __('user.child_count') }}</span>
            </p>
        </div>

        <!-- Identification -->
        <div style="margin-bottom: 20px;">
            <h2 style="border-bottom: 2px solid #333; padding-bottom: 5px; color: #333; font-size: 18px;">IDENTIFICATION
            </h2>
            <p><strong>@lang('lang_v1.id_proof_name'):</strong> <span
                    style="<?php echo isIncomplete($user->id_proof_name) ? 'color: red;' : ''; ?>">{{ $user->id_proof_name ?? __('user.no_data') }}</span>
            </p>
            <p><strong>@lang('lang_v1.id_proof_number'):</strong> <span
                    style="<?php echo isIncomplete($user->id_proof_number) ? 'color: red;' : ''; ?>">{{ $user->id_proof_number ?? __('user.no_data') }}</span>
            </p>
            <p><strong>@lang('user.insurance_number'):</strong> <span
                    style="<?php echo isIncomplete($user->insurance_number) ? 'color: red;' : ''; ?>">{{ $user->insurance_number ?? __('user.no_data') }}</span>
            </p>
            <p><strong>@lang('user.ss_number'):</strong> <span
                    style="<?php echo isIncomplete($user->ss_number) ? 'color: red;' : ''; ?>">{{ $user->ss_number ?? __('user.no_data') }}</span>
            </p>
            @php
                $bank_details = !empty($user->bank_details) ? json_decode($user->bank_details, true) : [];
                $tax_payer_id = $bank_details['tax_payer_id'] ?? __('user.no_data');
            @endphp
            <p><strong>@lang('lang_v1.tax_payer_id'):</strong> <span
                    style="<?php echo isIncomplete($tax_payer_id) ? 'color: red;' : ''; ?>">{{ $tax_payer_id }}</span>
            </p>

            <div class="clearfix"></div>

            @if(!empty($view_partials))
                @foreach($view_partials as $partial)
                    {!! $partial !!}
                @endforeach
            @endif
        </div>
    </div>

    <!-- Right Side (70%) -->
    <div class="right-side" style="width: 70%; padding: 30px; position: relative; float: left; box-sizing: border-box;">
        <!-- Header Section -->
        <div class="cv-header" style="text-align: center; margin-bottom: 30px;">
            <h1 style="margin-bottom: 5px; color: #333; font-size: 28px;">{{ $user->first_name }} {{ $user->last_name }}
            </h1>
            <p><strong>@lang('user.name_in_khmer'):</strong> <span
                    style="<?php echo isIncomplete($user->name_in_khmer) ? 'color: red;' : ''; ?>">{{ $user->name_in_khmer ?? __('user.no_data') }}</span>
            </p>
            <button class="btn btn-danger no-print"
                style="background-color: #dc3545; border-color: #dc3545; cursor: default;">
                <i class="fas fa-exclamation-triangle"></i>
                Incomplete: <?php echo $incompletePercentage; ?>%
            </button>
        </div>

        <!-- Contact Information -->
        <div style="margin-bottom: 20px;">
            <h2 style="border-bottom: 2px solid #333; padding-bottom: 5px; color: #333; font-size: 18px;">CONTACT</h2>
            <p><strong>@lang('lang_v1.mobile_number'):</strong> <span
                    style="<?php echo isIncomplete($user->contact_number) ? 'color: red;' : ''; ?>">{{ $user->contact_number ?? __('user.no_data') }}</span>
            </p>
            <p><strong>@lang('user.alternate_number'):</strong> <span
                    style="<?php echo isIncomplete($user->alt_number) ? 'color: red;' : ''; ?>">{{ $user->alt_number ?? __('user.no_data') }}</span>
            </p>
            <p><strong>@lang('lang_v1.family_contact_number'):</strong> <span
                    style="<?php echo isIncomplete($user->family_number) ? 'color: red;' : ''; ?>">{{ $user->family_number ?? __('user.no_data') }}</span>
            </p>
            <p><strong>@lang('business.email'):</strong> <span
                    style="<?php echo isIncomplete($user->email) ? 'color: red;' : ''; ?>">{{ $user->email ?? __('user.no_data') }}</span>
            </p>
            <p><strong>@lang('lang_v1.fb_link'):</strong> <span
                    style="<?php echo isIncomplete($user->fb_link) ? 'color: red;' : ''; ?>">{{ $user->fb_link ?: __('user.no_data') }}</span>
            </p>
            <p><strong>@lang('lang_v1.twitter_link'):</strong> <span
                    style="<?php echo isIncomplete($user->twitter_link) ? 'color: red;' : ''; ?>">{{ $user->twitter_link ?: __('user.no_data') }}</span>
            </p>
            <p><strong>@lang('lang_v1.social_media', ['number' => 1]):</strong> <span
                    style="<?php echo isIncomplete($user->social_media_1) ? 'color: red;' : ''; ?>">{{ $user->social_media_1 ?? __('user.no_data') }}</span>
            </p>
            <p><strong>@lang('lang_v1.social_media', ['number' => 2]):</strong> <span
                    style="<?php echo isIncomplete($user->social_media_2) ? 'color: red;' : ''; ?>">{{ $user->social_media_2 ?? __('user.no_data') }}</span>
            </p>
        </div>

        <!-- Education Section -->
        <div class="cv-section" style="margin-bottom: 20px;">
            <h2 style="border-bottom: 2px solid #333; padding-bottom: 5px; color: #333; font-size: 18px;">EDUCATION</h2>
            <p><strong>@lang('user.education'):</strong> <span
                    style="<?php echo isIncomplete($user->education) ? 'color: red;' : ''; ?>">{{ $user->education ?? __('user.no_data') }}</span>
            </p>
        </div>

        <!-- Address -->
        <div style="margin-bottom: 20px;">
            <h2 style="border-bottom: 2px solid #333; padding-bottom: 5px; color: #333; font-size: 18px;">Address</h2>
            <p><strong>@lang('lang_v1.permanent_address'):</strong> <span
                    style="<?php echo isIncomplete($user->permanent_address) ? 'color: red;' : ''; ?>">{{ $user->permanent_address ?: __('user.no_data') }}</span>
            </p>
            <p><strong>@lang('lang_v1.current_address'):</strong> <span
                    style="<?php echo isIncomplete($user->current_address) ? 'color: red;' : ''; ?>">{{ $user->current_address ?: __('user.no_data') }}</span>
            </p>
        </div>

        <!-- Skills & Hobbies -->
        <div class="cv-section" style="margin-bottom: 20px;">
            <h2 style="border-bottom: 2px solid #333; padding-bottom: 5px; color: #333; font-size: 18px;">SKILLS &
                HOBBIES</h2>
            <p><strong>@lang('user.hobby'):</strong> <span
                    style="<?php echo isIncomplete($user->hobby) ? 'color: red;' : ''; ?>">{{ $user->hobby ?? __('user.no_data') }}</span>
            </p>
        </div>

        <!-- Bank Details -->
        <div class="cv-section" style="margin-bottom: 10px;">
            @php
                $bank_details = !empty($user->bank_details) ? json_decode($user->bank_details, true) : [];
                $account_holder_name = $bank_details['account_holder_name'] ?? __('user.no_data');
                $account_number = $bank_details['account_number'] ?? __('user.no_data');
                $bank_name = $bank_details['bank_name'] ?? __('user.no_data');
                $bank_code = $bank_details['bank_code'] ?? __('user.no_data');
                $branch = $bank_details['branch'] ?? __('user.no_data');
            @endphp
            <h2 style="border-bottom: 2px solid #333; padding-bottom: 5px; color: #333; font-size: 18px;">
                @lang('lang_v1.bank_details')
            </h2>
            <p><strong>@lang('lang_v1.account_holder_name'):</strong> <span
                    style="<?php echo isIncomplete($account_holder_name) ? 'color: red;' : ''; ?>">{{ $account_holder_name }}</span>
            </p>
            <p><strong>@lang('lang_v1.account_number'):</strong> <span
                    style="<?php echo isIncomplete($account_number) ? 'color: red;' : ''; ?>">{{ $account_number }}</span>
            </p>
            <p><strong>@lang('lang_v1.bank_name'):</strong> <span
                    style="<?php echo isIncomplete($bank_name) ? 'color: red;' : ''; ?>">{{ $bank_name }}</span></p>
            <p><strong>@lang('lang_v1.bank_code'):</strong> <span
                    style="<?php echo isIncomplete($bank_code) ? 'color: red;' : ''; ?>">{{ $bank_code }}</span></p>
            <p><strong>@lang('lang_v1.branch'):</strong> <span
                    style="<?php echo isIncomplete($branch) ? 'color: red;' : ''; ?>">{{ $branch }}</span></p>
        </div>

        <!-- Custom Field -->
        <div class="cv-section" style="margin-bottom: 20px;">
            <h2 style="border-bottom: 2px solid #333; padding-bottom: 5px; color: #333; font-size: 18px;">
                @lang('lang_v1.user_custom_field1')
            </h2>
            <p><strong>{{ $custom_labels['user']['custom_field_1'] ?? __('lang_v1.user_custom_field1')}}:</strong>
                <span
                    style="<?php echo isIncomplete($user->custom_field_1) ? 'color: red;' : ''; ?>">{{ $user->custom_field_1 ?? __('user.no_data') }}</span>
            </p>
            <p><strong>{{ $custom_labels['user']['custom_field_2'] ?? __('lang_v1.user_custom_field2')}}:</strong>
                <span
                    style="<?php echo isIncomplete($user->custom_field_2) ? 'color: red;' : ''; ?>">{{ $user->custom_field_2 ?? __('user.no_data') }}</span>
            </p>
            <p><strong>{{ $custom_labels['user']['custom_field_3'] ?? __('lang_v1.user_custom_field3')}}:</strong>
                <span
                    style="<?php echo isIncomplete($user->custom_field_3) ? 'color: red;' : ''; ?>">{{ $user->custom_field_3 ?? __('user.no_data') }}</span>
            </p>
            <p><strong>{{ $custom_labels['user']['custom_field_4'] ?? __('lang_v1.user_custom_field4')}}:</strong>
                <span
                    style="<?php echo isIncomplete($user->custom_field_4) ? 'color: red;' : ''; ?>">{{ $user->custom_field_4 ?? __('user.no_data') }}</span>
            </p>
        </div>

        <!-- Experience Section -->
        <div class="cv-section" style="margin-bottom: 20px;">
            <h2 style="border-bottom: 2px solid #333; padding-bottom: 5px; color: #333; font-size: 18px;">EXPERIENCE
            </h2>
            <div class="experience-item" style="margin-bottom: 15px;">
                <h3 style="margin-bottom: 5px; font-size: 16px;">WORK HISTORY</h3>
                <p
                    style="margin-top: 0; font-style: italic; <?php echo isIncomplete($user->job_history) ? 'color: red;' : ''; ?>">
                    {{ $user->job_history ?? __('user.no_data') }}
                </p>
            </div>
        </div>

        <!-- Stop Working -->
        <div class="cv-section" style="margin-bottom: 20px;">
            <h2 style="border-bottom: 2px solid #333; padding-bottom: 5px; color: #333; font-size: 18px;">STOP WORKING
            </h2>
            <p style="margin-top: 0;"><strong>Date Left:</strong> <span
                    style="<?php echo isIncomplete($user->date_left_job) ? 'color: red;' : ''; ?>">{{ $user->date_left_job ? \Carbon\Carbon::parse($user->date_left_job)->format('d/m/Y') : __('user.no_data') }}</span>
            </p>
            <p style="margin-top: 0;"><strong>Reason:</strong> <span
                    style="<?php echo isIncomplete($user->reason) ? 'color: red;' : ''; ?>">{{ $user->reason ?? __('user.no_data') }}</span>
            </p>
        </div>

        @php
            use Illuminate\Support\Str;
        @endphp

   
     
    </div>
</div>



<script>
    function printAssetSection() {
        var printContents = document.getElementById('asset-print').innerHTML;
        var originalContents = document.body.innerHTML;
        
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        window.location.reload();
    }
    
  
    </script>

<style>
    @media print {

        body,
        html {
            width: 100% !important;
            height: auto !important;
            margin: 0 !important;
            padding: 0 !important;
            background: white !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            line-height: 1.2 !important;
        }

        .no-print {
            display: none !important;
        }

        .cv-container {
            display: flex !important;
            width: 100% !important;
            min-height: 100vh !important;
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box !important;
            line-height: 1.2 !important;
        }

        .left-side,
        .right-side {
            padding: 15px !important;
        }

        .left-side {
            width: 30% !important;
            background: #f5f5f5 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            page-break-inside: avoid !important;
            float: left !important;
        }

        .right-side {
            width: 70% !important;
            page-break-inside: avoid !important;
            float: left !important;
        }

        p,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        div {
            margin-top: 0.3em !important;
            margin-bottom: 0.3em !important;
            line-height: 1.2 !important;
        }

        h2 {
            border-bottom: 1px solid #333 !important;
            padding-bottom: 1px !important;
            margin-bottom: 8px !important;
            font-size: 12px !important;
        }

        .cv-section,
        .experience-item {
            page-break-inside: avoid !important;
            margin-bottom: 10px !important;
        }

        * {
            box-sizing: border-box !important;
        }
    }

    .completion-button-container {
        position: absolute;
        top: 10px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1000;
    }

    @media print {
        .completion-button-container {
            display: none !important;
        }
    }

    .progress {
        position: relative;
        border-radius: 4px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .progress-bar {
        transition: width 0.6s ease;
        background-color: #28a745 !important;
        /* Green color for completed portion */
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }

    .progress-bar span {
        font-weight: 500;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }
</style>



<script>


    function printCV(orientation) {
        const cvClone = document.querySelector('.cv-container').cloneNode(true);

        const printStyles = `
        <style>
            @page {
                size: ${orientation === 'portrait' ? 'A4 portrait' : 'A4 landscape'};
                margin: 0;
            }
            body {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                min-height: 100% !important;
                background: white !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                line-height: 1.2 !important;
            }
            .cv-container {
                display: flex !important;
                width: 100% !important;
                min-height: 100vh !important;
                margin: 0 !important;
                padding: 0 !important;
                box-sizing: border-box !important;
                line-height: 1.2 !important;
            }
            .left-side, .right-side {
                padding: 15px !important;
            }
            .left-side {
                width: 30% !important;
                background: #f5f5f5 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                page-break-inside: avoid !important;
                float: left !important;
            }
            .right-side {
                width: 70% !important;
                page-break-inside: avoid !important;
                float: left !important;
            }
            p, h1, h2, h3, h4, h5, h6, div {
                margin-top: 0.3em !important;
                margin-bottom: 0.3em !important;
                line-height: 1.2 !important;
            }
            h2 {
                border-bottom: 2px solid #333 !important;
                padding-bottom: 3px !important;
                margin-bottom: 8px !important;
                font-size: 18px !important;
            }
            .cv-section, .experience-item {
                page-break-inside: avoid !important;
                margin-bottom: 10px !important;
            }
            .no-print {
                display: none !important;
            }
            * {
                box-sizing: border-box !important;
            }
        </style>
    `;

        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>CV of <?php echo $user->first_name . ' ' . $user->last_name; ?></title>
            ${printStyles}
        </head>
        <body>
            ${cvClone.outerHTML}
            <script>
                window.onload = function() {
                    setTimeout(function() {
                        window.print();
                        window.close();
                    }, 300);
                };
            <\/script>
        </body>
        </html>
    `);
        printWindow.document.close();
    }
</script>