<?php

use Illuminate\Support\Facades\Route;
use Modules\EmployeeCardB1\Http\Controllers\Api\EmployeeCardB1Controller;

Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::get('/EmployeeCardB1-field', [EmployeeCardB1Controller::class, 'modulefield']);

    Route::get('/EmployeeCardB1', [EmployeeCardB1Controller::class, 'index']);
    Route::get('/EmployeeCardB1/create', [EmployeeCardB1Controller::class, 'create']);
    Route::post('/EmployeeCardB1', [EmployeeCardB1Controller::class, 'store']);
    Route::get('/EmployeeCardB1/edit/{id}', [EmployeeCardB1Controller::class, 'edit']);
    Route::put('/EmployeeCardB1/edit/{id}', [EmployeeCardB1Controller::class, 'update']);
    Route::delete('/EmployeeCardB1/delete/{id}', [EmployeeCardB1Controller::class, 'destroy']);
    
    Route::get('/EmployeeCardB1-categories', [EmployeeCardB1Controller::class, 'getCategories']);
    Route::post('/EmployeeCardB1-categories', [EmployeeCardB1Controller::class, 'storeCategory']);
    Route::get('/EmployeeCardB1-categories/edit/{id}', [EmployeeCardB1Controller::class, 'editCategory']);
    Route::put('/EmployeeCardB1-categories/{id}', [EmployeeCardB1Controller::class, 'updateCategory']);
    Route::delete('/EmployeeCardB1-categories/{id}', [EmployeeCardB1Controller::class, 'destroyCategory']);

    

});