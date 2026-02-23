<?php

use Illuminate\Support\Facades\Route;
use Modules\ProductCostingB11\Http\Controllers\ProductCostingB11Controller;
use Modules\ProductCostingB11\Http\Controllers\SettingController;

Route::middleware('web', 'SetSessionData', 'auth', 'ProductCostingB11Language', 'timezone', 'AdminSidebarMenu')->prefix('productcostingb11')->group(function () {
    Route::get('/install', [Modules\ProductCostingB11\Http\Controllers\InstallController::class, 'index']);
    Route::post('/install', [Modules\ProductCostingB11\Http\Controllers\InstallController::class, 'install']);
    Route::get('/install/uninstall', [Modules\ProductCostingB11\Http\Controllers\InstallController::class, 'uninstall']);


    Route::get('/', [ProductCostingB11Controller::class, 'dashboard'])->name('ProductCostingB11.dashboard');
    Route::get('/ProductCostingB11', [ProductCostingB11Controller::class, 'index'])->name('ProductCostingB11.index');
    Route::get('/ProductCostingB11/{id}', [ProductCostingB11Controller::class, 'show'])->name('ProductCostingB11.show');
    Route::get('/create', [ProductCostingB11Controller::class, 'create'])->name('ProductCostingB11.create');
    Route::post('/create', [ProductCostingB11Controller::class, 'store'])->name('ProductCostingB11.store');
    Route::get('/edit/{id}', [ProductCostingB11Controller::class, 'edit'])->name('ProductCostingB11.edit');
    Route::put('/edit/{id}', [ProductCostingB11Controller::class, 'update'])->name('ProductCostingB11.update');
    Route::delete('/delete/{id}', [ProductCostingB11Controller::class, 'destroy'])->name('ProductCostingB11.destroy');

    

    Route::get('/ProductCostingB11-categories', [ProductCostingB11Controller::class, 'getCategories'])->name('ProductCostingB11.getCategories');
    Route::get('/ProductCostingB11-categories/create', [ProductCostingB11Controller::class, 'createCategory'])->name('ProductCostingB11-categories.create');
    Route::post('/ProductCostingB11-categories', [ProductCostingB11Controller::class, 'storeCategory'])->name('ProductCostingB11-categories.store');
    Route::get('/ProductCostingB11-categories/edit/{id}', [ProductCostingB11Controller::class, 'editCategory'])->name('ProductCostingB11-categories.edit');
    Route::put('/ProductCostingB11-categories/{id}', [ProductCostingB11Controller::class, 'updateCategory'])->name('ProductCostingB11-categories.update');
    Route::delete('/ProductCostingB11-categories/{id}', [ProductCostingB11Controller::class, 'destroyCategory'])->name('ProductCostingB11-categories.destroy');

    Route::get('/ProductCostingB11-permission', [SettingController::class, 'showProductCostingB11PermissionForm'])->name('assignPermissionToBusinessRolesForm');
    Route::post('/ProductCostingB11-permission', [SettingController::class, 'assignPermissionToRoles'])->name('ProductCostingB11.permission');
    Route::post('/ProductCostingB11/lang', [SettingController::class, 'saveTranslations'])->name('ProductCostingB11.lang');
    Route::post('/ProductCostingB11/update-language', [SettingController::class, 'updateLanguage'])->name('ProductCostingB11.update-language');
});