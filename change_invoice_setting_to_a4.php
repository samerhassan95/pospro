<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get business ID 4
$business = \App\Models\Business::find(4);

if ($business) {
    // Get current settings
    $settings = $business->options ?? [];
    
    // Update invoice setting to A4
    if (isset($settings['invoice-settings'])) {
        $settings['invoice-settings']['value'] = 'a4';
    } else {
        $settings['invoice-settings'] = ['value' => 'a4'];
    }
    
    // Save
    $business->options = $settings;
    $business->save();
    
    echo "✓ Invoice setting changed to A4 for business ID 4\n";
    echo "Current setting: " . ($settings['invoice-settings']['value'] ?? 'not set') . "\n";
} else {
    echo "✗ Business not found\n";
}
