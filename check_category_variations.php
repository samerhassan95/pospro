<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Category;

$category = Category::where('categoryName', 'T-Shirt')->first();

if ($category) {
    echo "Category found: " . $category->categoryName . "\n";
    echo "ID: " . $category->id . "\n";
    echo "variationColor: " . $category->variationColor . "\n";
    echo "variationSize: " . $category->variationSize . "\n";
    echo "custom_variations: " . json_encode($category->custom_variations) . "\n";
    echo "\nRaw data:\n";
    print_r($category->toArray());
} else {
    echo "Category not found\n";
}
