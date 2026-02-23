@extends('crm::layouts.customer')
@php
    $title = __('crm::lang.add_order_request');
@endphp
@section('title', $title)
@section('content')
    <style>
        .jester_ecommerce_product-scroll-container {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            gap: 1.5rem;
            padding-bottom: .5rem;
        }
        .jester_ecommerce_product-scroll-container > .jester_ecommerce_product-card {
            flex: 0 0 250px;
            min-width: 250px;
        }
        .jester_ecommerce_product-scroll-container::-webkit-scrollbar {
            height: 6px;
        }
        .jester_ecommerce_product-scroll-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        .jester_ecommerce_product-scroll-container::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .jester_ecommerce_step-btn:not(.jester_ecommerce_active) {
            background-color: #cccccc !important;
            color: #000000 !important;
        }
        .jester_ecommerce_step-btn.jester_ecommerce_active {
            background-color: #28a745 !important;
            color: #ffffff !important;
        }

        /* Add to Cart Button Styles */
        .jester_ecommerce_add-to-cart-btn {
            position: relative;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            background: linear-gradient(to right, #ff6200, #ff8c00);
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(255, 98, 0, 0.4);
            letter-spacing: 0.5px;
            width: 100%;
        }
        .jester_ecommerce_add-to-cart-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 98, 0, 0.6);
            background: linear-gradient(to right, #e55a00, #ff7b00);
        }
        .jester_ecommerce_add-to-cart-btn:active {
            transform: translateY(1px);
            box-shadow: 0 3px 10px rgba(255, 98, 0, 0.4);
        }
        .jester_ecommerce_add-to-cart-btn i {
            font-size: 18px;
            transition: all 0.4s ease;
        }
        .jester_ecommerce_cart-text {
            transition: all 0.3s ease;
        }
        .jester_ecommerce_cart-text.jester_ecommerce_hide {
            opacity: 0;
            transform: translateX(-8px);
        }
        .jester_ecommerce_added-text {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%) scale(0.8);
            opacity: 0;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
            color: #fff;
            font-weight: 600;
        }
        .jester_ecommerce_added-text.jester_ecommerce_show {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }
        .jester_ecommerce_cart-icon {
            opacity: 1;
            transition: all 0.3s ease;
        }
        .jester_ecommerce_cart-icon.jester_ecommerce_animate {
            animation: jester_ecommerce_bounce 0.8s ease;
        }
        @keyframes jester_ecommerce_bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }
        .jester_ecommerce_success-check {
            position: absolute;
            opacity: 0;
            transform: scale(0);
            transition: all 0.3s ease;
            color: #4caf50;
            font-size: 20px;
        }
        .jester_ecommerce_success-check.jester_ecommerce_show {
            opacity: 1;
            transform: scale(1);
        }
        .jester_ecommerce_pulse {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.4);
            opacity: 0;
            z-index: -1;
        }
        .jester_ecommerce_add-to-cart-btn:active .jester_ecommerce_pulse {
            animation: jester_ecommerce_pulse 0.5s ease-out;
        }
        @keyframes jester_ecommerce_pulse {
            0% { transform: scale(1); opacity: 0.7; }
            100% { transform: scale(1.5); opacity: 0; }
        }

        .jester_ecommerce_product-scroll-container::-webkit-scrollbar { 
            display: none; 
        }

        .jester_ecommerce_category-tabs-container {
            position: -webkit-sticky; /* For Safari */
            position: sticky;
            top: 0;
            background-color: #fff;
            z-index: 998;
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            gap: 1rem;
            justify-content: flex-start;
            margin-bottom: 2rem;
            padding: 1rem;
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
        .jester_ecommerce_category-tab {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            font-weight: 500;
            color: #333;
            background-color: #f1f5f9;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        .jester_ecommerce_category-tab:hover {
            background-color: #e2e8f0;
        }
        .jester_ecommerce_category-tab.active {
            background-color: #ff6200;
            color: white;
        }
        .jester_ecommerce_category-tabs-container::-webkit-scrollbar {
            height: 6px;
        }
        .jester_ecommerce_category-tabs-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        .jester_ecommerce_category-tabs-container::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        /* Focused Product View Styles */
        #focused_product_view .row {
            display: flex;
            align-items: flex-start;
        }
        #focused_product_image {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 0.5rem;
        }
        #qty_stepper_container {
            width: 200px;
        }
        #add_to_cart_container .jester_ecommerce_add-to-cart-btn {
            width: 200px;
            justify-content: flex-start;
            padding-left: 20px;
        }
        #step1_select_products .jester_ecommerce_add-to-cart-btn {
            justify-content: flex-start;
            padding-left: 20px;
        }
        #related_products_view .jester_ecommerce_add-to-cart-btn {
            justify-content: flex-start;
            padding-left: 20px;
        }

        @media (max-width: 767px) {
            *::-webkit-scrollbar { display: none; }
            #pos_table .jester_ecommerce_pos_quantity {
                min-width: 70px !important;
                text-align: center;
            }
            #pos_table::-webkit-scrollbar { display: none; }
            .jester_ecommerce_product-scroll-container > .jester_ecommerce_product-card {
                flex: 0 0 170px;
                min-width: 170px;
            }
            .jester_ecommerce_product-card img { height: 170px !important; }
            .jester_ecommerce_product-card .tw-p-4 { padding: 0.5rem; }
            .jester_ecommerce_product-card h3 {
                font-size: 0.75rem;
                line-height: 1rem;
                margin-top: 0.25rem;
                font-weight: normal;
            }
            .jester_ecommerce_product-card p {
                font-size: 0.75rem;
                line-height: 1rem;
                margin-top: 0.25rem;
                font-weight: bold;
            }
            #focused_product_view .row {
                flex-direction: column;
            }
            #focused_product_view .col-md-5, #focused_product_view .col-md-7 {
                width: 100%;
                text-align: center;
            }
            #focused_product_image {
                margin-bottom: 1rem;
                margin-left: auto;
                margin-right: auto;
            }
            #focused_product_qty_controls {
                justify-content: center;
            }
            .jester_ecommerce_add-to-cart-btn {
                font-size: 12px;
                padding: 6px 12px;
                gap: 6px;
            }
            #submit-sell, 
            #next_to_review, 
            #go_to_review {
                width: 100%;
            }
        }

        
        .jester_ecommerce_step-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        #steps_container {
            position: relative;
            width: 100%;
            height: 100px; /* Adjust as needed */
        }

        #lineCanvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        .step-wrapper {
            position: relative;
            z-index: 1;
        }
    </style>

    {{-- <section class="content-header">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">{{ $title }}</h1>
    </section> --}}

    <section class="content no-print">
        <input type="hidden" id="amount_rounding_method" value="{{ $pos_settings['amount_rounding_method'] ?? '' }}">
        @if (count($business_locations) > 0)
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
                            {!! Form::select(
                                'select_location_id',
                                $business_locations,
                                $default_location->id ?? null,
                                [
                                    'class' => 'form-control input-sm',
                                    'id' => 'select_location_id',
                                    'required',
                                    'autofocus',
                                ],
                                $bl_attributes,
                            ) !!}
                            <span class="input-group-addon">@show_tooltip(__('tooltip.sale_location'))</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @php
            $custom_labels = json_decode(session('business.custom_labels'), true);
            $common_settings = session()->get('business.common_settings');
        @endphp
        <input type="hidden" id="item_addition_method" value="{{ $business_details->item_addition_method }}">

        {!! Form::open([
            'url' => action([\Modules\Crm\Http\Controllers\OrderRequestController::class, 'store']),
            'method' => 'post',
            'id' => 'add_sell_form',
            'files' => true,
        ]) !!}
        <input type="hidden" id="customer_id" name="contact_id" value="{{ $contact->id }}">
        <input type="hidden" id="sale_type" name="type" value="crm_order_request">

        <!-- STEPS NAVIGATION -->
        <div id="steps_container" class="relative tw-mb-6">
            <div class="tw-flex tw-justify-center tw-items-center">
                <canvas id="lineCanvas" class="absolute top-0 left-0 w-full h-full z-0"></canvas>
                <div class="step-wrapper tw-flex tw-flex-col tw-items-center z-10">
                    <button type="button"
                        class="tw-dw-btn tw-text-white tw-rounded-full tw-w-10 tw-h-10 tw-flex tw-items-center tw-justify-center jester_ecommerce_step-btn"
                        data-step="1" id="step_btn_1">1</button>
                    <span class="tw-mt-2">@lang('crm::lang.products')</span>
                </div>
                <div class="step-spacer" style="width: 50px;"></div>
                <div class="step-wrapper tw-flex tw-flex-col tw-items-center z-10">
                    <button type="button"
                        class="tw-dw-btn tw-text-white tw-rounded-full tw-w-10 tw-h-10 tw-flex tw-items-center tw-justify-center jester_ecommerce_step-btn"
                        data-step="2" id="step_btn_2" disabled>2</button>
                    <span class="tw-mt-2">@lang('crm::lang.cart')</span>
                </div>
                <div class="step-spacer" style="width: 50px;"></div>
                <div class="step-wrapper tw-flex tw-flex-col tw-items-center z-10">
                    <button type="button"
                        class="tw-dw-btn tw-text-white tw-rounded-full tw-w-10 tw-h-10 tw-flex tw-items-center tw-justify-center jester_ecommerce_step-btn"
                        data-step="3" id="step_btn_3" disabled>3</button>
                    <span class="tw-mt-2">@lang('crm::lang.review')</span>
                </div>
            </div>
        </div>

        <!-- STEP 1: SELECT PRODUCTS -->
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="tw-flex tw-justify-end tw-mb-4" id="search_and_back_container">
                    <button type="button" id="step_back_btn" class="tw-dw-btn tw-dw-btn-neutral" style="display: none; position: fixed; left: 10px; top: 9%; z-index: 1000;">
                        <i class="fas fa-arrow-left tw-mr-2"></i>
                    </button>
                    <div class="input-group" style="max-width: 340px;" id="search_box_container">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default bg-white btn-flat" data-toggle="modal"
                                data-target="#configure_search_modal"
                                title="{{ __('lang_v1.configure_product_search') }}">
                                <i class="fas fa-search-plus"></i>
                            </button>
                        </span>
                        {!! Form::text('or_search_product', null, [
                            'class' => 'form-control mousetrap',
                            'id' => 'or_search_product',
                            'placeholder' => __('lang_v1.search_product_placeholder'),
                            'disabled' => is_null($default_location) ? true : false,
                            'autofocus' => is_null($default_location) ? false : true,
                        ]) !!}
                    </div>
                </div>
            </div>
        </div>
        <div id="step1_select_products">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    {!! Form::hidden('location_id', !empty($default_location) ? $default_location->id : null, [
                        'id' => 'location_id',
                        'data-receipt_printer_type' => !empty($default_location->receipt_printer_type)
                            ? $default_location->receipt_printer_type
                            : 'browser',
                        'data-default_payment_accounts' => !empty($default_location) ? $default_location->default_payment_accounts : '',
                    ]) !!}

                    <!-- CATEGORY TABS -->
                    <div class="jester_ecommerce_category-tabs-container">
                        <a href="#" class="jester_ecommerce_category-tab active" data-category="all">
                            @lang('lang_v1.all')
                        </a>
                        @foreach ($products as $category_name => $category_products)
                            @php
                                $category_id = $category_products->first()->category_id;
                            @endphp
                            <a href="#" class="jester_ecommerce_category-tab" data-category="{{ $category_id ?: 'uncategorized' }}">
                                {{ $category_name ?: __('crm::lang.others') }}
                            </a>
                        @endforeach
                    </div>

                    <div id="product_list_container">
                        @foreach ($products as $category_name => $category_products)
                            @php
                                $category_id = $category_products->first()->category_id;
                            @endphp
                            <div class="category-section" id="category_{{ $category_id ?: 'uncategorized' }}">
                                <h2 class="tw-text-xl tw-font-bold tw-mb-4">{{ $category_name ?: __('crm::lang.others') }}</h2>
                                <div class="tw-pb-4">
                                    <div class="jester_ecommerce_product-scroll-container">
                                        @foreach ($category_products as $product)
                                            <div class="jester_ecommerce_product-card view-description-card tw-bg-white tw-rounded-lg tw-shadow-md tw-overflow-hidden tw-flex tw-flex-col tw-transition-all tw-duration-300 hover:tw-shadow-2xl hover:tw-transform hover:-tw-translate-y-2 h-100"
                                                 data-name="{{ $product->product }}"
                                                 data-description="{{ $product->product_description }}"
                                                 data-image-url="{{ $product->image_url }}"
                                                 data-variation-id="{{ $product->variation_id }}"
                                                 data-category-id="{{ $category_id ?: 'uncategorized' }}"
                                                 data-price="@format_currency($product->price)"
                                                 style="cursor: pointer;">
                                                <div class="tw-relative">
                                                    <img src="{{ $product->image_url }}" alt="Product image" class="tw-w-full tw-object-cover" style="height: 200px;">
                                                    <div class="tw-absolute tw-top-2 tw-left-2 tw-bg-red-500 tw-text-white tw-text-xs tw-font-bold tw-px-2 tw-py-1 tw-rounded">-10%</div>
                                                </div>
                                                <div class="tw-p-4 tw-flex-grow tw-flex tw-flex-col">
                                                    <h3 class="tw-font-bold tw-text-lg tw-mt-1">{{ $product->product }}</h3>
                                                    <p class="tw-text-gray-800 tw-font-semibold tw-text-base tw-mt-1">
                                                        @format_currency($product->price)
                                                    </p>
                                                    <div class="tw-mt-auto">
                                                        <button type="button" class="jester_ecommerce_add-to-cart-btn" data-variation="{{ $product->variation_id }}">
                                                            <span class="jester_ecommerce_cart-text">@lang('crm::lang.add_to_cart')</span>
                                                            <span class="jester_ecommerce_added-text">
                                                                <i class="fas fa-check"></i> @lang('crm::lang.added')
                                                            </span>
                                                            <i class="fas fa-shopping-cart jester_ecommerce_cart-icon"></i>
                                                            <i class="fas fa-check-circle jester_ecommerce_success-check"></i>
                                                            <span class="jester_ecommerce_pulse"></span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="row text-center" id="next_button_container" style="margin-bottom: 70px;">
                        <div class="col-sm-12">
                            <button type="button" id="next_to_review" class="tw-dw-btn tw-dw-btn-success tw-text-white tw-dw-btn-lg">
                                @lang('crm::lang.next')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP: VIEW DESCRIPTION -->
        <div id="step_view_description" style="display: none; margin-bottom: 70px;">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    @component('components.widget', ['class' => 'box-solid'])
                        <button type="button" id="back_to_products" class="tw-dw-btn tw-dw-btn-neutral tw-mb-4">
                            <i class="fas fa-arrow-left tw-mr-2"></i> Back to Products
                        </button>
                        
                        <div id="focused_product_view" class="tw-mb-8" data-variation-id="">
                            <div class="row">
                                <div class="col-md-5 text-center">
                                    <img id="focused_product_image" src="" alt="Product image" class="tw-rounded-lg tw-shadow-md">
                                </div>
                                <div class="col-md-7">
                                    <h2 id="focused_product_name" class="tw-text-3xl tw-font-bold tw-mb-2"></h2>
                                    <p id="focused_product_price" class="tw-text-gray-800 tw-font-semibold tw-text-2xl tw-mb-2"></p>
                                    <div id="focused_product_description" class="tw-prose tw-max-w-none tw-mb-4"></div>
                                    <div id="focused_product_qty_controls">
                                        <div id="add_to_cart_container">
                                            <button type="button" class="jester_ecommerce_add-to-cart-btn">
                                                <span class="jester_ecommerce_cart-text">@lang('crm::lang.add_to_cart')</span>
                                                <span class="jester_ecommerce_added-text"><i class="fas fa-check"></i> @lang('crm::lang.added')</span>
                                                <i class="fas fa-shopping-cart jester_ecommerce_cart-icon"></i>
                                                <i class="fas fa-check-circle jester_ecommerce_success-check"></i>
                                                <span class="jester_ecommerce_pulse"></span>
                                            </button>
                                        </div>
                                        <div id="qty_stepper_container" style="display: none;">
                                            <!-- Cloned controls will be injected here -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tw-py-6">
                            <hr>
                        </div>

                        <div id="related_products_view">
                            <h3 class="tw-text-2xl tw-font-bold tw-mb-4">Related Products</h3>
                            <div id="related_products_scroll_container" class="jester_ecommerce_product-scroll-container">
                                <!-- Related products will be injected here -->
                            </div>
                        </div>
                    @endcomponent
                </div>
            </div>
        </div>

        <!-- STEP 2 & 3: REVIEW ORDER / CART -->
        <div id="step2_review_order" style="display: none;">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div id="pos_table_container">
                        @component('components.widget', ['class' => 'box-solid'])
                            <div class="table-responsive">
                                <table class="table table-condensed table-bordered table-striped">
                                    <tr>
                                        <td>
                                            <div class="pull-right">
                                                <b>@lang('sale.item'):</b> <span class="total_quantity">0</span>
                                                &nbsp;&nbsp;&nbsp;&nbsp;
                                                <b>@lang('sale.total'): </b> <span class="price_total">0</span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        @endcomponent
                        @component('components.widget', ['class' => 'box-solid'])
                            <div class="row col-sm-12 pos_product_div" style="min-height: 0">
                                <input type="hidden" name="sell_price_tax" id="sell_price_tax" value="{{ $business_details->sell_price_tax }}">
                                <input type="hidden" id="product_row_count" value="0">
                                <div class="table-responsive">
                                    <table class="table table-condensed table-bordered table-striped table-responsive" id="pos_table">
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        @endcomponent
                        <div class="row" id="review_order_button_container" style="margin-bottom: 70px;">
                            <div class="col-sm-12 text-center">
                                <button type="button" id="go_to_review" class="tw-dw-btn tw-dw-btn-success tw-text-white tw-dw-btn-lg">
                                    @lang('crm::lang.review_order')
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="review_details_container" style="display: none;">
                        @component('components.widget', ['class' => 'box-solid'])
                            <div class="table-responsive">
                                <table class="table table-condensed table-bordered table-striped">
                                    <tr>
                                        <td>
                                            <div class="pull-right">
                                                <b>@lang('sale.item'):</b> <span class="total_quantity">0</span>
                                                &nbsp;&nbsp;&nbsp;&nbsp;
                                                <b>@lang('sale.total'): </b> <span class="price_total">0</span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        @endcomponent

                        @component('components.widget', ['class' => 'box-solid'])
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('sell_note', __('purchase.additional_notes')) !!}
                                    {!! Form::textarea('sale_note', null, ['class' => 'form-control', 'rows' => 3]) !!}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('shipping_address', __('lang_v1.shipping_address')) !!}
                                    {!! Form::textarea('shipping_address', null, [
                                        'class' => 'form-control',
                                        'placeholder' => __('lang_v1.shipping_address'),
                                        'rows' => '3',
                                        'cols' => '30',
                                    ]) !!}
                                </div>
                            </div>

                            <div class="col-md-4 col-md-offset-8">
                                @if (!empty($pos_settings['amount_rounding_method']) && $pos_settings['amount_rounding_method'] > 0)
                                    <small id="round_off"><br>(@lang('lang_v1.round_off'): <span id="round_off_text">0</span>)</small><br />
                                    <input type="hidden" name="round_off_amount" id="round_off_amount" value=0>
                                @endif
                                <div><b>@lang('sale.total_payable'): </b>
                                    <input type="hidden" name="final_total" id="final_total_input">
                                    <span id="total_payable">0</span>
                                </div>
                            </div>
                            <input type="hidden" name="is_direct_sale" value="1">
                        @endcomponent

                        <div class="row" style="margin-bottom: 70px;">
                            <div class="col-sm-12 text-center">
                                <button type="button" id="submit-sell" class="tw-dw-btn tw-dw-btn-success tw-text-white tw-dw-btn-lg">
                                    @lang('messages.save')
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </section>

    @include('sale_pos.partials.configure_search_modal')
@stop

@section('javascript')
    <script src="{{ asset('js/pos.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            let currentStep = 1;

            function getCenter(el, parent) {
                const rect = el.getBoundingClientRect();
                const parentRect = parent.getBoundingClientRect();
                return {
                    x: rect.left - parentRect.left + rect.width / 2,
                    y: rect.top - parentRect.top + rect.height / 2,
                };
            }

            function drawLines() {
                const canvas = document.getElementById('lineCanvas');
                const ctx = canvas.getContext('2d');
                canvas.width = canvas.offsetWidth;
                canvas.height = canvas.offsetHeight;
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                const c1 = document.getElementById('step_btn_1');
                const c2 = document.getElementById('step_btn_2');
                const c3 = document.getElementById('step_btn_3');

                const p1 = getCenter(c1, canvas);
                const p2 = getCenter(c2, canvas);
                const p3 = getCenter(c3, canvas);

                const radius = c1.offsetWidth / 2;

                // Line 1 -> 2
                let dx12 = p2.x - p1.x;
                let dy12 = p2.y - p1.y;
                let dist12 = Math.sqrt(dx12 * dx12 + dy12 * dy12);
                let startX12 = p1.x + (dx12 * radius) / dist12;
                let startY12 = p1.y + (dy12 * radius) / dist12;
                let endX12 = p2.x - (dx12 * radius) / dist12;
                let endY12 = p2.y - (dy12 * radius) / dist12;

                ctx.beginPath();
                ctx.moveTo(startX12, startY12);
                ctx.lineTo(endX12, endY12);
                ctx.strokeStyle = (c2.classList.contains('jester_ecommerce_active') || c3.classList.contains('jester_ecommerce_active')) ? '#2ecc71' : '#2c3e50';
                ctx.lineWidth = 3;
                ctx.stroke();

                // Line 2 -> 3
                let dx23 = p3.x - p2.x;
                let dy23 = p3.y - p2.y;
                let dist23 = Math.sqrt(dx23 * dx23 + dy23 * dy23);
                let startX23 = p2.x + (dx23 * radius) / dist23;
                let startY23 = p2.y + (dy23 * radius) / dist23;
                let endX23 = p3.x - (dx23 * radius) / dist23;
                let endY23 = p3.y - (dy23 * radius) / dist23;

                ctx.beginPath();
                ctx.moveTo(startX23, startY23);
                ctx.lineTo(endX23, endY23);
                ctx.strokeStyle = c3.classList.contains('jester_ecommerce_active') ? '#2ecc71' : '#2c3e50';
                ctx.lineWidth = 3;
                ctx.stroke();
            }

            function updateStepButtons(newStep) {
                currentStep = newStep;
                const hasProducts = $('#pos_table tbody tr').length > 0;
                $('.jester_ecommerce_step-btn').each(function() {
                    const step = parseInt($(this).data('step'));
                    $(this).removeClass('jester_ecommerce_active');
                    if (step === currentStep) $(this).addClass('jester_ecommerce_active');
                    if (step === 1) {
                        $(this).prop('disabled', false);
                    } else if (step === 2 || step === 3) {
                        $(this).prop('disabled', !hasProducts);
                    }
                });

                if (currentStep === 1) {
                    $('#step_back_btn').hide();
                    $('#search_box_container').show();
                } else {
                    $('#step_back_btn').show();
                    $('#search_box_container').hide();
                }

                drawLines();
            }

            function navigateToStep(targetStep) {
                if ($('.jester_ecommerce_step-btn[data-step="' + targetStep + '"]').is(':disabled')) return;
                $('#step1_select_products, #step2_review_order, #pos_table_container, #review_details_container, #step_view_description').hide();
                
                if (targetStep === 1) {
                    $('#step1_select_products').show();
                } else if (targetStep === 2) {
                    $('#step2_review_order').show();
                    $('#pos_table_container').show();
                } else if (targetStep === 3) {
                    $('#step2_review_order').show();
                    $('#review_details_container').show();
                }
                updateStepButtons(targetStep);
            }

            updateStepButtons(1);
            window.onload = drawLines;
            window.onresize = drawLines;

            $('.jester_ecommerce_step-btn').on('click', function() {
                navigateToStep(parseInt($(this).data('step')));
            });

            $('#next_to_review').click(function() {
                if ($('#pos_table tbody tr').length > 0) {
                    navigateToStep(2);
                } else {
                    alert("{{ __('crm::lang.please_add_at_least_one_product') }}");
                }
            });

            $('#go_to_review').click(function() {
                navigateToStep(3);
            });

            $('#step_back_btn').click(function() {
                if (currentStep > 1) {
                    navigateToStep(currentStep - 1);
                }
            });

            function onCartChange() {
                setTimeout(() => {
                    const hasProducts = $('#pos_table tbody tr').length > 0;
                    if (!hasProducts && (currentStep === 2 || currentStep === 3)) {
                        navigateToStep(1);
                    } else {
                        updateStepButtons(currentStep);
                    }
                    if ($('#step_view_description').is(':visible')) {
                        updateFocusedProductControls();
                    }
                }, 100);
            }

            const debounce = (func, delay) => {
                let timeout;
                return function(...args) {
                    const ctx = this;
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(ctx, args), delay);
                };
            };

            const performSearch = () => {
                let term = $('#or_search_product').val().trim();
                if (term === '') {
                    let location_id = $('input#location_id').val();
                    let url = '{{ action([\Modules\Crm\Http\Controllers\OrderRequestController::class, 'create']) }}?location_id=' + location_id;
                    $('#product_list_container').load(url + ' #product_list_container > *');
                    return;
                }
                if (term.length < 1) return;
                let price_group = '';
                let search_fields = [];
                $('.search_fields:checked').each(function(i) {
                    search_fields[i] = $(this).val();
                });
                if ($('#price_group').length > 0) price_group = $('#price_group').val();
                let location_id = $('input#location_id').val();
                $.ajax({
                    url: '/contact/products/list',
                    data: {
                        term: term,
                        location_id: location_id,
                        price_group: price_group,
                        search_fields: search_fields,
                        not_for_selling: 0,
                        per_page: 12
                    },
                    dataType: 'json',
                    success: function(data) {
                        let html;
                        if (data.length === 0) {
                            html = `<div class="tw-col-span-full tw-text-center tw-p-8"><p>@lang('lang_v1.no_products_found')</p></div>`;
                            $('#product_list_container').html(html);
                        } else {
                            let product_html = '';
                            $.each(data, function(i, p) {
                                let img = p.image_url || '{{ url('img/default.png') }}';
                                product_html += `
                                <div class="jester_ecommerce_product-card tw-bg-white tw-rounded-lg tw-shadow-md tw-overflow-hidden tw-flex tw-flex-col tw-transition-all tw-duration-300 hover:tw-shadow-2xl hover:tw-transform hover:-tw-translate-y-2 h-100">
                                    <div class="tw-relative">
                                        <img src="${img}" alt="Product image" class="tw-w-full tw-object-cover" style="height: 200px;">
                                        <div class="tw-absolute tw-top-2 tw-left-2 tw-bg-red-500 tw-text-white tw-text-xs tw-font-bold tw-px-2 tw-py-1 tw-rounded">-10%</div>
                                    </div>
                                    <div class="tw-p-4 tw-flex-grow tw-flex tw-flex-col">
                                        <h3 class="tw-font-bold tw-text-lg tw-mt-1">${p.name}</h3>
                                        <p class="tw-text-gray-800 tw-font-semibold tw-text-base tw-mt-1">${__currency_trans_from_en(p.selling_price, false, false)}</p>
                                        <div class="tw-mt-auto">
                                            <button type="button" class="jester_ecommerce_add-to-cart-btn" data-variation="${p.variation_id}">
                                                <span class="jester_ecommerce_cart-text">@lang('crm::lang.add_to_cart')</span>
                                                <span class="jester_ecommerce_added-text">
                                                    <i class="fas fa-check"></i> @lang('crm::lang.added')
                                                </span>
                                                <i class="fas fa-shopping-cart jester_ecommerce_cart-icon"></i>
                                                <i class="fas fa-check-circle jester_ecommerce_success-check"></i>
                                                <span class="jester_ecommerce_pulse"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>`;
                            });
                            html = '<div class="jester_ecommerce_product-scroll-container">' + product_html + '</div>';
                            $('#product_list_container').html(html);
                        }
                        $('#product_list_container .pagination').hide();
                    }
                });
            };

            $('#or_search_product').on('keyup', debounce(performSearch, 500));

            $(document).on('change', '#select_location_id', function() {
                var location_id = $(this).val();
                $('input#location_id').val(location_id);
                var url = '{{ action([\Modules\Crm\Http\Controllers\OrderRequestController::class, 'create']) }}?location_id=' + location_id;
                $('#product_list_container').load(url + ' #product_list_container > *');
            });

            $(document).on('click', '#product_list_container .pagination a', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                $('#product_list_container').load(url + ' #product_list_container > *');
            });

            function or_product_row(variation_id = null, purchase_line_id = null, weighing_scale_barcode = null, quantity = 1) {
                var existing_row = $('#pos_table tbody tr[data-variation_id="' + variation_id + '"]');
                if (existing_row.length > 0) {
                    var qty_element = existing_row.find('.pos_quantity');
                    var current_qty = __read_number(qty_element);
                    var new_qty = current_qty + quantity;
                    __write_number(qty_element, new_qty);
                    qty_element.change();
                    existing_row.addClass('product-row-updated');
                    setTimeout(function() {
                        existing_row.removeClass('product-row-updated');
                    }, 800);
                    return;
                }
                var product_row = $('input#product_row_count').val();
                var location_id = $('input#location_id').val();
                var customer_id = $('input#customer_id').val();
                $.ajax({
                    method: 'GET',
                    url: '/contact/order-request/get_product_row/' + variation_id + '/' + location_id,
                    async: false,
                    data: {
                        product_row: product_row,
                        customer_id: customer_id,
                        is_direct_sell: true,
                        quantity: quantity,
                    },
                    dataType: 'json',
                    success: function(result) {
                        if (result.success) {
                            var new_row_html = $(result.html_content).attr('data-variation_id', variation_id);
                            $('table#pos_table tbody').append(new_row_html);
                            var new_row = $('table#pos_table tbody tr').last();
                            pos_each_row(new_row);
                            pos_total_row();
                            $('input#product_row_count').val(parseInt(product_row) + 1);
                            onCartChange();
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            }

            $('#shipping_documents').fileinput({
                showUpload: false,
                showPreview: false,
                browseLabel: LANG.file_browse_label,
                removeLabel: LANG.remove
            });

            $(document).on('click', '.jester_ecommerce_category-tab', function(e) {
                e.preventDefault();
                $('.jester_ecommerce_category-tab').removeClass('active');
                $(this).addClass('active');
                var categoryId = $(this).data('category');
                if (categoryId == 'all') {
                    $('.category-section').show();
                } else {
                    $('.category-section').hide();
                    $('#category_' + categoryId).show();
                }
            });

            $(document).on('click', '.jester_ecommerce_add-to-cart-btn', function() {
                const button = $(this);
                const variationId = button.data('variation');
                const cartText    = button.find('.jester_ecommerce_cart-text');
                const addedText   = button.find('.jester_ecommerce_added-text');
                const cartIcon    = button.find('.jester_ecommerce_cart-icon');
                const successCheck= button.find('.jester_ecommerce_success-check');
                cartText.addClass('jester_ecommerce_hide');
                setTimeout(() => cartIcon.addClass('jester_ecommerce_animate'), 200);
                setTimeout(() => {
                    addedText.addClass('jester_ecommerce_show');
                    cartIcon.css('opacity', '0');
                }, 600);
                setTimeout(() => successCheck.addClass('jester_ecommerce_show'), 1000);
                or_product_row(variationId);
                setTimeout(() => {
                    cartIcon.removeClass('jester_ecommerce_animate');
                    addedText.removeClass('jester_ecommerce_show');
                    cartText.removeClass('jester_ecommerce_hide');
                    cartIcon.css('opacity', '1');
                    successCheck.removeClass('jester_ecommerce_show');
                }, 2000);
            });

            function updateFocusedProduct(card) {
                var productName = card.data('name');
                var productDescription = card.data('description');
                var imageUrl = card.data('image-url');
                var variationId = card.data('variation-id');
                var price = card.data('price');

                $('#focused_product_view').attr('data-variation-id', variationId);
                $('#focused_product_image').attr('src', imageUrl);
                $('#focused_product_name').text(productName);
                $('#focused_product_price').text(price);
                $('#focused_product_description').html(productDescription);
                $('#add_to_cart_container .jester_ecommerce_add-to-cart-btn').attr('data-variation', variationId);
                
                updateFocusedProductControls();
            }

            function updateFocusedProductControls() {
                var variationId = $('#focused_product_view').attr('data-variation-id');
                if (!variationId) return;

                var cartRow = $('#pos_table tbody tr[data-variation_id="' + variationId + '"]');
                var addToCartContainer = $('#add_to_cart_container');
                var qtyStepperContainer = $('#qty_stepper_container');

                if (cartRow.length) {
                    var qtyControls = cartRow.find('.pos_quantity').closest('.input-group').clone(true, true);
                    qtyStepperContainer.empty().append(qtyControls);
                    addToCartContainer.hide();
                    qtyStepperContainer.show();
                } else {
                    qtyStepperContainer.hide();
                    addToCartContainer.show();
                }
            }

            function showDescriptionStep(clickedCard) {
                var categoryId = clickedCard.data('category-id');
                
                updateFocusedProduct(clickedCard);

                var relatedContainer = $('#related_products_scroll_container');
                relatedContainer.empty();
                var allCards = $('#product_list_container .view-description-card');
                allCards.each(function() {
                    var currentCard = $(this);
                    if (currentCard.data('category-id') == categoryId && currentCard.data('variation-id') != clickedCard.data('variation-id')) {
                        var clonedCard = currentCard.clone();
                        relatedContainer.append(clonedCard);
                    }
                });

                $('#step1_select_products').hide();
                $('#step_view_description').show();
            }

            $(document).on('click', '.view-description-card', function(e) {
                if ($(e.target).closest('.jester_ecommerce_add-to-cart-btn').length) {
                    return;
                }
                showDescriptionStep($(this));
            });

            $('#back_to_products').on('click', function() {
                $('#step_view_description').hide();
                $('#step1_select_products').show();
            });

            $(document).on('click', '#qty_stepper_container button', function() {
                var variationId = $('#focused_product_view').attr('data-variation-id');
                var cartRow = $('#pos_table tbody tr[data-variation_id="' + variationId + '"]');

                // Handle the plus button
                if ($(this).find('i').hasClass('fa-plus')) {
                    cartRow.find('.quantity-up').click();
                    return;
                }

                // Handle the combined minus/trash button by checking visible icon
                if ($(this).find('i.pos_remove_row').is(':visible')) {
                    cartRow.find('.pos_remove_row').click();
                } else if ($(this).find('i.quantity-down').is(':visible')) {
                    cartRow.find('.quantity-down').click();
                }
            });

            var observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    onCartChange();
                });
            });
            observer.observe(document.querySelector('#pos_table tbody'), { childList: true, subtree: true });
        });
    </script>
@endsection
