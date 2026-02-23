<?php

use Illuminate\Support\Facades\Route;
use Modules\ExpenseRequest\Http\Controllers\Api\ExpenseRequestController;

Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::get('/ExpenseRequest-field', [ExpenseRequestController::class, 'modulefield']);

    Route::get('/ExpenseRequest', [ExpenseRequestController::class, 'index']);
    Route::get('/ExpenseRequest/create', [ExpenseRequestController::class, 'create']);
    Route::post('/ExpenseRequest', [ExpenseRequestController::class, 'store']);
    Route::get('/ExpenseRequest/edit/{id}', [ExpenseRequestController::class, 'edit']);
    Route::put('/ExpenseRequest/edit/{id}', [ExpenseRequestController::class, 'update']);
    Route::delete('/ExpenseRequest/delete/{id}', [ExpenseRequestController::class, 'destroy']);
    
    Route::get('/ExpenseRequest-categories', [ExpenseRequestController::class, 'getCategories']);
    Route::post('/ExpenseRequest-categories', [ExpenseRequestController::class, 'storeCategory']);
    Route::get('/ExpenseRequest-categories/edit/{id}', [ExpenseRequestController::class, 'editCategory']);
    Route::put('/ExpenseRequest-categories/{id}', [ExpenseRequestController::class, 'updateCategory']);
    Route::delete('/ExpenseRequest-categories/{id}', [ExpenseRequestController::class, 'destroyCategory']);

    

});