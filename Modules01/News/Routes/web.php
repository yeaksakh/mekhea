<?php

use Illuminate\Support\Facades\Route;
use Modules\News\Http\Controllers\NewsController;
use Modules\News\Http\Controllers\SettingController;

Route::middleware('web', 'SetSessionData', 'auth', 'NewsLanguage', 'timezone', 'AdminSidebarMenu')->prefix('news')->group(function () {
    Route::get('/install', [Modules\News\Http\Controllers\InstallController::class, 'index']);
    Route::post('/install', [Modules\News\Http\Controllers\InstallController::class, 'install']);
    Route::get('/install/uninstall', [Modules\News\Http\Controllers\InstallController::class, 'uninstall']);


    Route::get('/', [NewsController::class, 'dashboard'])->name('News.dashboard');
    Route::get('/News', [NewsController::class, 'index'])->name('News.index');
    Route::get('/News/{id}', [NewsController::class, 'show'])->name('News.show');
    Route::get('/create', [NewsController::class, 'create'])->name('News.create');
    Route::post('/create', [NewsController::class, 'store'])->name('News.store');
    Route::get('/edit/{id}', [NewsController::class, 'edit'])->name('News.edit');
    Route::put('/edit/{id}', [NewsController::class, 'update'])->name('News.update');
    Route::delete('/delete/{id}', [NewsController::class, 'destroy'])->name('News.destroy');

    

    Route::get('/News-categories', [NewsController::class, 'getCategories'])->name('News.getCategories');
    Route::get('/News-categories/create', [NewsController::class, 'createCategory'])->name('News-categories.create');
    Route::post('/News-categories', [NewsController::class, 'storeCategory'])->name('News-categories.store');
    Route::get('/News-categories/edit/{id}', [NewsController::class, 'editCategory'])->name('News-categories.edit');
    Route::put('/News-categories/{id}', [NewsController::class, 'updateCategory'])->name('News-categories.update');
    Route::delete('/News-categories/{id}', [NewsController::class, 'destroyCategory'])->name('News-categories.destroy');

    Route::get('/News-permission', [SettingController::class, 'showNewsPermissionForm'])->name('assignPermissionToBusinessRolesForm');
    Route::post('/News-permission', [SettingController::class, 'assignPermissionToRoles'])->name('News.permission');
    Route::post('/News/lang', [SettingController::class, 'saveTranslations'])->name('News.lang');
    Route::post('/News/update-language', [SettingController::class, 'updateLanguage'])->name('News.update-language');
    Route::post('/News/update-social', [SettingController::class, 'updateSocial'])->name('News.update-social');
});

Route::get('/News/qrcode-show/{id}', [NewsController::class, 'showQrcodeUrl'])->name('News.showQrcodeUrl');
Route::get('/News/qrcode-qrcodeView/{id}', [NewsController::class, 'qrcodeView'])->name('News.qrcodeView');