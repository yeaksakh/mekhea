<?php

use Illuminate\Support\Facades\Route;
use Modules\SchedulePayment\Http\Controllers\Api\SchedulePaymentController;

Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::get('/SchedulePayment-field', [SchedulePaymentController::class, 'modulefield']);

    Route::get('/SchedulePayment', [SchedulePaymentController::class, 'index']);
    Route::get('/SchedulePayment/create', [SchedulePaymentController::class, 'create']);
    Route::post('/SchedulePayment', [SchedulePaymentController::class, 'store']);
    Route::get('/SchedulePayment/edit/{id}', [SchedulePaymentController::class, 'edit']);
    Route::put('/SchedulePayment/edit/{id}', [SchedulePaymentController::class, 'update']);
    Route::delete('/SchedulePayment/delete/{id}', [SchedulePaymentController::class, 'destroy']);
    
    Route::get('/SchedulePayment-categories', [SchedulePaymentController::class, 'getCategories']);
    Route::post('/SchedulePayment-categories', [SchedulePaymentController::class, 'storeCategory']);
    Route::get('/SchedulePayment-categories/edit/{id}', [SchedulePaymentController::class, 'editCategory']);
    Route::put('/SchedulePayment-categories/{id}', [SchedulePaymentController::class, 'updateCategory']);
    Route::delete('/SchedulePayment-categories/{id}', [SchedulePaymentController::class, 'destroyCategory']);

    

});