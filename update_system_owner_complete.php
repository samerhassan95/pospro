<?php

/**
 * تحديث بيانات مالك النظام بشكل كامل
 * Update System Owner Data Completely
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Business;

echo "╔════════════════════════════════════════════════════════════════════╗\n";
echo "║   تحديث بيانات مالك النظام - System Owner Update                  ║\n";
echo "╚════════════════════════════════════════════════════════════════════╝\n\n";

$systemOwner = Business::first();

if (!$systemOwner) {
    echo "❌ لا يوجد مالك نظام\n";
    exit(1);
}

echo "مالك النظام الحالي: {$systemOwner->companyName}\n";
echo "Business ID: {$systemOwner->id}\n\n";

echo "البيانات الحالية:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "VAT Number: " . ($systemOwner->vat_no ?: '❌ غير موجود') . "\n";
echo "Commercial Registration: " . ($systemOwner->commercial_registration ?: '❌ غير موجود') . "\n";
echo "Additional ID: " . ($systemOwner->additional_id ?: 'غير موجود') . "\n";
echo "Country Code: " . ($systemOwner->country_code ?: 'غير موجود') . "\n";
echo "Building Number: " . ($systemOwner->building_number ?: 'غير موجود') . "\n";
echo "Street Name: " . ($systemOwner->street_name ?: 'غير موجود') . "\n";
echo "District: " . ($systemOwner->district ?: 'غير موجود') . "\n";
echo "City: " . ($systemOwner->city ?: 'غير موجود') . "\n";
echo "Postal Code: " . ($systemOwner->postal_code ?: 'غير موجود') . "\n";
echo "Additional Address: " . ($systemOwner->additional_address ?: 'غير موجود') . "\n";
echo "Bank Name: " . ($systemOwner->bank_name ?: 'غير موجود') . "\n";
echo "Bank Account: " . ($systemOwner->bank_account_number ?: 'غير موجود') . "\n\n";

echo "هل تريد تحديث البيانات؟ (yes/no): ";
$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));

if (strtolower($line) !== 'yes') {
    echo "\n❌ تم الإلغاء\n";
    exit(0);
}

echo "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "تحديث البيانات...\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Update with complete B2B data
$updateData = [
    'vat_no' => '300123456789003',  // 15 digits
    'commercial_registration' => '1010000001',  // 10 digits
    'additional_id' => 'OTH-TRADEG-001',
    'country_code' => 'SA',
    'building_number' => '123',
    'street_name' => 'King Fahd Road',
    'district' => 'Al Olaya',
    'city' => 'Riyadh',
    'postal_code' => '11564',
    'additional_address' => 'Near Kingdom Tower',
    'bank_name' => 'Al Rajhi Bank',
    'bank_account_number' => 'SA1234567890123456789012',
];

$systemOwner->update($updateData);

echo "✅ تم التحديث بنجاح!\n\n";

echo "البيانات الجديدة:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
foreach ($updateData as $key => $value) {
    echo "✓ {$key}: {$value}\n";
}

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════╗\n";
echo "║   ✅ تم تحديث بيانات مالك النظام بنجاح                            ║\n";
echo "╚════════════════════════════════════════════════════════════════════╝\n\n";

echo "الخطوة التالية:\n";
echo "1. افتح صفحة Edit Business: /admin/business/1/edit\n";
echo "2. تحقق من البيانات\n";
echo "3. افتح فاتورة اشتراك للتأكد من عدم وجود تنبيهات\n";
echo "4. شغل: php check_subscription_invoice_b2b.php\n\n";
