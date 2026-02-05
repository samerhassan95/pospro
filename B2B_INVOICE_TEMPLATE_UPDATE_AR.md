# 🎨 تحديث قالب فاتورة B2B الموحد

## ✅ ما تم إنجازه

تم إنشاء قالب فاتورة B2B موحد جديد يعرض جميع البيانات بشكل مرتب ومنظم.

---

## 📄 القالب الجديد

**الملف**: `Modules/Business/resources/views/sales/invoices/b2b-unified.blade.php`

### المميزات:
1. ✅ **ترتيب موحد** - نفس ترتيب حقول الـ form
2. ✅ **بيانات كاملة** - يعرض جميع الحقول المطلوبة
3. ✅ **دعم RTL** - يدعم العربي بشكل كامل
4. ✅ **تصميم احترافي** - مناسب للطباعة
5. ✅ **متوافق مع ZATCA** - يحتوي على جميع المتطلبات

---

## 📊 ترتيب البيانات في الفاتورة

### 1. Header (الرأسية)
- Logo الشركة
- عنوان الفاتورة (TAX INVOICE / فاتورة ضريبية)
- رقم الفاتورة
- التاريخ
- تاريخ التوريد (إن وجد)

### 2. Seller Information (بيانات البائع)
```
✅ اسم الشركة (Company Name)
✅ الرقم الضريبي (VAT Number)
✅ رقم السجل التجاري (CR Number)
✅ الرقم الإضافي (Additional ID)
✅ العنوان الكامل (Building, Street, District, City, Postal Code, Country)
✅ البنك (Bank Name)
✅ رقم الحساب (Account Number)
```

### 3. Buyer Information (بيانات المشتري)
```
✅ اسم العميل (Customer Name)
✅ الرقم الضريبي (VAT Number)
✅ رقم السجل التجاري (CR Number)
✅ الرقم الإضافي (Additional ID)
✅ العنوان الكامل (Building, Street, District, City, Postal Code, Country)
✅ رقم الهاتف (Phone)
```

### 4. Additional Information (معلومات إضافية)
```
✅ رقم أمر الشراء (PO Number)
✅ رقم العقد (Contract Number)
✅ شروط الدفع (Payment Terms)
✅ طريقة الدفع (Payment Means)
```

### 5. Products Table (جدول المنتجات)
```
# | Product Name | Qty | Unit Price | Subtotal | VAT % | VAT Amount | Total
```

### 6. Summary (الملخص)
```
✅ Subtotal (المجموع الفرعي)
✅ VAT (الضريبة)
✅ Discount (الخصم)
✅ Shipping (الشحن)
✅ Total Amount (المجموع الكلي)
```

### 7. QR Code
- رمز QR للتحقق من الفاتورة

### 8. Footer
- ملاحظات ختامية

---

## 🎨 التصميم

### الألوان:
- **Header**: خلفية سوداء مع نص أبيض
- **Info Boxes**: خلفية رمادية فاتحة (#f9f9f9)
- **Table**: صفوف متبادلة الألوان

### الخطوط:
- **العنوان**: 24px Bold
- **العناوين الفرعية**: 14px Bold
- **النص العادي**: 11-12px
- **الإجمالي**: 14px Bold

### التخطيط:
- **عرض الصفحة**: 210mm (A4)
- **Padding**: 10mm
- **Gap بين الأقسام**: 15-20px

---

## 🔧 كيفية الاستخدام

### في Controller:

```php
// For B2B invoices
if ($sale->invoice_type === 'b2b') {
    return view('business::sales.invoices.b2b-unified', compact('sale', 'setting'));
}
```

### في Blade:

```blade
@if($sale->invoice_type === 'b2b')
    @include('business::sales.invoices.b2b-unified')
@else
    @include('business::sales.invoices.a4-size')
@endif
```

---

## 📋 البيانات المطلوبة

### من Business:
- `companyName`
- `vat_no`
- `commercial_registration`
- `additional_id`
- `building_number`, `street_name`, `district`, `city`, `postal_code`, `country_code`
- `bank_name`, `bank_account_number`

### من Party:
- `name`
- `vat_number`
- `commercial_registration`
- `additional_id`
- `building_number`, `street_name`, `district`, `city`, `postal_code`, `country_code`
- `phone`

### من Sale:
- `invoiceNumber`, `saleDate`, `supply_date`
- `po_number`, `contract_number`, `payment_terms`, `payment_means`
- `totalAmount`, `vat_amount`, `discountAmount`, `shipping_charge`
- `qr_code`

---

## ⚠️ ملاحظات مهمة

### 1. البيانات الناقصة
- إذا كانت بيانات ناقصة، سيظهر "N/A"
- يُفضل ملء جميع البيانات قبل إنشاء الفاتورة

### 2. العنوان
- إذا كان العنوان المنظم موجود (building_number, street_name, etc.) → يُعرض بالتفصيل
- إذا لم يكن موجود → يُعرض حقل `address` العادي

### 3. الطباعة
- القالب جاهز للطباعة مباشرة
- يدعم A4 و Thermal
- يخفي العناصر غير المطلوبة عند الطباعة

### 4. RTL Support
- يدعم العربي بشكل كامل
- التخطيط يتغير تلقائياً حسب اللغة

---

## 🚀 الخطوة التالية

### 1. تحديث Controller
يجب تحديث `AcnooSaleController` لاستخدام القالب الجديد:

```php
public function invoice($id)
{
    $sale = Sale::with(['business', 'party', 'details.product', 'vat'])
        ->where('business_id', auth()->user()->business_id)
        ->findOrFail($id);
    
    $setting = Option::where('key', 'business-settings')
        ->whereJsonContains('value->business_id', auth()->user()->business_id)
        ->first();
    
    // Use unified template for B2B invoices
    if ($sale->invoice_type === 'b2b') {
        return view('business::sales.invoices.b2b-unified', compact('sale', 'setting'));
    }
    
    // Use regular template for B2C
    return view('business::sales.invoices.a4-size', compact('sale', 'setting'));
}
```

### 2. اختبار القالب
1. أنشئ فاتورة B2B جديدة
2. املأ جميع البيانات
3. اطبع الفاتورة
4. تحقق من ظهور جميع البيانات بشكل صحيح

---

## ✅ تم الانتهاء!

القالب الجديد جاهز للاستخدام ويعرض جميع البيانات بشكل مرتب ومنظم! 🎉
