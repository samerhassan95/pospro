<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking tables for business_id = 4:\n\n";

$tables = App\Models\RestaurantTable::where('business_id', 4)
    ->orderBy('table_name')
    ->get(['id', 'table_name', 'is_custom', 'chair_count', 'table_type']);

echo "Total tables: " . $tables->count() . "\n\n";

foreach($tables as $table) {
    $isCustom = $table->is_custom ? 'YES' : 'NO';
    echo sprintf(
        "ID: %d | Name: %-10s | Custom: %-3s | Chairs: %2d | Type: %s\n",
        $table->id,
        $table->table_name,
        $isCustom,
        $table->chair_count,
        $table->table_type
    );
}

echo "\n";
echo "Custom tables: " . $tables->where('is_custom', true)->count() . "\n";
echo "Default tables: " . $tables->where('is_custom', false)->count() . "\n";
