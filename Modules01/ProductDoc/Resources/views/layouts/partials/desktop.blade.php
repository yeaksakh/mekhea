@php
    $business_id = session()->get('user.business_id');
    $module_util = new \App\Utils\ModuleUtil();
    $is_productdoc_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'ProductDoc');
    $is_admin = $module_util->is_admin(auth()->user(), $business_id);
@endphp

<div class="home-grid-tile" data-key="productdoc">
    <a href="{{ action([\Modules\ProductDoc\Http\Controllers\ProductDocController::class, 'index']) }}"  title="{{__('productdoc::lang.productdoc')}}">
        <img src="{{ asset('public/uploads/ProductDoc/1762414083_product-guide-svgrepo-com.svg') }}" class="home-icon" alt="">
        <span class="home-label">{{__('productdoc::lang.productdoc')}}</span>
    </a>
</div>
