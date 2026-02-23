<?php

use Illuminate\Support\Facades\Route;
use Modules\SOP\Http\Controllers\Api\SOPController;

Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::get('/SOP-field', [SOPController::class, 'modulefield']);

    Route::get('/SOP', [SOPController::class, 'index']);
    Route::get('/SOP/create', [SOPController::class, 'create']);
    Route::post('/SOP', [SOPController::class, 'store']);
    Route::get('/SOP/edit/{id}', [SOPController::class, 'edit']);
    Route::put('/SOP/edit/{id}', [SOPController::class, 'update']);
    Route::delete('/SOP/delete/{id}', [SOPController::class, 'destroy']);
    
    Route::get('/SOP-categories', [SOPController::class, 'getCategories']);
    Route::post('/SOP-categories', [SOPController::class, 'storeCategory']);
    Route::get('/SOP-categories/edit/{id}', [SOPController::class, 'editCategory']);
    Route::put('/SOP-categories/{id}', [SOPController::class, 'updateCategory']);
    Route::delete('/SOP-categories/{id}', [SOPController::class, 'destroyCategory']);

    

});