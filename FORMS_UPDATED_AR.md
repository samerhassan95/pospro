# تحديث الفورمات - Forms Updated

## ✅ ما تم إنجازه

تم تحديث فورمات إدخال البيانات لتشمل جميع الحقول المطلوبة لفواتير ZATCA B2B.

---

## 1. فورم إنشاء تاجر جديد
**الرابط:** `http://127.0.0.1:8000/admin/business/create`

### الحقول المضافة:

#### بيانات ضريبية (ZATCA / Tax Information):
- ✅ **VAT Number** (الرقم الضريبي) - 15 digits
- ✅ **Commercial Registration** (رقم السجل التجاري) - 10 digits
- ✅ **Additional ID** (معرف إضافي) - 6 digits (Optional)
- ✅ **Country Code** (رمز الدولة) - 2 letters (e.g., SA)
- ✅ **Building Number** (رقم المبنى)
- ✅ **Street Name** (اسم الشارع)
- ✅ **District** (الحي)
- ✅ **City** (المدينة)
- ✅ **Postal Code** (الرمز البريدي)

#### معلومات البنك (Bank Information):
- ✅ **Bank Name** (اسم البنك) - Optional
- ✅ **Bank Account Number** (رقم الحساب البنكي) - IBAN format (Optional)

---

## 2. فورم إعدادات ZATCA للنظام
**الرابط:** `http://127.0.0.1:8000/admin/zatca-subscription-settings`

### الحقول المضافة:

#### بيانات مالك النظام (System Owner Details):
- ✅ **Environment Mode** (Sandbox / Simulation / Production)
- ✅ **Common Name** (Company Name)
- ✅ **VAT Registration Number** - 15 digits
- ✅ **Commercial Registration Number** (رقم السجل التجاري) - 10 digits
- ✅ **Additional ID** (معرف إضافي) - 6 digits (Optional)
- ✅ **Location** (City)
- ✅ **Registered Address** (Street)
- ✅ **Organization Unit Name** (District)
- ✅ **Building Number**
- ✅ **Postal Code**
- ✅ **Country Code** (رمز الدولة) - 2 letters
- ✅ **Industry**

#### معلومات البنك (Bank Information):
- ✅ **Bank Name** (اسم البنك) - Optional
- ✅ **Bank Account Number** (رقم الحساب البنكي) - IBAN format (Optional)

---

## 3. استخدام البيانات في الفواتير

### فواتير المبيعات B2B (Sales Invoices):
تستخدم بيانات التاجر (Business) من الفورم الأول:
- اسم الشركة
- رقم ضريبة القيمة المضافة
- رقم السجل التجاري
- معرف إضافي
- العنوان الكامل
- معلومات البنك

### فواتير الاشتراك (Subscription Invoices):
تستخدم بيانات مالك النظام من الفورم الثاني:
- اسم النظام
- رقم ضريبة القيمة المضافة
- رقم السجل التجاري
- معرف إضافي
- العنوان الكامل
- معلومات البنك

---

## 4. مثال على البيانات

### للتاجر (Business):
```
Company Name: codgoo software
VAT Number: 300000000000003 (15 digits)
Commercial Registration: 1234567890 (10 digits)
Additional ID: 152034 (6 digits)
Country Code: SA
Building Number: 1234
Street Name: King Fahad Road
District: Riyadh Branch
City: Riyadh
Postal Code: 12345
Bank Name: البنك الأهلي السعودي
Bank Account: SA1234567890123456789012
```

### لمالك النظام (System Owner):
```
Common Name: POS System
VAT Number: 300000000000003 (15 digits)
Commercial Registration: 1234567890 (10 digits)
Additional ID: 152034 (6 digits)
Country Code: SA
Location: Riyadh
Street: King Fahad Road
District: HQ
Building Number: 1234
Postal Code: 12345
Industry: Software
Bank Name: البنك الأهلي السعودي
Bank Account: SA1234567890123456789012
```

---

## 5. ملاحظات مهمة

### الحقول الإلزامية:
- ✅ VAT Number (15 digits)
- ✅ Building Number
- ✅ Street Name
- ✅ District
- ✅ City
- ✅ Postal Code
- ✅ Country Code

### الحقول الاختيارية:
- Commercial Registration (مهم لفواتير B2B)
- Additional ID
- Bank Name
- Bank Account Number

### التنسيقات:
- **VAT Number**: 15 digits (e.g., 300000000000003)
- **Commercial Registration**: 10 digits (e.g., 1234567890)
- **Additional ID**: 6 digits (e.g., 152034)
- **Country Code**: 2 letters (e.g., SA)
- **IBAN**: Starts with SA followed by 22 digits

---

## 6. كيفية الاستخدام

### لإنشاء تاجر جديد:
1. اذهب إلى: `Admin → Business → Create Business`
2. املأ جميع الحقول الأساسية
3. املأ بيانات ZATCA الضريبية
4. املأ معلومات البنك (اختياري)
5. احفظ

### لتحديث إعدادات ZATCA:
1. اذهب إلى: `Admin → Settings → ZATCA Subscription Settings`
2. املأ بيانات مالك النظام
3. املأ معلومات البنك (اختياري)
4. احفظ

---

## 7. التحقق من البيانات

بعد ملء البيانات، تحقق من:
- ✅ الفواتير تعرض جميع البيانات بشكل صحيح
- ✅ QR Code يحتوي على البيانات الصحيحة
- ✅ الفواتير مطابقة لمتطلبات ZATCA

---

## 8. الملفات المحدثة

1. ✅ `resources/views/admin/business/create.blade.php`
   - إضافة حقول ZATCA الكاملة
   - إضافة معلومات البنك

2. ✅ `resources/views/admin/settings/zatca.blade.php`
   - إضافة حقول ZATCA الكاملة لمالك النظام
   - إضافة معلومات البنك

---

**تم التحديث:** 1 فبراير 2026
**الحالة:** ✅ جاهز للاستخدام
