<?php

use Illuminate\Support\Facades\Route;
use Modules\AiStudio\Http\Controllers\Api\AiStudioController;

Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::get('/AiStudio-field', [AiStudioController::class, 'modulefield']);

    Route::get('/AiStudio', [AiStudioController::class, 'index']);
    Route::get('/AiStudio/create', [AiStudioController::class, 'create']);
    Route::post('/AiStudio', [AiStudioController::class, 'store']);
    Route::get('/AiStudio/edit/{id}', [AiStudioController::class, 'edit']);
    Route::put('/AiStudio/edit/{id}', [AiStudioController::class, 'update']);
    Route::delete('/AiStudio/delete/{id}', [AiStudioController::class, 'destroy']);
    
    Route::get('/AiStudio-categories', [AiStudioController::class, 'getCategories']);
    Route::post('/AiStudio-categories', [AiStudioController::class, 'storeCategory']);
    Route::get('/AiStudio-categories/edit/{id}', [AiStudioController::class, 'editCategory']);
    Route::put('/AiStudio-categories/{id}', [AiStudioController::class, 'updateCategory']);
    Route::delete('/AiStudio-categories/{id}', [AiStudioController::class, 'destroyCategory']);

    

});