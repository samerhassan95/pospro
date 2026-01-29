# 🔧 دليل حل المشاكل - فواتير B2B

## المشكلة الأكثر شيوعاً: الحقول لا تظهر عند اختيار B2B

### الأعراض:
- اخترت "B2B - Tax Invoice"
- الحقول الإضافية لم تظهر
- عند الحفظ تظهر رسالة خطأ:
  ```
  The vat number field is required when zatca type is b2b.
  The building number field is required when zatca type is b2b.
  ... (and 5 more errors)
  ```

### الحلول:

#### الحل 1: مسح Cache (الأسرع) ⚡
```bash
php artisan view:clear
php artisan cache:clear
```
ثم أعد تحميل الصفحة بـ **Ctrl+F5** (أو Cmd+Shift+R على Mac)

#### الحل 2: التحقق من JavaScript
1. افتح Developer Tools في المتصفح (F12)
2. اذهب إلى تبويب "Console"
3. ابحث عن أي أخطاء JavaScript
4. إذا وجدت خطأ مثل:
   ```
   $ is not defined
   ```
   معناها jQuery غير محمّل

#### الحل 3: استخدام الحقول يدوياً
إذا لم تظهر الحقول تلقائياً، يمكنك:
1. افتح Developer Tools (F12)
2. اذهب إلى تبويب "Console"
3. اكتب هذا الكود:
   ```javascript
   document.querySelectorAll('.b2b-field').forEach(f => f.style.display = 'block');
   document.getElementById('vat_number_field').style.display = 'block';
   ```
4. اضغط Enter
5. الحقول ستظهر

#### الحل 4: التحقق من الكود
تأكد من أن الكود التالي موجود في نهاية الملف:
```javascript
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const zatcaTypeSelect = document.getElementById('zatca_type');
        // ... باقي الكود
    });
</script>
@endpush
```

---

## مشاكل أخرى شائعة

### المشكلة 2: "Column not found: zatca_type"

#### الأعراض:
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'zatca_type'
```

#### الحل:
```bash
# تشغيل Migration
php artisan migrate

# إذا لم ينفع، جرب:
php artisan migrate:fresh --seed
# ⚠️ تحذير: هذا سيحذف جميع البيانات!
```

---

### المشكلة 3: "Class not found"

#### الأعراض:
```
Class 'Database\Seeders\UpdateB2BFieldsSeeder' not found
```

#### الحل:
```bash
composer dump-autoload
php artisan cache:clear
```

---

### المشكلة 4: الحقول تظهر ولكن لا تُحفظ

#### الأعراض:
- الحقول تظهر بشكل صحيح
- تملأ البيانات
- عند الحفظ، البيانات لا تُحفظ

#### الحل:
تحقق من أن الحقول موجودة في `$fillable` في Model:

```php
// app/Models/Party.php
protected $fillable = [
    // ... الحقول الموجودة
    'zatca_type',
    'vat_number',
    'building_number',
    'street_name',
    'district',
    'city',
    'postal_code',
    'country_code',
];
```

---

### المشكلة 5: "CSRF token mismatch"

#### الأعراض:
```
419 | Page Expired
CSRF token mismatch
```

#### الحل:
```bash
php artisan cache:clear
php artisan config:clear
php artisan session:clear
```
ثم أعد تحميل الصفحة

---

### المشكلة 6: الحقول تظهر للـ B2C أيضاً

#### الأعراض:
- الحقول تظهر حتى عند اختيار B2C

#### الحل:
تحقق من أن الحقول لها class `b2b-field`:
```html
<div class="col-lg-6 mb-2 b2b-field" style="display: none;">
```

---

### المشكلة 7: "VAT number must be 15 digits"

#### الأعراض:
- أدخلت الرقم الضريبي
- رسالة خطأ تقول يجب أن يكون 15 رقم

#### الحل:
تأكد من:
- ✅ الرقم 15 رقم بالضبط (لا أكثر ولا أقل)
- ✅ لا توجد مسافات
- ✅ لا توجد شرطات
- ✅ أرقام فقط (لا حروف)

مثال صحيح: `300123456789003`
مثال خاطئ: `300-123-456-789-003` (فيه شرطات)

---

### المشكلة 8: الصفحة بطيئة جداً

#### الأعراض:
- الصفحة تأخذ وقت طويل للتحميل
- الحقول تظهر بعد تأخير

#### الحل:
```bash
# تحسين الأداء
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

### المشكلة 9: لا يمكن رؤية الحقول على الموبايل

#### الأعراض:
- على الكمبيوتر تعمل
- على الموبايل لا تظهر

#### الحل:
تحقق من أن الـ CSS responsive:
```css
@media (max-width: 768px) {
    .b2b-field {
        width: 100%;
    }
}
```

---

### المشكلة 10: الفاتورة لا تُحفظ كـ B2B

#### الأعراض:
- العميل محفوظ كـ B2B
- الفاتورة تُحفظ كـ B2C

#### الحل:
تحقق من Sale Controller:
```php
$sale = Sale::create([
    // ... باقي الحقول
    'invoice_type' => $party->zatca_type, // يجب أن يكون موجود
]);
```

---

## 🔍 كيفية التشخيص

### الخطوة 1: تحقق من Logs
```bash
tail -f storage/logs/laravel.log
```

### الخطوة 2: تحقق من Database
```sql
-- تحقق من وجود الحقول
DESCRIBE parties;

-- تحقق من البيانات
SELECT id, name, zatca_type, vat_number FROM parties LIMIT 5;
```

### الخطوة 3: تحقق من Browser Console
1. افتح Developer Tools (F12)
2. اذهب إلى Console
3. ابحث عن أخطاء JavaScript

### الخطوة 4: تحقق من Network
1. افتح Developer Tools (F12)
2. اذهب إلى Network
3. أعد تحميل الصفحة
4. ابحث عن أي طلبات فاشلة (حمراء)

---

## 🆘 الحل النهائي (إذا لم ينفع شيء)

### إعادة التثبيت الكاملة:

```bash
# 1. مسح كل شيء
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# 2. إعادة تشغيل Migration
php artisan migrate:rollback --step=1
php artisan migrate

# 3. إعادة تحميل Composer
composer dump-autoload

# 4. إعادة تشغيل السيرفر
php artisan serve
```

---

## 📞 لا تزال المشكلة موجودة؟

### معلومات يجب توفيرها للدعم:

1. **رسالة الخطأ الكاملة**
   ```
   انسخ رسالة الخطأ من المتصفح أو من logs
   ```

2. **خطوات إعادة المشكلة**
   ```
   1. فتحت صفحة ...
   2. اخترت ...
   3. حدث ...
   ```

3. **معلومات البيئة**
   ```bash
   php artisan --version
   php --version
   ```

4. **Browser Console Errors**
   ```
   افتح F12 → Console → انسخ الأخطاء
   ```

5. **Laravel Logs**
   ```bash
   tail -n 50 storage/logs/laravel.log
   ```

---

## ✅ Checklist للتحقق

قبل طلب الدعم، تأكد من:

- [ ] شغّلت `php artisan migrate`
- [ ] مسحت الـ cache (`php artisan cache:clear`)
- [ ] مسحت الـ views (`php artisan view:clear`)
- [ ] أعدت تحميل الصفحة بـ Ctrl+F5
- [ ] تحققت من Browser Console (F12)
- [ ] تحققت من Laravel Logs
- [ ] جربت على متصفح آخر
- [ ] تحققت من أن JavaScript شغال

---

## 💡 نصائح للوقاية

### 1. استخدم Version Control (Git)
```bash
git add .
git commit -m "Added B2B feature"
```

### 2. اعمل Backup للـ Database
```bash
php artisan backup:run
# أو
mysqldump -u username -p database_name > backup.sql
```

### 3. اختبر على بيئة تطوير أولاً
لا تطبق التغييرات مباشرة على الإنتاج

### 4. راقب الـ Logs
```bash
tail -f storage/logs/laravel.log
```

---

**آخر تحديث**: 22 يناير 2026
**الإصدار**: 1.0.1
