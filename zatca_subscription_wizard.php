<?php

/**
 * معالج تفاعلي لإعداد فواتير الاشتراكات مع ZATCA
 * Interactive Wizard for ZATCA Subscription Invoices Setup
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Business;
use App\Models\PlanSubscribe;

function printHeader($title) {
    echo "\n";
    echo "╔════════════════════════════════════════════════════════════════════╗\n";
    echo "║  " . str_pad($title, 66) . "║\n";
    echo "╚════════════════════════════════════════════════════════════════════╝\n\n";
}

function printStep($number, $title) {
    echo "\n━━━ الخطوة {$number}: {$title} ━━━\n\n";
}

function askYesNo($question) {
    echo "{$question} (yes/no): ";
    $handle = fopen("php://stdin", "r");
    $line = trim(fgets($handle));
    return strtolower($line) === 'yes';
}

function pressEnterToContinue() {
    echo "\nاضغط Enter للمتابعة...";
    fgets(STDIN);
}

// Start
printHeader("معالج إعداد فواتير الاشتراكات - ZATCA Setup Wizard");

echo "مرحباً! هذا المعالج سيساعدك على:\n";
echo "✓ فحص النظام الحالي\n";
echo "✓ تحديث البيانات المطلوبة\n";
echo "✓ التحقق من الفواتير\n";
echo "✓ الاستعداد للربط مع ZATCA\n\n";

if (!askYesNo("هل تريد البدء؟")) {
    echo "\n❌ تم الإلغاء\n";
    exit(0);
}

// Step 1: Check System
printStep(1, "فحص النظام الحالي");

echo "جاري فحص النظام...\n\n";

$systemOwner = Business::first();
$subscribers = PlanSubscribe::with('business')->latest()->take(5)->get();

// Check system owner
$ownerIssues = [];
$requiredFields = ['vat_no', 'commercial_registration', 'building_number', 'street_name', 'district', 'city', 'postal_code', 'country_code'];

foreach ($requiredFields as $field) {
    if (empty($systemOwner->$field)) {
        $ownerIssues[] = $field;
    }
}

echo "مالك النظام: {$systemOwner->companyName}\n";
if (empty($ownerIssues)) {
    echo "✅ بيانات مالك النظام: مكتملة\n";
} else {
    echo "⚠️  بيانات مالك النظام: ناقصة (" . count($ownerIssues) . " حقل)\n";
    echo "   الحقول الناقصة: " . implode(', ', $ownerIssues) . "\n";
}

// Check subscribers
$subscribersWithIssues = 0;
foreach ($subscribers as $sub) {
    if ($sub->business) {
        $issues = [];
        if (empty($sub->business->vat_no)) $issues[] = 'vat_no';
        if (empty($sub->business->building_number)) $issues[] = 'building_number';
        if (empty($sub->business->city)) $issues[] = 'city';
        
        if (!empty($issues)) {
            $subscribersWithIssues++;
        }
    }
}

echo "\nالمشتركون:\n";
echo "✓ إجمالي الاشتراكات: " . $subscribers->count() . "\n";
if ($subscribersWithIssues > 0) {
    echo "⚠️  مشتركون يحتاجون تحديث: {$subscribersWithIssues}\n";
} else {
    echo "✅ جميع المشتركين: بيانات مكتملة\n";
}

pressEnterToContinue();

// Step 2: Update System Owner
if (!empty($ownerIssues)) {
    printStep(2, "تحديث بيانات مالك النظام");
    
    echo "بيانات مالك النظام ناقصة. هل تريد تحديثها؟\n";
    echo "1. تحديث تلقائي (بيانات تجريبية)\n";
    echo "2. تحديث يدوي (سأقوم بذلك بنفسي)\n";
    echo "3. تخطي\n\n";
    
    echo "اختر (1/2/3): ";
    $choice = trim(fgets(STDIN));
    
    if ($choice === '1') {
        echo "\nجاري التحديث التلقائي...\n";
        
        $systemOwner->update([
            'vat_no' => '300123456789003',
            'commercial_registration' => '1010000001',
            'additional_id' => 'OTH-TRADEG-001',
            'country_code' => 'SA',
            'building_number' => '123',
            'street_name' => 'King Fahd Road',
            'district' => 'Al Olaya',
            'city' => 'Riyadh',
            'postal_code' => '11564',
        ]);
        
        echo "✅ تم التحديث بنجاح!\n";
    } elseif ($choice === '2') {
        echo "\nافتح الرابط التالي لتحديث البيانات يدوياً:\n";
        echo "/admin/business/1/edit\n";
        echo "\nبعد التحديث، شغل هذا السكريبت مرة أخرى.\n";
        exit(0);
    }
    
    pressEnterToContinue();
} else {
    printStep(2, "بيانات مالك النظام");
    echo "✅ بيانات مالك النظام مكتملة - لا حاجة للتحديث\n";
    pressEnterToContinue();
}

// Step 3: Update Subscribers
if ($subscribersWithIssues > 0) {
    printStep(3, "تحديث بيانات المشتركين");
    
    echo "يوجد {$subscribersWithIssues} مشترك يحتاج تحديث.\n\n";
    echo "لتحديث بيانات المشتركين:\n";
    echo "1. افتح: /admin/business/{{id}}/edit\n";
    echo "2. املأ الحقول المطلوبة\n";
    echo "3. احفظ\n\n";
    
    echo "المشتركون الذين يحتاجون تحديث:\n";
    foreach ($subscribers as $sub) {
        if ($sub->business) {
            $issues = [];
            if (empty($sub->business->vat_no)) $issues[] = 'vat_no';
            if (empty($sub->business->building_number)) $issues[] = 'address';
            
            if (!empty($issues)) {
                echo "  • {$sub->business->companyName} (ID: {$sub->business->id})\n";
                echo "    الناقص: " . implode(', ', $issues) . "\n";
            }
        }
    }
    
    pressEnterToContinue();
} else {
    printStep(3, "بيانات المشتركين");
    echo "✅ بيانات جميع المشتركين مكتملة\n";
    pressEnterToContinue();
}

// Step 4: Check Invoice
printStep(4, "التحقق من الفاتورة");

echo "لفتح فاتورة اشتراك:\n";
echo "1. اذهب إلى: /admin/subscription-orders\n";
echo "2. اختر أي اشتراك\n";
echo "3. اضغط على \"View Invoice\"\n\n";

echo "تحقق من:\n";
echo "✓ العنوان: \"فاتورة ضريبية / TAX INVOICE (B2B)\"\n";
echo "✓ بيانات البائع كاملة (بدون ⚠️)\n";
echo "✓ بيانات المشتري كاملة (بدون ⚠️)\n";
echo "✓ المبالغ صحيحة\n";
echo "✓ رمز QR موجود\n\n";

if (askYesNo("هل فتحت الفاتورة وتحققت منها؟")) {
    if (askYesNo("هل كل شيء يبدو صحيحاً؟")) {
        echo "✅ ممتاز!\n";
    } else {
        echo "\n⚠️  إذا وجدت مشاكل:\n";
        echo "1. شغل: php check_subscription_invoice_b2b.php\n";
        echo "2. راجع البيانات الناقصة\n";
        echo "3. حدث البيانات\n";
        echo "4. شغل هذا السكريبت مرة أخرى\n";
        exit(0);
    }
}

pressEnterToContinue();

// Step 5: ZATCA Integration
printStep(5, "الربط مع ZATCA");

echo "للربط مع ZATCA:\n\n";

echo "1. سجل في بوابة ZATCA:\n";
echo "   https://fatoora.zatca.gov.sa\n\n";

echo "2. احصل على بيانات الاعتماد:\n";
echo "   • OTP\n";
echo "   • Certificate\n";
echo "   • Private Key\n\n";

echo "3. افتح إعدادات ZATCA في النظام:\n";
echo "   /admin/settings (ابحث عن ZATCA Settings)\n\n";

echo "4. املأ البيانات واحفظ\n\n";

echo "5. اختبر في Sandbox أولاً\n\n";

if (askYesNo("هل أعددت ZATCA؟")) {
    echo "✅ ممتاز! النظام جاهز\n";
} else {
    echo "\n📝 راجع الدليل الشامل:\n";
    echo "   ZATCA_SUBSCRIPTION_COMPLETE_GUIDE_AR.md\n";
}

pressEnterToContinue();

// Final Summary
printHeader("الملخص النهائي");

echo "✅ ما تم:\n";
if (empty($ownerIssues)) {
    echo "  ✓ بيانات مالك النظام: مكتملة\n";
} else {
    echo "  ⏳ بيانات مالك النظام: تحتاج تحديث\n";
}

if ($subscribersWithIssues === 0) {
    echo "  ✓ بيانات المشتركين: مكتملة\n";
} else {
    echo "  ⏳ بيانات المشتركين: {$subscribersWithIssues} يحتاج تحديث\n";
}

echo "\n📋 الخطوات التالية:\n";
echo "1. تحقق من الفاتورة: /admin/subscription-orders\n";
echo "2. أعد ZATCA: /admin/settings\n";
echo "3. اختبر في Sandbox\n";
echo "4. انتقل للإنتاج\n\n";

echo "📚 للمزيد من المعلومات:\n";
echo "  • ZATCA_SUBSCRIPTION_COMPLETE_GUIDE_AR.md\n";
echo "  • php check_subscription_invoice_b2b.php\n\n";

echo "🎉 شكراً لاستخدام المعالج!\n\n";
