<?php

use Illuminate\Support\Facades\Route;
use Modules\Documentary\Http\Controllers\Api\DocumentaryController;

Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::get('/Documentary-field', [DocumentaryController::class, 'modulefield']);

    Route::get('/Documentary', [DocumentaryController::class, 'index']);
    Route::get('/Documentary/create', [DocumentaryController::class, 'create']);
    Route::post('/Documentary', [DocumentaryController::class, 'store']);
    Route::get('/Documentary/edit/{id}', [DocumentaryController::class, 'edit']);
    Route::put('/Documentary/edit/{id}', [DocumentaryController::class, 'update']);
    Route::delete('/Documentary/delete/{id}', [DocumentaryController::class, 'destroy']);
    
    Route::get('/Documentary-categories', [DocumentaryController::class, 'getCategories']);
    Route::post('/Documentary-categories', [DocumentaryController::class, 'storeCategory']);
    Route::get('/Documentary-categories/edit/{id}', [DocumentaryController::class, 'editCategory']);
    Route::put('/Documentary-categories/{id}', [DocumentaryController::class, 'updateCategory']);
    Route::delete('/Documentary-categories/{id}', [DocumentaryController::class, 'destroyCategory']);

    

});