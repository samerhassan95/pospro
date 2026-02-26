<?php

/**
 * عرض معلومات الحسابات الثلاثة المختارة للاختبار
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Business;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\Branch;

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║         معلومات حساباتك الثلاثة للاختبار                  ║\n";
echo "║         Your 3 Test Accounts Information                  ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

$accounts = [
    ['name' => 'ahmed', 'id' => 23, 'plan' => 'A'],
    ['name' => 'samer', 'id' => 24, 'plan' => 'B'],
    ['name' => 'mohamed', 'id' => 25, 'plan' => 'C'],
];

foreach ($accounts as $account) {
    $business = Business::find($account['id']);
    
    if (!$business) {
        echo "⚠️  Business '{$account['name']}' not found!\n\n";
        continue;
    }
    
    $plan = $business->plan();
    $admin = User::where('business_id', $business->id)
        ->where('role', 'admin')
        ->first();
    
    $warehouseCount = Warehouse::where('business_id', $business->id)->count();
    $branchCount = Branch::where('business_id', $business->id)->count();
    
    echo "┌────────────────────────────────────────────────────────────┐\n";
    echo "│ {$account['plan']}. {$account['name']} - الباقة {$account['plan']} (Plan {$account['plan']})                                    │\n";
    echo "└────────────────────────────────────────────────────────────┘\n\n";
    
    echo "🏢 الشركة | Company: {$business->companyName}\n";
    echo "🆔 Business ID: {$business->id}\n";
    
    if ($admin) {
        echo "👤 المستخدم | User: {$admin->name}\n";
        echo "📧 Email: {$admin->email}\n";
    }
    
    echo "\n📊 الإحصائيات | Statistics:\n";
    
    // Warehouses
    $warehouseLimit = $plan->warehouse_limit;
    if ($warehouseLimit === null) {
        echo "  📦 المستودعات | Warehouses: {$warehouseCount} / ∞ (Unlimited)\n";
    } else {
        $status = $warehouseCount > $warehouseLimit ? '⚠️' : ($warehouseCount == $warehouseLimit ? '⚠️' : '✅');
        echo "  📦 المستودعات | Warehouses: {$warehouseCount} / {$warehouseLimit} {$status}\n";
    }
    
    if ($business->canAddWarehouse()) {
        echo "     ✅ يمكن إضافة المزيد | Can add more\n";
    } else {
        echo "     ❌ تم الوصول للحد الأقصى | Limit reached\n";
    }
    
    // Branches
    $branchLimit = $plan->branch_limit;
    if ($branchLimit === null) {
        echo "  🏪 الفروع | Branches: {$branchCount} / ∞ (Unlimited)\n";
    } else {
        $status = $branchCount > $branchLimit ? '⚠️' : ($branchCount == $branchLimit ? '⚠️' : '✅');
        echo "  🏪 الفروع | Branches: {$branchCount} / {$branchLimit} {$status}\n";
    }
    
    if ($business->canAddBranch()) {
        echo "     ✅ يمكن إضافة المزيد | Can add more\n";
    } else {
        echo "     ❌ تم الوصول للحد الأقصى | Limit reached\n";
    }
    
    echo "\n🔐 الصلاحيات الرئيسية | Key Permissions:\n";
    
    $keyPermissions = [
        'due_list' => 'قائمة مستحقات | Due List',
        'finance' => 'مالية | Finance',
        'commission' => 'عمولة | Commission',
        'hrm' => 'موارد بشرية | HRM',
        'pos_app' => 'تطبيق | POS App',
        'store' => 'متجر | Store',
    ];
    
    foreach ($keyPermissions as $key => $label) {
        $allowed = $business->allows($key);
        $icon = $allowed ? '✅' : '❌';
        echo "  {$icon} {$label}\n";
    }
    
    echo "\n🔑 بيانات الدخول | Login:\n";
    echo "  URL: " . url('/login') . "\n";
    if ($admin) {
        echo "  Email: {$admin->email}\n";
        echo "  Password: [كلمة المرور التي أنشأت بها الحساب]\n";
    }
    
    echo "\n";
}

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║                    ملخص سريع | Quick Summary              ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

echo "1️⃣  ahmed (A): مستودع واحد، فرع واحد، صلاحيات محدودة\n";
echo "   ⚠️  عنده 2 مستودعات (فوق الحد!) - اختبر إنه مش هيقدر يضيف تالت\n\n";

echo "2️⃣  samer (B): مستودعات وفروع غير محدودة، كل الصلاحيات ما عدا POS App و Store\n\n";

echo "3️⃣  mohamed (C): كل شيء غير محدود، جميع الصلاحيات\n\n";

echo "📝 راجع الملف: MY_TEST_ACCOUNTS_AR.md للتفاصيل الكاملة\n";
echo "📝 Review file: MY_TEST_ACCOUNTS_AR.md for full details\n\n";

echo "✅ جاهز للاختبار! | Ready to test!\n";
