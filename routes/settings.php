<?php

use App\Http\Controllers\Private\Settings\AppearanceController;
use App\Http\Controllers\Private\Settings\PasswordController;
use App\Http\Controllers\Private\Settings\ProfileController;
use App\Http\Controllers\Private\Settings\TwoFactorAuthenticationController;
use App\Http\Controllers\Private\Settings\W9DocumentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/settings', [ProfileController::class, 'edit'])->name('settings');
        Route::put('/settings', [ProfileController::class, 'update'])->name('settings.update');
        Route::delete('/settings', [ProfileController::class, 'destroy'])->name('settings.destroy');

        Route::get('/settings/password', [PasswordController::class, 'edit'])->name('settings.password.edit');
        Route::post('/settings/password', [PasswordController::class, 'update'])->name('settings.password');

        Route::get('/settings/appearance', AppearanceController::class)->name('settings.appearance');
        Route::get('/settings/two-factor', [TwoFactorAuthenticationController::class, 'show'])->name('settings.two-factor');
        Route::get('/settings/w9', W9DocumentController::class)->name('settings.w9');
    });
