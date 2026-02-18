<?php

namespace Modules\Business\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ComboProduct;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\SaleDetails;
use App\Models\PurchaseDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcnooAdvancedReportController extends Controller
{
    public function __construct()
    {
        // No permission checks - accessible to all shop owners
    }

    // Product Wise Profit & Loss
    public function productLossProfit(Request $request)
    {
        $products = Product::where('business_id', auth()->user()->business_id)
            ->with(['saleDetails', 'purchaseDetails'])
            ->get();

        $products->map(function ($product) {
            // Calculate total sales (price * quantities)
            $totalSales = $product->saleDetails->sum(function ($detail) {
                return $detail->price * $detail->quantities;
            });
            
            // Calculate total purchases (productPurchasePrice * quantities)
            $totalPurchases = $product->purchaseDetails->sum(function ($detail) {
                return $detail->productPurchasePrice * $detail->quantities;
            });
            
            $product->total_sales = $totalSales;
            $product->total_purchases = $totalPurchases;
            $product->profit_loss = $totalSales - $totalPurchases;
            
            return $product;
        });

        // Calculate summary data for the view
        $opening_stock_by_purchase = 0;
        $opening_stock_by_sale = 0;
        $closing_stock_by_purchase = 0;
        $closing_stock_by_sale = 0;
        $total_purchase_price = Purchase::where('business_id', auth()->user()->business_id)->sum('totalAmount');
        $total_sale_price = Sale::where('business_id', auth()->user()->business_id)->sum('totalAmount');
        $total_purchase_shipping_charge = 0;
        $total_sale_shipping_charge = 0;
        $total_sale_discount = Sale::where('business_id', auth()->user()->business_id)->sum('discountAmount');
        $total_purchase_discount = Purchase::where('business_id', auth()->user()->business_id)->sum('discountAmount');
        $all_sale_return = 0;
        $all_purchase_return = 0;
        $total_sale_rounding_off = 0;

        return view('business::reports.loss-profits-details.index', compact(
            'products',
            'opening_stock_by_purchase',
            'opening_stock_by_sale',
            'closing_stock_by_purchase',
            'closing_stock_by_sale',
            'total_purchase_price',
            'total_sale_price',
            'total_purchase_shipping_charge',
            'total_sale_shipping_charge',
            'total_sale_discount',
            'total_purchase_discount',
            'all_sale_return',
            'all_purchase_return',
            'total_sale_rounding_off'
        ));
    }

    public function productLossProfitFilter(Request $request)
    {
        $products = Product::where('business_id', auth()->user()->business_id);

        if ($request->search) {
            $products->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->category_id) {
            $products->where('category_id', $request->category_id);
        }

        $products = $products->with(['saleDetails', 'purchaseDetails'])->get();

        $products->map(function ($product) {
            // Calculate total sales (price * quantities)
            $totalSales = $product->saleDetails->sum(function ($detail) {
                return $detail->price * $detail->quantities;
            });
            
            // Calculate total purchases (productPurchasePrice * quantities)
            $totalPurchases = $product->purchaseDetails->sum(function ($detail) {
                return $detail->productPurchasePrice * $detail->quantities;
            });
            
            $product->total_sales = $totalSales;
            $product->total_purchases = $totalPurchases;
            $product->profit_loss = $totalSales - $totalPurchases;
            
            return $product;
        });

        return view('business::reports.loss-profits-details.datas', compact('products'));
    }

    // Top 5 Products
    public function topProducts(Request $request)
    {
        $products = Product::where('business_id', auth()->user()->business_id)
            ->with('saleDetails')
            ->withSum('saleDetails', 'quantities')
            ->orderBy('sale_details_sum_quantities', 'desc')
            ->limit(5)
            ->get();

        return view('business::reports.top.products', compact('products'));
    }

    // Combo Product Reports
    public function comboProducts(Request $request)
    {
        $combos = ComboProduct::whereHas('product', function ($query) {
                $query->where('business_id', auth()->user()->business_id);
            })
            ->with(['product', 'stock'])
            ->get();

        return view('business::combo-products.index', compact('combos'));
    }

    // Discount Product Reports
    public function discountProducts(Request $request)
    {
        $sales = Sale::where('business_id', auth()->user()->business_id)
            ->where('discountAmount', '>', 0)
            ->with(['saleDetails.product', 'party'])
            ->get();

        $totalDiscount = $sales->sum('discountAmount');

        return view('business::reports.discount-products.index', compact('sales', 'totalDiscount'));
    }

    // Product Wise Purchase
    public function productPurchase(Request $request)
    {
        $products = Product::where('business_id', auth()->user()->business_id)
            ->with('purchaseDetails')
            ->get()
            ->map(function ($product) {
                $product->total_purchases = $product->purchaseDetails->sum(function ($detail) {
                    return $detail->productPurchasePrice * $detail->quantities;
                });
                $product->total_quantity = $product->purchaseDetails->sum('quantities');
                return $product;
            })
            ->sortByDesc('total_purchases');

        return view('business::reports.product-purchase.index', compact('products'));
    }

    // Product Wise Sale
    public function productSale(Request $request)
    {
        $products = Product::where('business_id', auth()->user()->business_id)
            ->with('saleDetails')
            ->get()
            ->map(function ($product) {
                $product->total_sales = $product->saleDetails->sum(function ($detail) {
                    return $detail->price * $detail->quantities;
                });
                $product->total_quantity = $product->saleDetails->sum('quantities');
                return $product;
            })
            ->sortByDesc('total_sales');

        return view('business::reports.product-sale.index', compact('products'));
    }
}
