<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$sale = \App\Models\Sale::with('details')->find(69);

if ($sale) {
    echo "Sale ID: " . $sale->id . "\n";
    echo "Shipping Charge: " . ($sale->shipping_charge ?? 'NULL') . "\n";
    echo "VAT Amount: " . ($sale->vat_amount ?? 'NULL') . "\n";
    echo "Total Amount: " . ($sale->totalAmount ?? 'NULL') . "\n";
    echo "Discount: " . ($sale->discountAmount ?? 'NULL') . "\n";
    echo "Details Count: " . $sale->details->count() . "\n";
    
    echo "\nProducts:\n";
    foreach ($sale->details as $detail) {
        echo "- " . $detail->product->productName . ": " . $detail->price . " x " . $detail->quantities . " = " . ($detail->price * $detail->quantities) . "\n";
    }
} else {
    echo "Sale not found\n";
}
