<?php

use Illuminate\Support\Facades\Route;
use Modules\ProductBook\Http\Controllers\ContactController;
use Modules\ProductBook\Http\Controllers\SettingController;
use Modules\ProductBook\Http\Controllers\IndicatorController;
use Modules\ProductBook\Http\Controllers\ProductBookController;
use Modules\ProductBook\Http\Controllers\ProductReportController;

Route::middleware('web', 'SetSessionData', 'auth', 'ProductBookLanguage', 'timezone', 'AdminSidebarMenu')->prefix('productbook')->group(function () {
    Route::get('/install', [Modules\ProductBook\Http\Controllers\InstallController::class, 'index']);
    Route::post('/install', [Modules\ProductBook\Http\Controllers\InstallController::class, 'install']);
    Route::get('/install/uninstall', [Modules\ProductBook\Http\Controllers\InstallController::class, 'uninstall']);

    Route::get('/standardreport/product-package-price', [ProductReportController::class, 'productPackagePrice'])->name('sr_productPackagePrice1');
    Route::get('/standardreport/products/{product}/details', [ProductReportController::class, 'getDetailProduct'])->name('product.show.details1');

    Route::get('/', [ProductBookController::class, 'dashboard'])->name('ProductBook.dashboard');
    Route::get('/ProductBook', [ProductBookController::class, 'index'])->name('ProductBook.index');
    Route::get('/ProductBook/{id}', [ProductBookController::class, 'show'])->name('ProductBook.show');
    Route::get('/create', [ProductBookController::class, 'create'])->name('ProductBook.create');
    Route::post('/create', [ProductBookController::class, 'store'])->name('ProductBook.store');
    Route::get('/edit/{id}', [ProductBookController::class, 'edit'])->name('ProductBook.edit');
    Route::put('/edit/{id}', [ProductBookController::class, 'update'])->name('ProductBook.update');
    Route::delete('/delete/{id}', [ProductBookController::class, 'destroy'])->name('ProductBook.destroy');

    // Product Book Routes
    Route::get('/ProductBook-customers', [ContactController::class, 'index'])->name('productbook.customers.index');
    Route::get('/ProductBook-customers/{id}', [ContactController::class, 'show'])->name('productbook.customers.show');
    Route::get('/customer/{contact_id}/appraisals', [ContactController::class, 'getCustomerAppraisals'])->name('productbook.customer_appraisals');
    Route::get('/appraisal-view/{id}', [IndicatorController::class, 'appraisal_view'])->name('productbook.visa.appraisal.view');

    Route::get('/ProductBook-categories', [ProductBookController::class, 'getCategories'])->name('ProductBook.getCategories');
    Route::get('/ProductBook-categories/create', [ProductBookController::class, 'createCategory'])->name('ProductBook-categories.create');
    Route::post('/ProductBook-categories', [ProductBookController::class, 'storeCategory'])->name('ProductBook-categories.store');
    Route::get('/ProductBook-categories/edit/{id}', [ProductBookController::class, 'editCategory'])->name('ProductBook-categories.edit');
    Route::put('/ProductBook-categories/{id}', [ProductBookController::class, 'updateCategory'])->name('ProductBook-categories.update');
    Route::delete('/ProductBook-categories/{id}', [ProductBookController::class, 'destroyCategory'])->name('ProductBook-categories.destroy');

    Route::get('/ProductBook-permission', [SettingController::class, 'showProductBookPermissionForm'])->name('assignPermissionToBusinessRolesForm');
    Route::post('/ProductBook-permission', [SettingController::class, 'assignPermissionToRoles'])->name('ProductBook.permission');
    Route::post('/ProductBook/lang', [SettingController::class, 'saveTranslations'])->name('ProductBook.lang');
    Route::post('/ProductBook/update-language', [SettingController::class, 'updateLanguage'])->name('ProductBook.update-language');
    Route::post('/ProductBook/update-social', [SettingController::class, 'updateSocial'])->name('ProductBook.update-social');

    // Standalone 3-tab page
    Route::get('/three-tabs', [ProductBookController::class, 'threeTabs'])->name('ProductBook.threeTabs');
});

Route::get('/ProductBook/qrcode-show/{id}', [ProductBookController::class, 'showQrcodeUrl'])->name('ProductBook.showQrcodeUrl');
Route::get('/ProductBook/qrcode-qrcodeView/{id}', [ProductBookController::class, 'qrcodeView'])->name('ProductBook.qrcodeView');