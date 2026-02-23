<?php

use Illuminate\Support\Facades\Route;
use Modules\PurchaseAutoFill\Http\Controllers\Api\PurchaseAutoFillController;

Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::get('/PurchaseAutoFill-field', [PurchaseAutoFillController::class, 'modulefield']);

    Route::get('/PurchaseAutoFill', [PurchaseAutoFillController::class, 'index']);
    Route::get('/PurchaseAutoFill/create', [PurchaseAutoFillController::class, 'create']);
    Route::post('/PurchaseAutoFill', [PurchaseAutoFillController::class, 'store']);
    Route::get('/PurchaseAutoFill/edit/{id}', [PurchaseAutoFillController::class, 'edit']);
    Route::put('/PurchaseAutoFill/edit/{id}', [PurchaseAutoFillController::class, 'update']);
    Route::delete('/PurchaseAutoFill/delete/{id}', [PurchaseAutoFillController::class, 'destroy']);
    
    Route::get('/PurchaseAutoFill-categories', [PurchaseAutoFillController::class, 'getCategories']);
    Route::post('/PurchaseAutoFill-categories', [PurchaseAutoFillController::class, 'storeCategory']);
    Route::get('/PurchaseAutoFill-categories/edit/{id}', [PurchaseAutoFillController::class, 'editCategory']);
    Route::put('/PurchaseAutoFill-categories/{id}', [PurchaseAutoFillController::class, 'updateCategory']);
    Route::delete('/PurchaseAutoFill-categories/{id}', [PurchaseAutoFillController::class, 'destroyCategory']);

    

});