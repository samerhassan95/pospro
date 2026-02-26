<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Checking businesses table columns...\n\n";

$requiredColumns = [
    'vat_no',
    'commercial_registration',
    'additional_id',
    'building_number',
    'street_name',
    'district',
    'city',
    'postal_code',
    'country_code',
    'additional_address',
    'bank_name',
    'bank_account_number',
];

$existingColumns = Schema::getColumnListing('businesses');

echo "Required B2B Columns:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$missingColumns = [];

foreach ($requiredColumns as $column) {
    if (in_array($column, $existingColumns)) {
        echo "✓ {$column} - EXISTS\n";
    } else {
        echo "✗ {$column} - MISSING\n";
        $missingColumns[] = $column;
    }
}

echo "\n";

if (empty($missingColumns)) {
    echo "✅ All required columns exist!\n";
} else {
    echo "⚠️  Missing columns: " . count($missingColumns) . "\n";
    echo "Need to add: " . implode(', ', $missingColumns) . "\n";
}
