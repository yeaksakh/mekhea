<div class="draggable title" style="position: absolute; top: 80px; left: 800px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <strong>@lang('lang_v1.name'):</strong>
</div>
<div class="draggable data" style="position: absolute; top: 110px; left: 800px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5>{{ $user->user_full_name ?? '' }}</h5>
</div>

<div class="draggable title" style="position: absolute; top: 140px; left: 800px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <strong>@lang('lang_v1.department'):</strong>
</div>
<div class="draggable data" style="position: absolute; top: 170px; left: 800px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5>{{ $user->crm_department }}</h5>
</div>

<div class="draggable title" style="position: absolute; top: 200px; left: 800px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <strong>@lang('lang_v1.designation'):</strong>
</div>
<div class="draggable data" style="position: absolute; top: 230px; left: 800px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5>{{ $user->crm_designation }}</h5>
</div>

<div class="draggable title" style="position: absolute; top: 260px; left: 800px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <strong>@lang('lang_v1.id_proof_name'):</strong>
</div>
<div class="draggable data" style="position: absolute; top: 290px; left: 800px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5>{{ $user->id_proof_name ?? '' }}</h5>
</div>

<div class="draggable title" style="position: absolute; top: 320px; left: 800px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <strong>@lang('lang_v1.id_proof_number'):</strong>
</div>
<div class="draggable data" style="position: absolute; top: 350px; left: 800px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5>{{ $user->id_proof_number ?? '' }}</h5>
</div>

<div class="draggable title" style="position: absolute; top: 380px; left: 800px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <strong>@lang('lang_v1.dob'):</strong>
</div>
<div class="draggable data" style="position: absolute; top: 410px; left: 800px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    @if (!empty($user->dob)) <h5>{{ @format_date($user->dob) }}</h5> @endif
</div>

<div class="draggable title" style="position: absolute; top: 440px; left: 800px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <strong>@lang('lang_v1.gender'):</strong>
</div>
<div class="draggable data" style="position: absolute; top: 470px; left: 800px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    @if (!empty($user->gender)) @lang('lang_v1.' . $user->gender) @endif
</div>

<div class="draggable title" style="position: absolute; top: 500px; left: 800px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <strong>@lang('lang_v1.blood_group'):</strong> 
</div>
<div class="draggable data" style="position: absolute; top: 530px; left: 800px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5>{{ $user->blood_group ?? '' }}</h5>
</div>

<div class="draggable title" style="position: absolute; top: 560px; left: 800px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <strong>@lang('lang_v1.marital_status'):</strong>
</div>
<div class="draggable data" style="position: absolute; top: 590px; left: 800px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    @if (!empty($user->marital_status)) @lang('lang_v1.' . $user->marital_status) @endif
</div>

<div class="draggable title" style="position: absolute; top: 620px; left: 800px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <strong>@lang('lang_v1.mobile_number'):</strong>
</div>
<div class="draggable data" style="position: absolute; top: 650px; left: 800px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5>{{ $user->contact_number ?? '' }}</h5>
</div>

<div class="draggable title" style="position: absolute; top: 680px; left: 800px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <strong>@lang('business.alternate_number'):</strong>
</div>
<div class="draggable data" style="position: absolute; top: 710px; left: 800px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5>{{ $user->alt_number ?? '' }}</h5>
</div>

<div class="draggable title" style="position: absolute; top: 740px; left: 800px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <strong>@lang('lang_v1.family_contact_number'):</strong>
</div>
<div class="draggable data" style="position: absolute; top: 770px; left: 800px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5>{{ $user->family_number ?? '' }}</h5>
</div>

<div class="draggable title" style="position: absolute; top: 800px; left: 800px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <strong>@lang('lang_v1.fb_link'):</strong>
</div>
<div class="draggable data" style="position: absolute; top: 830px; left: 800px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    @if ($user->fb_link)<a href="{{ $user->fb_link }}" target="_blank"><h5>{{ $user->fb_link }}</h5></a>@else <h5>{{ '' }}</h5> @endif
</div>

<div class="draggable title" style="position: absolute; top: 860px; left: 800px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <strong>@lang('lang_v1.twitter_link'):</strong>
</div>
<div class="draggable data" style="position: absolute; top: 890px; left: 800px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    @if ($user->twitter_link)<a href="{{ $user->twitter_link }}" target="_blank"><h5>{{ $user->twitter_link }}</h5></a>@else <h5>{{ '' }}</h5> @endif
</div>

<div class="draggable title" style="position: absolute; top: 920px; left: 800px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <strong>@lang('lang_v1.social_media', ['number' => 1]):</strong>
</div>
<div class="draggable data" style="position: absolute; top: 950px; left: 800px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    @if ($user->social_media_1)<a href="{{ $user->social_media_1 }}" target="_blank"><h5>{{ $user->social_media_1 }}</h5></a>@else <h5>{{ '' }}</h5> @endif
</div>





<div class="draggable title" style="position: absolute; top: 0px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <strong>@lang('lang_v1.account_holder_name'):</strong>
</div>
<div class="draggable data" style="position: absolute; top: 30px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5>{{ $bank_details['account_holder_name'] ?? '' }}</h5>
</div>

<div class="draggable title" style="position: absolute; top: 60px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <strong>@lang('lang_v1.account_number'):</strong>
</div>
<div class="draggable data" style="position: absolute; top: 90px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5>{{ $bank_details['account_number'] ?? '' }}</h5>
</div>

<div class="draggable title" style="position: absolute; top: 120px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <strong>@lang('lang_v1.bank_name'):</strong>
</div>
<div class="draggable data" style="position: absolute; top: 150px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5>{{ $bank_details['bank_name'] ?? '' }}</h5>
</div>

<div class="draggable title" style="position: absolute; top: 180px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <strong>@lang('lang_v1.bank_code'):</strong>
</div>
<div class="draggable data" style="position: absolute; top: 210px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5>{{ $bank_details['bank_code'] ?? '' }}</h5>
</div>

<div class="draggable title" style="position: absolute; top: 240px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <strong>@lang('lang_v1.branch'):</strong>
</div>
<div class="draggable data" style="position: absolute; top: 270px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5>{{ $bank_details['branch'] ?? '' }}</h5>
</div>

<div class="draggable title" style="position: absolute; top: 300px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <strong>@lang('lang_v1.tax_payer_id'):</strong>
</div>
<div class="draggable data" style="position: absolute; top: 330px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5>{{ $bank_details['tax_payer_id'] ?? '' }}</h5>
</div>

<div class="draggable title" style="position: absolute; top: 360px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <strong>@lang('lang_v1.permanent_address'):</strong>
</div>
<div class="draggable data" style="position: absolute; top: 390px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5>{{ $user->permanent_address ?? '' }}</h5>
</div>

<div class="draggable title" style="position: absolute; top: 420px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <strong>@lang('lang_v1.current_address'):</strong>
</div>
<div class="draggable data" style="position: absolute; top: 450px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5>{{ $user->current_address ?? '' }}</h5>
</div>

<div class="draggable title" style="position: absolute; top: 480px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <strong>{{ $custom_labels['user']['custom_field_1'] ?? __('lang_v1.user_custom_field1') }}:</strong>
</div>
<div class="draggable data" style="position: absolute; top: 510px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5>{{ $user->custom_field_1 ?? '' }}</h5>
</div>

<div class="draggable title" style="position: absolute; top: 540px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <strong>{{ $custom_labels['user']['custom_field_2'] ?? __('lang_v1.user_custom_field2') }}:</strong>
</div>
<div class="draggable data" style="position: absolute; top: 570px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5>{{ $user->custom_field_2 ?? '' }}</h5>
</div>

<div class="draggable title" style="position: absolute; top: 600px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <strong>{{ $custom_labels['user']['custom_field_3'] ?? __('lang_v1.user_custom_field3') }}:</strong>
</div>
<div class="draggable data" style="position: absolute; top: 630px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5>{{ $user->custom_field_3 ?? '' }}</h5>
</div>
<div class="draggable title" style="position: absolute; top: 660px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <strong>{{ $custom_labels['user']['custom_field_4'] ?? __('lang_v1.user_custom_field4') }}:</strong>
</div>
<div class="draggable data" style="position: absolute; top: 690px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5>{{ $user->custom_field_4 ?? '' }}</h5>
</div>
<div class="draggable title" style="position: absolute; top: 720px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <strong>{{ $custom_labels['user']['custom_field_5'] ?? __('lang_v1.user_custom_field5') }}:</strong>
</div>
<div class="draggable data" style="position: absolute; top: 750px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5>{{ $user->custom_field_5 ?? '' }}</h5>
</div>
<div class="draggable title" style="position: absolute; top: 780px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <strong>{{ $custom_labels['user']['custom_field_6'] ?? __('lang_v1.user_custom_field6') }}:</strong>
</div>
<div class="draggable data" style="position: absolute; top: 570px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5>{{ $user->custom_field_6 ?? '' }}</h5>
</div>