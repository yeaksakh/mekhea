<?php

use Illuminate\Support\Facades\Route;
use Modules\Visa\Http\Controllers\VisaController;
use Modules\Visa\Http\Controllers\IndicatorController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('web', 'SetSessionData', 'auth', 'timezone', 'AdminSidebarMenu')->prefix('visa')->group(function () {
    Route::get('/visa', [VisaController::class, 'index'])->name('kip.dashboard');

    Route::get('/indicator', [IndicatorController::class, 'index'])->name('visa.indicator.index');
    Route::get('/indicator-create', [IndicatorController::class, 'create'])->name('visa.indicator.create');
    Route::post('/indicator', [IndicatorController::class, 'store'])->name('visa.indicator.store');
    Route::get('/indicator-view/{id}', [IndicatorController::class, 'view'])->name('visa.indicator.view');
    Route::get('/indicator-update/{id}', [IndicatorController::class, 'edit'])->name('visa.indicator.edit');
    Route::put('/indicator/{id}', [IndicatorController::class, 'update'])->name('visa.indicator.update');
    Route::delete('/indicator/{id}', [IndicatorController::class, 'delete'])->name('visa.indicator.delete');

    Route::get('/appraisal', [IndicatorController::class, 'appraisal'])->name('visa.appraisal');
    Route::post('/appraisal', [IndicatorController::class, 'appraisal_store'])->name('visa.appraisal.store');
    Route::get('/appraisal-list', [IndicatorController::class, 'appraisal_list'])->name('visa.appraisal.list');
    Route::get('/appraisal-view/{id}', [IndicatorController::class, 'appraisal_view'])->name('visa.appraisal.view');
    Route::delete('/appraisal/{appraisal_id}', [IndicatorController::class, 'appraisal_delete'])->name('visa.appraisal.delete');

    Route::get('/report', [IndicatorController::class, 'appraisal_report'])->name('visa.appraisal.report');

    Route::get('/give_appraisal/{id}', [IndicatorController::class, 'give_appraisal'])->name('visa.give_appraisal');
    Route::post('/give_appraisal/{id}', [IndicatorController::class, 'store_appraisal'])->name('visa.indicator.store_appraisal');


    // Route::get('/index', [Modules\Visa\Http\Controllers\InstallController::class, 'index']);
    // Route::post('/install', [Modules\Visa\Http\Controllers\InstallController::class, 'install']);
    // Route::get('/install/uninstall', [Modules\Visa\Http\Controllers\InstallController::class, 'uninstall']);
    Route::get('/install', [Modules\Visa\Http\Controllers\InstallController::class, 'index']);
    Route::post('/install', [Modules\Visa\Http\Controllers\InstallController::class, 'install']);
    Route::get('/install/uninstall', [Modules\Visa\Http\Controllers\InstallController::class, 'uninstall']);
    Route::get('/install/update', [Modules\Visa\Http\Controllers\InstallController::class, 'update']);
});