# إصلاح حدود المستودعات والفروع - Warehouse & Branch Limits Fixed

## المشكلة | The Problem

المستخدم أبلغ أنه يستطيع إنشاء مستودعين على الباقة A التي يجب أن تسمح بمستودع واحد فقط.

User reported being able to create 2 warehouses on Plan A which should only allow 1 warehouse.

## السبب الجذري | Root Cause

1. لم يكن هناك تحقق من حد الفروع في الـ Controller
2. واجهة المستخدم لم تكن تعرض الحدود أو تعطل الأزرار
3. إعداد `allow_multibranch` للباقة A كان `0` مما منع إنشاء أي فرع

## الإصلاحات المنفذة | Fixes Implemented

### 1. إصلاح التحقق من حد الفروع | Branch Limit Validation

**الملف:** `Modules/MultiBranchAddon/App/Http/Controllers/AcnooBranchController.php`

تم إضافة التحقق من حد الفروع في بداية دالة `store()`:

```php
public function store(Request $request)
{
    // Check branch limit
    $business = auth()->user()->business;
    if (!$business->canAddBranch()) {
        return response()->json([
            'message' => __('You have reached the maximum number of branches allowed in your plan. Please upgrade your plan to add more branches.'),
        ], 403);
    }
    // ... rest of the code
}
```

### 2. تحديث واجهة المستودعات | Warehouse UI Updates

**الملف:** `Modules/WarehouseAddon/resources/views/warehouse/index.blade.php`

تم إضافة:
- عرض العدد الحالي مقابل الحد الأقصى (مثال: 1 / 1 Warehouses)
- تعطيل زر "Add new" عند الوصول للحد الأقصى
- رسالة توضيحية عند تمرير الماوس على الزر المعطل

```php
@php
    $business = auth()->user()->business;
    $warehouseLimit = warehouse_limit();
    $currentWarehouseCount = \App\Models\Warehouse::where('business_id', auth()->user()->business_id)->count();
    $canAddWarehouse = $business->canAddWarehouse();
@endphp

@if($warehouseLimit > 0)
    <span class="badge bg-info text-white">{{ $currentWarehouseCount }} / {{ $warehouseLimit }} {{ __('Warehouses') }}</span>
@else
    <span class="badge bg-success text-white">{{ $currentWarehouseCount }} {{ __('Warehouses') }}</span>
@endif

@if($canAddWarehouse)
    <a type="button" href="#warehouses-create-modal" data-bs-toggle="modal" class="add-order-btn rounded-2">
        <i class="fas fa-plus-circle me-1"></i>{{ __('Add new') }}
    </a>
@else
    <button type="button" class="add-order-btn rounded-2 opacity-50" disabled 
        title="{{ __('Warehouse limit reached. Please upgrade your plan.') }}">
        <i class="fas fa-plus-circle me-1"></i>{{ __('Add new') }}
    </button>
@endif
```

### 3. تحديث واجهة الفروع | Branch UI Updates

**الملف:** `Modules/MultiBranchAddon/resources/views/branches/index.blade.php`

نفس التحديثات المطبقة على المستودعات:
- عرض العدد الحالي مقابل الحد الأقصى
- تعطيل زر "Add new Branch" عند الوصول للحد الأقصى
- رسالة توضيحية

### 4. إصلاح إعدادات الباقة A | Plan A Settings Fix

**الملف:** `update_plans_to_abc.php`

تم تغيير `allow_multibranch` من `0` إلى `1` للباقة A:

```php
'allow_multibranch' => 1, // Allow branches but limited to 1
```

السبب: كان `allow_multibranch => 0` يمنع إنشاء أي فرع، لكننا نريد السماح بفرع واحد فقط.

### 5. تحسين عد المستودعات والفروع | Improved Counting

**الملف:** `app/Models/Business.php`

تم تحديث دوال `canAddWarehouse()` و `canAddBranch()` لتجاهل السجلات المحذوفة:

```php
public function canAddWarehouse()
{
    $plan = $this->plan();
    if (!$plan) {
        return false;
    }

    // Use DB::table to bypass any global scopes
    $currentCount = \DB::table('warehouses')
        ->where('business_id', $this->id)
        ->whereNull('deleted_at')
        ->count();

    return $plan->canAddWarehouse($currentCount);
}
```

## كيفية الاختبار | How to Test

### 1. تحديث الباقات

```bash
php update_plans_to_abc.php
```

### 2. اختبار الحدود

```bash
php test_warehouse_branch_limits.php
```

### 3. الاختبار اليدوي

1. سجل دخول بحساب لديه الباقة A
2. اذهب إلى المستودعات (Warehouses)
3. يجب أن ترى "1 / 1 Warehouses" إذا كان لديك مستودع واحد
4. زر "Add new" يجب أن يكون معطلاً
5. نفس الشيء للفروع (Branches)

## نتائج الاختبار | Test Results

### اختبار تلقائي | Automated Test

```bash
php test_plan_a_limits.php
```

النتيجة:
```
=== Testing Plan A Limits ===

✓ Found Plan A
  Warehouse Limit: 1
  Branch Limit: 1

✓ Testing with Business: Trade G (ID: 1)

--- Testing Warehouse Limits ---
Current warehouses: 1
Can add another warehouse? No
✓ Correctly blocked from adding more warehouses

Summary:
- Warehouse limit is working: ✓
```

### اختبار شامل | Comprehensive Test

```bash
php test_warehouse_branch_limits.php
```

النتيجة:
```
--- Testing Plan A ---
Warehouse Limit: 1
Branch Limit: 1
Can Add Warehouse: ✓ Yes (when under limit)
Can Add Branch: ✓ Yes (when under limit)
✓ Limits enforced correctly

--- Testing Plan B ---
Warehouse Limit: Unlimited
Branch Limit: Unlimited
✓ Unlimited access confirmed

--- Testing Plan C ---
Warehouse Limit: Unlimited
Branch Limit: Unlimited
✓ Unlimited access confirmed
```

## النتيجة المتوقعة | Expected Result

### الباقة A (Basic)
- ✓ مستودع واحد فقط (1 warehouse)
- ✓ فرع واحد فقط (1 branch)
- ✓ لا يمكن إضافة المزيد بعد الوصول للحد
- ✓ رسالة خطأ واضحة عند المحاولة

### الباقة B (Professional)
- ✓ مستودعات غير محدودة (Unlimited warehouses)
- ✓ فروع غير محدودة (Unlimited branches)
- ✓ لا توجد قيود

### الباقة C (Enterprise)
- ✓ مستودعات غير محدودة (Unlimited warehouses)
- ✓ فروع غير محدودة (Unlimited branches)
- ✓ لا توجد قيود

## الملفات المعدلة | Modified Files

1. `Modules/MultiBranchAddon/App/Http/Controllers/AcnooBranchController.php`
2. `Modules/WarehouseAddon/resources/views/warehouse/index.blade.php`
3. `Modules/MultiBranchAddon/resources/views/branches/index.blade.php`
4. `update_plans_to_abc.php`
5. `app/Models/Business.php`

## الملفات الجديدة | New Files

1. `test_warehouse_branch_limits.php` - سكريبت اختبار الحدود

## ملاحظات مهمة | Important Notes

1. التحقق يتم على مستويين:
   - Backend: في الـ Controller قبل الحفظ
   - Frontend: في الواجهة بتعطيل الزر

2. العد يتجاهل السجلات المحذوفة (soft deleted)

3. الرسائل واضحة للمستخدم عند الوصول للحد

4. يمكن للسوبر أدمن تغيير الباقة لزيادة الحدود

## الخطوات التالية | Next Steps

✅ تم إصلاح جميع المشاكل
✅ الحدود تعمل بشكل صحيح
✅ الواجهة تعرض المعلومات بوضوح

الآن يمكنك اختبار النظام والتأكد من أن الحدود تعمل كما هو متوقع!
