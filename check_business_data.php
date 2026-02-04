<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Business;
use App\Models\Party;
use App\Models\Sale;

echo "=== فحص بيانات الشركة والعملاء ===\n\n";

// Get current business
$businessId = 1; // غير الرقم ده لو محتاج business تاني
$business = Business::find($businessId);

if (!$business) {
    echo "❌ الشركة غير موجودة!\n";
    exit;
}

echo "معلومات الشركة (Business ID: {$businessId}):\n";
echo str_repeat("-", 80) . "\n";
echo "Company Name: " . ($business->companyName ?? 'N/A') . "\n";
echo "VAT Number: " . ($business->vat_no ?? 'N/A') . "\n";
echo "CR Number: " . ($business->commercial_registration ?? '❌ NOT SET') . "\n";
echo "Additional ID: " . ($business->additional_id ?? '❌ NOT SET') . "\n";
echo "Bank Name: " . ($business->bank_name ?? '❌ NOT SET') . "\n";
echo "Bank Account: " . ($business->bank_account_number ?? '❌ NOT SET') . "\n";
echo "Building Number: " . ($business->building_number ?? 'N/A') . "\n";
echo "Street: " . ($business->street_name ?? 'N/A') . "\n";
echo "District: " . ($business->district ?? 'N/A') . "\n";
echo "City: " . ($business->city ?? 'N/A') . "\n";
echo "Postal Code: " . ($business->postal_code ?? 'N/A') . "\n";
echo "Country Code: " . ($business->country_code ?? 'N/A') . "\n";

echo "\n" . str_repeat("=", 80) . "\n\n";

// Get parties
echo "العملاء (Parties):\n";
echo str_repeat("-", 80) . "\n";
$parties = Party::where('business_id', $businessId)->get();

foreach ($parties as $party) {
    echo "\nParty ID: {$party->id}\n";
    echo "Name: {$party->name}\n";
    echo "Type: {$party->type}\n";
    echo "ZATCA Type: " . ($party->zatca_type ?? 'N/A') . "\n";
    echo "VAT Number: " . ($party->vat_number ?? 'N/A') . "\n";
    echo "CR Number: " . ($party->commercial_registration ?? '❌ NOT SET') . "\n";
    echo "Additional ID: " . ($party->additional_id ?? '❌ NOT SET') . "\n";
    echo str_repeat("-", 40) . "\n";
}

echo "\n" . str_repeat("=", 80) . "\n\n";

// Check latest sale
echo "آخر فاتورة:\n";
echo str_repeat("-", 80) . "\n";
$sale = Sale::where('business_id', $businessId)->latest()->first();

if ($sale) {
    echo "Sale ID: {$sale->id}\n";
    echo "Invoice Number: {$sale->invoiceNumber}\n";
    echo "Invoice Type: " . ($sale->invoice_type ?? 'N/A') . "\n";
    echo "Supply Date: " . ($sale->supply_date ?? '❌ NOT SET') . "\n";
    echo "PO Number: " . ($sale->po_number ?? '❌ NOT SET') . "\n";
    echo "Contract Number: " . ($sale->contract_number ?? '❌ NOT SET') . "\n";
    echo "Payment Terms: " . ($sale->payment_terms ?? '❌ NOT SET') . "\n";
    echo "Payment Means: " . ($sale->payment_means ?? '❌ NOT SET') . "\n";
    echo "Shipping Address: " . ($sale->shipping_address_line1 ?? '❌ NOT SET') . "\n";
} else {
    echo "❌ لا توجد فواتير!\n";
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "\n✅ انتهى الفحص\n";
