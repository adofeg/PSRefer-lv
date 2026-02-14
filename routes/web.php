<?php

use App\Http\Controllers\Private\DashboardRedirectController;
use App\Http\Controllers\Public\PublicApiController;
use App\Http\Controllers\Public\PublicController;
use Illuminate\Support\Facades\Route;

// Guest-facing welcome route
Route::get('/', [PublicController::class, 'home'])->name('home');

// Guest-facing routes (no auth required)
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
});
