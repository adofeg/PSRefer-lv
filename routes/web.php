<?php

use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisterController::class, 'create'])->name('register');
    Route::post('register', [RegisterController::class, 'store']);

    // Placeholder for Login
    Route::get('login', [\App\Http\Controllers\Auth\LoginController::class, 'create'])->name('login');
    Route::post('login', [\App\Http\Controllers\Auth\LoginController::class, 'store']);
    Route::post('logout', [\App\Http\Controllers\Auth\LoginController::class, 'destroy'])->name('logout');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();

        // Stats Logic
        if (in_array($user->role, ['admin', 'psadmin'])) {
            // PSAdmin/Admin sees everything
            $referralsQuery = \App\Models\Referral::query();
            $closedReferralsQuery = \App\Models\Referral::where('status', 'Cerrado');
        } else {
            // Associate/Others see own
            $referralsQuery = \App\Models\Referral::where('user_id', $user->id);
            $closedReferralsQuery = \App\Models\Referral::where('user_id', $user->id)->where('status', 'Cerrado');
        }

        $totalEarnings = $user->balance; // Always show own balance for payment context
        if (in_array($user->role, ['admin', 'psadmin'])) {
            // For admin, maybe show total platform revenue as 'total_revenue'
            $totalRevenue = \App\Models\Referral::where('status', 'Cerrado')->sum('revenue_generated');
        } else {
            $totalRevenue = $closedReferralsQuery->sum('revenue_generated');
        }

        // Recent Referrals
        $recentReferrals = $referralsQuery->with('offering')
            ->latest()
            ->take(5)
            ->get();

        // Chart Data (DB Agnostic via Collection)
        // Fetch closed referrals for current year with minimal fields
        $chartData = $closedReferralsQuery->clone()
            ->whereYear('closed_at', date('Y'))
            ->get(['closed_at', 'revenue_generated']);

        // Group by month in PHP
        $monthlyRevenue = $chartData->groupBy(function ($item) {
            return $item->closed_at ? \Carbon\Carbon::parse($item->closed_at)->format('m') : '00';
        })->map(function ($group) {
            return $group->sum('revenue_generated');
        });

        return Inertia::render('Dashboard', [
            'stats' => [
                'start_balance' => 0,
                'current_balance' => $totalEarnings,
                'total_revenue' => $totalRevenue
            ],
            'recentReferrals' => $recentReferrals,
            'monthlyRevenue' => $monthlyRevenue
        ]);
    })->name('dashboard');

    Route::get('/offerings', [\App\Http\Controllers\OfferingController::class, 'index'])->name('offerings.index');
    Route::resource('referrals', \App\Http\Controllers\ReferralController::class);

    // API-like routes for Dashboard components
    Route::get('/api/analytics/revenue', [\App\Http\Controllers\AnalyticsController::class, 'revenue'])->name('analytics.revenue');
});

// Admin Routes
Route::middleware(['auth', 'verified', 'admin']) // Simplified admin middleware alias
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Re-map resource controllers if needed for strictly admin paths,
        // but currently Offering/Referral handle their own auth/role checks in Controller.
    });
