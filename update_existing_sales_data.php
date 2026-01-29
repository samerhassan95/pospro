<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Sale;
use App\Models\SaleDetails;
use App\Models\Product;

echo "=== تحديث بيانات الفواتير الموجودة ===\n\n";

// Update sale details with missing fields
$saleDetails = SaleDetails::with('product')->get();
$updated = 0;

foreach ($saleDetails as $detail) {
    $needsUpdate = false;
    $updates = [];
    
    // Add item code if missing
    if (empty($detail->item_code) && $detail->product) {
        $updates['item_code'] = $detail->product->productCode ?? $detail->product->id;
        $needsUpdate = true;
    }
    
    // Add unit of measure if missing
    if (empty($detail->unit_of_measure)) {
        if ($detail->product && $detail->product->unit) {
            $updates['unit_of_measure'] = $detail->product->unit->name;
        } else {
            $updates['unit_of_measure'] = 'PCS';
        }
        $needsUpdate = true;
    }
    
    // Add list price if missing
    if (empty($detail->list_price)) {
        $updates['list_price'] = $detail->price;
        $needsUpdate = true;
    }
    
    // Add net price if missing
    if (empty($detail->net_price)) {
        $updates['net_price'] = $detail->price;
        $needsUpdate = true;
    }
    
    // Calculate discount percent if there's a discount
    if (empty($detail->discount_percent) && $detail->discount > 0) {
        $lineTotal = $detail->price * $detail->quantities;
        if ($lineTotal > 0) {
            $updates['discount_percent'] = ($detail->discount / $lineTotal) * 100;
            $needsUpdate = true;
        }
    }
    
    // Calculate tax per item if missing
    if (empty($detail->tax_per_item)) {
        $sale = $detail->sale;
        if ($sale) {
            $vatRate = $sale->vat_percent ?? 15;
            $lineTotal = $detail->price * $detail->quantities;
            $updates['tax_per_item'] = $lineTotal * $vatRate / 100;
            $needsUpdate = true;
        }
    }
    
    if ($needsUpdate) {
        $detail->update($updates);
        $updated++;
        echo "✓ تم تحديث تفاصيل المنتج ID: {$detail->id}\n";
    }
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "تم تحديث {$updated} سجل من تفاصيل المنتجات\n";

// Update sales with supply_date if missing
$sales = Sale::whereNull('supply_date')->get();
$salesUpdated = 0;

foreach ($sales as $sale) {
    $sale->update([
        'supply_date' => $sale->saleDate
    ]);
    $salesUpdated++;
}

echo "تم تحديث {$salesUpdated} فاتورة بتاريخ التوريد\n";

echo "\n=== اكتمل التحديث ===\n";
echo "\nملاحظة: لا تنسى تحديث:\n";
echo "1. معلومات الشركة (Settings → General):\n";
echo "   - رقم السجل التجاري (CR Number)\n";
echo "   - معلومات البنك (Bank Name & Account)\n";
echo "\n2. معلومات العملاء (Parties → Edit):\n";
echo "   - رقم السجل التجاري (CR Number)\n";
echo "   - المعرف الإضافي (Additional ID)\n";
