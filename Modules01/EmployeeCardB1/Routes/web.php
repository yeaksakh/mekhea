<?php

use Illuminate\Support\Facades\Route;
use Modules\EmployeeCardB1\Http\Controllers\SettingController;
use Modules\EmployeeCardB1\Http\Controllers\IndicatorController;
use Modules\EmployeeCardB1\Http\Controllers\ManageUserController;
use Modules\EmployeeCardB1\Http\Controllers\EmployeeCardB1Controller;

Route::middleware('web', 'SetSessionData', 'auth', 'EmployeeCardB1Language', 'timezone', 'AdminSidebarMenu')->prefix('employeecardb1')->group(function () {
    Route::get('/install', [Modules\EmployeeCardB1\Http\Controllers\InstallController::class, 'index']);
    Route::post('/install', [Modules\EmployeeCardB1\Http\Controllers\InstallController::class, 'install']);
    Route::get('/install/uninstall', [Modules\EmployeeCardB1\Http\Controllers\InstallController::class, 'uninstall']);

    Route::get('/appraisal', [IndicatorController::class, 'appraisal'])->name('visa.appraisal');
    
    // Users Cards
    Route::get('/EmployeeCardB1-users', [ManageUserController::class, 'index'])->name('employeecardb1.users.index');
    Route::get('/EmployeeCardB1-users/{id}', [ManageUserController::class, 'show'])->name('employeecardb1.users.show');
    Route::get('visa-appraisals/{user_id}', [ManageUserController::class, 'getVisaAppraisals'])->name('employeecardb1.visa_appraisals');
    Route::get('/appraisal-view/{id}', [IndicatorController::class, 'appraisal_view'])->name('employeecardb1.visa.appraisal.view');
    Route::delete('/appraisal/{appraisal_id}', [IndicatorController::class, 'appraisal_delete'])->name('employeecardb1.visa.appraisal.delete');

    Route::get('/', [EmployeeCardB1Controller::class, 'dashboard'])->name('EmployeeCardB1.dashboard');
    Route::get('/EmployeeCardB1', [EmployeeCardB1Controller::class, 'index'])->name('EmployeeCardB1.index');
    Route::get('/EmployeeCardB1/{id}', [EmployeeCardB1Controller::class, 'show'])->name('EmployeeCardB1.show');
    Route::get('/create', [EmployeeCardB1Controller::class, 'create'])->name('EmployeeCardB1.create');
    Route::post('/create', [EmployeeCardB1Controller::class, 'store'])->name('EmployeeCardB1.store');
    Route::get('/edit/{id}', [EmployeeCardB1Controller::class, 'edit'])->name('EmployeeCardB1.edit');
    Route::put('/edit/{id}', [EmployeeCardB1Controller::class, 'update'])->name('EmployeeCardB1.update');
    Route::delete('/delete/{id}', [EmployeeCardB1Controller::class, 'destroy'])->name('EmployeeCardB1.destroy');

    Route::get('/EmployeeCardB1-categories', [EmployeeCardB1Controller::class, 'getCategories'])->name('EmployeeCardB1.getCategories');
    Route::get('/EmployeeCardB1-categories/create', [EmployeeCardB1Controller::class, 'createCategory'])->name('EmployeeCardB1-categories.create');
    Route::post('/EmployeeCardB1-categories', [EmployeeCardB1Controller::class, 'storeCategory'])->name('EmployeeCardB1-categories.store');
    Route::get('/EmployeeCardB1-categories/edit/{id}', [EmployeeCardB1Controller::class, 'editCategory'])->name('EmployeeCardB1-categories.edit');
    Route::put('/EmployeeCardB1-categories/{id}', [EmployeeCardB1Controller::class, 'updateCategory'])->name('EmployeeCardB1-categories.update');
    Route::delete('/EmployeeCardB1-categories/{id}', [EmployeeCardB1Controller::class, 'destroyCategory'])->name('EmployeeCardB1-categories.destroy');

    Route::get('/EmployeeCardB1-permission', [SettingController::class, 'showEmployeeCardB1PermissionForm'])->name('assignPermissionToBusinessRolesForm');
    Route::post('/EmployeeCardB1-permission', [SettingController::class, 'assignPermissionToRoles'])->name('EmployeeCardB1.permission');
    Route::post('/EmployeeCardB1/lang', [SettingController::class, 'saveTranslations'])->name('EmployeeCardB1.lang');
    Route::post('/EmployeeCardB1/update-language', [SettingController::class, 'updateLanguage'])->name('EmployeeCardB1.update-language');
    Route::post('/EmployeeCardB1/update-social', [SettingController::class, 'updateSocial'])->name('EmployeeCardB1.update-social');
});

Route::get('/EmployeeCardB1/qrcode-show/{id}', [EmployeeCardB1Controller::class, 'showQrcodeUrl'])->name('EmployeeCardB1.showQrcodeUrl');
Route::get('/EmployeeCardB1/qrcode-qrcodeView/{id}', [EmployeeCardB1Controller::class, 'qrcodeView'])->name('EmployeeCardB1.qrcodeView');