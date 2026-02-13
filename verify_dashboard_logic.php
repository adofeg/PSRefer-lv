<?php

use App\Models\User;
use App\Models\Associate;
use App\Models\Referral;
use App\Models\Offering;
use App\Enums\ReferralStatus;
use App\Actions\Associate\GetDashboardStatsAction;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Setup Test Data
echo "Setting up test data...\n";

// Helper to create offering
function createOffering($name, $config, $base = 0, $rate = 0) {
    return Offering::factory()->create([
        'name' => $name,
        'commission_config' => $config,
        'base_commission' => $base,
        'commission_rate' => $rate,
        'is_active' => true,
    ]);
}

$fixedOffering = createOffering('Fixed Service', ['fixed_amount' => 25], 25, 0);
$percentOffering = createOffering('Percent Service', ['percentage' => 10], 0, 10);

// Create Associate
$user = User::factory()->create(['name' => 'Dashboard Tester', 'email' => 'dashboard@test.com']);
$associate = Associate::create(['user_id' => $user->id]);
$user->assignRole('associate');

// Create Referrals
// 1. Won (Paid) - Fixed $25
Referral::create([
    'associate_id' => $associate->id,
    'offering_id' => $fixedOffering->id,
    'client_name' => 'Client Paid',
    'client_contact' => 'test',
    'status' => ReferralStatus::Paid->value,
    'revenue_generated' => 25.00,
]);

// 2. Pending (Closed) - Fixed $25
Referral::create([
    'associate_id' => $associate->id,
    'offering_id' => $fixedOffering->id,
    'client_name' => 'Client Pending',
    'client_contact' => 'test',
    'status' => ReferralStatus::Closed->value, // Eligibility: Closed or Paid
    'revenue_generated' => 0, // Not paid yet
]);

// 3. Lost (Fixed) - $25 Potential
Referral::create([
    'associate_id' => $associate->id,
    'offering_id' => $fixedOffering->id,
    'client_name' => 'Client Lost Fixed',
    'client_contact' => 'test',
    'status' => ReferralStatus::Lost->value,
]);

// 4. Lost (Percent) - $100 Potential (10% of 1000)
Referral::create([
    'associate_id' => $associate->id,
    'offering_id' => $percentOffering->id,
    'client_name' => 'Client Lost Percent',
    'client_contact' => 'test',
    'status' => ReferralStatus::Lost->value,
    'deal_value' => 1000.00,
]);

// 5. Lost (Percent, No Deal Value) - $0 Potential
Referral::create([
    'associate_id' => $associate->id,
    'offering_id' => $percentOffering->id,
    'client_name' => 'Client Lost Unknown',
    'client_contact' => 'test',
    'status' => ReferralStatus::Lost->value,
    'deal_value' => null,
]);


// 2. Run Action
echo "Running GetDashboardStatsAction...\n";
$action = new GetDashboardStatsAction();
$stats = $action->execute($user);

// 3. Output Results
echo "Stats:\n";
echo "Total Earned (Exp: 25.00): " . $stats['total_earned'] . "\n";
echo "Pending Earned (Exp: 25.00): " . $stats['pending_earned'] . "\n";
echo "Lost Potential (Exp: 125.00): " . $stats['lost_potential'] . "\n";
echo "Total Referrals (Exp: 5): " . $stats['total_referrals'] . "\n";
echo "Conversion Rate (Exp: 40%): " . $stats['conversion_rate'] . "%\n";

// Validation
$pass = true;
if ($stats['total_earned'] != 25) { echo "FAIL: Total Earned\n"; $pass = false; }
if ($stats['pending_earned'] != 25) { echo "FAIL: Pending Earned\n"; $pass = false; }
if ($stats['lost_potential'] != 125) { echo "FAIL: Lost Potential\n"; $pass = false; }
if ($stats['conversion_rate'] != 40) { echo "FAIL: Conversion Rate\n"; $pass = false; }

if ($pass) {
    echo "\n✅ VERIFICATION PASSED\n";
} else {
    echo "\n❌ VERIFICATION FAILED\n";
}

// Cleanup (Optional, for now keep to debug if needed)
// $user->delete(); 
