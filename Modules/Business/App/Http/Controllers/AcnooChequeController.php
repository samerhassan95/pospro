<?php

namespace Modules\Business\App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PaymentType;

class AcnooChequeController extends Controller
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
        $cheques = PaymentType::withoutGlobalScope('excludeCashCheque')
            ->where('business_id', auth()->user()->business_id)
            ->where('name', 'Cheque')
            ->latest()
            ->paginate(20);
            
        return view('business::cheques.index', compact('cheques'));
    }

    public function acnooFilter(Request $request)
    {
        $cheques = PaymentType::withoutGlobalScope('excludeCashCheque')
            ->where('business_id', auth()->user()->business_id)
            ->where('name', 'Cheque')
            ->when(request('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->latest()
            ->paginate($request->per_page ?? 10);

        if ($request->ajax()) {
            return response()->json([
                'data' => view('business::cheques.datas', compact('cheques'))->render()
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
            'name' => 'Cheque',
            'balance' => $request->opening_balance ?? 0,
        ]);

        return response()->json([
            'message' => __('Cheque account created successfully'),
            'redirect' => route('business.cheques.index'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $cheque = PaymentType::withoutGlobalScope('excludeCashCheque')->findOrFail($id);

        $request->validate([
            'status' => 'required|boolean',
            'opening_balance' => 'nullable|numeric',
        ]);

        $cheque->update($request->except('business_id') + [
            'business_id' => auth()->user()->business_id,
            'name' => 'Cheque',
        ]);

        return response()->json([
            'message' => __('Cheque account updated successfully'),
            'redirect' => route('business.cheques.index'),
        ]);
    }

    public function destroy($id)
    {
        $cheque = PaymentType::withoutGlobalScope('excludeCashCheque')->findOrFail($id);
        $cheque->delete();

        return response()->json([
            'message' => __('Cheque account deleted successfully'),
            'redirect' => route('business.cheques.index')
        ]);
    }

    public function status(Request $request, $id)
    {
        $cheque = PaymentType::withoutGlobalScope('excludeCashCheque')->findOrFail($id);
        $cheque->update(['status' => $request->status]);
        return response()->json(['message' => __('Cheque Status Updated')]);
    }

    public function deleteAll(Request $request)
    {
        $idsToDelete = $request->input('ids');
        PaymentType::withoutGlobalScope('excludeCashCheque')->whereIn('id', $idsToDelete)->delete();
        return response()->json([
            'message' => __('Selected Cheque accounts deleted successfully'),
            'redirect' => route('business.cheques.index')
        ]);
    }
}
