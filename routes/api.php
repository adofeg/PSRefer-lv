<?php

use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\CommissionOverrideController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['web', 'auth', 'verified'])->group(function () {
    // Analytics
    Route::get('/analytics/clicks', [AnalyticsController::class, 'clicks'])->name('api.analytics.clicks');
    Route::get('/analytics/revenue', [AnalyticsController::class, 'revenue'])->name('api.analytics.revenue');

    // Commission Overrides
    Route::get('/commissions/overrides', [CommissionOverrideController::class, 'index'])->name('api.commissions.overrides.index');
    Route::post('/commissions/overrides', [CommissionOverrideController::class, 'store'])->name('api.commissions.overrides.store');
    Route::delete('/commissions/overrides/{override}', [CommissionOverrideController::class, 'destroy'])->name('api.commissions.overrides.destroy');
});

// Internal dashboard API uses session auth; keep `web` middleware for sessions.
