# شرح زر "Save & Published" 💾

## الزر موجود في أعلى الصفحة!

الزر الأخضر "Save & Published" موجود في أعلى صفحة Add Product بجانب زر "Bulk Upload".

## كيف يعمل النظام

### في وضع Batch مع Variations:

1. **املأ البيانات الأساسية:**
   - اسم المنتج
   - الكود (SKU)
   - Category
   - Unit
   - Brand (اختياري)

2. **اختر Batch Mode**

3. **اختر الـ Variations:**
   - مثلاً: Size و Color
   - أدخل القيم: S, M, L للـ Size
   - أدخل القيم: Red, Blue للـ Color

4. **اضغط "Generate Variations"**
   - سيتم إنشاء 6 صفوف (3 × 2)

5. **املأ البيانات لكل صف:**
   - اختر الضريبة
   - أدخل السعر قبل الضريبة
   - أدخل نسبة الربح
   - أدخل الكمية

6. **اضغط "Save & Published"** ⭐

## ماذا يحدث بعد الضغط على Save؟

### يتم حفظ:
- ✅ منتج واحد في جدول `products`
- ✅ عدة صفوف في جدول `stocks` (واحد لكل variation)

### مثال:
```
Product: T-Shirt (ID: 1)
├── Stock 1: S - Red (Qty: 10, Price: 100)
├── Stock 2: S - Blue (Qty: 15, Price: 100)
├── Stock 3: M - Red (Qty: 20, Price: 110)
├── Stock 4: M - Blue (Qty: 25, Price: 110)
├── Stock 5: L - Red (Qty: 30, Price: 120)
└── Stock 6: L - Blue (Qty: 35, Price: 120)
```

## كيف تشوف المنتجات بعد الحفظ؟

1. اذهب إلى: **Products → All Products**
2. هتلاقي منتج واحد اسمه "T-Shirt"
3. اضغط على "Edit" أو "View"
4. هتشوف كل الـ variations (stocks) جواه

## إذا كان الزر مش شغال:

### تحقق من:

1. **البيانات المطلوبة:**
   - اسم المنتج (مطلوب)
   - Category (مطلوب في بعض الإعدادات)
   - على الأقل صف واحد في الجدول

2. **افتح Console (F12):**
   - شوف إذا فيه أخطاء JavaScript
   - شوف إذا فيه أخطاء في الـ Network

3. **تأكد من الصلاحيات:**
   - يجب أن يكون عندك صلاحية `products.create`

### الكود المسؤول عن الحفظ:

```php
// في الـ Blade
<form action="{{ route('business.products.store') }}" method="POST" class="ajaxform_instant_reload">
    @csrf
    <!-- كل الحقول هنا -->
    <button class="save-publish-btn submit-btn">
        {{ __('Save & Published') }}
    </button>
</form>
```

الفورم يرسل البيانات إلى:
- Route: `business.products.store`
- Controller: `AcnooProductController@store`
- Method: POST

## البيانات اللي بتتبعت:

### للمنتج الأساسي:
```
productName: "T-Shirt"
productCode: "SHIRT-001"
category_id: 1
unit_id: 1
brand_id: 2
```

### لكل Stock/Variation:
```
stocks[row-123][batch_no]: "SHIRT-001-1"
stocks[row-123][productStock]: 10
stocks[row-123][vat_id]: 1
stocks[row-123][exclusive_price]: 100
stocks[row-123][inclusive_price]: 115
stocks[row-123][profit_percent]: 20
stocks[row-123][productSalePrice]: 120
stocks[row-123][variation_name]: "S - Red"
```

## نصائح:

1. **املأ كل الحقول المطلوبة** قبل الضغط على Save
2. **تأكد من وجود صف واحد على الأقل** في الجدول
3. **لا تنسى اختيار الضريبة** لكل صف
4. **تأكد من إدخال الأسعار** بشكل صحيح

## إذا ظهرت رسالة خطأ:

### "Product name is required"
- أدخل اسم المنتج في الحقل الأول

### "At least one stock is required"
- تأكد من وجود صف واحد على الأقل في الجدول
- استخدم زر "+ Add" أو "Generate Variations"

### "Price is required"
- أدخل السعر لكل صف في الجدول

---

**الزر يعمل بشكل صحيح!** فقط تأكد من ملء كل البيانات المطلوبة قبل الضغط عليه. 🎉
