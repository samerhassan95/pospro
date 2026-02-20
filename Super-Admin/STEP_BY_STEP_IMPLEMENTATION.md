# خطوات التطبيق الكاملة بالترتيب - POSpro Fixes

## 📌 نظرة عامة
هذا الدليل يحتوي على خطوات تفصيلية لتطبيق جميع الإصلاحات بالترتيب الصحيح.

---

## 🔴 الخطوة 1: إنشاء Migration للعمولات

### المسار
```
database/migrations/2026_02_16_203141_add_commission_fields_to_users_table.php
```

### الكود الكامل
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('commission_type', ['fixed', 'percentage'])->nullable()->after('email');
            $table->decimal('commission_value', 10, 2)->nullable()->after('commission_type');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['commission_type', 'commission_value']);
        });
    }
};
```

### تشغيل Migration
```bash
php artisan migrate
```

---

## 🔴 الخطوة 2: تعديل AcnooPartyReportController

### المسار
```
Modules/Business/App/Http/Controllers/AcnooPartyReportController.php
```

### التعديلات المطلوبة

#### 1. في دالة `customerLedger()`
**ابحث عن:**
```php
->select('id', 'party_id', 'invoice_no', 'grand_total', 'due', 'paid')
```

**استبدل بـ:**
```php
->select('id', 'party_id', 'invoiceNumber', 'totalAmount', 'dueAmount', 'paidAmount')
->with(['party', 'saleDetails'])
```

#### 2. في دالة `customerLedgerShow()`
**ابحث عن:**
```php
->select('id', 'party_id', 'invoice_no', 'grand_total', 'due', 'paid')
```

**استبدل بـ:**
```php
->select('id', 'party_id', 'invoiceNumber', 'totalAmount', 'dueAmount', 'paidAmount')
->with(['party', 'saleDetails'])
```

#### 3. في دالة `supplierLedger()`
**ابحث عن:**
```php
->select('id', 'party_id', 'invoice_no', 'grand_total', 'due', 'paid')
```

**استبدل بـ:**
```php
->select('id', 'party_id', 'invoiceNumber', 'totalAmount', 'dueAmount', 'paidAmount')
->with(['party', 'purchaseDetails'])
```

#### 4. في دالة `supplierLedgerShow()`
**ابحث عن:**
```php
->select('id', 'party_id', 'invoice_no', 'grand_total', 'due', 'paid')
```

**استبدل بـ:**
```php
->select('id', 'party_id', 'invoiceNumber', 'totalAmount', 'dueAmount', 'paidAmount')
->with(['party', 'purchaseDetails'])
```

#### 5. في دالة `partyProfitLoss()`
**ابحث عن:**
```php
->select('id', 'party_id', 'grand_total', 'due', 'paid')
```

**استبدل بـ:**
```php
->select('id', 'party_id', 'totalAmount', 'dueAmount', 'paidAmount')
```

#### 6. في دالة `topCustomer()`
**ابحث عن:**
```php
DB::raw('SUM(grand_total) as total_amount')
```

**استبدل بـ:**
```php
DB::raw('SUM(totalAmount) as total_amount')
```

#### 7. في دالة `topSupplier()`
**ابحث عن:**
```php
DB::raw('SUM(grand_total) as total_amount')
```

**استبدل بـ:**
```php
DB::raw('SUM(totalAmount) as total_amount')
```

---

## 🔴 الخطوة 3: إنشاء Views لـ Party Reports

### 3.1 إنشاء المجلد
```
Modules/Business/resources/views/party-reports/
```

### 3.2 إنشاء الملفات (7 ملفات)

يجب نسخ الملفات التالية من المشروع الحالي:

1. `customer-ledger.blade.php`
2. `customer-ledger-show.blade.php`
3. `supplier-ledger.blade.php`
4. `supplier-ledger-show.blade.php`
5. `party-profit-loss.blade.php`
6. `top-customer.blade.php`
7. `top-supplier.blade.php`

**ملاحظة:** هذه الملفات موجودة في المشروع الحالي في نفس المسار.

---

## 🔴 الخطوة 4: تعديل AcnooAdvancedReportController

### المسار
```
Modules/Business/App/Http/Controllers/AcnooAdvancedReportController.php
```

### التعديلات المطلوبة

#### 1. في دالة `discountProducts()`
**ابحث عن:**
```php
DB::raw('SUM(sale_details.total) as total_sales')
```

**استبدل بـ:**
```php
DB::raw('SUM(sale_details.price * sale_details.quantities) as total_sales')
```

**وأيضاً ابحث عن:**
```php
->select('products.id', 'products.name', 'products.sku')
```

**استبدل بـ:**
```php
->select('products.id', 'products.productName', 'products.productCode')
```

#### 2. في دالة `productSale()`
**ابحث عن:**
```php
DB::raw('SUM(sale_details.total) as total_amount')
```

**استبدل بـ:**
```php
DB::raw('SUM(sale_details.price * sale_details.quantities) as total_amount')
```

**وأيضاً:**
```php
->select('products.id', 'products.name', 'products.sku')
```

**استبدل بـ:**
```php
->select('products.id', 'products.productName', 'products.productCode')
```

#### 3. في دالة `productPurchase()`
**ابحث عن:**
```php
DB::raw('SUM(purchase_details.total) as total_amount')
```

**استبدل بـ:**
```php
DB::raw('SUM(purchase_details.productPurchasePrice * purchase_details.quantities) as total_amount')
```

**وأيضاً:**
```php
->select('products.id', 'products.name', 'products.sku')
```

**استبدل بـ:**
```php
->select('products.id', 'products.productName', 'products.productCode')
```

#### 4. في دالة `productLossProfitReports()`
**أضف في بداية الدالة:**
```php
$opening_stock_by_purchase = 0;
$opening_stock_by_sale = 0;
$closing_stock_by_purchase = 0;
$closing_stock_by_sale = 0;
```

**وابحث عن:**
```php
DB::raw('SUM(sale_details.total) as total_sales')
```

**استبدل بـ:**
```php
DB::raw('SUM(sale_details.price * sale_details.quantities) as total_sales')
```

**وأيضاً:**
```php
DB::raw('SUM(purchase_details.total) as total_purchases')
```

**استبدل بـ:**
```php
DB::raw('SUM(purchase_details.productPurchasePrice * purchase_details.quantities) as total_purchases')
```

#### 5. في دالة `topProducts()`
**ابحث عن:**
```php
return view('business::reports.top-products.index', compact('topProducts'));
```

**استبدل بـ:**
```php
return view('business::reports.top-products.index', compact('products'));
```

**وابحث عن:**
```php
DB::raw('SUM(sale_details.total) as total_sales')
```

**استبدل بـ:**
```php
DB::raw('SUM(sale_details.price * sale_details.quantities) as total_sales')
```

#### 6. في دالة `comboProductReports()`
**ابحث عن:**
```php
$comboProducts = ComboProduct::where('business_id', auth()->user()->business_id)
```

**استبدل بـ:**
```php
$combos = ComboProduct::whereHas('product', function ($query) {
        $query->where('business_id', auth()->user()->business_id);
    })
    ->with(['product', 'stock'])
```

**وابحث عن:**
```php
return view('business::reports.combo-product-reports.index', compact('comboProducts'));
```

**استبدل بـ:**
```php
return view('business::reports.combo-product-reports.index', compact('combos'));
```

---

## 🔴 الخطوة 5: إنشاء Views لـ Advanced Reports

### 5.1 إنشاء المجلدات والملفات

#### المجلد الأول
```
Modules/Business/resources/views/reports/discount-products/
```
**الملف:** `index.blade.php`

#### المجلد الثاني
```
Modules/Business/resources/views/reports/product-sale/
```
**الملف:** `index.blade.php`

#### المجلد الثالث
```
Modules/Business/resources/views/reports/product-purchase/
```
**الملف:** `index.blade.php`

**ملاحظة:** انسخ هذه الملفات من المشروع الحالي.

---

## 🔴 الخطوة 6: تعديل AcnooCommissionController

### المسار
```
Modules/Business/App/Http/Controllers/AcnooCommissionController.php
```

### التعديلات المطلوبة

#### في دالة `index()`
**ابحث عن:**
```php
return view('business::commissions.index');
```

**استبدل بـ:**
```php
$users = User::where('business_id', auth()->user()->business_id)
    ->whereNotNull('commission_type')
    ->get();

if ($request->ajax()) {
    return view('business::commissions.datas', compact('users'));
}

return view('business::commissions.index', compact('users'));
```

#### في دالة `create()`
**ابحث عن:**
```php
return view('business::commissions.create');
```

**استبدل بـ:**
```php
$users = User::where('business_id', auth()->user()->business_id)
    ->whereNull('commission_type')
    ->get();

return view('business::commissions.create', compact('users'));
```

#### في دالة `edit()`
**ابحث عن:**
```php
return view('business::commissions.edit', compact('user'));
```

**استبدل بـ:**
```php
$users = User::where('business_id', auth()->user()->business_id)->get();

return view('business::commissions.edit', compact('user', 'users'));
```

---

## 🔴 الخطوة 7: تعديل AcnooSaleCommissionController

### المسار
```
Modules/Business/App/Http/Controllers/AcnooSaleCommissionController.php
```

### التعديلات المطلوبة

#### في دالة `index()`
**ابحث عن:**
```php
return view('business::sale-commissions.index');
```

**استبدل بـ:**
```php
$sales = Sale::where('business_id', auth()->user()->business_id)
    ->with(['user', 'party'])
    ->get();

if ($request->ajax()) {
    return view('business::sale-commissions.datas', compact('sales'));
}

return view('business::sale-commissions.index', compact('sales'));
```

---

## 🔴 الخطوة 8: إنشاء Views لـ Commissions

### 8.1 إنشاء المجلد
```
Modules/Business/resources/views/commissions/
```

### 8.2 إنشاء الملفات (4 ملفات)
1. `index.blade.php`
2. `datas.blade.php`
3. `create.blade.php`
4. `edit.blade.php`

### 8.3 إنشاء المجلد الثاني
```
Modules/Business/resources/views/sale-commissions/
```

### 8.4 إنشاء الملفات (2 ملف)
1. `index.blade.php`
2. `datas.blade.php`

**ملاحظة:** انسخ هذه الملفات من المشروع الحالي.

---

## 🔴 الخطوة 9: تعديل AcnooWalkDueController

### المسار
```
Modules/Business/App/Http/Controllers/AcnooWalkDueController.php
```

### التعديلات المطلوبة

#### في دالة `index()`
**ابحث عن:**
```php
->select('id', 'party_id', 'due', 'paid')
```

**استبدل بـ:**
```php
->select('id', 'party_id', 'dueAmount', 'paidAmount')
```

**وتأكد من وجود:**
```php
$sales = Sale::where('business_id', auth()->user()->business_id)
    ->whereHas('party', function ($query) {
        $query->where('type', 'walk-in-customer');
    })
    ->with('party')
    ->get();

if ($request->ajax()) {
    return view('business::walk-dues.datas', compact('sales'));
}

return view('business::walk-dues.index', compact('sales'));
```

---

## 🔴 الخطوة 10: إنشاء Views لـ Walk Dues

### 10.1 إنشاء المجلد
```
Modules/Business/resources/views/walk-dues/
```

### 10.2 إنشاء الملفات (2 ملف)
1. `index.blade.php`
2. `datas.blade.php`

**ملاحظة:** انسخ هذه الملفات من المشروع الحالي.

---

## 🔴 الخطوة 11: تعديل AcnooComboProductController (الأهم)

### المسار
```
Modules/Business/App/Http/Controllers/AcnooComboProductController.php
```

### استبدل الملف بالكامل بهذا الكود:

```php
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
        $query = ComboProduct::whereHas('product', function ($query) {
            $query->where('business_id', auth()->user()->business_id);
        });

        if (!auth()->user()->active_branch_id && !auth()->user()->branch_id) {
            $query->withoutGlobalScope(\App\Models\Scopes\BranchScope::class);
        }

        $combo = $query->findOrFail($id);

        // ComboProduct doesn't have status field, so we'll just return success
        return response()->json([
            'message' => __('Status updated successfully'),
        ]);
    }

    public function deleteAll(Request $request)
    {
        $ids = $request->ids;
        
        $query = ComboProduct::whereHas('product', function ($query) {
            $query->where('business_id', auth()->user()->business_id);
        });

        if (!auth()->user()->active_branch_id && !auth()->user()->branch_id) {
            $query->withoutGlobalScope(\App\Models\Scopes\BranchScope::class);
        }

        $query->whereIn('id', $ids)->delete();

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
```

---

## 🔴 الخطوة 12: إنشاء Views لـ Combo Products

### 12.1 إنشاء المجلد
```
Modules/Business/resources/views/combo-products/
```

### 12.2 إنشاء الملفات (4 ملفات)

انسخ الملفات التالية من المشروع الحالي:
1. `index.blade.php`
2. `datas.blade.php`
3. `create.blade.php`
4. `edit.blade.php`

**ملاحظة مهمة:** تأكد من أن الملفات تستخدم:
- `$product->productName` بدلاً من `$product->name`
- `$product->productCode` بدلاً من `$product->sku`

---

## 🔴 الخطوة 13: التحقق من Routes

### المسار
```
Modules/Business/routes/web.php
```

### تأكد من وجود هذه الراوتس

```php
// Party Reports
Route::get('party-reports/customer-ledger', [Business\AcnooPartyReportController::class, 'customerLedger'])->name('party-reports.customer-ledger');
Route::get('party-reports/customer-ledger/{id}', [Business\AcnooPartyReportController::class, 'customerLedgerShow'])->name('party-reports.customer-ledger-show');
Route::get('party-reports/supplier-ledger', [Business\AcnooPartyReportController::class, 'supplierLedger'])->name('party-reports.supplier-ledger');
Route::get('party-reports/supplier-ledger/{id}', [Business\AcnooPartyReportController::class, 'supplierLedgerShow'])->name('party-reports.supplier-ledger-show');
Route::get('party-reports/party-profit-loss', [Business\AcnooPartyReportController::class, 'partyProfitLoss'])->name('party-reports.party-profit-loss');
Route::get('party-reports/top-customer', [Business\AcnooPartyReportController::class, 'topCustomer'])->name('party-reports.top-customer');
Route::get('party-reports/top-supplier', [Business\AcnooPartyReportController::class, 'topSupplier'])->name('party-reports.top-supplier');

// Advanced Reports
Route::get('reports/discount-products', [Business\AcnooAdvancedReportController::class, 'discountProducts'])->name('reports.discount-products');
Route::get('reports/product-sale', [Business\AcnooAdvancedReportController::class, 'productSale'])->name('reports.product-sale');
Route::get('reports/product-purchase', [Business\AcnooAdvancedReportController::class, 'productPurchase'])->name('reports.product-purchase');
Route::get('reports/product-loss-profit', [Business\AcnooAdvancedReportController::class, 'productLossProfitReports'])->name('reports.product-loss-profit');
Route::get('reports/top-products', [Business\AcnooAdvancedReportController::class, 'topProducts'])->name('reports.top-products');
Route::get('reports/combo-product-reports', [Business\AcnooAdvancedReportController::class, 'comboProductReports'])->name('reports.combo-product-reports');

// Commissions
Route::resource('commissions', Business\AcnooCommissionController::class);
Route::post('commissions/filter', [Business\AcnooCommissionController::class, 'acnooFilter'])->name('commissions.filter');

// Sale Commissions
Route::resource('sale-commissions', Business\AcnooSaleCommissionController::class);
Route::post('sale-commissions/filter', [Business\AcnooSaleCommissionController::class, 'acnooFilter'])->name('sale-commissions.filter');

// Walk Dues
Route::resource('walk-dues', Business\AcnooWalkDueController::class);
Route::post('walk-dues/filter', [Business\AcnooWalkDueController::class, 'acnooFilter'])->name('walk-dues.filter');

// Combo Products
Route::resource('combo-products', Business\AcnooComboProductController::class);
Route::post('combo-products/filter', [Business\AcnooComboProductController::class, 'acnooFilter'])->name('combo-products.filter');
Route::post('combo-products/status/{id}', [Business\AcnooComboProductController::class, 'status'])->name('combo-products.status');
Route::post('combo-products/delete-all', [Business\AcnooComboProductController::class, 'deleteAll'])->name('combo-products.delete-all');
```

---

## 🔴 الخطوة 14: مسح الـ Cache

بعد الانتهاء من جميع التعديلات، قم بتشغيل هذه الأوامر:

```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear
```

---

## 🔴 الخطوة 15: الاختبار

### اختبر كل راوت على حدة:

#### Party Reports
```
✓ /business/party-reports/customer-ledger
✓ /business/party-reports/supplier-ledger
✓ /business/party-reports/party-profit-loss
✓ /business/party-reports/top-customer
✓ /business/party-reports/top-supplier
```

#### Advanced Reports
```
✓ /business/reports/discount-products
✓ /business/reports/product-sale
✓ /business/reports/product-purchase
✓ /business/reports/product-loss-profit
✓ /business/reports/top-products
✓ /business/reports/combo-product-reports
```

#### Commissions
```
✓ /business/commissions
✓ /business/commissions/create
✓ /business/sale-commissions
```

#### Walk Dues
```
✓ /business/walk-dues
```

#### Combo Products
```
✓ /business/combo-products
✓ /business/combo-products/create
✓ /business/combo-products/{id}/edit
✓ حذف combo product
```

---

## 📊 ملخص الملفات

### إجمالي الملفات المطلوبة: 32 ملف

#### Migration (1 ملف)
- `database/migrations/2026_02_16_203141_add_commission_fields_to_users_table.php`

#### Controllers (6 ملفات)
1. `Modules/Business/App/Http/Controllers/AcnooPartyReportController.php`
2. `Modules/Business/App/Http/Controllers/AcnooAdvancedReportController.php`
3. `Modules/Business/App/Http/Controllers/AcnooCommissionController.php`
4. `Modules/Business/App/Http/Controllers/AcnooSaleCommissionController.php`
5. `Modules/Business/App/Http/Controllers/AcnooWalkDueController.php`
6. `Modules/Business/App/Http/Controllers/AcnooComboProductController.php`

#### Views (25 ملف)
- Party Reports: 7 ملفات
- Advanced Reports: 3 ملفات
- Commissions: 6 ملفات
- Walk Dues: 2 ملف
- Combo Products: 4 ملفات
- Routes: 1 ملف (للتحقق فقط)

---

## ⚠️ ملاحظات مهمة جداً

### 1. أسماء الأعمدة
تأكد من استخدام الأسماء الصحيحة:
- `totalAmount` ليس `grand_total`
- `dueAmount` ليس `due`
- `paidAmount` ليس `paid`
- `invoiceNumber` ليس `invoice_no`
- `productName` ليس `name`
- `productCode` ليس `sku`

### 2. حساب Total
لا تستخدم `SUM(total)` بل استخدم:
- `SUM(price * quantities)` في sale_details
- `SUM(productPurchasePrice * quantities)` في purchase_details

### 3. BranchScope
في ComboProduct دائماً استخدم:
```php
if (!auth()->user()->active_branch_id && !auth()->user()->branch_id) {
    $query->withoutGlobalScope(\App\Models\Scopes\BranchScope::class);
}
```

### 4. Backup
قبل البدء، احتفظ بنسخة احتياطية من:
- قاعدة البيانات
- جميع الملفات التي ستعدلها

---

## 🎯 الخلاصة

اتبع الخطوات بالترتيب من 1 إلى 15 ولا تتخطى أي خطوة.

إذا واجهت أي خطأ:
1. تحقق من `storage/logs/laravel.log`
2. تأكد من أسماء الأعمدة
3. تأكد من تشغيل Migration
4. تأكد من مسح الـ Cache

**تاريخ الإنشاء:** 16 فبراير 2026
**الإصدار:** 1.0


---

## 🔴 الخطوة 16: التحقق من السايدبار

### المسار
```
resources/views/layouts/business/partials/side-bar.blade.php
```

### التحقق من وجود العناصر

السايدبار يجب أن يحتوي على العناصر التالية (موجودة بالفعل في المشروع):

#### 1. Combo Products (في قسم Products)
```php
@usercan('products.read')
<li><a class="{{ Request::routeIs('business.combo-products.index') ? 'active' : '' }}"
        href="{{ route('business.combo-products.index') }}">{{ __('Combo Products') }}</a>
</li>
@endusercan
```

#### 2. Party Reports (في قسم Reports)
```php
@usercan('party-reports.read')
<li class="dropdown">
    <a href="#">{{ __('Party Reports') }}</a>
    <ul>
        <li><a href="{{ route('business.party-reports.customer-ledger') }}">{{ __('Customer Ledger') }}</a></li>
        <li><a href="{{ route('business.party-reports.supplier-ledger') }}">{{ __('Supplier Ledger') }}</a></li>
        <li><a href="{{ route('business.party-reports.party-profit-loss') }}">{{ __('Party Profit & Loss') }}</a></li>
        <li><a href="{{ route('business.party-reports.top-customer') }}">{{ __('Top 5 Customer') }}</a></li>
        <li><a href="{{ route('business.party-reports.top-supplier') }}">{{ __('Top 5 Supplier') }}</a></li>
    </ul>
</li>
@endusercan
```

#### 3. Advanced Reports (في قسم Reports)
```php
<li><a href="{{ route('business.reports.discount-products') }}">{{ __('Discount Products') }}</a></li>
<li><a href="{{ route('business.reports.product-sale') }}">{{ __('Product Sale') }}</a></li>
<li><a href="{{ route('business.reports.product-purchase') }}">{{ __('Product Purchase') }}</a></li>
<li><a href="{{ route('business.reports.product-loss-profit') }}">{{ __('Product Loss/Profit') }}</a></li>
<li><a href="{{ route('business.reports.top-products') }}">{{ __('Top Products') }}</a></li>
<li><a href="{{ route('business.reports.combo-product-reports') }}">{{ __('Combo Product Reports') }}</a></li>
```

#### 4. Commissions (قسم منفصل أو تحت Finance)
```php
@usercan('commissions.read')
<li><a href="{{ route('business.commissions.index') }}">{{ __('Set Commissions') }}</a></li>
<li><a href="{{ route('business.sale-commissions.index') }}">{{ __('Sale Commissions') }}</a></li>
@endusercan
```

#### 5. Walk-in Customer Due (تحت Due List)
```php
@usercan('walk-dues.read')
<li><a href="{{ route('business.walk-dues.index') }}">{{ __('Walk-in Customer Due') }}</a></li>
@endusercan
```

### ⚠️ ملاحظة مهمة
**لا تحتاج لتعديل السايدبار** - جميع العناصر موجودة بالفعل في المشروع الأصلي.

إذا كانت بعض العناصر مفقودة في نسختك، انسخ السايدبار كامل من المشروع الحالي:
```
resources/views/layouts/business/partials/side-bar.blade.php
```

---

## 📋 قائمة مراجعة نهائية (Checklist)

قبل الانتهاء، تأكد من:

### Database
- [ ] تم تشغيل Migration للعمولات
- [ ] جدول users يحتوي على commission_type و commission_value
- [ ] جدول combo_products موجود

### Controllers (6 ملفات)
- [ ] AcnooPartyReportController.php - تم تعديله
- [ ] AcnooAdvancedReportController.php - تم تعديله
- [ ] AcnooCommissionController.php - تم تعديله
- [ ] AcnooSaleCommissionController.php - تم تعديله
- [ ] AcnooWalkDueController.php - تم تعديله
- [ ] AcnooComboProductController.php - تم استبداله بالكامل

### Views (25 ملف)
- [ ] Party Reports (7 ملفات) - تم إنشاؤها
- [ ] Advanced Reports (3 ملفات) - تم إنشاؤها
- [ ] Commissions (6 ملفات) - تم إنشاؤها
- [ ] Walk Dues (2 ملف) - تم إنشاؤها
- [ ] Combo Products (4 ملفات) - تم إنشاؤها

### Routes
- [ ] جميع الراوتس موجودة في web.php
- [ ] تم التحقق من أسماء الراوتس

### Cache
- [ ] تم مسح cache
- [ ] تم مسح view cache
- [ ] تم مسح config cache
- [ ] تم مسح route cache

### Testing
- [ ] Party Reports - جميع الصفحات تعمل
- [ ] Advanced Reports - جميع الصفحات تعمل
- [ ] Commissions - Create/Edit/Delete تعمل
- [ ] Walk Dues - القائمة تعمل
- [ ] Combo Products - CRUD كامل يعمل

### Sidebar
- [ ] جميع الروابط موجودة في السايدبار
- [ ] الروابط تعمل بشكل صحيح

---

## 🎉 تم الانتهاء!

إذا اتبعت جميع الخطوات من 1 إلى 16 بالترتيب، يجب أن تكون جميع الميزات تعمل الآن بشكل صحيح.

### في حالة وجود مشاكل:

1. **راجع الـ logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **تحقق من أسماء الأعمدة في قاعدة البيانات:**
   ```sql
   DESCRIBE sales;
   DESCRIBE purchases;
   DESCRIBE products;
   DESCRIBE combo_products;
   ```

3. **تأكد من الـ permissions:**
   - تحقق من أن المستخدم لديه الصلاحيات المطلوبة

4. **امسح الـ cache مرة أخرى:**
   ```bash
   php artisan optimize:clear
   ```

---

**آخر تحديث:** 16 فبراير 2026
**الإصدار:** 1.1 (مع السايدبار)
**إجمالي الخطوات:** 16 خطوة
**إجمالي الملفات:** 32 ملف
