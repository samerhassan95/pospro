<?php

/**
 * Fix Missing Logos and Images
 * 
 * This script checks for missing logo files and resets them to default values
 * Run: php fix_missing_logos.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Option;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

echo "=== Fixing Missing Logos and Images ===\n\n";

// Get general settings
$option = Option::where('key', 'general')->first();

if (!$option) {
    echo "❌ General settings not found!\n";
    exit(1);
}

$value = $option->value;
$updated = false;

// Define logo fields and their default values
$logoFields = [
    'logo' => 'assets/images/Logo.png',
    'admin_logo' => 'assets/images/Logo.png',
    'common_header_logo' => 'assets/images/Logo.png',
    'footer_logo' => 'assets/images/Logo.png',
    'favicon' => 'favicon.ico',
    'login_page_logo' => 'assets/images/Logo.png',
    'login_page_image' => 'assets/images/login.png',
];

echo "Checking logo files...\n\n";

foreach ($logoFields as $field => $defaultPath) {
    $currentPath = $value[$field] ?? null;
    
    if (!$currentPath) {
        echo "⚠️  {$field}: Not set, using default\n";
        $value[$field] = $defaultPath;
        $updated = true;
        continue;
    }
    
    $fullPath = public_path($currentPath);
    
    if (!File::exists($fullPath)) {
        echo "❌ {$field}: File not found at '{$currentPath}'\n";
        echo "   → Resetting to default: '{$defaultPath}'\n";
        $value[$field] = $defaultPath;
        $updated = true;
    } else {
        echo "✅ {$field}: OK - '{$currentPath}'\n";
    }
}

if ($updated) {
    echo "\n📝 Updating database...\n";
    $option->value = $value;
    $option->save();
    
    echo "✅ Database updated successfully!\n\n";
    
    // Clear cache
    echo "🧹 Clearing cache...\n";
    Cache::flush();
    \Artisan::call('view:clear');
    
    echo "✅ Cache cleared!\n\n";
    echo "✨ All done! Please refresh your browser (Ctrl+F5)\n";
} else {
    echo "\n✅ All logos are OK! No changes needed.\n";
}

echo "\n=== Summary ===\n";
echo "Current logo paths:\n";
foreach ($logoFields as $field => $defaultPath) {
    echo "  - {$field}: {$value[$field]}\n";
}

echo "\n";
