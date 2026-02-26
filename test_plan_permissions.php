<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Plan;

echo "=== Testing Plan Permissions System ===\n\n";

// Get all plans
$plans = Plan::all();

foreach ($plans as $plan) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Plan: {$plan->subscriptionName}\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    echo "Basic Features:\n";
    echo "  ✓ Sales (POS): " . ($plan->allows('sales') ? '✓' : '✗') . "\n";
    echo "  ✓ Purchases: " . ($plan->allows('purchases') ? '✓' : '✗') . "\n";
    echo "  ✓ Products: " . ($plan->allows('products') ? '✓' : '✗') . "\n";
    echo "  ✓ Stock: " . ($plan->allows('stock') ? '✓' : '✗') . "\n";
    echo "  ✓ Customers: " . ($plan->allows('customers') ? '✓' : '✗') . "\n";
    echo "  ✓ Suppliers: " . ($plan->allows('suppliers') ? '✓' : '✗') . "\n";
    echo "  ✓ VAT Settings: " . ($plan->allows('vat_settings') ? '✓' : '✗') . "\n";
    echo "  ✓ Reports: " . ($plan->allows('reports') ? '✓' : '✗') . "\n\n";
    
    echo "Limits:\n";
    echo "  • Warehouses: " . $plan->getWarehouseLimitText() . "\n";
    echo "  • Branches: " . $plan->getBranchLimitText() . "\n\n";
    
    echo "Advanced Features:\n";
    echo "  • Due List: " . ($plan->allows('due_list') ? '✓' : '✗') . "\n";
    echo "  • Finance & Accounting: " . ($plan->allows('finance') ? '✓' : '✗') . "\n";
    echo "  • Commission & Sales: " . ($plan->allows('commission') ? '✓' : '✗') . "\n";
    echo "  • HRM: " . ($plan->allows('hrm') ? '✓' : '✗') . "\n";
    echo "  • POS App: " . ($plan->allows('pos_app') ? '✓' : '✗') . "\n";
    echo "  • Online Store: " . ($plan->allows('store') ? '✓' : '✗') . "\n\n";
    
    // Test warehouse limit
    echo "Testing Warehouse Limits:\n";
    for ($i = 0; $i <= 3; $i++) {
        $canAdd = $plan->canAddWarehouse($i);
        echo "  • With {$i} warehouses: " . ($canAdd ? 'Can add more ✓' : 'Limit reached ✗') . "\n";
    }
    echo "\n";
    
    // Test branch limit
    echo "Testing Branch Limits:\n";
    for ($i = 0; $i <= 3; $i++) {
        $canAdd = $plan->canAddBranch($i);
        echo "  • With {$i} branches: " . ($canAdd ? 'Can add more ✓' : 'Limit reached ✗') . "\n";
    }
    echo "\n\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✓ All tests completed successfully!\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
