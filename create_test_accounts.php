<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Business;
use App\Models\Plan;
use App\Models\PlanSubscribe;
use Carbon\Carbon;

echo "=== Creating Test Accounts for Plan Testing ===\n\n";

// Get plans
$planA = Plan::where('subscriptionName', 'A')->first();
$planB = Plan::where('subscriptionName', 'B')->first();
$planC = Plan::where('subscriptionName', 'C')->first();

if (!$planA || !$planB || !$planC) {
    echo "❌ Plans not found! Please run update_plans_to_abc.php first.\n";
    exit(1);
}

echo "Found Plans:\n";
echo "  - Plan A (ID: {$planA->id})\n";
echo "  - Plan B (ID: {$planB->id})\n";
echo "  - Plan C (ID: {$planC->id})\n\n";

// Test accounts data
$testAccounts = [
    [
        'plan' => $planA,
        'email' => 'test-plan-a@example.com',
        'password' => 'password123',
        'company' => 'Test Company A (Basic Plan)',
        'phone' => '0501234567',
    ],
    [
        'plan' => $planB,
        'email' => 'test-plan-b@example.com',
        'password' => 'password123',
        'company' => 'Test Company B (Professional Plan)',
        'phone' => '0501234568',
    ],
    [
        'plan' => $planC,
        'email' => 'test-plan-c@example.com',
        'password' => 'password123',
        'company' => 'Test Company C (Enterprise Plan)',
        'phone' => '0501234569',
    ],
];

foreach ($testAccounts as $accountData) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Creating account for Plan {$accountData['plan']->subscriptionName}...\n";
    
    // Check if user already exists
    $existingUser = User::where('email', $accountData['email'])->first();
    if ($existingUser) {
        echo "⚠️  User already exists. Deleting old account...\n";
        
        // Delete old business and related data
        if ($existingUser->business_id) {
            $business = Business::find($existingUser->business_id);
            if ($business) {
                // Delete plan subscription
                PlanSubscribe::where('business_id', $business->id)->delete();
                
                // Delete business
                $business->delete();
            }
        }
        
        // Delete user
        $existingUser->delete();
    }
    
    // Create Business
    $business = Business::create([
        'companyName' => $accountData['company'],
        'phoneNumber' => $accountData['phone'],
        'address' => 'Test Address',
        'subscriptionDate' => Carbon::now(),
        'will_expire' => Carbon::now()->addYear(),
        'status' => 1,
        'business_category_id' => 1, // Default category
    ]);
    
    echo "✓ Business created (ID: {$business->id})\n";
    
    // Create Plan Subscription
    $planSubscribe = PlanSubscribe::create([
        'business_id' => $business->id,
        'plan_id' => $accountData['plan']->id,
        'price' => $accountData['plan']->subscriptionPrice,
        'duration' => $accountData['plan']->duration,
        'will_expire' => Carbon::now()->addYear(),
        'payment_type' => 'manual',
        'status' => 1,
        'allow_multibranch' => $accountData['plan']->allow_multibranch,
    ]);
    
    echo "✓ Plan subscription created (ID: {$planSubscribe->id})\n";
    
    // Update business with plan_subscribe_id
    $business->update([
        'plan_subscribe_id' => $planSubscribe->id,
    ]);
    
    // Create User
    $user = User::create([
        'name' => 'Test User ' . $accountData['plan']->subscriptionName,
        'email' => $accountData['email'],
        'password' => Hash::make($accountData['password']),
        'business_id' => $business->id,
        'role' => 2, // Business user role
        'status' => 1,
    ]);
    
    echo "✓ User created (ID: {$user->id})\n";
    
    // Assign default role/permissions (if using Spatie)
    try {
        $user->assignRole('Business Owner');
    } catch (\Exception $e) {
        echo "⚠️  Could not assign role (might not exist)\n";
    }
    
    echo "\n📋 Account Details:\n";
    echo "   Email: {$accountData['email']}\n";
    echo "   Password: {$accountData['password']}\n";
    echo "   Company: {$accountData['company']}\n";
    echo "   Plan: {$accountData['plan']->subscriptionName}\n";
    echo "   Expires: " . Carbon::now()->addYear()->format('Y-m-d') . "\n";
    echo "\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ All test accounts created successfully!\n\n";

echo "📝 Summary:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "Plan A (Basic) Account:\n";
echo "  Email: test-plan-a@example.com\n";
echo "  Password: password123\n";
echo "  Features: Basic features, 1 warehouse, 1 branch\n";
echo "  No access to: Due List, Finance, Commission, HRM\n\n";

echo "Plan B (Professional) Account:\n";
echo "  Email: test-plan-b@example.com\n";
echo "  Password: password123\n";
echo "  Features: All features except POS App & Store\n";
echo "  Unlimited warehouses and branches\n\n";

echo "Plan C (Enterprise) Account:\n";
echo "  Email: test-plan-c@example.com\n";
echo "  Password: password123\n";
echo "  Features: ALL features including POS App & Store\n";
echo "  Unlimited warehouses and branches\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🎯 Next Steps:\n";
echo "1. Login with any of the accounts above\n";
echo "2. Check the sidebar - you'll see different menus based on plan\n";
echo "3. Try accessing restricted features\n";
echo "4. Try adding warehouses/branches to test limits\n\n";

echo "💡 Tips:\n";
echo "- Plan A users won't see: Due List, Finance, Commission, HRM\n";
echo "- Plan A users can only create 1 warehouse and 1 branch\n";
echo "- Plan B users have everything except POS App & Store\n";
echo "- Plan C users have full access to everything\n\n";
