<?php

use Illuminate\Support\Facades\Route;
use Modules\CustomerCardB1\Http\Controllers\Api\CustomerCardB1Controller;

Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::get('/CustomerCardB1-field', [CustomerCardB1Controller::class, 'modulefield']);

    Route::get('/CustomerCardB1', [CustomerCardB1Controller::class, 'index']);
    Route::get('/CustomerCardB1/create', [CustomerCardB1Controller::class, 'create']);
    Route::post('/CustomerCardB1', [CustomerCardB1Controller::class, 'store']);
    Route::get('/CustomerCardB1/edit/{id}', [CustomerCardB1Controller::class, 'edit']);
    Route::put('/CustomerCardB1/edit/{id}', [CustomerCardB1Controller::class, 'update']);
    Route::delete('/CustomerCardB1/delete/{id}', [CustomerCardB1Controller::class, 'destroy']);
    
    Route::get('/CustomerCardB1-categories', [CustomerCardB1Controller::class, 'getCategories']);
    Route::post('/CustomerCardB1-categories', [CustomerCardB1Controller::class, 'storeCategory']);
    Route::get('/CustomerCardB1-categories/edit/{id}', [CustomerCardB1Controller::class, 'editCategory']);
    Route::put('/CustomerCardB1-categories/{id}', [CustomerCardB1Controller::class, 'updateCategory']);
    Route::delete('/CustomerCardB1-categories/{id}', [CustomerCardB1Controller::class, 'destroyCategory']);

    

});