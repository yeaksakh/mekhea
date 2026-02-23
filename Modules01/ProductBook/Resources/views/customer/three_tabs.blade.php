@extends('layouts.app')

@section('title', __('customercardb1::lang.view_customer'))

@section('content')
    <section class="content">
        @includeIf('productbook::layouts.navbar_three_tabs')

        <div class="row">
            <div class="col-md-12">
                <div class="nav-tabs-custom">
                    <div class="tab-content">
                        @php
                            $view_type = request()->get('view', isset($view_type) ? $view_type : 'inner_page');
                        @endphp

                        <div class="tab-pane {{ $view_type === 'inner_page' ? 'active' : '' }}" id="inner_page_tab">
                            <div class="row">
                                <div class="col-md-12" align="center">
                                    @include('productbook::customer.partials.card.inner_page')
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane {{ $view_type === 'cover_page' ? 'active' : '' }}" id="cover_page_tab">
                            <div class="row">
                                <div class="col-md-12" align="center">
                                    @include('productbook::customer.partials.card.cover_page')
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane {{ $view_type === 'static_blank_page' ? 'active' : '' }}" id="static_blank_page_tab">
                            <div class="row">
                                <div class="col-md-12" align="center">
                                    @include('productbook::customer.partials.card.static_blank_page')
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane {{ $view_type === 'package_xs' ? 'active' : '' }}" id="package_xs_tab">
                            <div class="row">
                                <div class="col-md-12" align="center">
                                    @include('productbook::customer.partials.card.PackageXS')
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane {{ $view_type === 'package_b' ? 'active' : '' }}" id="package_b_tab">
                            <div class="row">
                                <div class="col-md-12" align="center">
                                    @include('productbook::customer.partials.card.PackageB')
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane {{ $view_type === 'package_a' ? 'active' : '' }}" id="package_a_tab">
                            <div class="row">
                                <div class="col-md-12" align="center">
                                    @include('productbook::customer.partials.card.PackageA')
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane {{ $view_type === 'package_24page' ? 'active' : '' }}" id="package_24page_tab">
                            <div class="row">
                                <div class="col-md-12" align="center">
                                    @include('productbook::customer.partials.card.Package24page')
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane {{ $view_type === 'package_20page' ? 'active' : '' }}" id="package_20page_tab">
                            <div class="row">
                                <div class="col-md-12" align="center">
                                    @include('productbook::customer.partials.card.Package20page')
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane {{ $view_type === 'package_16page' ? 'active' : '' }}" id="package_16page_tab">
                            <div class="row">
                                <div class="col-md-12" align="center">
                                    @include('productbook::customer.partials.card.Package16page')
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane {{ $view_type === 'package_12page' ? 'active' : '' }}" id="package_12page_tab">
                            <div class="row">
                                <div class="col-md-12" align="center">
                                    @include('productbook::customer.partials.card.Package12page')
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane {{ $view_type === 'package_8page' ? 'active' : '' }}" id="package_8page_tab">
                            <div class="row">
                                <div class="col-md-12" align="center">
                                    @include('productbook::customer.partials.card.Package8page')
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane {{ $view_type === 'package_4page' ? 'active' : '' }}" id="package_4page_tab">
                            <div class="row">
                                <div class="col-md-12" align="center">
                                    @include('productbook::customer.partials.card.Package4page')
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane {{ $view_type === 'package_franchise' ? 'active' : '' }}" id="package_franchise_tab">
                            <div class="row">
                                <div class="col-md-12" align="center">
                                    <div style="width: 100%; height: 100vh; overflow: hidden;">
                                        <iframe 
                                            src="/minireportb1/standardreport/product-package-price"
                                            style="width: 100%; height: calc(100vh + 100px); transform: translateY(-100px); border: none;"
                                        ></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
