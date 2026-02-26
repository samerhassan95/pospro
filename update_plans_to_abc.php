<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Updating Plans to A, B, C System ===\n\n";

// Run the migration first
echo "Running migration...\n";
exec('php artisan migrate --path=database/migrations/2026_02_26_000000_add_permissions_to_plans_table.php', $output, $return);
if ($return === 0) {
    echo "✓ Migration completed successfully\n\n";
} else {
    echo "✗ Migration failed\n";
    print_r($output);
    exit(1);
}

// Update Plan A (Free -> A)
echo "Updating Plan A (Basic)...\n";
DB::table('plans')->where('id', 1)->update([
    'subscriptionName' => 'A',
    'allow_purchases' => 1,
    'allow_products' => 1,
    'allow_warehouses' => 1,
    'warehouse_limit' => 1, // Only 1 warehouse
    'branch_limit' => 1, // Only 1 branch
    'allow_stock' => 1,
    'allow_customers' => 1,
    'allow_suppliers' => 1,
    'allow_vat_settings' => 1,
    'allow_due_list' => 0, // No access
    'allow_finance' => 0, // No access
    'allow_commission' => 0, // No access
    'allow_hrm' => 0, // No access
    'allow_reports' => 1,
    'allow_pos_app' => 0, // No access
    'allow_store' => 0, // No access
    'allow_sales' => 1,
    'allow_multibranch' => 1, // Allow branches but limited to 1
]);
echo "✓ Plan A updated\n\n";

// Update Plan B (Standard -> B)
echo "Updating Plan B (Professional)...\n";
DB::table('plans')->where('id', 2)->update([
    'subscriptionName' => 'B',
    'allow_purchases' => 1,
    'allow_products' => 1,
    'allow_warehouses' => 1,
    'warehouse_limit' => null, // Unlimited
    'branch_limit' => null, // Unlimited
    'allow_stock' => 1,
    'allow_customers' => 1,
    'allow_suppliers' => 1,
    'allow_vat_settings' => 1,
    'allow_due_list' => 1,
    'allow_finance' => 1,
    'allow_commission' => 1,
    'allow_hrm' => 1,
    'allow_reports' => 1,
    'allow_pos_app' => 0, // No access
    'allow_store' => 0, // No access
    'allow_sales' => 1,
    'allow_multibranch' => 1,
]);
echo "✓ Plan B updated\n\n";

// Update Plan C (Premium -> C)
echo "Updating Plan C (Enterprise)...\n";
DB::table('plans')->where('id', 3)->update([
    'subscriptionName' => 'C',
    'allow_purchases' => 1,
    'allow_products' => 1,
    'allow_warehouses' => 1,
    'warehouse_limit' => null, // Unlimited
    'branch_limit' => null, // Unlimited
    'allow_stock' => 1,
    'allow_customers' => 1,
    'allow_suppliers' => 1,
    'allow_vat_settings' => 1,
    'allow_due_list' => 1,
    'allow_finance' => 1,
    'allow_commission' => 1,
    'allow_hrm' => 1,
    'allow_reports' => 1,
    'allow_pos_app' => 1, // Full access
    'allow_store' => 1, // Full access
    'allow_sales' => 1,
    'allow_multibranch' => 1,
]);
echo "✓ Plan C updated\n\n";

// Display updated plans
echo "=== Updated Plans ===\n\n";
$plans = DB::table('plans')->get();

foreach ($plans as $plan) {
    echo "Plan: {$plan->subscriptionName}\n";
    echo "  - Sales: " . ($plan->allow_sales ? '✓' : '✗') . "\n";
    echo "  - Purchases: " . ($plan->allow_purchases ? '✓' : '✗') . "\n";
    echo "  - Products: " . ($plan->allow_products ? '✓' : '✗') . "\n";
    echo "  - Warehouses: " . ($plan->allow_warehouses ? '✓' : '✗') . " (Limit: " . ($plan->warehouse_limit ?? 'Unlimited') . ")\n";
    echo "  - Branches: " . ($plan->allow_multibranch ? '✓' : '✗') . " (Limit: " . ($plan->branch_limit ?? 'Unlimited') . ")\n";
    echo "  - Stock: " . ($plan->allow_stock ? '✓' : '✗') . "\n";
    echo "  - Customers: " . ($plan->allow_customers ? '✓' : '✗') . "\n";
    echo "  - Suppliers: " . ($plan->allow_suppliers ? '✓' : '✗') . "\n";
    echo "  - VAT Settings: " . ($plan->allow_vat_settings ? '✓' : '✗') . "\n";
    echo "  - Due List: " . ($plan->allow_due_list ? '✓' : '✗') . "\n";
    echo "  - Finance: " . ($plan->allow_finance ? '✓' : '✗') . "\n";
    echo "  - Commission: " . ($plan->allow_commission ? '✓' : '✗') . "\n";
    echo "  - HRM: " . ($plan->allow_hrm ? '✓' : '✗') . "\n";
    echo "  - Reports: " . ($plan->allow_reports ? '✓' : '✗') . "\n";
    echo "  - POS App: " . ($plan->allow_pos_app ? '✓' : '✗') . "\n";
    echo "  - Store: " . ($plan->allow_store ? '✓' : '✗') . "\n";
    echo "---\n\n";
}

echo "✓ All plans updated successfully!\n";
