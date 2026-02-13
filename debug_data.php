<?php

use App\Models\User;
use App\Models\Associate;
use App\Models\Offering;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- USERS & ASSOCIATES ---\n";
$user = User::with('associate')->whereHas('associate')->first();
if ($user) {
    print_r($user->toArray());
    print_r($user->associate->toArray());
} else {
    echo "No associate found.\n";
}

echo "\n--- OFFERINGS ---\n";
$offerings = Offering::all();
foreach ($offerings as $offering) {
    echo "ID: {$offering->id} | Name: {$offering->name} | Type: {$offering->type} | Price: {$offering->base_price} | Comm: {$offering->commission_rate}\n";
}
