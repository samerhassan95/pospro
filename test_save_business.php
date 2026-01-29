<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Business Data Save ===\n\n";

try {
    $business = App\Models\Business::first();
    
    echo "Before Update:\n";
    echo "Building Number: " . ($business->building_number ?? 'NULL') . "\n";
    echo "Street Name: " . ($business->street_name ?? 'NULL') . "\n";
    echo "District: " . ($business->district ?? 'NULL') . "\n";
    echo "City: " . ($business->city ?? 'NULL') . "\n";
    echo "Postal Code: " . ($business->postal_code ?? 'NULL') . "\n";
    echo "Country Code: " . ($business->country_code ?? 'NULL') . "\n\n";
    
    // Try to update
    $updated = $business->update([
        'building_number' => '123',
        'street_name' => 'King Fahd Road',
        'district' => 'Al Olaya',
        'city' => 'Riyadh',
        'postal_code' => '11564',
        'country_code' => 'SA',
    ]);
    
    if ($updated) {
        echo "✅ Update successful!\n\n";
        
        // Refresh from database
        $business->refresh();
        
        echo "After Update:\n";
        echo "Building Number: " . ($business->building_number ?? 'NULL') . "\n";
        echo "Street Name: " . ($business->street_name ?? 'NULL') . "\n";
        echo "District: " . ($business->district ?? 'NULL') . "\n";
        echo "City: " . ($business->city ?? 'NULL') . "\n";
        echo "Postal Code: " . ($business->postal_code ?? 'NULL') . "\n";
        echo "Country Code: " . ($business->country_code ?? 'NULL') . "\n";
    } else {
        echo "❌ Update failed!\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== Test Complete ===\n";
