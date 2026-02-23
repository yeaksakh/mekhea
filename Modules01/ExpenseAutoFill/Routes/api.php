<?php

use Illuminate\Support\Facades\Route;
use Modules\ExpenseAutoFill\Http\Controllers\Api\ExpenseAutoFillController;

Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::get('/ExpenseAutoFill-field', [ExpenseAutoFillController::class, 'modulefield']);

    Route::get('/ExpenseAutoFill', [ExpenseAutoFillController::class, 'index']);
    Route::get('/ExpenseAutoFill/create', [ExpenseAutoFillController::class, 'create']);
    Route::post('/ExpenseAutoFill', [ExpenseAutoFillController::class, 'store']);
    Route::get('/ExpenseAutoFill/edit/{id}', [ExpenseAutoFillController::class, 'edit']);
    Route::put('/ExpenseAutoFill/edit/{id}', [ExpenseAutoFillController::class, 'update']);
    Route::delete('/ExpenseAutoFill/delete/{id}', [ExpenseAutoFillController::class, 'destroy']);
    
    Route::get('/ExpenseAutoFill-categories', [ExpenseAutoFillController::class, 'getCategories']);
    Route::post('/ExpenseAutoFill-categories', [ExpenseAutoFillController::class, 'storeCategory']);
    Route::get('/ExpenseAutoFill-categories/edit/{id}', [ExpenseAutoFillController::class, 'editCategory']);
    Route::put('/ExpenseAutoFill-categories/{id}', [ExpenseAutoFillController::class, 'updateCategory']);
    Route::delete('/ExpenseAutoFill-categories/{id}', [ExpenseAutoFillController::class, 'destroyCategory']);

    

});