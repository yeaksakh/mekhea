<?php

use Illuminate\Support\Facades\Route;
use Modules\AuditIncome\Http\Controllers\Api\AuditIncomeController;

Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::get('/AuditIncome-field', [AuditIncomeController::class, 'modulefield']);

    Route::get('/AuditIncome', [AuditIncomeController::class, 'index']);
    Route::get('/AuditIncome/create', [AuditIncomeController::class, 'create']);
    Route::post('/AuditIncome', [AuditIncomeController::class, 'store']);
    Route::get('/AuditIncome/edit/{id}', [AuditIncomeController::class, 'edit']);
    Route::put('/AuditIncome/edit/{id}', [AuditIncomeController::class, 'update']);
    Route::delete('/AuditIncome/delete/{id}', [AuditIncomeController::class, 'destroy']);
    
    Route::get('/AuditIncome-categories', [AuditIncomeController::class, 'getCategories']);
    Route::post('/AuditIncome-categories', [AuditIncomeController::class, 'storeCategory']);
    Route::get('/AuditIncome-categories/edit/{id}', [AuditIncomeController::class, 'editCategory']);
    Route::put('/AuditIncome-categories/{id}', [AuditIncomeController::class, 'updateCategory']);
    Route::delete('/AuditIncome-categories/{id}', [AuditIncomeController::class, 'destroyCategory']);

    

});