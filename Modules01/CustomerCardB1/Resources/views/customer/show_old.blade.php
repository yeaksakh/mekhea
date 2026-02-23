@extends('layouts.app')

@section('title', __('lang_v1.view_user'))
@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row no-print">
        <div class="col-md-12" style="background-color: black; padding: 20px; display: flex; align-items: center;">
    <div class="col-md-2" style="padding-right: 20px;">
        @php
                    $img_src = $contact->media->display_url ?? 'https://ulm.webstudio.co.zw/themes/adminlte/img/user.png';
                @endphp
                <img class="profile-user-img img-fluid img-circle"
                    src="{{$img_src}}"
                    alt="User profile picture">
    </div>
    
    <div class="col-md-10" style="color: white;">
        <p style="margin: 10px 0; color: white;">
            <strong>@lang('contact.prefix'):</strong> 
            <span style="font-size: 28px;">{{ implode(' ', array_filter([
                $contact->prefix,
                $contact->first_name,
                $contact->middle_name,
                $contact->last_name
            ], fn($value) => !is_null($value) && $value !== '')) ?: '-' }}</span> :
       
            <strong>@lang('contact.customer_groups'):</strong> 
            <span style="font-size: 18px;">{{ $contact->customer_group ? $contact->customer_group->name : '-' }}</span>
        </p>
        
        <!-- Dates -->
       <p style="margin: 10px 0; color: white;">
    <strong>
        <i class="fas fa-bullseye" style="margin-right: 5px;"></i>@lang('contact.register_date'):
    </strong>
    <span style="font-size: 18px;">{{ $contact->register_date ? \Carbon\Carbon::parse($contact->register_date)->format('d-m-Y') : '-' }}</span>

    <strong>
        <i class="fas fa-bullseye" style="margin-right: 5px;"></i>@lang('contact.expired_at'):
    </strong>
    <span style="font-size: 18px;">{{ $contact->expired_date ? \Carbon\Carbon::parse($contact->expired_date)->format('d-m-Y') : '-' }}</span>

    <strong>
        <i class="fas fa-bullseye" style="margin-right: 5px;"></i>@lang('contact.study_date'):
    </strong>
    <span style="font-size: 18px;">{{ $contact->study_date ? \Carbon\Carbon::parse($contact->study_date)->format('d-m-Y') : '-' }}</span>

    <strong>
        <i class="fas fa-bullseye" style="margin-right: 5px;"></i>@lang('lang_v1.created_at'):
    </strong>
    <span style="font-size: 18px;">{{ $contact->created_at ? \Carbon\Carbon::parse($contact->created_at)->format('d-m-Y') : '-' }}</span>
</p>

       <!-- Created By -->
@php
    $user = \App\User::find($contact->created_by);
    $name = $user ? ($user->first_name . ' ' . $user->last_name) : '-';
@endphp
<p style="margin: 10px 0; color: white;">
    <strong>
        <i class="fas fa-bullseye" style="margin-right: 5px;"></i>@lang('contact.created_by'):
    </strong>
    <span style="color: white; font-size: 18px;">{{ $name }}</span>
    
    <strong>
        <i class="fas fa-bullseye" style="margin-right: 5px;"></i>@lang('contact.assigned_to_users'):
    </strong>
    <span style="font-size: 18px;">{{ !empty($assignToNames) ? implode(', ', array_column($assignToNames, 'name')) : '-' }}</span>
</p>
 <!-- Edit Button -->
            <a href="{{ action([\App\Http\Controllers\ContactController::class, 'edit'], [$contact->id]) }}"
               class="edit_contact_button tw-dw-btn tw-dw-btn-orange tw-text-white no-print" 
               style="height: 40px; padding: 8px 16px; background-color: orange; display: inline-flex; align-items: center;">
               <i class="fas fa-edit" aria-hidden="true"></i>
               <span class="ml-2">@lang('messages.edit')</span>
            </a>

    </div>
    
</div>
</div><br>
<!-- Sidebar -->
<section class="no-print">
    <nav class="navbar-default tw-transition-all tw-duration-5000 tw-shrink-0 tw-rounded-2xl tw-m-[16px] tw-border-2 !tw-bg-white">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false" style="margin-top: 3px; margin-right: 3px;">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{action([\Modules\CustomerCardB1\Http\Controllers\ContactController::class, 'index'], ['type' => 'customer'])}}"><i class="fas fa fa-broadcast-tower"></i> {{__('customercardb1::lang.customers')}}</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <input type="hidden" id="sell_list_filter_customer_id" value="{{ $contact->id }}">
                <input type="hidden" id="purchase_list_filter_supplier_id" value="{{ $contact->id }}">
                <ul class="nav navbar-nav">
                    @php
                    $menuItems = [
                    ['icon' => 'fas fa-user', 'label' => __('contact.profile'), 'tab' => 'profile_tab'],
                    [
                        'icon' => 'fas fa-passport',
                        'label' => __('contact.customer_visa'),
                        'tab' => 'customer_visa_tab',
                    ],
                    [
                        'icon' => 'fas fa-passport',
                        'label' => __('contact.passport_card'),
                        'tab' => 'passport_card_tab',
                    ],
                    [
                        'icon' => 'fas fa-passport',
                        'label' => __('contact.passport_page1'),
                        'tab' => 'passport_page1_tab',
                    ],
                    [
                        'icon' => 'fas fa-passport',
                        'label' => __('contact.passport_page2'),
                        'tab' => 'passport_page2_tab',
                    ],
                    [
                        'icon' => 'fas fa-shield-alt',
                        'label' => __('contact.certificate_of_yeaksa_member'),
                        'tab' => 'certificate_of_yeaksa_member_tab',
                    ],
                    [
                        'icon' => 'fas fa-shield-alt',
                        'label' => __('contact.certificate_of_yeaksa_training'),
                        'tab' => 'certificate_of_yeaksa_training_tab',
                    ],
                    [
                        'tab' => 'name_card_tab',
                        'icon' => 'fas fa-address-card',
                        'label' => __('user.name_card'),
                    ],
                    [
                        'tab' => 'id_card_tab',
                        'icon' => 'fas fa-address-card',
                        'label' => __('user.id_card'),
                    ],
                    [
                        'icon' => 'fas fa-file-signature',
                        'label' => __('contact.franchise_contract'),
                        'tab' => 'franchise_contract_tab',
                    ],
                    [
                        'icon' => 'fas fa-file-signature',
                        'label' => __('contact.products_contract'),
                        'tab' => 'products_contract_tab',
                    ],
                    [
                        'icon' => 'fas fa-file-signature',
                        'label' => __('contact.notification_of_termination_using_franchise_contract'),
                        'tab' => 'notification_of_termination_using_franchise_contract_tab',
                    ],
                    [
                    'icon' => 'fas fa-users',
                    'label' => __('contact.checklist'),
                    'tab' => 'checklist_tab',
                    ]
                    ];

                    // Define tabs to exclude for customers
                    $customerExcludedTabs = ['activity_tab', 'activity_tab'];

                    // Define tabs to exclude for suppliers
                    $supplierExcludedTabs = ['activity_tab', 'activity_tab'];

                    // Filter menu items based on contact type
                    if ($contact->type === 'supplier') {
                    $menuItems = array_filter($menuItems, function ($item) use ($supplierExcludedTabs) {
                    return !in_array($item['tab'], $supplierExcludedTabs);
                    });
                    } elseif ($contact->type === 'customer') {
                    $menuItems = array_filter($menuItems, function ($item) use ($customerExcludedTabs) {
                    return !in_array($item['tab'], $customerExcludedTabs);
                    });
                    }

                    $view_type = request()->get('view', 'profile');
                    @endphp

                    @foreach ($menuItems as $item)
                    <li class="{{ $item['tab'] === $view_type . '_tab' ? 'active' : '' }}">
                        <a href="#{{ $item['tab'] }}" data-toggle="tab" aria-expanded="true">
                            <i class="{{ $item['icon'] }}"></i>
                            {{ $item['label'] }}
                            @if (isset($item['count']))
                            <span class="label label-primary pull-right">{{ $item['count'] }}</span>
                            @endif
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</section>

    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <div class="tab-content">
                    <!-- Profile Tab -->
                    <div class="tab-pane {{ $view_type === 'profile' ? 'active' : '' }}" id="profile_tab">
                        @include('customercardb1::customer.partials.profile_contact')
                    </div>
                    <div class="tab-pane" id="certificate_of_yeaksa_member_tab">
                          @include('customercardb1::customer.partials.card.certificate_of_yeaksa_member')
                    </div>
                    <div class="tab-pane" id="certificate_of_yeaksa_training_tab">
                          @include('customercardb1::customer.partials.card.certificate_of_yeaksa_training')
                    </div>
                    <div class="tab-pane" id="name_card_tab">
                         <div class="row">
                             <div class="col-md-12 " align="center">
                                   @include('customercardb1::customer.partials.card.name_card')
                             </div>
                         </div>
                    </div>
                    <div class="tab-pane" id="id_card_tab">
                         <div class="row">
                             <div class="col-md-12 " align="center">
                                   @include('customercardb1::customer.partials.card.id_card')
                             </div>
                         </div>
                    </div>
                    <div class="tab-pane" id="franchise_contract_tab">
                            <div class="row">
                                <div class="col-md-12 " align="center">
                                        @include('customercardb1::customer.partials.card.franchise_contract')
                                </div>
                            </div>
                        </div>
                    <div class="tab-pane" id="products_contract_tab">
                            <div class="row">
                                <div class="col-md-12 " align="center">
                                        @include('customercardb1::customer.partials.card.products_contract')
                                </div>
                            </div>
                    </div>
                    <div class="tab-pane" id="notification_of_termination_using_franchise_contract_tab">
	                    <div class="row">
	                        <div class="col-md-12 " align="center">
	                                @include('customercardb1::customer.partials.card.notification_of_termination_using_franchise_contract')
	                        </div>
	                    </div>
                    </div>
                    <div class="tab-pane" id="checklist_tab">
                        <div class="row">
                            <div class="col-md-12 " align="center">
                                @include('customercardb1::customer.partials.card.checklist')
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="customer_visa_tab">
                        <div class="row">
                            <div class="col-md-12 " align="center">
                                @include('customercardb1::customer.partials.card.customer_visa')
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="passport_card_tab">
                        <div class="row">
                            <div class="col-md-12 " align="center">
                                @include('customercardb1::customer.partials.card.passport_card')
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="passport_page1_tab">
                        <div class="row">
                            <div class="col-md-12 " align="center">
                                @include('customercardb1::customer.partials.card.passport_page1')
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="passport_page2_tab">
                        <div class="row">
                            <div class="col-md-12 " align="center">
                                @include('customercardb1::customer.partials.card.passport_page2')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</section>
<!-- /.content -->
<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade pay_contact_due_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade" id="edit_ledger_discount_modal" tabindex="-1" role="dialog"
    aria-labelledby="gridSystemModalLabel">
</div>
@include('ledger_discount.create')


@stop
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_API_KEY') }}&libraries=places"></script>

@section('javascript')
@endsection