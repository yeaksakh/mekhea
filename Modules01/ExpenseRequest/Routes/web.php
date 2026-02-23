<?php

use Illuminate\Support\Facades\Route;
use Modules\ExpenseRequest\Http\Controllers\ExpenseController;
use Modules\ExpenseRequest\Http\Controllers\SettingController;
use Modules\ExpenseRequest\Http\Controllers\ExpenseRequestController;

Route::middleware('web', 'SetSessionData', 'auth', 'ExpenseRequestLanguage', 'timezone', 'AdminSidebarMenu')->prefix('expenserequest')->group(function () {
    Route::get('/install', [Modules\ExpenseRequest\Http\Controllers\InstallController::class, 'index']);
    Route::post('/install', [Modules\ExpenseRequest\Http\Controllers\InstallController::class, 'install']);
    Route::get('/install/uninstall', [Modules\ExpenseRequest\Http\Controllers\InstallController::class, 'uninstall']);


    Route::get('/', [ExpenseRequestController::class, 'dashboard'])->name('ExpenseRequest.dashboard');
    Route::get('/ExpenseRequest', [ExpenseRequestController::class, 'index'])->name('ExpenseRequest.index');
    Route::get('/ExpenseRequest/{id}', [ExpenseRequestController::class, 'show'])->name('ExpenseRequest.show');
    Route::get('/create', [ExpenseRequestController::class, 'create'])->name('ExpenseRequest.create');
    Route::post('/create', [ExpenseRequestController::class, 'store'])->name('ExpenseRequest.store');
    Route::get('/edit/{id}', [ExpenseRequestController::class, 'edit'])->name('ExpenseRequest.edit');
    Route::put('/edit/{id}', [ExpenseRequestController::class, 'update'])->name('ExpenseRequest.update');
    Route::delete('/delete/{id}', [ExpenseRequestController::class, 'destroy'])->name('ExpenseRequest.destroy');

    Route::get('get-expense-request', [ExpenseController::class, 'indexExpenseRequest'])->name('get-expense-request');
    
    Route::get('/ExpenseRequest-categories', [ExpenseRequestController::class, 'getCategories'])->name('ExpenseRequest.getCategories');
    Route::get('/ExpenseRequest-categories/create', [ExpenseRequestController::class, 'createCategory'])->name('ExpenseRequest-categories.create');
    Route::post('/ExpenseRequest-categories', [ExpenseRequestController::class, 'storeCategory'])->name('ExpenseRequest-categories.store');
    Route::get('/ExpenseRequest-categories/edit/{id}', [ExpenseRequestController::class, 'editCategory'])->name('ExpenseRequest-categories.edit');
    Route::put('/ExpenseRequest-categories/{id}', [ExpenseRequestController::class, 'updateCategory'])->name('ExpenseRequest-categories.update');
    Route::delete('/ExpenseRequest-categories/{id}', [ExpenseRequestController::class, 'destroyCategory'])->name('ExpenseRequest-categories.destroy');

    Route::get('/ExpenseRequest-permission', [SettingController::class, 'showExpenseRequestPermissionForm'])->name('assignPermissionToBusinessRolesForm');
    Route::post('/ExpenseRequest-permission', [SettingController::class, 'assignPermissionToRoles'])->name('ExpenseRequest.permission');
    Route::post('/ExpenseRequest/lang', [SettingController::class, 'saveTranslations'])->name('ExpenseRequest.lang');
    Route::post('/ExpenseRequest/update-language', [SettingController::class, 'updateLanguage'])->name('ExpenseRequest.update-language');
    Route::post('/ExpenseRequest/update-social', [SettingController::class, 'updateSocial'])->name('ExpenseRequest.update-social');
});

Route::get('/ExpenseRequest/qrcode-show/{id}', [ExpenseRequestController::class, 'showQrcodeUrl'])->name('ExpenseRequest.showQrcodeUrl');
Route::get('/ExpenseRequest/qrcode-qrcodeView/{id}', [ExpenseRequestController::class, 'qrcodeView'])->name('ExpenseRequest.qrcodeView');