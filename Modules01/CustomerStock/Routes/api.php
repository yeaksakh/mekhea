<?php

use Illuminate\Support\Facades\Route;
use Modules\CustomerStock\Http\Controllers\Api\CustomerStockController;

Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::get('/CustomerStock-field', [CustomerStockController::class, 'modulefield']);

    Route::get('/CustomerStock', [CustomerStockController::class, 'index']);
    Route::get('/CustomerStock/create', [CustomerStockController::class, 'create']);
    Route::post('/CustomerStock', [CustomerStockController::class, 'store']);
    Route::get('/CustomerStock/edit/{id}', [CustomerStockController::class, 'edit']);
    Route::put('/CustomerStock/edit/{id}', [CustomerStockController::class, 'update']);
    Route::delete('/CustomerStock/delete/{id}', [CustomerStockController::class, 'destroy']);
    
    Route::get('/CustomerStock-categories', [CustomerStockController::class, 'getCategories']);
    Route::post('/CustomerStock-categories', [CustomerStockController::class, 'storeCategory']);
    Route::get('/CustomerStock-categories/edit/{id}', [CustomerStockController::class, 'editCategory']);
    Route::put('/CustomerStock-categories/{id}', [CustomerStockController::class, 'updateCategory']);
    Route::delete('/CustomerStock-categories/{id}', [CustomerStockController::class, 'destroyCategory']);

    

});