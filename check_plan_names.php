<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Plan;

echo "Checking Plan Names:\n\n";

$plans = Plan::all();

foreach ($plans as $plan) {
    echo "Plan ID: {$plan->id}\n";
    echo "Name: " . substr($plan->subscriptionName, 0, 100) . "\n";
    
    if (strpos($plan->subscriptionName, '<svg') !== false) {
        echo "⚠️  WARNING: Contains SVG code!\n";
    }
    
    echo "---\n";
}
