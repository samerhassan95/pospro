<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Sale;

echo "=== Checking QR Code ===\n\n";

$sale = Sale::find(45);

if (!$sale) {
    echo "❌ Sale not found!\n";
    exit;
}

echo "Sale ID: {$sale->id}\n";
echo "Invoice: {$sale->invoiceNumber}\n\n";

echo "QR Code Field:\n";
if ($sale->qr_code) {
    echo "✅ QR Code exists\n";
    echo "Length: " . strlen($sale->qr_code) . " characters\n";
    echo "First 50 chars: " . substr($sale->qr_code, 0, 50) . "...\n";
} else {
    echo "❌ QR Code is NULL or empty\n";
}

echo "\n=== Done ===\n";
