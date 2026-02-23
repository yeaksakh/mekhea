<?php

use Illuminate\Support\Facades\Route;
use Modules\ProductBook\Http\Controllers\Api\ProductBookController;

Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::get('/ProductBook-field', [ProductBookController::class, 'modulefield']);

    Route::get('/ProductBook', [ProductBookController::class, 'index']);
    Route::get('/ProductBook/create', [ProductBookController::class, 'create']);
    Route::post('/ProductBook', [ProductBookController::class, 'store']);
    Route::get('/ProductBook/edit/{id}', [ProductBookController::class, 'edit']);
    Route::put('/ProductBook/edit/{id}', [ProductBookController::class, 'update']);
    Route::delete('/ProductBook/delete/{id}', [ProductBookController::class, 'destroy']);
    
    Route::get('/ProductBook-categories', [ProductBookController::class, 'getCategories']);
    Route::post('/ProductBook-categories', [ProductBookController::class, 'storeCategory']);
    Route::get('/ProductBook-categories/edit/{id}', [ProductBookController::class, 'editCategory']);
    Route::put('/ProductBook-categories/{id}', [ProductBookController::class, 'updateCategory']);
    Route::delete('/ProductBook-categories/{id}', [ProductBookController::class, 'destroyCategory']);

    

});