<?php

namespace App\Library;

use App\Models\Gateway;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Business;
use App\Models\DueCollect;
use App\Models\Transaction;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class Moyasar
{
    private function getApiKeys($business = null)
    {
        // الحصول على إعدادات السوبر أدمن العامة
        $superAdminSettings = Setting::where('key', 'moyasar_settings')->first();
        $globalSettings = $superAdminSettings ? json_decode($superAdminSettings->value, true) : [];
        
        if (!($globalSettings['is_active'] ?? false)) {
            throw new \Exception('Moyasar is not enabled by Super Admin');
        }
        
        // تحديد البيئة (اختبار أم إنتاج) من إعدادات التاجر
        $environment = 'test';
        if ($business && $business->moyasar_setting) {
            $businessSettings = json_decode($business->moyasar_setting, true);
            $environment = $businessSettings['environment'] ?? 'test';
        }
        
        // استخدام المفاتيح المناسبة حسب البيئة
        $keyPrefix = $environment === 'live' ? 'live' : 'test';
        
        return [
            'publishable_key' => $globalSettings["{$keyPrefix}_publishable_key"] ?? null,
            'secret_key' => isset($globalSettings["{$keyPrefix}_secret_key"]) ? 
                Crypt::decryptString($globalSettings["{$keyPrefix}_secret_key"]) : null,
            'environment' => $environment,
            'currency' => $globalSettings['default_currency'] ?? 'SAR'
        ];
    }
    public static function redirect_if_payment_success()
    {
        if (Session::has('fund_callback')) {
            return url(Session::get('fund_callback')['success_url']);
        } else {
            return url('payment/success');
        }
    }

    public static function redirect_if_payment_faild()
    {
        if (Session::has('fund_callback')) {
            return url(Session::get('fund_callback')['cancel_url']);
        } else {
            return url('payment/failed');
        }
    }

    public static function fallback()
    {
        return url('payment/moyasar/status');
    }

    public static function make_payment($array)
    {
        $business = Business::find($array['business_id'] ?? null);
        $moyasar = new self();
        $keys = $moyasar->getApiKeys($business);
        
        $amount = round($array['pay_amount'], 2) * 100;
        $currency = $keys['currency'];
        $api_key = $keys['secret_key'];
        $description = $array['billName'] ?? 'Payment';
        
        $callback_url = Moyasar::fallback();

        Session::put('moyasar_credentials', [
            'api_key' => $api_key,
            'gateway_id' => $array['gateway_id'] ?? 0,
            'amount' => $array['amount'],
            'charge' => $array['charge'] ?? 0,
            'payment_type' => $array['payment_type'] ?? 'plan_payment',
            'sale_id' => $array['sale_id'] ?? null,
            'purchase_id' => $array['purchase_id'] ?? null,
            'business_id' => $array['business_id'] ?? null,
            'due_collect_id' => $array['due_collect_id'] ?? null,
        ]);

        return redirect()->route('moyasar.view', [
            'amount' => $amount,
            'description' => $description,
            'publishable_key' => $keys['publishable_key']
        ]);
    }

    public static function view(Request $request)
    {
        $amount = $request->amount;
        $description = $request->description;
        
        // If publishableKey is not in request, try to get from session or DB
        $credentials = Session::get('moyasar_credentials');
        if (!$credentials) return redirect('/');

        $publishable_key = $request->publishable_key;
        if (!$publishable_key) {
             // Try to get from Business if it's a sale
             if ($credentials['payment_type'] == 'sale_payment') {
                 $business = \App\Models\Business::find($credentials['business_id']);
                 $publishable_key = $business->moyasar_setting['publishable_key'] ?? '';
             } else {
                 $gateway = Gateway::where('namespace', 'App\Library\Moyasar')->first();
                 $publishable_key = $gateway->data['publishable_key'] ?? '';
             }
        }

        return view('moyasar_view', compact('amount', 'description', 'publishable_key'));
    }

    public static function status(Request $request)
    {
        $payment_id = $request->id;
        $status = $request->status;
        $message = $request->message;

        $credentials = Session::get('moyasar_credentials');
        if (!$credentials) {
            return redirect('/')->with('error', __('Session expired'));
        }
        
        $api_key = $credentials['api_key'];

        if ($status == 'paid') {
            // Verify payment on Moyasar
            $response = Http::withBasicAuth($api_key, '')
                ->get("https://api.moyasar.com/v1/payments/{$payment_id}");

            if ($response->successful() && $response->json()['status'] == 'paid') {
                $resData = $response->json();
                
                DB::beginTransaction();
                try {
                    $amount = $credentials['amount'];
                    
                    // Handle Sale Payment
                    if ($credentials['payment_type'] == 'sale_payment') {
                        $sale = Sale::find($credentials['sale_id']);
                        if ($sale && !$sale->isPaid) {
                            // Update Sale
                            $sale->paidAmount += $amount;
                            $sale->dueAmount -= $amount;
                            if ($sale->dueAmount <= 0) {
                                $sale->isPaid = true;
                                $sale->dueAmount = 0;
                            }
                            $sale->save();

                            // Update business balance
                            updateBalance($amount, 'increment');

                            // Update party due if exists
                            if ($sale->party_id) {
                                $party = \App\Models\Party::find($sale->party_id);
                                if ($party) {
                                    $party->decrement('due', min($amount, $party->due));
                                }
                            }

                            // Record Transaction
                            DueCollect::create([
                                'business_id' => $sale->business_id,
                                'party_id' => $sale->party_id,
                                'sale_id' => $sale->id,
                                'amount' => $amount,
                                'date' => now(),
                                'payment_type' => 'Online (Moyasar)',
                                'note' => 'Paid via Moyasar. ID: ' . $payment_id
                            ]);
                        }
                    }
                    
                    // Handle Purchase Payment
                    elseif ($credentials['payment_type'] == 'purchase_payment') {
                        $purchase = Purchase::find($credentials['purchase_id']);
                        if ($purchase && !$purchase->isPaid) {
                            // Update Purchase
                            $purchase->paidAmount += $amount;
                            $purchase->dueAmount -= $amount;
                            if ($purchase->dueAmount <= 0) {
                                $purchase->isPaid = true;
                                $purchase->dueAmount = 0;
                            }
                            $purchase->save();

                            // Update business balance (decrease for purchase payment)
                            updateBalance($amount, 'decrement');

                            // Update supplier due if exists
                            if ($purchase->party_id) {
                                $party = \App\Models\Party::find($purchase->party_id);
                                if ($party) {
                                    $party->decrement('due', min($amount, $party->due));
                                }
                            }

                            // Record Transaction
                            Transaction::create([
                                'business_id' => $purchase->business_id,
                                'party_id' => $purchase->party_id,
                                'purchase_id' => $purchase->id,
                                'amount' => $amount,
                                'date' => now(),
                                'type' => 'expense',
                                'payment_type' => 'Online (Moyasar)',
                                'note' => 'Purchase payment via Moyasar. ID: ' . $payment_id
                            ]);
                        }
                    }
                    
                    // Handle Plan Subscription Payment
                    elseif ($credentials['payment_type'] == 'plan_subscription') {
                        $plan = \App\Models\Plan::find($credentials['plan_id']);
                        $business = \App\Models\Business::find($credentials['business_id']);
                        
                        if ($plan && $business) {
                            // Create subscription
                            \App\Models\PlanSubscribe::create([
                                'business_id' => $business->id,
                                'plan_id' => $plan->id,
                                'price' => $plan->price,
                                'start_date' => now(),
                                'end_date' => now()->addDays($plan->duration_days ?? 30),
                                'status' => 'active',
                                'payment_method' => 'moyasar',
                                'payment_id' => $payment_id
                            ]);
                            
                            // Update business status
                            $business->update([
                                'plan_id' => $plan->id,
                                'subscription_status' => 'active',
                                'subscription_end_date' => now()->addDays($plan->duration_days ?? 30)
                            ]);
                        }
                    } else if ($credentials['payment_type'] === 'due_collect') {
                        $dueCollect = DueCollect::find($credentials['due_collect_id']);
                        if ($dueCollect) {
                            $dueCollect->update([
                                'payment_type' => 'Online (Moyasar)',
                                'note' => ($dueCollect->note ?? '') . ' | Paid via Moyasar. ID: ' . $payment_id
                            ]);
                        }
                        
                        // Update business balance
                        updateBalance($amount, 'increment');
                    }

                    DB::commit();
                    
                    Log::info('Moyasar payment processed successfully', [
                        'payment_id' => $payment_id,
                        'amount' => $amount,
                        'type' => $credentials['payment_type']
                    ]);
                    
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Moyasar payment processing failed', [
                        'payment_id' => $payment_id,
                        'error' => $e->getMessage()
                    ]);
                    return redirect(Moyasar::redirect_if_payment_faild())->with('error', __('Payment recorded but system error occurred.'));
                }

                $data['payment_id'] = $payment_id;
                $data['payment_method'] = "moyasar";
                $data['gateway_id'] = $credentials['gateway_id'];
                $data['amount'] = $credentials['amount'];
                $data['charge'] = $credentials['charge'];
                $data['status'] = 1;
                $data['payment_status'] = 1;

                Session::put('payment_info', $data);
                Session::forget('moyasar_credentials');

                return redirect(Moyasar::redirect_if_payment_success());
            }
        }

        Session::forget('moyasar_credentials');
        return redirect(Moyasar::redirect_if_payment_faild())->with('error', $message ?? __('Payment failed'));
    }
    
    public static function paySaleDue($sale_id, $amount)
    {
        $sale = Sale::findOrFail($sale_id);
        $business = Business::findOrFail($sale->business_id);
        
        return self::make_payment([
            'pay_amount' => $amount,
            'amount' => $amount,
            'billName' => __('Sale Payment') . ' - ' . $sale->invoiceNumber,
            'payment_type' => 'sale_payment',
            'sale_id' => $sale_id,
            'business_id' => $sale->business_id,
        ]);
    }
    
    public static function payPurchaseDue($purchase_id, $amount)
    {
        $purchase = Purchase::findOrFail($purchase_id);
        $business = Business::findOrFail($purchase->business_id);
        
        return self::make_payment([
            'pay_amount' => $amount,
            'amount' => $amount,
            'billName' => __('Purchase Payment') . ' - ' . $purchase->invoiceNumber,
            'payment_type' => 'purchase_payment',
            'purchase_id' => $purchase_id,
            'business_id' => $purchase->business_id,
        ]);
    }
}
