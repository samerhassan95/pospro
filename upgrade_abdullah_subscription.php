<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Business;
use App\Models\Plan;
use App\Models\PlanSubscribe;
use Carbon\Carbon;

echo "=== ترقية اشتراك المستخدم abdullah ===\n\n";

// البحث عن اليوزر
$user = User::where('email', 'abdullah25.ye@gmail.com')->first();

if (!$user) {
    echo "❌ اليوزر غير موجود!\n";
    exit;
}

echo "✓ تم العثور على اليوزر: {$user->name} (ID: {$user->id})\n";
echo "  البريد: {$user->email}\n";

// البحث عن البزنس الخاص باليوزر
$business = Business::where('id', $user->business_id)->first();

if (!$business) {
    echo "❌ البزنس غير موجود!\n";
    exit;
}

echo "✓ البزنس: {$business->business_name} (ID: {$business->id})\n\n";

// البحث عن أعلى باقة (حسب السعر)
$highestPlan = Plan::where('status', 1)
    ->orderBy('subscriptionPrice', 'DESC')
    ->first();

if (!$highestPlan) {
    echo "❌ لا توجد باقات متاحة!\n";
    exit;
}

echo "✓ أعلى باقة: {$highestPlan->subscriptionName}\n";
echo "  السعر: {$highestPlan->subscriptionPrice}\n";
echo "  المدة: {$highestPlan->duration} يوم\n";
echo "  Multi-branch: " . ($highestPlan->allow_multibranch ? 'نعم' : 'لا') . "\n";
echo "  حد النطاقات الإضافية: {$highestPlan->addon_domain_limit}\n";
echo "  حد النطاقات الفرعية: {$highestPlan->subdomain_limit}\n\n";

// التحقق من الاشتراك الحالي
$currentSubscription = PlanSubscribe::where('business_id', $business->id)
    ->orderBy('created_at', 'DESC')
    ->first();

if ($currentSubscription) {
    echo "📋 الاشتراك الحالي:\n";
    echo "  الباقة: " . ($currentSubscription->plan ? $currentSubscription->plan->subscriptionName : 'غير معروف') . "\n";
    echo "  تاريخ البداية: {$currentSubscription->service_start_date}\n";
    echo "  تاريخ النهاية: {$currentSubscription->service_end_date}\n";
    echo "  حالة الدفع: {$currentSubscription->payment_status}\n\n";
}

// إنشاء الاشتراك مباشرة

// إنشاء اشتراك جديد
$startDate = Carbon::now();
$endDate = Carbon::now()->addDays($highestPlan->duration);

$subscription = PlanSubscribe::create([
    'plan_id' => $highestPlan->id,
    'business_id' => $business->id,
    'price' => $highestPlan->subscriptionPrice,
    'duration' => $highestPlan->duration,
    'service_start_date' => $startDate->format('Y-m-d'),
    'service_end_date' => $endDate->format('Y-m-d'),
    'payment_status' => 'paid',
    'gateway_id' => 1, // Manual/Admin
    'allow_multibranch' => $highestPlan->allow_multibranch,
    'addon_domain_limit' => $highestPlan->addon_domain_limit,
    'subdomain_limit' => $highestPlan->subdomain_limit,
    'invoice_type' => 'B2C',
]);

echo "\n✅ تم إنشاء الاشتراك بنجاح!\n\n";
echo "📄 تفاصيل الاشتراك الجديد:\n";
echo "  ID: {$subscription->id}\n";
echo "  UUID: {$subscription->uuid}\n";
echo "  رقم الفاتورة: {$subscription->invoice_number}\n";
echo "  الباقة: {$highestPlan->subscriptionName}\n";
echo "  السعر: {$subscription->price}\n";
echo "  تاريخ البداية: {$subscription->service_start_date}\n";
echo "  تاريخ النهاية: {$subscription->service_end_date}\n";
echo "  حالة الدفع: {$subscription->payment_status}\n";
echo "  Multi-branch: " . ($subscription->allow_multibranch ? 'مفعل' : 'غير مفعل') . "\n";
echo "  حد النطاقات الإضافية: {$subscription->addon_domain_limit}\n";
echo "  حد النطاقات الفرعية: {$subscription->subdomain_limit}\n\n";

echo "✅ تم ترقية اشتراك المستخدم abdullah بنجاح!\n";
