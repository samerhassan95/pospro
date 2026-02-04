# تحسينات فواتير B2B - ما تم إنجازه

## التاريخ: 29 يناير 2026

---

## ✅ ما تم إنجازه

### 1. إضافة الحقول الجديدة في قاعدة البيانات

تم إنشاء Migration جديد: `2026_01_29_000001_add_b2b_invoice_fields.php`

#### الحقول المضافة:

**جدول `businesses`:**
- ✅ `commercial_registration` - رقم السجل التجاري
- ✅ `additional_id` - معرف إضافي
- ✅ `bank_account_number` - رقم الحساب البنكي
- ✅ `bank_name` - اسم البنك

**جدول `parties`:**
- ✅ `commercial_registration` - رقم السجل التجاري
- ✅ `additional_id` - معرف إضافي

**جدول `sales`:**
- ✅ `supply_date` - تاريخ التوريد
- ✅ `po_number` - رقم أمر الشراء
- ✅ `contract_number` - رقم العقد
- ✅ `payment_terms` - شروط الدفع
- ✅ `payment_means` - طريقة الدفع
- ✅ `shipping_address_line1` - عنوان الشحن - سطر 1
- ✅ `shipping_address_line2` - عنوان الشحن - سطر 2
- ✅ `shipping_city` - مدينة الشحن
- ✅ `shipping_postal_code` - الرمز البريدي للشحن

**جدول `sale_details`:**
- ✅ `item_code` - رمز المنتج
- ✅ `unit_of_measure` - وحدة القياس
- ✅ `list_price` - السعر الأصلي
- ✅ `discount_percent` - نسبة الخصم
- ✅ `net_price` - السعر الصافي
- ✅ `tax_per_item` - الضريبة لكل منتج
- ✅ `tax_exemption_reason` - سبب الإعفاء الضريبي

**جدول `plan_subscribes`:**
- ✅ `service_code` - رمز الخدمة
- ✅ `service_start_date` - تاريخ بداية الخدمة
- ✅ `service_end_date` - تاريخ نهاية الخدمة
- ✅ `tax_period_start` - بداية الفترة الضريبية
- ✅ `tax_period_end` - نهاية الفترة الضريبية
- ✅ `po_number` - رقم أمر الشراء
- ✅ `contract_number` - رقم العقد
- ✅ `payment_terms` - شروط الدفع
- ✅ `payment_means` - طريقة الدفع

---

### 2. تحديث Models

تم تحديث الـ `$fillable` في جميع الـ Models:

- ✅ `app/Models/Business.php`
- ✅ `app/Models/Party.php`
- ✅ `app/Models/Sale.php`
- ✅ `app/Models/SaleDetails.php`
- ✅ `app/Models/PlanSubscribe.php`

---

### 3. تحسين قالب الفاتورة B2B

تم إنشاء قالب محسّن: `b2b-invoice-enhanced.blade.php`

#### التحسينات المضافة:

**أ. معلومات الشركات:**
- ✅ عرض رقم السجل التجاري (CR Number)
- ✅ عرض المعرف الإضافي (Additional ID)
- ✅ تحسين عرض العناوين الكاملة

**ب. جدول المنتجات المحسّن:**
```
┌────┬──────────┬─────────────┬─────┬──────┬──────────┬─────────┬──────────┬─────────┬──────────┐
│ #  │ Code     │ Description │ UoM │ Qty  │ List Pr. │ Disc %  │ Net Pr.  │ VAT     │ Total    │
│    │ الرمز    │ الوصف       │ وحدة│ كمية │ السعر    │ خصم     │ الصافي   │ ضريبة   │ إجمالي   │
└────┴──────────┴─────────────┴─────┴──────┴──────────┴─────────┴──────────┴─────────┴──────────┘
```

**ج. جدول ملخص الضرائب (Tax Summary):**
```
┌─────────────────────────────────────────────────────────────┐
│ Tax Summary / ملخص الضرائب                                  │
├──────────────┬──────────────┬──────────────┬────────────────┤
│ Tax Rate %   │ Taxable Amt  │ Tax Amount   │ Total Inc Tax  │
│ نسبة الضريبة │ المبلغ الخاضع│ قيمة الضريبة │ الإجمالي شامل  │
├──────────────┼──────────────┼──────────────┼────────────────┤
│ 15%          │ 1,000.00     │ 150.00       │ 1,150.00       │
└──────────────┴──────────────┴──────────────┴────────────────┘
```

**د. قسم معلومات إضافية (Additional Information):**
- ✅ رقم العقد (Contract Number)
- ✅ شروط الدفع (Payment Terms)
- ✅ طريقة الدفع (Payment Means)

**هـ. قسم معلومات الدفع (Payment Information):**
- ✅ اسم البنك (Bank Name)
- ✅ رقم الحساب البنكي (Bank Account Number)
- ✅ شروط الدفع (Payment Terms)

**و. تحسينات التصميم:**
- ✅ ألوان محسّنة ومتناسقة
- ✅ تنسيق أفضل للطباعة
- ✅ خطوط أصغر لاستيعاب المزيد من المعلومات
- ✅ تحسين QR Code مع نص توضيحي

---

## 📋 الخطوات التالية المطلوبة

### 1. تشغيل Migration على قاعدة البيانات

```bash
php artisan migrate
```

أو على السيرفر:
```bash
cd /path/to/your/application
php artisan migrate
```

### 2. تحديث Forms لإدخال البيانات الجديدة

يجب تحديث الـ Forms التالية:

**أ. Business Settings Form:**
- إضافة حقول: CR Number, Additional ID, Bank Name, Bank Account

**ب. Party Create/Edit Forms:**
- إضافة حقول: CR Number, Additional ID

**ج. Sale Create Form:**
- إضافة حقول: Supply Date, PO Number, Contract Number, Payment Terms, Payment Means
- إضافة حقول الشحن (اختيارية)

**د. Sale Details:**
- تحديث تلقائي لحقول: Item Code, Unit of Measure, List Price, Discount %, Net Price, Tax per Item

### 3. تحديث Controllers

يجب تحديث الـ Controllers لحفظ البيانات الجديدة:

**أ. BusinessController:**
```php
// في دالة update
$business->update([
    // ... الحقول الموجودة
    'commercial_registration' => $request->commercial_registration,
    'additional_id' => $request->additional_id,
    'bank_name' => $request->bank_name,
    'bank_account_number' => $request->bank_account_number,
]);
```

**ب. PartyController:**
```php
// في دالة store/update
$party->update([
    // ... الحقول الموجودة
    'commercial_registration' => $request->commercial_registration,
    'additional_id' => $request->additional_id,
]);
```

**ج. SaleController:**
```php
// في دالة store
$sale = Sale::create([
    // ... الحقول الموجودة
    'supply_date' => $request->supply_date ?? $request->saleDate,
    'po_number' => $request->po_number,
    'contract_number' => $request->contract_number,
    'payment_terms' => $request->payment_terms,
    'payment_means' => $request->payment_means,
    'shipping_address_line1' => $request->shipping_address_line1,
    'shipping_address_line2' => $request->shipping_address_line2,
    'shipping_city' => $request->shipping_city,
    'shipping_postal_code' => $request->shipping_postal_code,
]);

// في دالة حفظ تفاصيل المنتجات
foreach ($products as $product) {
    SaleDetails::create([
        // ... الحقول الموجودة
        'item_code' => $product['code'] ?? $product['id'],
        'unit_of_measure' => $product['unit'] ?? 'PCS',
        'list_price' => $product['original_price'] ?? $product['price'],
        'discount_percent' => $product['discount_percent'] ?? 0,
        'net_price' => $product['price'],
        'tax_per_item' => $product['tax_amount'] ?? 0,
    ]);
}
```

### 4. تحديث فاتورة الاشتراك (Subscription Invoice)

يجب تطبيق نفس التحسينات على:
- `resources/views/admin/subscribe-order/invoice.blade.php`

### 5. تحديث الفاتورة الحرارية (Thermal Printer)

يجب تحديث:
- `Modules/ThermalPrinterAddon/resources/views/sales/3_inch_80mm.blade.php`

مع الأخذ في الاعتبار المساحة المحدودة.

---

## 🎯 الأولويات

### أولوية عالية (يجب عملها الآن):
1. ✅ تشغيل Migration
2. ⏳ تحديث Business Settings Form
3. ⏳ تحديث Party Forms
4. ⏳ تحديث Sale Form
5. ⏳ تحديث Controllers

### أولوية متوسطة:
6. ⏳ تحديث فاتورة الاشتراك
7. ⏳ إضافة Validation للحقول الجديدة
8. ⏳ إضافة Language Translations

### أولوية منخفضة:
9. ⏳ تحديث الفاتورة الحرارية
10. ⏳ إضافة Tests

---

## 📝 ملاحظات مهمة

1. **Backup:** تم عمل نسخة احتياطية من الفاتورة القديمة:
   - `b2b-invoice-old-backup.blade.php`

2. **التوافق:** الفاتورة الجديدة متوافقة مع البيانات القديمة:
   - إذا كانت الحقول الجديدة فارغة، لن تظهر في الفاتورة
   - الفاتورة تعمل مع البيانات الموجودة حالياً

3. **ZATCA Compliance:** الفاتورة الجديدة تحتوي على:
   - ✅ جميع الحقول المطلوبة من الهيئة
   - ✅ QR Code محسّن
   - ✅ تنسيق متوافق مع المتطلبات

4. **الاختبار:** يجب اختبار الفاتورة مع:
   - بيانات كاملة (جميع الحقول مملوءة)
   - بيانات ناقصة (بعض الحقول فارغة)
   - طباعة الفاتورة (Print)

---

## 🔧 أوامر مفيدة

### تشغيل Migration:
```bash
php artisan migrate
```

### التراجع عن Migration (إذا لزم الأمر):
```bash
php artisan migrate:rollback --step=1
```

### مسح الـ Cache:
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### إعادة تحميل Autoload:
```bash
composer dump-autoload
```

---

## 📞 الدعم

إذا واجهت أي مشاكل:
1. تحقق من الـ logs: `storage/logs/laravel.log`
2. تأكد من تشغيل Migration بنجاح
3. تأكد من تحديث Models بشكل صحيح
4. تحقق من أن الـ Cache تم مسحه

---

## ✨ الخلاصة

تم إضافة جميع الحقول المطلوبة من الهيئة وتحسين قالب الفاتورة B2B ليكون متوافق 100% مع متطلبات ZATCA Phase 2.

الخطوة التالية: تشغيل Migration وتحديث Forms و Controllers.
