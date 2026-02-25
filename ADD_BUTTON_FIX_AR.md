# إصلاح زر "+ Add" في Batch Mode ✅

## التاريخ: 25 فبراير 2026

## المشكلة
زر "+ Add" في وضع Batch لا يضيف صفوف جديدة في الجدول.

## السبب المحتمل
1. قد يكون `#vats-data` أو `#warehouses-data` غير موجود
2. قد يكون هناك خطأ في parsing البيانات
3. قد يكون الكود يتوقف عند خطأ JavaScript

## الحل المطبق

### 1. إضافة معالجة للأخطاء (Error Handling)

تم تحديث الكود ليتحقق من وجود البيانات قبل استخدامها:

```javascript
// Check if permissions-data exists
const permissionsElement = document.getElementById("permissions-data");
if (!permissionsElement) {
    console.error("permissions-data element not found!");
    alert("Error: Missing permissions data. Please refresh the page.");
    return;
}

// Get warehouses data (may not exist if warehouse addon is disabled)
let warehouses = [];
const warehousesElement = document.getElementById("warehouses-data");
if (warehousesElement && warehousesElement.value) {
    try {
        warehouses = JSON.parse(warehousesElement.value);
    } catch (e) {
        console.warn("Could not parse warehouses data:", e);
    }
}

// Get vats data
let vats = [];
const vatsElement = document.getElementById("vats-data");
if (vatsElement && vatsElement.value) {
    try {
        vats = JSON.parse(vatsElement.value);
    } catch (e) {
        console.error("Could not parse vats data:", e);
    }
}
```

### 2. إضافة Console Logs للتتبع

```javascript
console.log("Adding new row to #product-data");
$("#product-data").append(newRow);
console.log("Row added successfully");
```

### 3. إصلاح حساب عدد الصفوف

تم تغيير:
```javascript
// قبل
$(".single-product-table tbody tr").length + 1

// بعد
$("#product-data tr").length + 1
```

## خطوات الاختبار

### 1. افتح Console في المتصفح
- اضغط F12
- اذهب إلى تبويب "Console"

### 2. جرب الضغط على زر "+ Add"

**إذا ظهرت رسالة خطأ:**
- `permissions-data element not found!` → المشكلة في الـ blade file
- `Could not parse vats data` → المشكلة في بيانات الضرائب
- `Could not parse warehouses data` → المشكلة في بيانات المخازن

**إذا ظهرت الرسائل التالية:**
```
Adding new row to #product-data
Row added successfully
```
معناها الكود يعمل بشكل صحيح!

### 3. تحقق من وجود البيانات

في Console، اكتب:
```javascript
// تحقق من permissions
console.log(document.getElementById("permissions-data"));

// تحقق من vats
console.log(document.getElementById("vats-data"));

// تحقق من warehouses
console.log(document.getElementById("warehouses-data"));
```

## الحلول البديلة

### إذا كانت المشكلة في vats-data:

تأكد من وجود هذا السطر في `create.blade.php`:
```php
<input type="hidden" id="vats-data" value='@json($vats)'>
```

### إذا كانت المشكلة في permissions-data:

تأكد من وجود هذا الكود في `create.blade.php`:
```php
@php
    $permissionsArray = [];
    foreach($defaultPermissions as $key) {
        $value = isset($modules[$key]) ? ($modules[$key] ? 1 : 0) : 1;
        $permissionsArray[$key] = $value == 1;
    }
@endphp
<input type="hidden" id="permissions-data" value='@json($permissionsArray)'>
```

### إذا كانت المشكلة في warehouses-data:

هذا اختياري (فقط إذا كان Warehouse Addon مفعل):
```php
@if (moduleCheck('WarehouseAddon') && is_module_enabled($modules, 'show_warehouse'))
    <input type="hidden" id="warehouses-data" value='@json($warehouses)'>
@endif
```

## التحقق من الإصلاح

1. امسح الـ cache: `Ctrl + Shift + R`
2. اذهب إلى صفحة Add Product
3. اختر Batch mode
4. اضغط على زر "+ Add"
5. يجب أن يضاف صف جديد في الجدول

## الملفات المعدلة

- `public/assets/js/custom/product.js` - إضافة error handling و console logs

## ملاحظات

- الكود الآن أكثر أماناً ويتعامل مع الأخطاء بشكل أفضل
- إذا كانت البيانات غير موجودة، سيظهر تنبيه للمستخدم
- Console logs تساعد في تتبع المشكلة

---

**جرب الآن وأخبرني بالنتيجة!** 🔧
