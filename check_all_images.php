<?php

/**
 * Check All Images Status
 * 
 * This script checks the status of all images in the system
 * Run: php check_all_images.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Option;
use App\Models\Category;
use App\Models\Product;
use App\Models\Business;
use Illuminate\Support\Facades\File;

echo "=== Checking All Images Status ===\n\n";

// 1. Check General Settings Logos
echo "1. GENERAL SETTINGS LOGOS\n";
echo str_repeat("-", 50) . "\n";

$option = Option::where('key', 'general')->first();
if ($option) {
    $value = $option->value;
    $logoFields = [
        'logo', 'admin_logo', 'common_header_logo', 'footer_logo',
        'favicon', 'login_page_logo', 'login_page_image'
    ];
    
    foreach ($logoFields as $field) {
        $path = $value[$field] ?? null;
        if ($path) {
            $exists = File::exists(public_path($path));
            $status = $exists ? "✅" : "❌";
            echo "{$status} {$field}: {$path}\n";
        } else {
            echo "⚠️  {$field}: Not set\n";
        }
    }
} else {
    echo "❌ General settings not found!\n";
}

// 2. Check Category Icons
echo "\n2. CATEGORY ICONS\n";
echo str_repeat("-", 50) . "\n";

$categories = Category::whereNotNull('icon')->get();
$totalCategories = $categories->count();
$missingIcons = 0;

foreach ($categories as $category) {
    if (!File::exists(public_path($category->icon))) {
        echo "❌ Category '{$category->categoryName}': {$category->icon}\n";
        $missingIcons++;
    }
}

if ($missingIcons == 0) {
    echo "✅ All {$totalCategories} category icons are OK!\n";
} else {
    echo "⚠️  {$missingIcons} out of {$totalCategories} category icons are missing\n";
}

// 3. Check Product Images
echo "\n3. PRODUCT IMAGES\n";
echo str_repeat("-", 50) . "\n";

$products = Product::whereNotNull('productPicture')->get();
$totalProducts = $products->count();
$missingImages = 0;

foreach ($products as $product) {
    if (!File::exists(public_path($product->productPicture))) {
        $missingImages++;
    }
}

if ($missingImages == 0) {
    echo "✅ All {$totalProducts} product images are OK!\n";
} else {
    echo "⚠️  {$missingImages} out of {$totalProducts} product images are missing\n";
    echo "   (Run with --verbose to see details)\n";
}

// 4. Check Business Logos
echo "\n4. BUSINESS LOGOS (Invoice Logos)\n";
echo str_repeat("-", 50) . "\n";

$businesses = Business::all();
$totalBusinesses = $businesses->count();
$missingBusinessLogos = 0;

foreach ($businesses as $business) {
    $settings = Option::where('key', 'business-settings')
        ->whereJsonContains('value->business_id', $business->id)
        ->first();
    
    if ($settings) {
        $logoPath = $settings->value['invoice_logo'] ?? $settings->value['logo'] ?? null;
        if ($logoPath && !File::exists(public_path($logoPath))) {
            echo "❌ Business '{$business->companyName}': {$logoPath}\n";
            $missingBusinessLogos++;
        }
    }
}

if ($missingBusinessLogos == 0) {
    echo "✅ All {$totalBusinesses} business logos are OK!\n";
} else {
    echo "⚠️  {$missingBusinessLogos} out of {$totalBusinesses} business logos are missing\n";
}

// Summary
echo "\n" . str_repeat("=", 50) . "\n";
echo "SUMMARY\n";
echo str_repeat("=", 50) . "\n";
echo "General Logos: Check above\n";
echo "Category Icons: " . ($missingIcons == 0 ? "✅ OK" : "⚠️  {$missingIcons} missing") . "\n";
echo "Product Images: " . ($missingImages == 0 ? "✅ OK" : "⚠️  {$missingImages} missing") . "\n";
echo "Business Logos: " . ($missingBusinessLogos == 0 ? "✅ OK" : "⚠️  {$missingBusinessLogos} missing") . "\n";

echo "\n";
