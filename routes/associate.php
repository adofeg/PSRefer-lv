<?php

use App\Http\Controllers\Private\Associate\CommissionController;
use App\Http\Controllers\Private\Associate\MarketingCenterController;
use App\Http\Controllers\Private\Associate\NetworkController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/commissions', [CommissionController::class, 'index'])->name('commissions');
        Route::get('/network', [NetworkController::class, 'index'])->name('network');
        Route::get('/marketing-center', [MarketingCenterController::class, 'index'])->name('marketing-center');
    });
