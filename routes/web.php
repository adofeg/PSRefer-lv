<?php

use Inertia\Inertia;

// Public Welcome Route
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('home');

// Dashboard Redirect Logic
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();

        // Check Roles (using Spatie)
        // Admin, PSAdmin, Associate -> Admin Dashboard
        if ($user->hasRole(['admin', 'psadmin', 'associate'])) {
            return redirect()->route('admin.dashboard');
        }

        // Future: Client Dashboard?
        // if ($user->hasRole('client')) { return redirect()->route('client.dashboard'); }

        return redirect()->route('home');
    })->name('dashboard');
});
