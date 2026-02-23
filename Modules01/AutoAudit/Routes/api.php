<?php

use Illuminate\Support\Facades\Route;
use Modules\AutoAudit\Http\Controllers\Api\AutoAuditController;

Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::get('/AutoAudit-field', [AutoAuditController::class, 'modulefield']);

    Route::get('/AutoAudit', [AutoAuditController::class, 'index']);
    Route::get('/AutoAudit/create', [AutoAuditController::class, 'create']);
    Route::post('/AutoAudit', [AutoAuditController::class, 'store']);
    Route::get('/AutoAudit/edit/{id}', [AutoAuditController::class, 'edit']);
    Route::put('/AutoAudit/edit/{id}', [AutoAuditController::class, 'update']);
    Route::delete('/AutoAudit/delete/{id}', [AutoAuditController::class, 'destroy']);
    
    Route::get('/AutoAudit-categories', [AutoAuditController::class, 'getCategories']);
    Route::post('/AutoAudit-categories', [AutoAuditController::class, 'storeCategory']);
    Route::get('/AutoAudit-categories/edit/{id}', [AutoAuditController::class, 'editCategory']);
    Route::put('/AutoAudit-categories/{id}', [AutoAuditController::class, 'updateCategory']);
    Route::delete('/AutoAudit-categories/{id}', [AutoAuditController::class, 'destroyCategory']);

    

});