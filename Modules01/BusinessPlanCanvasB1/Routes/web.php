<?php

use Illuminate\Support\Facades\Route;
use Modules\BusinessPlanCanvasB1\Http\Controllers\BusinessPlanCanvasB1Controller;
use Modules\BusinessPlanCanvasB1\Http\Controllers\SettingController;

Route::middleware('web', 'SetSessionData', 'auth', 'BusinessPlanCanvasB1Language', 'timezone', 'AdminSidebarMenu')->prefix('businessplancanvasb1')->group(function () {
    Route::get('/', [BusinessPlanCanvasB1Controller::class, 'dashboard'])->name('BusinessPlanCanvasB1.dashboard');
    Route::get('/BusinessPlanCanvasB1', [BusinessPlanCanvasB1Controller::class, 'index'])->name('BusinessPlanCanvasB1.index');
    Route::get('/BusinessPlanCanvasB1/{id}', [BusinessPlanCanvasB1Controller::class, 'show'])->name('BusinessPlanCanvasB1.show');
    Route::get('/create', [BusinessPlanCanvasB1Controller::class, 'create'])->name('BusinessPlanCanvasB1.create');
    Route::post('/create', [BusinessPlanCanvasB1Controller::class, 'store'])->name('BusinessPlanCanvasB1.store');
    Route::get('/edit/{id}', [BusinessPlanCanvasB1Controller::class, 'edit'])->name('BusinessPlanCanvasB1.edit');
    Route::put('/edit/{id}', [BusinessPlanCanvasB1Controller::class, 'update'])->name('BusinessPlanCanvasB1.update');
    Route::delete('/delete/{id}', [BusinessPlanCanvasB1Controller::class, 'destroy'])->name('BusinessPlanCanvasB1.destroy');

    

    Route::get('/BusinessPlanCanvasB1-categories', [BusinessPlanCanvasB1Controller::class, 'getCategories'])->name('BusinessPlanCanvasB1.getCategories');
    Route::get('/BusinessPlanCanvasB1-categories/create', [BusinessPlanCanvasB1Controller::class, 'createCategory'])->name('BusinessPlanCanvasB1-categories.create');
    Route::post('/BusinessPlanCanvasB1-categories', [BusinessPlanCanvasB1Controller::class, 'storeCategory'])->name('BusinessPlanCanvasB1-categories.store');
    Route::get('/BusinessPlanCanvasB1-categories/edit/{id}', [BusinessPlanCanvasB1Controller::class, 'editCategory'])->name('BusinessPlanCanvasB1-categories.edit');
    Route::put('/BusinessPlanCanvasB1-categories/{id}', [BusinessPlanCanvasB1Controller::class, 'updateCategory'])->name('BusinessPlanCanvasB1-categories.update');
    Route::delete('/BusinessPlanCanvasB1-categories/{id}', [BusinessPlanCanvasB1Controller::class, 'destroyCategory'])->name('BusinessPlanCanvasB1-categories.destroy');

    Route::get('/BusinessPlanCanvasB1-permission', [SettingController::class, 'showBusinessPlanCanvasB1PermissionForm'])->name('assignPermissionToBusinessRolesForm');
    Route::post('/BusinessPlanCanvasB1-permission', [SettingController::class, 'assignPermissionToRoles'])->name('BusinessPlanCanvasB1.permission');
    Route::post('/BusinessPlanCanvasB1/lang', [SettingController::class, 'saveTranslations'])->name('BusinessPlanCanvasB1.lang');
    Route::post('/BusinessPlanCanvasB1/update-language', [SettingController::class, 'updateLanguage'])->name('BusinessPlanCanvasB1.update-language');

    Route::get('/install', [Modules\BusinessPlanCanvasB1\Http\Controllers\InstallController::class, 'index']);
    Route::post('/install', [Modules\BusinessPlanCanvasB1\Http\Controllers\InstallController::class, 'install']);
    Route::get('/install/uninstall', [Modules\BusinessPlanCanvasB1\Http\Controllers\InstallController::class, 'uninstall']);
    Route::get('/install/update', [Modules\BusinessPlanCanvasB1\Http\Controllers\InstallController::class, 'update']);
});