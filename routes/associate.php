<?php

use App\Http\Controllers\Private\Associate\CommissionController;
use App\Http\Controllers\Private\Associate\MarketingCenterController;
use App\Http\Controllers\Private\Associate\NetworkController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:associate'])
    ->prefix('portal')
    ->name('associate.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Private\Associate\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/commissions', [CommissionController::class, 'index'])->name('commissions');
        Route::get('/network', [NetworkController::class, 'index'])->name('network');
        Route::get('/marketing-center', [MarketingCenterController::class, 'index'])->name('marketing-center');
        Route::resource('offerings', \App\Http\Controllers\Private\Associate\OfferingController::class)->only(['index', 'show']);
        Route::resource('referrals', \App\Http\Controllers\Private\Associate\ReferralController::class)->only(['index', 'create', 'store']);
    });
