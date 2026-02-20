<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TableReservation;
use App\Models\User;

echo "=== Checking Recent Reservations ===\n\n";

// Get current user's business_id
$user = User::whereNotNull('business_id')->first();
if (!$user) {
    echo "No user found\n";
    exit;
}

echo "Checking for business_id: {$user->business_id}\n\n";

// Get all reservations
$allReservations = TableReservation::with('table')->orderBy('id', 'desc')->limit(10)->get();
echo "Total reservations in database: " . TableReservation::count() . "\n";
echo "Last 10 reservations:\n";
foreach ($allReservations as $r) {
    $tableName = $r->table ? $r->table->table_name : 'N/A';
    echo "  - ID: {$r->id}, Business: {$r->business_id}, Table: {$tableName}, Customer: {$r->customer_name}, Date: {$r->reservation_date} {$r->reservation_time}, Status: {$r->status}\n";
}

echo "\n";

// Get reservations for current business
$businessReservations = TableReservation::where('business_id', $user->business_id)
    ->with('table')
    ->orderBy('id', 'desc')
    ->get();

echo "Reservations for business {$user->business_id}: {$businessReservations->count()}\n";
foreach ($businessReservations as $r) {
    $tableName = $r->table ? $r->table->table_name : 'N/A';
    echo "  - ID: {$r->id}, Table: {$tableName}, Customer: {$r->customer_name}, Date: {$r->reservation_date} {$r->reservation_time}, Status: {$r->status}\n";
}
