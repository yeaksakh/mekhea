<?php

use Illuminate\Support\Facades\Route;
use Modules\BusinessPlanCanvasB1\Http\Controllers\Api\BusinessPlanCanvasB1Controller;

Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::get('/BusinessPlanCanvasB1-field', [BusinessPlanCanvasB1Controller::class, 'modulefield']);

    Route::get('/BusinessPlanCanvasB1', [BusinessPlanCanvasB1Controller::class, 'index']);
    Route::get('/BusinessPlanCanvasB1/create', [BusinessPlanCanvasB1Controller::class, 'create']);
    Route::post('/BusinessPlanCanvasB1', [BusinessPlanCanvasB1Controller::class, 'store']);
    Route::get('/BusinessPlanCanvasB1/edit/{id}', [BusinessPlanCanvasB1Controller::class, 'edit']);
    Route::put('/BusinessPlanCanvasB1/edit/{id}', [BusinessPlanCanvasB1Controller::class, 'update']);
    Route::delete('/BusinessPlanCanvasB1/delete/{id}', [BusinessPlanCanvasB1Controller::class, 'destroy']);
    
    Route::get('/BusinessPlanCanvasB1-categories', [BusinessPlanCanvasB1Controller::class, 'getCategories']);
    Route::post('/BusinessPlanCanvasB1-categories', [BusinessPlanCanvasB1Controller::class, 'storeCategory']);
    Route::get('/BusinessPlanCanvasB1-categories/edit/{id}', [BusinessPlanCanvasB1Controller::class, 'editCategory']);
    Route::put('/BusinessPlanCanvasB1-categories/{id}', [BusinessPlanCanvasB1Controller::class, 'updateCategory']);
    Route::delete('/BusinessPlanCanvasB1-categories/{id}', [BusinessPlanCanvasB1Controller::class, 'destroyCategory']);

    

});