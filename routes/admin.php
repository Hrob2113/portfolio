<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TranslationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/translations', [TranslationController::class, 'index'])->name('translations.index');
    Route::get('/translations/{group}', [TranslationController::class, 'edit'])->name('translations.edit');
    Route::put('/translations/{group}', [TranslationController::class, 'update'])->name('translations.update');

    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{message}', [MessageController::class, 'show'])->name('messages.show');
    Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
    Route::patch('/messages/{message}/read', [MessageController::class, 'markAsRead'])->name('messages.read');
});
