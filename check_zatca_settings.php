<?php

/**
 * فحص إعدادات ZATCA في النظام
 * Check ZATCA settings in the system
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Business;

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║           فحص إعدادات ZATCA | ZATCA Settings Check        ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

// 1. Check global ZATCA settings
echo "━━━ 1. الإعدادات العامة | Global Settings ━━━\n\n";

$globalSettings = DB::table('options')
    ->where('key', 'LIKE', 'zatca%')
    ->orWhere('key', 'LIKE', '%zatca%')
    ->get();

if ($globalSettings->isEmpty()) {
    echo "⚠️  لا توجد إعدادات ZATCA عامة\n";
    echo "⚠️  No global ZATCA settings found\n\n";
} else {
    echo "✓ تم العثور على " . $globalSettings->count() . " إعدادات ZATCA\n";
    echo "✓ Found " . $globalSettings->count() . " ZATCA settings\n\n";
    
    foreach ($globalSettings as $setting) {
        echo "  • {$setting->key}\n";
    }
    echo "\n";
}

// 2. Check businesses with ZATCA settings
echo "━━━ 2. الشركات مع إعدادات ZATCA | Businesses with ZATCA ━━━\n\n";

$businesses = Business::whereNotNull('zatca_setting')->get();

echo "عدد الشركات مع إعدادات ZATCA: " . $businesses->count() . "\n";
echo "Businesses with ZATCA settings: " . $businesses->count() . "\n\n";

if ($businesses->count() > 0) {
    foreach ($businesses->take(5) as $business) {
        echo "┌─ {$business->companyName} (ID: {$business->id})\n";
        
        $zatcaSettings = is_string($business->zatca_setting) 
            ? json_decode($business->zatca_setting, true) 
            : $business->zatca_setting;
        
        if ($zatcaSettings && is_array($zatcaSettings)) {
            echo "│  ✓ لديه إعدادات ZATCA\n";
            echo "│  Keys: " . implode(', ', array_keys($zatcaSettings)) . "\n";
            
            // Check important fields
            $importantFields = [
                'vat_registration_number',
                'commercial_registration',
                'building_number',
                'street_name',
                'district',
                'city',
                'postal_code',
                'country_code',
                'otp_code',
                'csid',
                'production_csid'
            ];
            
            $missingFields = [];
            foreach ($importantFields as $field) {
                if (!isset($zatcaSettings[$field]) || empty($zatcaSettings[$field])) {
                    $missingFields[] = $field;
                }
            }
            
            if (empty($missingFields)) {
                echo "│  ✓ جميع الحقول المهمة موجودة\n";
            } else {
                echo "│  ⚠️  حقول ناقصة: " . implode(', ', $missingFields) . "\n";
            }
        } else {
            echo "│  ⚠️  إعدادات ZATCA فارغة أو غير صالحة\n";
        }
        echo "└─\n\n";
    }
}

// 3. Check businesses table structure
echo "━━━ 3. هيكل جدول الشركات | Businesses Table Structure ━━━\n\n";

$columns = DB::select("SHOW COLUMNS FROM businesses WHERE Field LIKE '%zatca%' OR Field IN ('vat_no', 'commercial_registration', 'building_number', 'street_name', 'district', 'city', 'postal_code', 'country_code')");

if (empty($columns)) {
    echo "⚠️  لا توجد أعمدة ZATCA في جدول businesses\n\n";
} else {
    echo "✓ أعمدة ZATCA الموجودة:\n";
    foreach ($columns as $column) {
        echo "  • {$column->Field} ({$column->Type})\n";
    }
    echo "\n";
}

// 4. Check plan_subscribes for ZATCA status
echo "━━━ 4. حالة ZATCA في الاشتراكات | ZATCA Status in Subscriptions ━━━\n\n";

$subscriptionsWithZatca = DB::table('plan_subscribes')
    ->whereNotNull('zatca_status')
    ->orWhereNotNull('zatca_response')
    ->count();

echo "اشتراكات مع حالة ZATCA: {$subscriptionsWithZatca}\n";
echo "Subscriptions with ZATCA status: {$subscriptionsWithZatca}\n\n";

if ($subscriptionsWithZatca > 0) {
    $statuses = DB::table('plan_subscribes')
        ->select('zatca_status', DB::raw('COUNT(*) as count'))
        ->whereNotNull('zatca_status')
        ->groupBy('zatca_status')
        ->get();
    
    echo "توزيع الحالات:\n";
    foreach ($statuses as $status) {
        echo "  • {$status->zatca_status}: {$status->count}\n";
    }
    echo "\n";
}

// 5. Check sales table for ZATCA fields
echo "━━━ 5. حقول ZATCA في المبيعات | ZATCA Fields in Sales ━━━\n\n";

$salesColumns = DB::select("SHOW COLUMNS FROM sales WHERE Field LIKE '%zatca%' OR Field LIKE '%qr%' OR Field LIKE '%uuid%'");

if (empty($salesColumns)) {
    echo "⚠️  لا توجد حقول ZATCA في جدول sales\n\n";
} else {
    echo "✓ حقول ZATCA في المبيعات:\n";
    foreach ($salesColumns as $column) {
        echo "  • {$column->Field} ({$column->Type})\n";
    }
    echo "\n";
}

// 6. Check recent sales with ZATCA data
$salesWithZatca = DB::table('sales')
    ->whereNotNull('uuid')
    ->orWhereNotNull('zatca_status')
    ->count();

echo "فواتير مع بيانات ZATCA: {$salesWithZatca}\n";
echo "Invoices with ZATCA data: {$salesWithZatca}\n\n";

// 7. Summary
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║                    الملخص | Summary                        ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

$issues = [];

if ($globalSettings->isEmpty()) {
    $issues[] = "لا توجد إعدادات ZATCA عامة";
}

if ($businesses->count() === 0) {
    $issues[] = "لا توجد شركات مع إعدادات ZATCA";
}

if (empty($columns)) {
    $issues[] = "حقول ZATCA ناقصة في جدول businesses";
}

if (empty($salesColumns)) {
    $issues[] = "حقول ZATCA ناقصة في جدول sales";
}

if (empty($issues)) {
    echo "✅ جميع إعدادات ZATCA موجودة ومضبوطة!\n";
    echo "✅ All ZATCA settings are present and configured!\n\n";
} else {
    echo "⚠️  مشاكل محتملة:\n";
    echo "⚠️  Potential issues:\n\n";
    foreach ($issues as $issue) {
        echo "  • {$issue}\n";
    }
    echo "\n";
}

echo "📝 للمزيد من المعلومات، راجع:\n";
echo "📝 For more information, check:\n";
echo "  - ZATCA_README_AR.md\n";
echo "  - ZATCA_COMPLETE_CHECKLIST_AR.md\n";
echo "  - docs/ZATCA_INTEGRATION_PROCESS_AR.md\n";
