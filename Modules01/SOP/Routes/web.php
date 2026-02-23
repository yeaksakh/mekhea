<?php

use Illuminate\Support\Facades\Route;
use Modules\SOP\Http\Controllers\SOPController;
use Modules\SOP\Http\Controllers\SettingController;

Route::middleware('web', 'SetSessionData', 'auth', 'SOPLanguage', 'timezone', 'AdminSidebarMenu')->prefix('sop')->group(function () {
    Route::get('/install', [Modules\SOP\Http\Controllers\InstallController::class, 'index']);
    Route::post('/install', [Modules\SOP\Http\Controllers\InstallController::class, 'install']);
    Route::get('/install/uninstall', [Modules\SOP\Http\Controllers\InstallController::class, 'uninstall']);


    Route::get('/', [SOPController::class, 'dashboard'])->name('SOP.dashboard');
    Route::get('/SOP', [SOPController::class, 'index'])->name('SOP.index');
    Route::get('/SOP/{id}', [SOPController::class, 'show'])->name('SOP.show');
    Route::get('/create', [SOPController::class, 'create'])->name('SOP.create');
    Route::post('/create', [SOPController::class, 'store'])->name('SOP.store');
    Route::get('/edit/{id}', [SOPController::class, 'edit'])->name('SOP.edit');
    Route::put('/edit/{id}', [SOPController::class, 'update'])->name('SOP.update');
    Route::delete('/delete/{id}', [SOPController::class, 'destroy'])->name('SOP.destroy');

    

    Route::get('/SOP-categories', [SOPController::class, 'getCategories'])->name('SOP.getCategories');
    Route::get('/SOP-categories/create', [SOPController::class, 'createCategory'])->name('SOP-categories.create');
    Route::post('/SOP-categories', [SOPController::class, 'storeCategory'])->name('SOP-categories.store');
    Route::get('/SOP-categories/edit/{id}', [SOPController::class, 'editCategory'])->name('SOP-categories.edit');
    Route::put('/SOP-categories/{id}', [SOPController::class, 'updateCategory'])->name('SOP-categories.update');
    Route::delete('/SOP-categories/{id}', [SOPController::class, 'destroyCategory'])->name('SOP-categories.destroy');

    Route::get('/SOP-permission', [SettingController::class, 'showSOPPermissionForm'])->name('assignPermissionToBusinessRolesForm');
    Route::post('/SOP-permission', [SettingController::class, 'assignPermissionToRoles'])->name('SOP.permission');
    Route::post('/SOP/lang', [SettingController::class, 'saveTranslations'])->name('SOP.lang');
    Route::post('/SOP/update-language', [SettingController::class, 'updateLanguage'])->name('SOP.update-language');
    Route::post('/SOP/update-social', [SettingController::class, 'updateSocial'])->name('SOP.update-social');
});

Route::get('/SOP/qrcode-show/{id}', [SOPController::class, 'showQrcodeUrl'])->name('SOP.showQrcodeUrl');
Route::get('/SOP/qrcode-qrcodeView/{id}', [SOPController::class, 'qrcodeView'])->name('SOP.qrcodeView');