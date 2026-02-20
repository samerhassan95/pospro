<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Making all tables custom for business_id = 4...\n\n";

$updated = App\Models\RestaurantTable::where('business_id', 4)
    ->update(['is_custom' => true]);

echo "Updated $updated tables to is_custom = true\n\n";

// Verify
$tables = App\Models\RestaurantTable::where('business_id', 4)
    ->orderBy('table_name')
    ->get(['id', 'table_name', 'is_custom']);

echo "Verification:\n";
foreach($tables as $table) {
    $isCustom = $table->is_custom ? 'YES' : 'NO';
    echo sprintf("%-10s | Custom: %s\n", $table->table_name, $isCustom);
}

echo "\nAll tables are now custom!\n";
