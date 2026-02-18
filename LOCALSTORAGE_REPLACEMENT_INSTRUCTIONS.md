# تعليمات استبدال localStorage بـ API calls

## الملفات المُحدثة:
1. `public/table-reservation-api.js` - دوال API الجديدة
2. `public/table-complete-replacement.js` - دوال الاستبدال الكاملة
3. تم تحديث جزئي في ملفات sales و purchases

## التحديثات المطلوبة يدوياً:

### في ملف sales/create.blade.php:
استبدل جميع الأماكن التي تحتوي على:
```javascript
JSON.parse(localStorage.getItem('tableReservations') || '{}')
```
بـ:
```javascript
await fetchReservations()
```

استبدل:
```javascript
JSON.parse(localStorage.getItem('tableOrders') || '{}')
```
بـ:
```javascript
await fetchTableOrders()
```

### في ملف purchases/create.blade.php:
نفس التحديثات المطلوبة

### تحديثات إضافية مطلوبة:

1. **في دالة حفظ الحجز:**
```javascript
// استبدل localStorage.setItem مع:
const success = await createReservation({
    table_id: tableId,
    customer_name: customerName,
    customer_phone: phone,
    reservation_date: date,
    reservation_time: time,
    number_of_guests: guests,
    special_notes: notes
});
```

2. **في دالة حفظ الطلب:**
```javascript
// استبدل localStorage.setItem مع:
const success = await createTableOrder({
    table_id: tableId,
    customer_name: customerName,
    number_of_guests: guests,
    order_items: orderItems,
    special_notes: notes,
    order_time: time
});
```

3. **في دالة إكمال الطلب:**
```javascript
// استبدل localStorage operations مع:
const success = await completeTableOrder(orderId);
```

4. **تحديث event listeners:**
جميع الدوال التي تستخدم localStorage يجب أن تصبح async وتستخدم await

## ملاحظات مهمة:
- تأكد من أن جميع الدوال التي تستدعي API calls أصبحت async
- تأكد من إضافة error handling مناسب
- تأكد من تحديث UI بناءً على نتائج API calls
- الملفات الجديدة تحتوي على جميع الدوال المطلوبة للاستبدال الكامل

## الخطوات التالية:
1. قم بتشغيل `php artisan migrate` لإنشاء الجداول
2. تأكد من وجود الـ routes في ملف api.php
3. اختبر النظام للتأكد من عمل جميع الوظائف
4. استبدل باقي استخدامات localStorage يدوياً حسب التعليمات أعلاه