{{-- <section class="no-print">
    <nav class="navbar navbar-default bg-white m-4">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{action([\Modules\CustomerStock\Http\Controllers\CustomerStockController::class, 'dashboard'])}}">
                    <i class="fa "  style="width: 30px; height: auto; color:#ff0000;" aria-hidden="true"></i>
                    @lang("customerstock::lang.dashboard")
                </a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <!-- Dashboard link -->
                    <li @if(request()->segment(2) == 'CustomerStock') class="active" @endif>
                        <a href="{{action([\Modules\CustomerStock\Http\Controllers\CustomerStockController::class, 'index'])}}">
                            @lang("customerstock::lang.customerstock")
                        </a>
                    </li>

                    <!-- Categories link -->
                    <li @if(request()->segment(2) == 'CustomerStock-categories') class="active" @endif>
                        <a href="{{action([\Modules\CustomerStock\Http\Controllers\CustomerStockController::class, 'getCategories'])}}">
                            @lang("customerstock::lang.CustomerStock_category")
                        </a>
                    </li>

                    <!-- Permission link -->
                    <li @if(request()->segment(2) == 'CustomerStock-permission') class="active" @endif>
                        <a href="{{action([\Modules\CustomerStock\Http\Controllers\SettingController::class, 'showCustomerStockPermissionForm'])}}">
                            @lang("customerstock::lang.setting")
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</section> --}}