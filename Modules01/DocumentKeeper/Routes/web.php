<?php

use Illuminate\Support\Facades\Route;
use Modules\DocumentKeeper\Http\Controllers\DocumentKeeperController;
use Modules\DocumentKeeper\Http\Controllers\SettingController;

Route::middleware('web', 'SetSessionData', 'auth', 'DocumentKeeperLanguage', 'timezone', 'AdminSidebarMenu')->prefix('documentkeeper')->group(function () {
    Route::get('/install', [Modules\DocumentKeeper\Http\Controllers\InstallController::class, 'index']);
    Route::post('/install', [Modules\DocumentKeeper\Http\Controllers\InstallController::class, 'install']);
    Route::get('/install/uninstall', [Modules\DocumentKeeper\Http\Controllers\InstallController::class, 'uninstall']);


    Route::get('/', [DocumentKeeperController::class, 'dashboard'])->name('DocumentKeeper.dashboard');
    Route::get('/DocumentKeeper', [DocumentKeeperController::class, 'index'])->name('DocumentKeeper.index');
    Route::get('/DocumentKeeper/{id}', [DocumentKeeperController::class, 'show'])->name('DocumentKeeper.show');
    Route::get('/create', [DocumentKeeperController::class, 'create'])->name('DocumentKeeper.create');
    Route::post('/create', [DocumentKeeperController::class, 'store'])->name('DocumentKeeper.store');
    Route::get('/edit/{id}', [DocumentKeeperController::class, 'edit'])->name('DocumentKeeper.edit');
    Route::put('/edit/{id}', [DocumentKeeperController::class, 'update'])->name('DocumentKeeper.update');
    Route::delete('/delete/{id}', [DocumentKeeperController::class, 'destroy'])->name('DocumentKeeper.destroy');

    

    Route::get('/DocumentKeeper-categories', [DocumentKeeperController::class, 'getCategories'])->name('DocumentKeeper.getCategories');
    Route::get('/DocumentKeeper-categories/create', [DocumentKeeperController::class, 'createCategory'])->name('DocumentKeeper-categories.create');
    Route::post('/DocumentKeeper-categories', [DocumentKeeperController::class, 'storeCategory'])->name('DocumentKeeper-categories.store');
    Route::get('/DocumentKeeper-categories/edit/{id}', [DocumentKeeperController::class, 'editCategory'])->name('DocumentKeeper-categories.edit');
    Route::put('/DocumentKeeper-categories/{id}', [DocumentKeeperController::class, 'updateCategory'])->name('DocumentKeeper-categories.update');
    Route::delete('/DocumentKeeper-categories/{id}', [DocumentKeeperController::class, 'destroyCategory'])->name('DocumentKeeper-categories.destroy');

    Route::get('/DocumentKeeper-permission', [SettingController::class, 'showDocumentKeeperPermissionForm'])->name('assignPermissionToBusinessRolesForm');
    Route::post('/DocumentKeeper-permission', [SettingController::class, 'assignPermissionToRoles'])->name('DocumentKeeper.permission');
    Route::post('/DocumentKeeper/lang', [SettingController::class, 'saveTranslations'])->name('DocumentKeeper.lang');
    Route::post('/DocumentKeeper/update-language', [SettingController::class, 'updateLanguage'])->name('DocumentKeeper.update-language');
    Route::post('/DocumentKeeper/update-social', [SettingController::class, 'updateSocial'])->name('DocumentKeeper.update-social');
});

Route::get('/DocumentKeeper/qrcode-show/{id}', [DocumentKeeperController::class, 'showQrcodeUrl'])->name('DocumentKeeper.showQrcodeUrl');
Route::get('/DocumentKeeper/qrcode-qrcodeView/{id}', [DocumentKeeperController::class, 'qrcodeView'])->name('DocumentKeeper.qrcodeView');