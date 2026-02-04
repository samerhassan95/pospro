<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Verifying Business Data After Update ===\n\n";

$business = App\Models\Business::first();

$fields = [
    'Building Number' => $business->building_number,
    'Street Name' => $business->street_name,
    'District' => $business->district,
    'City' => $business->city,
    'Postal Code' => $business->postal_code,
    'Country Code' => $business->country_code,
];

$allFilled = true;

foreach ($fields as $name => $value) {
    $status = $value ? '✅' : '❌';
    $displayValue = $value ?? 'NULL';
    echo "$status $name: $displayValue\n";
    
    if (!$value) {
        $allFilled = false;
    }
}

echo "\n";

if ($allFilled) {
    echo "✅ All fields are filled! Invoice should display correctly now.\n";
} else {
    echo "❌ Some fields are still empty. Please fill them in Settings.\n";
}

echo "\n=== Check Complete ===\n";
