<?php

use Illuminate\Support\Facades\Route;
use Modules\Documentary\Http\Controllers\DocumentaryController;
use Modules\Documentary\Http\Controllers\SettingController;

Route::middleware('web', 'SetSessionData', 'auth', 'DocumentaryLanguage', 'timezone', 'AdminSidebarMenu')->prefix('documentary')->group(function () {
    Route::get('/install', [Modules\Documentary\Http\Controllers\InstallController::class, 'index']);
    Route::post('/install', [Modules\Documentary\Http\Controllers\InstallController::class, 'install']);
    Route::get('/install/uninstall', [Modules\Documentary\Http\Controllers\InstallController::class, 'uninstall']);


    Route::get('/', [DocumentaryController::class, 'dashboard'])->name('Documentary.dashboard');
    Route::get('/Documentary', [DocumentaryController::class, 'index'])->name('Documentary.index');
    Route::get('/Documentary/{id}', [DocumentaryController::class, 'show'])->name('Documentary.show');
    Route::get('/create', [DocumentaryController::class, 'create'])->name('Documentary.create');
    Route::post('/create', [DocumentaryController::class, 'store'])->name('Documentary.store');
    Route::get('/edit/{id}', [DocumentaryController::class, 'edit'])->name('Documentary.edit');
    // Route::put('/edit/{id}', [DocumentaryController::class, 'update'])->name('Documentary.update');
    Route::match(['POST', 'PUT'], 'documentary/{id}', [DocumentaryController::class, 'update'])->name('Documentary.update');
    Route::delete('/delete/{id}', [DocumentaryController::class, 'destroy'])->name('Documentary.destroy');

    Route::get('/subcategories', [DocumentaryController::class, 'getSubcategories'])->name('Documentary.getSubcategories');

    Route::get('/Documentary-categories', [DocumentaryController::class, 'getCategories'])->name('Documentary.getCategories');
    Route::get('/Documentary-categories/create', [DocumentaryController::class, 'createCategory'])->name('Documentary-categories.create');
    Route::post('/Documentary-categories', [DocumentaryController::class, 'storeCategory'])->name('Documentary-categories.store');
    Route::get('/Documentary-categories/edit/{id}', [DocumentaryController::class, 'editCategory'])->name('Documentary-categories.edit');
    Route::put('/Documentary-categories/{id}', [DocumentaryController::class, 'updateCategory'])->name('Documentary-categories.update');
    Route::delete('/Documentary-categories/{id}', [DocumentaryController::class, 'destroyCategory'])->name('Documentary-categories.destroy');

    Route::get('/Documentary-permission', [SettingController::class, 'showDocumentaryPermissionForm'])->name('assignPermissionToBusinessRolesForm');
    Route::post('/Documentary-permission', [SettingController::class, 'assignPermissionToRoles'])->name('Documentary.permission');
    Route::post('/Documentary/lang', [SettingController::class, 'saveTranslations'])->name('Documentary.lang');
    Route::post('/Documentary/update-language', [SettingController::class, 'updateLanguage'])->name('Documentary.update-language');
    Route::post('/Documentary/update-social', [SettingController::class, 'updateSocial'])->name('Documentary.update-social');
});

Route::get('/Documentary/qrcode-show/{id}', [DocumentaryController::class, 'showQrcodeUrl'])->name('Documentary.showQrcodeUrl');
Route::get('/Documentary/qrcode-qrcodeView/{id}', [DocumentaryController::class, 'qrcodeView'])->name('Documentary.qrcodeView');