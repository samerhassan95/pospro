<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Sale;
use App\Models\Party;
use App\Models\Business;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Vat;

echo "=== B2B System Diagnostic Tool ===\n\n";

// Check Business Data
echo "1. Checking Business Data...\n";
$business = Business::find(4);
if ($business) {
    echo "   ✅ Business Found: {$business->companyName}\n";
    echo "   - VAT Number: " . ($business->vat_no ? '✅' : '❌') . "\n";
    echo "   - CR Number: " . ($business->commercial_registration ? '✅' : '❌') . "\n";
    echo "   - Bank Name: " . ($business->bank_name ? '✅' : '❌') . "\n";
    echo "   - Bank Account: " . ($business->bank_account_number ? '✅' : '❌') . "\n";
} else {
    echo "   ❌ Business not found!\n";
}
echo "\n";

// Check B2B Party
echo "2. Checking B2B Party...\n";
$party = Party::find(28);
if ($party) {
    echo "   ✅ Party Found: {$party->name}\n";
    echo "   - ZATCA Type: " . ($party->zatca_type === 'b2b' ? '✅ B2B' : '❌ Not B2B') . "\n";
    echo "   - VAT Number: " . ($party->vat_number ? '✅' : '❌') . "\n";
    echo "   - CR Number: " . ($party->commercial_registration ? '✅' : '❌') . "\n";
} else {
    echo "   ❌ Party not found!\n";
}
echo "\n";

// Check Products with Prices
echo "3. Checking Products with Valid Prices...\n";
$products = Product::where('business_id', 4)
    ->with('stocks')
    ->limit(5)
    ->get();

if ($products->isEmpty()) {
    echo "   ❌ No products found!\n";
} else {
    foreach ($products as $product) {
        $stock = $product->stocks->first();
        if ($stock) {
            $hasPrice = $stock->productSalePrice > 0;
            $hasStock = $stock->productStock > 0;
            echo "   " . ($hasPrice && $hasStock ? '✅' : '⚠️') . " {$product->productName}\n";
            echo "      - Sale Price: {$stock->productSalePrice}\n";
            echo "      - Stock: {$stock->productStock}\n";
        }
    }
}
echo "\n";

// Check VAT Settings
echo "4. Checking VAT Settings...\n";
$vat = Vat::where('business_id', 4)->where('rate', 15)->first();
if ($vat) {
    echo "   ✅ VAT 15% Found: {$vat->name}\n";
} else {
    echo "   ❌ VAT 15% not found!\n";
}
echo "\n";

// Check Recent B2B Sales
echo "5. Checking Recent B2B Sales...\n";
$sales = Sale::where('business_id', 4)
    ->where('invoice_type', 'b2b')
    ->with(['party', 'details'])
    ->orderBy('id', 'desc')
    ->limit(3)
    ->get();

if ($sales->isEmpty()) {
    echo "   ⚠️ No B2B sales found\n";
} else {
    foreach ($sales as $sale) {
        $hasPrice = $sale->details->sum('price') > 0;
        $hasVat = $sale->vat_amount > 0;
        $hasB2BFields = !empty($sale->po_number) || !empty($sale->supply_date);
        
        echo "   " . ($hasPrice && $hasVat && $hasB2BFields ? '✅' : '⚠️') . " {$sale->invoiceNumber}\n";
        echo "      - Total: {$sale->totalAmount}\n";
        echo "      - VAT Amount: {$sale->vat_amount}\n";
        echo "      - Has B2B Fields: " . ($hasB2BFields ? 'Yes' : 'No') . "\n";
        echo "      - Product Prices: " . ($hasPrice ? 'Valid' : 'Zero!') . "\n";
    }
}
echo "\n";

// Summary
echo "=== Summary ===\n";
$businessOk = $business && $business->vat_no && $business->commercial_registration;
$partyOk = $party && $party->zatca_type === 'b2b' && $party->vat_number;
$productsOk = $products->isNotEmpty() && $products->first()->stocks->first()->productSalePrice > 0;
$vatOk = $vat !== null;

if ($businessOk && $partyOk && $productsOk && $vatOk) {
    echo "✅ System is ready for B2B invoices!\n";
    echo "\nNext Steps:\n";
    echo "1. Clear browser cache (Ctrl + Shift + R)\n";
    echo "2. Create new sale with B2B customer\n";
    echo "3. Select product with valid price\n";
    echo "4. Fill B2B additional fields\n";
    echo "5. Save and verify invoice\n";
} else {
    echo "⚠️ Some issues found:\n";
    if (!$businessOk) echo "   - Business data incomplete\n";
    if (!$partyOk) echo "   - B2B party data incomplete\n";
    if (!$productsOk) echo "   - Products have zero prices\n";
    if (!$vatOk) echo "   - VAT 15% not configured\n";
}

echo "\n=== Done ===\n";
