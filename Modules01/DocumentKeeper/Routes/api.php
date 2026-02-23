<?php

use Illuminate\Support\Facades\Route;
use Modules\DocumentKeeper\Http\Controllers\Api\DocumentKeeperController;

Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::get('/DocumentKeeper-field', [DocumentKeeperController::class, 'modulefield']);

    Route::get('/DocumentKeeper', [DocumentKeeperController::class, 'index']);
    Route::get('/DocumentKeeper/create', [DocumentKeeperController::class, 'create']);
    Route::post('/DocumentKeeper', [DocumentKeeperController::class, 'store']);
    Route::get('/DocumentKeeper/edit/{id}', [DocumentKeeperController::class, 'edit']);
    Route::put('/DocumentKeeper/edit/{id}', [DocumentKeeperController::class, 'update']);
    Route::delete('/DocumentKeeper/delete/{id}', [DocumentKeeperController::class, 'destroy']);
    
    Route::get('/DocumentKeeper-categories', [DocumentKeeperController::class, 'getCategories']);
    Route::post('/DocumentKeeper-categories', [DocumentKeeperController::class, 'storeCategory']);
    Route::get('/DocumentKeeper-categories/edit/{id}', [DocumentKeeperController::class, 'editCategory']);
    Route::put('/DocumentKeeper-categories/{id}', [DocumentKeeperController::class, 'updateCategory']);
    Route::delete('/DocumentKeeper-categories/{id}', [DocumentKeeperController::class, 'destroyCategory']);

    

});