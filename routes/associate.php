<?php

use App\Http\Controllers\Private\Associate\CommissionController;
use App\Http\Controllers\Private\Associate\DashboardController;
use App\Http\Controllers\Private\Associate\OfferingController;
use App\Http\Controllers\Private\Associate\ReferralController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:associate'])
    ->prefix('associate')
    ->name('associate.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/commissions', [CommissionController::class, 'index'])->name('commissions');

        Route::resource('offerings', OfferingController::class)->only(['index', 'show']);
        Route::resource('referrals', ReferralController::class)->only(['index', 'create', 'store']);
    });
