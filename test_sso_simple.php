<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing SSO with plan_id...\n\n";

try {
    $ssoService = new \App\Services\SSOService();
    
    $testData = [
        'user_id' => 'TEST_' . time(),
        'name' => 'Test User',
        'email' => 'test_' . time() . '@example.com',
        'plan_id' => 1,
        'business_name' => 'Test Business',
        'timestamp' => time()
    ];
    
    echo "Creating user with plan_id: {$testData['plan_id']}\n";
    
    $user = $ssoService->findOrCreateUser($testData);
    
    if ($user) {
        echo "✓ Success! User ID: {$user->id}\n";
        echo "  Business ID: {$user->business_id}\n";
        
        if ($user->business_id) {
            $business = \App\Models\Business::find($user->business_id);
            echo "  Business Name: {$business->companyName}\n";
            echo "  Subscription ID: {$business->plan_subscribe_id}\n";
        }
    } else {
        echo "✗ Failed to create user\n";
    }
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
