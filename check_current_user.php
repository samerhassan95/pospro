<?php
// Check current logged in user
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\RestaurantTable;

echo "=== Checking Users and Tables ===\n\n";

// Get all users with business_id
$users = User::whereNotNull('business_id')->get();
echo "Users with business_id:\n";
foreach ($users as $user) {
    echo "  - ID: {$user->id}, Email: {$user->email}, Business ID: {$user->business_id}\n";
}
echo "\n";

// Get tables for each business
$businessIds = $users->pluck('business_id')->unique();
foreach ($businessIds as $businessId) {
    echo "Tables for business_id = $businessId:\n";
    $tables = RestaurantTable::where('business_id', $businessId)
        ->where('is_active', true)
        ->get();
    
    if ($tables->count() > 0) {
        foreach ($tables as $table) {
            echo "  - {$table->table_name} ({$table->chair_count} chairs)\n";
        }
    } else {
        echo "  No tables found\n";
    }
    echo "\n";
}
