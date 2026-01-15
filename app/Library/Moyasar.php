<?php

namespace App\Library;

use App\Models\Gateway;
use App\Models\Sale;
use App\Models\Business;
use App\Models\DueCollect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class Moyasar
{
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
        $amount = round($array['pay_amount'], 2) * 100; // Moyasar expects amount in halalas
        $currency = $array['currency'] ?? 'SAR';
        $api_key = $array['api_key'] ?? '';
        $description = $array['billName'] ?? 'Payment';
        
        $callback_url = Moyasar::fallback();

        // Moyasar supports multiple sources. We will use a hosted approach where possible
        // but since we want to be simple, we can use their Mojo widget or a redirect.
        
        // Actually Moyasar API v1 /payments can be used to create a payment but it requires a source.
        // For a generic redirect flow, we can use their "Invoice" API or just a custom page with their widget.
        
        // Let's use the simplest: Store data in session and redirect to a view that has the Mojo widget.
        
        Session::put('moyasar_credentials', [
            'api_key' => $api_key,
            'gateway_id' => $array['gateway_id'] ?? 0,
            'amount' => $array['amount'],
            'charge' => $array['charge'] ?? 0,
            'payment_type' => $array['payment_type'] ?? 'plan_payment',
            'sale_id' => $array['sale_id'] ?? null,
            'business_id' => $array['business_id'] ?? null,
        ]);

        return redirect()->route('moyasar.view', [
            'amount' => $amount,
            'description' => $description,
            'publishable_key' => $array['publishable_key'] ?? Gateway::where('namespace', 'App\Library\Moyasar')->first()?->data['publishable_key'] ?? ''
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
                
                // Handle Sale Payment
                if ($credentials['payment_type'] == 'sale_payment') {
                    $sale = Sale::find($credentials['sale_id']);
                    if ($sale && !$sale->isPaid) {
                        DB::beginTransaction();
                        try {
                            $amount = $credentials['amount'];
                            
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

                            DB::commit();
                        } catch (\Exception $e) {
                            DB::rollBack();
                            return redirect(Moyasar::redirect_if_payment_faild())->with('error', __('Payment recorded but system error occurred.'));
                        }
                    }
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
}
