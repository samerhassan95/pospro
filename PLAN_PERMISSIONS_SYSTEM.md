# نظام صلاحيات الباقات (A, B, C)

## نظرة عامة

تم تحديث نظام الباقات ليشمل ثلاث باقات رئيسية (A, B, C) مع صلاحيات محددة لكل باقة.

## الباقات

### باقة A (الأساسية)
- **المبيعات (POS)**: ✓
- **المشتريات**: ✓
- **المنتجات**: ✓
- **المستودعات**: 1 فقط
- **الفروع**: 1 فقط
- **المخزون**: ✓
- **العملاء**: ✓
- **الموردين**: ✓
- **إعداد الضريبة**: ✓
- **قائمة المستحقات**: ✗
- **المالية والحسابات**: ✗
- **العمولة والبيع**: ✗
- **إدارة الموارد البشرية**: ✗
- **التقارير**: ✓
- **التطبيق**: ✗
- **المتجر**: ✗

### باقة B (المتوسطة)
- **المبيعات (POS)**: ✓
- **المشتريات**: ✓
- **المنتجات**: ✓
- **المستودعات**: غير محدود
- **الفروع**: غير محدود
- **المخزون**: ✓
- **العملاء**: ✓
- **الموردين**: ✓
- **إعداد الضريبة**: ✓
- **قائمة المستحقات**: ✓
- **المالية والحسابات**: ✓
- **العمولة والبيع**: ✓
- **إدارة الموارد البشرية**: ✓
- **التقارير**: ✓
- **التطبيق**: ✗
- **المتجر**: ✗

### باقة C (المتقدمة)
- **المبيعات (POS)**: ✓
- **المشتريات**: ✓
- **المنتجات**: ✓
- **المستودعات**: غير محدود
- **الفروع**: غير محدود
- **المخزون**: ✓
- **العملاء**: ✓
- **الموردين**: ✓
- **إعداد الضريبة**: ✓
- **قائمة المستحقات**: ✓
- **المالية والحسابات**: ✓
- **العمولة والبيع**: ✓
- **إدارة الموارد البشرية**: ✓
- **التقارير**: ✓
- **التطبيق**: ✓
- **المتجر**: ✓

## الحقول الجديدة في جدول `plans`

```php
- allow_sales (boolean)
- allow_purchases (boolean)
- allow_products (boolean)
- allow_warehouses (boolean)
- warehouse_limit (integer, nullable) // null = unlimited
- branch_limit (integer, nullable) // null = unlimited
- allow_stock (boolean)
- allow_customers (boolean)
- allow_suppliers (boolean)
- allow_vat_settings (boolean)
- allow_due_list (boolean)
- allow_finance (boolean)
- allow_commission (boolean)
- allow_hrm (boolean)
- allow_reports (boolean)
- allow_pos_app (boolean)
- allow_store (boolean)
```

## استخدام النظام

### 1. في Controllers

استخدم middleware للتحقق من الصلاحيات:

```php
// في routes
Route::middleware(['auth', 'plan.permission:finance'])->group(function () {
    Route::get('/finance', [FinanceController::class, 'index']);
});

// أو في Controller
public function __construct()
{
    $this->middleware('plan.permission:hrm');
}
```

### 2. في Blade Views

استخدم helper functions للتحقق من الصلاحيات:

```blade
@if(plan_allows('finance'))
    <a href="{{ route('finance.index') }}">المالية والحسابات</a>
@endif

@if(can_add_warehouse())
    <button>إضافة مستودع جديد</button>
@else
    <p>لقد وصلت للحد الأقصى من المستودعات ({{ warehouse_limit() }})</p>
@endif

<p>الباقة الحالية: {{ current_plan_name() }}</p>
```

### 3. في PHP Code

```php
// التحقق من صلاحية
if (plan_allows('commission')) {
    // عرض صفحة العمولات
}

// التحقق من إمكانية إضافة فرع
if (can_add_branch()) {
    // السماح بإضافة فرع جديد
} else {
    return back()->with('error', 'لقد وصلت للحد الأقصى من الفروع');
}

// الحصول على حد المستودعات
$limit = warehouse_limit(); // null = unlimited, number = limit
```

### 4. في Business Model

```php
$business = auth()->user()->business;

// التحقق من صلاحية
if ($business->allows('hrm')) {
    // ...
}

// التحقق من إمكانية إضافة مستودع
if ($business->canAddWarehouse()) {
    // ...
}

// الحصول على الباقة
$plan = $business->plan();
```

## Helper Functions المتاحة

- `plan_allows($permission)` - التحقق من صلاحية معينة
- `can_add_warehouse()` - التحقق من إمكانية إضافة مستودع
- `can_add_branch()` - التحقق من إمكانية إضافة فرع
- `warehouse_limit()` - الحصول على حد المستودعات
- `branch_limit()` - الحصول على حد الفروع
- `current_plan_name()` - الحصول على اسم الباقة الحالية

## أسماء الصلاحيات (Permissions)

- `sales` - المبيعات
- `purchases` - المشتريات
- `products` - المنتجات
- `warehouses` - المستودعات
- `stock` - المخزون
- `customers` - العملاء
- `suppliers` - الموردين
- `vat_settings` - إعداد الضريبة
- `due_list` - قائمة المستحقات
- `finance` - المالية والحسابات
- `commission` - العمولة والبيع
- `hrm` - إدارة الموارد البشرية
- `reports` - التقارير
- `pos_app` - التطبيق
- `store` - المتجر

## الملفات المعدلة

1. `database/migrations/2026_02_26_000000_add_permissions_to_plans_table.php` - Migration جديد
2. `app/Models/Plan.php` - إضافة helper methods
3. `app/Models/Business.php` - إضافة helper methods
4. `app/Http/Middleware/CheckPlanPermission.php` - Middleware جديد
5. `app/Http/Kernel.php` - تسجيل middleware
6. `app/Helpers/Helper.php` - إضافة helper functions
7. `update_plans_to_abc.php` - Script لتحديث الباقات

## التشغيل

تم تشغيل النظام بنجاح:
- ✓ Migration تم تنفيذه
- ✓ الباقات تم تحديثها إلى A, B, C
- ✓ الصلاحيات تم تطبيقها
- ✓ Helper functions جاهزة للاستخدام
- ✓ Middleware جاهز للاستخدام

## الخطوات التالية

1. تطبيق middleware على routes المطلوبة
2. إضافة checks في views لإخفاء/إظهار العناصر حسب الصلاحيات
3. إضافة validation في controllers للتحقق من limits
4. تحديث صفحة الباقات لعرض الصلاحيات الجديدة
