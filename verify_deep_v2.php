<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$offerings = App\Models\Offering::where('is_active', true)->get();

foreach ($offerings as $o) {
    echo "- Name: " . $o->name . "\n";
    echo "  Rate: " . ($o->commission_rate > 0 ? $o->commission_rate . '%' : '0%') . "\n";
    echo "  Base: $" . $o->base_commission . "\n";
    echo "  Desc: " . $o->description . "\n";
    echo "\n";
}
