<?php

namespace Modules\Business\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PaymentType;
use App\Traits\DateFilterTrait;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Business\App\Exports\ExportBalanceSheet;

class AcnooBalanceSheetReportController extends Controller
{
    use DateFilterTrait;

    public function __construct()
    {
        $this->middleware('check.permission:reports.read')->only(['index']);
    }

    public function index()
    {
        $businessId = auth()->user()->business_id;

        $products = Product::select('id', 'business_id', 'productName', 'product_type', 'created_at')
            ->with(['stocks:id,business_id,product_id,productStock,productPurchasePrice', 'combo_products.stock'])
            ->where('business_id', $businessId)
            ->get();

        $banks = PaymentType::where('business_id', $businessId)->get();

        $total_stock_value = 0;

        foreach ($products as $product) {
            if (in_array($product->product_type, ['single', 'variant'])) {
                foreach ($product->stocks as $stock) {
                    $total_stock_value += $stock->productStock * $stock->productPurchasePrice;
                }
            }

            if ($product->product_type === 'combo') {
                foreach ($product->combo_products as $combo) {
                    $childStock = $combo->stock;
                    if ($childStock) {
                        $total_stock_value += ($childStock->productStock / $combo->quantity) * $combo->purchase_price;
                    }
                }
            }
        }

        $totalBankBalance = $banks->sum('balance');
        $total_asset = $total_stock_value + $totalBankBalance;

        return view('business::reports.balance-sheet.index', compact(
            'products',
            'banks',
            'total_stock_value',
            'totalBankBalance',
            'total_asset'
        ));
    }

    public function acnooFilter(Request $request)
    {
        $businessId = auth()->user()->business_id;
        $duration = $request->duration ?: 'today';

        $productQuery = Product::select('id', 'business_id', 'productName', 'product_type', 'created_at')
            ->with(['stocks:id,business_id,product_id,productStock,productPurchasePrice', 'combo_products.stock'])
            ->where('business_id', $businessId);

        $this->applyDateFilter($productQuery, $duration, 'created_at', $request->from_date, $request->to_date);
        $products = $productQuery->get();

        $bankQuery = PaymentType::where('business_id', $businessId);
        $this->applyDateFilter($bankQuery, $duration, 'opening_date', $request->from_date, $request->to_date);
        $banks = $bankQuery->get();

        $total_stock_value = 0;

        foreach ($products as $product) {
            if (in_array($product->product_type, ['single', 'variant'])) {
                foreach ($product->stocks as $stock) {
                    $total_stock_value += $stock->productStock * $stock->productPurchasePrice;
                }
            }

            if ($product->product_type === 'combo') {
                foreach ($product->combo_products as $combo) {
                    $childStock = $combo->stock;
                    if ($childStock) {
                        $total_stock_value += ($childStock->productStock / $combo->quantity) * $combo->purchase_price;
                    }
                }
            }
        }

        $totalBankBalance = $banks->sum('balance');
        $total_asset = $total_stock_value + $totalBankBalance;

        if ($request->ajax()) {
            return response()->json([
                'data' => view('business::reports.balance-sheet.datas', compact('products', 'banks'))->render(),
                'summary' => [
                    'total_stock_value' => $total_stock_value,
                    'totalBankBalance' => $totalBankBalance,
                    'total_asset' => $total_asset,
                ]
            ]);
        }

        return redirect(url()->previous());
    }

    public function generatePDF(Request $request)
    {
        $businessId = auth()->user()->business_id;

        $products = Product::with(['stocks', 'combo_products.stock'])
            ->where('business_id', $businessId)
            ->get();

        $banks = PaymentType::where('business_id', $businessId)->get();

        $pdf = Pdf::loadView('business::reports.balance-sheet.pdf', compact('products', 'banks'));
        return $pdf->download('balance-sheet-report.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new ExportBalanceSheet($request), 'balance-sheet-report.xlsx');
    }

    public function exportCsv(Request $request)
    {
        return Excel::download(new ExportBalanceSheet($request), 'balance-sheet-report.csv');
    }
}
