<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // البحث عن اليوزر
    $user = DB::table('users')->where('email', 'abdullah25.ye@gmail.com')->first();
    
    if (!$user) {
        die("اليوزر غير موجود\n");
    }
    
    echo "اليوزر: {$user->name}\n";
    echo "Business ID: {$user->business_id}\n\n";
    
    // البحث عن أعلى باقة
    $plan = DB::table('plans')
        ->where('status', 1)
        ->orderBy('subscriptionPrice', 'DESC')
        ->first();
    
    if (!$plan) {
        die("لا توجد باقات\n");
    }
    
    echo "الباقة: {$plan->subscriptionName}\n";
    echo "السعر: {$plan->subscriptionPrice}\n";
    echo "المدة: {$plan->duration} يوم\n\n";
    
    // إنشاء الاشتراك
    $startDate = date('Y-m-d');
    $endDate = date('Y-m-d', strtotime("+{$plan->duration} days"));
    $uuid = \Illuminate\Support\Str::uuid();
    $invoiceNumber = 'SUB-' . strtoupper(\Illuminate\Support\Str::random(8));
    
    $subscriptionId = DB::table('plan_subscribes')->insertGetId([
        'plan_id' => $plan->id,
        'business_id' => $user->business_id,
        'price' => $plan->subscriptionPrice,
        'duration' => $plan->duration,
        'service_start_date' => $startDate,
        'service_end_date' => $endDate,
        'payment_status' => 'paid',
        'gateway_id' => 1,
        'allow_multibranch' => $plan->allow_multibranch ?? 0,
        'addon_domain_limit' => $plan->addon_domain_limit ?? 0,
        'subdomain_limit' => $plan->subdomain_limit ?? 0,
        'invoice_type' => 'B2C',
        'uuid' => $uuid,
        'invoice_number' => $invoiceNumber,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "✅ تم إنشاء الاشتراك بنجاح!\n";
    echo "ID: {$subscriptionId}\n";
    echo "UUID: {$uuid}\n";
    echo "رقم الفاتورة: {$invoiceNumber}\n";
    echo "من: {$startDate}\n";
    echo "إلى: {$endDate}\n";
    
} catch (\Exception $e) {
    echo "خطأ: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
