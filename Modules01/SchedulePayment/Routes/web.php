<?php

use Illuminate\Support\Facades\Route;
use Modules\SchedulePayment\Http\Controllers\SchedulePaymentController;
use Modules\SchedulePayment\Http\Controllers\SettingController;

Route::middleware('web', 'SetSessionData', 'auth', 'SchedulePaymentLanguage', 'timezone', 'AdminSidebarMenu')->prefix('schedulepayment')->group(function () {
    Route::get('/install', [Modules\SchedulePayment\Http\Controllers\InstallController::class, 'index']);
    Route::post('/install', [Modules\SchedulePayment\Http\Controllers\InstallController::class, 'install']);
    Route::get('/install/uninstall', [Modules\SchedulePayment\Http\Controllers\InstallController::class, 'uninstall']);


    Route::get('/', [SchedulePaymentController::class, 'dashboard'])->name('SchedulePayment.dashboard');
    Route::get('/SchedulePayment', [SchedulePaymentController::class, 'index'])->name('SchedulePayment.index');
    Route::get('/SchedulePayment/{id}', [SchedulePaymentController::class, 'show'])->name('SchedulePayment.show');
    Route::get('/create', [SchedulePaymentController::class, 'create'])->name('SchedulePayment.create');
    Route::post('/create', [SchedulePaymentController::class, 'store'])->name('SchedulePayment.store');
    Route::get('/edit/{id}', [SchedulePaymentController::class, 'edit'])->name('SchedulePayment.edit');
    Route::put('/edit/{id}', [SchedulePaymentController::class, 'update'])->name('SchedulePayment.update');
    Route::delete('/delete/{id}', [SchedulePaymentController::class, 'destroy'])->name('SchedulePayment.destroy');

    

    Route::get('/SchedulePayment-categories', [SchedulePaymentController::class, 'getCategories'])->name('SchedulePayment.getCategories');
    Route::get('/SchedulePayment-categories/create', [SchedulePaymentController::class, 'createCategory'])->name('SchedulePayment-categories.create');
    Route::post('/SchedulePayment-categories', [SchedulePaymentController::class, 'storeCategory'])->name('SchedulePayment-categories.store');
    Route::get('/SchedulePayment-categories/edit/{id}', [SchedulePaymentController::class, 'editCategory'])->name('SchedulePayment-categories.edit');
    Route::put('/SchedulePayment-categories/{id}', [SchedulePaymentController::class, 'updateCategory'])->name('SchedulePayment-categories.update');
    Route::delete('/SchedulePayment-categories/{id}', [SchedulePaymentController::class, 'destroyCategory'])->name('SchedulePayment-categories.destroy');

    Route::get('/SchedulePayment-permission', [SettingController::class, 'showSchedulePaymentPermissionForm'])->name('assignPermissionToBusinessRolesForm');
    Route::post('/SchedulePayment-permission', [SettingController::class, 'assignPermissionToRoles'])->name('SchedulePayment.permission');
    Route::post('/SchedulePayment/lang', [SettingController::class, 'saveTranslations'])->name('SchedulePayment.lang');
    Route::post('/SchedulePayment/update-language', [SettingController::class, 'updateLanguage'])->name('SchedulePayment.update-language');
    Route::post('/SchedulePayment/update-social', [SettingController::class, 'updateSocial'])->name('SchedulePayment.update-social');
});

Route::get('/SchedulePayment/qrcode-show/{id}', [SchedulePaymentController::class, 'showQrcodeUrl'])->name('SchedulePayment.showQrcodeUrl');
Route::get('/SchedulePayment/qrcode-qrcodeView/{id}', [SchedulePaymentController::class, 'qrcodeView'])->name('SchedulePayment.qrcodeView');