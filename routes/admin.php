<?php

use App\Http\Controllers\Private\Admin\DashboardController;
use App\Http\Controllers\Private\Admin\MarketingController;
use App\Http\Controllers\Private\Admin\OfferingController;
use App\Http\Controllers\Private\Admin\ReferralController;
use App\Http\Controllers\Private\Admin\CategoryController;
use App\Http\Controllers\Private\Admin\CommissionController;
use App\Http\Controllers\Private\Admin\CommissionOverrideController;
use App\Http\Controllers\Private\Admin\SmtpSettingsController;
use App\Http\Controllers\Private\Admin\UserController;
use App\Http\Controllers\Private\Admin\AuditController;
use App\Enums\RoleName;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:' . implode('|', RoleName::adminOrPsAdmin())])
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
    
    // Audit Logs
    Route::get('/audit-logs', [AuditController::class, 'index'])->name('audit-logs.index');

    // Users
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

    // Commission Overrides
    Route::get('/commissions', [CommissionController::class, 'index'])->name('commissions.index');
    Route::get('/commissions/create', [CommissionController::class, 'create'])->name('commissions.create');
    Route::get('/commissions/report', [CommissionController::class, 'report'])->name('commissions.report');
    Route::post('/commissions', [CommissionController::class, 'store'])->name('commissions.store');
    Route::get('/commissions/{commission}/edit', [CommissionController::class, 'edit'])->name('commissions.edit');
    Route::put('/commissions/{commission}', [CommissionController::class, 'update'])->name('commissions.update');
    Route::delete('/commissions/{commission}', [CommissionController::class, 'destroy'])->name('commissions.destroy');
    Route::get('/commissions/overrides', [CommissionOverrideController::class, 'index'])->name('commissions.overrides.index');
    Route::post('/commissions/overrides', [CommissionOverrideController::class, 'store'])->name('commissions.overrides.store');
    Route::delete('/commissions/overrides/{override}', [CommissionOverrideController::class, 'destroy'])->name('commissions.overrides.destroy');

    // Categories
    Route::resource('categories', CategoryController::class);
    
    // Resources
    Route::resource('offerings', OfferingController::class);
    Route::post('/offerings/{offering}/status', [OfferingController::class, 'toggleStatus'])->name('offerings.toggle-status');
    Route::resource('referrals', ReferralController::class);
});
