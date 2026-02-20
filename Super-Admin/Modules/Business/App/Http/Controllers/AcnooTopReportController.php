<?php

namespace Modules\Business\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Party;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Traits\DateFilterTrait;

class AcnooTopReportController extends Controller
{
    use DateFilterTrait;

    public function topCustomers(Request $request)
    {
        $businessId = auth()->user()->business_id;
        $duration = $request->duration ?: 'today';

        $customers = Party::where('business_id', $businessId)
            ->whereIn('type', ['Retailer', 'Dealer', 'Wholesaler'])
            ->withSum(['sales' => function ($query) use ($request, $duration) {
                $this->applyDateFilter($query, $duration, 'saleDate', $request->from_date, $request->to_date);
            }], 'totalAmount')
            ->orderByDesc('sales_sum_total_amount')
            ->take(5)
            ->get();

        return view('business::reports.top.customers', compact('customers'));
    }

    public function topSuppliers(Request $request)
    {
        $businessId = auth()->user()->business_id;
        $duration = $request->duration ?: 'today';

        $suppliers = Party::where('business_id', $businessId)
            ->where('type', 'Supplier')
            ->withSum(['purchases' => function ($query) use ($request, $duration) {
                $this->applyDateFilter($query, $duration, 'purchaseDate', $request->from_date, $request->to_date);
            }], 'totalAmount')
            ->orderByDesc('purchases_sum_total_amount')
            ->take(5)
            ->get();

        return view('business::reports.top.suppliers', compact('suppliers'));
    }

    public function topProducts(Request $request)
    {
        $businessId = auth()->user()->business_id;
        $duration = $request->duration ?: 'today';

        $products = Product::where('business_id', $businessId)
            ->withSum(['saleDetails' => function ($query) use ($request, $duration) {
                $query->whereHas('sale', function ($q) use ($request, $duration) {
                    $this->applyDateFilter($q, $duration, 'saleDate', $request->from_date, $request->to_date);
                });
            }], 'quantities')
            ->orderByDesc('sale_details_sum_quantities')
            ->take(5)
            ->get();

        return view('business::reports.top.products', compact('products'));
    }
}
