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

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    // Analytics
    Route::get('/analytics/clicks', [AnalyticsController::class, 'clicks'])->name('api.analytics.clicks');
    Route::get('/analytics/revenue', [AnalyticsController::class, 'revenue'])->name('api.analytics.revenue');

    // Commission Overrides
    Route::get('/commissions/overrides', [CommissionOverrideController::class, 'index'])->name('api.commissions.overrides.index');
    Route::post('/commissions/overrides', [CommissionOverrideController::class, 'store'])->name('api.commissions.overrides.store');
    Route::delete('/commissions/overrides/{override}', [CommissionOverrideController::class, 'destroy'])->name('api.commissions.overrides.destroy');
});

// If using session auth instead of sanctum for internal use, change middleware to 'web', 'auth' or similar.
// But standard API usually implies tokens. The user mentioned "internal api" before.
// Since we are using Inertia, session auth is already there if we hit it from browser.
// However, the `api` middleware group is stateless by default.
// If this is for the Dashboard charts (internal), it should probably use 'web' middleware for session sharing.
// But the user asked for `api.php`.
// Laravel 11 `api` routes use `api` middleware group.
// Let's stick to standard `auth:sanctum` which works with SPA (session) if configured, or just change middleware manually in bootstrap if needed.
// For now, I'll use `auth:sanctum` which is the default for Laravel API routes.
