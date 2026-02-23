<?php

use Illuminate\Support\Facades\Route;
use Modules\CustomerStock\Http\Controllers\CustomerStockController;
use Modules\CustomerStock\Http\Controllers\SettingController;

Route::middleware('web', 'SetSessionData', 'auth', 'CustomerStockLanguage', 'timezone', 'AdminSidebarMenu')->prefix('customerstock')->group(function () {
    Route::get('/install', [Modules\CustomerStock\Http\Controllers\InstallController::class, 'index']);
    Route::post('/install', [Modules\CustomerStock\Http\Controllers\InstallController::class, 'install']);
    Route::get('/install/uninstall', [Modules\CustomerStock\Http\Controllers\InstallController::class, 'uninstall']);


    Route::get('/', [CustomerStockController::class, 'dashboard'])->name('CustomerStock.dashboard');
    Route::get('/CustomerStock', [CustomerStockController::class, 'index'])->name('CustomerStock.index');
    Route::get('/CustomerStock/{id}', [CustomerStockController::class, 'show'])->name('CustomerStock.show');
    Route::get('/create', [CustomerStockController::class, 'create'])->name('CustomerStock.create');
    Route::post('/create', [CustomerStockController::class, 'store'])->name('CustomerStock.store');
    Route::get('/delivery/{id}', [CustomerStockController::class, 'delivery'])->name('CustomerStock.delivery');
    Route::post('/process-delivery', [CustomerStockController::class, 'processDelivery'])->name('CustomerStock.processdelivery');
    Route::get('/print-delivery/{id}', [CustomerStockController::class, 'printRecord'])->name('CustomerStock.printdelivery');
    // Example using RouteServiceProvider or directly in your module's routes file
    // Example using RouteServiceProvider or directly in your module's routes file
    // Add this route to your web.php or module routes file
    Route::get('/invoices/search', [CustomerStockController::class, 'getInvoices'])->name('invoices.search');
   

    Route::delete('/delete-delivery/{delivery_id}', 'CustomerStockController@delete')->name('customerstock.delete');
    Route::get('/edit/{id}', 'CustomerStockController@edit')->name('customerstock.edit');
    Route::put('/update/{id}', 'CustomerStockController@update')->name('customerstock.update');

    // This should be in your routes file (e.g., web.php or in your module routes)
 // In your routes file
    Route::get('/customerstock/edit-delivery/{delivery_id}', [CustomerStockController::class, 'editDelivery'])->name('CustomerStock.edit-delivery');
    Route::match(['PUT', 'PATCH', 'POST'], '/customerstock/update-delivery/{delivery_id}', [CustomerStockController::class, 'updateDelivery'])->name('CustomerStock.updateDelivery');


    Route::post('/update-delivery', 'CustomerStockController@updateDelivery');
    Route::get('/view-delivery/{id}', 'CustomerStockController@showDelivery')->name('CustomerStock.showdelivery');


    // Route::put('/edit/{id}', [CustomerStockController::class, 'update'])->name('CustomerStock.update');
    Route::delete('/delete/{id}', [CustomerStockController::class, 'destroy'])->name('CustomerStock.destroy');





    Route::get('/CustomerStock-categories', [CustomerStockController::class, 'getCategories'])->name('CustomerStock.getCategories');
    Route::get('/CustomerStock-categories/create', [CustomerStockController::class, 'createCategory'])->name('CustomerStock-categories.create');
    Route::post('/CustomerStock-categories', [CustomerStockController::class, 'storeCategory'])->name('CustomerStock-categories.store');
    Route::get('/CustomerStock-categories/edit/{id}', [CustomerStockController::class, 'editCategory'])->name('CustomerStock-categories.edit');
    Route::put('/CustomerStock-categories/{id}', [CustomerStockController::class, 'updateCategory'])->name('CustomerStock-categories.update');
    Route::delete('/CustomerStock-categories/{id}', [CustomerStockController::class, 'destroyCategory'])->name('CustomerStock-categories.destroy');

    Route::get('/CustomerStock-permission', [SettingController::class, 'showCustomerStockPermissionForm'])->name('assignPermissionToBusinessRolesForm');
    Route::post('/CustomerStock-permission', [SettingController::class, 'assignPermissionToRoles'])->name('CustomerStock.permission');
    Route::post('/CustomerStock/lang', [SettingController::class, 'saveTranslations'])->name('CustomerStock.lang');
    Route::post('/CustomerStock/update-language', [SettingController::class, 'updateLanguage'])->name('CustomerStock.update-language');
    Route::post('/CustomerStock/update-social', [SettingController::class, 'updateSocial'])->name('CustomerStock.update-social');
});

Route::get('/CustomerStock/qrcode-show/{id}', [CustomerStockController::class, 'showQrcodeUrl'])->name('CustomerStock.showQrcodeUrl');
Route::get('/CustomerStock/qrcode-qrcodeView/{id}', [CustomerStockController::class, 'qrcodeView'])->name('CustomerStock.qrcodeView');
