<?php

namespace Modules\Business\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Traits\DateFilterTrait;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Business\App\Exports\ExportBillWiseProfit;

class AcnooBillWiseProfitReportController extends Controller
{
    use DateFilterTrait;

    public function __construct()
    {
        $this->middleware('check.permission:reports.read')->only(['index']);
    }

    public function index()
    {
        $businessId = auth()->user()->business_id;

        $bills = Sale::select('id', 'business_id', 'party_id', 'invoiceNumber', 'saleDate', 'totalAmount', 'lossProfit')
            ->with('party:id,name', 'details:id,sale_id,product_id,price,quantities,productPurchasePrice,lossProfit', 'details.product:id,productName')
            ->where('business_id', $businessId)
            ->latest('saleDate')
            ->paginate(20);

        $total_amount = Sale::where('business_id', $businessId)->sum('totalAmount');
        $total_bill_profit = Sale::where('business_id', $businessId)->where('lossProfit', '>=', 0)->sum('lossProfit');
        $total_bill_loss = Sale::where('business_id', $businessId)->where('lossProfit', '<', 0)->sum('lossProfit');

        return view('business::reports.bill-wise-profit.index', compact(
            'bills',
            'total_amount',
            'total_bill_profit',
            'total_bill_loss'
        ));
    }

    public function acnooFilter(Request $request)
    {
        $businessId = auth()->user()->business_id;
        $duration = $request->duration ?: 'today';

        $billQuery = Sale::select('id', 'business_id', 'party_id', 'invoiceNumber', 'saleDate', 'totalAmount', 'lossProfit')
            ->with('party:id,name', 'details:id,sale_id,product_id,price,quantities,productPurchasePrice,lossProfit', 'details.product:id,productName')
            ->where('business_id', $businessId);

        $this->applyDateFilter($billQuery, $duration, 'saleDate', $request->from_date, $request->to_date);
        
        $bills = $billQuery->latest('saleDate')->paginate($request->per_page ?? 20);

        $total_amount = (clone $billQuery)->sum('totalAmount');
        $total_bill_profit = (clone $billQuery)->where('lossProfit', '>=', 0)->sum('lossProfit');
        $total_bill_loss = (clone $billQuery)->where('lossProfit', '<', 0)->sum('lossProfit');

        if ($request->ajax()) {
            return response()->json([
                'data' => view('business::reports.bill-wise-profit.datas', compact('bills'))->render(),
                'summary' => [
                    'total_amount' => $total_amount,
                    'total_bill_profit' => $total_bill_profit,
                    'total_bill_loss' => $total_bill_loss,
                ]
            ]);
        }

        return redirect(url()->previous());
    }

    public function show($id)
    {
        $businessId = auth()->user()->business_id;
        
        $bill = Sale::with('party:id,name', 'details:id,sale_id,product_id,price,quantities,productPurchasePrice,lossProfit', 'details.product:id,productName')
            ->where('business_id', $businessId)
            ->findOrFail($id);

        return view('business::reports.bill-wise-profit.show', compact('bill'));
    }

    public function generatePDF(Request $request)
    {
        $businessId = auth()->user()->business_id;

        $bills = Sale::with('party:id,name', 'details.product:id,productName')
            ->where('business_id', $businessId)
            ->latest('saleDate')
            ->get();

        $pdf = Pdf::loadView('business::reports.bill-wise-profit.pdf', compact('bills'));
        return $pdf->download('bill-wise-profit-report.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new ExportBillWiseProfit($request), 'bill-wise-profit-report.xlsx');
    }

    public function exportCsv(Request $request)
    {
        return Excel::download(new ExportBillWiseProfit($request), 'bill-wise-profit-report.csv');
    }
}
