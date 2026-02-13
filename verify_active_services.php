<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$count = App\Models\Offering::where('is_active', true)->count();
echo "Active Offerings: " . $count . "\n";

$offerings = App\Models\Offering::where('is_active', true)->pluck('name');
foreach ($offerings as $name) {
    echo "- " . $name . "\n";
}
