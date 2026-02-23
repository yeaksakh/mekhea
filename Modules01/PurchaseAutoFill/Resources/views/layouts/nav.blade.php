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
                <a class="navbar-brand" href="{{action([\Modules\PurchaseAutoFill\Http\Controllers\PurchaseAutoFillController::class, 'dashboard'])}}">
                    <i class="fa "  style="width: 30px; height: auto; color:#e11414;" aria-hidden="true"></i>
                    @lang("purchaseautofill::lang.dashboard")
                </a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <!-- Dashboard link -->
                    <li @if(request()->segment(2) == 'PurchaseAutoFill') class="active" @endif>
                        <a href="{{action([\Modules\PurchaseAutoFill\Http\Controllers\PurchaseAutoFillController::class, 'index'])}}">
                            @lang("purchaseautofill::lang.purchaseautofill")
                        </a>
                    </li>

                    <!-- Categories link -->
                    <li @if(request()->segment(2) == 'PurchaseAutoFill-categories') class="active" @endif>
                        <a href="{{action([\Modules\PurchaseAutoFill\Http\Controllers\PurchaseAutoFillController::class, 'getCategories'])}}">
                            @lang("purchaseautofill::lang.PurchaseAutoFill_category")
                        </a>
                    </li>

                    <!-- Permission link -->
                    <li @if(request()->segment(2) == 'PurchaseAutoFill-permission') class="active" @endif>
                        <a href="{{action([\Modules\PurchaseAutoFill\Http\Controllers\SettingController::class, 'showPurchaseAutoFillPermissionForm'])}}">
                            @lang("purchaseautofill::lang.setting")
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</section>