<?php

namespace Modules\Business\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Library\Moyasar;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Business;
use App\Models\DueCollect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MoyasarPaymentController extends Controller
{
    /**
     * Pay sale due amount via Moyasar
     */
    public function paySaleDue(Request $request, $sale_id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01'
        ]);

        try {
            $sale = Sale::where('business_id', auth()->user()->business_id)
                ->findOrFail($sale_id);

            if ($sale->isPaid) {
                return response()->json([
                    'message' => __('Sale is already paid')
                ], 400);
            }

            if ($request->amount > $sale->dueAmount) {
                return response()->json([
                    'message' => __('Amount cannot be more than due amount')
                ], 400);
            }

            $business = Business::findOrFail(auth()->user()->business_id);
            
            if (empty($business->moyasar_setting)) {
                return response()->json([
                    'message' => __('Moyasar settings not configured. Please configure in settings.')
                ], 400);
            }

            return Moyasar::paySaleDue($sale_id, $request->amount);

        } catch (\Exception $e) {
            return response()->json([
                'message' => __('Error processing payment: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Pay purchase due amount via Moyasar
     */
    public function payPurchaseDue(Request $request, $purchase_id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01'
        ]);

        try {
            $purchase = Purchase::where('business_id', auth()->user()->business_id)
                ->findOrFail($purchase_id);

            if ($purchase->isPaid) {
                return response()->json([
                    'message' => __('Purchase is already paid')
                ], 400);
            }

            if ($request->amount > $purchase->dueAmount) {
                return response()->json([
                    'message' => __('Amount cannot be more than due amount')
                ], 400);
            }

            $business = Business::findOrFail(auth()->user()->business_id);
            
            if (empty($business->moyasar_setting)) {
                return response()->json([
                    'message' => __('Moyasar settings not configured. Please configure in settings.')
                ], 400);
            }

            return Moyasar::payPurchaseDue($purchase_id, $request->amount);

        } catch (\Exception $e) {
            return response()->json([
                'message' => __('Error processing payment: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Pay due collection via Moyasar
     */
    public function payDueCollection(Request $request)
    {
        $request->validate([
            'party_id' => 'required|exists:parties,id',
            'amount' => 'required|numeric|min:0.01'
        ]);

        try {
            $business = Business::findOrFail(auth()->user()->business_id);
            
            if (empty($business->moyasar_setting)) {
                return response()->json([
                    'message' => __('Moyasar settings not configured. Please configure in settings.')
                ], 400);
            }

            $party = \App\Models\Party::where('business_id', auth()->user()->business_id)
                ->findOrFail($request->party_id);

            if ($request->amount > $party->due) {
                return response()->json([
                    'message' => __('Amount cannot be more than party due amount')
                ], 400);
            }

            // Create due collect record first
            $dueCollect = DueCollect::create([
                'business_id' => auth()->user()->business_id,
                'party_id' => $request->party_id,
                'amount' => $request->amount,
                'date' => now(),
                'payment_type' => 'Online (Moyasar)',
                'note' => 'Due collection via Moyasar - Pending'
            ]);

            $moyasar_setting = $business->moyasar_setting;

            return Moyasar::make_payment([
                'pay_amount' => $request->amount,
                'amount' => $request->amount,
                'currency' => 'SAR',
                'api_key' => $moyasar_setting['api_key'],
                'publishable_key' => $moyasar_setting['publishable_key'],
                'billName' => __('Due Collection') . ' - ' . $party->name,
                'payment_type' => 'due_payment',
                'due_collect_id' => $dueCollect->id,
                'business_id' => auth()->user()->business_id,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => __('Error processing payment: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process direct sale payment via Moyasar
     */
    public function processSalePayment(Request $request)
    {
        $request->validate([
            'sale_data' => 'required|array',
            'payment_amount' => 'required|numeric|min:0.01'
        ]);

        try {
            $business = Business::findOrFail(auth()->user()->business_id);
            
            if (empty($business->moyasar_setting)) {
                return response()->json([
                    'message' => __('Moyasar settings not configured. Please configure in settings.')
                ], 400);
            }

            // Store sale data in session for processing after payment
            session(['pending_sale_data' => $request->sale_data]);

            $moyasar_setting = $business->moyasar_setting;

            return Moyasar::make_payment([
                'pay_amount' => $request->payment_amount,
                'amount' => $request->payment_amount,
                'currency' => 'SAR',
                'api_key' => $moyasar_setting['api_key'],
                'publishable_key' => $moyasar_setting['publishable_key'],
                'billName' => __('Sale Payment') . ' - ' . ($request->sale_data['invoiceNumber'] ?? 'New Sale'),
                'payment_type' => 'direct_sale_payment',
                'business_id' => auth()->user()->business_id,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => __('Error processing payment: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process direct purchase payment via Moyasar
     */
    public function processPurchasePayment(Request $request)
    {
        $request->validate([
            'purchase_data' => 'required|array',
            'payment_amount' => 'required|numeric|min:0.01'
        ]);

        try {
            $business = Business::findOrFail(auth()->user()->business_id);
            
            if (empty($business->moyasar_setting)) {
                return response()->json([
                    'message' => __('Moyasar settings not configured. Please configure in settings.')
                ], 400);
            }

            // Store purchase data in session for processing after payment
            session(['pending_purchase_data' => $request->purchase_data]);

            $moyasar_setting = $business->moyasar_setting;

            return Moyasar::make_payment([
                'pay_amount' => $request->payment_amount,
                'amount' => $request->payment_amount,
                'currency' => 'SAR',
                'api_key' => $moyasar_setting['api_key'],
                'publishable_key' => $moyasar_setting['publishable_key'],
                'billName' => __('Purchase Payment') . ' - ' . ($request->purchase_data['invoiceNumber'] ?? 'New Purchase'),
                'payment_type' => 'direct_purchase_payment',
                'business_id' => auth()->user()->business_id,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => __('Error processing payment: ') . $e->getMessage()
            ], 500);
        }
    }
}