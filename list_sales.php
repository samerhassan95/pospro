<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Sale;

echo "=== Listing All Sales ===\n\n";

$sales = Sale::with(['party', 'business'])
    ->orderBy('id', 'desc')
    ->limit(10)
    ->get();

if ($sales->isEmpty()) {
    echo "❌ No sales found!\n";
    exit;
}

foreach ($sales as $sale) {
    echo "Sale ID: {$sale->id}\n";
    echo "  Invoice: {$sale->invoiceNumber}\n";
    echo "  Type: {$sale->invoice_type}\n";
    echo "  Party: " . ($sale->party ? $sale->party->name : 'N/A') . "\n";
    echo "  Business: {$sale->business->companyName}\n";
    echo "  Total: {$sale->totalAmount}\n";
    echo "  VAT Amount: {$sale->vat_amount}\n";
    echo "  Date: {$sale->saleDate}\n";
    echo "---\n";
}

echo "\n=== Done ===\n";
