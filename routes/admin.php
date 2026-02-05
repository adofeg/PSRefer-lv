<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OfferingController;
use App\Http\Controllers\Admin\ReferralController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:admin|psadmin|associate'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Resources
    Route::resource('offerings', OfferingController::class);
    Route::resource('referrals', ReferralController::class);
});
