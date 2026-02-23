@extends('layouts.app')

@section('title', __('customercardb1::lang.view_customer'))

@section('content')
    <!-- Main content -->
    <section class="content">
        {{-- <div class="row no-print small_hide">
            <div class="col-md-12" style="background-color: black; padding: 20px; display: flex; align-items: center;">
                <div class="col-md-2" style="padding-right: 20px;">
                    @php
                        $img_src =
                            $contact->media->display_url ?? 'https://ulm.webstudio.co.zw/themes/adminlte/img/user.png';
                    @endphp
                    <img class="profile-user-img img-fluid img-circle" src="{{ $img_src }}" alt="User profile picture">
                </div>

                <div class="col-md-10" style="color: white;">
                    <p style="margin: 10px 0; color: white;">
                        <strong>@lang('customercardb1::contact.prefix'):</strong>
                        <span
                            style="font-size: 28px;">{{ implode(
                                ' ',
                                array_filter(
                                    [$contact->prefix, $contact->first_name, $contact->middle_name, $contact->last_name],
                                    fn($value) => !is_null($value) && $value !== '',
                                ),
                            ) ?:
                                '-' }}</span>
                        :

                        <strong>@lang('customercardb1::contact.customer_groups'):</strong>
                        <span
                            style="font-size: 18px;">{{ $contact->customer_group ? $contact->customer_group->name : '-' }}</span>
                    </p>

                    <!-- Dates -->
                    <p style="margin: 10px 0; color: white;">
                        <strong>
                            <i class="fas fa-bullseye" style="margin-right: 5px;"></i>@lang('customercardb1::contact.register_date'):
                        </strong>
                        <span
                            style="font-size: 18px;">{{ $contact->register_date ? \Carbon\Carbon::parse($contact->register_date)->format('d-m-Y') : '-' }}</span>

                        <strong>
                            <i class="fas fa-bullseye" style="margin-right: 5px;"></i>@lang('customercardb1::contact.expired_at'):
                        </strong>
                        <span
                            style="font-size: 18px;">{{ $contact->expired_date ? \Carbon\Carbon::parse($contact->expired_date)->format('d-m-Y') : '-' }}</span>

                        <strong>
                            <i class="fas fa-bullseye" style="margin-right: 5px;"></i>@lang('customercardb1::contact.study_date'):
                        </strong>
                        <span
                            style="font-size: 18px;">{{ $contact->study_date ? \Carbon\Carbon::parse($contact->study_date)->format('d-m-Y') : '-' }}</span>

                        <strong>
                            <i class="fas fa-bullseye" style="margin-right: 5px;"></i>@lang('lang_v1.created_at'):
                        </strong>
                        <span
                            style="font-size: 18px;">{{ $contact->created_at ? \Carbon\Carbon::parse($contact->created_at)->format('d-m-Y') : '-' }}</span>
                    </p>

                    <!-- Created By -->
                    @php
                        $user = \App\User::find($contact->created_by);
                        $name = $user ? $user->first_name . ' ' . $user->last_name : '-';
                    @endphp
                    <p style="margin: 10px 0; color: white;">
                        <strong>
                            <i class="fas fa-bullseye" style="margin-right: 5px;"></i>@lang('customercardb1::contact.created_by'):
                        </strong>
                        <span style="color: white; font-size: 18px;">{{ $name }}</span>

                        <strong>
                            <i class="fas fa-bullseye" style="margin-right: 5px;"></i>@lang('customercardb1::contact.assigned_to_users'):
                        </strong>
                        <span
                            style="font-size: 18px;">{{ !empty($assignToNames) ? implode(', ', array_column($assignToNames, 'name')) : '-' }}</span>
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
        </div><br> --}}

        <!-- Navbar -->
        @includeIf('productbook::layouts.navbar')
        {{-- @includeIf('productbook::layouts.navbarwidget') --}}

        <div class="row">
            <div class="col-md-12">
                <div class="nav-tabs-custom">
                    <div class="tab-content">
                        @php
                            $view_type = request()->get('view', 'passport_page1');
                        @endphp
                        {{-- <div class="tab-pane active" id="profile_tab">
                            <div class="row">
                                <div class="col-md-12">
                                    @include('productbook::customer.partials.profile_contact')
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="customer_visa_tab">
                            <div class="row">
                                <div class="col-md-12">
                                    @include('productbook::customer.partials.card.customer_visa')
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="passport_card_tab">
                            <div class="row">
                                <div class="col-md-12" align="center">
                                    @include('productbook::customer.partials.card.passport_card')
                                </div>
                            </div>
                        </div> --}}
                        
                        <div class="tab-pane {{ $view_type === 'passport_page1' ? 'active' : '' }}" id="passport_page1_tab">
                            <div class="row">
                                <div class="col-md-12" align="center">
                                    @include('productbook::customer.partials.card.passport_page1')
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane {{ $view_type === 'passport_page2' ? 'active' : '' }}" id="passport_page2_tab">
                            <div class="row">
                                <div class="col-md-12" align="center">
                                    @include('productbook::customer.partials.card.passport_page2')
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="package_franchise_tab">
                            <div class="row">
                                <div class="col-md-12" align="center">
                                    <div style="
                                        width: 100%;
                                        height: 100vh;
                                        overflow: hidden;
                                        ">
                                        <iframe 
                                            src="/minireportb1/standardreport/product-package-price"
                                            style="
                                                width: 100%;
                                                height: calc(100vh + 100px);
                                                transform: translateY(-100px); /* Shift up instead of margin */
                                                border: none;
                                            "
                                        ></iframe>
                                    </div>
                                    {{-- @include('productbook::ProductBook.product_package_price') --}}
                                </div>
                            </div>
                        </div>

                        {{--<div class="tab-pane" id="welcome_card_tab">
                            <div class="row">
                                <div class="col-md-12" align="center">
                                    @include('productbook::customer.partials.card.welcome_card')
                                </div>
                            </div>
                        </div>
                        

                        <div class="tab-pane" id="certificate_of_yeaksa_member_tab">
                            <div class="row">
                                <div class="col-md-12" align="center">
                                    @include('productbook::customer.partials.card.certificate_of_yeaksa_member')
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane" id="certificate_of_yeaksa_training_tab">
                            <div class="row">
                                <div class="col-md-12" align="center">
                                    @include('productbook::customer.partials.card.certificate_of_yeaksa_training')
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="name_card_tab">
                            <div class="row">
                                <div class="col-md-12" align="center">
                                    @include('productbook::customer.partials.card.name_card')
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="id_card_tab">
                            <div class="row">
                                <div class="col-md-12" align="center">
                                    @include('productbook::customer.partials.card.id_card')
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="franchise_contract_tab">
                            <div class="row">
                                <div class="col-md-12" align="center">
                                    @include('productbook::customer.partials.card.franchise_contract')
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane" id="products_contract_tab">
                            <div class="row">
                                <div class="col-md-12" align="center">
                                    @include('productbook::customer.partials.card.products_contract')
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane" id="notification_of_termination_using_franchise_contract_tab">
                            <div class="row">
                                <div class="col-md-12" align="center">
                                    @include('productbook::customer.partials.card.notification_of_termination_using_franchise_contract')
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane" id="checklist_tab">
                            <div class="row">
                                <div class="col-md-12" align="center">
                                    @include('productbook::customer.partials.card.checklist')
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

        <div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>
        
        <div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>
        
        <div class="modal fade pay_contact_due_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>
        
        <div class="modal fade" id="edit_ledger_discount_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>
        
        @include('ledger_discount.create')
    </section>
@endsection

<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_API_KEY') }}&libraries=places"></script>