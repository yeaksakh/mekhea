<?php

use Illuminate\Support\Facades\Route;
use Modules\ModuleCreateModule\Http\Controllers\Api\ApiModuleController;

Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::get('/Module', [ApiModuleController::class, 'index']);
});