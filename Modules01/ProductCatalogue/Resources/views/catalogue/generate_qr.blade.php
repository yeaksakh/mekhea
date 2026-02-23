@extends('layouts.app')
@section('title', __( 'productcatalogue::lang.catalogue_qr' ))

@section('content')

<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="/resources/demos/style.css">
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
<script>
    $(function() {
        $("#tabs").tabs();
    });
</script>

<body>

    <div id="tabs">
        <ul>
            <li><a href="#stuff">Employee</a></li>
            <li><a href="#catelogue">catelogue</a></li>
            <li><a href="#customer">customer</a></li>
            <li><a href="#product">Product</a></li>
            <li><a href="#customer_login">Customer Login</a></li>
              <li><a href="#sell_online">Sell Online</a></li>
        </ul>
        <div id="stuff">
            @include('productcatalogue::catalogue.partials.employee')
        </div>
        <div id="catelogue">
            @include('productcatalogue::catalogue.partials.product')
        </div>
        <div id="customer">
            @include('productcatalogue::catalogue.partials.customer')
        </div>
        <div id="product">
            @include('productcatalogue::catalogue.partials.ProductQr')
        </div>
        <div id="customer_login">
            @include('productcatalogue::catalogue.partials.auto_login_customer')
        </div>
         <div id="sell_online">
            @include('productcatalogue::catalogue.partials.sell_online')
        </div>
    </div>
</body>

@endsection
