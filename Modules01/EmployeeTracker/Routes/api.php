<?php

use Illuminate\Support\Facades\Route;
use Modules\EmployeeTracker\Http\Controllers\Api\EmployeeTrackerController;

Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::get('/EmployeeTracker-field', [EmployeeTrackerController::class, 'modulefield']);

    Route::get('/EmployeeTracker', [EmployeeTrackerController::class, 'index']);
    Route::get('/EmployeeTracker/create', [EmployeeTrackerController::class, 'create']);
    Route::post('/EmployeeTracker', [EmployeeTrackerController::class, 'store']);
    Route::get('/EmployeeTracker/edit/{id}', [EmployeeTrackerController::class, 'edit']);
    Route::put('/EmployeeTracker/edit/{id}', [EmployeeTrackerController::class, 'update']);
    Route::delete('/EmployeeTracker/delete/{id}', [EmployeeTrackerController::class, 'destroy']);
    
    Route::get('/EmployeeTracker-categories', [EmployeeTrackerController::class, 'getCategories']);
    Route::post('/EmployeeTracker-categories', [EmployeeTrackerController::class, 'storeCategory']);
    Route::get('/EmployeeTracker-categories/edit/{id}', [EmployeeTrackerController::class, 'editCategory']);
    Route::put('/EmployeeTracker-categories/{id}', [EmployeeTrackerController::class, 'updateCategory']);
    Route::delete('/EmployeeTracker-categories/{id}', [EmployeeTrackerController::class, 'destroyCategory']);

    

});