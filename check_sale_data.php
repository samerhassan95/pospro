<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Sale;
use App\Models\SaleDetails;

echo "=== Checking Sale Data ===\n\n";

// Get Sale ID 45
$sale = Sale::with(['details', 'vat', 'business', 'party'])->find(45);

if (!$sale) {
    echo "❌ Sale ID 45 not found!\n";
    exit;
}

echo "✅ Sale ID: {$sale->id}\n";
echo "Invoice Number: {$sale->invoiceNumber}\n";
echo "Invoice Type: {$sale->invoice_type}\n";
echo "Total Amount: {$sale->totalAmount}\n";
echo "VAT Amount: {$sale->vat_amount}\n";
echo "Discount Amount: {$sale->discountAmount}\n";
echo "Shipping Charge: {$sale->shipping_charge}\n";
echo "Paid Amount: {$sale->paidAmount}\n";
echo "Due Amount: {$sale->dueAmount}\n\n";

echo "=== VAT Info ===\n";
if ($sale->vat) {
    echo "VAT ID: {$sale->vat->id}\n";
    echo "VAT Name: {$sale->vat->name}\n";
    echo "VAT Rate: {$sale->vat->rate}%\n";
} else {
    echo "❌ No VAT relationship found!\n";
}
echo "\n";

echo "=== Business Info ===\n";
echo "Company Name: {$sale->business->companyName}\n";
echo "VAT Number: {$sale->business->vat_no}\n";
echo "CR Number: {$sale->business->commercial_registration}\n";
echo "Additional ID: {$sale->business->additional_id}\n";
echo "Bank Name: {$sale->business->bank_name}\n";
echo "Bank Account: {$sale->business->bank_account_number}\n\n";

echo "=== Party Info ===\n";
if ($sale->party) {
    echo "Party Name: {$sale->party->name}\n";
    echo "ZATCA Type: {$sale->party->zatca_type}\n";
    echo "VAT Number: {$sale->party->vat_number}\n";
    echo "CR Number: {$sale->party->commercial_registration}\n";
    echo "Additional ID: {$sale->party->additional_id}\n";
} else {
    echo "❌ No party found!\n";
}
echo "\n";

echo "=== Sale Details ===\n";
foreach ($sale->details as $index => $detail) {
    echo "Item " . ($index + 1) . ":\n";
    echo "  Product: {$detail->product->productName}\n";
    echo "  Quantity: {$detail->quantities}\n";
    echo "  Price: {$detail->price}\n";
    echo "  Subtotal: " . ($detail->price * $detail->quantities) . "\n";
}
echo "\n";

echo "=== B2B Additional Fields ===\n";
echo "Supply Date: " . ($sale->supply_date ?? 'NOT SET') . "\n";
echo "PO Number: " . ($sale->po_number ?? 'NOT SET') . "\n";
echo "Contract Number: " . ($sale->contract_number ?? 'NOT SET') . "\n";
echo "Payment Terms: " . ($sale->payment_terms ?? 'NOT SET') . "\n";
echo "Payment Means: " . ($sale->payment_means ?? 'NOT SET') . "\n";
echo "Shipping Address: " . ($sale->shipping_address ?? 'NOT SET') . "\n";

echo "\n=== Done ===\n";
