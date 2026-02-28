<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== User Types in Database ===\n\n";

// Get sample users
$users = DB::table('users')
    ->select('id', 'name', 'email', 'role', 'business_id', 'branch_id')
    ->take(10)
    ->get();

foreach ($users as $user) {
    echo "ID: {$user->id}\n";
    echo "Name: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "Role: " . ($user->role ?? 'NULL') . "\n";
    echo "Business ID: " . ($user->business_id ?? 'NULL') . "\n";
    echo "Branch ID: " . ($user->branch_id ?? 'NULL') . "\n";
    echo "---\n";
}

echo "\n=== User Statistics ===\n\n";

// Count by role
$roleStats = DB::table('users')
    ->select('role', DB::raw('count(*) as count'))
    ->groupBy('role')
    ->get();

echo "Users by Role:\n";
foreach ($roleStats as $stat) {
    echo "  " . ($stat->role ?? 'NULL') . ": {$stat->count}\n";
}

// Count by business_id
$businessStats = DB::table('users')
    ->select(DB::raw('CASE WHEN business_id IS NULL THEN "Admin" ELSE "Business User" END as type'), DB::raw('count(*) as count'))
    ->groupBy('type')
    ->get();

echo "\nUsers by Type:\n";
foreach ($businessStats as $stat) {
    echo "  {$stat->type}: {$stat->count}\n";
}

// Check roles table
echo "\n=== Available Roles (Spatie) ===\n\n";
$roles = DB::table('roles')->select('id', 'name', 'guard_name')->get();
foreach ($roles as $role) {
    echo "ID: {$role->id} - Name: {$role->name} - Guard: {$role->guard_name}\n";
}
