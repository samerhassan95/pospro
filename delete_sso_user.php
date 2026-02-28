<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Delete SSO User ===\n\n";

$email = 'demo2@gmail.com';

$user = App\Models\User::where('email', $email)->first();

if (!$user) {
    echo "✗ User not found: {$email}\n";
    exit(0);
}

echo "Found User:\n";
echo "  ID: {$user->id}\n";
echo "  Name: {$user->name}\n";
echo "  Email: {$user->email}\n";
echo "  Role: {$user->role}\n";
echo "  Business ID: {$user->business_id}\n\n";

// Delete business if exists
if ($user->business_id) {
    $business = App\Models\Business::find($user->business_id);
    if ($business) {
        echo "Deleting Business:\n";
        echo "  ID: {$business->id}\n";
        echo "  Name: {$business->companyName}\n";
        
        // Delete subscription if exists
        if ($business->plan_subscribe_id) {
            $subscription = App\Models\PlanSubscribe::find($business->plan_subscribe_id);
            if ($subscription) {
                echo "  Deleting Subscription ID: {$subscription->id}\n";
                $subscription->delete();
            }
        }
        
        echo "  Deleting Business...\n";
        $business->delete();
        echo "  ✓ Business deleted\n\n";
    }
}

echo "Deleting User...\n";
$user->delete();
echo "✓ User deleted\n\n";

echo "Done! You can now test SSO from scratch.\n";
