<?php

namespace Modules\Business\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Traits\DateFilterTrait;

class AcnooProductHistoryReportController extends Controller
{
    use DateFilterTrait;

    public function productSaleHistory(Request $request)
    {
        $businessId = auth()->user()->business_id;
        $duration   = $request->duration ?: 'today';

        $productQuery = Product::with(['saleDetails', 'purchaseDetails', 'saleDetails.sale', 'stocks', 'combo_products'])
            ->where('business_id', $businessId)
            ->whereHas('saleDetails.sale', function ($sale) use ($duration, $request) {
                $this->applyDateFilter($sale, $duration, 'saleDate', $request->from_date, $request->to_date);
            });

        $products = $productQuery->get();

        $total_purchase_qty = $products->sum(function ($product) {
            return $product->purchaseDetails->sum('quantities');
        });
        $total_sale_qty = $products->sum(function ($product) {
            return $product->saleDetails->sum('quantities');
        });

        return view('business::reports.product-history.sale', compact('products', 'total_sale_qty', 'total_purchase_qty'));
    }

    public function productPurchaseHistory(Request $request)
    {
        $businessId = auth()->user()->business_id;
        $duration   = $request->duration ?: 'today';

        $productQuery = Product::with(['saleDetails', 'purchaseDetails', 'purchaseDetails.purchase', 'stocks', 'combo_products'])
            ->where('business_id', $businessId)
            ->whereHas('purchaseDetails.purchase', function ($purchase) use ($duration, $request) {
                $this->applyDateFilter($purchase, $duration, 'purchaseDate', $request->from_date, $request->to_date);
            });

        $products = $productQuery->get();

        $total_purchase_qty = $products->sum(function ($product) {
            return $product->purchaseDetails->sum('quantities');
        });
        $total_sale_qty = $products->sum(function ($product) {
            return $product->saleDetails->sum('quantities');
        });

        return view('business::reports.product-history.purchase', compact('products', 'total_sale_qty', 'total_purchase_qty'));
    }
}
