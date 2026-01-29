<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking B2B Data ===\n\n";

// Check Business Data
echo "1. BUSINESS (Seller) DATA:\n";
echo str_repeat("-", 50) . "\n";

$business = App\Models\Business::first();

if ($business) {
    echo "ID: " . $business->id . "\n";
    echo "Company Name: " . ($business->companyName ?? 'NULL') . "\n";
    echo "VAT Number: " . ($business->vat_no ?? 'NULL') . "\n";
    echo "Building Number: " . ($business->building_number ?? 'NULL') . "\n";
    echo "Street Name: " . ($business->street_name ?? 'NULL') . "\n";
    echo "District: " . ($business->district ?? 'NULL') . "\n";
    echo "City: " . ($business->city ?? 'NULL') . "\n";
    echo "Postal Code: " . ($business->postal_code ?? 'NULL') . "\n";
    echo "Country Code: " . ($business->country_code ?? 'NULL') . "\n";
    echo "Phone: " . ($business->phoneNumber ?? 'NULL') . "\n";
    echo "Email: " . ($business->email ?? 'NULL') . "\n";
} else {
    echo "No business found!\n";
}

echo "\n";

// Check Party Data (B2B Customers)
echo "2. PARTIES (Buyers) DATA:\n";
echo str_repeat("-", 50) . "\n";

$parties = App\Models\Party::where('zatca_type', 'b2b')->get();

if ($parties->count() > 0) {
    foreach ($parties as $party) {
        echo "\nParty ID: " . $party->id . "\n";
        echo "Name: " . ($party->name ?? 'NULL') . "\n";
        echo "ZATCA Type: " . ($party->zatca_type ?? 'NULL') . "\n";
        echo "VAT Number: " . ($party->vat_number ?? 'NULL') . "\n";
        echo "Building Number: " . ($party->building_number ?? 'NULL') . "\n";
        echo "Street Name: " . ($party->street_name ?? 'NULL') . "\n";
        echo "District: " . ($party->district ?? 'NULL') . "\n";
        echo "City: " . ($party->city ?? 'NULL') . "\n";
        echo "Postal Code: " . ($party->postal_code ?? 'NULL') . "\n";
        echo "Country Code: " . ($party->country_code ?? 'NULL') . "\n";
        echo "Phone: " . ($party->phone ?? 'NULL') . "\n";
        echo str_repeat("-", 30) . "\n";
    }
} else {
    echo "No B2B parties found!\n";
}

echo "\n";

// Check if columns exist
echo "3. DATABASE COLUMNS CHECK:\n";
echo str_repeat("-", 50) . "\n";

try {
    $businessColumns = DB::select("SHOW COLUMNS FROM businesses WHERE Field IN ('building_number', 'street_name', 'district', 'city', 'postal_code', 'country_code')");
    echo "Business table B2B columns: " . count($businessColumns) . " found\n";
    foreach ($businessColumns as $col) {
        echo "  - " . $col->Field . " (" . $col->Type . ")\n";
    }
} catch (\Exception $e) {
    echo "Error checking business columns: " . $e->getMessage() . "\n";
}

echo "\n";

try {
    $partyColumns = DB::select("SHOW COLUMNS FROM parties WHERE Field IN ('zatca_type', 'vat_number', 'building_number', 'street_name', 'district', 'city', 'postal_code', 'country_code')");
    echo "Parties table B2B columns: " . count($partyColumns) . " found\n";
    foreach ($partyColumns as $col) {
        echo "  - " . $col->Field . " (" . $col->Type . ")\n";
    }
} catch (\Exception $e) {
    echo "Error checking party columns: " . $e->getMessage() . "\n";
}

echo "\n";
echo "=== Check Complete ===\n";
