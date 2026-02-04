# خطوات سريعة: إنشاء فاتورة B2B

## 🚀 الخطوات المختصرة

### 1️⃣ إعداد الشركة (مرة واحدة فقط)
```
Settings → General Settings
↓
املأ:
- VAT Number (15 رقم)
- CR Number (رقم السجل التجاري)
- Bank Name (اسم البنك)
- Bank Account (رقم الحساب)
- Building Number, Street, District, City, Postal Code, Country Code
↓
Save ✅
```

---

### 2️⃣ إضافة عميل B2B (مرة واحدة لكل عميل)
```
Parties → Add Customer
↓
املأ:
- Name (اسم العميل)
- Type (Retailer/Dealer/Wholesaler)
- ZATCA Type: B2B ⬅️ مهم!
- VAT Number (15 رقم) - مطلوب
- CR Number (اختياري)
- Building Number, Street, District, City, Postal Code, Country Code - مطلوب
↓
Save ✅
```

---

### 3️⃣ إنشاء فاتورة
```
Sales → Create Sale / POS
↓
1. اختر العميل B2B
2. أضف المنتجات
3. اضغط "B2B Additional Info" (اختياري):
   - Supply Date
   - PO Number
   - Payment Terms
   - Payment Means
4. Submit ✅
```

---

### 4️⃣ طباعة الفاتورة
```
Sales → All Sales
↓
ابحث عن الفاتورة
↓
اضغط Print/View
↓
الفاتورة B2B جاهزة! 🎉
```

---

## ✅ Checklist سريع

قبل إنشاء الفاتورة، تأكد من:

**الشركة (البائع):**
- [ ] VAT Number
- [ ] CR Number
- [ ] Bank Info
- [ ] Full Address

**العميل (المشتري):**
- [ ] ZATCA Type = B2B
- [ ] VAT Number
- [ ] Full Address

**الفاتورة:**
- [ ] Products Added
- [ ] VAT Calculated
- [ ] Payment Method Selected

---

## 🎯 النتيجة

الفاتورة ستحتوي على:
✅ معلومات البائع والمشتري كاملة
✅ جدول منتجات مفصل
✅ جدول ملخص الضرائب
✅ معلومات الدفع والبنك
✅ QR Code
✅ متوافقة مع ZATCA Phase 2

---

## 📞 مشكلة؟

- الفاتورة ليست B2B؟ → تأكد أن العميل ZATCA Type = B2B
- معلومات ناقصة؟ → راجع Settings و Party Info
- QR Code مفقود؟ → امسح Cache: `php artisan cache:clear`

---

**وبس! بسيطة 😊**
