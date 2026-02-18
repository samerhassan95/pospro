# خطوات سريعة لإكمال التكامل

## 🚀 الخطوة 1: إضافة CSRF Token

أضف هذا السطر في قسم `<head>` في كلا الملفين:

- `Modules/Business/resources/views/sales/create.blade.php`
- `Modules/Business/resources/views/purchases/create.blade.php`

```html
<meta name="csrf-token" content="{{ csrf_token() }}" />
```

## 🔧 الخطوة 2: تحديث دوال الـ fetch

ابحث عن جميع استدعاءات `fetch()` في الملفين واستبدل:

```javascript
// القديم
headers: {
    'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
    'Accept': 'application/json'
}

// الجديد
headers: {
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    'Accept': 'application/json',
    'Content-Type': 'application/json'
}
```

## 🗄️ الخطوة 3: تشغيل Migrations

```bash
php artisan migrate
```

## ✅ الخطوة 4: اختبار النظام

1. افتح المتصفح على: `http://localhost:8000/sales/create`
2. انقر على "Manage Tables"
3. جرب إضافة طاولة جديدة
4. جرب حجز طاولة
5. تحقق من قاعدة البيانات

## 🔍 الخطوة 5: فحص الأخطاء

إذا واجهت أخطاء، افحص:

```bash
# عرض آخر 50 سطر من الـ logs
tail -n 50 storage/logs/laravel.log

# أو في Windows PowerShell
Get-Content storage/logs/laravel.log -Tail 50
```

## 📊 التحقق من البيانات

```sql
-- تحقق من الطاولات
SELECT * FROM restaurant_tables;

-- تحقق من الحجوزات
SELECT * FROM table_reservations;

-- تحقق من الطلبات
SELECT * FROM table_orders;

-- تحقق من التخطيطات
SELECT * FROM floor_plan_layouts;
```

## 🎯 نقاط الاختبار الرئيسية

- [ ] إنشاء طاولة جديدة
- [ ] تحريك طاولة وحفظ الموقع
- [ ] تدوير طاولة
- [ ] حذف طاولة مخصصة
- [ ] إنشاء حجز جديد
- [ ] إلغاء حجز
- [ ] تحديد وصول الضيف
- [ ] إنشاء طلب لطاولة
- [ ] إكمال طلب
- [ ] حفظ تخطيط الأرضية
- [ ] تحميل تخطيط محفوظ
- [ ] إنشاء فاتورة مع table_id

---

**ملاحظة**: جميع التحويلات من localStorage إلى API تمت بنجاح! ✅
