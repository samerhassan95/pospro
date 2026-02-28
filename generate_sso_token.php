<?php

echo "=== SSO Token Generator ===\n\n";

$secret = '6856fe49d4f8bc0830199edaefe41fa7922d0a323b07ea216dfbc96ce8257cca';

// Ask user for input
echo "Choose scenario:\n";
echo "1. Create user with Plan A (7 days, Free)\n";
echo "2. Create user with Plan B (30 days, 10 SAR)\n";
echo "3. Create user with Plan C (180 days, 60 SAR)\n";
echo "4. Create user for existing business\n";
echo "5. Create admin user\n";
echo "6. Custom data\n";
echo "\nEnter choice (1-6): ";

$handle = fopen("php://stdin", "r");
$choice = trim(fgets($handle));

$data = [];

switch ($choice) {
    case '1':
        $data = [
            'user_id' => 'PLAN_A_' . time(),
            'name' => 'مستخدم باقة A',
            'email' => 'plan_a_' . time() . '@test.com',
            'plan_id' => 1,
            'business_name' => 'متجر تجريبي A',
            'phone' => '0501234567',
            'locale' => 'ar',
            'timestamp' => time(),
        ];
        break;
    
    case '2':
        $data = [
            'user_id' => 'PLAN_B_' . time(),
            'name' => 'مستخدم باقة B',
            'email' => 'plan_b_' . time() . '@test.com',
            'plan_id' => 2,
            'business_name' => 'متجر تجريبي B',
            'phone' => '0501234567',
            'vat_no' => '300123456789003',
            'commercial_registration' => '1234567890',
            'locale' => 'ar',
            'timestamp' => time(),
        ];
        break;
    
    case '3':
        $data = [
            'user_id' => 'PLAN_C_' . time(),
            'name' => 'مستخدم باقة C',
            'email' => 'plan_c_' . time() . '@test.com',
            'plan_id' => 3,
            'business_name' => 'متجر تجريبي C',
            'phone' => '0501234567',
            'vat_no' => '300123456789003',
            'commercial_registration' => '1234567890',
            'building_number' => '1234',
            'street_name' => 'شارع الملك فهد',
            'district' => 'العليا',
            'city' => 'الرياض',
            'postal_code' => '12345',
            'country_code' => 'SA',
            'locale' => 'ar',
            'timestamp' => time(),
        ];
        break;
    
    case '4':
        echo "Enter existing business_id: ";
        $business_id = trim(fgets($handle));
        $data = [
            'user_id' => 'EXISTING_BIZ_' . time(),
            'name' => 'مستخدم لعمل موجود',
            'email' => 'existing_' . time() . '@test.com',
            'business_id' => (int)$business_id,
            'timestamp' => time(),
        ];
        break;
    
    case '5':
        $data = [
            'user_id' => 'ADMIN_' . time(),
            'name' => 'Admin User',
            'email' => 'admin_' . time() . '@test.com',
            'role' => 'admin',
            'timestamp' => time(),
        ];
        break;
    
    case '6':
        echo "\nEnter user_id: ";
        $user_id = trim(fgets($handle));
        echo "Enter name: ";
        $name = trim(fgets($handle));
        echo "Enter email: ";
        $email = trim(fgets($handle));
        echo "Enter plan_id (1-3, or leave empty): ";
        $plan_id = trim(fgets($handle));
        
        $data = [
            'user_id' => $user_id ?: 'CUSTOM_' . time(),
            'name' => $name ?: 'Custom User',
            'email' => $email ?: 'custom_' . time() . '@test.com',
            'timestamp' => time(),
        ];
        
        if (!empty($plan_id)) {
            $data['plan_id'] = (int)$plan_id;
            echo "Enter business_name: ";
            $business_name = trim(fgets($handle));
            if (!empty($business_name)) {
                $data['business_name'] = $business_name;
            }
        }
        break;
    
    default:
        echo "Invalid choice!\n";
        exit(1);
}

fclose($handle);

// Generate token
$json = json_encode($data);
$iv = substr(hash('sha256', $secret), 0, 16);
$encrypted = openssl_encrypt($json, 'AES-256-CBC', $secret, 0, $iv);
$signature = hash_hmac('sha256', $encrypted, $secret);
$token = base64_encode($encrypted . '::' . $signature);

echo "\n" . str_repeat("=", 80) . "\n";
echo "✓ Token Generated Successfully!\n";
echo str_repeat("=", 80) . "\n\n";

echo "User Data:\n";
echo str_repeat("-", 80) . "\n";
foreach ($data as $key => $value) {
    echo sprintf("%-20s: %s\n", $key, $value);
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "Token (copy this):\n";
echo str_repeat("=", 80) . "\n";
echo $token . "\n";

echo "\n" . str_repeat("=", 80) . "\n";
echo "Full URL (for browser):\n";
echo str_repeat("=", 80) . "\n";
echo "http://127.0.0.1:8000/sso/login?token=" . urlencode($token) . "\n";

echo "\n" . str_repeat("=", 80) . "\n";
echo "Postman Instructions:\n";
echo str_repeat("=", 80) . "\n";
echo "Method: GET\n";
echo "URL: http://127.0.0.1:8000/sso/login?token=YOUR_TOKEN\n";
echo "\nOR\n\n";
echo "Method: POST\n";
echo "URL: http://127.0.0.1:8000/sso/login\n";
echo "Headers: Content-Type: application/json\n";
echo "Body (JSON):\n";
echo "{\n";
echo "    \"token\": \"" . $token . "\"\n";
echo "}\n";

echo "\n" . str_repeat("=", 80) . "\n";
echo "cURL Command:\n";
echo str_repeat("=", 80) . "\n";
echo "curl -X GET \"http://127.0.0.1:8000/sso/login?token=" . urlencode($token) . "\" -L\n";

echo "\n";
