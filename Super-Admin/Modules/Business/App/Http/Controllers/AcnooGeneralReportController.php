<?php

namespace Modules\Business\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Traits\DateFilterTrait;

class AcnooGeneralReportController extends Controller
{
    use DateFilterTrait;

    public function lossProfit(Request $request)
    {
        $businessId = auth()->user()->business_id;
        $duration = $request->duration ?: 'today';
        $branchId = null; 
        
        if (moduleCheck('MultiBranchAddon')) {
            $user = auth()->user();
            $branchId = $user->branch_id ?? $user->active_branch_id;
        }

        // --- Sales Data ---
        $salesQuery = DB::table('sales')
            ->select(
                DB::raw('DATE(saleDate) as date'),
                DB::raw('SUM(actual_total_amount) as total_sales'),
                DB::raw('SUM(lossProfit) as total_sale_income')
            )
            ->where('business_id', $businessId)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->groupBy(DB::raw('DATE(saleDate)'));

        $this->applyDateFilter($salesQuery, $duration, 'saleDate', $request->from_date, $request->to_date);
        $dailySales = $salesQuery->get();

        $sale_datas = $dailySales->map(fn($sale) => (object)[
            'type'          => 'Sale',
            'date'          => $sale->date,
            'total_sales'   => $sale->total_sales,
            'total_incomes' => $sale->total_sale_income,
        ]);

        // --- Income Data ---
        $incomeQuery = DB::table('incomes')
            ->select(
                DB::raw('DATE(incomeDate) as date'),
                DB::raw('SUM(amount) as total_incomes')
            )
            ->where('business_id', $businessId)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->groupBy(DB::raw('DATE(incomeDate)'));

        $this->applyDateFilter($incomeQuery, $duration, 'incomeDate', $request->from_date, $request->to_date);
        $dailyIncomes = $incomeQuery->get();

        $income_datas = $dailyIncomes->map(fn($income) => (object)[
            'type'          => 'Income',
            'date'          => $income->date,
            'total_incomes' => $income->total_incomes,
        ]);

        // Merge Income & Sale
        $mergedIncomeSaleData = collect();
        $allDates = $dailySales->pluck('date')
            ->merge($dailyIncomes->pluck('date'))
            ->unique()
            ->sort();

        foreach ($allDates as $date) {
            if ($income = $income_datas->firstWhere('date', $date)) {
                $mergedIncomeSaleData->push($income);
            }
            if ($sale = $sale_datas->firstWhere('date', $date)) {
                $mergedIncomeSaleData->push($sale);
            }
        }

        // --- Expenses ---
        $dailyPayrolls = collect();
        if (moduleCheck('HrmAddon')) {
            $payrollQuery = DB::table('payrolls')
                ->select(DB::raw('DATE(date) as date'), DB::raw('SUM(amount) as total_payrolls'))
                ->where('business_id', $businessId)
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->groupBy(DB::raw('DATE(date)'));
            $this->applyDateFilter($payrollQuery, $duration, 'date', $request->from_date, $request->to_date);
            $dailyPayrolls = $payrollQuery->get();
        }

        $expenseQuery = DB::table('expenses')
            ->select(DB::raw('DATE(expenseDate) as date'), DB::raw('SUM(amount) as total_expenses_only'))
            ->where('business_id', $businessId)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->groupBy(DB::raw('DATE(expenseDate)'));
        $this->applyDateFilter($expenseQuery, $duration, 'expenseDate', $request->from_date, $request->to_date);
        $dailyExpenses = $expenseQuery->get();

        // Merge Expenses
        $mergedExpenseData = collect();
        $allExpenseDates = $dailyExpenses->pluck('date')
            ->merge($dailyPayrolls->pluck('date'))
            ->unique()
            ->sort();

        foreach ($allExpenseDates as $date) {
            if ($expense = $dailyExpenses->firstWhere('date', $date)) {
                $mergedExpenseData->push((object)['type' => 'Expense', 'date' => $date, 'total_expenses' => $expense->total_expenses_only]);
            }
            if ($payroll = $dailyPayrolls->firstWhere('date', $date)) {
                $mergedExpenseData->push((object)['type' => 'Payroll', 'date' => $date, 'total_expenses' => $payroll->total_payrolls]);
            }
        }

        // Totals
        $grossSaleProfit   = $sale_datas->sum('total_sales'); // Note: logic might differ based on requirement (sales amount vs profit)
        $grossIncomeProfit = $income_datas->sum('total_incomes') + $sale_datas->sum('total_incomes');
        $totalExpenses     = $mergedExpenseData->sum('total_expenses');
        $netProfit         = $grossIncomeProfit - $totalExpenses;

        return view('business::reports.loss-profit-history.index', compact(
            'mergedIncomeSaleData', 'mergedExpenseData', 
            'grossSaleProfit', 'grossIncomeProfit', 'totalExpenses', 'netProfit'
        ));
    }
}
