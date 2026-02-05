<?php

use App\Http\Controllers\Private\Admin\DashboardController;
use App\Http\Controllers\Private\Admin\MarketingController;
use App\Http\Controllers\Private\Admin\OfferingController;
use App\Http\Controllers\Private\Admin\ReferralController;
use App\Http\Controllers\Private\Admin\CategoryController;
use App\Http\Controllers\Private\Admin\SmtpSettingsController;
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

    // Categories
    Route::resource('categories', CategoryController::class);
    
    // Resources
    Route::resource('offerings', OfferingController::class);
    Route::resource('referrals', ReferralController::class);
});
