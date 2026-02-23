<?php

use Illuminate\Support\Facades\Route;
use Modules\AuditExpense\Http\Controllers\Api\AuditExpenseController;

Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::get('/AuditExpense-field', [AuditExpenseController::class, 'modulefield']);

    Route::get('/AuditExpense', [AuditExpenseController::class, 'index']);
    Route::get('/AuditExpense/create', [AuditExpenseController::class, 'create']);
    Route::post('/AuditExpense', [AuditExpenseController::class, 'store']);
    Route::get('/AuditExpense/edit/{id}', [AuditExpenseController::class, 'edit']);
    Route::put('/AuditExpense/edit/{id}', [AuditExpenseController::class, 'update']);
    Route::delete('/AuditExpense/delete/{id}', [AuditExpenseController::class, 'destroy']);
    
    Route::get('/AuditExpense-categories', [AuditExpenseController::class, 'getCategories']);
    Route::post('/AuditExpense-categories', [AuditExpenseController::class, 'storeCategory']);
    Route::get('/AuditExpense-categories/edit/{id}', [AuditExpenseController::class, 'editCategory']);
    Route::put('/AuditExpense-categories/{id}', [AuditExpenseController::class, 'updateCategory']);
    Route::delete('/AuditExpense-categories/{id}', [AuditExpenseController::class, 'destroyCategory']);

    

});