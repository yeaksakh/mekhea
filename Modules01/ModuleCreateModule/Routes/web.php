<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Modules\ModuleCreateModule\Http\Controllers\ModuleCreateModuleController;

Route::middleware('web', 'SetSessionData', 'auth', 'language', 'timezone', 'AdminSidebarMenu')->prefix('ModuleCreator')->group(function () {
    Route::get('install', [Modules\ModuleCreateModule\Http\Controllers\InstallController::class, 'index']);
    Route::post('install', [Modules\ModuleCreateModule\Http\Controllers\InstallController::class, 'install']);
    Route::get('/install/uninstall', [Modules\ModuleCreateModule\Http\Controllers\InstallController::class, 'uninstall']);
    Route::post('/install/module', [ModuleCreateModuleController::class, 'install'])->name('modulecreatemodule.install');
    Route::post('/uninstall/module', [ModuleCreateModuleController::class, 'uninstall'])->name('modulecreatemodule.uninstall');
    Route::post('toggle-module', [ModuleCreateModuleController::class, 'toggleModule'])->name('modulecreatemodule.toggle');


   
    Route::get('/', [ModuleCreateModuleController::class, 'index'])->name('modulecreatemodule.index');
    Route::get('/create', [ModuleCreateModuleController::class, 'create'])->name('modulecreatemodule.create');
    Route::post('/store', [ModuleCreateModuleController::class, 'store'])->name('modulecreatemodule.store');
    Route::get('/view/{id}', [ModuleCreateModuleController::class, 'view'])->name('modulecreatemodule.view');
    Route::post('modulecreatemodule/toggle', 'Modules\ModuleCreateModule\Http\Controllers\ModuleCreateModuleController@toggleModule')->name('modulecreatemodule.toggle');
    Route::delete('/delete/{id}', [ModuleCreateModuleController::class, 'destroy'])->name('modulecreatemodule.destroy');
    Route::get('/modulecreatemodule/icons', function () {
        $svg_path = base_path('Modules/ModuleCreateModule/Resources/assets/svgs/');
        $svg_files = File::exists($svg_path) ? File::files($svg_path) : [];
        $svg_files = array_filter($svg_files, function ($file) {
            return strtolower($file->getExtension()) === 'svg';
        });
        $svg_files = array_map(function ($file) {
            return $file->getFilename();
        }, $svg_files);
        return response()->json($svg_files);
    })->name('modulecreatemodule.icons');
});
