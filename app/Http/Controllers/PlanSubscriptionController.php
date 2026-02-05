<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Business;
use App\Models\PlanSubscribe;
use App\Library\Moyasar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PlanSubscriptionController extends Controller
{
    public function subscribe(Request $request, $planId)
    {
        $plan = Plan::findOrFail($planId);
        $business = auth()->user()->business;
        
        // التحقق من تفعيل ميسر
        $superAdminSettings = \App\Models\Setting::where('key', 'moyasar_settings')->first();
        $globalSettings = $superAdminSettings ? json_decode($superAdminSettings->value, true) : [];
        
        if (!($globalSettings['is_active'] ?? false)) {
            return redirect()->back()->with('error', __('Payment gateway is not available'));
        }
        
        // إنشاء دفعة ميسر
        return Moyasar::make_payment([
            'pay_amount' => $plan->price,
            'amount' => $plan->price,
            'currency' => 'SAR',
            'billName' => __('Plan Subscription') . ' - ' . $plan->name,
            'payment_type' => 'plan_subscription',
            'business_id' => $business->id,
            'plan_id' => $planId,
            'gateway_id' => 0
        ]);
    }
    
    public function success()
    {
        $paymentInfo = Session::get('payment_info');
        
        if (!$paymentInfo || $paymentInfo['payment_method'] !== 'moyasar') {
            return redirect()->route('plans.index')->with('error', __('Invalid payment session'));
        }
        
        $credentials = Session::get('moyasar_credentials');
        if (!$credentials || $credentials['payment_type'] !== 'plan_subscription') {
            return redirect()->route('plans.index')->with('error', __('Invalid subscription session'));
        }
        
        DB::beginTransaction();
        try {
            $plan = Plan::findOrFail($credentials['plan_id']);
            $business = Business::findOrFail($credentials['business_id']);
            
            // إنشاء اشتراك جديد
            $subscription = PlanSubscribe::create([
                'business_id' => $business->id,
                'plan_id' => $plan->id,
                'price' => $plan->price,
                'start_date' => now(),
                'end_date' => now()->addDays($plan->duration_days ?? 30),
                'status' => 'active',
                'payment_method' => 'moyasar',
                'payment_id' => $paymentInfo['payment_id']
            ]);
            
            // تحديث حالة العمل
            $business->update([
                'plan_id' => $plan->id,
                'subscription_status' => 'active',
                'subscription_end_date' => $subscription->end_date
            ]);
            
            DB::commit();
            
            Session::forget(['payment_info', 'moyasar_credentials']);
            
            return redirect()->route('business.dashboard')
                ->with('success', __('Subscription activated successfully!'));
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Plan subscription failed: ' . $e->getMessage());
            
            return redirect()->route('plans.index')
                ->with('error', __('Subscription failed. Please try again.'));
        }
    }
}