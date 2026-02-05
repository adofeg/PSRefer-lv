<?php

use App\Http\Controllers\Private\Admin\DashboardController;
use App\Http\Controllers\Private\Admin\MarketingController;
use App\Http\Controllers\Private\Admin\OfferingController;
use App\Http\Controllers\Private\Admin\ReferralController;
use App\Http\Controllers\Private\Admin\CategoryController;
use App\Http\Controllers\Private\Admin\CommissionOverrideController;
use App\Http\Controllers\Private\Admin\SmtpSettingsController;
use App\Http\Controllers\Private\Admin\UserController;
use App\Enums\RoleName;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:' . implode('|', RoleName::adminOrAssociate())])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Referrals - Kanban Pipeline
    Route::get('/referrals/pipeline', [ReferralController::class, 'pipeline'])->name('referrals.pipeline');
    
    // Marketing Center
    Route::get('/marketing', [MarketingController::class, 'index'])->name('marketing');
    
    // Settings
    Route::get('/settings/smtp', [SmtpSettingsController::class, 'smtp'])->name('settings.smtp');
    Route::post('/settings/smtp', [SmtpSettingsController::class, 'updateSmtp'])->name('settings.smtp.update');
    Route::post('/settings/smtp/test', [SmtpSettingsController::class, 'testSmtp'])->name('settings.smtp.test');

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::patch('/users/{user}/status', [UserController::class, 'toggleStatus'])->name('users.status');

    // Commission Overrides
    Route::get('/commissions/overrides', [CommissionOverrideController::class, 'index'])->name('commissions.overrides.index');
    Route::post('/commissions/overrides', [CommissionOverrideController::class, 'store'])->name('commissions.overrides.store');
    Route::delete('/commissions/overrides/{override}', [CommissionOverrideController::class, 'destroy'])->name('commissions.overrides.destroy');

    // Categories
    Route::resource('categories', CategoryController::class);
    
    // Resources
    Route::resource('offerings', OfferingController::class);
    Route::resource('referrals', ReferralController::class);
});
