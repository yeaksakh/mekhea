<?php

use Illuminate\Support\Facades\Route;
use Modules\AutoAudit\Http\Controllers\AuditController;
use Modules\AutoAudit\Http\Controllers\AutoAuditController;
use Modules\AutoAudit\Http\Controllers\SettingController;

Route::middleware('web', 'SetSessionData', 'auth', 'AutoAuditLanguage', 'timezone', 'AdminSidebarMenu')->prefix('autoaudit')->group(function () {
    Route::get('/install', [Modules\AutoAudit\Http\Controllers\InstallController::class, 'index']);
    Route::post('/install', [Modules\AutoAudit\Http\Controllers\InstallController::class, 'install']);
    Route::get('/install/uninstall', [Modules\AutoAudit\Http\Controllers\InstallController::class, 'uninstall']);

    Route::get('/', [AutoAuditController::class, 'dashboard'])->name('AutoAudit.dashboard');
    Route::get('/AutoAudit/{id}', [AutoAuditController::class, 'show'])->name('AutoAudit.show');
    Route::get('/create', [AutoAuditController::class, 'create'])->name('AutoAudit.create');
    Route::post('/create', [AutoAuditController::class, 'store'])->name('AutoAudit.store');
    Route::get('/edit/{id}', [AutoAuditController::class, 'edit'])->name('AutoAudit.edit');
    Route::put('/edit/{id}', [AutoAuditController::class, 'update'])->name('AutoAudit.update');
    Route::delete('/delete/{id}', [AutoAuditController::class, 'destroy'])->name('AutoAudit.destroy');

    Route::get('/AutoAudit-categories', [AutoAuditController::class, 'getCategories'])->name('AutoAudit.getCategories');
    Route::get('/AutoAudit-categories/create', [AutoAuditController::class, 'createCategory'])->name('AutoAudit-categories.create');
    Route::post('/AutoAudit-categories', [AutoAuditController::class, 'storeCategory'])->name('AutoAudit-categories.store');
    Route::get('/AutoAudit-categories/edit/{id}', [AutoAuditController::class, 'editCategory'])->name('AutoAudit-categories.edit');
    Route::put('/AutoAudit-categories/{id}', [AutoAuditController::class, 'updateCategory'])->name('AutoAudit-categories.update');
    Route::delete('/AutoAudit-categories/{id}', [AutoAuditController::class, 'destroyCategory'])->name('AutoAudit-categories.destroy');

    Route::get('/AutoAudit-permission', [SettingController::class, 'showAutoAuditPermissionForm'])->name('assignPermissionToBusinessRolesForm');
    Route::post('/AutoAudit-permission', [SettingController::class, 'assignPermissionToRoles'])->name('AutoAudit.permission');
    Route::post('/AutoAudit/lang', [SettingController::class, 'saveTranslations'])->name('AutoAudit.lang');
    Route::post('/AutoAudit/update-language', [SettingController::class, 'updateLanguage'])->name('AutoAudit.update-language');
    Route::post('/AutoAudit/update-social', [SettingController::class, 'updateSocial'])->name('AutoAudit.update-social');

    // auto audit
    Route::get('/auto-audit', [AuditController::class, 'autoAudit'])->name('AutoAudit.autoaudit');
    Route::get('/AutoAudit', [AuditController::class, 'index'])->name('AutoAudit.index');
    Route::get('/bot-audit', [AuditController::class, 'botAudit'])->name('AutoAudit.botaudit');
    Route::get('/no-bot-audit', [AuditController::class, 'botNotAudit'])->name('AutoAudit.botnotaudit');
    Route::get('/invoices', [AuditController::class, 'getinvoices'])->name('AutoAudit.invoices');

});

Route::get('/AutoAudit/qrcode-show/{id}', [AutoAuditController::class, 'showQrcodeUrl'])->name('AutoAudit.showQrcodeUrl');
Route::get('/AutoAudit/qrcode-qrcodeView/{id}', [AutoAuditController::class, 'qrcodeView'])->name('AutoAudit.qrcodeView');