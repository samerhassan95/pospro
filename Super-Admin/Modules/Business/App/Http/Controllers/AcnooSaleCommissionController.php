<?php

namespace Modules\Business\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcnooSaleCommissionController extends Controller
{
    public function __construct()
    {
        // No permission checks - accessible to all shop owners
    }

    public function index(Request $request)
    {
        $sales = Sale::where('business_id', auth()->user()->business_id)
            ->with('user')
            ->whereHas('user', function ($query) {
                $query->whereNotNull('commission_type');
            })
            ->latest()
            ->get();

        // Calculate commission for each sale
        $sales->map(function ($sale) {
            if ($sale->user && $sale->user->commission_type) {
                if ($sale->user->commission_type == 'percentage') {
                    $sale->commission = ($sale->totalAmount * $sale->user->commission_value) / 100;
                } else {
                    $sale->commission = $sale->user->commission_value;
                }
            } else {
                $sale->commission = 0;
            }
            return $sale;
        });

        if ($request->ajax()) {
            return view('business::sale-commissions.datas', compact('sales'));
        }

        return view('business::sale-commissions.index', compact('sales'));
    }

    public function acnooFilter(Request $request)
    {
        $sales = Sale::where('business_id', auth()->user()->business_id)
            ->with('user')
            ->whereHas('user', function ($query) {
                $query->whereNotNull('commission_type');
            });

        if ($request->search) {
            $sales->where(function ($query) use ($request) {
                $query->where('invoiceNumber', 'like', '%' . $request->search . '%')
                    ->orWhereHas('user', function ($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        if ($request->user_id) {
            $sales->where('user_id', $request->user_id);
        }

        if ($request->date_range) {
            $dates = explode(' - ', $request->date_range);
            if (count($dates) == 2) {
                $sales->whereBetween('created_at', [$dates[0], $dates[1]]);
            }
        }

        $sales = $sales->latest()->get();

        // Calculate commission for each sale
        $sales->map(function ($sale) {
            if ($sale->user && $sale->user->commission_type) {
                if ($sale->user->commission_type == 'percentage') {
                    $sale->commission = ($sale->totalAmount * $sale->user->commission_value) / 100;
                } else {
                    $sale->commission = $sale->user->commission_value;
                }
            } else {
                $sale->commission = 0;
            }
            return $sale;
        });

        return view('business::sale-commissions.datas', compact('sales'));
    }
}
