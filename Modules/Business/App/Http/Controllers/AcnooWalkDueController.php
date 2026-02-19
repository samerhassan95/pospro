<?php

namespace Modules\Business\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\DueCollect;
use Illuminate\Http\Request;

class AcnooWalkDueController extends Controller
{
    public function __construct()
    {
        // No permission checks - accessible to all shop owners
    }

    public function index(Request $request)
    {
        $sales = Sale::where('business_id', auth()->user()->business_id)
            ->whereNull('party_id') // Walk-in customers (no party)
            ->where('dueAmount', '>', 0)
            ->with('user')
            ->latest()
            ->get();

        if ($request->ajax()) {
            return view('business::walk-dues.datas', compact('sales'));
        }

        return view('business::walk-dues.index', compact('sales'));
    }

    public function acnooFilter(Request $request)
    {
        $sales = Sale::where('business_id', auth()->user()->business_id)
            ->whereNull('party_id');

        if ($request->search) {
            $sales->where(function ($query) use ($request) {
                $query->where('invoice_no', 'like', '%' . $request->search . '%')
                    ->orWhere('customer_name', 'like', '%' . $request->search . '%')
                    ->orWhere('customer_phone', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->start_date) {
            $sales->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $sales->whereDate('created_at', '<=', $request->end_date);
        }

        $sales = $sales->where('dueAmount', '>', 0)
            ->with('user')
            ->latest()
            ->get();

        return view('business::walk-dues.datas', compact('sales'));
    }

    public function collectDue($id)
    {
        $sale = Sale::where('business_id', auth()->user()->business_id)
            ->whereNull('party_id')
            ->findOrFail($id);

        return view('business::walk-dues.collect', compact('sale'));
    }

    public function collectDueStore(Request $request)
    {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'amount' => 'required|numeric|min:0',
            'payment_type' => 'required|string',
        ]);

        $sale = Sale::where('business_id', auth()->user()->business_id)
            ->findOrFail($request->sale_id);

        if ($request->amount > $sale->dueAmount) {
            return response()->json([
                'message' => __('Amount cannot be greater than due amount'),
            ], 422);
        }

        DueCollect::create([
            'business_id' => auth()->user()->business_id,
            'sale_id' => $sale->id,
            'party_id' => null,
            'amount' => $request->amount,
            'payment_type' => $request->payment_type,
            'note' => $request->note,
            'user_id' => auth()->id(),
        ]);

        $sale->update([
            'dueAmount' => $sale->dueAmount - $request->amount,
            'paidAmount' => $sale->paidAmount + $request->amount,
        ]);

        return response()->json([
            'message' => __('Due collected successfully'),
            'redirect' => route('business.walk-dues.index'),
        ]);
    }
}
