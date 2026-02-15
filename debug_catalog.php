<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Offering;

$offerings = Offering::all();
foreach ($offerings as $o) {
    echo "ID: {$o->id} | Name: {$o->name}\n";
    echo "Schema: " . json_encode($o->form_schema) . "\n\n";
}
