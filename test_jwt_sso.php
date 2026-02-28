<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== JWT SSO Token Test ===\n\n";

// The JWT token from Master App
$jwtToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJtYXJrZXRwbGFjZSIsInN1YiI6IjE0MCIsImVtYWlsIjoiZGVtbzJAZ21haWwuY29tIiwibmFtZSI6ImRlbW8iLCJhcHBfaWQiOjEyLCJzdWJzY3JpcHRpb25fZW5kcyI6MTc3NDg4MjUyOCwiaWF0IjoxNzcyMjkwNjI2LCJleHAiOjIwODc2NTA2MjYsImp0aSI6ImNjMGRiZTg1NTllMzBjZGNmYjNkODlkMDM5NTcyMGM5In0.SJeV9vA8eOcwWpH-LSivxRYzmuW9RqDRdwg4PWFq5-kBoSWeQm2jOPf9_JcUsB7wg2zmaX9XbXCipbnK-K6budLXyTBQSOcWOXpdRK1uGvPED3YQDp4u_I8M7ak_lEJB8-TOU3-5A92DGStFn2XOlqnduShsYtvT2kYjeVNaNVzpazRpaFlXyVDxRdOPKvb170QOJRRaWIdR9vr-gJypiYyUGwvEhhImGijCjMcheLruTMiiqbUE3Cu_VrzDz-fvyHkDK44lKgAlX3D2yHFsSVy6O_pSIkK_BBzxvzY2qkL2kqQKYjaIPtdOgGPHbdXxXLBqCtE9pWav_EnSXlswhQ';

// Decode JWT manually to see what's inside
$parts = explode('.', $jwtToken);
$payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);

echo "JWT Payload:\n";
echo str_repeat("-", 80) . "\n";
print_r($payload);

echo "\n" . str_repeat("=", 80) . "\n";
echo "Testing JWT Decryption with SSOService\n";
echo str_repeat("=", 80) . "\n\n";

$ssoService = new App\Services\SSOService();
$data = $ssoService->decryptJWT($jwtToken);

if ($data) {
    echo "✓ JWT Decoded Successfully!\n\n";
    echo "Mapped Data:\n";
    echo str_repeat("-", 80) . "\n";
    foreach ($data as $key => $value) {
        echo sprintf("%-20s: %s\n", $key, $value);
    }
    
    echo "\n" . str_repeat("=", 80) . "\n";
    echo "Testing User Creation\n";
    echo str_repeat("=", 80) . "\n\n";
    
    // Check if user exists
    $existingUser = App\Models\User::where('external_id', $data['user_id'])
        ->where('sso_provider', 'nomuapps')
        ->first();
    
    if ($existingUser) {
        echo "⚠ User already exists:\n";
        echo "  ID: {$existingUser->id}\n";
        echo "  Name: {$existingUser->name}\n";
        echo "  Email: {$existingUser->email}\n";
        echo "  Business ID: {$existingUser->business_id}\n";
        echo "\nDo you want to delete and recreate? (yes/no): ";
        $handle = fopen("php://stdin", "r");
        $answer = trim(fgets($handle));
        fclose($handle);
        
        if (strtolower($answer) === 'yes') {
            if ($existingUser->business_id) {
                $business = App\Models\Business::find($existingUser->business_id);
                if ($business) {
                    echo "  Deleting business...\n";
                    $business->delete();
                }
            }
            echo "  Deleting user...\n";
            $existingUser->delete();
            echo "✓ Deleted!\n\n";
        } else {
            echo "Skipping user creation.\n";
            exit(0);
        }
    }
    
    // Create user
    $user = $ssoService->findOrCreateUser($data);
    
    if ($user) {
        echo "✓ User Created Successfully!\n\n";
        echo "User Details:\n";
        echo str_repeat("-", 80) . "\n";
        echo "ID: {$user->id}\n";
        echo "Name: {$user->name}\n";
        echo "Email: {$user->email}\n";
        echo "Role: {$user->role}\n";
        echo "External ID: {$user->external_id}\n";
        echo "SSO Provider: {$user->sso_provider}\n";
        echo "Business ID: {$user->business_id}\n";
        
        if ($user->business_id) {
            $business = App\Models\Business::find($user->business_id);
            if ($business) {
                echo "\nBusiness Details:\n";
                echo str_repeat("-", 80) . "\n";
                echo "ID: {$business->id}\n";
                echo "Name: {$business->companyName}\n";
                echo "Email: {$business->email}\n";
                echo "Status: {$business->status}\n";
                echo "Subscription Date: {$business->subscriptionDate}\n";
                echo "Will Expire: {$business->will_expire}\n";
                
                if ($business->plan_subscribe_id) {
                    $subscription = App\Models\PlanSubscribe::find($business->plan_subscribe_id);
                    if ($subscription) {
                        echo "\nSubscription Details:\n";
                        echo str_repeat("-", 80) . "\n";
                        echo "ID: {$subscription->id}\n";
                        echo "Plan ID: {$subscription->plan_id}\n";
                        echo "Price: {$subscription->price}\n";
                        echo "Duration: {$subscription->duration} days\n";
                        echo "Payment Status: {$subscription->payment_status}\n";
                        echo "Start Date: {$subscription->service_start_date}\n";
                        echo "End Date: {$subscription->service_end_date}\n";
                    }
                }
            }
        }
        
        echo "\n" . str_repeat("=", 80) . "\n";
        echo "✓ Test Complete!\n";
        echo str_repeat("=", 80) . "\n\n";
        
        echo "You can now login with:\n";
        echo "Email: {$user->email}\n";
        echo "Or use SSO URL:\n";
        echo "https://nomupos.com/sso/auth?token={$jwtToken}\n";
        
    } else {
        echo "✗ User creation failed!\n";
    }
    
} else {
    echo "✗ JWT Decryption Failed!\n";
    echo "Check logs: storage/logs/laravel.log\n";
}

echo "\n";
