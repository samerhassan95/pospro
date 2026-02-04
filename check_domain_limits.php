<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Plan;
use App\Models\PlanSubscribe;

echo "=== Checking Domain Limits Configuration ===\n\n";

// Check all plans
$plans = Plan::all();
echo "Total Plans: " . $plans->count() . "\n\n";

foreach ($plans as $plan) {
    echo "Plan ID: {$plan->id}\n";
    echo "Plan Name: {$plan->subscriptionName}\n";
    echo "Addon Domain Limit: " . ($plan->addon_domain_limit ?? 'NULL') . "\n";
    echo "Subdomain Limit: " . ($plan->subdomain_limit ?? 'NULL') . "\n";
    echo "---\n";
}

// Check current user's plan
if (auth()->check()) {
    echo "\n=== Current User's Plan ===\n";
    $planData = plan_data();
    if ($planData) {
        echo "Plan Subscribe ID: {$planData->id}\n";
        echo "Plan ID: {$planData->plan_id}\n";
        echo "Plan Name: " . ($planData->plan->subscriptionName ?? 'N/A') . "\n";
        echo "Addon Domain Limit: " . ($planData->plan->addon_domain_limit ?? 'NULL') . "\n";
        echo "Subdomain Limit: " . ($planData->plan->subdomain_limit ?? 'NULL') . "\n";
    } else {
        echo "No active plan subscription found.\n";
    }
} else {
    echo "\n=== No authenticated user ===\n";
    echo "Please run this from a logged-in session or check via database directly.\n";
}

echo "\n=== SQL to update all plans ===\n";
echo "UPDATE plans SET addon_domain_limit = 5, subdomain_limit = 10;\n";
