<?php

/**
 * Check and Add Commercial Registration Numbers
 * هذا السكريبت يتحقق من وجود أرقام السجل التجاري ويضيفها
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Business;

echo "=== فحص أرقام السجل التجاري ===\n\n";

// Get all businesses
$businesses = Business::all();

echo "عدد الحسابات: " . $businesses->count() . "\n\n";

foreach ($businesses as $business) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "ID: {$business->id}\n";
    echo "الاسم: {$business->companyName}\n";
    echo "البريد: {$business->email}\n";
    
    // Check VAT Number
    if ($business->vat_no) {
        echo "✅ رقم ض.ق.م: {$business->vat_no}\n";
    } else {
        echo "❌ رقم ض.ق.م: غير موجود\n";
    }
    
    // Check Commercial Registration
    if ($business->commercial_registration) {
        echo "✅ السجل التجاري: {$business->commercial_registration}\n";
    } else {
        echo "❌ السجل التجاري: غير موجود\n";
    }
    
    // Check Additional ID
    if ($business->additional_id) {
        echo "✅ معرف إضافي: {$business->additional_id}\n";
    } else {
        echo "⚠️  معرف إضافي: غير موجود\n";
    }
    
    // Check Address
    if ($business->building_number && $business->street_name && $business->city) {
        echo "✅ العنوان: مكتمل\n";
    } else {
        echo "⚠️  العنوان: غير مكتمل\n";
    }
    
    echo "\n";
}

echo "\n=== ملخص ===\n";
$withVat = Business::whereNotNull('vat_no')->count();
$withCR = Business::whereNotNull('commercial_registration')->count();
$withAdditionalId = Business::whereNotNull('additional_id')->count();

echo "حسابات بها رقم ض.ق.م: {$withVat} / {$businesses->count()}\n";
echo "حسابات بها سجل تجاري: {$withCR} / {$businesses->count()}\n";
echo "حسابات بها معرف إضافي: {$withAdditionalId} / {$businesses->count()}\n";

echo "\n=== تعليمات ===\n";
echo "لإضافة السجل التجاري:\n";
echo "1. افتح صفحة إعدادات الأعمال (Business Settings)\n";
echo "2. أدخل رقم السجل التجاري في حقل 'Commercial Registration'\n";
echo "3. احفظ التغييرات\n";
echo "\nأو استخدم السكريبت التالي لإضافة بيانات تجريبية:\n";
echo "php add_test_cr_numbers.php\n";
