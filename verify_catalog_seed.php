<?php

use App\Models\Offering;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Checking Offerings count...\n";
    $count = Offering::count();
    echo "Total Offerings: " . $count . "\n";

    if ($count > 0) {
        $offering = Offering::where('name', 'like', '%Business Liability%')->first();
        if ($offering) {
            echo "Found Offering: " . $offering->name . "\n";
            echo "Commission Rate: " . $offering->commission_rate . "%\n";
            
            $schema = $offering->form_schema;
            echo "Schema Fields Count: " . count($schema) . "\n";
            
            // Check for a specific new field from the PDF
            $hasEin = false;
            foreach ($schema as $field) {
                if ($field['name'] === 'ein') {
                    $hasEin = true;
                    echo "field 'ein' found in schema!\n";
                    break;
                }
            }
            if (!$hasEin) echo "field 'ein' NOT found in schema.\n";
        } else {
            echo "Offering 'Business Liability / Workers Comp' not found.\n";
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
