<?php

namespace Modules\CustomDomainAddon\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\CustomDomainAddon\App\Models\Domain;

class AcnooDomainController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:domains-read')->only(['index']);
        $this->middleware('permission:domains-create')->only(['store']);
        $this->middleware('permission:domains-update')->only(['update', 'status']);
        $this->middleware('permission:domains-delete')->only(['destroy', 'deleteAll']);
    }

    public function index()
    {
        $domains = Domain::latest()->paginate(20);
        return view('customdomainaddon::admin.index', compact('domains'));
    }

    public function acnooFilter(Request $request)
    {
        $domains = Domain::when(request('search'), function ($q) {
            $q->where('domain', 'like', '%' . request('search') . '%');
        })
            ->latest()
            ->paginate($request->per_page ?? 20);

        if ($request->ajax()) {
            return response()->json([
                'data' => view('customdomainaddon::admin.datas', compact('domains'))->render()
            ]);
        }

        return redirect(url()->previous());
    }

    public function edit(string $id)
    {
        $domain = Domain::findOrFail($id);
        return view('customdomainaddon::admin.edit', compact('domain'));
    }

    public function update(Request $request, string $id)
    {
        $domain = Domain::findOrFail($id);

        $request->validate([
            'domain' => [
                'required',
                'string',
                'unique:domains,domain,' . $domain->id,
            ],
            'is_verified' => 'required',
            'is_ssl_enabled' => 'required',
            'status' => 'required',
        ]);

        $domain->update($request->all());

        return response()->json([
            'message' => __('Domain updated successfully'),
            'redirect' => route('admin.domains.index')
        ]);
    }

    public function status(Request $request, $id)
    {
        $domain = Domain::findOrFail($id);
        $domain->update(['status' => $request->status]);
        return response()->json(['message' => 'Domain']);
    }

    public function destroy(Domain $domain)
    {
        $domain->delete();
        return response()->json([
            'message' => __('Domain deleted successfully'),
            'redirect' => route('admin.domains.index')
        ]);
    }

    public function deleteAll(Request $request)
    {
        Domain::whereIn('id', $request->ids)->delete();
        return response()->json([
            'message' => __('Domains deleted successfully'),
            'redirect' => route('admin.domains.index')
        ]);
    }

    public function instructions()
    {
        return view('customdomainaddon::admin.instructions');
    }

    public function reject(Request $request, string $id)
    {
        $request->validate([
            'notes' => 'required|string|max:255',
        ]);

        $domain = Domain::findOrFail($id);

        if ($domain) {
            $domain->update([
                'status' => 2,
                'notes' => $request->notes,
            ]);

            return response()->json([
                'message' => __('Domain has been rejected successfully.'),
                'redirect' => route('admin.domains.index'),
            ]);
        } else {
            return response()->json(['message' => 'request not found'], 404);
        }
    }

    public function approved(Request $request, string $id)
    {
        $request->validate([
            'notes' => 'required|string|max:255',
        ]);

        $domain = Domain::findOrFail($id);

        if ($domain) {
            $domain->update([
                'status' => 1,
                'is_verified' => 1,
                'notes' => $request->notes,
            ]);

            return response()->json([
                'message' => __('Domain has been approved successfully.'),
                'redirect' => route('admin.domains.index'),
            ]);
        } else {
            return response()->json(['message' => 'request not found'], 404);
        }
    }
}
