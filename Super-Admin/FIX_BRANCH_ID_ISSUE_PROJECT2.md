# حل مشكلة branch_id في المشروع الثاني

## المشكلة
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'branch_id' in 'where clause'
```

## السبب
المشروع الثاني مش فيه Multi-Branch Addon، فالـ `branch_id` column مش موجود في الجداول.

---

## الحل: تعديل الـ Controller

### الملف المطلوب تعديله:
```
المشروع_الثاني/Modules/Business/App/Http/Controllers/AcnooGeneralReportController.php
```

---

## التعديل المطلوب

### ابحث عن السطر ده (حوالي سطر 16-22):

```php
$branchId = null; 

if (moduleCheck('MultiBranchAddon')) {
    $user = auth()->user();
    $branchId = $user->branch_id ?? $user->active_branch_id;
}
```

### استبدله بـ:

```php
$branchId = null;
$hasBranchColumn = false;

if (moduleCheck('MultiBranchAddon')) {
    $user = auth()->user();
    $branchId = $user->branch_id ?? $user->active_branch_id;
    
    // Check if branch_id column exists in sales table
    $hasBranchColumn = \Schema::hasColumn('sales', 'branch_id');
}
```

---

### ثم ابحث عن كل الأسطر اللي فيها:

```php
->when($branchId, fn($q) => $q->where('branch_id', $branchId))
```

### استبدلها بـ:

```php
->when($branchId && $hasBranchColumn, fn($q) => $q->where('branch_id', $branchId))
```

---

## الكود الكامل بعد التعديل

```php
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
        $hasBranchColumn = false;
        
        if (moduleCheck('MultiBranchAddon')) {
            $user = auth()->user();
            $branchId = $user->branch_id ?? $user->active_branch_id;
            
            // Check if branch_id column exists
            $hasBranchColumn = \Schema::hasColumn('sales', 'branch_id');
        }

        // --- Sales Data ---
        $salesQuery = DB::table('sales')
            ->select(
                DB::raw('DATE(saleDate) as date'),
                DB::raw('SUM(actual_total_amount) as total_sales'),
                DB::raw('SUM(lossProfit) as total_sale_income')
            )
            ->where('business_id', $businessId)
            ->when($branchId && $hasBranchColumn, fn($q) => $q->where('branch_id', $branchId))
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
            ->when($branchId && $hasBranchColumn, fn($q) => $q->where('branch_id', $branchId))
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
                ->when($branchId && $hasBranchColumn, fn($q) => $q->where('branch_id', $branchId))
                ->groupBy(DB::raw('DATE(date)'));
            $this->applyDateFilter($payrollQuery, $duration, 'date', $request->from_date, $request->to_date);
            $dailyPayrolls = $payrollQuery->get();
        }

        $expenseQuery = DB::table('expenses')
            ->select(DB::raw('DATE(expenseDate) as date'), DB::raw('SUM(amount) as total_expenses_only'))
            ->where('business_id', $businessId)
            ->when($branchId && $hasBranchColumn, fn($q) => $q->where('branch_id', $branchId))
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
        $grossSaleProfit   = $sale_datas->sum('total_sales');
        $grossIncomeProfit = $income_datas->sum('total_incomes') + $sale_datas->sum('total_incomes');
        $totalExpenses     = $mergedExpenseData->sum('total_expenses');
        $netProfit         = $grossIncomeProfit - $totalExpenses;

        return view('business::reports.loss-profit-history.index', compact(
            'mergedIncomeSaleData', 'mergedExpenseData', 
            'grossSaleProfit', 'grossIncomeProfit', 'totalExpenses', 'netProfit'
        ));
    }
}
```

---

## ملاحظة مهمة

نفس المشكلة ممكن تحصل في Controllers تانية. لو لقيت نفس الخطأ في أي Controller تاني، طبق نفس الحل:

1. أضف `$hasBranchColumn = \Schema::hasColumn('table_name', 'branch_id');`
2. غير `->when($branchId, ...)` إلى `->when($branchId && $hasBranchColumn, ...)`

---

## Controllers اللي ممكن تحتاج نفس التعديل

إذا ظهرت نفس المشكلة في Controllers تانية، عدلها بنفس الطريقة:
- AcnooProductHistoryReportController
- AcnooAdvancedReportController
- أي Controller بيستخدم `branch_id`

---

**تاريخ الإنشاء:** 16 فبراير 2026
