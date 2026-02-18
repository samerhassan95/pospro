<?php

namespace App\Http\Controllers;

use App\Library\Moyasar;
use App\Models\Sale;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicInvoiceController extends Controller
{
    /**
     * Show public invoice
     */
    public function show($uuid)
    {
        $sale = Sale::with([
            'business:id,companyName,phoneNumber,email,address,moyasar_setting',
            'party:id,name,phone,email,address',
            'details:id,sale_id,product_id,quantities,price',
            'details.product:id,productName',
            'payment_type:id,name',
            'vat:id,name,rate'
        ])->where('uuid', $uuid)->firstOrFail();

        // Check if invoice is already paid
        if ($sale->isPaid) {
            return view('payments.invoice-paid', compact('sale'));
        }

        // Check if business has Moyasar configured
        $moyasarEnabled = !empty($sale->business->moyasar_setting) && 
                         !empty($sale->business->moyasar_setting['api_key']);

        return view('payments.public-invoice', compact('sale', 'moyasarEnabled'));
    }

    /**
     * Process payment for public invoice
     */
    public function pay(Request $request, $uuid)
    {
        $request->validate([
            'payment_method' => 'required|in:moyasar',
            'amount' => 'required|numeric|min:0.01'
        ]);

        $sale = Sale::where('uuid', $uuid)->firstOrFail();

        if ($sale->isPaid) {
            return response()->json([
                'message' => __('Invoice is already paid')
            ], 400);
        }

        if ($request->amount > $sale->dueAmount) {
            return response()->json([
                'message' => __('Amount cannot be more than due amount')
            ], 400);
        }

        $business = $sale->business;

        if (empty($business->moyasar_setting)) {
            return response()->json([
                'message' => __('Payment method not available')
            ], 400);
        }

        try {
            if ($request->payment_method === 'moyasar') {
                return $this->processMoyasarPayment($sale, $request->amount);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => __('Payment processing failed: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process Moyasar payment for public invoice
     */
    private function processMoyasarPayment($sale, $amount)
    {
        $business = $sale->business;
        $moyasar_setting = $business->moyasar_setting;

        // Set callback URLs for public invoice
        session(['fund_callback' => [
            'success_url' => route('invoice.show', $sale->uuid) . '?payment=success',
            'cancel_url' => route('invoice.show', $sale->uuid) . '?payment=failed'
        ]]);

        return Moyasar::make_payment([
            'pay_amount' => $amount,
            'amount' => $amount,
            'currency' => 'SAR',
            'api_key' => $moyasar_setting['api_key'],
            'publishable_key' => $moyasar_setting['publishable_key'],
            'billName' => __('Invoice Payment') . ' - ' . $sale->invoiceNumber,
            'payment_type' => 'sale_payment',
            'sale_id' => $sale->id,
            'business_id' => $sale->business_id,
        ]);
    }
}