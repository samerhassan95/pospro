<?php

/**
 * تحديث رمز الريال السعودي إلى الرمز الخاص ⃁
 * Update Saudi Riyal symbol to special symbol ⃁
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "بدء تحديث رمز الريال السعودي إلى الرمز الخاص ⃁\n";
    echo "Starting Saudi Riyal symbol update to special symbol ⃁\n\n";
    
    // الرمز الخاص
    $specialSymbol = '⃁';
    
    // تحديث رمز الريال السعودي في جدول العملات
    $updated = DB::table('currencies')
        ->where('code', 'SAR')
        ->update(['symbol' => $specialSymbol]);
    
    if ($updated > 0) {
        echo "✓ تم تحديث رمز الريال السعودي بنجاح في جدول العملات\n";
        echo "✓ Saudi Riyal symbol updated successfully in currencies table\n";
        echo "  عدد السجلات المحدثة: {$updated}\n";
        echo "  Records updated: {$updated}\n\n";
    } else {
        echo "⚠ لم يتم العثور على عملة الريال السعودي (SAR) في قاعدة البيانات\n";
        echo "⚠ Saudi Riyal (SAR) currency not found in database\n\n";
    }
    
    // تحديث رمز الريال في جدول user_currencies إذا كان موجود
    if (DB::getSchemaBuilder()->hasTable('user_currencies')) {
        $userCurrenciesUpdated = DB::table('user_currencies')
            ->where('code', 'SAR')
            ->update(['symbol' => $specialSymbol]);
        
        if ($userCurrenciesUpdated > 0) {
            echo "✓ تم تحديث رمز الريال في جدول user_currencies\n";
            echo "✓ Saudi Riyal symbol updated in user_currencies table\n";
            echo "  عدد السجلات المحدثة: {$userCurrenciesUpdated}\n";
            echo "  Records updated: {$userCurrenciesUpdated}\n\n";
        }
    }
    
    // عرض الرمز الجديد للتأكيد
    $currency = DB::table('currencies')
        ->where('code', 'SAR')
        ->first();
    
    if ($currency) {
        echo "الرمز الحالي للريال السعودي: {$currency->symbol}\n";
        echo "Current Saudi Riyal symbol: {$currency->symbol}\n\n";
    }
    
    echo "✓ اكتمل التحديث بنجاح!\n";
    echo "✓ Update completed successfully!\n";
    echo "\nالرمز الجديد: ⃁\n";
    echo "New symbol: ⃁\n";
    echo "\nUnicode: U+20E1 (Combining Enclosing Circle Backslash)\n";
    
} catch (Exception $e) {
    echo "✗ حدث خطأ: " . $e->getMessage() . "\n";
    echo "✗ Error occurred: " . $e->getMessage() . "\n";
    exit(1);
}
