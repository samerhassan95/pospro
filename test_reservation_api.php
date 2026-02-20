<?php
// Test Reservation API endpoints
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\RestaurantTable;
use App\Models\TableReservation;
use App\Models\User;

echo "=== Testing Reservation API ===\n\n";

// Get a business user
$user = User::whereNotNull('business_id')->first();
if (!$user) {
    echo "❌ No user with business_id found\n";
    exit;
}

echo "✅ Testing with business: {$user->business_id}\n\n";

// Test 1: Get all tables
echo "1. Testing GET /business/tables\n";
$tables = RestaurantTable::where('business_id', $user->business_id)
    ->where('is_active', true)
    ->get();

echo "   Found {$tables->count()} tables:\n";
foreach ($tables as $table) {
    echo "   - {$table->table_name} ({$table->chair_count} chairs) - Status: {$table->status}\n";
}
echo "\n";

// Test 2: Get all reservations
echo "2. Testing GET /business/table-reservations\n";
$reservations = TableReservation::where('business_id', $user->business_id)
    ->with('table')
    ->get();

echo "   Found {$reservations->count()} reservations:\n";
foreach ($reservations as $reservation) {
    $tableName = $reservation->table ? $reservation->table->table_name : 'Unknown';
    echo "   - {$tableName} on {$reservation->reservation_date} at {$reservation->reservation_time} - Status: {$reservation->status}\n";
}
echo "\n";

// Test 3: Check for overlapping reservations
echo "3. Testing overlap detection (2 hours window)\n";
$testDate = '2026-02-27';
$testTime = '18:00:00';
$testDateTime = strtotime("$testDate $testTime");

echo "   Checking for reservations around $testDate $testTime\n";

$overlapping = TableReservation::where('business_id', $user->business_id)
    ->where('reservation_date', $testDate)
    ->whereIn('status', ['reserved', 'arrived'])
    ->where(function ($query) use ($testDateTime) {
        $start = date('H:i:s', $testDateTime - 7200); // -2 hours
        $end = date('H:i:s', $testDateTime + 7200); // +2 hours
        $query->whereBetween('reservation_time', [$start, $end]);
    })
    ->get();

echo "   Found {$overlapping->count()} overlapping reservations:\n";
foreach ($overlapping as $res) {
    $tableName = $res->table ? $res->table->table_name : 'Unknown';
    echo "   - {$tableName} at {$res->reservation_time}\n";
}
echo "\n";

// Test 4: Available tables for specific time
echo "4. Testing available tables for $testDate $testTime with 2 guests\n";
$guests = 2;

$reservedTableIds = $overlapping->pluck('table_id')->toArray();
echo "   Reserved table IDs: " . implode(', ', $reservedTableIds) . "\n";

$availableTables = RestaurantTable::where('business_id', $user->business_id)
    ->where('is_active', true)
    ->where('chair_count', '>=', $guests)
    ->whereNotIn('id', $reservedTableIds)
    ->where('status', '!=', 'utilized')
    ->get();

echo "   Found {$availableTables->count()} available tables:\n";
foreach ($availableTables as $table) {
    echo "   - {$table->table_name} ({$table->chair_count} chairs)\n";
}
echo "\n";

echo "=== Test Complete ===\n";
