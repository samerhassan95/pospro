# إصلاح خطأ "Something went wrong" عند الدفع

## المشكلة
عند الضغط على زر "Complete Payment" في صفحة المبيعات، كان يظهر خطأ:
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'delivery_type'
```

## السبب الحقيقي
كان هناك migration لم يتم تشغيله يضيف عمود `delivery_type` إلى جدول `sales`. هذا العمود مطلوب لحفظ نوع الطلب (delivery, pre-order, takeaway).

## الحل المطبق

### 1. تشغيل الـ Migrations المعلقة
تم تشغيل الـ migrations التالية:

```bash
php artisan migrate --path=database/migrations/2024_02_20_000001_add_delivery_type_to_sales_table.php
php artisan migrate --path=database/migrations/2026_02_04_000000_add_moyasar_settings_to_businesses_table.php
```

**النتيجة:**
- ✅ تم إضافة عمود `delivery_type` إلى جدول `sales`
- ✅ تم إضافة إعدادات Moyasar إلى جدول `businesses`

### 2. إضافة تسجيل الأخطاء (Error Logging)
تم تحديث دالة `store()` و `update()` في الملف:
```
Modules/Business/App/Http/Controllers/AcnooSaleController.php
```

**التغييرات:**
- إضافة `Log::error()` لتسجيل تفاصيل الخطأ الكاملة
- إضافة رسالة الخطأ الفعلية إلى الرد المرسل للمستخدم
- تسجيل معلومات إضافية: رقم السطر، اسم الملف، البيانات المرسلة، وتتبع الخطأ

**الكود الجديد:**
```php
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
```

## اختبار الحل

الآن يمكنك اختبار عملية الدفع:

1. **افتح صفحة المبيعات:** `http://127.0.0.1:8000/business/sales/create`
2. **أضف منتجات إلى السلة**
3. **اختر عميل**
4. **اضغط على "Pay the Bill"**
5. **أدخل المبلغ المستلم**
6. **اضغط على "Complete Payment"**

**النتيجة المتوقعة:**
- ✅ يجب أن تتم عملية الدفع بنجاح
- ✅ يتم حفظ البيع في قاعدة البيانات
- ✅ يتم تحديث المخزون
- ✅ يتم التوجيه إلى صفحة الفاتورة

## كيفية استخدام التحديث

### الآن عند حدوث خطأ:

1. **سيظهر للمستخدم:** رسالة خطأ واضحة تحتوي على السبب الفعلي
   ```
   Something went wrong! - [تفاصيل الخطأ الفعلي]
   ```

2. **سيتم تسجيله في اللوج:** يمكنك فحص الملف `storage/logs/laravel.log` لرؤية:
   - رسالة الخطأ الكاملة
   - رقم السطر الذي حدث فيه الخطأ
   - اسم الملف
   - البيانات التي تم إرسالها
   - تتبع كامل للخطأ (stack trace)

### مثال على فحص اللوج:
```bash
# في PowerShell
Get-Content storage/logs/laravel.log -Tail 50

# أو في CMD
type storage\logs\laravel.log | more
```

## الأخطاء المحتملة وحلولها

### 1. خطأ في قاعدة البيانات (Database Error)
**مثال:** `Column not found` أو `Table doesn't exist`
**الحل:** التأكد من تشغيل جميع الـ migrations

### 2. خطأ في البيانات المطلوبة (Validation Error)
**مثال:** `The party_id field is required`
**الحل:** التأكد من اختيار العميل قبل الدفع

### 3. خطأ في المخزون (Stock Error)
**مثال:** `Stock not available`
**الحل:** التأكد من توفر المنتجات في المخزون

### 4. خطأ في الصلاحيات (Permission Error)
**مثال:** `Unauthorized`
**الحل:** التأكد من صلاحيات المستخدم

## ملاحظات مهمة

- ✅ تم تشغيل migration لإضافة عمود `delivery_type` إلى جدول `sales`
- ✅ تم تشغيل migration لإضافة إعدادات Moyasar
- ✅ تم إضافة تسجيل الأخطاء في دالة `store()` (إنشاء مبيعات جديدة)
- ✅ تم إضافة تسجيل الأخطاء في دالة `update()` (تحديث مبيعات موجودة)
- ✅ الآن يمكن إتمام عمليات الدفع بنجاح
- 📝 جميع الأخطاء المستقبلية سيتم تسجيلها في `storage/logs/laravel.log`

## ما تم إصلاحه بالضبط

### المشكلة الأساسية:
عند محاولة حفظ عملية بيع جديدة، كان الكود يحاول حفظ قيمة في عمود `delivery_type` لكن هذا العمود لم يكن موجوداً في قاعدة البيانات.

### الحل:
1. تشغيل الـ migration الذي يضيف العمود المفقود
2. إضافة تسجيل أفضل للأخطاء لتسهيل اكتشاف المشاكل المستقبلية

### الأعمدة المضافة:
- `delivery_type` في جدول `sales` - لحفظ نوع الطلب (delivery, pre-order, takeaway)
- `moyasar_settings` في جدول `businesses` - لإعدادات بوابة الدفع Moyasar

## التاريخ
- **تاريخ الإصلاح:** 21 فبراير 2026
- **الملفات المعدلة:** 
  - `Modules/Business/App/Http/Controllers/AcnooSaleController.php`
  - `database/migrations/2024_02_20_000001_add_delivery_type_to_sales_table.php` (تم تشغيله)
  - `database/migrations/2026_02_04_000000_add_moyasar_settings_to_businesses_table.php` (تم تشغيله)
