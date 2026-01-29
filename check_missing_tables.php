<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "=== CHECKING FOR MISSING TABLES ===\n\n";

// List of tables that should exist based on enabled modules
$requiredTables = [
    // Core tables
    'users',
    'businesses',
    'plans',
    'plan_subscribes',
    'options',
    'currencies',
    'gateways',
    'notifications',
    
    // Business tables
    'parties',
    'products',
    'sales',
    'sale_details',
    'purchases',
    'purchase_details',
    'expenses',
    'incomes',
    'categories',
    'brands',
    'units',
    'due_collects',
    'sale_returns',
    'sale_return_details',
    'purchase_returns',
    'purchase_return_details',
    'transactions',
    
    // Module tables
    'domains',  // CustomDomainAddon
    'warehouses',  // WarehouseAddon
    'transfers',  // WarehouseAddon
    'transfer_products',  // WarehouseAddon
    'branches',  // MultiBranchAddon
];

$missingTables = [];
$existingTables = [];

foreach ($requiredTables as $table) {
    if (Schema::hasTable($table)) {
        $existingTables[] = $table;
        echo "✓ Table '{$table}' exists\n";
    } else {
        $missingTables[] = $table;
        echo "✗ Table '{$table}' is MISSING\n";
    }
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "SUMMARY:\n";
echo "Existing tables: " . count($existingTables) . "\n";
echo "Missing tables: " . count($missingTables) . "\n";

if (count($missingTables) > 0) {
    echo "\n" . str_repeat("=", 80) . "\n";
    echo "MISSING TABLES:\n";
    foreach ($missingTables as $table) {
        echo "  - {$table}\n";
    }
    
    echo "\n" . str_repeat("=", 80) . "\n";
    echo "ACTION REQUIRED:\n";
    echo "Run the following command on your server:\n";
    echo "  php artisan migrate\n";
    echo "\nOr run module-specific migrations:\n";
    echo "  php artisan module:migrate\n";
} else {
    echo "\n✓ All required tables exist!\n";
}

echo "\n=== CHECK COMPLETE ===\n";
