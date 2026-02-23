<?php

use Illuminate\Support\Facades\Route;
use Modules\ExpenseAutoFill\Http\Controllers\BotImageController;
use Modules\ExpenseAutoFill\Http\Controllers\ExpenseAutoFillController;
use Modules\ExpenseAutoFill\Http\Controllers\SettingController;
use Modules\ExpenseAutoFill\Http\Controllers\TelegramExpenseImageWebhookController;
use Modules\ExpenseAutoFill\Http\Controllers\TelegramWebhookController;

Route::middleware('web', 'SetSessionData', 'auth', 'ExpenseAutoFillLanguage', 'timezone', 'AdminSidebarMenu')->prefix('expenseautofill')->group(function () {
    Route::get('/install', [Modules\ExpenseAutoFill\Http\Controllers\InstallController::class, 'index']);
    Route::post('/install', [Modules\ExpenseAutoFill\Http\Controllers\InstallController::class, 'install']);
    Route::get('/install/uninstall', [Modules\ExpenseAutoFill\Http\Controllers\InstallController::class, 'uninstall']);


    Route::get('/', [ExpenseAutoFillController::class, 'dashboard'])->name('ExpenseAutoFill.dashboard');
    Route::get('/ExpenseAutoFill', [ExpenseAutoFillController::class, 'index'])->name('ExpenseAutoFill.index');
    Route::get('/ExpenseAutoFill/{id}', [ExpenseAutoFillController::class, 'show'])->name('ExpenseAutoFill.show');
    Route::get('/create', [ExpenseAutoFillController::class, 'create'])->name('ExpenseAutoFill.create');
    Route::post('/create', [ExpenseAutoFillController::class, 'store'])->name('ExpenseAutoFill.store');
    Route::get('/edit/{id}', [ExpenseAutoFillController::class, 'edit'])->name('ExpenseAutoFill.edit');
    Route::put('/edit/{id}', [ExpenseAutoFillController::class, 'update'])->name('ExpenseAutoFill.update');
    Route::delete('/delete/{id}', [ExpenseAutoFillController::class, 'destroy'])->name('ExpenseAutoFill.destroy');


    Route::get('/ExpenseAutoFill-categories', [ExpenseAutoFillController::class, 'getCategories'])->name('ExpenseAutoFill.getCategories');
    Route::get('/ExpenseAutoFill-categories/create', [ExpenseAutoFillController::class, 'createCategory'])->name('ExpenseAutoFill-categories.create');
    Route::post('/ExpenseAutoFill-categories', [ExpenseAutoFillController::class, 'storeCategory'])->name('ExpenseAutoFill-categories.store');
    Route::get('/ExpenseAutoFill-categories/edit/{id}', [ExpenseAutoFillController::class, 'editCategory'])->name('ExpenseAutoFill-categories.edit');
    Route::put('/ExpenseAutoFill-categories/{id}', [ExpenseAutoFillController::class, 'updateCategory'])->name('ExpenseAutoFill-categories.update');
    Route::delete('/ExpenseAutoFill-categories/{id}', [ExpenseAutoFillController::class, 'destroyCategory'])->name('ExpenseAutoFill-categories.destroy');


    Route::get('/ExpenseAutoFill-permission', [SettingController::class, 'showExpenseAutoFillPermissionForm'])->name('assignPermissionToBusinessRolesForm');
    Route::post('/ExpenseAutoFill-permission', [SettingController::class, 'assignPermissionToRoles'])->name('ExpenseAutoFill.permission');
    Route::post('/ExpenseAutoFill/lang', [SettingController::class, 'saveTranslations'])->name('ExpenseAutoFill.lang');
    Route::post('/ExpenseAutoFill/update-language', [SettingController::class, 'updateLanguage'])->name('ExpenseAutoFill.update-language');
    Route::post('/ExpenseAutoFill/update-social', [SettingController::class, 'updateSocial'])->name('ExpenseAutoFill.update-social');


    // Make sure these routes are defined correctly
    Route::get('/bot-images', [BotImageController::class, 'index'])->name('bot-images.index');
    Route::get('/bot-image/{fileId}', [BotImageController::class, 'showImage'])->name('bot-images.show');
    Route::get('/display-bot-image/{fileId}', [BotImageController::class, 'showImage'])->name('bot-images.display');
    Route::get('/download-image/{fileId}', [BotImageController::class, 'downloadImage'])->name('bot-images.download');
    Route::get('/get-image-details/{id}', [BotImageController::class, 'getImageDetails'])->name('bot-images.details');
    Route::delete('/bot-image/{id}', [BotImageController::class, 'destroy'])
        ->name('bot-images.destroy');


    Route::get('/prefill-create-expense/{id}', [BotImageController::class, 'prefillForm'])->name('expenseautofill.prefill');
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

    Route::get('/ExpenseAutoFill/qrcode-show/{id}', [ExpenseAutoFillController::class, 'showQrcodeUrl'])->name('ExpenseAutoFill.showQrcodeUrl');
    Route::get('/ExpenseAutoFill/qrcode-qrcodeView/{id}', [ExpenseAutoFillController::class, 'qrcodeView'])->name('ExpenseAutoFill.qrcodeView');

    // In routes/api.php
    Route::post('/telegram/{id}/webhook-expense', [TelegramWebhookController::class, 'handleWebhook']);
    Route::post('/telegram/webhook-expense', [TelegramExpenseImageWebhookController::class, 'handleWebhook']);

    Route::get('/telegram/set-webhook', [TelegramWebhookController::class, 'setWebhook']);
