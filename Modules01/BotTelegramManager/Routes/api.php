<?php

use Illuminate\Support\Facades\Route;
use Modules\BotTelegramManager\Http\Controllers\Api\BotTelegramManagerController;

Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::get('/BotTelegramManager-field', [BotTelegramManagerController::class, 'modulefield']);

    Route::get('/BotTelegramManager', [BotTelegramManagerController::class, 'index']);
    Route::get('/BotTelegramManager/create', [BotTelegramManagerController::class, 'create']);
    Route::post('/BotTelegramManager', [BotTelegramManagerController::class, 'store']);
    Route::get('/BotTelegramManager/edit/{id}', [BotTelegramManagerController::class, 'edit']);
    Route::put('/BotTelegramManager/edit/{id}', [BotTelegramManagerController::class, 'update']);
    Route::delete('/BotTelegramManager/delete/{id}', [BotTelegramManagerController::class, 'destroy']);
    
    Route::get('/BotTelegramManager-categories', [BotTelegramManagerController::class, 'getCategories']);
    Route::post('/BotTelegramManager-categories', [BotTelegramManagerController::class, 'storeCategory']);
    Route::get('/BotTelegramManager-categories/edit/{id}', [BotTelegramManagerController::class, 'editCategory']);
    Route::put('/BotTelegramManager-categories/{id}', [BotTelegramManagerController::class, 'updateCategory']);
    Route::delete('/BotTelegramManager-categories/{id}', [BotTelegramManagerController::class, 'destroyCategory']);

    

});