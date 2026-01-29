# كيفية ملء بيانات B2B
# How to Fill B2B Data

## 📋 المشكلة / Problem

عند عرض فاتورة B2B، بعض الحقول تظهر `---` لأن البيانات غير متوفرة.

When viewing a B2B invoice, some fields show `---` because the data is not available.

---

## ✅ الحل / Solution

يجب ملء البيانات في مكانين:

You need to fill data in two places:

### 1. بيانات الشركة (Seller) / Company Data (Seller)
### 2. بيانات العميل (Buyer) / Customer Data (Buyer)

---

## 📍 الخطوة 1: ملء بيانات الشركة / Step 1: Fill Company Data

### المسار / Path:
```
Settings > General Settings
الإعدادات > الإعدادات العامة
```

### الحقول المطلوبة / Required Fields:

#### معلومات أساسية / Basic Information:
- ✅ **Company Name** / اسم الشركة
- ✅ **VAT Number** / الرقم الضريبي (15 رقم)
- ✅ **Phone** / الهاتف
- ✅ **Email** / البريد الإلكتروني

#### عنوان B2B / B2B Address:
- ✅ **Building Number** / رقم المبنى
- ✅ **Street Name** / اسم الشارع
- ✅ **District** / الحي
- ✅ **City** / المدينة
- ✅ **Postal Code** / الرمز البريدي
- ✅ **Country Code** / رمز الدولة (مثال: SA, EG, AE)

### مثال / Example:

```
Company Name: codgoo
VAT Number: 123456789101213
Phone: 01028343913
Email: samerhassan@gmail.com

Building Number: 123
Street Name: King Fahd Road
District: Al Olaya
City: Riyadh
Postal Code: 11564
Country Code: SA
```

---

## 📍 الخطوة 2: ملء بيانات العميل / Step 2: Fill Customer Data

### المسار / Path:
```
Parties > Add New Party (or Edit existing)
العملاء > إضافة عميل جديد (أو تعديل موجود)
```

### الحقول المطلوبة / Required Fields:

#### معلومات أساسية / Basic Information:
- ✅ **Name** / الاسم
- ✅ **Phone** / الهاتف
- ✅ **Type** / النوع: اختر Retailer, Dealer, أو Wholesaler
- ✅ **ZATCA Type**: اختر **B2B** (مهم جداً!)

#### عنوان B2B (يظهر فقط عند اختيار B2B):
- ✅ **VAT Number** / الرقم الضريبي (15 رقم - إلزامي)
- ✅ **Building Number** / رقم المبنى
- ✅ **Street Name** / اسم الشارع
- ✅ **District** / الحي
- ✅ **City** / المدينة
- ✅ **Postal Code** / الرمز البريدي
- ✅ **Country Code** / رمز الدولة

### مثال / Example:

```
Name: علي محمود
Phone: 753951852
Type: Retailer
ZATCA Type: B2B

VAT Number: 987654321012345
Building Number: 456
Street Name: Prince Mohammed Bin Abdulaziz St
District: Al Malqa
City: Riyadh
Postal Code: 13521
Country Code: SA
```

---

## 🎯 التحقق من البيانات / Verify Data

### بعد ملء البيانات / After Filling Data:

1. **اذهب إلى** / Go to: `Sales > Create Sale`
2. **اختر عميل B2B** / Select B2B customer
3. **أضف منتجات** / Add products
4. **أكمل البيع** / Complete sale
5. **اعرض الفاتورة** / View invoice

### يجب أن تظهر / Should Display:

#### في صندوق البائع / In Seller Box:
```
Company Name: codgoo
VAT Number: 123456789101213
Building No: 123
Street: King Fahd Road
District: Al Olaya
City: Riyadh
Postal Code: 11564
Country: SA
Phone: 01028343913
Email: samerhassan@gmail.com
```

#### في صندوق المشتري / In Buyer Box:
```
Company Name: علي محمود
VAT Number: 987654321012345
Building No: 456
Street: Prince Mohammed Bin Abdulaziz St
District: Al Malqa
City: Riyadh
Postal Code: 13521
Country: SA
Phone: 753951852
```

---

## ⚠️ ملاحظات مهمة / Important Notes

### 1. الرقم الضريبي / VAT Number
- ✅ يجب أن يكون **15 رقم بالضبط**
- ✅ Must be **exactly 15 digits**
- ❌ لا يقبل أحرف أو رموز
- ❌ No letters or symbols allowed

### 2. رمز الدولة / Country Code
- ✅ حرفين فقط (ISO 3166-1 alpha-2)
- ✅ Only 2 letters (ISO 3166-1 alpha-2)
- ✅ أمثلة / Examples: SA, EG, AE, US, GB

### 3. نوع ZATCA
- ✅ **B2B**: للشركات (يتطلب رقم ضريبي وعنوان كامل)
- ✅ **B2C**: للأفراد (لا يتطلب رقم ضريبي)

### 4. الحقول الإلزامية / Required Fields
- عند اختيار **B2B**، جميع الحقول **إلزامية**
- When selecting **B2B**, all fields are **required**

---

## 🔧 حل المشاكل / Troubleshooting

### المشكلة 1: الحقول لا تظهر
**Problem 1: Fields don't appear**

**الحل / Solution:**
```bash
php artisan view:clear
php artisan cache:clear
```

### المشكلة 2: البيانات لا تحفظ
**Problem 2: Data doesn't save**

**الحل / Solution:**
1. تأكد من تشغيل الـ migration
2. تأكد من أن الحقول موجودة في قاعدة البيانات
3. راجع ملف `INSTALLATION_B2B.md`

### المشكلة 3: الفاتورة تظهر B2C بدلاً من B2B
**Problem 3: Invoice shows B2C instead of B2B**

**الحل / Solution:**
1. تأكد من اختيار **ZATCA Type: B2B** للعميل
2. تأكد من ملء **VAT Number** للعميل
3. أعد إنشاء الفاتورة

---

## 📊 جدول الحقول / Fields Table

| الحقل / Field | البائع / Seller | المشتري / Buyer | إلزامي / Required |
|--------------|-----------------|-----------------|------------------|
| Company Name | ✅ | ✅ | نعم / Yes |
| VAT Number | ✅ | ✅ (B2B only) | نعم / Yes |
| Building Number | ✅ | ✅ (B2B only) | نعم / Yes |
| Street Name | ✅ | ✅ (B2B only) | نعم / Yes |
| District | ✅ | ✅ (B2B only) | نعم / Yes |
| City | ✅ | ✅ (B2B only) | نعم / Yes |
| Postal Code | ✅ | ✅ (B2B only) | نعم / Yes |
| Country Code | ✅ | ✅ (B2B only) | نعم / Yes |
| Phone | ✅ | ✅ | نعم / Yes |
| Email | ✅ | ❌ | لا / No |

---

## 🎓 فيديو توضيحي / Tutorial Video

### خطوات بالصور / Steps with Images:

#### 1. إعدادات الشركة / Company Settings
```
1. اذهب إلى: Settings > General Settings
2. املأ جميع الحقول في قسم "B2B Invoice Address Details"
3. احفظ التغييرات
```

#### 2. إضافة عميل B2B / Add B2B Customer
```
1. اذهب إلى: Parties > Add New Party
2. املأ الاسم والهاتف
3. اختر ZATCA Type: B2B
4. املأ جميع حقول B2B التي ستظهر
5. احفظ
```

#### 3. إنشاء فاتورة / Create Invoice
```
1. اذهب إلى: Sales > Create Sale
2. اختر العميل B2B
3. أضف المنتجات
4. أكمل البيع
5. اعرض الفاتورة
```

---

## 📞 الدعم / Support

إذا واجهت أي مشكلة:

If you face any issues:

1. راجع ملف `TROUBLESHOOTING_B2B.md`
2. راجع ملف `FAQ_B2B.md`
3. تأكد من تشغيل جميع الأوامر المطلوبة
4. تحقق من قاعدة البيانات

---

## ✅ قائمة التحقق / Checklist

قبل إنشاء فاتورة B2B، تأكد من:

Before creating a B2B invoice, make sure:

- [ ] تم ملء بيانات الشركة كاملة
- [ ] تم ملء بيانات العميل كاملة
- [ ] تم اختيار ZATCA Type: B2B للعميل
- [ ] الرقم الضريبي 15 رقم للطرفين
- [ ] رمز الدولة حرفين فقط
- [ ] تم مسح الـ cache

---

**الآن يمكنك إنشاء فواتير B2B كاملة! ✅**
**Now you can create complete B2B invoices! ✅**
