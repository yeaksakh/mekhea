<section class="no-print">
    <nav class="navbar navbar-default bg-white m-4">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{action([\Modules\ProductCostingB11\Http\Controllers\ProductCostingB11Controller::class, 'index'])}}">
                    <i class="fa fa-donate"  style="width: 30px; height: auto;" aria-hidden="true"></i>
                    @lang("productcostingb11::lang.dashboard")
                </a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <!-- Dashboard link -->
                    <li @if(request()->segment(2) == 'ProductCostingB11') class="active" @endif>
                        <a href="{{action([\Modules\ProductCostingB11\Http\Controllers\ProductCostingB11Controller::class, 'index'])}}">
                            @lang("productcostingb11::lang.productcostingb11")
                        </a>
                    </li>

                    <!-- Categories link -->
                    <li @if(request()->segment(2) == 'ProductCostingB11-categories') class="active" @endif>
                        <a href="{{action([\Modules\ProductCostingB11\Http\Controllers\ProductCostingB11Controller::class, 'getCategories'])}}">
                            @lang("productcostingb11::lang.ProductCostingB11_category")
                        </a>
                    </li>

                    <!-- Permission link -->
                    <li @if(request()->segment(2) == 'ProductCostingB11-permission') class="active" @endif>
                        <a href="{{action([\Modules\ProductCostingB11\Http\Controllers\SettingController::class, 'showProductCostingB11PermissionForm'])}}">
                            @lang("productcostingb11::lang.setting")
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</section>