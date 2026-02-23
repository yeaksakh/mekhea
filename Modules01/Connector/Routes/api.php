<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::resource('business-location', Modules\Connector\Http\Controllers\Api\BusinessLocationController::class)->only('index', 'show');

    Route::resource('contactapi', Modules\Connector\Http\Controllers\Api\ContactController::class)->only('index', 'show', 'store', 'update');

    Route::post('contactapi-payment', [Modules\Connector\Http\Controllers\Api\ContactController::class, 'contactPay']);

    Route::resource('unit', Modules\Connector\Http\Controllers\Api\UnitController::class)->only('index', 'show');

    Route::resource('taxonomy', 'Modules\Connector\Http\Controllers\Api\CategoryController')->only('index', 'show');

    Route::resource('brand', Modules\Connector\Http\Controllers\Api\BrandController::class)->only('index', 'show');

    Route::resource('product', Modules\Connector\Http\Controllers\Api\ProductControllerAPI::class);
    Route::delete('deleteMediaApi/{media_id}', [Modules\Connector\Http\Controllers\Api\ProductControllerAPI::class, 'deleteMedia']);
    Route::get('Dataforcreate', [Modules\Connector\Http\Controllers\Api\ProductControllerAPI::class, 'Dataforcreate']);

    Route::get('selling-price-group', [Modules\Connector\Http\Controllers\Api\ProductControllerAPI::class, 'getSellingPriceGroup']);

    Route::get('variation/{id?}', [Modules\Connector\Http\Controllers\Api\ProductControllerAPI::class, 'listVariations']);

    Route::resource('tax', 'Modules\Connector\Http\Controllers\Api\TaxController')->only('index', 'show');

    Route::resource('table', Modules\Connector\Http\Controllers\Api\TableController::class)->only('index', 'show');

    Route::get('user/loggedin', [Modules\Connector\Http\Controllers\Api\UserController::class, 'loggedin']);
    Route::post('user-registration', [Modules\Connector\Http\Controllers\Api\UserController::class, 'registerUser']);
    Route::resource('user', Modules\Connector\Http\Controllers\Api\UserController::class)->only('index', 'show');
    Route::post('/user/{user_id}/edit', [Modules\Connector\Http\Controllers\Api\UserController::class, 'edit']);

    Route::resource('types-of-service', Modules\Connector\Http\Controllers\Api\TypesOfServiceController::class)->only('index', 'show');

    Route::get('payment-accounts', [Modules\Connector\Http\Controllers\Api\CommonResourceController::class, 'getPaymentAccounts']);

    Route::get('payment-methods', [Modules\Connector\Http\Controllers\Api\CommonResourceController::class, 'getPaymentMethods']);

    Route::resource('sell', Modules\Connector\Http\Controllers\Api\SellController::class)->only('index', 'store', 'show', 'update', 'destroy');
    Route::delete('/delete-sell/{id}', [Modules\Connector\Http\Controllers\Api\SellController::class, 'destroy']);



    Route::post('sell-return', [Modules\Connector\Http\Controllers\Api\SellController::class, 'addSellReturn']);

    Route::get('list-sell-return', [Modules\Connector\Http\Controllers\Api\SellController::class, 'listSellReturn']);

    Route::post('update-shipping-status', [Modules\Connector\Http\Controllers\Api\SellController::class, 'updateSellShippingStatus']);
    Route::post('payment', [Modules\Connector\Http\Controllers\Api\SellController::class, 'storeTransactionPayment']);
    Route::put('/payment/{id}', [Modules\Connector\Http\Controllers\Api\SellController::class, 'updateTransectionPayment']);
    Route::delete('/delete-payment/{id}', [Modules\Connector\Http\Controllers\Api\SellController::class, 'destroyPayment']);

    Route::put('update-audit/{id}', [Modules\Connector\Http\Controllers\Api\SellController::class, 'updateAuditStatus']);
    Route::get('sell-audit/{id}', [Modules\Connector\Http\Controllers\Api\SellController::class, 'editActionStatus']);

    Route::resource('expense', Modules\Connector\Http\Controllers\Api\ExpenseController::class)->only('index', 'store', 'show', 'update');
    Route::get('expense-refund', [Modules\Connector\Http\Controllers\Api\ExpenseController::class, 'listExpenseRefund']);
    Route::put('expense/{id}', [Modules\Connector\Http\Controllers\Api\ExpenseController::class, 'update']);
    Route::delete('expense/{id}', [Modules\Connector\Http\Controllers\Api\ExpenseController::class, 'destroy']);

    Route::get('expense-categories', [Modules\Connector\Http\Controllers\Api\ExpenseController::class, 'listExpenseCategories']);

    Route::get('expense-audit/{id}', [Modules\Connector\Http\Controllers\Api\ExpenseController::class, 'editActionStatus']);

    Route::resource('cash-register', Modules\Connector\Http\Controllers\Api\CashRegisterController::class)->only('index', 'store', 'show', 'update');

    Route::get('business-details', [Modules\Connector\Http\Controllers\Api\CommonResourceController::class, 'getBusinessDetails']);

    Route::get('profit-loss-report', [Modules\Connector\Http\Controllers\Api\CommonResourceController::class, 'getProfitLoss']);

    Route::get('product-stock-report', [Modules\Connector\Http\Controllers\Api\CommonResourceController::class, 'getProductStock']);
    Route::get('notifications', [Modules\Connector\Http\Controllers\Api\CommonResourceController::class, 'getNotifications']);
    Route::post('update_notifications', [Modules\Connector\Http\Controllers\Api\CommonResourceController::class, 'updateNotification']);

    Route::get('active-subscription', [Modules\Connector\Http\Controllers\Api\SuperadminController::class, 'getActiveSubscription']);
    Route::get('packages', [Modules\Connector\Http\Controllers\Api\SuperadminController::class, 'getPackages']);

    Route::get('get-attendance/{user_id}', [Modules\Connector\Http\Controllers\Api\AttendanceController::class, 'getAttendance']);
    Route::get('attendance/{user_id}', [Modules\Connector\Http\Controllers\Api\AttendanceController::class, 'getAllAttendance']);
    Route::post('clock-in', [Modules\Connector\Http\Controllers\Api\AttendanceController::class, 'clockin']);
    Route::post('clock-out', [Modules\Connector\Http\Controllers\Api\AttendanceController::class, 'clockout']);
    Route::get('holidays', [Modules\Connector\Http\Controllers\Api\AttendanceController::class, 'getHolidays']);
    Route::post('update-password', [Modules\Connector\Http\Controllers\Api\UserController::class, 'updatePassword']);
    Route::post('forget-password', [Modules\Connector\Http\Controllers\Api\UserController::class, 'forgetPassword']);
    Route::get('get-location', [Modules\Connector\Http\Controllers\Api\CommonResourceController::class, 'getLocation']);

    Route::get('new_product', [Modules\Connector\Http\Controllers\Api\ProductSellController::class, 'newProduct'])->name('new_product');
    Route::get('new_sell', [Modules\Connector\Http\Controllers\Api\ProductSellController::class, 'newSell'])->name('new_sell');
    Route::get('new_contactapi', [Modules\Connector\Http\Controllers\Api\ProductSellController::class, 'newContactApi'])->name('new_contactapi');

    // route api document and note in costumer
    route::get('document_get/{contact_id}', [Modules\Connector\Http\Controllers\Api\DocumentAndNoteController::class, 'index']);
    route::post('document_add/{contact_id}', [Modules\Connector\Http\Controllers\Api\DocumentAndNoteController::class, 'store']);
    Route::get('document_view/{id}', [Modules\Connector\Http\Controllers\Api\DocumentAndNoteController::class, 'show']);
    Route::post('document_edit/{id}', [Modules\Connector\Http\Controllers\Api\DocumentAndNoteController::class, 'update']);
    Route::post('document_delete/{id}', [Modules\Connector\Http\Controllers\Api\DocumentAndNoteController::class, 'delete']);

    //route api contact payments 
    Route::resource('customer-payment', 'Modules\Connector\Http\Controllers\Api\CostumerPaymentController')->only('index', 'store', 'show', 'update','destroy');
    //reward point
    Route::get('reward-point/{contact_id}', [Modules\Connector\Http\Controllers\Api\RewardPointController::class, 'index']);
    //activity_contact
    Route::get('activity/{id}', [Modules\Connector\Http\Controllers\Api\ActivityAPIController::class, 'index']);
});

Route::middleware('auth:api', 'timezone')->prefix('connector/api/crm')->group(function () {
    Route::resource('follow-ups', 'Modules\Connector\Http\Controllers\Api\Crm\FollowUpController')->only('index', 'store', 'show', 'update');

    Route::get('follow-up-resources', [Modules\Connector\Http\Controllers\Api\Crm\FollowUpController::class, 'getFollowUpResources']);

    Route::get('ledger/{contact_id}', [Modules\Connector\Http\Controllers\Api\Crm\FollowUpController::class, 'getLedger']);

    Route::get('leads', [Modules\Connector\Http\Controllers\Api\Crm\FollowUpController::class, 'getLeads']);

    Route::post('call-logs', [Modules\Connector\Http\Controllers\Api\Crm\CallLogsController::class, 'saveCallLogs']);
});

Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::get('field-force', [Modules\Connector\Http\Controllers\Api\FieldForce\FieldForceController::class, 'index']);
    Route::post('field-force/create', [Modules\Connector\Http\Controllers\Api\FieldForce\FieldForceController::class, 'store']);
    Route::post('field-force/update-visit-status/{id}', [Modules\Connector\Http\Controllers\Api\FieldForce\FieldForceController::class, 'updateStatus']);
});

//api of leave
Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::get('/essentials-leaves', [Modules\Connector\Http\Controllers\Api\EssentialsLeaveController::class, 'index']);
    Route::get('/getUserLeave', [Modules\Connector\Http\Controllers\Api\EssentialsLeaveController::class, 'getUserLeave']);
    Route::get('/getLeaveType', [Modules\Connector\Http\Controllers\Api\EssentialsLeaveController::class, 'getLeaveType']);
    Route::post('/essentials-leaves', [Modules\Connector\Http\Controllers\Api\EssentialsLeaveController::class, 'store']);
    Route::delete('essentials-leaves/{id}', [Modules\Connector\Http\Controllers\Api\EssentialsLeaveController::class, 'destroy']);
    Route::put('essentials-leaves/{id}/change-status', [Modules\Connector\Http\Controllers\Api\EssentialsLeaveController::class, 'changeStatus']);
    
    Route::get('/my-payrolls', [Modules\Connector\Http\Controllers\Api\EssentialsPayrollController::class, 'getMyPayrolls']);
    Route::get('/payroll/{id}', [Modules\Connector\Http\Controllers\Api\EssentialsPayrollController::class, 'show']);

});

//api order request
Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::get('/oder-request', [Modules\Connector\Http\Controllers\Api\OrderRequestController::class, 'index']);
    Route::post('/oder-request', [Modules\Connector\Http\Controllers\Api\OrderRequestController::class, 'store']);
    Route::get('/oder-request/all-sell/{id}', [Modules\Connector\Http\Controllers\Api\OrderRequestController::class, 'show']);
    Route::get('/oder-request/all-sell', [Modules\Connector\Http\Controllers\Api\OrderRequestController::class, 'getSellList']);
});

//api daily report
Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::get('/daily-report', [Modules\Connector\Http\Controllers\Api\DailyReportContoller::class, 'index']);
});

//api tracking user
Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::get('/user-tracking', [Modules\Connector\Http\Controllers\Api\UserTrackingController::class, 'index']);
    Route::get('/view-tracking', [Modules\Connector\Http\Controllers\Api\UserTrackingController::class, 'show']);
    Route::post('/user-tracking', [Modules\Connector\Http\Controllers\Api\UserTrackingController::class, 'clockInClockOut']);
});

//api request expense
Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::resource('request-expense', Modules\Connector\Http\Controllers\Api\RequestExpenseController::class)->only('index', 'store', 'show', 'update');
});

//api customer group
Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::resource('customer-groups', Modules\Connector\Http\Controllers\Api\CustomerGroupController::class);
});
Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    //download file 
    Route::get('download-template', [Modules\Connector\Http\Controllers\api\ImportContactController::class, 'downloadTemplate']);
    // Route for importing contacts via API
    Route::post('import', [Modules\Connector\Http\Controllers\api\ImportContactController::class, 'postImportContacts']);
});
Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::resource('source', Modules\Connector\Http\Controllers\api\TaxonomyController::class);
});
Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::get('user-customer', [Modules\Connector\Http\Controllers\Api\CustomerFollowUpController::class, 'index']);
});
Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::resource('todo', Modules\Connector\Http\Controllers\Api\ToDoApiController::class);
    Route::post('comment', [Modules\Connector\Http\Controllers\Api\ToDoApiController::class, 'addComments']);
    Route::get('show-comment/{id}', [Modules\Connector\Http\Controllers\Api\ToDoApiController::class, 'showComments']);
    Route::delete('delete-comment/{id}', [Modules\Connector\Http\Controllers\Api\ToDoApiController::class, 'deleteComment']);
    Route::post ('uploadDocument', [Modules\Connector\Http\Controllers\Api\ToDoApiController::class, 'uploadDocument']);
    Route::delete ('deleteDocument/{id}', [Modules\Connector\Http\Controllers\Api\ToDoApiController::class, 'deleteDocument']);
    
});

Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::resource('DocumentApi', Modules\Connector\Http\Controllers\Api\DocumentApiController::class);
    Route::get('download/{id}', [Modules\Connector\Http\Controllers\Api\DocumentApiController::class,'download']);
    Route::resource('DocumentShare', Modules\Connector\Http\Controllers\Api\DocumentShareController::class);
    
});
Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::resource('reminder', Modules\Connector\Http\Controllers\Api\ReminderController::class);
    
});
Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::resource('messageApi', Modules\Connector\Http\Controllers\Api\EssentialsMessageController::class);
    Route::get('newMessage', [Modules\Connector\Http\Controllers\Api\EssentialsMessageController::class,'getNewMessages']);
});
Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::resource('knowledge_baseApi', Modules\Connector\Http\Controllers\Api\KnowledgeBaseApiController::class);
});
Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::prefix('laundry-packages')->group(function () {
        Route::get('/', [Modules\Connector\Http\Controllers\Api\LaundryPackageApiController::class, 'index'])->name('api.laundry-packages.index');
        Route::post('/store', [Modules\Connector\Http\Controllers\Api\LaundryPackageApiController::class, 'store'])->name('api.laundry-packages.store');
        Route::get('/{laundryPackage}', [Modules\Connector\Http\Controllers\Api\LaundryPackageApiController::class, 'show'])->name('api.laundry-packages.show');
        Route::put('/{laundryPackage}', [Modules\Connector\Http\Controllers\Api\LaundryPackageApiController::class, 'update'])->name('api.laundry-packages.update');
        Route::delete('/{laundryPackage}', [Modules\Connector\Http\Controllers\Api\LaundryPackageApiController::class, 'destroy'])->name('api.laundry-packages.destroy');

    });
    Route::get('laundry_packages/categories', [Modules\Connector\Http\Controllers\Api\LaundryPackageApiController::class, 'getCategories'])->name('api.laundry-packages.categories.index');        Route::post('/categories', [Modules\Connector\Http\Controllers\Api\LaundryPackageApiController::class, 'storeCategory'])->name('api.laundry-packages.categories.store');
    Route::post('laundry_packages/categories', [Modules\Connector\Http\Controllers\Api\LaundryPackageApiController::class, 'storeCategory'])->name('api.laundry-packages.categories.index');        Route::post('/categories', [Modules\Connector\Http\Controllers\Api\LaundryPackageApiController::class, 'storeCategory'])->name('api.laundry-packages.categories.store');
    Route::get('laundry_packages/categories/edit/{id}', [Modules\Connector\Http\Controllers\Api\LaundryPackageApiController::class, 'editCategory'])->name('api.laundry-packages.categories.edit');
    Route::put('laundry_packages/categories/{id}', [Modules\Connector\Http\Controllers\Api\LaundryPackageApiController::class, 'updateCategory'])->name('api.laundry-packages.categories.update');
    Route::delete('laundry_packages/categories/{id}', [Modules\Connector\Http\Controllers\Api\LaundryPackageApiController::class, 'destroyCategory'])->name('api.laundry-packages.categories.destroy');
});
Route::prefix('connector/api')->group(function () {
    Route::get('product-list', [Modules\Connector\Http\Controllers\Api\ProductControllerAPI::class, 'indexProduct']);        
    Route::get('product-detail/{id}', [Modules\Connector\Http\Controllers\Api\ProductControllerAPI::class, 'productDetail']);        
    // Route::resource('product', Modules\Connector\Http\Controllers\Api\ProductControllerAPI::class);
});