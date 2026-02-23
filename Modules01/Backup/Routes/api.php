<?php

use Illuminate\Support\Facades\Route;
use Modules\Backup\Http\Controllers\Api\BackupController;

Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::get('/Backup-field', [BackupController::class, 'modulefield']);

    Route::get('/Backup', [BackupController::class, 'index']);
    Route::get('/Backup/create', [BackupController::class, 'create']);
    Route::post('/Backup', [BackupController::class, 'store']);
    Route::get('/Backup/edit/{id}', [BackupController::class, 'edit']);
    Route::put('/Backup/edit/{id}', [BackupController::class, 'update']);
    Route::delete('/Backup/delete/{id}', [BackupController::class, 'destroy']);
    
    Route::get('/Backup-categories', [BackupController::class, 'getCategories']);
    Route::post('/Backup-categories', [BackupController::class, 'storeCategory']);
    Route::get('/Backup-categories/edit/{id}', [BackupController::class, 'editCategory']);
    Route::put('/Backup-categories/{id}', [BackupController::class, 'updateCategory']);
    Route::delete('/Backup-categories/{id}', [BackupController::class, 'destroyCategory']);

    

});