# الخطوات التالية لإكمال تطبيق فواتير B2B

## ✅ تم إنجازه

1. ✅ إضافة حقول B2B إلى قاعدة البيانات (Migration)
2. ✅ تحديث Models (Party, Business, Sale)
3. ✅ تحديث صفحات إضافة وتعديل العملاء
4. ✅ إضافة Validation في Controller
5. ✅ إضافة JavaScript لإظهار/إخفاء الحقول
6. ✅ إنشاء Seeder لتحديث البيانات الموجودة

## 🔄 المطلوب إنجازه

### 1. تحديث UBL Generator (عالي الأولوية)
**الملف**: `app/Services/Zatca/UblGenerator.php`

**المطلوب**:
```php
// إضافة دالة جديدة لتوليد B2B Invoice
public function generateB2BInvoice($sale, $business, $party)
{
    // إضافة معلومات العميل الكاملة
    // - الرقم الضريبي
    // - العنوان الكامل (building_number, street_name, district, city, postal_code, country_code)
    // - معلومات الاتصال
    
    // استخدام UBL 2.1 Standard Invoice بدلاً من Simplified Invoice
}
```

**الفرق بين B2C و B2B في UBL**:
- B2C: يستخدم `<Invoice>` مع `InvoiceTypeCode = 388` (Simplified)
- B2B: يستخدم `<Invoice>` مع `InvoiceTypeCode = 381` (Standard)
- B2B يتطلب إضافة `<cac:AccountingCustomerParty>` كاملة

### 2. تحديث ZATCA Service (عالي الأولوية)
**الملف**: `app/Services/Zatca/ZatcaService.php`

**المطلوب**:
```php
// إضافة دالة لإرسال B2B Invoice
public function clearInvoice($signedXml, $uuid, $invoiceHash, $zatcaSettings)
{
    // استخدام endpoint مختلف للـ B2B
    // POST /clearances/invoices
    // بدلاً من /invoices/reporting/single
}

// تحديث دالة reportInvoice لتحديد النوع
public function processInvoice($sale, $business, $party)
{
    if ($sale->invoice_type === 'b2b') {
        return $this->clearInvoice(...);
    } else {
        return $this->reportInvoice(...);
    }
}
```

### 3. تحديث Sale Controller (عالي الأولوية)
**الملف**: `Modules/Business/App/Http/Controllers/SaleController.php` (أو ما يشابهه)

**المطلوب**:
```php
public function store(Request $request)
{
    // عند إنشاء فاتورة جديدة
    $party = Party::find($request->party_id);
    
    $sale = Sale::create([
        // ... باقي الحقول
        'invoice_type' => $party->zatca_type, // تلقائياً من نوع العميل
    ]);
    
    // إرسال إلى ZATCA حسب النوع
    if ($sale->invoice_type === 'b2b') {
        // Clear Invoice (B2B)
        $zatcaService->clearInvoice($sale, $business, $party);
    } else {
        // Report Invoice (B2C)
        $zatcaService->reportInvoice($sale, $business, $party);
    }
}
```

### 4. تحديث PDF Template (متوسط الأولوية)
**الملف**: `resources/views/business/sales/invoice-pdf.blade.php` (أو ما يشابهه)

**المطلوب**:
- إضافة عرض نوع الفاتورة (B2C أو B2B)
- عرض معلومات العميل الكاملة للفواتير B2B:
  ```blade
  @if($sale->invoice_type === 'b2b')
      <div class="customer-details">
          <h4>{{ __('Customer Information') }}</h4>
          <p>{{ __('VAT Number') }}: {{ $party->vat_number }}</p>
          <p>{{ __('Address') }}: 
              {{ $party->building_number }}, 
              {{ $party->street_name }}, 
              {{ $party->district }}, 
              {{ $party->city }}, 
              {{ $party->postal_code }}, 
              {{ $party->country_code }}
          </p>
      </div>
  @endif
  ```

### 5. تحديث صفحة إنشاء الفاتورة (متوسط الأولوية)
**الملف**: `Modules/Business/resources/views/sales/create.blade.php`

**المطلوب**:
```blade
{{-- إضافة حقل نوع الفاتورة --}}
<div class="col-lg-6 mb-2">
    <label>{{ __('Invoice Type') }}</label>
    <select name="invoice_type" id="invoice_type" class="form-control">
        <option value="b2c">{{ __('B2C - Simplified Invoice') }}</option>
        <option value="b2b">{{ __('B2B - Tax Invoice') }}</option>
    </select>
</div>

{{-- JavaScript لتحديد النوع تلقائياً عند اختيار العميل --}}
<script>
$('#party_id').on('change', function() {
    const partyId = $(this).val();
    $.get('/api/parties/' + partyId, function(party) {
        $('#invoice_type').val(party.zatca_type);
    });
});
</script>
```

### 6. إضافة API Endpoint للعملاء (منخفض الأولوية)
**الملف**: `routes/api.php`

**المطلوب**:
```php
Route::get('/parties/{id}', function($id) {
    return Party::findOrFail($id);
})->middleware('auth:sanctum');
```

### 7. تحديث Business Settings (منخفض الأولوية)
**الملف**: صفحة إعدادات الشركة

**المطلوب**:
- إضافة حقول العنوان للشركة:
  - Building Number
  - Street Name
  - District
  - City
  - Postal Code
  - Country Code

### 8. Testing (عالي الأولوية)
**المطلوب**:
1. اختبار إنشاء عميل B2B
2. اختبار إنشاء فاتورة B2B
3. اختبار إرسال فاتورة B2B إلى ZATCA
4. اختبار PDF للفاتورة B2B
5. اختبار التحويل من B2C إلى B2B والعكس

## 📋 Checklist للتطبيق الكامل

- [ ] تحديث UBL Generator لدعم B2B
- [ ] تحديث ZATCA Service لإرسال B2B
- [ ] تحديث Sale Controller لتحديد النوع تلقائياً
- [ ] تحديث PDF Template
- [ ] تحديث صفحة إنشاء الفاتورة
- [ ] إضافة API Endpoint للعملاء
- [ ] تحديث Business Settings
- [ ] اختبار شامل للنظام
- [ ] توثيق API
- [ ] تدريب المستخدمين

## 🔍 نقاط مهمة للانتباه

1. **ZATCA Endpoints**:
   - B2C (Report): `/invoices/reporting/single`
   - B2B (Clear): `/clearances/invoices`

2. **Invoice Type Codes**:
   - B2C: `388` (Simplified Tax Invoice)
   - B2B: `381` (Standard Tax Invoice)

3. **Validation**:
   - التأكد من أن الرقم الضريبي 15 رقم بالضبط
   - التأكد من أن جميع حقول العنوان مملوءة للـ B2B

4. **Error Handling**:
   - إضافة رسائل خطأ واضحة إذا كانت بيانات العميل ناقصة
   - التعامل مع أخطاء ZATCA بشكل صحيح

## 📚 مراجع مفيدة

1. [ZATCA E-Invoicing Documentation](https://zatca.gov.sa/en/E-Invoicing/Pages/default.aspx)
2. [UBL 2.1 Standard](http://docs.oasis-open.org/ubl/UBL-2.1.html)
3. [ZATCA SDK Documentation](https://zatca.gov.sa/en/E-Invoicing/SystemsDevelopers/Pages/TechnicalRequirements.aspx)

## 🚀 أولويات التنفيذ

### المرحلة 1 (الأساسية):
1. تحديث UBL Generator
2. تحديث ZATCA Service
3. تحديث Sale Controller

### المرحلة 2 (التحسينات):
4. تحديث PDF Template
5. تحديث صفحة إنشاء الفاتورة

### المرحلة 3 (الإضافات):
6. API Endpoint
7. Business Settings
8. Testing الشامل
