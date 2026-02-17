<?php

/**
 * Change Admin Credentials
 * 
 * Changes email from samerhassan@gmail.com to admin@example.com
 * and sets password to 123456
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== Changing Admin Credentials ===\n\n";

// Find user by old email
$user = User::where('email', 'samerhassan@gmail.com')->first();

if (!$user) {
    echo "❌ User with email 'samerhassan@gmail.com' not found!\n";
    echo "\nSearching for all admin users...\n";
    
    $admins = User::where('role', 'superadmin')->get();
    if ($admins->count() > 0) {
        echo "\nFound " . $admins->count() . " admin user(s):\n";
        foreach ($admins as $admin) {
            echo "  - ID: {$admin->id}, Email: {$admin->email}, Name: {$admin->name}\n";
        }
    } else {
        echo "❌ No admin users found!\n";
    }
    exit(1);
}

echo "✅ Found user:\n";
echo "   ID: {$user->id}\n";
echo "   Name: {$user->name}\n";
echo "   Email: {$user->email}\n";
echo "   Role: {$user->role}\n\n";

// Check if new email already exists
$existingUser = User::where('email', 'admin@example.com')->where('id', '!=', $user->id)->first();
if ($existingUser) {
    echo "⚠️  Warning: Email 'admin@example.com' already exists for another user!\n";
    echo "   User ID: {$existingUser->id}, Name: {$existingUser->name}\n";
    echo "\nDo you want to continue anyway? This will create a duplicate email.\n";
    exit(1);
}

// Update credentials
echo "📝 Updating credentials...\n";

$user->email = 'admin@example.com';
$user->password = Hash::make('123456');
$user->save();

echo "✅ Credentials updated successfully!\n\n";

echo "=== New Credentials ===\n";
echo "Email: admin@example.com\n";
echo "Password: 123456\n\n";

echo "✨ You can now login with the new credentials!\n";
