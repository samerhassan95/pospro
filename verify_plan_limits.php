<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Plan;
use App\Models\PlanSubscribe;
use App\Models\Business;

echo "=== PLAN LIMITS VERIFICATION ===\n\n";

// Check all plans
echo "All Plans in Database:\n";
echo str_repeat("-", 80) . "\n";
$plans = Plan::all();
foreach ($plans as $plan) {
    echo "Plan ID: {$plan->id}\n";
    echo "Name: {$plan->subscriptionName}\n";
    echo "Addon Domain Limit: {$plan->addon_domain_limit}\n";
    echo "Subdomain Limit: {$plan->subdomain_limit}\n";
    echo "Allow Multibranch: {$plan->allow_multibranch}\n";
    echo str_repeat("-", 80) . "\n";
}

// Check plan subscribes
echo "\nActive Plan Subscriptions:\n";
echo str_repeat("-", 80) . "\n";
$subscriptions = PlanSubscribe::with('plan', 'business')->get();
foreach ($subscriptions as $sub) {
    echo "Subscription ID: {$sub->id}\n";
    echo "Business ID: {$sub->business_id}\n";
    echo "Business Name: " . ($sub->business->companyName ?? 'N/A') . "\n";
    echo "Plan ID: {$sub->plan_id}\n";
    echo "Plan Name: " . ($sub->plan->subscriptionName ?? 'N/A') . "\n";
    echo "Plan Addon Domain Limit: " . ($sub->plan->addon_domain_limit ?? 'N/A') . "\n";
    echo "Plan Subdomain Limit: " . ($sub->plan->subdomain_limit ?? 'N/A') . "\n";
    echo str_repeat("-", 80) . "\n";
}

// Test plan_data() helper function
echo "\nTesting plan_data() Helper Function:\n";
echo str_repeat("-", 80) . "\n";

$businesses = Business::all();
foreach ($businesses as $business) {
    echo "Business ID: {$business->id}\n";
    echo "Business Name: {$business->companyName}\n";
    
    try {
        $planData = plan_data($business->id);
        
        if ($planData) {
            echo "Plan Data Found: YES\n";
            echo "Plan ID: {$planData->plan_id}\n";
            
            if ($planData->plan) {
                echo "Plan Object Loaded: YES\n";
                echo "Plan Name: {$planData->plan->subscriptionName}\n";
                echo "Addon Domain Limit: {$planData->plan->addon_domain_limit}\n";
                echo "Subdomain Limit: {$planData->plan->subdomain_limit}\n";
            } else {
                echo "Plan Object Loaded: NO\n";
            }
        } else {
            echo "Plan Data Found: NO\n";
        }
    } catch (\Exception $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
    }
    
    echo str_repeat("-", 80) . "\n";
}

echo "\n=== VERIFICATION COMPLETE ===\n";
