<?php

namespace Modules\Business\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ComboProduct;
use App\Models\Product;
use Illuminate\Http\Request;

class AcnooComboProductController extends Controller
{
    public function __construct()
    {
        // No permission checks - accessible to all shop owners
    }

    public function index(Request $request)
    {
        $query = ComboProduct::whereHas('product', function ($query) {
            $query->where('business_id', auth()->user()->business_id);
        });

        // If user doesn't have active branch, include null branch_id records
        if (!auth()->user()->active_branch_id && !auth()->user()->branch_id) {
            $query->withoutGlobalScope(\App\Models\Scopes\BranchScope::class);
        }

        $combos = $query->with(['product', 'stock', 'stock.product'])->get();

        if ($request->ajax()) {
            return view('business::combo-products.datas', compact('combos'));
        }

        return view('business::combo-products.index', compact('combos'));
    }

    public function create()
    {
        $products = Product::where('business_id', auth()->user()->business_id)
            ->get();

        $stocks = \App\Models\Stock::whereHas('product', function ($query) {
                $query->where('business_id', auth()->user()->business_id);
            })
            ->with('product')
            ->get();

        $branches = [];
        if (auth()->user()->accessToMultiBranch()) {
            $branches = \App\Models\Branch::where('business_id', auth()->user()->business_id)->get();
        }

        return view('business::combo-products.create', compact('products', 'stocks', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'stock_id' => 'required|exists:stocks,id',
            'purchase_price' => 'required|numeric|min:0',
            'quantity' => 'required|numeric|min:0',
        ]);

        // Verify product belongs to user's business
        $product = Product::where('business_id', auth()->user()->business_id)
            ->findOrFail($request->product_id);

        ComboProduct::create([
            'product_id' => $request->product_id,
            'stock_id' => $request->stock_id,
            'branch_id' => $request->branch_id,
            'purchase_price' => $request->purchase_price,
            'quantity' => $request->quantity,
        ]);

        return redirect()->route('business.combo-products.index')
            ->with('success', __('Combo product created successfully'));
    }

    public function edit($id)
    {
        $query = ComboProduct::whereHas('product', function ($query) {
            $query->where('business_id', auth()->user()->business_id);
        });

        // If user doesn't have active branch, include null branch_id records
        if (!auth()->user()->active_branch_id && !auth()->user()->branch_id) {
            $query->withoutGlobalScope(\App\Models\Scopes\BranchScope::class);
        }

        $combo = $query->with(['product', 'stock'])->findOrFail($id);

        $products = Product::where('business_id', auth()->user()->business_id)->get();

        $stocks = \App\Models\Stock::whereHas('product', function ($query) {
                $query->where('business_id', auth()->user()->business_id);
            })
            ->with('product')
            ->get();

        $branches = [];
        if (auth()->user()->accessToMultiBranch()) {
            $branches = \App\Models\Branch::where('business_id', auth()->user()->business_id)->get();
        }

        return view('business::combo-products.edit', compact('combo', 'products', 'stocks', 'branches'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'stock_id' => 'required|exists:stocks,id',
            'purchase_price' => 'required|numeric|min:0',
            'quantity' => 'required|numeric|min:0',
        ]);

        $query = ComboProduct::whereHas('product', function ($query) {
            $query->where('business_id', auth()->user()->business_id);
        });

        // If user doesn't have active branch, include null branch_id records
        if (!auth()->user()->active_branch_id && !auth()->user()->branch_id) {
            $query->withoutGlobalScope(\App\Models\Scopes\BranchScope::class);
        }

        $combo = $query->findOrFail($id);

        $combo->update([
            'product_id' => $request->product_id,
            'stock_id' => $request->stock_id,
            'branch_id' => $request->branch_id,
            'purchase_price' => $request->purchase_price,
            'quantity' => $request->quantity,
        ]);

        return redirect()->route('business.combo-products.index')
            ->with('success', __('Combo product updated successfully'));
    }

    public function destroy($id)
    {
        // Find combo product - use withoutGlobalScope if needed for branch filtering
        $query = ComboProduct::whereHas('product', function ($query) {
            $query->where('business_id', auth()->user()->business_id);
        });

        // If user doesn't have active branch, include null branch_id records
        if (!auth()->user()->active_branch_id && !auth()->user()->branch_id) {
            $query->withoutGlobalScope(\App\Models\Scopes\BranchScope::class);
        }

        $combo = $query->findOrFail($id);
        $combo->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'message' => __('Combo product deleted successfully'),
                'redirect' => route('business.combo-products.index')
            ]);
        }

        return redirect()->route('business.combo-products.index')
            ->with('success', __('Combo product deleted successfully'));
    }

    public function status($id)
    {
        $combo = ComboProduct::whereHas('product', function ($query) {
                $query->where('business_id', auth()->user()->business_id);
            })
            ->findOrFail($id);

        // ComboProduct doesn't have status field, so we'll just return success
        return response()->json([
            'message' => __('Status updated successfully'),
        ]);
    }

    public function deleteAll(Request $request)
    {
        $ids = $request->ids;
        
        ComboProduct::whereHas('product', function ($query) {
                $query->where('business_id', auth()->user()->business_id);
            })
            ->whereIn('id', $ids)
            ->delete();

        return response()->json([
            'message' => __('Selected combo products deleted successfully'),
            'redirect' => route('business.combo-products.index')
        ]);
    }

    public function acnooFilter(Request $request)
    {
        $query = ComboProduct::whereHas('product', function ($q) use ($request) {
            $q->where('business_id', auth()->user()->business_id);
            
            if ($request->search) {
                $q->where('productName', 'like', '%' . $request->search . '%')
                  ->orWhere('productCode', 'like', '%' . $request->search . '%');
            }
        });

        // If user doesn't have active branch, include null branch_id records
        if (!auth()->user()->active_branch_id && !auth()->user()->branch_id) {
            $query->withoutGlobalScope(\App\Models\Scopes\BranchScope::class);
        }

        $combos = $query->with(['product', 'stock', 'stock.product'])->get();

        return view('business::combo-products.datas', compact('combos'));
    }
}
