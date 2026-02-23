<?php

use Illuminate\Support\Facades\Route;
use Modules\Backup\Http\Controllers\BackupController;
use Modules\Backup\Http\Controllers\SettingController;

Route::middleware('web', 'SetSessionData', 'auth', 'BackupLanguage', 'timezone', 'AdminSidebarMenu')->prefix('backup')->group(function () {
    Route::get('/install', [Modules\Backup\Http\Controllers\InstallController::class, 'index']);
    Route::post('/install', [Modules\Backup\Http\Controllers\InstallController::class, 'install']);
    Route::get('/install/uninstall', [Modules\Backup\Http\Controllers\InstallController::class, 'uninstall']);

    Route::get('/Backup', [Modules\Backup\Http\Controllers\BackupController::class, 'index'])->name('backup.backup.index');
    Route::get('/backup-products', [Modules\Backup\Http\Controllers\BackupController::class, 'backupProducts'])->name('backup.backup-products');
    Route::post('/backup', [Modules\Backup\Http\Controllers\BackupController::class, 'backup'])->name('backup.backup');
    // Route::post('/backup/import', [Modules\Backup\Http\Controllers\BackupController::class, 'import'])->name('backup.import');

    Route::get('/import', [Modules\Backup\Http\Controllers\BackupController::class, 'showForm'])->name('backup.import.form');
    Route::post('/import', [Modules\Backup\Http\Controllers\BackupController::class, 'import'])->name('backup.import.process');

    Route::post('/backup/export', [Modules\Backup\Http\Controllers\BackupController::class, 'export'])->name('backup.export');

    Route::get('backup/download/{filename}', [Modules\Backup\Http\Controllers\BackupController::class, 'download'])->name('backup.backup.download');
    Route::delete('backup/delete/{filename}', [Modules\Backup\Http\Controllers\BackupController::class, 'delete'])->name('backup.backup.delete');
});

Route::get('/Backup/qrcode-show/{id}', [Modules\Backup\Http\Controllers\BackupController::class, 'showQrcodeUrl'])->name('Backup.showQrcodeUrl');
Route::get('/Backup/qrcode-qrcodeView/{id}', [Modules\Backup\Http\Controllers\BackupController::class, 'qrcodeView'])->name('Backup.qrcodeView');
