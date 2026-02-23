<?php

use Illuminate\Support\Facades\Route;
use Modules\News\Http\Controllers\Api\NewsController;

Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::get('/News-field', [NewsController::class, 'modulefield']);

    Route::get('/News', [NewsController::class, 'index']);
    Route::get('/News/create', [NewsController::class, 'create']);
    Route::post('/News', [NewsController::class, 'store']);
    Route::get('/News/edit/{id}', [NewsController::class, 'edit']);
    Route::put('/News/edit/{id}', [NewsController::class, 'update']);
    Route::delete('/News/delete/{id}', [NewsController::class, 'destroy']);
    
    Route::get('/News-categories', [NewsController::class, 'getCategories']);
    Route::post('/News-categories', [NewsController::class, 'storeCategory']);
    Route::get('/News-categories/edit/{id}', [NewsController::class, 'editCategory']);
    Route::put('/News-categories/{id}', [NewsController::class, 'updateCategory']);
    Route::delete('/News-categories/{id}', [NewsController::class, 'destroyCategory']);

    

});