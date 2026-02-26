<?php

/**
 * Add Test Commercial Registration Numbers
 * إضافة أرقام سجل تجاري تجريبية للحسابات
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Business;

echo "=== إضافة أرقام السجل التجاري التجريبية ===\n\n";

// Get all businesses without commercial registration
$businesses = Business::whereNull('commercial_registration')
    ->orWhere('commercial_registration', '')
    ->get();

if ($businesses->isEmpty()) {
    echo "✅ جميع الحسابات لديها أرقام سجل تجاري\n";
    exit;
}

echo "عدد الحسابات التي تحتاج سجل تجاري: " . $businesses->count() . "\n\n";

$confirm = readline("هل تريد إضافة أرقام سجل تجاري تجريبية؟ (yes/no): ");

if (strtolower($confirm) !== 'yes') {
    echo "تم الإلغاء\n";
    exit;
}

echo "\n";

foreach ($businesses as $index => $business) {
    // Generate a test CR number (format: 1010xxxxxx)
    $crNumber = '1010' . str_pad($business->id, 6, '0', STR_PAD_LEFT);
    
    $business->commercial_registration = $crNumber;
    
    // Add additional_id if not exists
    if (!$business->additional_id) {
        $business->additional_id = 'CRN';
    }
    
    // Add VAT if not exists (for testing)
    if (!$business->vat_no) {
        $business->vat_no = '3' . str_pad($business->id, 14, '0', STR_PAD_LEFT);
    }
    
    $business->save();
    
    echo "✅ تم تحديث: {$business->companyName}\n";
    echo "   - السجل التجاري: {$crNumber}\n";
    echo "   - رقم ض.ق.م: {$business->vat_no}\n";
    echo "   - معرف إضافي: {$business->additional_id}\n\n";
}

echo "\n=== اكتمل ===\n";
echo "تم تحديث {$businesses->count()} حساب بنجاح\n";
echo "\nالآن يمكنك:\n";
echo "1. فتح فاتورة الاشتراك\n";
echo "2. التحقق من ظهور السجل التجاري\n";
echo "3. تعديل الأرقام من صفحة إعدادات الأعمال\n";
