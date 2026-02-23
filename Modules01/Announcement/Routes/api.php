<?php

use Illuminate\Support\Facades\Route;
use Modules\Announcement\Http\Controllers\Api\AnnouncementController;

Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::get('/Announcement-field', [AnnouncementController::class, 'modulefield']);

    Route::get('/Announcement', [AnnouncementController::class, 'index']);
    Route::get('/Announcement/create', [AnnouncementController::class, 'create']);
    Route::post('/Announcement', [AnnouncementController::class, 'store']);
    Route::get('/Announcement/edit/{id}', [AnnouncementController::class, 'edit']);
    Route::put('/Announcement/edit/{id}', [AnnouncementController::class, 'update']);
    Route::delete('/Announcement/delete/{id}', [AnnouncementController::class, 'destroy']);
    
    Route::get('/Announcement-categories', [AnnouncementController::class, 'getCategories']);
    Route::post('/Announcement-categories', [AnnouncementController::class, 'storeCategory']);
    Route::get('/Announcement-categories/edit/{id}', [AnnouncementController::class, 'editCategory']);
    Route::put('/Announcement-categories/{id}', [AnnouncementController::class, 'updateCategory']);
    Route::delete('/Announcement-categories/{id}', [AnnouncementController::class, 'destroyCategory']);

    

});