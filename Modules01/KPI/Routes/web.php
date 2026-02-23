<?php

use Illuminate\Support\Facades\Route;
use Modules\KPI\Http\Controllers\KPIController;
use Modules\KPI\Http\Controllers\IndicatorController;

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

Route::middleware('web', 'SetSessionData', 'auth', 'timezone', 'AdminSidebarMenu')->prefix('kpi')->group(function () {
    Route::get('/kpi', [KPIController::class, 'index'])->name('kip.dashboard');

    Route::get('/indicator', action: [IndicatorController::class, 'index'])->name('indicator.index');
    Route::get('/indicator-create', [IndicatorController::class, 'create'])->name('indicator.create');
    Route::post('/indicator', [IndicatorController::class, 'store'])->name('indicator.store');
    Route::get('/indicator-view/{id}', [IndicatorController::class, 'view'])->name('indicator.view');
    Route::get('/indicator-update/{id}', [IndicatorController::class, 'edit'])->name('indicator.edit');
    Route::put('/indicator/{id}', [IndicatorController::class, 'update'])->name('indicator.update');
    Route::delete('/indicator/{id}', [IndicatorController::class, 'delete'])->name('indicator.delete');

    Route::get('/appraisal', [IndicatorController::class, 'appraisal'])->name('appraisal');
    Route::post('/appraisal', [IndicatorController::class, 'appraisal_store'])->name('appraisal.store');
    Route::get('/appraisal-list', [IndicatorController::class, 'appraisal_list'])->name('appraisal.list');
    Route::get('/appraisal-view/{id}', [IndicatorController::class, 'appraisal_view'])->name('appraisal.view');
    Route::delete('/appraisal/{appraisal_id}', [IndicatorController::class, 'appraisal_delete'])->name('appraisal.delete');

    Route::get('/report', [IndicatorController::class, 'appraisal_report'])->name('appraisal.report');

    Route::get('/give_appraisal/{id}', [IndicatorController::class, 'give_appraisal'])->name('give_appraisal');
    Route::post('/give_appraisal/{id}', [IndicatorController::class, 'store_appraisal'])->name('indicator.store_appraisal');


    // Route::get('/index', [Modules\KPI\Http\Controllers\InstallController::class, 'index']);
    // Route::post('/install', [Modules\KPI\Http\Controllers\InstallController::class, 'install']);
    // Route::get('/install/uninstall', [Modules\KPI\Http\Controllers\InstallController::class, 'uninstall']);
    Route::get('/install', [Modules\KPI\Http\Controllers\InstallController::class, 'index']);
    Route::post('/install', [Modules\KPI\Http\Controllers\InstallController::class, 'install']);
    Route::get('/install/uninstall', [Modules\KPI\Http\Controllers\InstallController::class, 'uninstall']);
    Route::get('/install/update', [Modules\KPI\Http\Controllers\InstallController::class, 'update']);
});