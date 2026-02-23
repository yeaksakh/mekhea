<?php

use Illuminate\Support\Facades\Route;
use Modules\BotTelegramManager\Http\Controllers\BotTelegramManagerController;
use Modules\BotTelegramManager\Http\Controllers\SettingController;

Route::middleware('web', 'SetSessionData', 'auth', 'BotTelegramManagerLanguage', 'timezone', 'AdminSidebarMenu')->prefix('bottelegrammanager')->group(function () {
    Route::get('/install', [Modules\BotTelegramManager\Http\Controllers\InstallController::class, 'index']);
    Route::post('/install', [Modules\BotTelegramManager\Http\Controllers\InstallController::class, 'install']);
    Route::get('/install/uninstall', [Modules\BotTelegramManager\Http\Controllers\InstallController::class, 'uninstall']);


    Route::get('/', [BotTelegramManagerController::class, 'dashboard'])->name('BotTelegramManager.dashboard');
    Route::get('/BotTelegramManager', [BotTelegramManagerController::class, 'index'])->name('BotTelegramManager.index');
    Route::get('/BotTelegramManager/{id}', [BotTelegramManagerController::class, 'show'])->name('BotTelegramManager.show');
    Route::get('/create', [BotTelegramManagerController::class, 'create'])->name('BotTelegramManager.create');
    Route::post('/create', [BotTelegramManagerController::class, 'store'])->name('BotTelegramManager.store');
    Route::get('/edit/{id}', [BotTelegramManagerController::class, 'edit'])->name('BotTelegramManager.edit');
    Route::put('/edit/{id}', [BotTelegramManagerController::class, 'update'])->name('BotTelegramManager.update');
    Route::delete('/delete/{id}', [BotTelegramManagerController::class, 'destroy'])->name('BotTelegramManager.destroy');

    

    Route::get('/BotTelegramManager-categories', [BotTelegramManagerController::class, 'getCategories'])->name('BotTelegramManager.getCategories');
    Route::get('/BotTelegramManager-categories/create', [BotTelegramManagerController::class, 'createCategory'])->name('BotTelegramManager-categories.create');
    Route::post('/BotTelegramManager-categories', [BotTelegramManagerController::class, 'storeCategory'])->name('BotTelegramManager-categories.store');
    Route::get('/BotTelegramManager-categories/edit/{id}', [BotTelegramManagerController::class, 'editCategory'])->name('BotTelegramManager-categories.edit');
    Route::put('/BotTelegramManager-categories/{id}', [BotTelegramManagerController::class, 'updateCategory'])->name('BotTelegramManager-categories.update');
    Route::delete('/BotTelegramManager-categories/{id}', [BotTelegramManagerController::class, 'destroyCategory'])->name('BotTelegramManager-categories.destroy');

    Route::get('/BotTelegramManager-permission', [SettingController::class, 'showBotTelegramManagerPermissionForm'])->name('assignPermissionToBusinessRolesForm');
    Route::post('/BotTelegramManager-permission', [SettingController::class, 'assignPermissionToRoles'])->name('BotTelegramManager.permission');
    Route::post('/BotTelegramManager/lang', [SettingController::class, 'saveTranslations'])->name('BotTelegramManager.lang');
    Route::post('/BotTelegramManager/update-language', [SettingController::class, 'updateLanguage'])->name('BotTelegramManager.update-language');
    Route::post('/BotTelegramManager/update-social', [SettingController::class, 'updateSocial'])->name('BotTelegramManager.update-social');
});

Route::get('/BotTelegramManager/qrcode-show/{id}', [BotTelegramManagerController::class, 'showQrcodeUrl'])->name('BotTelegramManager.showQrcodeUrl');
Route::get('/BotTelegramManager/qrcode-qrcodeView/{id}', [BotTelegramManagerController::class, 'qrcodeView'])->name('BotTelegramManager.qrcodeView');