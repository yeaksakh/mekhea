<?php

use Illuminate\Support\Facades\Route;
use Modules\ProductDoc\Http\Controllers\ProductDocController;
use Modules\ProductDoc\Http\Controllers\SettingController;
use Modules\ProductDoc\Http\Controllers\TelegramWebhookProductDocController;

Route::middleware('web', 'SetSessionData', 'auth', 'ProductDocLanguage', 'timezone', 'AdminSidebarMenu')->prefix('productdoc')->group(function () {
    Route::get('/install', [Modules\ProductDoc\Http\Controllers\InstallController::class, 'index']);
    Route::post('/install', [Modules\ProductDoc\Http\Controllers\InstallController::class, 'install']);
    Route::get('/install/uninstall', [Modules\ProductDoc\Http\Controllers\InstallController::class, 'uninstall']);


    Route::get('/', [ProductDocController::class, 'dashboard'])->name('ProductDoc.dashboard');
    Route::get('/ProductDoc', [ProductDocController::class, 'index'])->name('ProductDoc.index');
    Route::get('/ProductDoc/index', [ProductDocController::class, 'productDoc'])->name('ProductDoc.productDoc');
    Route::get('/ProductDoc/{id}', [ProductDocController::class, 'show'])->name('ProductDoc.show');
    Route::get('/create', [ProductDocController::class, 'create'])->name('ProductDoc.create');
    Route::post('/create', [ProductDocController::class, 'store'])->name('ProductDoc.store');
    Route::get('/edit/{id}', [ProductDocController::class, 'edit'])->name('ProductDoc.edit');
    Route::put('/edit/{id}', [ProductDocController::class, 'update'])->name('ProductDoc.update');
    Route::delete('/delete/{id}', [ProductDocController::class, 'destroy'])->name('ProductDoc.destroy');


    Route::get('/ProductDoc-categories', [ProductDocController::class, 'getCategories'])->name('ProductDoc.getCategories');
    Route::get('/ProductDoc-categories/create', [ProductDocController::class, 'createCategory'])->name('ProductDoc-categories.create');
    Route::post('/ProductDoc-categories', [ProductDocController::class, 'storeCategory'])->name('ProductDoc-categories.store');
    Route::get('/ProductDoc-categories/edit/{id}', [ProductDocController::class, 'editCategory'])->name('ProductDoc-categories.edit');
    Route::put('/ProductDoc-categories/{id}', [ProductDocController::class, 'updateCategory'])->name('ProductDoc-categories.update');
    Route::delete('/ProductDoc-categories/{id}', [ProductDocController::class, 'destroyCategory'])->name('ProductDoc-categories.destroy');

    Route::get('/ProductDoc-permission', [SettingController::class, 'showProductDocPermissionForm'])->name('assignPermissionToBusinessRolesForm');
    Route::post('/ProductDoc-permission', [SettingController::class, 'assignPermissionToRoles'])->name('ProductDoc.permission');
    Route::post('/ProductDoc/lang', [SettingController::class, 'saveTranslations'])->name('ProductDoc.lang');
    Route::post('/ProductDoc/update-language', [SettingController::class, 'updateLanguage'])->name('ProductDoc.update-language');
    Route::post('/ProductDoc/update-social', [SettingController::class, 'updateSocial'])->name('ProductDoc.update-social');
    Route::get('/telegram-messages', [ProductDocController::class, 'getTelegramMessages'])
    ->name('ProductDoc.telegramMessages');

    Route::get('/stream-video/{fileId}', [ProductDocController::class, 'stream'])
    ->name('stream.video');
});

Route::get('/ProductDoc/qrcode-show/{id}', [ProductDocController::class, 'showQrcodeUrl'])->name('ProductDoc.showQrcodeUrl');
Route::get('/ProductDoc/qrcode-qrcodeView/{id}', [ProductDocController::class, 'qrcodeView'])->name('ProductDoc.qrcodeView');

Route::post('/telegram/{id}/productdoc-webhook', [TelegramWebhookProductDocController::class, 'handleWebhook']);
