<?php

use Illuminate\Support\Facades\Route;
use Modules\ProductCostingB11\Http\Controllers\Api\ProductCostingB11Controller;

Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::get('/ProductCostingB11-field', [ProductCostingB11Controller::class, 'modulefield']);

    Route::get('/ProductCostingB11', [ProductCostingB11Controller::class, 'index']);
    Route::get('/ProductCostingB11/create', [ProductCostingB11Controller::class, 'create']);
    Route::post('/ProductCostingB11', [ProductCostingB11Controller::class, 'store']);
    Route::get('/ProductCostingB11/edit/{id}', [ProductCostingB11Controller::class, 'edit']);
    Route::put('/ProductCostingB11/edit/{id}', [ProductCostingB11Controller::class, 'update']);
    Route::delete('/ProductCostingB11/delete/{id}', [ProductCostingB11Controller::class, 'destroy']);
    
    Route::get('/ProductCostingB11-categories', [ProductCostingB11Controller::class, 'getCategories']);
    Route::post('/ProductCostingB11-categories', [ProductCostingB11Controller::class, 'storeCategory']);
    Route::get('/ProductCostingB11-categories/edit/{id}', [ProductCostingB11Controller::class, 'editCategory']);
    Route::put('/ProductCostingB11-categories/{id}', [ProductCostingB11Controller::class, 'updateCategory']);
    Route::delete('/ProductCostingB11-categories/{id}', [ProductCostingB11Controller::class, 'destroyCategory']);

    

});