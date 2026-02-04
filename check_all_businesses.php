<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Business;
use App\Models\Party;
use App\Models\Sale;
use App\Models\SaleDetails;

echo "=== فحص شامل لجميع البيانات ===\n\n";

// Get all businesses
$businesses = Business::all();

echo "عدد الشركات: " . $businesses->count() . "\n";
echo str_repeat("=", 100) . "\n\n";

foreach ($businesses as $business) {
    echo "🏢 الشركة (Business ID: {$business->id}):\n";
    echo str_repeat("-", 100) . "\n";
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
    
    // Get parties for this business
    echo "\n📋 العملاء (Parties):\n";
    echo str_repeat("-", 100) . "\n";
    $parties = Party::where('business_id', $business->id)->get();
    
    if ($parties->isEmpty()) {
        echo "❌ لا يوجد عملاء لهذه الشركة\n";
    } else {
        foreach ($parties as $party) {
            echo "\n  👤 Party ID: {$party->id}\n";
            echo "  Name: {$party->name}\n";
            echo "  Type: {$party->type}\n";
            echo "  ZATCA Type: " . ($party->zatca_type ?? '❌ NOT SET') . "\n";
            echo "  VAT Number: " . ($party->vat_number ?? '❌ NOT SET') . "\n";
            echo "  CR Number: " . ($party->commercial_registration ?? '❌ NOT SET') . "\n";
            echo "  Additional ID: " . ($party->additional_id ?? '❌ NOT SET') . "\n";
            echo "  Building Number: " . ($party->building_number ?? '❌ NOT SET') . "\n";
            echo "  Street: " . ($party->street_name ?? '❌ NOT SET') . "\n";
            echo "  District: " . ($party->district ?? '❌ NOT SET') . "\n";
            echo "  City: " . ($party->city ?? '❌ NOT SET') . "\n";
            echo "  Postal Code: " . ($party->postal_code ?? '❌ NOT SET') . "\n";
            echo "  Country Code: " . ($party->country_code ?? '❌ NOT SET') . "\n";
            echo "  Created At: " . $party->created_at . "\n";
            echo str_repeat("-", 80) . "\n";
        }
    }
    
    // Get sales for this business
    echo "\n💰 الفواتير (Sales):\n";
    echo str_repeat("-", 100) . "\n";
    $sales = Sale::where('business_id', $business->id)->latest()->take(5)->get();
    
    if ($sales->isEmpty()) {
        echo "❌ لا توجد فواتير لهذه الشركة\n";
    } else {
        foreach ($sales as $sale) {
            echo "\n  📄 Sale ID: {$sale->id}\n";
            echo "  Invoice Number: {$sale->invoiceNumber}\n";
            echo "  Invoice Type: " . ($sale->invoice_type ?? 'N/A') . "\n";
            echo "  Party ID: " . ($sale->party_id ?? 'N/A') . "\n";
            
            if ($sale->party_id) {
                $party = Party::find($sale->party_id);
                if ($party) {
                    echo "  Party Name: {$party->name}\n";
                    echo "  Party ZATCA Type: " . ($party->zatca_type ?? 'N/A') . "\n";
                }
            }
            
            echo "  Supply Date: " . ($sale->supply_date ?? '❌ NOT SET') . "\n";
            echo "  PO Number: " . ($sale->po_number ?? '❌ NOT SET') . "\n";
            echo "  Contract Number: " . ($sale->contract_number ?? '❌ NOT SET') . "\n";
            echo "  Payment Terms: " . ($sale->payment_terms ?? '❌ NOT SET') . "\n";
            echo "  Payment Means: " . ($sale->payment_means ?? '❌ NOT SET') . "\n";
            echo "  Shipping Address: " . ($sale->shipping_address_line1 ?? '❌ NOT SET') . "\n";
            echo "  Created At: " . $sale->created_at . "\n";
            
            // Check sale details
            $saleDetails = SaleDetails::where('sale_id', $sale->id)->first();
            if ($saleDetails) {
                echo "\n  📦 Sale Details (First Item):\n";
                echo "  Item Code: " . ($saleDetails->item_code ?? '❌ NOT SET') . "\n";
                echo "  Unit of Measure: " . ($saleDetails->unit_of_measure ?? '❌ NOT SET') . "\n";
                echo "  List Price: " . ($saleDetails->list_price ?? '❌ NOT SET') . "\n";
                echo "  Discount Percent: " . ($saleDetails->discount_percent ?? '❌ NOT SET') . "\n";
                echo "  Net Price: " . ($saleDetails->net_price ?? '❌ NOT SET') . "\n";
                echo "  Tax Per Item: " . ($saleDetails->tax_per_item ?? '❌ NOT SET') . "\n";
            }
            
            echo str_repeat("-", 80) . "\n";
        }
    }
    
    echo "\n" . str_repeat("=", 100) . "\n\n";
}

echo "\n✅ انتهى الفحص الشامل\n";
