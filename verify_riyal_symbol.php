<?php

/**
 * التحقق من رمز الريال السعودي في قاعدة البيانات
 * Verify Saudi Riyal symbol in database
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== التحقق من رمز الريال السعودي ===\n";
    echo "=== Verifying Saudi Riyal Symbol ===\n\n";
    
    // جلب بيانات عملة الريال السعودي
    $currency = DB::table('currencies')
        ->where('code', 'SAR')
        ->first();
    
    if ($currency) {
        echo "✓ تم العثور على عملة الريال السعودي\n";
        echo "✓ Saudi Riyal currency found\n\n";
        
        echo "الاسم / Name: {$currency->name}\n";
        echo "الكود / Code: {$currency->code}\n";
        echo "الرمز / Symbol: {$currency->symbol}\n";
        echo "الموضع / Position: {$currency->position}\n";
        echo "الحالة / Status: " . ($currency->status ? 'Active' : 'Inactive') . "\n\n";
        
        // التحقق من الرمز
        if ($currency->symbol === '﷼') {
            echo "✓ الرمز صحيح! يستخدم رمز الريال ﷼\n";
            echo "✓ Symbol is correct! Using Riyal symbol ﷼\n\n";
        } else {
            echo "⚠ الرمز الحالي: {$currency->symbol}\n";
            echo "⚠ Current symbol: {$currency->symbol}\n";
            echo "⚠ يجب أن يكون: ﷼\n";
            echo "⚠ Should be: ﷼\n\n";
        }
    } else {
        echo "✗ لم يتم العثور على عملة الريال السعودي (SAR)\n";
        echo "✗ Saudi Riyal (SAR) currency not found\n\n";
    }
    
    // التحقق من user_currencies إذا كان موجود
    if (DB::getSchemaBuilder()->hasTable('user_currencies')) {
        echo "\n--- فحص جدول user_currencies ---\n";
        echo "--- Checking user_currencies table ---\n\n";
        
        $userCurrencies = DB::table('user_currencies')
            ->where('code', 'SAR')
            ->get();
        
        if ($userCurrencies->count() > 0) {
            echo "عدد السجلات / Records count: {$userCurrencies->count()}\n\n";
            
            foreach ($userCurrencies as $uc) {
                echo "User ID: {$uc->user_id} - Symbol: {$uc->symbol}\n";
            }
        } else {
            echo "لا توجد سجلات للريال السعودي في user_currencies\n";
            echo "No SAR records in user_currencies\n";
        }
    }
    
    echo "\n=== انتهى الفحص ===\n";
    echo "=== Verification Complete ===\n";
    
} catch (Exception $e) {
    echo "✗ حدث خطأ: " . $e->getMessage() . "\n";
    echo "✗ Error occurred: " . $e->getMessage() . "\n";
    exit(1);
}
