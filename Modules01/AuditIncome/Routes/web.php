<?php

use Illuminate\Support\Facades\Route;
use Modules\AuditIncome\Http\Controllers\SellController;
use Modules\AuditIncome\Http\Controllers\SellPosController;
use Modules\AuditIncome\Http\Controllers\SettingController;
use Modules\AuditIncome\Http\Controllers\AuditIncomeController;

Route::middleware('web', 'SetSessionData', 'auth', 'AuditIncomeLanguage', 'timezone', 'AdminSidebarMenu')->prefix('auditincome')->group(function () {
    Route::get('/install', [Modules\AuditIncome\Http\Controllers\InstallController::class, 'index']);
    Route::post('/install', [Modules\AuditIncome\Http\Controllers\InstallController::class, 'install']);
    Route::get('/install/uninstall', [Modules\AuditIncome\Http\Controllers\InstallController::class, 'uninstall']);


    Route::get('/', [AuditIncomeController::class, 'dashboard'])->name('AuditIncome.dashboard');
    Route::get('/AuditIncome', [AuditIncomeController::class, 'index'])->name('AuditIncome.index');
    Route::get('/AuditIncome/{id}', [AuditIncomeController::class, 'show'])->name('AuditIncome.show');
    Route::get('/create', [AuditIncomeController::class, 'create'])->name('AuditIncome.create');
    Route::post('/create', [AuditIncomeController::class, 'store'])->name('AuditIncome.store');
    Route::get('/edit/{id}', [AuditIncomeController::class, 'edit'])->name('AuditIncome.edit');
    Route::put('/edit/{id}', [AuditIncomeController::class, 'update'])->name('AuditIncome.update');
    Route::delete('/delete/{id}', [AuditIncomeController::class, 'destroy'])->name('AuditIncome.destroy');

    // Audit Income
    Route::get('/AuditIncome-sells', [SellController::class, 'index'])->name('auditincome.sells.index');
    Route::get('/AuditIncome-sells/create', [SellController::class, 'create'])->name('auditincome.sells.create');
    Route::post('/AuditIncome-sells/store', [SellController::class, 'store'])->name('auditincome.sells.store');
    Route::get('/AuditIncome-sells/{id}', [SellController::class, 'show'])->name('auditincome.sells.show');
    Route::get('/AuditIncome-sells/{id}/edit', [SellController::class, 'edit'])->name('auditincome.sells.edit');
    Route::put('/AuditIncome-sells/{id}/update', [SellController::class, 'update'])->name('auditincome.sells.update');
    Route::delete('/AuditIncome-sale_pos/{id}/update', [SellPosController::class,'update'])->name('auditincome.sale_pos.update');
    Route::delete('/AuditIncome-sells/{id}/delete', [SellPosController::class,'destroy'])->name('auditincome.sells.destroy');
    Route::get('/AuditIncome-sells/edit-audit/{id}', [SellController::class, 'editAudit'])->name('auditincome.sells.editAudit');
    Route::put('/AuditIncome-sells/update-audit/{id}', [SellController::class, 'updateAudit'])->name('auditincome.sells.updateAudit');

    Route::get('/AuditIncome-categories', [AuditIncomeController::class, 'getCategories'])->name('AuditIncome.getCategories');
    Route::get('/AuditIncome-categories/create', [AuditIncomeController::class, 'createCategory'])->name('AuditIncome-categories.create');
    Route::post('/AuditIncome-categories', [AuditIncomeController::class, 'storeCategory'])->name('AuditIncome-categories.store');
    Route::get('/AuditIncome-categories/edit/{id}', [AuditIncomeController::class, 'editCategory'])->name('AuditIncome-categories.edit');
    Route::put('/AuditIncome-categories/{id}', [AuditIncomeController::class, 'updateCategory'])->name('AuditIncome-categories.update');
    Route::delete('/AuditIncome-categories/{id}', [AuditIncomeController::class, 'destroyCategory'])->name('AuditIncome-categories.destroy');

    Route::get('/AuditIncome-permission', [SettingController::class, 'showAuditIncomePermissionForm'])->name('assignPermissionToBusinessRolesForm');
    Route::post('/AuditIncome-permission', [SettingController::class, 'assignPermissionToRoles'])->name('AuditIncome.permission');
    Route::post('/AuditIncome/lang', [SettingController::class, 'saveTranslations'])->name('AuditIncome.lang');
    Route::post('/AuditIncome/update-language', [SettingController::class, 'updateLanguage'])->name('AuditIncome.update-language');
    Route::post('/AuditIncome/update-social', [SettingController::class, 'updateSocial'])->name('AuditIncome.update-social');
});

Route::get('/AuditIncome/qrcode-show/{id}', [AuditIncomeController::class, 'showQrcodeUrl'])->name('AuditIncome.showQrcodeUrl');
Route::get('/AuditIncome/qrcode-qrcodeView/{id}', [AuditIncomeController::class, 'qrcodeView'])->name('AuditIncome.qrcodeView');