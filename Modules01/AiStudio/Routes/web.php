<?php

use Illuminate\Support\Facades\Route;
use Modules\AiStudio\Http\Controllers\ChatController;
use Modules\AiStudio\Http\Controllers\SettingController;
use Modules\AiStudio\Http\Controllers\AiStudioController;
use Modules\AiStudio\Http\Controllers\DeepSeekController;

Route::middleware('web', 'SetSessionData', 'auth', 'AiStudioLanguage', 'timezone', 'AdminSidebarMenu')->prefix('aistudio')->group(function () {
    Route::get('/install', [Modules\AiStudio\Http\Controllers\InstallController::class, 'index']);
    Route::post('/install', [Modules\AiStudio\Http\Controllers\InstallController::class, 'install']);
    Route::get('/install/uninstall', [Modules\AiStudio\Http\Controllers\InstallController::class, 'uninstall']);

    Route::name('jester.deepseek.')->prefix('deepseek')->group(function () {
        Route::get('/', [DeepSeekController::class, 'index'])->name('index');
        Route::post('send', [DeepSeekController::class, 'send'])->name('send');
        Route::post('new', [DeepSeekController::class, 'new'])->name('new');
        Route::post('rename', [DeepSeekController::class, 'rename'])->name('rename');
        Route::post('delete', [DeepSeekController::class, 'delete'])->name('delete');
        Route::post('edit', [DeepSeekController::class, 'edit'])->name('edit');
    });

    Route::get('/', [AiStudioController::class, 'dashboard'])->name('AiStudio.dashboard');
    Route::get('/AiStudio', [AiStudioController::class, 'index'])->name('AiStudio.index');
    Route::get('/AiStudio/{id}', [AiStudioController::class, 'show'])->name('AiStudio.show');
    Route::get('/create', [AiStudioController::class, 'create'])->name('AiStudio.create');
    Route::post('/create', [AiStudioController::class, 'store'])->name('AiStudio.store');
    Route::get('/edit/{id}', [AiStudioController::class, 'edit'])->name('AiStudio.edit');
    Route::put('/edit/{id}', [AiStudioController::class, 'update'])->name('AiStudio.update');
    Route::delete('/delete/{id}', [AiStudioController::class, 'destroy'])->name('AiStudio.destroy');
    Route::name('jester.')->group(function () {
        Route::get('chat', [ChatController::class, 'index'])->name('chat.index');
        Route::post('chat/send', [ChatController::class, 'send'])->name('chat.send');
        Route::post('chat/new', [ChatController::class, 'new'])->name('chat.new');
        Route::post('chat/rename', [ChatController::class, 'rename'])->name('chat.rename');
        Route::post('chat/delete', [ChatController::class, 'delete'])->name('chat.delete');
        Route::post('chat/edit', [ChatController::class, 'edit'])->name('chat.edit');
    });
    Route::get('/AiStudio-categories', [AiStudioController::class, 'getCategories'])->name('AiStudio.getCategories');
    Route::get('/AiStudio-categories/create', [AiStudioController::class, 'createCategory'])->name('AiStudio-categories.create');
    Route::post('/AiStudio-categories', [AiStudioController::class, 'storeCategory'])->name('AiStudio-categories.store');
    Route::get('/AiStudio-categories/edit/{id}', [AiStudioController::class, 'editCategory'])->name('AiStudio-categories.edit');
    Route::put('/AiStudio-categories/{id}', [AiStudioController::class, 'updateCategory'])->name('AiStudio-categories.update');
    Route::delete('/AiStudio-categories/{id}', [AiStudioController::class, 'destroyCategory'])->name('AiStudio-categories.destroy');

    Route::get('/AiStudio-permission', [SettingController::class, 'showAiStudioPermissionForm'])->name('assignPermissionToBusinessRolesForm');
    Route::post('/AiStudio-permission', [SettingController::class, 'assignPermissionToRoles'])->name('AiStudio.permission');
    Route::post('/AiStudio/lang', [SettingController::class, 'saveTranslations'])->name('AiStudio.lang');
    Route::post('/AiStudio/update-language', [SettingController::class, 'updateLanguage'])->name('AiStudio.update-language');
    Route::post('/AiStudio/update-social', [SettingController::class, 'updateSocial'])->name('AiStudio.update-social');
});

Route::get('/AiStudio/qrcode-show/{id}', [AiStudioController::class, 'showQrcodeUrl'])->name('AiStudio.showQrcodeUrl');
Route::get('/AiStudio/qrcode-qrcodeView/{id}', [AiStudioController::class, 'qrcodeView'])->name('AiStudio.qrcodeView');