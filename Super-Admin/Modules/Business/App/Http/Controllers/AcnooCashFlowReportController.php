<?php

namespace Modules\Business\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Traits\DateFilterTrait;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Business\App\Exports\ExportCashFlow;

class AcnooCashFlowReportController extends Controller
{
    use DateFilterTrait;

    public function __construct()
    {
        $this->middleware('check.permission:reports.read')->only(['index']);
    }

    public function index()
    {
        $business_id = auth()->user()->business_id;
        
        $query = Transaction::with([
            'paymentType:id,name',
            'sale:id,party_id',
            'sale.party:id,name',
            'saleReturn:id,sale_id',
            'purchase:id,party_id',
            'purchase.party:id,name',
            'purchaseReturn:id,purchase_id',
            'dueCollect:id,party_id',
            'dueCollect.party:id,name',
        ])
            ->where('business_id', $business_id)
            ->whereIn('type', ['debit', 'credit']);

        $total_cash_in = (clone $query)->where('type', 'credit')->sum('amount');
        $total_cash_out = (clone $query)->where('type', 'debit')->sum('amount');
        $total_running_cash = $total_cash_in - $total_cash_out;

        $this->applyDateFilter($query, 'today', 'date');
        $cash_flows = $query->latest('date')->paginate(20);

        $firstDate = $cash_flows->first()?->date;
        $opening_balance = 0;

        if ($firstDate) {
            $opening_balance = Transaction::where('business_id', $business_id)
                ->whereDate('date', '<', $firstDate)
                ->selectRaw("SUM(CASE WHEN type='credit' THEN amount ELSE 0 END) - SUM(CASE WHEN type='debit' THEN amount ELSE 0 END) as balance")
                ->value('balance') ?? 0;
        }

        return view('business::reports.cash-flow.index', compact(
            'cash_flows',
            'total_cash_in',
            'total_cash_out',
            'total_running_cash',
            'opening_balance'
        ));
    }

    public function acnooFilter(Request $request)
    {
        $business_id = auth()->user()->business_id;
        
        $query = Transaction::with([
            'paymentType:id,name',
            'sale:id,party_id',
            'sale.party:id,name',
            'saleReturn:id,sale_id',
            'purchase:id,party_id',
            'purchase.party:id,name',
            'purchaseReturn:id,purchase_id',
            'dueCollect:id,party_id',
            'dueCollect.party:id,name',
        ])
            ->where('business_id', $business_id)
            ->whereIn('type', ['debit', 'credit']);

        $total_cash_in = (clone $query)->where('type', 'credit')->sum('amount');
        $total_cash_out = (clone $query)->where('type', 'debit')->sum('amount');
        $total_running_cash = $total_cash_in - $total_cash_out;

        $duration = $request->duration ?: 'today';
        $this->applyDateFilter($query, $duration, 'date', $request->from_date, $request->to_date);
        
        $cash_flows = $query->latest('date')->paginate($request->per_page ?? 20);

        $firstDate = $cash_flows->first()?->date;
        $opening_balance = 0;

        if ($firstDate) {
            $opening_balance = Transaction::where('business_id', $business_id)
                ->whereDate('date', '<', $firstDate)
                ->selectRaw("SUM(CASE WHEN type='credit' THEN amount ELSE 0 END) - SUM(CASE WHEN type='debit' THEN amount ELSE 0 END) as balance")
                ->value('balance') ?? 0;
        }

        if ($request->ajax()) {
            return response()->json([
                'data' => view('business::reports.cash-flow.datas', compact('cash_flows'))->render(),
                'summary' => [
                    'total_cash_in' => $total_cash_in,
                    'total_cash_out' => $total_cash_out,
                    'total_running_cash' => $total_running_cash,
                    'opening_balance' => $opening_balance,
                ]
            ]);
        }

        return redirect(url()->previous());
    }

    public function generatePDF(Request $request)
    {
        $business_id = auth()->user()->business_id;
        
        $query = Transaction::with([
            'paymentType:id,name',
            'sale.party:id,name',
            'purchase.party:id,name',
            'dueCollect.party:id,name',
        ])
            ->where('business_id', $business_id)
            ->whereIn('type', ['debit', 'credit']);

        $duration = $request->duration ?: 'today';
        $this->applyDateFilter($query, $duration, 'date', $request->from_date, $request->to_date);
        
        $cash_flows = $query->latest('date')->get();

        $pdf = Pdf::loadView('business::reports.cash-flow.pdf', compact('cash_flows'));
        return $pdf->download('cash-flow-report.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new ExportCashFlow($request), 'cash-flow-report.xlsx');
    }

    public function exportCsv(Request $request)
    {
        return Excel::download(new ExportCashFlow($request), 'cash-flow-report.csv');
    }
}
