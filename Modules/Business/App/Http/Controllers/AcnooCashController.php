<?php

namespace Modules\Business\App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PaymentType;

class AcnooCashController extends Controller
{
    public function __construct()
    {
        $this->middleware('check.permission:banks.read')->only(['index']);
        $this->middleware('check.permission:banks.create')->only(['store']);
        $this->middleware('check.permission:banks.update')->only(['update', 'status']);
        $this->middleware('check.permission:banks.delete')->only(['destroy', 'deleteAll']);
    }

    public function index()
    {
        $cashes = PaymentType::withoutGlobalScope('excludeCashCheque')
            ->where('business_id', auth()->user()->business_id)
            ->where('name', 'Cash')
            ->latest()
            ->paginate(20);
            
        return view('business::cashes.index', compact('cashes'));
    }

    public function acnooFilter(Request $request)
    {
        $cashes = PaymentType::withoutGlobalScope('excludeCashCheque')
            ->where('business_id', auth()->user()->business_id)
            ->where('name', 'Cash')
            ->when(request('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->latest()
            ->paginate($request->per_page ?? 10);

        if ($request->ajax()) {
            return response()->json([
                'data' => view('business::cashes.datas', compact('cashes'))->render()
            ]);
        }
        return redirect(url()->previous());
    }

    public function store(Request $request)
    {
        $request->validate([
            'status' => 'required|boolean',
            'opening_balance' => 'nullable|numeric',
        ]);

        PaymentType::create($request->except('business_id') + [
            'business_id' => auth()->user()->business_id,
            'name' => 'Cash',
            'balance' => $request->opening_balance ?? 0,
        ]);

        return response()->json([
            'message' => __('Cash account created successfully'),
            'redirect' => route('business.cashes.index'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $cash = PaymentType::withoutGlobalScope('excludeCashCheque')->findOrFail($id);

        $request->validate([
            'status' => 'required|boolean',
            'opening_balance' => 'nullable|numeric',
        ]);

        $cash->update($request->except('business_id') + [
            'business_id' => auth()->user()->business_id,
            'name' => 'Cash',
        ]);

        return response()->json([
            'message' => __('Cash account updated successfully'),
            'redirect' => route('business.cashes.index'),
        ]);
    }

    public function destroy($id)
    {
        $cash = PaymentType::withoutGlobalScope('excludeCashCheque')->findOrFail($id);
        $cash->delete();

        return response()->json([
            'message' => __('Cash account deleted successfully'),
            'redirect' => route('business.cashes.index')
        ]);
    }

    public function status(Request $request, $id)
    {
        $cash = PaymentType::withoutGlobalScope('excludeCashCheque')->findOrFail($id);
        $cash->update(['status' => $request->status]);
        return response()->json(['message' => __('Cash Status Updated')]);
    }

    public function deleteAll(Request $request)
    {
        $idsToDelete = $request->input('ids');
        PaymentType::withoutGlobalScope('excludeCashCheque')->whereIn('id', $idsToDelete)->delete();
        return response()->json([
            'message' => __('Selected Cash accounts deleted successfully'),
            'redirect' => route('business.cashes.index')
        ]);
    }
}
