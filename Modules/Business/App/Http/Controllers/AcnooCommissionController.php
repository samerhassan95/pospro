<?php

namespace Modules\Business\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcnooCommissionController extends Controller
{
    public function __construct()
    {
        // No permission checks - accessible to all shop owners
    }

    public function index(Request $request)
    {
        $users = User::where('business_id', auth()->user()->business_id)
            ->where('id', '!=', auth()->id())
            ->get();

        if ($request->ajax()) {
            return view('business::commissions.datas', compact('users'));
        }

        return view('business::commissions.index', compact('users'));
    }

    public function create()
    {
        $users = User::where('business_id', auth()->user()->business_id)
            ->where('id', '!=', auth()->id())
            ->get();

        return view('business::commissions.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'commission_type' => 'required|in:percentage,fixed',
            'commission_value' => 'required|numeric|min:0',
        ]);

        $user = User::where('business_id', auth()->user()->business_id)
            ->findOrFail($request->user_id);

        $user->update([
            'commission_type' => $request->commission_type,
            'commission_value' => $request->commission_value,
        ]);

        return response()->json([
            'message' => __('Commission set successfully'),
            'redirect' => route('business.commissions.index'),
        ]);
    }

    public function edit($id)
    {
        $user = User::where('business_id', auth()->user()->business_id)
            ->findOrFail($id);

        return view('business::commissions.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'commission_type' => 'required|in:percentage,fixed',
            'commission_value' => 'required|numeric|min:0',
        ]);

        $user = User::where('business_id', auth()->user()->business_id)
            ->findOrFail($id);

        $user->update([
            'commission_type' => $request->commission_type,
            'commission_value' => $request->commission_value,
        ]);

        return response()->json([
            'message' => __('Commission updated successfully'),
            'redirect' => route('business.commissions.index'),
        ]);
    }

    public function destroy($id)
    {
        $user = User::where('business_id', auth()->user()->business_id)
            ->findOrFail($id);

        $user->update([
            'commission_type' => null,
            'commission_value' => null,
        ]);

        return response()->json([
            'message' => __('Commission removed successfully'),
            'redirect' => route('business.commissions.index')
        ]);
    }

    public function deleteAll(Request $request)
    {
        $ids = $request->ids;
        
        User::where('business_id', auth()->user()->business_id)
            ->whereIn('id', $ids)
            ->update([
                'commission_type' => null,
                'commission_value' => null,
            ]);

        return response()->json([
            'message' => __('Selected commissions removed successfully'),
            'redirect' => route('business.commissions.index')
        ]);
    }

    public function acnooFilter(Request $request)
    {
        $users = User::where('business_id', auth()->user()->business_id)
            ->where('id', '!=', auth()->id());

        if ($request->search) {
            $users->where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->commission_type) {
            $users->where('commission_type', $request->commission_type);
        }

        $users = $users->get();

        return view('business::commissions.datas', compact('users'));
    }
}
