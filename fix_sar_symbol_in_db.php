<?php

/**
 * إصلاح رمز الريال السعودي في قاعدة البيانات
 * Fix SAR symbol in database
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== إصلاح رمز الريال السعودي ===\n";
echo "=== Fixing SAR Symbol ===\n\n";

// Check current SAR currencies
$sarCurrencies = DB::table('currencies')
    ->where('code', 'SAR')
    ->orWhere('name', 'LIKE', '%Saudi%')
    ->orWhere('name', 'LIKE', '%Riyal%')
    ->get();

if ($sarCurrencies->isEmpty()) {
    echo "⚠️  لم يتم العثور على عملة الريال السعودي\n";
    echo "⚠️  SAR currency not found\n\n";
    
    echo "هل تريد إنشاء عملة الريال السعودي؟ (y/n): ";
    $handle = fopen ("php://stdin","r");
    $line = fgets($handle);
    if(trim($line) != 'y'){
        echo "تم الإلغاء\n";
        exit;
    }
    fclose($handle);
    
    // Create SAR currency
    DB::table('currencies')->insert([
        'name' => 'Saudi Riyal',
        'code' => 'SAR',
        'symbol' => 'ر.س',
        'rate' => 1,
        'position' => 'left',
        'status' => 1,
        'is_default' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "✓ تم إنشاء عملة الريال السعودي\n";
    echo "✓ SAR currency created\n\n";
} else {
    echo "تم العثور على " . $sarCurrencies->count() . " عملة ريال سعودي:\n";
    echo "Found " . $sarCurrencies->count() . " SAR currency/currencies:\n\n";
    
    foreach ($sarCurrencies as $currency) {
        echo "ID: {$currency->id}\n";
        echo "Name: {$currency->name}\n";
        echo "Code: {$currency->code}\n";
        echo "Symbol: {$currency->symbol}\n";
        echo "Is Default: " . ($currency->is_default ? 'Yes' : 'No') . "\n";
        echo "---\n";
    }
    
    echo "\nتحديث الرمز إلى 'ر.س'...\n";
    echo "Updating symbol to 'ر.س'...\n";
    
    $updated = DB::table('currencies')
        ->where('code', 'SAR')
        ->update([
            'symbol' => 'ر.س',
            'updated_at' => now(),
        ]);
    
    echo "✓ تم تحديث {$updated} عملة\n";
    echo "✓ Updated {$updated} currency/currencies\n\n";
}

// Check if SAR is default
$defaultCurrency = DB::table('currencies')->where('is_default', 1)->first();

if ($defaultCurrency) {
    echo "العملة الافتراضية الحالية:\n";
    echo "Current default currency:\n";
    echo "  Name: {$defaultCurrency->name}\n";
    echo "  Code: {$defaultCurrency->code}\n";
    echo "  Symbol: {$defaultCurrency->symbol}\n\n";
    
    if ($defaultCurrency->code !== 'SAR') {
        echo "⚠️  العملة الافتراضية ليست الريال السعودي\n";
        echo "⚠️  Default currency is not SAR\n";
        echo "هل تريد جعل الريال السعودي العملة الافتراضية؟ (y/n): ";
        $handle = fopen ("php://stdin","r");
        $line = fgets($handle);
        if(trim($line) == 'y'){
            DB::table('currencies')->update(['is_default' => 0]);
            DB::table('currencies')->where('code', 'SAR')->update(['is_default' => 1]);
            echo "✓ تم جعل الريال السعودي العملة الافتراضية\n";
            echo "✓ SAR is now the default currency\n";
        }
        fclose($handle);
    }
}

// Clear cache
echo "\nمسح الكاش...\n";
echo "Clearing cache...\n";
Artisan::call('cache:clear');
echo "✓ تم مسح الكاش\n";
echo "✓ Cache cleared\n\n";

echo "✅ تم الإصلاح بنجاح!\n";
echo "✅ Fix completed successfully!\n\n";

echo "📝 ملاحظة: تأكد من تحديث الصفحة بـ Ctrl+F5\n";
echo "📝 Note: Make sure to refresh the page with Ctrl+F5\n";
