# العناصر الناقصة في فواتير B2B مقارنة بالمتطلبات الرسمية

## بعد مراجعة الفواتير الموجودة مقارنة بالصورة الرسمية من الهيئة

---

## 1. العناصر الناقصة في الفاتورة A4 (b2b-invoice.blade.php)

### ✗ العناصر المفقودة تماماً:

1. **رقم السجل التجاري (CR Number)**
   - غير موجود في قاعدة البيانات
   - غير معروض في الفاتورة
   - **مطلوب**: إضافة حقل `commercial_registration` في جدول `businesses`

2. **Additional ID (معرف إضافي)**
   - غير موجود
   - **مطلوب**: إضافة حقل `additional_id` في جدول `businesses` و `parties`

3. **تفاصيل الدفع (Payment Means)**
   - لا يوجد تفصيل لطريقة الدفع (نقدي/آجل/بنكي)
   - لا يوجد رقم حساب بنكي إن كان الدفع بنكي
   - **مطلوب**: إضافة حقول تفصيلية لطرق الدفع

4. **شروط الدفع (Payment Terms)**
   - لا يوجد تحديد لشروط الدفع (فوري/30 يوم/60 يوم)
   - **مطلوب**: إضافة حقل `payment_terms` في جدول `sales`

5. **تاريخ التوريد (Supply Date)**
   - غير موجود (مختلف عن تاريخ الفاتورة)
   - **مطلوب**: إضافة حقل `supply_date` في جدول `sales`

6. **رقم أمر الشراء (Purchase Order Number)**
   - غير موجود
   - **مطلوب**: إضافة حقل `po_number` في جدول `sales`

7. **رقم العقد (Contract Number)**
   - غير موجود
   - **مطلوب**: إضافة حقل `contract_number` في جدول `sales`

### ⚠ العناصر الموجودة لكن ناقصة:

8. **جدول المنتجات - أعمدة ناقصة:**
   - ✗ رمز المنتج (Item Code)
   - ✗ وحدة القياس (Unit of Measure)
   - ✗ السعر قبل الخصم (List Price)
   - ✗ نسبة الخصم (Discount %)
   - ✗ السعر بعد الخصم (Net Price)
   - ✗ الضريبة لكل منتج (VAT per item)
   - ✗ سبب الإعفاء الضريبي (Tax Exemption Reason) - إن وجد

9. **ملخص الضرائب (Tax Summary):**
   - لا يوجد جدول منفصل يوضح:
     - نسبة الضريبة
     - المبلغ الخاضع للضريبة
     - قيمة الضريبة
     - الإجمالي شامل الضريبة
   - **الحالي**: فقط سطر واحد للضريبة

10. **معلومات الشحن:**
    - لا توجد تفاصيل عنوان الشحن إذا كان مختلف عن عنوان الفاتورة
    - **مطلوب**: إضافة حقول `shipping_address` منفصلة

---

## 2. العناصر الناقصة في فاتورة الاشتراك (subscribe-order/invoice.blade.php)

### نفس المشاكل السابقة بالإضافة إلى:

11. **معلومات الخدمة:**
    - لا يوجد رمز الخدمة (Service Code)
    - لا يوجد تاريخ بداية الخدمة
    - لا يوجد تاريخ نهاية الخدمة
    - **مطلوب**: إضافة هذه الحقول في جدول `plan_subscribes`

12. **الفترة الضريبية:**
    - لا يوجد تحديد للفترة الضريبية المغطاة
    - **مطلوب**: إضافة `tax_period_start` و `tax_period_end`

---

## 3. العناصر الناقصة في الفاتورة الحرارية (ThermalPrinterAddon)

### المشاكل الإضافية:

13. **مساحة محدودة:**
    - الفاتورة الحرارية لا تستوعب كل التفاصيل المطلوبة
    - **الحل**: عرض الحد الأدنى المطلوب فقط

14. **QR Code:**
    - ✓ موجود (جيد)
    - لكن يجب التأكد من احتوائه على جميع البيانات المطلوبة

---

## 4. الحقول المطلوب إضافتها في قاعدة البيانات

### جدول `businesses`:
```sql
ALTER TABLE businesses 
ADD COLUMN commercial_registration VARCHAR(50) AFTER vat_no,
ADD COLUMN additional_id VARCHAR(50) AFTER commercial_registration,
ADD COLUMN bank_account_number VARCHAR(50) AFTER additional_id,
ADD COLUMN bank_name VARCHAR(100) AFTER bank_account_number;
```

### جدول `parties`:
```sql
ALTER TABLE parties 
ADD COLUMN commercial_registration VARCHAR(50) AFTER vat_number,
ADD COLUMN additional_id VARCHAR(50) AFTER commercial_registration;
```

### جدول `sales`:
```sql
ALTER TABLE sales 
ADD COLUMN supply_date DATE AFTER saleDate,
ADD COLUMN po_number VARCHAR(50) AFTER supply_date,
ADD COLUMN contract_number VARCHAR(50) AFTER po_number,
ADD COLUMN payment_terms VARCHAR(100) AFTER contract_number,
ADD COLUMN payment_means VARCHAR(50) AFTER payment_terms,
ADD COLUMN shipping_address_line1 VARCHAR(255) AFTER payment_means,
ADD COLUMN shipping_address_line2 VARCHAR(255) AFTER shipping_address_line1,
ADD COLUMN shipping_city VARCHAR(100) AFTER shipping_address_line2,
ADD COLUMN shipping_postal_code VARCHAR(20) AFTER shipping_city;
```

### جدول `sale_details`:
```sql
ALTER TABLE sale_details 
ADD COLUMN item_code VARCHAR(50) AFTER product_id,
ADD COLUMN unit_of_measure VARCHAR(20) AFTER item_code,
ADD COLUMN list_price DECIMAL(10,2) AFTER price,
ADD COLUMN discount_percent DECIMAL(5,2) AFTER list_price,
ADD COLUMN net_price DECIMAL(10,2) AFTER discount_percent,
ADD COLUMN tax_per_item DECIMAL(10,2) AFTER net_price,
ADD COLUMN tax_exemption_reason VARCHAR(255) AFTER tax_per_item;
```

### جدول `plan_subscribes`:
```sql
ALTER TABLE plan_subscribes 
ADD COLUMN service_code VARCHAR(50) AFTER plan_id,
ADD COLUMN service_start_date DATE AFTER service_code,
ADD COLUMN service_end_date DATE AFTER service_start_date,
ADD COLUMN tax_period_start DATE AFTER service_end_date,
ADD COLUMN tax_period_end DATE AFTER tax_period_start;
```

---

## 5. التحسينات المطلوبة في التصميم

### الفاتورة A4:

1. **إضافة جدول ملخص الضرائب:**
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

2. **تحسين جدول المنتجات:**
```
┌────┬──────────┬─────────────┬─────┬──────┬──────────┬─────────┬──────────┬─────────┬──────────┐
│ #  │ Code     │ Description │ UoM │ Qty  │ List Pr. │ Disc %  │ Net Pr.  │ VAT     │ Total    │
│    │ الرمز    │ الوصف       │ وحدة│ كمية │ السعر    │ خصم     │ الصافي   │ ضريبة   │ إجمالي   │
└────┴──────────┴─────────────┴─────┴──────┴──────────┴─────────┴──────────┴─────────┴──────────┘
```

3. **إضافة قسم معلومات الدفع:**
```
┌─────────────────────────────────────────────────────────────┐
│ Payment Information / معلومات الدفع                         │
├─────────────────────────────────────────────────────────────┤
│ Payment Terms: Net 30 Days / شروط الدفع: 30 يوم            │
│ Payment Means: Bank Transfer / طريقة الدفع: تحويل بنكي     │
│ Bank Account: SA1234567890 / الحساب البنكي                 │
│ PO Number: PO-2024-001 / رقم أمر الشراء                    │
└─────────────────────────────────────────────────────────────┘
```

4. **إضافة معلومات إضافية:**
```
┌─────────────────────────────────────────────────────────────┐
│ Additional Information / معلومات إضافية                     │
├─────────────────────────────────────────────────────────────┤
│ CR Number: 1234567890 / السجل التجاري                      │
│ Supply Date: 2024-01-29 / تاريخ التوريد                    │
│ Contract No: CNT-2024-001 / رقم العقد                      │
└─────────────────────────────────────────────────────────────┘
```

---

## 6. الأولويات

### أولوية عالية (High Priority):
1. ✓ رقم السجل التجاري (CR Number)
2. ✓ تفاصيل الدفع (Payment Means & Terms)
3. ✓ جدول ملخص الضرائب (Tax Summary)
4. ✓ تحسين جدول المنتجات (Product Table Enhancement)

### أولوية متوسطة (Medium Priority):
5. ✓ تاريخ التوريد (Supply Date)
6. ✓ رقم أمر الشراء (PO Number)
7. ✓ Additional ID
8. ✓ معلومات الشحن (Shipping Details)

### أولوية منخفضة (Low Priority):
9. رقم العقد (Contract Number)
10. الفترة الضريبية (Tax Period)

---

## 7. الخطوات التالية

1. **إنشاء Migration لإضافة الحقول الجديدة**
2. **تحديث Models لتشمل الحقول الجديدة**
3. **تحديث Forms لإدخال البيانات الجديدة**
4. **تحديث قوالب الفواتير لعرض البيانات الجديدة**
5. **اختبار الفواتير مع البيانات الكاملة**

---

## 8. ملاحظات مهمة

- ⚠ **بعض الحقول اختيارية** حسب نوع المعاملة
- ⚠ **الفاتورة الحرارية** قد لا تستوعب كل التفاصيل (مساحة محدودة)
- ✓ **QR Code** موجود ويعمل بشكل صحيح
- ✓ **البيانات الأساسية** موجودة (الأسماء، العناوين، الأرقام الضريبية)

---

## 9. المراجع

- [دليل الفوترة الإلكترونية - الهيئة العامة للزكاة والضريبة والجمارك](https://zatca.gov.sa)
- [متطلبات الفاتورة الضريبية B2B](https://zatca.gov.sa/ar/E-Invoicing/Introduction/Guidelines/Documents/E-Invoicing_Detailed_Guidelines_AR.pdf)
