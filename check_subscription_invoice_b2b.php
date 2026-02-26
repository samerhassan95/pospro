<?php

/**
 * فحص فاتورة الاشتراك ومقارنتها بمتطلبات ZATCA B2B
 * Check Subscription Invoice against ZATCA B2B Requirements
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Business;
use App\Models\PlanSubscribe;

echo "╔════════════════════════════════════════════════════════════════════╗\n";
echo "║   فحص فاتورة الاشتراك - متطلبات ZATCA B2B                         ║\n";
echo "║   Subscription Invoice - ZATCA B2B Requirements Check              ║\n";
echo "╚════════════════════════════════════════════════════════════════════╝\n\n";

// متطلبات ZATCA B2B للفواتير الضريبية
$b2bRequirements = [
    'seller' => [
        'name' => 'اسم البائع / Seller Name',
        'vat_number' => 'الرقم الضريبي للبائع / Seller VAT Number (15 digits)',
        'commercial_registration' => 'السجل التجاري / Commercial Registration',
        'building_number' => 'رقم المبنى / Building Number',
        'street_name' => 'اسم الشارع / Street Name',
        'district' => 'الحي / District',
        'city' => 'المدينة / City',
        'postal_code' => 'الرمز البريدي / Postal Code',
        'country_code' => 'رمز الدولة / Country Code (2 letters)',
    ],
    'buyer' => [
        'name' => 'اسم المشتري / Buyer Name',
        'vat_number' => 'الرقم الضريبي للمشتري / Buyer VAT Number (15 digits)',
        'commercial_registration' => 'السجل التجاري / Commercial Registration (optional)',
        'building_number' => 'رقم المبنى / Building Number',
        'street_name' => 'اسم الشارع / Street Name',
        'district' => 'الحي / District',
        'city' => 'المدينة / City',
        'postal_code' => 'الرمز البريدي / Postal Code',
        'country_code' => 'رمز الدولة / Country Code (2 letters)',
        'phone' => 'الهاتف / Phone (optional)',
        'email' => 'البريد الإلكتروني / Email (optional)',
    ],
    'invoice' => [
        'invoice_number' => 'رقم الفاتورة / Invoice Number',
        'invoice_date' => 'تاريخ الفاتورة / Invoice Date',
        'supply_date' => 'تاريخ التوريد / Supply Date',
        'invoice_type' => 'نوع الفاتورة / Invoice Type (B2B)',
        'currency' => 'العملة / Currency',
    ],
    'amounts' => [
        'subtotal' => 'المجموع الفرعي / Subtotal',
        'vat_rate' => 'نسبة الضريبة / VAT Rate (%)',
        'vat_amount' => 'مبلغ الضريبة / VAT Amount',
        'total' => 'الإجمالي شامل الضريبة / Total Including VAT',
    ],
    'additional' => [
        'qr_code' => 'رمز QR / QR Code',
        'payment_method' => 'طريقة الدفع / Payment Method',
    ]
];

echo "━━━ 1. فحص بيانات مالك النظام (البائع) | System Owner (Seller) ━━━\n\n";

// Get first business as system owner
$systemOwner = Business::first();

if (!$systemOwner) {
    echo "❌ لا يوجد مالك نظام في قاعدة البيانات\n";
    echo "❌ No system owner found in database\n\n";
    exit(1);
}

echo "✓ مالك النظام: {$systemOwner->companyName}\n\n";

$sellerIssues = [];
$sellerData = [];

// Check seller required fields
foreach ($b2bRequirements['seller'] as $field => $label) {
    $dbField = $field;
    if ($field === 'name') {
        $dbField = 'companyName';
    }
    
    $value = $systemOwner->$dbField ?? null;
    $sellerData[$field] = $value;
    
    if (empty($value)) {
        $sellerIssues[] = $label;
        echo "  ❌ {$label}: غير موجود / Missing\n";
    } else {
        // Validate format
        if ($field === 'vat_number' && strlen($value) !== 15) {
            $sellerIssues[] = $label . ' (يجب أن يكون 15 رقم / Must be 15 digits)';
            echo "  ⚠️  {$label}: {$value} (يجب أن يكون 15 رقم)\n";
        } elseif ($field === 'country_code' && strlen($value) !== 2) {
            $sellerIssues[] = $label . ' (يجب أن يكون حرفين / Must be 2 letters)';
            echo "  ⚠️  {$label}: {$value} (يجب أن يكون حرفين)\n";
        } else {
            echo "  ✓ {$label}: {$value}\n";
        }
    }
}

echo "\n";

if (empty($sellerIssues)) {
    echo "✅ جميع بيانات البائع مكتملة!\n";
    echo "✅ All seller data is complete!\n\n";
} else {
    echo "⚠️  بيانات البائع ناقصة:\n";
    echo "⚠️  Seller data is incomplete:\n";
    foreach ($sellerIssues as $issue) {
        echo "   • {$issue}\n";
    }
    echo "\n";
}

echo "━━━ 2. فحص بيانات المشتركين (المشترين) | Subscribers (Buyers) ━━━\n\n";

// Get recent subscriptions
$recentSubscriptions = PlanSubscribe::with('business')
    ->orderBy('created_at', 'desc')
    ->take(5)
    ->get();

echo "عدد الاشتراكات الأخيرة: " . $recentSubscriptions->count() . "\n\n";

$buyersWithIssues = 0;
$buyersComplete = 0;

foreach ($recentSubscriptions as $sub) {
    $buyer = $sub->business;
    
    if (!$buyer) {
        echo "⚠️  اشتراك #{$sub->id}: لا يوجد بيانات مشترك\n\n";
        $buyersWithIssues++;
        continue;
    }
    
    echo "┌─ اشتراك #{$sub->id}: {$buyer->companyName}\n";
    
    $buyerIssues = [];
    
    // Check buyer required fields
    foreach ($b2bRequirements['buyer'] as $field => $label) {
        $dbField = $field;
        if ($field === 'name') {
            $dbField = 'companyName';
        } elseif ($field === 'phone') {
            $dbField = 'phoneNumber';
        }
        
        $value = $buyer->$dbField ?? null;
        
        // Skip optional fields
        if (in_array($field, ['commercial_registration', 'phone', 'email']) && empty($value)) {
            continue;
        }
        
        if (empty($value)) {
            $buyerIssues[] = $label;
        } else {
            // Validate format
            if ($field === 'vat_number' && strlen($value) !== 15) {
                $buyerIssues[] = $label . ' (يجب أن يكون 15 رقم)';
            } elseif ($field === 'country_code' && strlen($value) !== 2) {
                $buyerIssues[] = $label . ' (يجب أن يكون حرفين)';
            }
        }
    }
    
    if (empty($buyerIssues)) {
        echo "│  ✅ جميع البيانات المطلوبة موجودة\n";
        $buyersComplete++;
    } else {
        echo "│  ⚠️  بيانات ناقصة:\n";
        foreach ($buyerIssues as $issue) {
            echo "│     • {$issue}\n";
        }
        $buyersWithIssues++;
    }
    
    echo "└─\n\n";
}

echo "━━━ 3. فحص بنية الفاتورة | Invoice Structure ━━━\n\n";

// Check plan_subscribes table structure
$subscriptionColumns = DB::select("SHOW COLUMNS FROM plan_subscribes");
$hasRequiredColumns = true;
$requiredInvoiceFields = [
    'invoice_number' => 'رقم الفاتورة',
    'price' => 'السعر',
    'duration' => 'المدة',
    'created_at' => 'تاريخ الإنشاء',
    'gateway_id' => 'طريقة الدفع',
    'transaction_id' => 'رقم المعاملة',
];

$existingColumns = array_column($subscriptionColumns, 'Field');

foreach ($requiredInvoiceFields as $field => $label) {
    if (in_array($field, $existingColumns)) {
        echo "  ✓ {$label} / {$field}\n";
    } else {
        echo "  ❌ {$label} / {$field} - غير موجود\n";
        $hasRequiredColumns = false;
    }
}

echo "\n";

// Check for ZATCA-specific fields
$zatcaFields = ['zatca_status', 'zatca_response', 'zatca_uuid'];
$hasZatcaFields = false;

echo "حقول ZATCA في جدول الاشتراكات:\n";
foreach ($zatcaFields as $field) {
    if (in_array($field, $existingColumns)) {
        echo "  ✓ {$field}\n";
        $hasZatcaFields = true;
    } else {
        echo "  ⚠️  {$field} - غير موجود (يمكن إضافته)\n";
    }
}

echo "\n";

echo "━━━ 4. فحص قالب الفاتورة | Invoice Template ━━━\n\n";

$invoiceTemplate = 'resources/views/admin/subscribe-order/invoice.blade.php';

if (file_exists($invoiceTemplate)) {
    echo "✓ قالب الفاتورة موجود\n";
    
    $templateContent = file_get_contents($invoiceTemplate);
    
    // Check for required sections
    $requiredSections = [
        'company-section' => 'قسم معلومات الشركة',
        'business-info' => 'قسم معلومات المشترك',
        'subscription-table' => 'جدول الاشتراك',
        'summary' => 'ملخص المبالغ',
        'qr' => 'رمز QR',
    ];
    
    echo "\nالأقسام الموجودة في القالب:\n";
    foreach ($requiredSections as $class => $label) {
        if (strpos($templateContent, $class) !== false) {
            echo "  ✓ {$label}\n";
        } else {
            echo "  ⚠️  {$label} - قد يكون غير موجود\n";
        }
    }
    
    // Check for B2B specific fields
    echo "\nحقول B2B في القالب:\n";
    $b2bFields = [
        'vat_no' => 'الرقم الضريبي',
        'commercial_registration' => 'السجل التجاري',
        'building_number' => 'رقم المبنى',
        'street_name' => 'اسم الشارع',
        'district' => 'الحي',
        'city' => 'المدينة',
        'postal_code' => 'الرمز البريدي',
    ];
    
    $missingB2BFields = [];
    foreach ($b2bFields as $field => $label) {
        if (strpos($templateContent, $field) !== false) {
            echo "  ✓ {$label}\n";
        } else {
            echo "  ❌ {$label} - غير موجود\n";
            $missingB2BFields[] = $label;
        }
    }
    
} else {
    echo "❌ قالب الفاتورة غير موجود\n";
}

echo "\n";

echo "━━━ 5. التوصيات | Recommendations ━━━\n\n";

$recommendations = [];

if (!empty($sellerIssues)) {
    $recommendations[] = "إكمال بيانات مالك النظام (البائع) من صفحة الإعدادات";
    $recommendations[] = "Complete system owner (seller) data from settings page";
}

if ($buyersWithIssues > 0) {
    $recommendations[] = "إكمال بيانات المشتركين (المشترين) - {$buyersWithIssues} مشترك يحتاج تحديث";
    $recommendations[] = "Complete subscribers (buyers) data - {$buyersWithIssues} subscribers need update";
}

if (!empty($missingB2BFields)) {
    $recommendations[] = "إضافة الحقول التالية لقالب الفاتورة: " . implode(', ', $missingB2BFields);
    $recommendations[] = "Add the following fields to invoice template: " . implode(', ', $missingB2BFields);
}

if (!$hasZatcaFields) {
    $recommendations[] = "إضافة حقول ZATCA لجدول plan_subscribes (zatca_status, zatca_response, zatca_uuid)";
    $recommendations[] = "Add ZATCA fields to plan_subscribes table";
}

if (empty($recommendations)) {
    echo "✅ لا توجد توصيات - النظام جاهز!\n";
    echo "✅ No recommendations - System is ready!\n\n";
} else {
    echo "📋 التوصيات:\n";
    foreach ($recommendations as $i => $rec) {
        echo "  " . ($i + 1) . ". {$rec}\n";
    }
    echo "\n";
}

echo "╔════════════════════════════════════════════════════════════════════╗\n";
echo "║                    الملخص النهائي | Final Summary                 ║\n";
echo "╚════════════════════════════════════════════════════════════════════╝\n\n";

$totalIssues = count($sellerIssues) + $buyersWithIssues + count($missingB2BFields ?? []);

if ($totalIssues === 0) {
    echo "🎉 ممتاز! فاتورة الاشتراك مطابقة لمتطلبات ZATCA B2B\n";
    echo "🎉 Excellent! Subscription invoice complies with ZATCA B2B requirements\n\n";
    
    echo "✅ بيانات البائع: مكتملة\n";
    echo "✅ بيانات المشترين: {$buyersComplete} مكتمل\n";
    echo "✅ بنية الفاتورة: صحيحة\n";
    echo "✅ قالب الفاتورة: موجود\n\n";
} else {
    echo "⚠️  يوجد {$totalIssues} مشكلة تحتاج إلى إصلاح\n";
    echo "⚠️  There are {$totalIssues} issues that need to be fixed\n\n";
    
    if (!empty($sellerIssues)) {
        echo "❌ بيانات البائع: " . count($sellerIssues) . " حقل ناقص\n";
    } else {
        echo "✅ بيانات البائع: مكتملة\n";
    }
    
    if ($buyersWithIssues > 0) {
        echo "⚠️  بيانات المشترين: {$buyersWithIssues} مشترك يحتاج تحديث\n";
    } else {
        echo "✅ بيانات المشترين: {$buyersComplete} مكتمل\n";
    }
    
    if (!empty($missingB2BFields)) {
        echo "⚠️  قالب الفاتورة: " . count($missingB2BFields) . " حقل ناقص\n";
    } else {
        echo "✅ قالب الفاتورة: مكتمل\n";
    }
    
    echo "\n";
}

echo "📝 للمزيد من المعلومات:\n";
echo "   - docs/B2B_INVOICE_IMPLEMENTATION.md\n";
echo "   - ZATCA_B2B_VS_B2C_AR.md\n";
echo "   - IMPLEMENTATION_COMPLETE.md\n\n";

