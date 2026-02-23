<?php

use Illuminate\Support\Facades\Route;
use Modules\ProductDoc\Http\Controllers\Api\ProductDocController;

Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::get('/ProductDoc-field', [ProductDocController::class, 'modulefield']);

    Route::get('/ProductDoc', [ProductDocController::class, 'index']);
    Route::get('/ProductDoc/create', [ProductDocController::class, 'create']);
    Route::post('/ProductDoc', [ProductDocController::class, 'store']);
    Route::get('/ProductDoc/edit/{id}', [ProductDocController::class, 'edit']);
    Route::put('/ProductDoc/edit/{id}', [ProductDocController::class, 'update']);
    Route::delete('/ProductDoc/delete/{id}', [ProductDocController::class, 'destroy']);
    
    Route::get('/ProductDoc-categories', [ProductDocController::class, 'getCategories']);
    Route::post('/ProductDoc-categories', [ProductDocController::class, 'storeCategory']);
    Route::get('/ProductDoc-categories/edit/{id}', [ProductDocController::class, 'editCategory']);
    Route::put('/ProductDoc-categories/{id}', [ProductDocController::class, 'updateCategory']);
    Route::delete('/ProductDoc-categories/{id}', [ProductDocController::class, 'destroyCategory']);

    

});