<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 Checking SAR Currency Symbol...\n\n";

// Check currencies table
$currencies = DB::table('currencies')->where('code', 'SAR')->get();

if ($currencies->isEmpty()) {
    echo "❌ No SAR currency found in currencies table\n";
} else {
    echo "✅ Found SAR currency in currencies table:\n";
    foreach ($currencies as $currency) {
        echo "   ID: {$currency->id}\n";
        echo "   Code: {$currency->code}\n";
        echo "   Symbol: '{$currency->symbol}'\n";
        echo "   Symbol (hex): " . bin2hex($currency->symbol) . "\n";
        echo "   Name: {$currency->name}\n";
        echo "   Status: {$currency->status}\n\n";
    }
}

// Check user_currencies table
$userCurrencies = DB::table('user_currencies')->where('code', 'SAR')->get();

if ($userCurrencies->isEmpty()) {
    echo "❌ No SAR currency found in user_currencies table\n";
} else {
    echo "✅ Found SAR currency in user_currencies table:\n";
    foreach ($userCurrencies as $currency) {
        echo "   ID: {$currency->id}\n";
        echo "   User ID: {$currency->user_id}\n";
        echo "   Code: {$currency->code}\n";
        echo "   Symbol: '{$currency->symbol}'\n";
        echo "   Symbol (hex): " . bin2hex($currency->symbol) . "\n\n";
    }
}

echo "\n📝 Current symbol should be: ^\n";
echo "   Hex: " . bin2hex('^') . "\n\n";

echo "🔧 To fix, run: php update_sar_symbol_to_caret.php\n";
