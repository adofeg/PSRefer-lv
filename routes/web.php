<?php

use App\Http\Controllers\Private\DashboardRedirectController;
use App\Http\Controllers\Private\Shared\NotificationController;
use App\Http\Controllers\Public\PublicApiController;
use App\Http\Controllers\Public\PublicController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
})->name('home');

Route::name('site.')->group(function () {
    Route::get('/users/{id}', [PublicApiController::class, 'userInfo'])->name('users.show');
    Route::get('/offerings/{id}', [PublicApiController::class, 'offeringInfo'])->name('offerings.show');
    Route::post('/leads', [PublicApiController::class, 'submitLead'])->name('leads.store');
    Route::post('/clicks', [PublicApiController::class, 'trackClick'])->name('clicks.store');

    Route::get('/apply/{offeringId}', [PublicController::class, 'showOfferingApplication'])
        ->name('apply');

    // Secure signed route for referrals
    Route::get('/invite/{offeringId}', [PublicController::class, 'showOfferingApplication'])
        ->name('invite')
        ->middleware('signed');

    Route::post('/apply/{offeringId}', [PublicController::class, 'submitOfferingApplication'])
        ->name('apply.submit');
});

// Dashboard redirect logic
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardRedirectController::class)->name('dashboard');
    Route::get('/assets/{fileAsset}/download', \App\Http\Controllers\Private\Shared\FileDownloadController::class)->name('assets.download');

    // Notifications API
    Route::prefix('api')->group(function () {
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
        Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);
    });
});
