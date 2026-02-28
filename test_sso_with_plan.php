<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Business;
use App\Models\PlanSubscribe;
use App\Models\Plan;

echo "=== اختبار SSO مع إنشاء Business و Subscription ===\n\n";

// 1. عرض الباقات المتاحة
echo "1. الباقات المتاحة:\n";
echo str_repeat("-", 80) . "\n";
$plans = Plan::where('status', 1)->get();
foreach ($plans as $plan) {
    echo "ID: {$plan->id} | الاسم: {$plan->subscriptionName} | المدة: {$plan->duration} يوم | السعر: {$plan->subscriptionPrice}\n";
}
echo "\n";

// 2. محاكاة بيانات Token
$testData = [
    'user_id' => 'SSO_TEST_' . time(),
    'name' => 'مستخدم تجريبي SSO',
    'email' => 'sso_test_' . time() . '@example.com',
    'plan_id' => 1, // استخدم ID الباقة الأولى
    'business_name' => 'متجر تجريبي SSO',
    'phone' => '0501234567',
    'vat_no' => '300123456789003',
    'commercial_registration' => '1234567890',
    'building_number' => '1234',
    'street_name' => 'شارع الملك فهد',
    'district' => 'العليا',
    'city' => 'الرياض',
    'postal_code' => '12345',
    'country_code' => 'SA',
    'locale' => 'ar',
    'timestamp' => time()
];

echo "2. بيانات الاختبار:\n";
echo str_repeat("-", 80) . "\n";
echo "User ID: {$testData['user_id']}\n";
echo "Name: {$testData['name']}\n";
echo "Email: {$testData['email']}\n";
echo "Plan ID: {$testData['plan_id']}\n";
echo "Business Name: {$testData['business_name']}\n";
echo "\n";

// 3. اختبار إنشاء المستخدم
echo "3. إنشاء المستخدم والعمل والاشتراك...\n";
echo str_repeat("-", 80) . "\n";

try {
    $ssoService = new \App\Services\SSOService();
    $user = $ssoService->findOrCreateUser($testData);
    
    if ($user) {
        echo "✓ تم إنشاء المستخدم بنجاح!\n";
        echo "  - User ID: {$user->id}\n";
        echo "  - Name: {$user->name}\n";
        echo "  - Email: {$user->email}\n";
        echo "  - Role: {$user->role}\n";
        echo "  - Business ID: {$user->business_id}\n";
        echo "  - External ID: {$user->external_id}\n";
        echo "  - SSO Provider: {$user->sso_provider}\n";
        echo "\n";
        
        // 4. التحقق من Business
        if ($user->business_id) {
            echo "4. بيانات العمل التجاري:\n";
            echo str_repeat("-", 80) . "\n";
            $business = Business::find($user->business_id);
            if ($business) {
                echo "✓ تم إنشاء العمل التجاري بنجاح!\n";
                echo "  - Business ID: {$business->id}\n";
                echo "  - Company Name: {$business->companyName}\n";
                echo "  - Email: {$business->email}\n";
                echo "  - Phone: {$business->phoneNumber}\n";
                echo "  - VAT No: {$business->vat_no}\n";
                echo "  - CR: {$business->commercial_registration}\n";
                echo "  - Status: " . ($business->status ? 'Active' : 'Inactive') . "\n";
                echo "  - Subscription Date: {$business->subscriptionDate}\n";
                echo "  - Will Expire: {$business->will_expire}\n";
                echo "  - Plan Subscribe ID: {$business->plan_subscribe_id}\n";
                echo "\n";
                
                // 5. التحقق من Subscription
                if ($business->plan_subscribe_id) {
                    echo "5. بيانات الاشتراك:\n";
                    echo str_repeat("-", 80) . "\n";
                    $subscription = PlanSubscribe::find($business->plan_subscribe_id);
                    if ($subscription) {
                        echo "✓ تم إنشاء الاشتراك بنجاح!\n";
                        echo "  - Subscription ID: {$subscription->id}\n";
                        echo "  - Plan ID: {$subscription->plan_id}\n";
                        echo "  - Business ID: {$subscription->business_id}\n";
                        echo "  - Price: {$subscription->price}\n";
                        echo "  - Duration: {$subscription->duration} days\n";
                        echo "  - Payment Status: {$subscription->payment_status}\n";
                        echo "  - Service Start: {$subscription->service_start_date}\n";
                        echo "  - Service End: {$subscription->service_end_date}\n";
                        echo "  - Invoice Number: {$subscription->invoice_number}\n";
                        echo "  - Invoice Type: {$subscription->invoice_type}\n";
                        echo "  - UUID: {$subscription->uuid}\n";
                        echo "\n";
                        
                        // 6. التحقق من الباقة
                        echo "6. بيانات الباقة:\n";
                        echo str_repeat("-", 80) . "\n";
                        $plan = $subscription->plan;
                        if ($plan) {
                            echo "✓ الباقة:\n";
                            echo "  - Plan Name: {$plan->subscriptionName}\n";
                            echo "  - Price: {$plan->subscriptionPrice}\n";
                            echo "  - Duration: {$plan->duration} days\n";
                            echo "  - Allow Sales: " . ($plan->allow_sales ? 'Yes' : 'No') . "\n";
                            echo "  - Allow Purchases: " . ($plan->allow_purchases ? 'Yes' : 'No') . "\n";
                            echo "  - Allow Products: " . ($plan->allow_products ? 'Yes' : 'No') . "\n";
                            echo "  - Allow Warehouses: " . ($plan->allow_warehouses ? 'Yes' : 'No') . "\n";
                            echo "  - Warehouse Limit: " . ($plan->warehouse_limit ?? 'Unlimited') . "\n";
                            echo "  - Allow Multibranch: " . ($plan->allow_multibranch ? 'Yes' : 'No') . "\n";
                            echo "  - Branch Limit: " . ($plan->branch_limit ?? 'Unlimited') . "\n";
                            echo "\n";
                        }
                    }
                }
            }
        }
        
        // 7. عرض الربط الكامل
        echo "7. الربط الكامل:\n";
        echo str_repeat("-", 80) . "\n";
        $fullData = \DB::table('users as u')
            ->join('businesses as b', 'u.business_id', '=', 'b.id')
            ->join('plan_subscribes as ps', 'b.plan_subscribe_id', '=', 'ps.id')
            ->join('plans as p', 'ps.plan_id', '=', 'p.id')
            ->where('u.id', $user->id)
            ->select(
                'u.name as user_name',
                'u.email as user_email',
                'u.role as user_role',
                'b.companyName as business_name',
                'b.status as business_status',
                'p.subscriptionName as plan_name',
                'ps.payment_status',
                'ps.service_start_date',
                'ps.service_end_date'
            )
            ->first();
        
        if ($fullData) {
            echo "✓ الربط الكامل موجود:\n";
            echo "  User: {$fullData->user_name} ({$fullData->user_email})\n";
            echo "  Role: {$fullData->user_role}\n";
            echo "  Business: {$fullData->business_name}\n";
            echo "  Plan: {$fullData->plan_name}\n";
            echo "  Payment: {$fullData->payment_status}\n";
            echo "  Valid From: {$fullData->service_start_date}\n";
            echo "  Valid Until: {$fullData->service_end_date}\n";
            echo "\n";
        }
        
        echo "=== الاختبار نجح! ===\n\n";
        
        // 8. تنظيف البيانات التجريبية (اختياري)
        echo "هل تريد حذف البيانات التجريبية؟ (y/n): ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        if (trim($line) == 'y') {
            if ($user->business_id) {
                $business = Business::find($user->business_id);
                if ($business && $business->plan_subscribe_id) {
                    PlanSubscribe::find($business->plan_subscribe_id)->delete();
                    echo "✓ تم حذف الاشتراك\n";
                }
                $business->delete();
                echo "✓ تم حذف العمل التجاري\n";
            }
            $user->delete();
            echo "✓ تم حذف المستخدم\n";
            echo "\nتم تنظيف البيانات التجريبية.\n";
        } else {
            echo "\nتم الاحتفاظ بالبيانات التجريبية.\n";
        }
        fclose($handle);
        
    } else {
        echo "✗ فشل إنشاء المستخدم\n";
    }
    
} catch (\Exception $e) {
    echo "✗ خطأ: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== انتهى الاختبار ===\n";
