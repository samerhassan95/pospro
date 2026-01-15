<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Business;
use App\Models\Gateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PublicInvoiceController extends Controller
{
    /**
     * Show the public invoice.
     */
    public function show($uuid)
    {
        $sale = Sale::where('uuid', $uuid)
            ->with(['business', 'party', 'details.product', 'payment_type'])
            ->firstOrFail();

        $business = $sale->business;
        $moyasar_setting = $business->moyasar_setting;

        return view('web.invoice', compact('sale', 'business', 'moyasar_setting'));
    }

    /**
     * Initiate payment for the sale.
     */
    public function pay(Request $request, $uuid)
    {
        $sale = Sale::where('uuid', $uuid)->firstOrFail();
        
        if ($sale->isPaid) {
            return redirect()->back()->with('success', __('This invoice is already paid.'));
        }

        $business = $sale->business;
        $moyasar_setting = $business->moyasar_setting;

        if (!$moyasar_setting || empty($moyasar_setting['api_key'])) {
            return redirect()->back()->with('error', __('Online payment is not configured for this business.'));
        }

        // Setup payment data for Moyasar library
        $payment_data = [
            'pay_amount' => $sale->dueAmount,
            'amount' => $sale->dueAmount,
            'charge' => 0,
            'currency' => 'SAR',
            'api_key' => $moyasar_setting['api_key'],
            'billName' => 'Payment for Invoice ' . $sale->invoiceNumber,
            'payment_type' => 'sale_payment',
            'sale_id' => $sale->id,
            'business_id' => $business->id,
            'gateway_id' => 0, // Not using a global gateway
        ];

        // Store callback info
        Session::put('fund_callback', [
            'success_url' => route('invoice.show', $uuid),
            'cancel_url' => route('invoice.show', $uuid),
        ]);

        return \App\Library\Moyasar::make_payment($payment_data);
    }
}
