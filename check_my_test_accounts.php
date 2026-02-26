<?php

/**
 * عرض معلومات الحسابات التجريبية
 * Display test accounts information
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Business;
use App\Models\Plan;
use App\Models\PlanSubscribe;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\Branch;

echo "=== معلومات الحسابات التجريبية ===\n";
echo "=== Test Accounts Information ===\n\n";

// Get all plans
$plans = Plan::whereIn('subscriptionName', ['A', 'B', 'C'])
    ->orderBy('id')
    ->get();

foreach ($plans as $plan) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📦 الباقة | Plan: {$plan->subscriptionName}\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    // Get subscriptions for this plan
    $subscriptions = PlanSubscribe::where('plan_id', $plan->id)
        ->with('business')
        ->get();
    
    if ($subscriptions->isEmpty()) {
        echo "⚠️  لا توجد حسابات لهذه الباقة\n";
        echo "⚠️  No accounts found for this plan\n\n";
        continue;
    }
    
    foreach ($subscriptions as $subscription) {
        $business = $subscription->business;
        
        if (!$business) {
            continue;
        }
        
        echo "🏢 اسم الشركة | Company: {$business->companyName}\n";
        echo "🆔 Business ID: {$business->id}\n";
        
        // Get admin user
        $admin = User::where('business_id', $business->id)
            ->where('role', 'admin')
            ->first();
        
        if ($admin) {
            echo "👤 المستخدم | User: {$admin->name}\n";
            echo "📧 البريد | Email: {$admin->email}\n";
        }
        
        // Count warehouses and branches
        $warehouseCount = Warehouse::where('business_id', $business->id)->count();
        $branchCount = Branch::where('business_id', $business->id)->count();
        
        echo "\n📊 الإحصائيات | Statistics:\n";
        
        // Warehouses
        $warehouseLimit = $plan->warehouse_limit;
        if ($warehouseLimit === null) {
            echo "  📦 المستودعات | Warehouses: {$warehouseCount} / غير محدود (Unlimited)\n";
        } else {
            echo "  📦 المستودعات | Warehouses: {$warehouseCount} / {$warehouseLimit}\n";
        }
        
        if ($business->canAddWarehouse()) {
            echo "     ✅ يمكن إضافة المزيد | Can add more\n";
        } else {
            echo "     ❌ تم الوصول للحد الأقصى | Limit reached\n";
        }
        
        // Branches
        $branchLimit = $plan->branch_limit;
        if ($branchLimit === null) {
            echo "  🏪 الفروع | Branches: {$branchCount} / غير محدود (Unlimited)\n";
        } else {
            echo "  🏪 الفروع | Branches: {$branchCount} / {$branchLimit}\n";
        }
        
        if ($business->canAddBranch()) {
            echo "     ✅ يمكن إضافة المزيد | Can add more\n";
        } else {
            echo "     ❌ تم الوصول للحد الأقصى | Limit reached\n";
        }
        
        echo "\n🔐 الصلاحيات | Permissions:\n";
        
        $permissions = [
            'sales' => 'مبيعات | Sales',
            'purchases' => 'مشتريات | Purchases',
            'products' => 'منتجات | Products',
            'warehouses' => 'مستودعات | Warehouses',
            'stock' => 'مخزون | Stock',
            'customers' => 'عملاء | Customers',
            'suppliers' => 'موردين | Suppliers',
            'vat_settings' => 'ضريبة | VAT',
            'due_list' => 'مستحقات | Due List',
            'finance' => 'مالية | Finance',
            'commission' => 'عمولة | Commission',
            'hrm' => 'موارد بشرية | HRM',
            'reports' => 'تقارير | Reports',
            'pos_app' => 'تطبيق | POS App',
            'store' => 'متجر | Store',
        ];
        
        foreach ($permissions as $key => $label) {
            $allowed = $business->allows($key);
            $icon = $allowed ? '✅' : '❌';
            echo "  {$icon} {$label}\n";
        }
        
        echo "\n📝 رابط تسجيل الدخول | Login URL:\n";
        echo "  " . url('/login') . "\n";
        
        if ($admin) {
            echo "\n🔑 بيانات الدخول | Login Credentials:\n";
            echo "  Email: {$admin->email}\n";
            echo "  Password: [كلمة المرور التي أنشأت بها الحساب]\n";
        }
        
        echo "\n";
    }
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ انتهى العرض | Display Complete\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "💡 نصائح للاختبار | Testing Tips:\n\n";
echo "1. سجل دخول بكل حساب وتحقق من الـ Sidebar\n";
echo "   Login with each account and check the Sidebar\n\n";
echo "2. حاول إضافة مستودعات وفروع\n";
echo "   Try adding warehouses and branches\n\n";
echo "3. حاول الوصول للصفحات الممنوعة\n";
echo "   Try accessing restricted pages\n\n";
echo "4. راجع الملف: TESTING_CHECKLIST_AR.md\n";
echo "   Review the file: TESTING_CHECKLIST_AR.md\n\n";
