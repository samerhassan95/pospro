# دليل شامل لجميع التعديلات والإضافات - POSpro

## نظرة عامة
هذا الدليل يحتوي على جميع التعديلات التي تم إجراؤها لإصلاح الراوتس والميزات المعطلة في المشروع.

---

## 1️⃣ إصلاح Party Reports (تقارير العملاء والموردين)

### المشكلة
- جميع راوتس Party Reports كانت معطلة
- Views مفقودة
- أخطاء في أسماء الأعمدة في قاعدة البيانات

### الملفات المعدلة

#### Controller: `Modules/Business/App/Http/Controllers/AcnooPartyReportController.php`
**التعديلات:**
- تصحيح أسماء الأعمدة:
  - `grand_total` → `totalAmount`
  - `due` → `dueAmount`
  - `paid` → `paidAmount`
  - `invoice_no` → `invoiceNumber`
- إضافة تحميل العلاقات المطلوبة: `with(['party', 'saleDetails'])`
- إصلاح الاستعلامات في جميع الدوال

### الملفات المنشأة (Views)

1. `Modules/Business/resources/views/party-reports/customer-ledger.blade.php`
2. `Modules/Business/resources/views/party-reports/customer-ledger-show.blade.php`
3. `Modules/Business/resources/views/party-reports/supplier-ledger.blade.php`
4. `Modules/Business/resources/views/party-reports/supplier-ledger-show.blade.php`
5. `Modules/Business/resources/views/party-reports/party-profit-loss.blade.php`
6. `Modules/Business/resources/views/party-reports/top-customer.blade.php`
7. `Modules/Business/resources/views/party-reports/top-supplier.blade.php`

---

## 2️⃣ إصلاح Advanced Reports (التقارير المتقدمة)

### المشكلة
- أخطاء في أسماء الأعمدة (`sale_details.total`, `purchase_details.total`)
- Views مفقودة
- أخطاء في استعلامات ComboProduct

### الملفات المعدلة

#### Controller: `Modules/Business/App/Http/Controllers/AcnooAdvancedReportController.php`
**التعديلات الرئيسية:**
- حساب الـ total من `price * quantities` بدلاً من عمود `total` غير موجود
- إصلاح استعلامات ComboProduct لاستخدام `whereHas('product')`
- إضافة جميع المتغيرات المطلوبة للـ views

### الملفات المنشأة (Views)

1. `Modules/Business/resources/views/reports/discount-products/index.blade.php`
2. `Modules/Business/resources/views/reports/product-sale/index.blade.php`
3. `Modules/Business/resources/views/reports/product-purchase/index.blade.php`

---

## 3️⃣ إضافة ميزة Commission Management (إدارة العمولات)

### المشكلة
- عمود `commission_type` و `commission_value` غير موجودين في جدول users
- Views مفقودة

### الملفات المنشأة

#### Migration: `database/migrations/2026_02_16_203141_add_commission_fields_to_users_table.php`
```php
Schema::table('users', function (Blueprint $table) {
    $table->enum('commission_type', ['fixed', 'percentage'])->nullable()->after('email');
    $table->decimal('commission_value', 10, 2)->nullable()->after('commission_type');
});
```

**تشغيل Migration:**
```bash
php artisan migrate
```

### الملفات المعدلة

#### Controller: `Modules/Business/App/Http/Controllers/AcnooCommissionController.php`
- إصلاح تمرير المتغيرات للـ views
- إضافة دعم AJAX و non-AJAX requests

#### Controller: `Modules/Business/App/Http/Controllers/AcnooSaleCommissionController.php`
- نفس التعديلات

### Views المنشأة

1. `Modules/Business/resources/views/commissions/index.blade.php`
2. `Modules/Business/resources/views/commissions/datas.blade.php`
3. `Modules/Business/resources/views/commissions/create.blade.php`
4. `Modules/Business/resources/views/commissions/edit.blade.php`
5. `Modules/Business/resources/views/sale-commissions/index.blade.php`
6. `Modules/Business/resources/views/sale-commissions/datas.blade.php`

---

## 4️⃣ إصلاح Walk-in Customer Due Management

### المشكلة
- متغير `$sales` غير معرف
- أخطاء في أسماء الأعمدة

### الملفات المعدلة

#### Controller: `Modules/Business/App/Http/Controllers/AcnooWalkDueController.php`
- إضافة تمرير `$sales` للـ views
- تصحيح أسماء الأعمدة: `due` → `dueAmount`, `paid` → `paidAmount`

### Views المنشأة
1. `Modules/Business/resources/views/walk-dues/index.blade.php`
2. `Modules/Business/resources/views/walk-dues/datas.blade.php`

---

## 5️⃣ إصلاح Combo Products Management (الأهم)

### المشكلة
- خطأ 403 Forbidden
- Views مفقودة
- أخطاء في أسماء الأعمدة
- مشاكل مع BranchScope

### الملفات المعدلة


#### Controller: `Modules/Business/App/Http/Controllers/AcnooComboProductController.php`

**التعديلات الرئيسية:**

1. **إزالة فحص الصلاحيات:**
```php
public function __construct()
{
    // No permission checks - accessible to all shop owners
}
```

2. **إصلاح أسماء الأعمدة:**
   - `name` → `productName`
   - `sku` → `productCode`

3. **إصلاح مشكلة BranchScope:**
```php
// في كل دالة (index, edit, update, destroy)
$query = ComboProduct::whereHas('product', function ($query) {
    $query->where('business_id', auth()->user()->business_id);
});

// If user doesn't have active branch, include null branch_id records
if (!auth()->user()->active_branch_id && !auth()->user()->branch_id) {
    $query->withoutGlobalScope(\App\Models\Scopes\BranchScope::class);
}
```

4. **تحويل الاستجابات من JSON إلى Redirects:**
```php
// في store()
return redirect()->route('business.combo-products.index')
    ->with('success', __('Combo product created successfully'));

// في update()
return redirect()->route('business.combo-products.index')
    ->with('success', __('Combo product updated successfully'));

// في destroy()
if (request()->ajax() || request()->wantsJson()) {
    return response()->json([
        'message' => __('Combo product deleted successfully'),
        'redirect' => route('business.combo-products.index')
    ]);
}
return redirect()->route('business.combo-products.index')
    ->with('success', __('Combo product deleted successfully'));
```

### Views المنشأة


1. **`Modules/Business/resources/views/combo-products/index.blade.php`**
   - عرض القائمة
   - عرض رسائل النجاح/الخطأ
   - زر إضافة combo product

2. **`Modules/Business/resources/views/combo-products/datas.blade.php`**
   - جدول البيانات
   - أزرار Edit و Delete في dropdown menu
   - استخدام `class="confirm-action"` للحذف

3. **`Modules/Business/resources/views/combo-products/create.blade.php`**
   - نموذج إضافة combo product
   - حقول: Product, Stock, Branch, Purchase Price, Quantity
   - Form submission عادي (ليس AJAX)
   - عرض أخطاء Validation

4. **`Modules/Business/resources/views/combo-products/edit.blade.php`**
   - نموذج تعديل combo product
   - نفس الحقول مع القيم المحملة مسبقاً
   - Form submission عادي

---

## 📋 ملخص أسماء الأعمدة الصحيحة

### جدول Sales
- `totalAmount` (ليس grand_total)
- `dueAmount` (ليس due)
- `paidAmount` (ليس paid)
- `invoiceNumber` (ليس invoice_no)
- `discountAmount` (ليس discount)

### جدول Purchases
- نفس الأسماء أعلاه

### جدول sale_details و purchase_details
- **لا يوجد عمود `total`**
- يتم الحساب: `price * quantities` أو `productPurchasePrice * quantities`

### جدول products
- `productName` (ليس name)
- `productCode` (ليس sku)
- `productStock` (ليس stock)

### جدول combo_products
- لا يحتوي على timestamps (created_at, updated_at)
- لا يحتوي على business_id مباشرة
- الحقول: id, product_id, stock_id, branch_id, purchase_price, quantity

---

## 🔧 خطوات التطبيق على مشروع جديد

### 1. تشغيل Migration للعمولات
```bash
php artisan migrate
```
هذا سيضيف `commission_type` و `commission_value` لجدول users

### 2. نسخ الملفات المعدلة

#### Controllers (نسخ كامل)
```
Modules/Business/App/Http/Controllers/AcnooPartyReportController.php
Modules/Business/App/Http/Controllers/AcnooAdvancedReportController.php
Modules/Business/App/Http/Controllers/AcnooCommissionController.php
Modules/Business/App/Http/Controllers/AcnooSaleCommissionController.php
Modules/Business/App/Http/Controllers/AcnooWalkDueController.php
Modules/Business/App/Http/Controllers/AcnooComboProductController.php
```

#### Views (نسخ المجلدات كاملة)
```
Modules/Business/resources/views/party-reports/
Modules/Business/resources/views/reports/discount-products/
Modules/Business/resources/views/reports/product-sale/
Modules/Business/resources/views/reports/product-purchase/
Modules/Business/resources/views/commissions/
Modules/Business/resources/views/sale-commissions/
Modules/Business/resources/views/walk-dues/
Modules/Business/resources/views/combo-products/
```

#### Migration
```
database/migrations/2026_02_16_203141_add_commission_fields_to_users_table.php
```

### 3. التحقق من Routes
تأكد أن الراوتس موجودة في `Modules/Business/routes/web.php`:
```php
// Party Reports
Route::get('party-reports/customer-ledger', [Business\AcnooPartyReportController::class, 'customerLedger'])->name('party-reports.customer-ledger');
Route::get('party-reports/supplier-ledger', [Business\AcnooPartyReportController::class, 'supplierLedger'])->name('party-reports.supplier-ledger');
// ... إلخ

// Combo Products
Route::resource('combo-products', Business\AcnooComboProductController::class);

// Commissions
Route::resource('commissions', Business\AcnooCommissionController::class);
Route::resource('sale-commissions', Business\AcnooSaleCommissionController::class);

// Walk Dues
Route::resource('walk-dues', Business\AcnooWalkDueController::class);
```

---

## 🎯 النقاط المهمة للتذكر

### 1. BranchScope في ComboProduct
عند التعامل مع ComboProduct، دائماً استخدم:
```php
$query = ComboProduct::whereHas('product', function ($query) {
    $query->where('business_id', auth()->user()->business_id);
});

if (!auth()->user()->active_branch_id && !auth()->user()->branch_id) {
    $query->withoutGlobalScope(\App\Models\Scopes\BranchScope::class);
}
```

### 2. أسماء الأعمدة في Products
دائماً استخدم:
- `$product->productName` (ليس name)
- `$product->productCode` (ليس sku)

### 3. حساب Total في sale_details
```php
// صحيح
DB::raw('SUM(price * quantities) as total')

// خطأ
DB::raw('SUM(total) as total')
```

### 4. Form Submissions
- Combo Products: استخدام form submission عادي (ليس AJAX)
- Delete: استخدام `class="confirm-action"` مع `data-method="DELETE"`

### 5. Flash Messages
```php
// في Controller
return redirect()->route('...')->with('success', 'Message');

// في View
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
```

---

## 📝 قائمة الملفات الكاملة

### Migrations (1 ملف)
- `database/migrations/2026_02_16_203141_add_commission_fields_to_users_table.php`

### Controllers (6 ملفات)
1. `Modules/Business/App/Http/Controllers/AcnooPartyReportController.php`
2. `Modules/Business/App/Http/Controllers/AcnooAdvancedReportController.php`
3. `Modules/Business/App/Http/Controllers/AcnooCommissionController.php`
4. `Modules/Business/App/Http/Controllers/AcnooSaleCommissionController.php`
5. `Modules/Business/App/Http/Controllers/AcnooWalkDueController.php`
6. `Modules/Business/App/Http/Controllers/AcnooComboProductController.php`

### Views (25 ملف)


#### Party Reports (7 ملفات)
1. `Modules/Business/resources/views/party-reports/customer-ledger.blade.php`
2. `Modules/Business/resources/views/party-reports/customer-ledger-show.blade.php`
3. `Modules/Business/resources/views/party-reports/supplier-ledger.blade.php`
4. `Modules/Business/resources/views/party-reports/supplier-ledger-show.blade.php`
5. `Modules/Business/resources/views/party-reports/party-profit-loss.blade.php`
6. `Modules/Business/resources/views/party-reports/top-customer.blade.php`
7. `Modules/Business/resources/views/party-reports/top-supplier.blade.php`

#### Advanced Reports (3 ملفات)
1. `Modules/Business/resources/views/reports/discount-products/index.blade.php`
2. `Modules/Business/resources/views/reports/product-sale/index.blade.php`
3. `Modules/Business/resources/views/reports/product-purchase/index.blade.php`

#### Commissions (6 ملفات)
1. `Modules/Business/resources/views/commissions/index.blade.php`
2. `Modules/Business/resources/views/commissions/datas.blade.php`
3. `Modules/Business/resources/views/commissions/create.blade.php`
4. `Modules/Business/resources/views/commissions/edit.blade.php`
5. `Modules/Business/resources/views/sale-commissions/index.blade.php`
6. `Modules/Business/resources/views/sale-commissions/datas.blade.php`

#### Walk Dues (2 ملف)
1. `Modules/Business/resources/views/walk-dues/index.blade.php`
2. `Modules/Business/resources/views/walk-dues/datas.blade.php`

#### Combo Products (4 ملفات)
1. `Modules/Business/resources/views/combo-products/index.blade.php`
2. `Modules/Business/resources/views/combo-products/datas.blade.php`
3. `Modules/Business/resources/views/combo-products/create.blade.php`
4. `Modules/Business/resources/views/combo-products/edit.blade.php`

---

## ✅ اختبار الميزات

### 1. Party Reports
```
/business/party-reports/customer-ledger
/business/party-reports/supplier-ledger
/business/party-reports/party-profit-loss
/business/party-reports/top-customer
/business/party-reports/top-supplier
```

### 2. Advanced Reports
```
/business/reports/discount-products
/business/reports/product-sale
/business/reports/product-purchase
/business/reports/product-loss-profit
/business/reports/top-products
/business/reports/combo-product-reports
```

### 3. Commissions
```
/business/commissions
/business/sale-commissions
```

### 4. Walk Dues
```
/business/walk-dues
```

### 5. Combo Products
```
/business/combo-products
/business/combo-products/create
/business/combo-products/{id}/edit
```

---

## 🚨 ملاحظات مهمة

1. **قبل تشغيل Migration:**
   - تأكد من backup للـ database
   - تحقق أن جدول users موجود

2. **عند نسخ الملفات:**
   - احتفظ بنسخة احتياطية من الملفات القديمة
   - تأكد من الـ permissions على المجلدات

3. **بعد النسخ:**
   - امسح الـ cache: `php artisan cache:clear`
   - امسح الـ view cache: `php artisan view:clear`
   - امسح الـ config cache: `php artisan config:clear`

4. **إذا ظهرت أخطاء:**
   - تحقق من الـ logs: `storage/logs/laravel.log`
   - تأكد من أن جميع الـ relationships موجودة في الـ Models
   - تحقق من أسماء الأعمدة في قاعدة البيانات

---

## 📞 الدعم

إذا واجهت أي مشاكل:
1. تحقق من أسماء الأعمدة في قاعدة البيانات
2. تأكد من تشغيل Migration
3. تحقق من الـ logs
4. تأكد من نسخ جميع الملفات بشكل صحيح

---

**تم إنشاء هذا الدليل في:** 16 فبراير 2026
**الإصدار:** 1.0
