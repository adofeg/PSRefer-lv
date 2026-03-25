<?php

use App\Http\Controllers\Api\AnalyticsController;
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
});

// Internal dashboard API uses session auth; keep `web` middleware for sessions.
