<?php

use Illuminate\Support\Facades\Route;
use Modules\Announcement\Http\Controllers\AnnouncementController;
use Modules\Announcement\Http\Controllers\SettingController;

Route::middleware('web', 'SetSessionData', 'auth', 'AnnouncementLanguage', 'timezone', 'AdminSidebarMenu')->prefix('announcement')->group(function () {
    Route::get('/install', [Modules\Announcement\Http\Controllers\InstallController::class, 'index']);
    Route::post('/install', [Modules\Announcement\Http\Controllers\InstallController::class, 'install']);
    Route::get('/install/uninstall', [Modules\Announcement\Http\Controllers\InstallController::class, 'uninstall']);


    Route::get('/', [AnnouncementController::class, 'dashboard'])->name('Announcement.dashboard');
    Route::get('/Announcement', [AnnouncementController::class, 'index'])->name('Announcement.index');
    Route::get('/Announcement/{id}', [AnnouncementController::class, 'show'])->name('Announcement.show');
    Route::get('/create', [AnnouncementController::class, 'create'])->name('Announcement.create');
    Route::post('/create', [AnnouncementController::class, 'store'])->name('Announcement.store');
    Route::get('/edit/{id}', [AnnouncementController::class, 'edit'])->name('Announcement.edit');
    Route::put('/edit/{id}', [AnnouncementController::class, 'update'])->name('Announcement.update');
    Route::delete('/delete/{id}', [AnnouncementController::class, 'destroy'])->name('Announcement.destroy');

    

    Route::get('/Announcement-categories', [AnnouncementController::class, 'getCategories'])->name('Announcement.getCategories');
    Route::get('/Announcement-categories/create', [AnnouncementController::class, 'createCategory'])->name('Announcement-categories.create');
    Route::post('/Announcement-categories', [AnnouncementController::class, 'storeCategory'])->name('Announcement-categories.store');
    Route::get('/Announcement-categories/edit/{id}', [AnnouncementController::class, 'editCategory'])->name('Announcement-categories.edit');
    Route::put('/Announcement-categories/{id}', [AnnouncementController::class, 'updateCategory'])->name('Announcement-categories.update');
    Route::delete('/Announcement-categories/{id}', [AnnouncementController::class, 'destroyCategory'])->name('Announcement-categories.destroy');

    Route::get('/Announcement-permission', [SettingController::class, 'showAnnouncementPermissionForm'])->name('assignPermissionToBusinessRolesForm');
    Route::post('/Announcement-permission', [SettingController::class, 'assignPermissionToRoles'])->name('Announcement.permission');
    Route::post('/Announcement/lang', [SettingController::class, 'saveTranslations'])->name('Announcement.lang');
    Route::post('/Announcement/update-language', [SettingController::class, 'updateLanguage'])->name('Announcement.update-language');
    Route::post('/Announcement/update-social', [SettingController::class, 'updateSocial'])->name('Announcement.update-social');
});

Route::get('/Announcement/qrcode-show/{id}', [AnnouncementController::class, 'showQrcodeUrl'])->name('Announcement.showQrcodeUrl');
Route::get('/Announcement/qrcode-qrcodeView/{id}', [AnnouncementController::class, 'qrcodeView'])->name('Announcement.qrcodeView');