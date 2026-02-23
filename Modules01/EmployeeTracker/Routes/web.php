<?php

use Illuminate\Support\Facades\Route;
use Modules\EmployeeTracker\Entities\EmployeeTracker;
use Modules\EmployeeTracker\Http\Controllers\ActivityFormController;
use Modules\EmployeeTracker\Http\Controllers\ComponentController;
use Modules\EmployeeTracker\Http\Controllers\EmployeeReportController;
use Modules\EmployeeTracker\Http\Controllers\EmployeeTrackerController;
use Modules\EmployeeTracker\Http\Controllers\PrintController;
use Modules\EmployeeTracker\Http\Controllers\SettingController;

Route::middleware('web', 'SetSessionData', 'auth', 'EmployeeTrackerLanguage', 'timezone', 'AdminSidebarMenu')->prefix('employeetracker')->group(function () {
    Route::get('/install', [Modules\EmployeeTracker\Http\Controllers\InstallController::class, 'index']);
    Route::post('/install', [Modules\EmployeeTracker\Http\Controllers\InstallController::class, 'install']);
    Route::get('/install/uninstall', [Modules\EmployeeTracker\Http\Controllers\InstallController::class, 'uninstall']);


    Route::get('/', [EmployeeTrackerController::class, 'dashboard'])->name('EmployeeTracker.dashboard');
    Route::get('/EmployeeTracker', [EmployeeTrackerController::class, 'index'])->name('EmployeeTracker.index');
    Route::get('/EmployeeTracker/{id}', [EmployeeTrackerController::class, 'show'])->name('EmployeeTracker.show');
    Route::get('/create', [EmployeeTrackerController::class, 'create'])->name('EmployeeTracker.create');
    Route::post('/create', [EmployeeTrackerController::class, 'store'])->name('EmployeeTracker.store');
    Route::get('/edit/{id}', [EmployeeTrackerController::class, 'edit'])->name('EmployeeTracker.edit');
    Route::put('/edit/{id}', [EmployeeTrackerController::class, 'update'])->name('EmployeeTracker.update');
    Route::delete('/delete/{id}', [EmployeeTrackerController::class, 'destroy'])->name('EmployeeTracker.destroy');
    Route::get('/get-users-by-department', [EmployeeTrackerController::class, 'getUsersByDepartment'])->name('getUsersByDepartment');
    Route::get('/get-form-fields-by-department', [EmployeeTrackerController::class, 'getFormFieldsByDepartment'])->name('getFormFieldsByDepartment');
    Route::post('/store', [EmployeeTrackerController::class, 'store'])->name('employeetracker.store');


    

    Route::get('/EmployeeTracker-categories', [EmployeeTrackerController::class, 'getCategories'])->name('EmployeeTracker.getCategories');
    Route::get('/EmployeeTracker-categories/create', [EmployeeTrackerController::class, 'createCategory'])->name('EmployeeTracker-categories.create');
    Route::post('/EmployeeTracker-categories', [EmployeeTrackerController::class, 'storeCategory'])->name('EmployeeTracker-categories.store');
    Route::get('/EmployeeTracker-categories/edit/{id}', [EmployeeTrackerController::class, 'editCategory'])->name('EmployeeTracker-categories.edit');
    Route::put('/EmployeeTracker-categories/{id}', [EmployeeTrackerController::class, 'updateCategory'])->name('EmployeeTracker-categories.update');
    Route::delete('/EmployeeTracker-categories/{id}', [EmployeeTrackerController::class, 'destroyCategory'])->name('EmployeeTracker-categories.destroy');
    Route::get('/EmployeeTracker-permission', [SettingController::class, 'showEmployeeTrackerPermissionForm'])->name('assignPermissionToBusinessRolesForm');
    Route::post('/EmployeeTracker-permission', [SettingController::class, 'assignPermissionToRoles'])->name('EmployeeTracker.permission');
    Route::post('/EmployeeTracker/lang', [SettingController::class, 'saveTranslations'])->name('EmployeeTracker.lang');
    Route::post('/EmployeeTracker/update-language', [SettingController::class, 'updateLanguage'])->name('EmployeeTracker.update-language');
    Route::post('/EmployeeTracker/update-social', [SettingController::class, 'updateSocial'])->name('EmployeeTracker.update-social');

     // the the  report 
    Route::get('/audit-tracking-report', [EmployeeReportController::class, 'getEmployeeWorkTrackingReport'])->name('audit-tracking.report');
    Route::get('/sale-tracking-report', [EmployeeReportController::class, 'getSalesEmployeeWorkTrackingReport'])->name('sale-tracking.report');
    Route::get('/accounting-tracking-report', [EmployeeReportController::class, 'getTechHrEmployeeWorkTrackingReport'])->name('tech-hr-tracking.report');
    Route::get('/franchise-tracking-report', [EmployeeReportController::class, 'getFranchiseEmployeeWorkTrackingReport'])->name('franchise-tracking.report');
    Route::get('/franchise-tracking-report', [EmployeeReportController::class, 'getFranchiseEmployeeWorkTrackingReport'])->name('franchise-tracking.report');

    Route::get('/sale-task-report', [EmployeeReportController::class, 'getSaleReport'])->name('sale-task.report');
    Route::get('/franchise-task-report', [EmployeeReportController::class, 'getFranchiseReport'])->name('franchise-tracking.report');
    Route::get('/accounting-task-report', [EmployeeReportController::class, 'getAccountingReport'])->name('accounting-tracking.report');
    Route::get('/hr-task-report', [EmployeeReportController::class, 'getHrReport'])->name('hr-tracking.report');





    //print the report route 
    Route::get('/employee-report/print', [PrintController::class, 'printEmployeeReport'])->name('employee-tracking.print');




    Route::get('/employee-report-track', [EmployeeReportController::class, 'getSaleEmployeeWorkTrackingReport'])->name('employee-tracking.report');
    Route::view('/test' ,'employeetracker::components.test' );

    // activity form parts
    Route::get('/activity-form', [ActivityFormController::class, 'indexActivityForm'])->name('activity-form.index');
    Route::get('/activity-form/create', [ActivityFormController::class, 'create'])->name('activity-form.create');
    Route::post('/activity-form', [ActivityFormController::class, 'store'])->name('activity-form.store');
    Route::get('/activity-form/fetch', [ActivityFormController::class, 'fetchForms'])->name('activity-form.fetch');
    Route::get('/activity-form/{id}', [ActivityFormController::class, 'show'])->name('activity-form.show');
    
    Route::get('/activity-form/edit/{id}', [ActivityFormController::class, 'edit'])->name('activity-form.edit');
    Route::put('/activity-form/{id}', [ActivityFormController::class, 'update'])->name('activity-form.update');
    Route::delete('/activity-form/{id}', [ActivityFormController::class, 'destroy'])->name('activity-form.destroy');


    Route::get('/get-form-fields/{department_id}', [EmployeeTrackerController::class, 'getFormFields'])->name('get_form_fields');
});

Route::get('/EmployeeTracker/qrcode-show/{id}', [EmployeeTrackerController::class, 'showQrcodeUrl'])->name('EmployeeTracker.showQrcodeUrl');
Route::get('/EmployeeTracker/qrcode-qrcodeView/{id}', [EmployeeTrackerController::class, 'qrcodeView'])->name('EmployeeTracker.qrcodeView');