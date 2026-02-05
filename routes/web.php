<?php

use App\Http\Controllers\Private\DashboardRedirectController;
use App\Http\Controllers\Public\PublicController;
use Illuminate\Support\Facades\Route;

// Public Welcome Route
Route::get('/', [PublicController::class, 'home'])->name('home');

// Public Offering Application Routes (No Auth Required)
Route::prefix('public')->name('public.')->group(function () {
    Route::get('/apply/{offeringId}', [PublicController::class, 'showOfferingApplication'])
        ->name('apply');
    Route::post('/apply/{offeringId}', [PublicController::class, 'submitOfferingApplication'])
        ->name('apply.submit');
});

// Dashboard Redirect Logic
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardRedirectController::class)->name('dashboard');
});
