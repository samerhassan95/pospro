<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Current Plans ===\n\n";

$plans = DB::table('plans')->get();

foreach ($plans as $plan) {
    echo "ID: {$plan->id}\n";
    echo "Name: {$plan->subscriptionName}\n";
    echo "Price: {$plan->subscriptionPrice}\n";
    echo "Allow Multibranch: " . ($plan->allow_multibranch ?? 'N/A') . "\n";
    echo "Features: " . ($plan->features ?? 'N/A') . "\n";
    echo "---\n\n";
}

echo "Total plans: " . $plans->count() . "\n";
