<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Cleaning Up Test Data ===\n\n";

// Delete test warehouses
$deletedWarehouses = DB::table('warehouses')
    ->where('name', 'LIKE', 'Test Warehouse%')
    ->delete();

echo "Deleted {$deletedWarehouses} test warehouses\n";

// Delete test branches
$deletedBranches = DB::table('branches')
    ->where('name', 'LIKE', 'Test Branch%')
    ->delete();

echo "Deleted {$deletedBranches} test branches\n";

echo "\n✓ Cleanup complete!\n";
