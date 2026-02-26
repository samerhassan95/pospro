<?php

/**
 * Test script to verify warehouse and branch limits are working correctly
 * 
 * This script tests:
 * 1. Plan A: 1 warehouse, 1 branch
 * 2. Plan B: Unlimited warehouses and branches
 * 3. Plan C: Unlimited warehouses and branches
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Business;
use App\Models\Plan;
use App\Models\PlanSubscribe;
use App\Models\Warehouse;
use App\Models\Branch;

echo "=== Testing Warehouse and Branch Limits ===\n\n";

// Get all plans
$plans = Plan::whereIn('subscriptionName', ['A', 'B', 'C'])->get()->keyBy('subscriptionName');

if ($plans->isEmpty()) {
    echo "❌ No plans found! Please run update_plans_to_abc.php first.\n";
    exit(1);
}

echo "✓ Found plans: " . $plans->pluck('subscriptionName')->implode(', ') . "\n\n";

// Test each plan
foreach ($plans as $planName => $plan) {
    echo "--- Testing Plan {$planName} ---\n";
    echo "Warehouse Limit: " . ($plan->warehouse_limit === null ? 'Unlimited' : $plan->warehouse_limit) . "\n";
    echo "Branch Limit: " . ($plan->branch_limit === null ? 'Unlimited' : $plan->branch_limit) . "\n";
    
    // Find a business with this plan
    $subscription = PlanSubscribe::where('plan_id', $plan->id)
        ->first();
    
    if (!$subscription) {
        echo "⚠️  No subscription found for Plan {$planName}\n\n";
        continue;
    }
    
    $business = Business::find($subscription->business_id);
    
    if (!$business) {
        echo "⚠️  Business not found for subscription\n\n";
        continue;
    }
    
    echo "Testing Business: {$business->companyName} (ID: {$business->id})\n";
    
    // Count current warehouses and branches
    $warehouseCount = Warehouse::where('business_id', $business->id)->count();
    $branchCount = Branch::where('business_id', $business->id)->count();
    
    echo "Current Warehouses: {$warehouseCount}\n";
    echo "Current Branches: {$branchCount}\n";
    
    // Test canAddWarehouse
    $canAddWarehouse = $business->canAddWarehouse();
    echo "Can Add Warehouse: " . ($canAddWarehouse ? '✓ Yes' : '✗ No') . "\n";
    
    // Test canAddBranch
    $canAddBranch = $business->canAddBranch();
    echo "Can Add Branch: " . ($canAddBranch ? '✓ Yes' : '✗ No') . "\n";
    
    // Verify limits are correct
    if ($planName === 'A') {
        // Plan A should have limit of 1 for both
        if ($warehouseCount >= 1 && !$canAddWarehouse) {
            echo "✓ Warehouse limit enforced correctly\n";
        } elseif ($warehouseCount < 1 && $canAddWarehouse) {
            echo "✓ Can still add warehouse (under limit)\n";
        } else {
            echo "⚠️  Warehouse limit check may have issues\n";
        }
        
        if ($branchCount >= 1 && !$canAddBranch) {
            echo "✓ Branch limit enforced correctly\n";
        } elseif ($branchCount < 1 && $canAddBranch) {
            echo "✓ Can still add branch (under limit)\n";
        } else {
            echo "⚠️  Branch limit check may have issues\n";
        }
    } else {
        // Plans B and C should be unlimited
        if ($canAddWarehouse && $canAddBranch) {
            echo "✓ Unlimited access confirmed\n";
        } else {
            echo "⚠️  Should have unlimited access but doesn't\n";
        }
    }
    
    echo "\n";
}

echo "=== Test Complete ===\n";
