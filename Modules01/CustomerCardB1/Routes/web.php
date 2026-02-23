<?php

use Illuminate\Support\Facades\Route;
use Modules\CustomerCardB1\Http\Controllers\ContactController;
use Modules\CustomerCardB1\Http\Controllers\SettingController;
use Modules\CustomerCardB1\Http\Controllers\IndicatorController;
use Modules\CustomerCardB1\Http\Controllers\CustomerCardB1Controller;

Route::middleware('web', 'SetSessionData', 'auth', 'CustomerCardB1Language', 'timezone', 'AdminSidebarMenu')->prefix('customercardb1')->group(function () {
    Route::get('/install', [Modules\CustomerCardB1\Http\Controllers\InstallController::class, 'index']);
    Route::post('/install', [Modules\CustomerCardB1\Http\Controllers\InstallController::class, 'install']);
    Route::get('/install/uninstall', [Modules\CustomerCardB1\Http\Controllers\InstallController::class, 'uninstall']);

    Route::get('/', [CustomerCardB1Controller::class, 'dashboard'])->name('CustomerCardB1.dashboard');
    Route::get('/CustomerCardB1', [CustomerCardB1Controller::class, 'index'])->name('CustomerCardB1.index');
    Route::get('/CustomerCardB1/{id}', [CustomerCardB1Controller::class, 'show'])->name('CustomerCardB1.show');
    Route::get('/create', [CustomerCardB1Controller::class, 'create'])->name('CustomerCardB1.create');
    Route::post('/create', [CustomerCardB1Controller::class, 'store'])->name('CustomerCardB1.store');
    Route::get('/edit/{id}', [CustomerCardB1Controller::class, 'edit'])->name('CustomerCardB1.edit');
    Route::put('/edit/{id}', [CustomerCardB1Controller::class, 'update'])->name('CustomerCardB1.update');
    Route::delete('/delete/{id}', [CustomerCardB1Controller::class, 'destroy'])->name('CustomerCardB1.destroy');

    // Customers Cards
    Route::get('/CustomerCardB1-customers', [ContactController::class, 'index'])->name('customercardb1.customers.index');
    Route::get('/CustomerCardB1-customers/{id}', [ContactController::class, 'show'])->name('customercardb1.customers.show');
    Route::get('/customer/{contact_id}/appraisals', [ContactController::class, 'getCustomerAppraisals'])->name('customercardb1.customer_appraisals');
    Route::get('/appraisal-view/{id}', [IndicatorController::class, 'appraisal_view'])->name('customercardb1.visa.appraisal.view');

    // Project Task
    Route::resource('project-task', 'Modules\CustomerCardB1\Http\Controllers\TaskController');
    Route::get('project-task/{id}/status', 
    [\Modules\CustomerCardB1\Http\Controllers\TaskController::class, 'getTaskStatus'])
    ->name('project-task.status');
    // For the postTaskStatus action
    Route::post('project-task/{id}/update-status', 
        [\Modules\CustomerCardB1\Http\Controllers\TaskController::class, 'postTaskStatus'])
        ->name('project-task.update-status');

    // Indicator
    Route::get('/indicator', [IndicatorController::class, 'index'])->name('customercardb1.visa.indicator.index');
    Route::get('/indicator-create', [IndicatorController::class, 'create'])->name('customercardb1.visa.indicator.create');
    Route::post('/indicator', [IndicatorController::class, 'store'])->name('customercardb1.visa.indicator.store');
    Route::get('/indicator-view/{id}', [IndicatorController::class, 'view'])->name('customercardb1.visa.indicator.view');
    Route::get('/indicator-update/{id}', [IndicatorController::class, 'edit'])->name('customercardb1.visa.indicator.edit');
    Route::put('/indicator/{id}', [IndicatorController::class, 'update'])->name('customercardb1.visa.indicator.update');
    Route::delete('/indicator/{id}', [IndicatorController::class, 'delete'])->name('customercardb1.visa.indicator.delete');
    
    // Visa
    Route::get('/appraisal', [IndicatorController::class, 'appraisal'])->name('customercardb1.visa.appraisal');
    Route::post('/appraisal', [IndicatorController::class, 'appraisal_store'])->name('customercardb1.visa.appraisal.store');
    Route::get('/appraisal-list', [IndicatorController::class, 'appraisal_list'])->name('customercardb1.visa.appraisal.list');
    Route::get('/appraisal-view/{id}', [IndicatorController::class, 'appraisal_view'])->name('customercardb1.visa.appraisal.view');
    Route::delete('/appraisal/{appraisal_id}', [IndicatorController::class, 'appraisal_delete'])->name('customercardb1.visa.appraisal.delete');

    Route::get('/CustomerCardB1-categories', [CustomerCardB1Controller::class, 'getCategories'])->name('CustomerCardB1.getCategories');
    Route::get('/CustomerCardB1-categories/create', [CustomerCardB1Controller::class, 'createCategory'])->name('CustomerCardB1-categories.create');
    Route::post('/CustomerCardB1-categories', [CustomerCardB1Controller::class, 'storeCategory'])->name('CustomerCardB1-categories.store');
    Route::get('/CustomerCardB1-categories/edit/{id}', [CustomerCardB1Controller::class, 'editCategory'])->name('CustomerCardB1-categories.edit');
    Route::put('/CustomerCardB1-categories/{id}', [CustomerCardB1Controller::class, 'updateCategory'])->name('CustomerCardB1-categories.update');
    Route::delete('/CustomerCardB1-categories/{id}', [CustomerCardB1Controller::class, 'destroyCategory'])->name('CustomerCardB1-categories.destroy');

    Route::get('/CustomerCardB1-permission', [SettingController::class, 'showCustomerCardB1PermissionForm'])->name('assignPermissionToBusinessRolesForm');
    Route::post('/CustomerCardB1-permission', [SettingController::class, 'assignPermissionToRoles'])->name('CustomerCardB1.permission');
    Route::post('/CustomerCardB1/lang', [SettingController::class, 'saveTranslations'])->name('CustomerCardB1.lang');
    Route::post('/CustomerCardB1/update-language', [SettingController::class, 'updateLanguage'])->name('CustomerCardB1.update-language');
    Route::post('/CustomerCardB1/update-social', [SettingController::class, 'updateSocial'])->name('CustomerCardB1.update-social');
});

Route::get('/CustomerCardB1/qrcode-show/{id}', [CustomerCardB1Controller::class, 'showQrcodeUrl'])->name('CustomerCardB1.showQrcodeUrl');
Route::get('/CustomerCardB1/qrcode-qrcodeView/{id}', [CustomerCardB1Controller::class, 'qrcodeView'])->name('CustomerCardB1.qrcodeView');