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
                <a class="navbar-brand" href="{{action([\Modules\ProductBook\Http\Controllers\ProductBookController::class, 'dashboard'])}}">
                    <i class="fa "  style="width: 30px; height: auto; color:#000000;" aria-hidden="true"></i>
                    @lang("productbook::lang.dashboard")
                </a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <!-- Dashboard link -->
                    <li @if(request()->segment(2) == 'ProductBook') class="active" @endif>
                        <a href="{{action([\Modules\ProductBook\Http\Controllers\ProductBookController::class, 'index'])}}">
                            @lang("productbook::lang.productbook")
                        </a>
                    </li>

                    <!-- Product Book link -->
                    <li @if(request()->segment(2) == 'three-tabs') class="active" @endif>
                        <a href="{{action([\Modules\ProductBook\Http\Controllers\ProductBookController::class, 'threeTabs'])}}">
                            @lang("productbook::lang.productbook customer")
                        </a>
                    </li>

                    <!-- Categories link -->
                    <li @if(request()->segment(2) == 'ProductBook-categories') class="active" @endif>
                        <a href="{{action([\Modules\ProductBook\Http\Controllers\ProductBookController::class, 'getCategories'])}}">
                            @lang("productbook::lang.ProductBook_category")
                        </a>
                    </li>

                    <!-- Permission link -->
                    <li @if(request()->segment(2) == 'ProductBook-permission') class="active" @endif>
                        <a href="{{action([\Modules\ProductBook\Http\Controllers\SettingController::class, 'showProductBookPermissionForm'])}}">
                            @lang("productbook::lang.setting")
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</section>