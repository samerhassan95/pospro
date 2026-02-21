<?php

namespace Modules\Business\App\Http\Controllers;

use App\Helpers\HasUploader;
use App\Models\PaymentType;
use App\Models\Stock;
use App\Models\Vat;
use Carbon\Carbon;
use App\Models\Sale;
use App\Models\Brand;
use App\Models\Party;
use App\Models\Product;
use App\Models\Business;
use App\Models\Category;
use App\Models\SaleReturn;
use App\Models\SaleDetails;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Validation\Rule;

class AcnooSaleController extends Controller
{
    use HasUploader;

    public function __construct()
    {
        $this->middleware('check.permission:sales.create')->only(['create', 'store']);
        $this->middleware('check.permission:sales.read')->only(['index', 'getZatcaIssues']);
        $this->middleware('check.permission:sales.update')->only(['edit', 'update']);
        $this->middleware('check.permission:sales.delete')->only(['destroy', 'deleteAll']);
        $this->middleware('check.permission:inventory.create')->only(['createInventory']);
    }

    public function index(Request $request)
    {
        $salesWithReturns = SaleReturn::where('business_id', auth()->user()->business_id)
            ->pluck('sale_id')
            ->toArray();

        $query = Sale::with('user:id,name', 'branch:id,name', 'party:id,name,email,phone,type', 'details', 'details.product:id,productName,category_id', 'details.product.category:id,categoryName', 'payment_type:id,name')
            ->where('business_id', auth()->user()->business_id)
            ->whereDate('saleDate', Carbon::today())
            ->latest();

        if ($request->has('today') && $request->today) {
            $query->whereDate('saleDate', Carbon::today());
        }

        $sales = $query->paginate(5);

        $branches = Branch::withTrashed()->where('business_id', auth()->user()->business_id)->latest()->get();

        return view('business::sales.index', compact('sales', 'salesWithReturns', 'branches'));
    }

    public function acnooFilter(Request $request)
    {
        $salesWithReturns = SaleReturn::where('business_id', auth()->user()->business_id)
            ->pluck('sale_id')
            ->toArray();

        $salesQuery = Sale::with('user:id,name', 'branch:id,name', 'party:id,name,email,phone,type', 'details', 'details.product:id,productName,category_id', 'details.product.category:id,categoryName', 'payment_type:id,name')
            ->where('business_id', auth()->user()->business_id);
        $salesQuery->when($request->branch_id, function ($q) use ($request) {
            $q->where('branch_id', $request->branch_id);
        });

        // Default to today
        $startDate = Carbon::today()->format('Y-m-d');
        $endDate = Carbon::today()->format('Y-m-d');

        if ($request->custom_days === 'yesterday') {
            $startDate = Carbon::yesterday()->format('Y-m-d');
            $endDate = Carbon::yesterday()->format('Y-m-d');
        } elseif ($request->custom_days === 'last_seven_days') {
            $startDate = Carbon::today()->subDays(6)->format('Y-m-d');
        } elseif ($request->custom_days === 'last_thirty_days') {
            $startDate = Carbon::today()->subDays(29)->format('Y-m-d');
        } elseif ($request->custom_days === 'current_month') {
            $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        } elseif ($request->custom_days === 'last_month') {
            $startDate = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');
        } elseif ($request->custom_days === 'current_year') {
            $startDate = Carbon::now()->startOfYear()->format('Y-m-d');
            $endDate = Carbon::now()->endOfYear()->format('Y-m-d');
        } elseif ($request->custom_days === 'custom_date' && $request->from_date && $request->to_date) {
            $startDate = Carbon::parse($request->from_date)->format('Y-m-d');
            $endDate = Carbon::parse($request->to_date)->format('Y-m-d');
        }

        $salesQuery->whereDate('saleDate', '>=', $startDate)
            ->whereDate('saleDate', '<=', $endDate);

        // Search Filter
        if ($request->filled('search')) {
            $salesQuery->where(function ($query) use ($request) {
                $query->where('paymentType', 'like', '%' . $request->search . '%')
                    ->orWhere('invoiceNumber', 'like', '%' . $request->search . '%')
                    ->orWhereHas('party', function ($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->search . '%');
                    })
                    ->orWhereHas('payment_type', function ($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->search . '%');
                    })
                    ->orWhereHas('branch', function ($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        $perPage = $request->input('per_page', 10);
        $sales = $salesQuery->latest()->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'data' => view('business::sales.datas', compact('sales', 'salesWithReturns'))->render()
            ]);
        }

        return redirect(url()->previous());
    }

    public function productFilter(Request $request)
    {
        $total_products_count = Product::where('business_id', auth()->user()->business_id)
            ->whereHas('stocks', function ($q) {
                $q->where('productStock', '>', 0);
            })
            ->count();

        $products = Product::where('business_id', auth()->user()->business_id)
            ->whereHas('stocks', function ($q) {
                $q->where('productStock', '>', 0);
            })
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('productName', 'like', '%' . $request->search . '%')
                        ->orWhere('productCode', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->category_id, function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            })
            ->when($request->brand_id, function ($query) use ($request) {
                $query->where('brand_id', $request->brand_id);
            })
            ->latest()
            ->get();

        // Query categories for search options
        $categories = Category::where('business_id', auth()->user()->business_id)
            ->when($request->search, function ($query) use ($request) {
                $query->where('categoryName', 'like', '%' . $request->search . '%');
            })
            ->get();

        // Query brands for search options
        $brands = Brand::where('business_id', auth()->user()->business_id)
            ->when($request->search, function ($query) use ($request) {
                $query->where('brandName', 'like', '%' . $request->search . '%');
            })
            ->get();

        $total_products = $products->count();

        if ($request->ajax()) {
            return response()->json([
                'total_products' => $total_products,
                'total_products_count' => $total_products_count,
                'product_id' => $total_products == 1 ? $products->first()->id : null,
                'data' => view('business::sales.product-list-new', compact('products'))->render(),
                'categories' => view('business::sales.category-list', compact('categories'))->render(),
                'brands' => view('business::sales.brand-list', compact('brands'))->render(),
            ]);
        }

        return redirect(url()->previous());
    }

    // Category search Filter
    public function categoryFilter(Request $request)
    {
        $search = $request->search;
        $categories = Category::where('business_id', auth()->user()->business_id)
            ->when($search, function ($query) use ($search) {
                $query->where('categoryName', 'like', '%' . $search . '%');
            })
            ->get();

        return response()->json([
            'categories' => view('business::sales.category-list', compact('categories'))->render(),
        ]);
    }

    // Brand search Filter
    public function brandFilter(Request $request)
    {
        $search = $request->search;
        $brands = Brand::where('business_id', auth()->user()->business_id)
            ->when($search, function ($query) use ($search) {
                $query->where('brandName', 'like', '%' . $search . '%');
            })
            ->get();

        return response()->json([
            'brands' => view('business::sales.brand-list', compact('brands'))->render(),
        ]);
    }

    public function create()
    {
        $business_id = auth()->user()->business_id;

        // Clears all cart items
        Cart::destroy();

        $customers = Party::where('type', '!=', 'supplier')
            ->where('business_id', $business_id)
            ->latest()
            ->get();

        $products = Product::with('category:id,categoryName', 'unit:id,unitName', 'stocks', 'stocks.warehouse')
            ->where('business_id', $business_id)
            ->withSum('stocks as total_stock', 'productStock')
            ->having('total_stock', '>', 0)
            ->latest()
            ->get();

        $categories = Category::where('business_id', $business_id)->latest()->get();
        $brands = Brand::where('business_id', $business_id)->latest()->get();
        $vats = Vat::where('business_id', $business_id)->whereStatus(1)->latest()->get();
        $payment_types = PaymentType::where('business_id', $business_id)->whereStatus(1)->latest()->get();

        // Generate a unique invoice number
        $sale_id = (Sale::max('id') ?? 0) + 1;
        $invoice_no = 'S-' . str_pad($sale_id, 5, '0', STR_PAD_LEFT);

        return view('business::sales.create', compact('customers', 'products', 'invoice_no', 'categories', 'brands', 'vats', 'payment_types'));
    }

    /** Get customer wise prices */
    public function getProductPrices(Request $request)
    {
        $type = $request->type;

        $stocks = Stock::with('product')
            ->whereHas('product', function ($query) {
                $query->where('business_id', auth()->user()->business_id);
            })
            ->where('productStock', '>', 0)
            ->get();

        $prices = [];

        foreach ($stocks as $stock) {
            $productId = $stock->product_id;

            if (!isset($prices[$productId])) {
                if ($type === 'Dealer') {
                    $prices[$productId] = currency_format($stock->productDealerPrice, currency: business_currency());
                } elseif ($type === 'Wholesaler') {
                    $prices[$productId] = currency_format($stock->productWholeSalePrice, currency: business_currency());
                } else {
                    $prices[$productId] = currency_format($stock->productSalePrice, currency: business_currency());
                }
            }
        }

        return response()->json($prices);
    }

    /** Get batch wise prices */
    public function getStockPrices(Request $request)
    {
        $type = $request->type;

        $stocks = Stock::where('business_id', auth()->user()->business_id)
            ->where('productStock', '>', 0)
            ->get();

        $prices = [];

        foreach ($stocks as $stock) {
            if ($type === 'Dealer') {
                $prices[$stock->id] = currency_format($stock->productDealerPrice, currency: business_currency());
            } elseif ($type === 'Wholesaler') {
                $prices[$stock->id] = currency_format($stock->productWholeSalePrice, currency: business_currency());
            } else {
                // For Retailer or any other type
                $prices[$stock->id] = currency_format($stock->productSalePrice, currency: business_currency());
            }
        }
        return response()->json($prices);
    }

    /** Get cart info */
    public function showSaleCart()
    {
        $cart_contents = Cart::content()->filter(fn($item) => $item->options->type == 'sale');
        return view('business::sales.cart-list-new', compact('cart_contents'));
    }

    public function getCartData()
    {
        $cart_contents = Cart::content()->filter(fn($item) => $item->options->type == 'sale');

        $data['sub_total'] = 0;

        foreach ($cart_contents as $cart) {
            $data['sub_total'] += $cart->price;
        }
        $data['sub_total'] = currency_format($data['sub_total'], currency: business_currency());

        return response()->json($data);
    }

    public function store(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'invoiceNumber' => 'required|string',
            'customer_phone' => 'nullable|string',
            'receive_amount' => 'nullable|numeric',
            'vat_id' => 'nullable|exists:vats,id',
            'payment_type_id' => 'required|exists:payment_types,id',
            'discountAmount' => 'nullable|numeric',
            'discount_type' => 'nullable|in:flat,percent',
            'shipping_charge' => 'nullable|numeric',
            'saleDate' => 'nullable|date',
            'delivery_type' => 'nullable|string|in:delivery,pre-order,takeaway',
            // B2B Additional Fields
            'supply_date' => 'nullable|date',
            'po_number' => 'nullable|string|max:50',
            'contract_number' => 'nullable|string|max:50',
            'payment_terms' => 'nullable|string|max:255',
            'payment_means' => 'nullable|string|max:50',
            'shipping_address_line1' => 'nullable|string|max:255',
            'shipping_address_line2' => 'nullable|string|max:255',
            'shipping_city' => 'nullable|string|max:100',
            'shipping_postal_code' => 'nullable|string|max:10',
            'shipping_country_code' => 'nullable|string|size:2',
            'table_id' => 'nullable|exists:restaurant_tables,id',
        ]);

        $business_id = auth()->user()->business_id;

        // Get only 'sale' type items from cart
        $carts = Cart::content()->filter(fn($item) => $item->options->type == 'sale');

        if ($carts->count() < 1) {
            return response()->json(['message' => __('Cart is empty. Add items first!')], 400);
        }

        DB::beginTransaction();
        try {

            // Calculation: subtotal, vat, discount, shipping, rounding
            $subtotal = $carts->sum(fn($item) => (float) $item->subtotal);
            $vat = Vat::find($request->vat_id);
            $vatAmount = $vat ? ($subtotal * $vat->rate) / 100 : 0;

            $discountAmount = $request->discountAmount ?? 0;
            $subtotalWithVat = $subtotal + $vatAmount;

            if ($request->discount_type === 'percent') {
                $discountAmount = ($subtotalWithVat * $discountAmount) / 100;
            }
            if ($discountAmount > $subtotalWithVat) {
                return response()->json(['message' => __('Discount cannot be more than subtotal with VAT!')], 400);
            }

            $shippingCharge = $request->shipping_charge ?? 0;
            $actualTotalAmount = $subtotalWithVat - $discountAmount + $shippingCharge;
            $roundingTotalAmount = sale_rounding($actualTotalAmount);
            $rounding_amount = $roundingTotalAmount - $actualTotalAmount;
            $rounding_option = sale_rounding();

            $receiveAmount = $request->receive_amount ?? 0;
            $changeAmount = max($receiveAmount - $roundingTotalAmount, 0);
            $dueAmount = max($roundingTotalAmount - $receiveAmount, 0);
            $paidAmount = $receiveAmount - $changeAmount;

            // Determine if invoice is paid:
            // - Must have received payment (receive_amount > 0)
            // - Must have no due amount (dueAmount == 0)
            // - Must have paid amount equal to total amount
            $isPaid = ($receiveAmount > 0 && $dueAmount == 0 && $paidAmount >= $roundingTotalAmount) ? 1 : 0;

            // Update business/branch balance only if payment was received
            $business = Business::findOrFail($business_id);
            if ($paidAmount > 0) {
                updateBalance($paidAmount, 'increment');
            }

            // Get party information to determine invoice type
            $party = null;
            if ($request->party_id && $request->party_id != 'guest') {
                $party = Party::find($request->party_id);
            }
            
            // Create sale record
            $sale = Sale::create([
                'user_id' => auth()->id(),
                'business_id' => $business_id,
                'branch_id' => auth()->user()->branch_id ?? session('branch_id'),
                'type' => $request->type == 'inventory' ? 'inventory' : 'sale',
                'delivery_type' => $request->delivery_type ?? 'takeaway',
                'party_id' => $request->party_id == 'guest' ? null : $request->party_id,
                'invoiceNumber' => $request->invoiceNumber,
                'saleDate' => $request->saleDate ?? now(),
                'vat_id' => $request->vat_id,
                'vat_amount' => $vatAmount,
                'discountAmount' => $discountAmount,
                'discount_type' => $request->discount_type ?? 'flat',
                'discount_percent' => $request->discount_type == 'percent' ? $request->discountAmount : 0,
                'totalAmount' => $roundingTotalAmount,
                'actual_total_amount' => $actualTotalAmount,
                'rounding_amount' => $rounding_amount,
                'rounding_option' => $rounding_option,
                'paidAmount' => min($paidAmount, $roundingTotalAmount),
                'change_amount' => $changeAmount,
                'dueAmount' => $dueAmount,
                'payment_type_id' => $request->payment_type_id,
                'shipping_charge' => $shippingCharge,
                'isPaid' => $isPaid,
                'uuid' => \Illuminate\Support\Str::uuid()->toString(), // ZATCA UUID
                'invoice_type' => $party && $party->zatca_type === 'b2b' ? 'b2b' : 'b2c', // Auto-detect from party
                // B2B Additional Fields
                'supply_date' => $request->supply_date,
                'po_number' => $request->po_number,
                'contract_number' => $request->contract_number,
                'payment_terms' => $request->payment_terms,
                'payment_means' => $request->payment_means,
                'shipping_address_line1' => $request->shipping_address_line1,
                'shipping_address_line2' => $request->shipping_address_line2,
                'shipping_city' => $request->shipping_city,
                'shipping_postal_code' => $request->shipping_postal_code,
                'shipping_country_code' => $request->shipping_country_code ? strtoupper($request->shipping_country_code) : null,
                'table_id' => $request->table_id,
                'meta' => [
                    'customer_phone' => $request->customer_phone,
                    'note' => $request->note,
                ]
            ]);

            // Handle table status if assigned
            if ($request->table_id) {
                $table = \App\Models\RestaurantTable::find($request->table_id);
                if ($table) {
                    $table->updateStatus('free');
                    
                    // Mark any in-progress orders for this table as completed
                    \App\Models\TableOrder::where('table_id', $table->id)
                        ->where('status', 'in_progress')
                        ->update(['status' => 'completed', 'sale_id' => $sale->id]);
                }
            }

            $avgDiscount = $discountAmount / max($carts->count(), 1);
            $totalPurchaseAmount = 0;
            $saleDetailsData = [];

            foreach ($carts as $cartItem) {
                $qty = $cartItem->qty;
                $purchase_price = $cartItem->options->purchase_price ?? 0;
                $stock = Stock::where('id', $cartItem->options->stock_id)->first();

                $lossProfit = (($cartItem->price - $stock->productPurchasePrice) * $cartItem->qty) - $avgDiscount;

                if ($stock->productStock < $qty) {
                    $batchText = $stock->batch_no ? " ($stock->batch_no)" : "";
                    return response()->json([
                        'message' => __($cartItem->name . $batchText . ' - stock not available. Available: ' . $stock->productStock)
                    ], 400);
                }

                $stock->decrement('productStock', $qty);

                $saleDetailsData[] = [
                    'sale_id' => $sale->id,
                    'stock_id' => $cartItem->options->stock_id,
                    'product_id' => $cartItem->id,
                    'price' => $cartItem->price,
                    'lossProfit' => $lossProfit,
                    'quantities' => $cartItem->qty,
                    'productPurchasePrice' => $purchase_price,
                    'expire_date' => $cartItem->options->expire_date ?? null,
                ];

                $totalPurchaseAmount += $purchase_price * $qty;
            }

            // Insert all sale details
            SaleDetails::insert($saleDetailsData);

            $sale->update([
                'lossProfit' => $subtotal - $totalPurchaseAmount - $discountAmount,
            ]);

            // Handle due tracking for non-guest customers
            if ($dueAmount > 0) {

                if (!$request->party_id || $request->party_id == 'guest') {
                    return response()->json(['message' => __('You cannot sale in due for a walking customer.')], 400);
                }
                $party = Party::findOrFail($request->party_id);

                if ($party->credit_limit > 0 && ($party->due + $dueAmount) > $party->credit_limit) {
                    return response()->json([
                        'message' => __('Sale cannot be created. Party due will exceed credit limit!')
                    ], 400);
                }

                $party->update(['due' => $party->due + $dueAmount]);

                if ($party->phone && env('MESSAGE_ENABLED')) {
                    sendMessage($party->phone, saleMessage($sale, $party, $business->companyName));
                }
            }

            // Clear all items from cart
            foreach ($carts as $cartItem) {
                Cart::remove($cartItem->rowId);
            }

            // Notify user
            sendNotifyToUser($sale->id, route('business.sales.index', ['id' => $sale->id]), __('New sale created.'), $business_id);

            // Trigger ZATCA Reporting
            if (!empty($business->zatca_setting) && !empty($business->zatca_setting['csid'])) {
                \App\Jobs\ReportSaleToZatca::dispatch($sale->id);
            }

            DB::commit();

            return response()->json([
                'message' => __('Sales created successfully.'),
                'redirect' => route('business.sales.index'),
                'secondary_redirect_url' => route('business.sales.invoice', $sale->id),
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Sale Store Error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => __('Something went wrong!') . ' - ' . $e->getMessage()], 404);
        }
    }

    public function edit($id)
    {
        // Clears all cart items
        Cart::destroy();

        $sale = Sale::with('user:id,name', 'party:id,name,email,phone,type', 'details', 'details.stock', 'details.product:id,productName,category_id,unit_id,productCode,productSalePrice,productPicture', 'details.product.category:id,categoryName', 'details.product.unit:id,unitName', 'payment_type:id,name')
            ->where('business_id', auth()->user()->business_id)
            ->findOrFail($id);

        $customers = Party::where('type', '!=', 'supplier')
            ->where('business_id', auth()->user()->business_id)
            ->latest()
            ->get();

        $products = Product::with('category:id,categoryName', 'unit:id,unitName', 'stocks', 'stocks.warehouse')
            ->where('business_id', auth()->user()->business_id)
            ->withSum('stocks as total_stock', 'productStock')
            ->having('total_stock', '>', 0)
            ->latest()
            ->get();

        $categories = Category::where('business_id', auth()->user()->business_id)->latest()->get();
        $brands = Brand::where('business_id', auth()->user()->business_id)->latest()->get();
        $vats = Vat::where('business_id', auth()->user()->business_id)->whereStatus(1)->latest()->get();
        $payment_types = PaymentType::where('business_id', auth()->user()->business_id)->whereStatus(1)->latest()->get();

        // Add sale details to the cart
        foreach ($sale->details as $detail) {
            // Add to cart
            Cart::add([
                'id' => $detail->product_id,
                'name' => $detail->product->productName ?? '',
                'qty' => $detail->quantities,
                'price' => $detail->price ?? 0,
                'options' => [
                    'type' => 'sale',
                    'product_code' => $detail->product->productCode ?? '',
                    'product_unit_id' => $detail->product->unit_id ?? null,
                    'product_unit_name' => $detail->product->unit->unitName ?? '',
                    'product_image' => $detail->product->productPicture ?? '',
                    'stock_id' => $detail->stock_id ?? null,
                    'batch_no' => $detail->stock->batch_no ?? '',
                    'expire_date' => $detail->expire_date ?? '',
                    'purchase_price' => $detail->productPurchasePrice ?? 0,
                ],
            ]);
        }

        $cart_contents = Cart::content()->filter(fn($item) => $item->options->type == 'sale');

        if ($sale->type == 'inventory') {
            return view('business::sales.edit-inventory', compact('sale', 'customers', 'products', 'cart_contents', 'categories', 'brands', 'vats', 'payment_types'));
        } else {
            return view('business::sales.edit', compact('sale', 'customers', 'products', 'cart_contents', 'categories', 'brands', 'vats', 'payment_types'));
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'invoiceNumber' => 'required|string',
            'customer_phone' => 'nullable|string',
            'receive_amount' => 'nullable|numeric',
            'vat_id' => 'nullable|exists:vats,id',
            'payment_type_id' => 'required|exists:payment_types,id',
            'discountAmount' => 'nullable|numeric',
            'discount_type' => 'nullable|in:flat,percent',
            'saleDate' => 'nullable|date',
            'shipping_charge' => 'nullable|numeric',
            // B2B Additional Fields
            'supply_date' => 'nullable|date',
            'po_number' => 'nullable|string|max:50',
            'contract_number' => 'nullable|string|max:50',
            'payment_terms' => 'nullable|string|max:255',
            'payment_means' => 'nullable|string|max:50',
            'shipping_address_line1' => 'nullable|string|max:255',
            'shipping_address_line2' => 'nullable|string|max:255',
            'shipping_city' => 'nullable|string|max:100',
            'shipping_postal_code' => 'nullable|string|max:10',
            'shipping_country_code' => 'nullable|string|size:2',
        ]);

        $business_id = auth()->user()->business_id;
        $carts = Cart::content()->filter(fn($item) => $item->options->type == 'sale');

        if ($carts->count() < 1) {
            return response()->json(['message' => __('Cart is empty. Add items first!')], 400);
        }

        DB::beginTransaction();
        try {
            $sale = Sale::findOrFail($id);
            $sale_prev_due = $sale->dueAmount;
            $prevDetails = $sale->details;

            $totalPurchaseAmount = 0;
            $subtotal = 0;

            foreach ($carts as $cartItem) {
                $prevProduct = $prevDetails->firstWhere('product_id', $cartItem->id);
                $stock = Stock::where('id', $cartItem->options->stock_id ?? null)
                    ->first() ?? Stock::where('product_id', $cartItem->id)->orderBy('id', 'asc')->first();

                if (!$stock) {
                    return response()->json([
                        'message' => __($cartItem->name . ' - no stock found.')
                    ], 400);
                }

                // Adjust available stock by adding back old quantity
                $availableStock = $stock->productStock + ($prevProduct->quantities ?? 0);

                if ($availableStock < $cartItem->qty) {
                    return response()->json([
                        'message' => __($cartItem->name . ' - stock not available for this product. Available quantity is: ' . $availableStock)
                    ], 400);
                }
                $totalPurchaseAmount += $cartItem->options->purchase_price * $cartItem->qty;
                $subtotal += (float)$cartItem->subtotal;
            }

            $vat = Vat::find($request->vat_id);
            $vatAmount = $vat ? ($subtotal * $vat->rate) / 100 : 0;
            $subtotalWithVat = $subtotal + $vatAmount;

            $discountAmount = $request->discountAmount ?? 0;
            if ($request->discount_type == 'percent') {
                $discountAmount = ($subtotalWithVat * $discountAmount) / 100;
            }
            if ($discountAmount > $subtotalWithVat) {
                return response()->json(['message' => __('Discount cannot be more than subtotal with VAT!')], 400);
            }

            $shippingCharge = $request->shipping_charge ?? 0;
            $actualTotalAmount = $subtotalWithVat - $discountAmount + $shippingCharge;
            $roundingTotalAmount = sale_rounding($actualTotalAmount, $sale->rounding_option);
            $rounding_amount = $roundingTotalAmount - $actualTotalAmount;

            $receiveAmount = $request->receive_amount ?? 0;
            $changeAmount = $receiveAmount > $roundingTotalAmount ? $receiveAmount - $roundingTotalAmount : 0;
            $dueAmount = max($roundingTotalAmount - $receiveAmount, 0);
            $paidAmount = $receiveAmount - $changeAmount;

            // Determine if invoice is paid:
            // - Must have received payment (receive_amount > 0)
            // - Must have no due amount (dueAmount == 0)
            // - Must have paid amount equal to total amount
            $isPaid = ($receiveAmount > 0 && $dueAmount == 0 && $paidAmount >= $roundingTotalAmount) ? 1 : 0;

            $business = Business::findOrFail($business_id);
            // Adjust balance: subtract old paidAmount and add new paidAmount
            $oldPaidAmount = $sale->paidAmount ?? 0;
            $balanceDifference = $paidAmount - $oldPaidAmount;
            if ($balanceDifference != 0) {
                if ($balanceDifference > 0) {
                    updateBalance($balanceDifference, 'increment');
                } else {
                    updateBalance(abs($balanceDifference), 'decrement');
                }
            }

            $sale->update([
                'invoiceNumber' => $request->invoiceNumber,
                'saleDate' => $request->saleDate ?? now(),
                'vat_id' => $request->vat_id,
                'vat_amount' => $vatAmount,
                'discountAmount' => $discountAmount,
                'discount_type' => $request->discount_type ?? 'flat',
                'discount_percent' => $request->discount_type == 'percent' ? $request->discountAmount : 0,
                'totalAmount' => $roundingTotalAmount,
                'actual_total_amount' => $actualTotalAmount,
                'rounding_amount' => $rounding_amount,
                'lossProfit' => $subtotal - $totalPurchaseAmount - $discountAmount,
                'paidAmount' => $paidAmount > $roundingTotalAmount ? $roundingTotalAmount : $paidAmount,
                'change_amount' => $changeAmount,
                'dueAmount' => $dueAmount,
                'payment_type_id' => $request->payment_type_id,
                'isPaid' => $isPaid,
                // B2B Additional Fields
                'supply_date' => $request->supply_date,
                'po_number' => $request->po_number,
                'contract_number' => $request->contract_number,
                'payment_terms' => $request->payment_terms,
                'payment_means' => $request->payment_means,
                'shipping_address_line1' => $request->shipping_address_line1,
                'shipping_address_line2' => $request->shipping_address_line2,
                'shipping_city' => $request->shipping_city,
                'shipping_postal_code' => $request->shipping_postal_code,
                'shipping_country_code' => $request->shipping_country_code ? strtoupper($request->shipping_country_code) : null,
                'meta' => [
                    'customer_phone' => $request->customer_phone,
                    'note' => $request->note,
                ]
            ]);

            SaleDetails::where('sale_id', $sale->id)->delete();

            $avgDiscount = $discountAmount / $carts->count();
            $saleDetailsData = [];

            foreach ($carts as $cartItem) {
                $prevProduct = $prevDetails->firstWhere('product_id', $cartItem->id);
                $oldQty = $prevProduct ? $prevProduct->quantities : 0;
                $newQty = $cartItem->qty;
                $diffQty = $newQty - $oldQty;

                $lossProfit = (($cartItem->price - $cartItem->options->purchase_price) * $newQty) - $avgDiscount;

                $saleDetailsData[] = [
                    'sale_id' => $sale->id,
                    'stock_id' => $cartItem->options->stock_id,
                    'product_id' => $cartItem->id,
                    'price' => $cartItem->price,
                    'lossProfit' => $lossProfit,
                    'quantities' => $newQty,
                    'expire_date' => $cartItem->options->expire_date ?? null,
                    'productPurchasePrice' => $cartItem->options->purchase_price ?? 0,
                ];

                $stock = Stock::where('id', $cartItem->options->stock_id ?? null)
                    ->first() ?? Stock::where('product_id', $cartItem->id)->orderBy('id', 'asc')->first();

                $stock->productStock += $diffQty;
            }

            SaleDetails::insert($saleDetailsData);

            if ($dueAmount > 0) {
                if (!$request->party_id || $request->party_id == 'guest') {
                    return response()->json(['message' => __('You cannot sale in due for a walk-in customer.')], 400);
                }

                $party = Party::findOrFail($request->party_id);

                // Party limit check
                $newTotalDue = $request->party_id == $sale->party_id ? $party->due + $dueAmount - $sale_prev_due : $party->due + $dueAmount;

                if ($party->credit_limit > 0 && $newTotalDue > $party->credit_limit) {
                    return response()->json([
                        'message' => __('Cannot update sale. Party due will exceed credit limit!')
                    ], 400);
                }

                if ($request->party_id == $sale->party_id) {
                    $party->update(['due' => $party->due + $dueAmount - $sale_prev_due]);
                } else {
                    $party->update(['due' => $party->due + $dueAmount]);
                    $prevParty = Party::findOrFail($sale->party_id);
                    $prevParty->update(['due' => $prevParty->due - $sale_prev_due]);
                }

                if ($party->phone && env('MESSAGE_ENABLED')) {
                    sendMessage($party->phone, saleMessage($sale, $party, $business->companyName));
                }
            }

            foreach ($carts as $cartItem) {
                Cart::remove($cartItem->rowId);
            }

            sendNotifyToUser($sale->id, route('business.sales.index', ['id' => $sale->id]), __('Sale has been updated.'), $business_id);

            DB::commit();

            return response()->json([
                'message' => __('Sales updated successfully.'),
                'redirect' => route('business.sales.index'),
                'secondary_redirect_url' => route('business.sales.invoice', $sale->id),
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Sale Update Error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => __('Something went wrong!') . ' Error: ' . $e->getMessage()], 404);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $sale = Sale::findOrFail($id);

            foreach ($sale->details as $detail) {
                $stock = Stock::find($detail->stock_id);

                if (!$stock) {
                    $stock = Stock::where('product_id', $detail->product_id)->orderBy('id', 'asc')->first();
                }

                if ($stock) {
                    $stock->increment('productStock', $detail->quantities);
                }
            }

            if ($sale->party_id) {
                $party = Party::findOrFail($sale->party_id);
                $party->update(['due' => $party->due - $sale->dueAmount]);
            }

            updateBalance($sale->paidAmount, 'decrement');

            sendNotifyToUser($sale->id, route('business.sales.index', ['id' => $sale->id]), __('Sale has been deleted.'), $sale->business_id);

            $sale->delete();

            // Clears all cart items
            Cart::destroy();

            DB::commit();

            return response()->json([
                'message' => __('Sale deleted successfully.'),
                'redirect' => route('business.sales.index')
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => __('Something went wrong!')], 404);
        }
    }

    public function getInvoice($sale_id)
    {
        $sale = Sale::where('business_id', auth()->user()->business_id)
            ->with([
                'user:id,name,role',
                'party:id,name,phone,address,zatca_type,vat_number,commercial_registration,additional_id,building_number,street_name,district,city,postal_code,country_code',
                'business:id,phoneNumber,companyName,vat_name,vat_no,commercial_registration,additional_id,bank_name,bank_account_number,address,email,building_number,street_name,district,city,postal_code,country_code',
                'details:id,price,quantities,product_id,sale_id,stock_id',
                'details.stock:id,batch_no',
                'details.product:id,productName',
                'payment_type:id,name',
                'vat:id,name,rate'
            ])
            ->findOrFail($sale_id);

        $sale_returns = SaleReturn::with('sale:id,party_id,isPaid,totalAmount,dueAmount,paidAmount,invoiceNumber', 'sale.party:id,name', 'details', 'details.saleDetail.product:id,productName')
            ->where('business_id', auth()->user()->business_id)
            ->where('sale_id', $sale_id)
            ->latest()
            ->get();

        // sum of  return_qty
        $sale->details = $sale->details->map(function ($detail) use ($sale_returns) {
            $return_qty_sum = $sale_returns->flatMap(function ($return) use ($detail) {
                return $return->details->where('saleDetail.id', $detail->id)->pluck('return_qty');
            })->sum();

            $detail->quantities = $detail->quantities + $return_qty_sum;
            return $detail;
        });

        // Calculate the initial discount for each product during sale returns
        $total_discount = 0;
        $product_discounts = [];

        foreach ($sale_returns as $return) {
            foreach ($return->details as $detail) {
                // Add the return quantities and return amounts for each sale_detail_id
                if (!isset($product_discounts[$detail->sale_detail_id])) {
                    // Initialize the first occurrence
                    $product_discounts[$detail->sale_detail_id] = [
                        'return_qty' => 0,
                        'return_amount' => 0,
                        'price' => $detail->saleDetail->price,
                    ];
                }

                // Accumulate quantities and return amounts for the same sale_detail_id
                $product_discounts[$detail->sale_detail_id]['return_qty'] += $detail->return_qty;
                $product_discounts[$detail->sale_detail_id]['return_amount'] += $detail->return_amount;
            }
        }

        // Calculate the total discount based on accumulated quantities and return amounts
        foreach ($product_discounts as $data) {
            $product_price = $data['price'] * $data['return_qty'];
            $discount = $product_price - $data['return_amount'];

            $total_discount += $discount;
        }

        return view('business::sales.invoice', compact('sale', 'sale_returns', 'total_discount'));
    }

    public function deleteAll(Request $request)
    {
        DB::beginTransaction();

        try {
            $sales = Sale::whereIn('id', $request->ids)->get();
            $business = Business::findOrFail(auth()->user()->business_id);

            foreach ($sales as $sale) {
                // Restore stock
                foreach ($sale->details as $detail) {
                    $stock = Stock::find($detail->stock_id);

                    if (!$stock) {
                        $stock = Stock::where('product_id', $detail->product_id)->orderBy('id', 'asc')->first();
                    }

                    if ($stock) {
                        $stock->increment('productStock', $detail->quantities);
                    }
                }

                // Adjust party due
                if ($sale->party_id) {
                    $party = Party::findOrFail($sale->party_id);
                    $party->update(['due' => $party->due - $sale->dueAmount]);
                }

                // Adjust business balance
                updateBalance($sale->paidAmount, 'decrement');

                sendNotifyToUser($sale->id, route('business.sales.index', ['id' => $sale->id]), __('Sale has been deleted.'), $sale->business_id);

                $sale->delete();
            }

            Cart::destroy();

            DB::commit();

            return response()->json([
                'message' => __('Selected sales deleted successfully.'),
                'redirect' => route('business.sales.index')
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => __('Something went wrong!')], 404);
        }
    }

    public function generatePDF(Request $request, $sale_id)
    {
        $sale = Sale::where('business_id', auth()->user()->business_id)->with('user:id,name,role', 'party:id,name,phone,address', 'business:id,phoneNumber,companyName,vat_name,vat_no', 'details:id,price,quantities,product_id,sale_id,stock_id', 'details.stock:id,batch_no', 'details.product:id,productName', 'payment_type:id,name')->findOrFail($sale_id);

        $sale_returns = SaleReturn::with('sale:id,party_id,isPaid,totalAmount,dueAmount,paidAmount,invoiceNumber', 'sale.party:id,name', 'details', 'details.saleDetail.product:id,productName')
            ->where('business_id', auth()->user()->business_id)
            ->where('sale_id', $sale_id)
            ->latest()
            ->get();

        // sum of  return_qty
        $sale->details = $sale->details->map(function ($detail) use ($sale_returns) {
            $return_qty_sum = $sale_returns->flatMap(function ($return) use ($detail) {
                return $return->details->where('saleDetail.id', $detail->id)->pluck('return_qty');
            })->sum();

            $detail->quantities = $detail->quantities + $return_qty_sum;
            return $detail;
        });

        // Calculate the initial discount for each product during sale returns
        $total_discount = 0;
        $product_discounts = [];

        foreach ($sale_returns as $return) {
            foreach ($return->details as $detail) {
                // Add the return quantities and return amounts for each sale_detail_id
                if (!isset($product_discounts[$detail->sale_detail_id])) {
                    // Initialize the first occurrence
                    $product_discounts[$detail->sale_detail_id] = [
                        'return_qty' => 0,
                        'return_amount' => 0,
                        'price' => $detail->saleDetail->price,
                    ];
                }

                // Accumulate quantities and return amounts for the same sale_detail_id
                $product_discounts[$detail->sale_detail_id]['return_qty'] += $detail->return_qty;
                $product_discounts[$detail->sale_detail_id]['return_amount'] += $detail->return_amount;
            }
        }

        // Calculate the total discount based on accumulated quantities and return amounts
        foreach ($product_discounts as $data) {
            $product_price = $data['price'] * $data['return_qty'];
            $discount = $product_price - $data['return_amount'];

            $total_discount += $discount;
        }

        $pdf = Pdf::loadView('business::sales.pdf', compact('sale', 'sale_returns', 'total_discount'));
        return $pdf->download('sales-invoice.pdf');
    }

    public function sendMail(Request $request, $sale_id)
    {
        $sale = Sale::where('business_id', auth()->user()->business_id)->with('user:id,name,role', 'party:id,name,phone,address', 'business:id,phoneNumber,companyName,vat_name,vat_no', 'details:id,price,quantities,product_id,sale_id,stock_id', 'details.stock:id,batch_no', 'details.product:id,productName', 'payment_type:id,name')->findOrFail($sale_id);

        $sale_returns = SaleReturn::with('sale:id,party_id,isPaid,totalAmount,dueAmount,paidAmount,invoiceNumber', 'sale.party:id,name', 'details', 'details.saleDetail.product:id,productName')
            ->where('business_id', auth()->user()->business_id)
            ->where('sale_id', $sale_id)
            ->latest()
            ->get();

        // sum of  return_qty
        $sale->details = $sale->details->map(function ($detail) use ($sale_returns) {
            $return_qty_sum = $sale_returns->flatMap(function ($return) use ($detail) {
                return $return->details->where('saleDetail.id', $detail->id)->pluck('return_qty');
            })->sum();

            $detail->quantities = $detail->quantities + $return_qty_sum;
            return $detail;
        });

        // Calculate the initial discount for each product during sale returns
        $total_discount = 0;
        $product_discounts = [];

        foreach ($sale_returns as $return) {
            foreach ($return->details as $detail) {
                // Add the return quantities and return amounts for each sale_detail_id
                if (!isset($product_discounts[$detail->sale_detail_id])) {
                    // Initialize the first occurrence
                    $product_discounts[$detail->sale_detail_id] = [
                        'return_qty' => 0,
                        'return_amount' => 0,
                        'price' => $detail->saleDetail->price,
                    ];
                }

                // Accumulate quantities and return amounts for the same sale_detail_id
                $product_discounts[$detail->sale_detail_id]['return_qty'] += $detail->return_qty;
                $product_discounts[$detail->sale_detail_id]['return_amount'] += $detail->return_amount;
            }
        }

        // Calculate the total discount based on accumulated quantities and return amounts
        foreach ($product_discounts as $data) {
            $product_price = $data['price'] * $data['return_qty'];
            $discount = $product_price - $data['return_amount'];

            $total_discount += $discount;
        }

        $pdf = Pdf::loadView('business::sales.pdf', compact('sale', 'sale_returns', 'total_discount'));

        // Send email with PDF attachment
        Mail::raw('Please find attached your sales invoice.', function ($message) use ($pdf) {
            $message->to(auth()->user()->email)
                ->subject('Sales Invoice')
                ->attachData($pdf->output(), 'sales-invoice.pdf', [
                    'mime' => 'application/pdf',
                ]);
        });

        return response()->json([
            'message' => __('Email Sent Successfully.'),
            'redirect' => route('business.sales.index'),
        ]);
    }

    public function createCustomer(Request $request)
    {
        $request->validate([
            'phone' => 'nullable|max:20|' . Rule::unique('parties')->where('business_id', auth()->user()->business_id),
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:Retailer,Dealer,Wholesaler,Supplier',
            'email' => 'nullable|email',
            'image' => 'nullable|image',
            'address' => 'nullable|string|max:255',
            'due' => 'nullable|numeric|min:0',
        ]);

        Party::create($request->except('image', 'due') + [
            'due' => $request->due ?? 0,
            'image' => $request->image ? $this->upload($request, 'image') : NULL,
            'business_id' => auth()->user()->business_id
        ]);

        return response()->json([
            'message'   => __('Customer created successfully'),
            'redirect'  => route('business.sales.create')
        ]);
    }


    public function createInventory()
    {
        // Clears all cart items
        Cart::destroy();

        $customers = Party::where('type', '!=', 'supplier')
            ->where('business_id', auth()->user()->business_id)
            ->latest()
            ->get();

        $products = Product::with([
            'stocks' => function ($query) {
                $query->where('productStock', '>', 0);
            },
            'category:id,categoryName',
            'unit:id,unitName',
            'stocks.warehouse'
        ])
            ->where('business_id', auth()->user()->business_id)
            ->withSum('stocks as total_stock', 'productStock')
            ->having('total_stock', '>', 0)
            ->latest()
            ->get();

        $categories = Category::where('business_id', auth()->user()->business_id)->latest()->get();
        $vats = Vat::where('business_id', auth()->user()->business_id)->whereStatus(1)->latest()->get();
        $payment_types = PaymentType::where('business_id', auth()->user()->business_id)->whereStatus(1)->latest()->get();

        // Generate a unique invoice number
        $sale_id = (Sale::max('id') ?? 0) + 1;
        $invoice_no = 'S-' . str_pad($sale_id, 5, '0', STR_PAD_LEFT);

        return view('business::sales.inventory', compact('customers', 'products', 'invoice_no', 'categories', 'vats', 'payment_types'));
    }

    /**
     * Get ZATCA compliance issues for a sale
     */
    public function getZatcaIssues($id)
    {
        try {
            $sale = Sale::where('business_id', auth()->user()->business_id)
                ->with(['business:id,business_id,vat_no,address,companyName,zatca_setting', 'details:id,sale_id,product_id,quantities,price'])
                ->select('id', 'business_id', 'uuid', 'saleDate', 'totalAmount', 'vat_amount', 'zatca_status', 'zatca_response', 'invoiceNumber')
                ->findOrFail($id);

            $issues = [];

            // Quick checks first
            $business = $sale->business;

            if (!$business) {
                return response()->json([
                    'success' => true,
                    'issues' => [__('Business not found')],
                    'has_issues' => true,
                    'zatca_status' => $sale->zatca_status ?? null
                ]);
            }

            // Check ZATCA settings
            if (empty($business->zatca_setting)) {
                $issues[] = __('ZATCA settings not configured. Please configure ZATCA settings first.');
            } else {
                $zatcaSetting = $business->zatca_setting;

                // Check CSID
                if (empty($zatcaSetting['csid'])) {
                    $issues[] = __('ZATCA CSID not configured');
                }

                // Check Secret
                if (empty($zatcaSetting['secret'])) {
                    $issues[] = __('ZATCA Secret not configured');
                }

                // Check Private Key
                if (empty($zatcaSetting['private_key'])) {
                    $issues[] = __('ZATCA Private Key not configured');
                }
            }

            // Check Business VAT Number
            if (empty($business->vat_no)) {
                $issues[] = __('Business VAT number is missing. Please add VAT number in business settings.');
            }

            // Check Business Address
            if (empty($business->address)) {
                $issues[] = __('Business address is missing. Please add address in business settings.');
            }

            // Check Business Company Name
            if (empty($business->companyName)) {
                $issues[] = __('Business company name is missing. Please add company name in business settings.');
            }

            // Check Sale UUID
            if (empty($sale->uuid)) {
                $issues[] = __('Invoice UUID is missing. This should be generated automatically.');
            }

            // Check Sale has details
            if ($sale->details->count() == 0) {
                $issues[] = __('Invoice has no items. Cannot report empty invoice to ZATCA.');
            }

            // Check Sale Date
            if (empty($sale->saleDate)) {
                $issues[] = __('Invoice date is missing.');
            }

            // Check VAT amount calculation
            if ($sale->totalAmount > 0 && $sale->vat_amount === null) {
                $issues[] = __('VAT amount is not calculated. Please ensure VAT is properly configured.');
            }

            // Check if sale has been returned completely
            if ($sale->details->sum('quantities') == 0) {
                $issues[] = __('Invoice has been completely returned. Cannot report returned invoice to ZATCA.');
            }

            // Check for validation errors from ZATCA response if exists
            if ($sale->zatca_response) {
                $response = is_string($sale->zatca_response) ? json_decode($sale->zatca_response, true) : $sale->zatca_response;

                if (isset($response['validationResults'])) {
                    if (isset($response['validationResults']['errorMessages']) && is_array($response['validationResults']['errorMessages'])) {
                        foreach ($response['validationResults']['errorMessages'] as $error) {
                            $issues[] = __('ZATCA Error') . ': ' . (is_string($error) ? $error : json_encode($error));
                        }
                    }
                    if (isset($response['validationResults']['warningMessages']) && is_array($response['validationResults']['warningMessages'])) {
                        foreach ($response['validationResults']['warningMessages'] as $warning) {
                            $issues[] = __('ZATCA Warning') . ': ' . (is_string($warning) ? $warning : json_encode($warning));
                        }
                    }
                }

                // Check for error messages in response
                if (isset($response['error'])) {
                    $issues[] = __('ZATCA Error') . ': ' . (is_string($response['error']) ? $response['error'] : json_encode($response['error']));
                }

                // Check for rejection reasons
                if (isset($response['reportedInvoiceIdentifier'])) {
                    // Invoice was reported but check for any issues
                }
            }

            $response = [
                'success' => true,
                'issues' => $issues,
                'has_issues' => count($issues) > 0,
                'zatca_status' => $sale->zatca_status ?? null,
                'invoice_number' => $sale->invoiceNumber ?? null
            ];

            Log::info('ZATCA Issues Response', ['sale_id' => $id, 'issues_count' => count($issues)]);

            return response()->json($response, 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            Log::error('ZATCA Issues Error: ' . $e->getMessage(), [
                'sale_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => __('Error loading issues: ') . $e->getMessage(),
                'issues' => [],
                'has_issues' => false
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Search for a sale by request/invoice number
     */
    public function searchByRequestNumber(Request $request)
    {
        $requestNumber = $request->get('request_number');
        
        if (!$requestNumber) {
            return response()->json([
                'success' => false,
                'message' => __('Request number is required')
            ]);
        }

        try {
            $sale = Sale::with(['party:id,name,phone,type', 'details.product', 'details.stock'])
                ->where('business_id', auth()->user()->business_id)
                ->where('invoiceNumber', $requestNumber)
                ->first();

            if (!$sale) {
                return response()->json([
                    'success' => false,
                    'message' => __('Request number not found')
                ]);
            }

            // Format the sale data for frontend
            $saleData = [
                'id' => $sale->id,
                'invoiceNumber' => $sale->invoiceNumber,
                'customer' => $sale->party ? [
                    'id' => $sale->party->id,
                    'name' => $sale->party->name,
                    'phone' => $sale->party->phone,
                    'type' => $sale->party->type
                ] : null,
                'items' => $sale->details->map(function ($detail) {
                    return [
                        'product_id' => $detail->product_id,
                        'product_name' => $detail->product->productName ?? '',
                        'quantity' => $detail->quantities,
                        'price' => $detail->price,
                        'discount' => $detail->discount ?? 0,
                        'stock_id' => $detail->stock_id
                    ];
                }),
                'discount_amount' => $sale->discountAmount ?? 0,
                'discount_type' => $sale->discount_type ?? 'flat',
                'vat_amount' => $sale->vat_amount ?? 0,
                'shipping_charge' => $sale->shipping_charge ?? 0,
                'total_amount' => $sale->totalAmount,
                'paid_amount' => $sale->paidAmount,
                'due_amount' => $sale->dueAmount,
                'sale_date' => $sale->saleDate
            ];

            return response()->json([
                'success' => true,
                'message' => __('Request found successfully'),
                'sale' => $saleData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error searching for request: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search products for barcode scanner
     */
    public function searchProducts(Request $request)
    {
        try {
            $query = $request->input('query');
            
            if (empty($query) || strlen($query) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => __('Search query must be at least 2 characters'),
                    'products' => []
                ]);
            }

            $business_id = auth()->user()->business_id;
            
            // Search products by name and code (barcode, qr_code, description columns don't exist in products table)
            $products = Product::with(['category:id,categoryName', 'stocks' => function($query) {
                    $query->where('productStock', '>', 0)->orderBy('created_at', 'desc');
                }])
                ->where('business_id', $business_id)
                ->where(function ($q) use ($query) {
                    $q->where('productName', 'like', '%' . $query . '%')
                      ->orWhere('productCode', 'like', '%' . $query . '%')
                      // Also search for exact matches
                      ->orWhere('productCode', '=', $query)
                      // Search in product ID
                      ->orWhere('id', '=', is_numeric($query) ? $query : 0);
                })
                ->limit(20)
                ->get();

            // If no products found with direct search, try to extract product info from QR code content
            if ($products->isEmpty() && $this->looksLikeQRCodeContent($query)) {
                $extractedInfo = $this->extractProductInfoFromQRCode($query);
                if ($extractedInfo) {
                    $products = Product::with(['category:id,categoryName', 'stocks' => function($query) {
                            $query->where('productStock', '>', 0)->orderBy('created_at', 'desc');
                        }])
                        ->where('business_id', $business_id)
                        ->where(function ($q) use ($extractedInfo) {
                            if (isset($extractedInfo['id'])) {
                                $q->orWhere('id', $extractedInfo['id']);
                            }
                            if (isset($extractedInfo['code'])) {
                                $q->orWhere('productCode', $extractedInfo['code']);
                            }
                            if (isset($extractedInfo['name'])) {
                                $q->orWhere('productName', 'like', '%' . $extractedInfo['name'] . '%');
                            }
                        })
                        ->limit(20)
                        ->get();
                }
            }

            // Format products for response
            $formattedProducts = $products->map(function ($product) {
                $stock = $product->stocks->first();
                $stockQuantity = $product->stocks->sum('productStock');
                
                // Format image path using asset() helper - return full URL
                $imagePath = $product->productPicture 
                    ? asset($product->productPicture)
                    : asset('assets/images/products/box.svg');
                
                return [
                    'id' => $product->id,
                    'productName' => $product->productName,
                    'name' => $product->productName, // Alias for compatibility
                    'productCode' => $product->productCode,
                    'code' => $product->productCode, // Alias for compatibility
                    'image' => $imagePath,
                    'sales_price' => $stock ? $stock->productSalePrice : $product->productSalePrice,
                    'price' => $stock ? $stock->productSalePrice : $product->productSalePrice, // Alias for compatibility
                    'stock_quantity' => $stockQuantity,
                    'stock_id' => $stock ? $stock->id : null,
                    'category' => $product->category ? [
                        'id' => $product->category->id,
                        'categoryName' => $product->category->categoryName,
                        'name' => $product->category->categoryName // Alias for compatibility
                    ] : null,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => __('Products found successfully'),
                'products' => $formattedProducts,
                'count' => $formattedProducts->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error searching products: ') . $e->getMessage(),
                'products' => []
            ], 500);
        }
    }

    /**
     * Check if the query looks like QR code content (URL, JSON, etc.)
     */
    private function looksLikeQRCodeContent($query)
    {
        // Check for common QR code patterns
        return (
            str_contains($query, 'http://') ||
            str_contains($query, 'https://') ||
            str_contains($query, 'www.') ||
            (str_starts_with($query, '{') && str_ends_with($query, '}')) ||
            str_contains($query, '|') ||
            str_contains($query, ';') ||
            strlen($query) > 50 // Long strings are often QR code content
        );
    }

    /**
     * Extract product information from QR code content
     */
    private function extractProductInfoFromQRCode($qrContent)
    {
        $info = [];
        
        try {
            // Try to parse as JSON
            $json = json_decode($qrContent, true);
            if ($json && is_array($json)) {
                if (isset($json['product_id'])) $info['id'] = $json['product_id'];
                if (isset($json['id'])) $info['id'] = $json['id'];
                if (isset($json['product_code'])) $info['code'] = $json['product_code'];
                if (isset($json['code'])) $info['code'] = $json['code'];
                if (isset($json['product_name'])) $info['name'] = $json['product_name'];
                if (isset($json['name'])) $info['name'] = $json['name'];
                return $info;
            }
        } catch (\Exception $e) {
            // Not JSON, continue with other parsing methods
        }

        // Try to parse pipe-separated values (common QR format)
        if (str_contains($qrContent, '|')) {
            $parts = explode('|', $qrContent);
            if (count($parts) >= 2) {
                $info['code'] = trim($parts[0]);
                $info['name'] = trim($parts[1]);
                if (count($parts) >= 3 && is_numeric($parts[2])) {
                    $info['id'] = intval($parts[2]);
                }
                return $info;
            }
        }

        // Try to parse semicolon-separated values
        if (str_contains($qrContent, ';')) {
            $parts = explode(';', $qrContent);
            if (count($parts) >= 2) {
                $info['code'] = trim($parts[0]);
                $info['name'] = trim($parts[1]);
                return $info;
            }
        }

        // Try to extract from URL parameters
        if (str_contains($qrContent, '?')) {
            $urlParts = parse_url($qrContent);
            if (isset($urlParts['query'])) {
                parse_str($urlParts['query'], $params);
                if (isset($params['product_id'])) $info['id'] = $params['product_id'];
                if (isset($params['id'])) $info['id'] = $params['id'];
                if (isset($params['code'])) $info['code'] = $params['code'];
                if (isset($params['name'])) $info['name'] = $params['name'];
                return $info;
            }
        }

        return null;
    }
}
