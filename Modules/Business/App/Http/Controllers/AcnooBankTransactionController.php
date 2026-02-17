<?php

namespace Modules\Business\App\Http\Controllers;

use App\Helpers\HasUploader;
use App\Http\Controllers\Controller;
use App\Models\PaymentType;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AcnooBankTransactionController extends Controller
{
    use HasUploader;

    public function __construct()
    {
        $this->middleware('check.permission:banks.read')->only(['index']);
        $this->middleware('check.permission:banks.create')->only(['store']);
        $this->middleware('check.permission:banks.update')->only(['update']);
        $this->middleware('check.permission:banks.delete')->only(['destroy']);
    }

    public function index()
    {
        $business_id = auth()->user()->business_id;
        $transactions = Transaction::with('user:id,name', 'fromBank:id,name', 'toBank:id,name')
            ->where('business_id', $business_id)
            ->where('platform', 'bank')
            ->latest()
            ->paginate(20);

        $banks = PaymentType::where('business_id', $business_id)->latest()->get();
        // Include cash and cheque for transfers
        $all_accounts = PaymentType::withoutGlobalScope('excludeCashCheque')
            ->where('business_id', $business_id)
            ->latest()
            ->get();

        return view('business::bank-transactions.index', compact('transactions', 'banks', 'all_accounts'));
    }

    public function acnooFilter(Request $request)
    {
        $business_id = auth()->user()->business_id;
        $transactions = Transaction::with('user:id,name', 'fromBank:id,name', 'toBank:id,name')
            ->where('business_id', $business_id)
            ->where('platform', 'bank')
            ->when($request->search, function ($q) use ($request) {
                $q->where('note', 'like', '%' . $request->search . '%');
            })
            ->latest()
            ->paginate($request->per_page ?? 10);

        if ($request->ajax()) {
            return response()->json([
                'data' => view('business::bank-transactions.datas', compact('transactions'))->render()
            ]);
        }
        return redirect(url()->previous());
    }

    public function store(Request $request)
    {
        $request->validate([
            'from' => 'required|exists:payment_types,id',
            'transaction_type' => 'required|in:bank_to_bank,bank_to_cash,adjust_bank',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,svg',
            'note' => 'nullable|string',
        ]);

        $business_id = auth()->user()->business_id;
        $amount = $request->amount ?? 0;
        $type = 'transfer';

        DB::beginTransaction();
        try {
            $fromBank = PaymentType::withoutGlobalScope('excludeCashCheque')->find($request->from);

            if ($request->transaction_type == 'bank_to_bank' && $request->from == $request->to) {
                return response()->json(['message' => __('Cannot transfer between the same bank account.')], 400);
            }

            if ($request->transaction_type == 'bank_to_bank') {
                $toBank = PaymentType::withoutGlobalScope('excludeCashCheque')->find($request->to);
                if ($fromBank->balance < $amount) {
                    return response()->json(['message' => __('Insufficient balance in source account.')], 400);
                }
                $fromBank->decrement('balance', $amount);
                $toBank->increment('balance', $amount);
            } elseif ($request->transaction_type == 'bank_to_cash') {
                if ($fromBank->balance < $amount) {
                    return response()->json(['message' => __('Insufficient balance in selected account.')], 400);
                }
                $fromBank->decrement('balance', $amount);
            } elseif ($request->transaction_type == 'adjust_bank') {
                $type = $request->type; // credit or debit
                if ($type == 'debit' && $fromBank->balance < $amount) {
                    return response()->json(['message' => __('Cannot decrease below zero balance.')], 400);
                }
                if ($type == 'credit') {
                    $fromBank->increment('balance', $amount);
                } else {
                    $fromBank->decrement('balance', $amount);
                }
            }

            Transaction::create([
                'business_id' => $business_id,
                'user_id' => auth()->id(),
                'type' => $type,
                'platform' => 'bank',
                'transaction_type' => $request->transaction_type,
                'amount' => $amount,
                'from_bank' => $request->from,
                'to_bank' => ($request->transaction_type == 'bank_to_bank') ? $request->to : null,
                'date' => $request->date ?? now(),
                'image' => $request->image ? $this->upload($request, 'image') : NULL,
                'note' => $request->note,
            ]);

            DB::commit();
            return response()->json([
                'message' => __('Transaction saved successfully.'),
                'redirect' => route('business.bank-transactions.index')
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 406);
        }
    }

    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);
        if ($transaction->platform !== 'bank') {
            return response()->json(['message' => __('Cannot delete from here.')], 400);
        }

        DB::beginTransaction();
        try {
            $fromBank = PaymentType::withoutGlobalScope('excludeCashCheque')->find($transaction->from_bank);
            $toBank = PaymentType::withoutGlobalScope('excludeCashCheque')->find($transaction->to_bank);
            $amount = $transaction->amount;

            switch ($transaction->transaction_type) {
                case 'bank_to_bank':
                    if ($toBank && $fromBank) {
                        if ($toBank->balance < $amount) {
                            return response()->json(['message' => __('Insufficient balance to reverse.')], 400);
                        }
                        $fromBank->increment('balance', $amount);
                        $toBank->decrement('balance', $amount);
                    }
                    break;
                case 'bank_to_cash':
                    if ($fromBank) {
                        $fromBank->increment('balance', $amount);
                    }
                    break;
                case 'adjust_bank':
                    if ($fromBank) {
                        if ($transaction->type === 'credit') {
                            if ($fromBank->balance < $amount) {
                                return response()->json(['message' => __('Insufficient balance to reverse.')], 400);
                            }
                            $fromBank->decrement('balance', $amount);
                        } else {
                            $fromBank->increment('balance', $amount);
                        }
                    }
                    break;
            }

            if ($transaction->image && Storage::exists($transaction->image)) {
                Storage::delete($transaction->image);
            }
            $transaction->delete();

            DB::commit();
            return response()->json([
                'message' => __('Transaction deleted successfully.'),
                'redirect' => route('business.bank-transactions.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
