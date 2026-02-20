# دليل اختبار Manage Reservations ✅

## الخطوات السريعة للاختبار

### 1. افتح صفحة Sales
```
http://127.0.0.1:8000/business/sales/create
```

### 2. اذهب إلى تبويب Tables
- اضغط على تبويب "Tables" في الأعلى
- يجب أن ترى 17 طاولة محملة من قاعدة البيانات

### 3. افتح Manage Reservations
- اضغط على زر "Manage All Tables" (أو "Manage Reservations")
- يجب أن يفتح modal

### 4. تحقق من التحميل
**ما يجب أن تراه:**
- ✅ Spinner أثناء التحميل
- ✅ جدول يعرض الحجوزات
- ✅ حجزين موجودين:
  - Ta8 - samer hassan - 2026-02-21 18:03
  - Ta10 - samer hassan - 2026-02-21 18:18

### 5. تحقق من Console
افتح Developer Tools (F12) وانظر إلى Console:
```javascript
✅ Opening Manage Reservations modal...
✅ Reservations from backend: {success: true, data: Array(2)}
```

### 6. اختبر الأزرار

#### إذا كان الوقت قد حان (Time Arrived):
- يجب أن ترى زر أخضر "Mark Arrived"
- اضغط عليه
- يجب أن تظهر رسالة نجاح
- يجب أن تتحدث الحالة إلى "Arrived"

#### زر Cancel:
- اضغط على زر "Cancel" الأحمر
- يجب أن يظهر modal تأكيد
- يعرض تفاصيل الحجز (الطاولة، العميل، التاريخ، الوقت)
- اضغط "Yes, Cancel Reservation"
- يجب أن تظهر رسالة نجاح
- يجب أن تتحدث الحالة إلى "Cancelled"

### 7. تحقق من قاعدة البيانات
```sql
SELECT * FROM table_reservations WHERE business_id = 4;
```

يجب أن ترى التحديثات في حقل `status`:
- `reserved` - حجز نشط
- `completed` - وصل الضيف
- `cancelled` - تم الإلغاء

## ما يجب أن يعمل

### ✅ التحميل
- يحمل من `/business/table-reservations`
- يستخدم XMLHttpRequest (لا fetch)
- يعرض spinner أثناء التحميل

### ✅ العرض
- يعرض جميع الحجوزات من قاعدة البيانات
- يعرض الحالة الصحيحة لكل حجز
- يعرض الأزرار المناسبة حسب الحالة

### ✅ Mark Arrived
- يظهر فقط عندما يحين وقت الحجز
- يحدث قاعدة البيانات
- يحدث UI تلقائياً

### ✅ Cancel
- يعرض modal تأكيد
- يحدث قاعدة البيانات
- يحدث UI تلقائياً

### ✅ معالجة الأخطاء
- رسائل واضحة للأخطاء
- معالجة أخطاء الشبكة
- معالجة أخطاء API

## الأخطاء المحتملة وحلولها

### 1. لا تظهر الحجوزات
**السبب:** مشكلة في API
**الحل:**
```bash
# تحقق من الروابط
php artisan route:list | grep table-reservations

# تحقق من قاعدة البيانات
php artisan tinker
>>> \App\Models\TableReservation::where('business_id', 4)->get();
```

### 2. Service Worker Error
**السبب:** fetch محظور من service worker
**الحل:** الكود الجديد يستخدم XMLHttpRequest بدلاً من fetch ✅

### 3. CSRF Token Error
**السبب:** CSRF token مفقود
**الحل:** تحقق من وجود meta tag في head:
```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```

### 4. 500 Internal Server Error
**السبب:** خطأ في Controller
**الحل:** تحقق من logs:
```bash
tail -f storage/logs/laravel.log
```

## Network Requests المتوقعة

### عند فتح Modal:
```
GET /business/table-reservations
Status: 200 OK
Response: {success: true, data: [...]}
```

### عند Mark Arrived:
```
POST /business/table-reservations/1/guest-arrived
Status: 200 OK
Response: {success: true, message: "Guest arrived"}
```

### عند Cancel:
```
POST /business/table-reservations/1/cancel
Status: 200 OK
Response: {success: true, message: "Reservation cancelled"}
```

## Console Messages المتوقعة

### عند فتح Modal:
```javascript
✅ Opening Manage Reservations modal...
✅ Reservations from backend: {success: true, data: Array(2)}
```

### عند Mark Arrived:
```javascript
// Success toast message
Guest marked as arrived
```

### عند Cancel:
```javascript
// Success toast message
Reservation cancelled successfully
```

## الخلاصة

إذا رأيت:
- ✅ الحجوزات تحمل من قاعدة البيانات
- ✅ الأزرار تعمل
- ✅ الرسائل تظهر
- ✅ UI يتحدث تلقائياً

**إذن النظام يعمل بشكل صحيح! 🎉**

## الخطوة التالية

بعد التأكد من أن كل شيء يعمل:
1. احذف أي ملفات localStorage قديمة
2. احذف أي كود localStorage متبقي
3. اختبر على بيئة الإنتاج

**النظام جاهز للاستخدام! 🚀**
