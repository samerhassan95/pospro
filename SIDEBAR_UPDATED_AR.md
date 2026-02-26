# تحديث Sidebar بناءً على صلاحيات الباقات

## ✅ التحديثات المنفذة

تم تحديث الـ sidebar لإخفاء/إظهار الأقسام بناءً على صلاحيات كل باقة:

### 1. قائمة المستحقات (Due List)
```blade
@if(plan_allows('due_list'))
@usercan('dues.read')
    <!-- Due List Section -->
@endusercan
@endif
```
- ✅ **باقة A**: مخفي
- ✅ **باقة B**: ظاهر
- ✅ **باقة C**: ظاهر

### 2. المالية والحسابات (Finance & Accounts)
```blade
@if(plan_allows('finance'))
@usercan('banks.read')
    <!-- Finance Section -->
@endusercan
@endif
```
- ✅ **باقة A**: مخفي
- ✅ **باقة B**: ظاهر
- ✅ **باقة C**: ظاهر

### 3. العمولة والبيع (Sale Commission)
```blade
@if(plan_allows('commission'))
@usercanany(['commissions.read', 'sale-commissions.read'])
    <!-- Commission Section -->
@endusercanany
@endif
```
- ✅ **باقة A**: مخفي
- ✅ **باقة B**: ظاهر
- ✅ **باقة C**: ظاهر

### 4. إدارة الموارد البشرية (HRM)
```blade
@if (moduleCheck('HrmAddon') && plan_allows('hrm'))
@usercanany([...])
    <!-- HRM Section -->
@endusercanany
@endif
```
- ✅ **باقة A**: مخفي
- ✅ **باقة B**: ظاهر
- ✅ **باقة C**: ظاهر

### 5. المستودعات (Warehouse)
```blade
@if (moduleCheck('WarehouseAddon') && plan_allows('warehouses'))
@usercan('warehouses.read')
    <!-- Warehouse Section -->
@endusercan
@endif
```
- ✅ **باقة A**: محدود (1 مستودع فقط)
- ✅ **باقة B**: غير محدود
- ✅ **باقة C**: غير محدود

### 6. عرض حدود الباقة في Sidebar
تم إضافة عرض لحدود الفروع والمستودعات في قسم الباقة:

```blade
@if(branch_limit() !== null)
<p class="text-muted small mb-0">
    {{ __('Branches') }}: {{ branch_limit() }}
</p>
@endif
@if(warehouse_limit() !== null)
<p class="text-muted small mb-0">
    {{ __('Warehouses') }}: {{ warehouse_limit() }}
</p>
@endif
```

## 📊 النتيجة النهائية

### باقة A - Sidebar
```
✓ Dashboard
✓ Sales (POS)
✓ Purchases
✓ Products
✓ Warehouse (1 فقط)
✓ Transfer
✓ Branch (1 فقط)
✓ Stock List
✓ Customers
✓ Suppliers
✓ Tax Setting
✗ Due List (مخفي)
✗ Finance & Accounts (مخفي)
✗ Sale Commission (مخفي)
✗ HRM (مخفي)
✓ Reports
✓ Party Reports
✓ Settings
✓ ZATCA Integration
✓ Download Apk
```

### باقة B - Sidebar
```
✓ Dashboard
✓ Sales (POS)
✓ Purchases
✓ Products
✓ Warehouse (غير محدود)
✓ Transfer
✓ Branch (غير محدود)
✓ Stock List
✓ Customers
✓ Suppliers
✓ Tax Setting
✓ Due List
✓ Finance & Accounts
✓ Sale Commission
✓ HRM
✓ Reports
✓ Party Reports
✓ Settings
✓ ZATCA Integration
✓ Download Apk
```

### باقة C - Sidebar
```
✓ Dashboard
✓ Sales (POS)
✓ Purchases
✓ Products
✓ Warehouse (غير محدود)
✓ Transfer
✓ Branch (غير محدود)
✓ Stock List
✓ Customers
✓ Suppliers
✓ Tax Setting
✓ Due List
✓ Finance & Accounts
✓ Sale Commission
✓ HRM
✓ Reports
✓ Party Reports
✓ My Domains (إذا كان Module مفعل)
✓ SMS Marketing (إذا كان Module مفعل)
✓ Settings
✓ ZATCA Integration
✓ Download Apk
```

## 🎯 الميزات الإضافية

1. **عرض حدود الباقة**: يظهر في أسفل الـ sidebar عدد الفروع والمستودعات المسموح بها
2. **إخفاء تلقائي**: الأقسام غير المتاحة تختفي تماماً من القائمة
3. **تجربة مستخدم أفضل**: المستخدم يرى فقط ما هو متاح له
4. **تشجيع على الترقية**: زر "Upgrade Now" واضح في الـ sidebar

## 📝 ملاحظات مهمة

- جميع التحديثات تحترم الـ `@usercan` الموجود مسبقاً
- الصلاحيات تعمل بشكل متسلسل: Plan Permission → User Permission
- إذا كانت الباقة لا تسمح بالميزة، لن تظهر حتى لو كان المستخدم لديه صلاحية
- الـ Modules (HRM, Warehouse, Custom Domain) تحتاج أن تكون مفعلة بالإضافة لصلاحية الباقة

## ✅ الملفات المعدلة

1. `resources/views/layouts/business/partials/side-bar.blade.php` - تحديث Sidebar

## 🚀 الخطوات التالية (اختياري)

1. **تحديث صفحات الإضافة**:
   - إضافة checks في صفحة إضافة فرع جديد
   - إضافة checks في صفحة إضافة مستودع جديد
   - عرض رسالة عند الوصول للحد الأقصى

2. **إضافة Middleware للـ Routes**:
   - حماية routes المالية بـ `plan.permission:finance`
   - حماية routes HRM بـ `plan.permission:hrm`
   - حماية routes العمولات بـ `plan.permission:commission`

3. **تحديث صفحة الباقات**:
   - عرض الصلاحيات بشكل واضح
   - مقارنة بين الباقات
   - تسهيل عملية الترقية

النظام جاهز للاستخدام! 🎉
