<?php

namespace Modules\Business\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Party;
use App\Models\Sale;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcnooPartyReportController extends Controller
{
    public function __construct()
    {
        // Permissions can be added later if needed
    }

    // Customer Ledger
    public function customerLedger(Request $request)
    {
        $parties = Party::where('business_id', auth()->user()->business_id)
            ->where('type', 'Customer')
            ->with('sales')
            ->orderBy('name')
            ->get();

        return view('business::party-reports.customer-ledger', compact('parties'));
    }

    public function customerLedgerShow($id)
    {
        $party = Party::where('business_id', auth()->user()->business_id)
            ->where('type', 'Customer')
            ->findOrFail($id);

        $sales = Sale::where('business_id', auth()->user()->business_id)
            ->where('party_id', $id)
            ->with('saleDetails')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('business::party-reports.customer-ledger-show', compact('party', 'sales'));
    }

    // Supplier Ledger
    public function supplierLedger(Request $request)
    {
        $parties = Party::where('business_id', auth()->user()->business_id)
            ->where('type', 'Supplier')
            ->with('purchases')
            ->orderBy('name')
            ->get();

        return view('business::party-reports.supplier-ledger', compact('parties'));
    }

    public function supplierLedgerShow($id)
    {
        $party = Party::where('business_id', auth()->user()->business_id)
            ->where('type', 'Supplier')
            ->findOrFail($id);

        $purchases = Purchase::where('business_id', auth()->user()->business_id)
            ->where('party_id', $id)
            ->with('purchaseDetails')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('business::party-reports.supplier-ledger-show', compact('party', 'purchases'));
    }

    // Party Loss Profit
    public function partyLossProfit(Request $request)
    {
        $parties = Party::where('business_id', auth()->user()->business_id)
            ->whereIn('type', ['Customer', 'Supplier'])
            ->withCount(['sales', 'purchases'])
            ->orderBy('name')
            ->get();

        // Calculate profit/loss for each party
        $parties->map(function ($party) {
            $totalSales = Sale::where('party_id', $party->id)->sum('totalAmount');
            $totalPurchases = Purchase::where('party_id', $party->id)->sum('totalAmount');
            
            $party->total_sales = $totalSales;
            $party->total_purchases = $totalPurchases;
            $party->profit_loss = $totalSales - $totalPurchases;
            
            return $party;
        });

        return view('business::party-reports.party-loss-profit', compact('parties'));
    }

    // Top 5 Customers
    public function topCustomers(Request $request)
    {
        $topCustomers = Party::where('business_id', auth()->user()->business_id)
            ->where('type', 'Customer')
            ->withSum('sales', 'totalAmount')
            ->withCount('sales')
            ->orderBy('sales_sum_total_amount', 'desc')
            ->limit(5)
            ->get();

        return view('business::party-reports.top-customers', compact('topCustomers'));
    }

    // Top 5 Suppliers
    public function topSuppliers(Request $request)
    {
        $topSuppliers = Party::where('business_id', auth()->user()->business_id)
            ->where('type', 'Supplier')
            ->withSum('purchases', 'totalAmount')
            ->withCount('purchases')
            ->orderBy('purchases_sum_total_amount', 'desc')
            ->limit(5)
            ->get();

        return view('business::party-reports.top-suppliers', compact('topSuppliers'));
    }
}
