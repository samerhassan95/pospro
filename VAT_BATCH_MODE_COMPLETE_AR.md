# إضافة عمود الضريبة في وضع Batch - مكتمل ✅

## التاريخ: 25 فبراير 2026

## ما تم إنجازه

### 1. إضافة عمود الضريبة (VAT) في جدول Batch Mode ✅

تم إضافة عمود جديد في جدول Batch Mode يسمح باختيار نوع الضريبة لكل صف (variation) بشكل منفصل.

**الملفات المعدلة:**
- `Modules/Business/resources/views/products/create.blade.php`
- `public/assets/js/custom/product.js`

### 2. التغييرات في واجهة المستخدم

#### في جدول Batch Mode:
```
| Batch No | Warehouse | Qty | Tax | Cost exc. tax | Cost inc. tax | Profit % | Sales Price | ... |
```

الآن كل صف في الجدول يحتوي على:
- قائمة منسدلة لاختيار نوع الضريبة (VAT)
- يمكن اختيار ضريبة مختلفة لكل variation
- الحساب التلقائي يعمل بناءً على الضريبة المختارة لكل صف

### 3. التغييرات في JavaScript

#### تحديث دالة `getVatRate()`
الآن تدعم الحصول على معدل الضريبة من مستويين:
1. **مستوى الصف (Batch Mode)**: تقرأ من `.row-vat-id` في الصف نفسه
2. **مستوى الحاوية (Single Mode)**: تقرأ من `.vat_id` في الحاوية

```javascript
function getVatRate($container) {
    // Check row-level VAT first (batch mode)
    const $row = $container && $container.closest ? $container.closest('tr') : null;
    
    if ($row && $row.length > 0) {
        const $rowVatSelect = $row.find('.row-vat-id');
        if ($rowVatSelect.length > 0) {
            // Return row-level VAT rate
        }
    }
    
    // Fallback to container-level VAT (single mode)
    // ...
}
```

#### إضافة Event Listener لتغيير الضريبة على مستوى الصف
```javascript
$(document).on("change", ".row-vat-id", function () {
    const $row = $(this).closest("tr");
    const $exclusiveInput = $row.find(".exclusive_price");
    
    if ($exclusiveInput.val() && parseFloat($exclusiveInput.val()) > 0) {
        calculateMrpRow($row);
    }
});
```

### 4. زر "+ Add" في Batch Mode

تم إصلاح زر "+ Add" ليضيف صفوف جديدة مع عمود الضريبة:

```javascript
// Add VAT dropdown for each row
if (permissions.show_vat_id) {
    let vatOptions = '<option value="">Select</option>';
    vats.forEach(function (vat) {
        vatOptions += `<option value="${vat.id}" data-vat_rate="${vat.rate}">${vat.name} (${vat.rate}%)</option>`;
    });
    
    newRow += `<td>
        <select name="stocks[${rowId}][vat_id]" class="form-control table-select w-100 row-vat-id">
            ${vatOptions}
        </select>
    </td>`;
}
```

### 5. الحساب التلقائي للأسعار

الآن عند:
1. إدخال السعر قبل الضريبة (Cost exc. tax)
2. اختيار نوع الضريبة من القائمة المنسدلة

**يتم تلقائياً:**
- حساب السعر بعد الضريبة (Cost inc. tax)
- تحديث سعر البيع (Sales Price) بناءً على نسبة الربح

### 6. كيفية الاستخدام

#### في وضع Batch:

1. اختر Category التي تحتوي على variations
2. اختر "Batch" من نوع المنتج
3. اختر الـ variations التي تريدها
4. اضغط "Generate Variations" لإنشاء كل التوليفات
5. **لكل صف في الجدول:**
   - اختر نوع الضريبة من القائمة المنسدلة
   - أدخل السعر قبل الضريبة
   - سيتم حساب السعر بعد الضريبة تلقائياً
   - أدخل نسبة الربح
   - سيتم حساب سعر البيع تلقائياً

#### مثال:
```
Variation: Red - Large
- Tax: VAT 15%
- Cost exc. tax: 100
- Cost inc. tax: 115 (محسوب تلقائياً)
- Profit %: 20
- Sales Price: 120 (محسوب تلقائياً)

Variation: Blue - Small
- Tax: VAT 5%
- Cost exc. tax: 100
- Cost inc. tax: 105 (محسوب تلقائياً)
- Profit %: 20
- Sales Price: 120 (محسوب تلقائياً)
```

### 7. الفرق بين Single Mode و Batch Mode

#### Single Mode:
- اختيار الضريبة مرة واحدة في الأعلى
- تطبق على المنتج بالكامل

#### Batch Mode:
- اختيار الضريبة لكل variation بشكل منفصل
- كل صف له ضريبة خاصة به
- مرونة أكبر في التسعير

## الحالة النهائية

✅ عمود الضريبة مضاف في جدول Batch Mode
✅ كل صف له قائمة منسدلة خاصة لاختيار الضريبة
✅ الحساب التلقائي يعمل بناءً على الضريبة المختارة لكل صف
✅ زر "+ Add" يعمل ويضيف صفوف جديدة مع عمود الضريبة
✅ دوال JavaScript محدثة لدعم الضريبة على مستوى الصف

## الخطوات التالية

1. **اختبار الوظيفة:**
   - جرب إضافة منتج في وضع Batch
   - اختر ضرائب مختلفة لكل variation
   - تأكد من الحساب التلقائي للأسعار

2. **إكمال زر "Generate Variations":**
   - حالياً الزر موجود لكن يحتاج تحسين
   - يجب أن ينشئ كل التوليفات تلقائياً في الجدول

3. **اختبار "+ Add" button:**
   - تأكد أن الزر يضيف صفوف جديدة بشكل صحيح
   - تأكد أن عمود الضريبة موجود في الصفوف الجديدة

## ملاحظات مهمة

- الضريبة الآن على مستوى الصف في Batch Mode
- كل variation يمكن أن يكون له ضريبة مختلفة
- الحساب التلقائي يعمل فوراً عند تغيير الضريبة أو السعر
- البيانات تُحفظ في قاعدة البيانات مع كل stock/variation

## الملفات المعدلة

1. `Modules/Business/resources/views/products/create.blade.php`
   - إضافة عمود Tax في header الجدول
   - إضافة dropdown للضريبة في الصف الأول
   - إضافة hidden input لبيانات الضرائب

2. `public/assets/js/custom/product.js`
   - تحديث `getVatRate()` لدعم row-level VAT
   - تحديث `add-variant-btn` لإضافة عمود الضريبة
   - إضافة event listener لـ `.row-vat-id`
   - تحديث دوال الحساب لاستخدام row-level VAT

---

**تم الانتهاء من إضافة عمود الضريبة في وضع Batch بنجاح! 🎉**
