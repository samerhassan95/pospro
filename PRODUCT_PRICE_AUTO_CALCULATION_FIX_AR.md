# ✅ تم إصلاح: حساب السعر التلقائي بناءً على الضريبة

## 🎯 المشكلة

عند إضافة منتج جديد:
- ❌ السعر "قبل الضريبة" (Cost exc. tax) لا يحسب تلقائياً
- ❌ السعر "شامل الضريبة" (Cost inc. tax) لا يحسب تلقائياً
- ❌ الحساب لا يطبق بناءً على نسبة الضريبة المحددة في الإعدادات

## ✅ الحل

تم إضافة كود JavaScript لحساب الأسعار تلقائياً عند:
1. إدخال السعر قبل الضريبة → يحسب السعر شامل الضريبة
2. إدخال السعر شامل الضريبة → يحسب السعر قبل الضريبة
3. تغيير نسبة الضريبة → يعيد حساب كل الأسعار
4. تحميل الصفحة → يحسب الأسعار إذا كانت الضريبة محددة

---

## 📝 كيف يعمل النظام الآن؟

### السيناريو 1: إدخال السعر قبل الضريبة

**مثال:**
```
نسبة الضريبة: 15%
السعر قبل الضريبة: 100 ريال

الحساب التلقائي:
السعر شامل الضريبة = 100 + (100 × 15%) = 115 ريال
```

**الخطوات:**
1. اختر الضريبة من القائمة (مثلاً: VAT 15%)
2. أدخل السعر في "Cost exc. tax": 100
3. تلقائياً سيظهر في "Cost inc. tax": 115

---

### السيناريو 2: إدخال السعر شامل الضريبة

**مثال:**
```
نسبة الضريبة: 15%
السعر شامل الضريبة: 115 ريال

الحساب التلقائي:
السعر قبل الضريبة = 115 ÷ (1 + 15%) = 100 ريال
```

**الخطوات:**
1. اختر الضريبة من القائمة (مثلاً: VAT 15%)
2. أدخل السعر في "Cost inc. tax": 115
3. اضغط خارج الحقل (blur)
4. تلقائياً سيظهر في "Cost exc. tax": 100

---

### السيناريو 3: تغيير نسبة الضريبة

**مثال:**
```
السعر قبل الضريبة: 100 ريال

عند اختيار VAT 15%:
السعر شامل الضريبة = 115 ريال

عند تغيير إلى VAT 5%:
السعر شامل الضريبة = 105 ريال (يتحدث تلقائياً)
```

**الخطوات:**
1. أدخل السعر قبل الضريبة: 100
2. اختر VAT 15% → السعر شامل الضريبة: 115
3. غيّر إلى VAT 5% → السعر شامل الضريبة: 105 (تلقائياً)

---

## 🔧 التفاصيل التقنية

### الدوال المستخدمة:

#### 1. `getVatRate()`
```javascript
// تحصل على نسبة الضريبة من القائمة المختارة
function getVatRate() {
    return parseFloat($("#vat_id").find("option:selected").data("vat_rate")) || 0;
}
```

#### 2. `updateInclusiveFromExclusive($row)`
```javascript
// تحسب السعر شامل الضريبة من السعر قبل الضريبة
function updateInclusiveFromExclusive($row) {
    const vatRate = getVatRate();
    const vatType = $("#vat_type").val();
    
    const exclusiveInput = $row.find(".exclusive_price");
    const inclusiveInput = $row.find(".inclusive_price");
    
    let exclusive = parseFloat(exclusiveInput.val()) || 0;
    
    // inclusive = exclusive + VAT%
    if (vatType && vatRate) {
        inclusiveInput.val((exclusive + (exclusive * vatRate) / 100).toFixed(2));
    } else {
        inclusiveInput.val(exclusive.toFixed(2));
    }
}
```

#### 3. `updateExclusiveFromInclusive($row)`
```javascript
// تحسب السعر قبل الضريبة من السعر شامل الضريبة
function updateExclusiveFromInclusive($row) {
    const vatRate = getVatRate();
    
    const inclusiveInput = $row.find(".inclusive_price");
    const exclusiveInput = $row.find(".exclusive_price");
    
    let inclusive = parseFloat(inclusiveInput.val()) || 0;
    
    // Reverse VAT: exclusive = inclusive / (1 + VAT%)
    let exclusive = inclusive / (1 + vatRate / 100);
    exclusiveInput.val(exclusive.toFixed(2));
    
    // Recalculate MRP and profit
    calculateMrpRow($row);
}
```

---

## 🎯 الأحداث (Events)

### 1. عند إدخال السعر قبل الضريبة:
```javascript
$(document).on("input change", ".exclusive_price", function () {
    const $row = $(this).closest("tr").length
        ? $(this).closest("tr")
        : $(this).closest(".row");
    calculateMrpRow($row); // يحسب السعر شامل الضريبة + سعر البيع
});
```

### 2. عند إدخال السعر شامل الضريبة:
```javascript
$(document).on("blur", ".inclusive_price", function () {
    const $row = $(this).closest("tr").length
        ? $(this).closest("tr")
        : $(this).closest(".row");
    updateExclusiveFromInclusive($row); // يحسب السعر قبل الضريبة
});
```

### 3. عند تغيير الضريبة:
```javascript
$("#vat_id, #vat_type").on("change", function () {
    $(".exclusive_price").each(function () {
        const $row = $(this).closest("tr").length
            ? $(this).closest("tr")
            : $(this).closest(".row");
        calculateMrpRow($row); // يعيد حساب كل الصفوف
    });
});
```

### 4. عند تحميل الصفحة (NEW):
```javascript
$(document).ready(function() {
    const vatRate = getVatRate();
    if (vatRate > 0) {
        // يحسب الأسعار للصفوف الموجودة
        $(".exclusive_price, .inclusive_price").each(function() {
            const $row = $(this).closest("tr").length
                ? $(this).closest("tr")
                : $(this).closest(".row");
            
            if ($(this).hasClass("exclusive_price") && $(this).val()) {
                updateInclusiveFromExclusive($row);
            }
            else if ($(this).hasClass("inclusive_price") && $(this).val()) {
                updateExclusiveFromInclusive($row);
            }
        });
    }
});
```

---

## 📊 أمثلة عملية

### مثال 1: منتج بضريبة 15%

```
الخطوات:
1. اختر VAT (15%)
2. أدخل Cost exc. tax: 100

النتيجة التلقائية:
✅ Cost inc. tax: 115.00
✅ إذا أدخلت Profit (25%)
   → Sales Price: 143.75
```

### مثال 2: منتج بضريبة 5%

```
الخطوات:
1. اختر VAT (5%)
2. أدخل Cost exc. tax: 200

النتيجة التلقائية:
✅ Cost inc. tax: 210.00
✅ إذا أدخلت Profit (20%)
   → Sales Price: 252.00
```

### مثال 3: حساب عكسي

```
الخطوات:
1. اختر VAT (15%)
2. أدخل Cost inc. tax: 230
3. اضغط خارج الحقل

النتيجة التلقائية:
✅ Cost exc. tax: 200.00
✅ إذا أدخلت Profit (30%)
   → Sales Price: 299.00
```

---

## ✅ ما تم إصلاحه

### قبل الإصلاح:
```
❌ لا يحسب السعر شامل الضريبة تلقائياً
❌ لا يحسب السعر قبل الضريبة تلقائياً
❌ لا يطبق الضريبة عند تحميل الصفحة
❌ يحتاج تدخل يدوي لكل حقل
```

### بعد الإصلاح:
```
✅ يحسب السعر شامل الضريبة تلقائياً
✅ يحسب السعر قبل الضريبة تلقائياً
✅ يطبق الضريبة عند تحميل الصفحة
✅ يعيد الحساب عند تغيير الضريبة
✅ يعمل مع Single و Batch Products
```

---

## 🧪 كيف تختبر؟

### الاختبار 1: إضافة منتج جديد
```
1. اذهب إلى: Products > Add Product
2. اختر Tax: VAT (15%)
3. أدخل Cost exc. tax: 100
4. تحقق: Cost inc. tax يجب أن يكون 115
```

### الاختبار 2: تغيير الضريبة
```
1. أدخل Cost exc. tax: 100
2. اختر VAT (15%) → Cost inc. tax: 115
3. غيّر إلى VAT (5%) → Cost inc. tax: 105
4. تحقق: التحديث تلقائي
```

### الاختبار 3: حساب عكسي
```
1. اختر VAT (15%)
2. أدخل Cost inc. tax: 115
3. اضغط Tab أو انقر خارج الحقل
4. تحقق: Cost exc. tax يجب أن يكون 100
```

### الاختبار 4: Batch Products
```
1. اختر Product Type: Batch
2. اختر VAT (15%)
3. اضغط "+ Add" لإضافة صف جديد
4. أدخل Cost exc. tax: 50
5. تحقق: Cost inc. tax يجب أن يكون 57.50
```

---

## 📁 الملفات المعدلة

```
✅ public/assets/js/custom/product.js
   - تم إضافة حساب تلقائي عند تحميل الصفحة
   - تم تحسين دوال الحساب
```

---

## 🎉 النتيجة النهائية

```
✅ النظام الآن يحسب الأسعار تلقائياً
✅ يطبق نسبة الضريبة من الإعدادات
✅ يعمل مع كل أنواع المنتجات
✅ يدعم الحساب العكسي
✅ يعيد الحساب عند تغيير الضريبة
```

---

**تاريخ الإصلاح:** {{ date('Y-m-d H:i:s') }}
**الحالة:** ✅ تم الإصلاح وجاهز للاستخدام
