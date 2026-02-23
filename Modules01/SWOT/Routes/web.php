<?php

use Illuminate\Support\Facades\Route;
use Modules\SWOT\Http\Controllers\SWOTController;
use Modules\SWOT\Http\Controllers\SettingController;

Route::middleware('web', 'SetSessionData', 'auth', 'SWOTLanguage', 'timezone', 'AdminSidebarMenu')->prefix('swot')->group(function () {
    Route::get('/install', [Modules\SWOT\Http\Controllers\InstallController::class, 'index']);
    Route::post('/install', [Modules\SWOT\Http\Controllers\InstallController::class, 'install']);
    Route::get('/install/uninstall', [Modules\SWOT\Http\Controllers\InstallController::class, 'uninstall']);


    Route::get('/', [SWOTController::class, 'dashboard'])->name('SWOT.dashboard');
    Route::get('/SWOT', [SWOTController::class, 'index'])->name('SWOT.index');
    Route::get('/SWOT/{id}', [SWOTController::class, 'show'])->name('SWOT.show');
    Route::get('/create', [SWOTController::class, 'create'])->name('SWOT.create');
    Route::post('/create', [SWOTController::class, 'store'])->name('SWOT.store');
    Route::get('/edit/{id}', [SWOTController::class, 'edit'])->name('SWOT.edit');
    Route::put('/edit/{id}', [SWOTController::class, 'update'])->name('SWOT.update');
    Route::delete('/delete/{id}', [SWOTController::class, 'destroy'])->name('SWOT.destroy');

    

    Route::get('/SWOT-categories', [SWOTController::class, 'getCategories'])->name('SWOT.getCategories');
    Route::get('/SWOT-categories/create', [SWOTController::class, 'createCategory'])->name('SWOT-categories.create');
    Route::post('/SWOT-categories', [SWOTController::class, 'storeCategory'])->name('SWOT-categories.store');
    Route::get('/SWOT-categories/edit/{id}', [SWOTController::class, 'editCategory'])->name('SWOT-categories.edit');
    Route::put('/SWOT-categories/{id}', [SWOTController::class, 'updateCategory'])->name('SWOT-categories.update');
    Route::delete('/SWOT-categories/{id}', [SWOTController::class, 'destroyCategory'])->name('SWOT-categories.destroy');

    Route::get('/SWOT-permission', [SettingController::class, 'showSWOTPermissionForm'])->name('assignPermissionToBusinessRolesForm');
    Route::post('/SWOT-permission', [SettingController::class, 'assignPermissionToRoles'])->name('SWOT.permission');
    Route::post('/SWOT/lang', [SettingController::class, 'saveTranslations'])->name('SWOT.lang');
    Route::post('/SWOT/update-language', [SettingController::class, 'updateLanguage'])->name('SWOT.update-language');
    Route::post('/SWOT/update-social', [SettingController::class, 'updateSocial'])->name('SWOT.update-social');
});

Route::get('/SWOT/qrcode-show/{id}', [SWOTController::class, 'showQrcodeUrl'])->name('SWOT.showQrcodeUrl');
Route::get('/SWOT/qrcode-qrcodeView/{id}', [SWOTController::class, 'qrcodeView'])->name('SWOT.qrcodeView');