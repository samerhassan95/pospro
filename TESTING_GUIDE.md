# 🧪 دليل الاختبار - حقول B2B

## ✅ تم التحديث!

تم تغيير الكود لاستخدام `onchange` مباشرة على الـ select بدلاً من الاعتماد على jQuery أو DOMContentLoaded.

---

## 🚀 خطوات الاختبار

### الخطوة 1: امسح الـ Cache
```bash
php artisan view:clear
php artisan cache:clear
```

### الخطوة 2: أعد تحميل الصفحة
- اضغط **Ctrl+Shift+R** (أو Cmd+Shift+R على Mac)
- هذا سيعيد تحميل الصفحة ويتجاهل الـ cache

### الخطوة 3: افتح صفحة إضافة عميل
```
http://localhost:8000/business/parties/create?type=Customer
```

### الخطوة 4: افتح Developer Tools
- اضغط **F12**
- اذهب إلى تبويب **Console**

### الخطوة 5: اختر B2B
1. في حقل "Invoice Type"
2. اختر **"B2B - Tax Invoice"**
3. **يجب أن تظهر الحقول فوراً!**

---

## 🔍 إذا لم تظهر الحقول

### الحل السريع (في Console):
```javascript
// اكتب هذا في Console واضغط Enter
toggleB2BFields('b2b')
```

إذا ظهرت رسالة خطأ مثل:
```
toggleB2BFields is not defined
```

معناها الـ JavaScript لم يتم تحميله. جرب:

```javascript
// اكتب هذا بدلاً منه
document.querySelectorAll('.b2b-field').forEach(function(f) { 
    f.style.display = 'block'; 
});
document.getElementById('vat_number_field').style.display = 'block';
```

---

## 🧪 اختبار بملف HTML

### افتح ملف الاختبار:
```
test-b2b-fields.html
```

1. افتح الملف في المتصفح
2. اختر "B2B - Tax Invoice"
3. يجب أن تظهر الحقول الزرقاء
4. اضغط زر "اختبار"
5. يجب أن تظهر رسالة نجاح خضراء

إذا نجح الاختبار في ملف HTML، معناها المشكلة في Laravel.

---

## 🔧 التشخيص

### 1. تحقق من وجود الـ select:
```javascript
// في Console
console.log(document.getElementById('zatca_type'));
// يجب أن يظهر: <select id="zatca_type" ...>
```

### 2. تحقق من وجود الحقول:
```javascript
// في Console
console.log(document.querySelectorAll('.b2b-field').length);
// يجب أن يظهر: 6 (عدد الحقول)
```

### 3. تحقق من الدالة:
```javascript
// في Console
console.log(typeof toggleB2BFields);
// يجب أن يظهر: function
```

### 4. اختبر الدالة يدوياً:
```javascript
// في Console
toggleB2BFields('b2b');
// يجب أن تظهر الحقول
```

---

## 📋 Checklist

قبل الاختبار، تأكد من:

- [ ] شغّلت `php artisan view:clear`
- [ ] أعدت تحميل الصفحة بـ Ctrl+Shift+R
- [ ] فتحت Developer Tools (F12)
- [ ] أنت في صفحة إضافة عميل الصحيحة
- [ ] لا توجد أخطاء في Console

---

## 🎯 النتيجة المتوقعة

عند اختيار **B2B - Tax Invoice**، يجب أن تظهر:

1. ✅ حقل الرقم الضريبي (VAT Number)
2. ✅ حقل رقم المبنى (Building Number)
3. ✅ حقل اسم الشارع (Street Name)
4. ✅ حقل الحي (District)
5. ✅ حقل المدينة (City)
6. ✅ حقل الرمز البريدي (Postal Code)
7. ✅ حقل كود الدولة (Country Code)

**جميع الحقول يجب أن تكون مرئية ومميزة بـ * حمراء**

---

## ❌ الأخطاء الشائعة

### خطأ 1: "toggleB2BFields is not defined"
**السبب**: الـ script لم يتم تحميله
**الحل**: تأكد من وجود الـ `<script>` في الصفحة

### خطأ 2: الحقول موجودة لكن مخفية
**السبب**: `style="display: none;"` لم يتم تغييره
**الحل**: استخدم الكود اليدوي في Console

### خطأ 3: "Cannot read property 'style' of null"
**السبب**: العنصر غير موجود في الصفحة
**الحل**: تأكد من أن الحقول موجودة في HTML

---

## 🆘 إذا لم ينفع أي شيء

### الحل النهائي:

1. **احذف الـ `style="display: none;"`** من الحقول
2. **اجعل الحقول ظاهرة دائماً**
3. **استخدم CSS بدلاً من JavaScript**

افتح الملف:
```
Modules/Business/resources/views/parties/create.blade.php
```

ابحث عن:
```html
<div class="col-lg-6 mb-2 b2b-field" style="display: none;">
```

غيّره إلى:
```html
<div class="col-lg-6 mb-2 b2b-field">
```

**ملاحظة**: هذا سيجعل الحقول ظاهرة دائماً، لكن على الأقل ستعمل!

---

## 📞 الدعم

إذا جربت كل شيء ولم ينفع:

1. أرسل screenshot من Console (F12)
2. أرسل screenshot من الصفحة
3. أرسل نتيجة هذا الكود:
   ```javascript
   console.log({
       select: document.getElementById('zatca_type'),
       fields: document.querySelectorAll('.b2b-field').length,
       func: typeof toggleB2BFields
   });
   ```

---

**آخر تحديث**: 22 يناير 2026
**الإصدار**: 1.0.2
