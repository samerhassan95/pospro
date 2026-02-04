# تطبيق فواتير B2B (Tax Invoice)

## نظرة عامة
تم إضافة دعم فواتير B2B (الفواتير الضريبية) بالإضافة إلى فواتير B2C (الفواتير المبسطة) الموجودة مسبقاً.

## الفرق بين B2C و B2B

### B2C (Simplified Invoice - فاتورة مبسطة)
- تصدر عادة بين منشأة ومنشأة
- لا تتطلب معلومات تفصيلية عن العميل
- لا يشترط وجود رقم ضريبي للعميل

### B2B (Tax Invoice - فاتورة ضريبية)
- تصدر بين منشأة ومنشأة أخرى
- تتطلب معلومات تفصيلية عن العميل
- **يجب** أن يكون للعميل رقم ضريبي (15 رقم)
- تتطلب عنوان كامل ومفصل

## الحقول المطلوبة للفواتير B2B

### في جدول `parties` (العملاء):
1. **zatca_type**: نوع الفاتورة (b2c أو b2b)
2. **vat_number**: الرقم الضريبي (15 رقم) - **إلزامي للـ B2B**
3. **building_number**: رقم المبنى - **إلزامي للـ B2B**
4. **street_name**: اسم الشارع - **إلزامي للـ B2B**
5. **district**: الحي - **إلزامي للـ B2B**
6. **city**: المدينة - **إلزامي للـ B2B**
7. **postal_code**: الرمز البريدي - **إلزامي للـ B2B**
8. **country_code**: كود الدولة (SA, AE, BH, etc.) - **إلزامي**

### في جدول `businesses`:
نفس الحقول المذكورة أعلاه (ما عدا zatca_type و vat_number)

### في جدول `sales`:
- **invoice_type**: نوع الفاتورة (b2c أو b2b)

## التغييرات المطبقة

### 1. Database Migration
```bash
php artisan migrate
```
سيتم إضافة الحقول الجديدة لجداول:
- parties
- businesses
- sales

### 2. Models
تم تحديث Models التالية:
- `App\Models\Party`
- `App\Models\Business`
- `App\Models\Sale`

### 3. Views
تم تحديث صفحات:
- `Modules/Business/resources/views/parties/create.blade.php`
- `Modules/Business/resources/views/parties/edit.blade.php`

### 4. Controller Validation
تم تحديث `Modules/Business/App/Http/Controllers/AcnooPartyController.php`

## كيفية الاستخدام

### 1. إضافة عميل B2B جديد
1. اذهب إلى قائمة العملاء
2. اضغط على "إضافة عميل جديد"
3. اختر "B2B - Tax Invoice" من قائمة "Invoice Type"
4. ستظهر الحقول الإضافية المطلوبة:
   - الرقم الضريبي (15 رقم)
   - رقم المبنى
   - اسم الشارع
   - الحي
   - المدينة
   - الرمز البريدي
   - كود الدولة
5. املأ جميع الحقول المطلوبة
6. احفظ العميل

### 2. تعديل عميل موجود إلى B2B
1. افتح صفحة تعديل العميل
2. غير "Invoice Type" إلى "B2B - Tax Invoice"
3. املأ الحقول الإضافية المطلوبة
4. احفظ التغييرات

### 3. إصدار فاتورة B2B
عند إنشاء فاتورة لعميل من نوع B2B:
- سيتم تلقائياً تعيين `invoice_type` في جدول `sales` إلى 'b2b'
- سيتم استخدام معلومات العميل الكاملة في الفاتورة
- سيتم إرسال الفاتورة إلى ZATCA كفاتورة ضريبية (Tax Invoice)

## Validation Rules

### للعملاء B2B:
```php
'zatca_type' => 'required|in:b2c,b2b',
'vat_number' => 'required_if:zatca_type,b2b|nullable|digits:15',
'building_number' => 'required_if:zatca_type,b2b|nullable|string|max:255',
'street_name' => 'required_if:zatca_type,b2b|nullable|string|max:255',
'district' => 'required_if:zatca_type,b2b|nullable|string|max:255',
'city' => 'required_if:zatca_type,b2b|nullable|string|max:255',
'postal_code' => 'required_if:zatca_type,b2b|nullable|string|max:10',
'country_code' => 'required|string|max:2',
```

## JavaScript Functionality
تم إضافة JavaScript لإظهار/إخفاء الحقول تلقائياً:
- عند اختيار B2C: تخفي الحقول الإضافية
- عند اختيار B2B: تظهر الحقول الإضافية وتجعلها إلزامية

## الخطوات التالية

### 1. تحديث UBL Generator
يجب تحديث `app/Services/Zatca/UblGenerator.php` لدعم فواتير B2B:
- إضافة معلومات العميل الكاملة
- إضافة الرقم الضريبي للعميل
- إضافة العنوان الكامل

### 2. تحديث ZATCA Service
يجب تحديث `app/Services/Zatca/ZatcaService.php`:
- إضافة دالة جديدة لإرسال فواتير B2B
- التفريق بين B2C و B2B في عملية الإرسال

### 3. تحديث PDF Template
يجب تحديث قالب PDF للفواتير لإظهار:
- نوع الفاتورة (B2C أو B2B)
- معلومات العميل الكاملة للفواتير B2B
- الرقم الضريبي للعميل

### 4. تحديث صفحة إنشاء الفاتورة
يجب إضافة خيار لاختيار نوع الفاتورة عند إنشاء فاتورة جديدة:
- إذا كان العميل من نوع B2B، يتم تلقائياً اختيار B2B
- إمكانية تغيير النوع يدوياً إذا لزم الأمر

## ملاحظات مهمة

1. **الرقم الضريبي**: يجب أن يكون 15 رقم بالضبط
2. **كود الدولة**: يجب أن يكون حرفين (SA, AE, BH, etc.)
3. **الحقول الإلزامية**: جميع حقول العنوان إلزامية للفواتير B2B
4. **التوافق مع ZATCA**: يجب التأكد من أن جميع البيانات متوافقة مع متطلبات ZATCA

## أمثلة

### مثال على بيانات عميل B2B:
```json
{
  "name": "شركة الأمثلة المحدودة",
  "zatca_type": "b2b",
  "vat_number": "300123456789003",
  "building_number": "1234",
  "street_name": "شارع الملك فهد",
  "district": "العليا",
  "city": "الرياض",
  "postal_code": "12345",
  "country_code": "SA"
}
```

### مثال على بيانات عميل B2C:
```json
{
  "name": "محمد أحمد",
  "zatca_type": "b2c",
  "phone": "0501234567",
  "email": "mohammed@example.com"
}
```
