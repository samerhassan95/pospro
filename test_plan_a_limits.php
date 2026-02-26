<?php

/**
 * Test Plan A limits by creating warehouses and branches
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Business;
use App\Models\Plan;
use App\Models\PlanSubscribe;
use App\Models\Warehouse;
use App\Models\Branch;

echo "=== Testing Plan A Limits ===\n\n";

// Get Plan A
$planA = Plan::where('subscriptionName', 'A')->first();

if (!$planA) {
    echo "❌ Plan A not found! Please run update_plans_to_abc.php first.\n";
    exit(1);
}

echo "✓ Found Plan A\n";
echo "  Warehouse Limit: {$planA->warehouse_limit}\n";
echo "  Branch Limit: {$planA->branch_limit}\n\n";

// Find a business with Plan A
$subscription = PlanSubscribe::where('plan_id', $planA->id)->first();

if (!$subscription) {
    echo "❌ No business found with Plan A\n";
    exit(1);
}

$business = Business::find($subscription->business_id);
echo "✓ Testing with Business: {$business->companyName} (ID: {$business->id})\n\n";

// Test Warehouse Creation
echo "--- Testing Warehouse Limits ---\n";
$warehouseCount = Warehouse::where('business_id', $business->id)->count();
echo "Current warehouses: {$warehouseCount}\n";

if ($warehouseCount < 1) {
    echo "Creating first warehouse...\n";
    $warehouse1 = Warehouse::create([
        'business_id' => $business->id,
        'branch_id' => 1,
        'name' => 'Test Warehouse 1',
        'phone' => '1234567890',
        'email' => 'warehouse1@test.com',
        'address' => 'Test Address 1'
    ]);
    echo "✓ First warehouse created (ID: {$warehouse1->id})\n";
    $warehouseCount++;
}

echo "Can add another warehouse? " . ($business->canAddWarehouse() ? 'Yes' : 'No') . "\n";

if ($business->canAddWarehouse()) {
    echo "⚠️  WARNING: Should not be able to add another warehouse!\n";
    echo "Attempting to create second warehouse...\n";
    try {
        $warehouse2 = Warehouse::create([
            'business_id' => $business->id,
            'branch_id' => 1,
            'name' => 'Test Warehouse 2',
            'phone' => '0987654321',
            'email' => 'warehouse2@test.com',
            'address' => 'Test Address 2'
        ]);
        echo "❌ ERROR: Second warehouse was created! (ID: {$warehouse2->id})\n";
        echo "This should have been blocked by the limit!\n";
        
        // Clean up
        $warehouse2->delete();
        echo "Cleaned up test warehouse 2\n";
    } catch (\Exception $e) {
        echo "✓ Correctly blocked: {$e->getMessage()}\n";
    }
} else {
    echo "✓ Correctly blocked from adding more warehouses\n";
}

echo "\n--- Testing Branch Limits ---\n";
$branchCount = Branch::where('business_id', $business->id)->count();
echo "Current branches: {$branchCount}\n";

echo "Can add another branch? " . ($business->canAddBranch() ? 'Yes' : 'No') . "\n";

if ($branchCount >= 1) {
    if ($business->canAddBranch()) {
        echo "❌ ERROR: Should not be able to add another branch when limit is reached!\n";
    } else {
        echo "✓ Correctly blocked from adding more branches\n";
    }
} else {
    echo "⚠️  No branches exist yet, cannot test limit enforcement\n";
    echo "Note: Branch creation requires authentication, skipping creation test\n";
}

echo "\n=== Test Complete ===\n";
echo "\nSummary:\n";
echo "- Warehouse limit is working: " . (!$business->canAddWarehouse() && $warehouseCount >= 1 ? '✓' : '✗') . "\n";
echo "- Branch limit check is working: " . (!$business->canAddBranch() && $branchCount >= 1 ? '✓' : ($branchCount < 1 ? '⚠️  (needs manual test)' : '✗')) . "\n";
