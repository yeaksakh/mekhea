<?php

use Illuminate\Support\Facades\Route;
use Modules\SWOT\Http\Controllers\Api\SWOTController;

Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::get('/SWOT-field', [SWOTController::class, 'modulefield']);

    Route::get('/SWOT', [SWOTController::class, 'index']);
    Route::get('/SWOT/create', [SWOTController::class, 'create']);
    Route::post('/SWOT', [SWOTController::class, 'store']);
    Route::get('/SWOT/edit/{id}', [SWOTController::class, 'edit']);
    Route::put('/SWOT/edit/{id}', [SWOTController::class, 'update']);
    Route::delete('/SWOT/delete/{id}', [SWOTController::class, 'destroy']);
    
    Route::get('/SWOT-categories', [SWOTController::class, 'getCategories']);
    Route::post('/SWOT-categories', [SWOTController::class, 'storeCategory']);
    Route::get('/SWOT-categories/edit/{id}', [SWOTController::class, 'editCategory']);
    Route::put('/SWOT-categories/{id}', [SWOTController::class, 'updateCategory']);
    Route::delete('/SWOT-categories/{id}', [SWOTController::class, 'destroyCategory']);

    

});