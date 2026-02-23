<?php

use Illuminate\Support\Facades\Route;
use Modules\AuditExpense\Http\Controllers\ExpenseController;
use Modules\AuditExpense\Http\Controllers\SettingController;
use Modules\AuditExpense\Http\Controllers\AuditExpenseController;

Route::middleware('web', 'SetSessionData', 'auth', 'AuditExpenseLanguage', 'timezone', 'AdminSidebarMenu')->prefix('auditexpense')->group(function () {
    Route::get('/install', [Modules\AuditExpense\Http\Controllers\InstallController::class, 'index']);
    Route::post('/install', [Modules\AuditExpense\Http\Controllers\InstallController::class, 'install']);
    Route::get('/install/uninstall', [Modules\AuditExpense\Http\Controllers\InstallController::class, 'uninstall']);


    Route::get('/', [AuditExpenseController::class, 'dashboard'])->name('AuditExpense.dashboard');
    Route::get('/AuditExpense', [AuditExpenseController::class, 'index'])->name('AuditExpense.index');
    Route::get('/AuditExpense/{id}', [AuditExpenseController::class, 'show'])->name('AuditExpense.show');
    Route::get('/create', [AuditExpenseController::class, 'create'])->name('AuditExpense.create');
    Route::post('/create', [AuditExpenseController::class, 'store'])->name('AuditExpense.store');
    Route::get('/edit/{id}', [AuditExpenseController::class, 'edit'])->name('AuditExpense.edit');
    Route::put('/edit/{id}', [AuditExpenseController::class, 'update'])->name('AuditExpense.update');
    Route::delete('/delete/{id}', [AuditExpenseController::class, 'destroy'])->name('AuditExpense.destroy');

    // Audit Expenses
    Route::get('/AuditExpense-expenses', [ExpenseController::class, 'index'])->name('auditexpense.expenses.index');
    Route::get('/AuditExpense-expenses/create', [ExpenseController::class, 'create'])->name('auditexpense.expenses.create');
    Route::post('/AuditExpense-expenses/store', [ExpenseController::class, 'store'])->name('auditexpense.expenses.store');
    Route::get('/AuditExpense-expenses/{id}', [ExpenseController::class, 'show'])->name('auditexpense.expenses.show');
    Route::get('/AuditExpense-expenses/{id}/edit', [ExpenseController::class, 'edit'])->name('auditexpense.expenses.edit');
    Route::put('/AuditExpense-expenses/{id}/update', [ExpenseController::class, 'update'])->name('auditexpense.expenses.update');
    Route::delete('/AuditExpense-expenses/{id}/delete', [ExpenseController::class, 'destroy'])->name('auditexpense.expenses.destroy');
    Route::get('/AuditExpense-expenses/edit-audit/{id}', [ExpenseController::class, 'editAudit'])->name('auditexpense.expenses.editAudit');
    Route::put('/AuditExpense-expenses/update-audit/{id}', [ExpenseController::class, 'updateAudit'])->name('auditexpense.expenses.updateAudit');
    Route::get('/AuditExpense-expenses/get-expense-request', [ExpenseController::class, 'indexExpenseRequest'])->name('auditexpense.expenses.get-expense-request');

    Route::get('/AuditExpense-categories', [AuditExpenseController::class, 'getCategories'])->name('AuditExpense.getCategories');
    Route::get('/AuditExpense-categories/create', [AuditExpenseController::class, 'createCategory'])->name('AuditExpense-categories.create');
    Route::post('/AuditExpense-categories', [AuditExpenseController::class, 'storeCategory'])->name('AuditExpense-categories.store');
    Route::get('/AuditExpense-categories/edit/{id}', [AuditExpenseController::class, 'editCategory'])->name('AuditExpense-categories.edit');
    Route::put('/AuditExpense-categories/{id}', [AuditExpenseController::class, 'updateCategory'])->name('AuditExpense-categories.update');
    Route::delete('/AuditExpense-categories/{id}', [AuditExpenseController::class, 'destroyCategory'])->name('AuditExpense-categories.destroy');

    Route::get('/AuditExpense-permission', [SettingController::class, 'showAuditExpensePermissionForm'])->name('assignPermissionToBusinessRolesForm');
    Route::post('/AuditExpense-permission', [SettingController::class, 'assignPermissionToRoles'])->name('AuditExpense.permission');
    Route::post('/AuditExpense/lang', [SettingController::class, 'saveTranslations'])->name('AuditExpense.lang');
    Route::post('/AuditExpense/update-language', [SettingController::class, 'updateLanguage'])->name('AuditExpense.update-language');
    Route::post('/AuditExpense/update-social', [SettingController::class, 'updateSocial'])->name('AuditExpense.update-social');
});

Route::get('/AuditExpense/qrcode-show/{id}', [AuditExpenseController::class, 'showQrcodeUrl'])->name('AuditExpense.showQrcodeUrl');
Route::get('/AuditExpense/qrcode-qrcodeView/{id}', [AuditExpenseController::class, 'qrcodeView'])->name('AuditExpense.qrcodeView');