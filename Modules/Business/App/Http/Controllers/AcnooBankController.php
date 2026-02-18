<?php

namespace Modules\Business\App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PaymentType;
use Illuminate\Validation\Rule;

class AcnooBankController extends Controller
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
        $banks = PaymentType::where('business_id', auth()->user()->business_id)->latest()->paginate(20);
        return view('business::banks.index', compact('banks'));
    }

    public function acnooFilter(Request $request)
    {
        $banks = PaymentType::where('business_id', auth()->user()->business_id)
            ->when(request('search'), function ($q) use ($request) {
                $q->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                });
            })
            ->latest()
            ->paginate($request->per_page ?? 10);

        if ($request->ajax()) {
            return response()->json([
                'data' => view('business::banks.datas', compact('banks'))->render()
            ]);
        }
        return redirect(url()->previous());
    }

    public function store(Request $request)
    {
        $request->validate([
            'status' => 'required|boolean',
            'opening_balance' => 'nullable|numeric',
            'opening_date' => 'nullable|date',
            'name' => [
                'required',
                Rule::unique('payment_types')->where(function ($query) {
                    return $query->where('business_id', auth()->user()->business_id);
                }),
            ],
        ]);

        PaymentType::create($request->except('business_id') + [
            'business_id' => auth()->user()->business_id,
            'balance' => $request->opening_balance ?? 0,
        ]);

        return response()->json([
            'message' => __('Bank account created successfully'),
            'redirect' => route('business.banks.index'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $bank = PaymentType::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:payment_types,name,' . $bank->id . ',id,business_id,' . auth()->user()->business_id,
            'status' => 'required|boolean',
            'opening_balance' => 'nullable|numeric',
            'opening_date' => 'nullable|date',
        ]);

        $bank->update($request->except('business_id') + [
            'business_id' => auth()->user()->business_id,
        ]);

        return response()->json([
            'message' => __('Bank account updated successfully'),
            'redirect' => route('business.banks.index'),
        ]);
    }

    public function destroy($id)
    {
        $bank = PaymentType::findOrFail($id);
        $bank->delete();

        return response()->json([
            'message' => __('Bank account deleted successfully'),
            'redirect' => route('business.banks.index')
        ]);
    }

    public function status(Request $request, $id)
    {
        $bank = PaymentType::findOrFail($id);
        $bank->update(['status' => $request->status]);
        return response()->json(['message' => __('Bank Account Status Updated')]);
    }

    public function deleteAll(Request $request)
    {
        $idsToDelete = $request->input('ids');
        PaymentType::whereIn('id', $idsToDelete)->delete();
        return response()->json([
            'message' => __('Selected Bank accounts deleted successfully'),
            'redirect' => route('business.banks.index')
        ]);
    }
}

