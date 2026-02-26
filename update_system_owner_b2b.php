<?php

/**
 * تحديث بيانات مالك النظام لتكون مطابقة لمتطلبات ZATCA B2B
 * Update System Owner Data for ZATCA B2B Compliance
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Business;

echo "╔════════════════════════════════════════════════════════════════════╗\n";
echo "║        تحديث بيانات مالك النظام | Update System Owner Data        ║\n";
echo "╚════════════════════════════════════════════════════════════════════╝\n\n";

// Get system owner (first business)
$systemOwner = Business::first();

if (!$systemOwner) {
    echo "❌ لا يوجد مالك نظام في قاعدة البيانات\n";
    echo "❌ No system owner found in database\n\n";
    exit(1);
}

echo "مالك النظام الحالي: {$systemOwner->companyName}\n\n";

echo "━━━ البيانات الحالية | Current Data ━━━\n\n";

$currentData = [
    'اسم الشركة / Company Name' => $systemOwner->companyName,
    'الرقم الضريبي / VAT Number' => $systemOwner->vat_no ?? '❌ غير موجود',
    'السجل التجاري / CR' => $systemOwner->commercial_registration ?? '❌ غير موجود',
    'رقم المبنى / Building' => $systemOwner->building_number ?? '❌ غير موجود',
    'اسم الشارع / Street' => $systemOwner->street_name ?? '❌ غير موجود',
    'الحي / District' => $systemOwner->district ?? '❌ غير موجود',
    'المدينة / City' => $systemOwner->city ?? '❌ غير موجود',
    'الرمز البريدي / Postal Code' => $systemOwner->postal_code ?? '❌ غير موجود',
    'رمز الدولة / Country Code' => $systemOwner->country_code ?? '❌ غير موجود',
];

foreach ($currentData as $label => $value) {
    echo "  {$label}: {$value}\n";
}

echo "\n";

// Suggested data for system owner
$suggestedData = [
    'vat_number' => '300000000000003', // Example VAT number (15 digits)
    'commercial_registration' => '1010000000', // Example CR (10 digits)
];

echo "━━━ البيانات المقترحة | Suggested Data ━━━\n\n";
echo "هذه بيانات تجريبية للاختبار فقط. يجب استبدالها ببيانات حقيقية.\n";
echo "This is test data for testing only. Should be replaced with real data.\n\n";

echo "الرقم الضريبي المقترح / Suggested VAT: {$suggestedData['vat_number']}\n";
echo "السجل التجاري المقترح / Suggested CR: {$suggestedData['commercial_registration']}\n\n";

echo "━━━ خيارات التحديث | Update Options ━━━\n\n";
echo "1. تحديث تلقائي بالبيانات المقترحة (للاختبار فقط)\n";
echo "   Auto-update with suggested data (for testing only)\n\n";
echo "2. تحديث يدوي من صفحة الإعدادات\n";
echo "   Manual update from settings page\n\n";

// Ask user
echo "هل تريد التحديث التلقائي؟ (yes/no): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
$answer = trim(strtolower($line));
fclose($handle);

if ($answer === 'yes' || $answer === 'y') {
    echo "\n━━━ جاري التحديث | Updating ━━━\n\n";
    
    try {
        $systemOwner->update([
            'vat_no' => $suggestedData['vat_number'],
            'commercial_registration' => $suggestedData['commercial_registration'],
        ]);
        
        echo "✅ تم التحديث بنجاح!\n";
        echo "✅ Updated successfully!\n\n";
        
        echo "البيانات الجديدة:\n";
        echo "  الرقم الضريبي: {$systemOwner->vat_no}\n";
        echo "  السجل التجاري: {$systemOwner->commercial_registration}\n\n";
        
        echo "⚠️  تذكير: هذه بيانات تجريبية. يجب تحديثها ببيانات حقيقية من:\n";
        echo "⚠️  Reminder: This is test data. Update with real data from:\n";
        echo "   Settings > Business Settings\n\n";
        
    } catch (\Exception $e) {
        echo "❌ فشل التحديث: {$e->getMessage()}\n";
        echo "❌ Update failed: {$e->getMessage()}\n\n";
    }
    
} else {
    echo "\n━━━ التحديث اليدوي | Manual Update ━━━\n\n";
    echo "لتحديث البيانات يدوياً:\n";
    echo "To update data manually:\n\n";
    echo "1. افتح صفحة الإعدادات / Open Settings page\n";
    echo "2. ابحث عن قسم \"بيانات الشركة\" / Find \"Business Data\" section\n";
    echo "3. املأ الحقول التالية / Fill the following fields:\n";
    echo "   - الرقم الضريبي (15 رقم) / VAT Number (15 digits)\n";
    echo "   - السجل التجاري (10 أرقام) / Commercial Registration (10 digits)\n";
    echo "4. احفظ التغييرات / Save changes\n\n";
}

echo "━━━ التحقق من البيانات | Verify Data ━━━\n\n";
echo "بعد التحديث، شغل السكريبت التالي للتحقق:\n";
echo "After update, run the following script to verify:\n\n";
echo "  php check_subscription_invoice_b2b.php\n\n";

echo "✅ تم!\n";

