<?php

use Illuminate\Support\Facades\Route;
use Modules\PurchaseAutoFill\Http\Controllers\BotImageController;
use Modules\PurchaseAutoFill\Http\Controllers\PurchaseAutoFillController;
use Modules\PurchaseAutoFill\Http\Controllers\SettingController;
use Modules\PurchaseAutoFill\Http\Controllers\TelegramExpenseImageWebhookController;
use Modules\PurchaseAutoFill\Http\Controllers\TelegramWebhookController;

Route::middleware('web', 'SetSessionData', 'auth', 'PurchaseAutoFillLanguage', 'timezone', 'AdminSidebarMenu')->prefix('purchaseautofill')->group(function () {
    Route::get('/install', [Modules\PurchaseAutoFill\Http\Controllers\InstallController::class, 'index']);
    Route::post('/install', [Modules\PurchaseAutoFill\Http\Controllers\InstallController::class, 'install']);
    Route::get('/install/uninstall', [Modules\PurchaseAutoFill\Http\Controllers\InstallController::class, 'uninstall']);


    Route::get('/', [PurchaseAutoFillController::class, 'dashboard'])->name('PurchaseAutoFill.dashboard');
    Route::get('/PurchaseAutoFill', [PurchaseAutoFillController::class, 'index'])->name('PurchaseAutoFill.index');
    Route::get('/PurchaseAutoFill/{id}', [PurchaseAutoFillController::class, 'show'])->name('PurchaseAutoFill.show');
    Route::get('/create', [PurchaseAutoFillController::class, 'create'])->name('PurchaseAutoFill.create');
    Route::post('/create', [PurchaseAutoFillController::class, 'store'])->name('PurchaseAutoFill.store');
    Route::get('/edit/{id}', [PurchaseAutoFillController::class, 'edit'])->name('PurchaseAutoFill.edit');
    Route::put('/edit/{id}', [PurchaseAutoFillController::class, 'update'])->name('PurchaseAutoFill.update');
    Route::delete('/delete/{id}', [PurchaseAutoFillController::class, 'destroy'])->name('PurchaseAutoFill.destroy');


    Route::get('/PurchaseAutoFill-categories', [PurchaseAutoFillController::class, 'getCategories'])->name('PurchaseAutoFill.getCategories');
    Route::get('/PurchaseAutoFill-categories/create', [PurchaseAutoFillController::class, 'createCategory'])->name('PurchaseAutoFill-categories.create');
    Route::post('/PurchaseAutoFill-categories', [PurchaseAutoFillController::class, 'storeCategory'])->name('PurchaseAutoFill-categories.store');
    Route::get('/PurchaseAutoFill-categories/edit/{id}', [PurchaseAutoFillController::class, 'editCategory'])->name('PurchaseAutoFill-categories.edit');
    Route::put('/PurchaseAutoFill-categories/{id}', [PurchaseAutoFillController::class, 'updateCategory'])->name('PurchaseAutoFill-categories.update');
    Route::delete('/PurchaseAutoFill-categories/{id}', [PurchaseAutoFillController::class, 'destroyCategory'])->name('PurchaseAutoFill-categories.destroy');


    Route::get('/PurchaseAutoFill-permission', [SettingController::class, 'showPurchaseAutoFillPermissionForm'])->name('assignPermissionToBusinessRolesForm');
    Route::post('/PurchaseAutoFill-permission', [SettingController::class, 'assignPermissionToRoles'])->name('PurchaseAutoFill.permission');
    Route::post('/PurchaseAutoFill/lang', [SettingController::class, 'saveTranslations'])->name('PurchaseAutoFill.lang');
    Route::post('/PurchaseAutoFill/update-language', [SettingController::class, 'updateLanguage'])->name('PurchaseAutoFill.update-language');
    Route::post('/PurchaseAutoFill/update-social', [SettingController::class, 'updateSocial'])->name('PurchaseAutoFill.update-social');


    // Make sure these routes are defined correctly
    Route::get('/bot-images', [BotImageController::class, 'index'])->name('bot-images.index');
    Route::get('/bot-image/{fileId}', [BotImageController::class, 'showImage'])->name('bot-images.show');
    Route::get('/display-bot-image/{fileId}', [BotImageController::class, 'showImage'])->name('bot-images.display');
    Route::get('/download-image/{fileId}', [BotImageController::class, 'downloadImage'])->name('bot-images.download');
    Route::get('/get-image-details/{id}', [BotImageController::class, 'getImageDetails'])->name('bot-images.details');
    Route::delete('/bot-image/{id}', [BotImageController::class, 'destroy'])
        ->name('bot-images.destroy');


    Route::get('/prefill-create-purchase/{id}', [BotImageController::class, 'prefillForm'])->name('purchaseautofill.prefill');
    Route::get('/telegram/images', [BotImageController::class, 'getGroupImages'])->name('telegram.images');



    // New Accept/Decline Routes
    Route::post('/accept-image', [BotImageController::class, 'acceptImage'])->name('bot-images.accept');
    Route::post('/decline-image', [BotImageController::class, 'declineImage'])->name('bot-images.decline');


    // OCR Processing
    Route::post('/process-ocr/{id}', [BotImageController::class, 'processOcr'])->name('bot-images.process-ocr');


    // Webhook Routes
    Route::get('/set-webhook', [BotImageController::class, 'setWebhook'])->name('bot-images.set-webhook');
    Route::post('/bot-webhook', [BotImageController::class, 'handleWebhook'])->name('bot-images.webhook');
});

    Route::get('/PurchaseAutoFill/qrcode-show/{id}', [PurchaseAutoFillController::class, 'showQrcodeUrl'])->name('PurchaseAutoFill.showQrcodeUrl');
    Route::get('/PurchaseAutoFill/qrcode-qrcodeView/{id}', [PurchaseAutoFillController::class, 'qrcodeView'])->name('PurchaseAutoFill.qrcodeView');

    // In routes/api.php
    Route::post('/telegram/{id}/webhook', [TelegramWebhookController::class, 'handleWebhook']);
    Route::post('/telegram/webhook-expense', [TelegramExpenseImageWebhookController::class, 'handleWebhook']);

    Route::get('/telegram/set-webhook', [TelegramWebhookController::class, 'setWebhook']);
