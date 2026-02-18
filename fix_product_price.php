<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\Stock;

echo "=== Fixing Product Prices ===\n\n";

// Find the product "first"
$product = Product::where('productName', 'first')
    ->where('business_id', 4)
    ->first();

if (!$product) {
    echo "❌ Product 'first' not found!\n";
    exit;
}

echo "✅ Product found: {$product->productName} (ID: {$product->id})\n\n";

// Get the stock
$stock = Stock::where('product_id', $product->id)->first();

if (!$stock) {
    echo "❌ Stock not found for this product!\n";
    exit;
}

echo "Current Prices:\n";
echo "  Sale Price: {$stock->productSalePrice}\n";
echo "  Wholesale Price: {$stock->productWholeSalePrice}\n";
echo "  Dealer Price: {$stock->productDealerPrice}\n";
echo "  Purchase Price: {$stock->productPurchasePrice}\n";
echo "  Stock: {$stock->productStock}\n\n";

// Update prices
echo "Updating prices...\n";
$stock->update([
    'productSalePrice' => 100,
    'productWholeSalePrice' => 90,
    'productDealerPrice' => 85,
    'productPurchasePrice' => 70,
]);

echo "✅ Prices updated successfully!\n\n";

// Verify
$stock->refresh();
echo "New Prices:\n";
echo "  Sale Price: {$stock->productSalePrice} ✅\n";
echo "  Wholesale Price: {$stock->productWholeSalePrice} ✅\n";
echo "  Dealer Price: {$stock->productDealerPrice} ✅\n";
echo "  Purchase Price: {$stock->productPurchasePrice} ✅\n";
echo "  Stock: {$stock->productStock}\n\n";

echo "=== Done! ===\n";
echo "\nNext Steps:\n";
echo "1. Clear browser cache (Ctrl + Shift + R)\n";
echo "2. Create new B2B sale\n";
echo "3. Select 'first' product\n";
echo "4. Verify price shows as 100 SAR\n";
echo "5. Fill B2B additional fields\n";
echo "6. Save and check invoice\n";
