# تعليمات تثبيت ميزة فواتير B2B

## الخطوات المطلوبة

### 1. تشغيل Migration
```bash
php artisan migrate
```

هذا سيضيف الحقول الجديدة إلى الجداول:
- `parties`: zatca_type, vat_number, building_number, street_name, district, city, postal_code, country_code
- `businesses`: building_number, street_name, district, city, postal_code, country_code
- `sales`: invoice_type

### 2. تشغيل Seeder (اختياري)
إذا كان لديك بيانات موجودة، قم بتشغيل:
```bash
php artisan db:seed --class=UpdateB2BFieldsSeeder
```

أو يمكنك تشغيل SQL مباشرة:
```bash
php artisan db:seed --class=UpdateB2BFieldsSeeder
# أو
mysql -u username -p database_name < database/sql/update_b2b_fields.sql
```

### 3. مسح Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### 4. إعادة تحميل Composer (إذا لزم الأمر)
```bash
composer dump-autoload
```

## التحقق من التثبيت

### 1. التحقق من قاعدة البيانات
```sql
-- التحقق من إضافة الحقول في جدول parties
DESCRIBE parties;

-- التحقق من إضافة الحقول في جدول businesses
DESCRIBE businesses;

-- التحقق من إضافة الحقول في جدول sales
DESCRIBE sales;
```

### 2. التحقق من الواجهة
1. اذهب إلى صفحة إضافة عميل جديد
2. تأكد من ظهور حقل "Invoice Type"
3. اختر "B2B - Tax Invoice"
4. تأكد من ظهور الحقول الإضافية:
   - VAT Number
   - Building Number
   - Street Name
   - District
   - City
   - Postal Code
   - Country Code

### 3. اختبار الوظائف
1. أضف عميل B2B جديد
2. املأ جميع الحقول المطلوبة
3. احفظ العميل
4. تحقق من حفظ البيانات بشكل صحيح

## استكشاف الأخطاء

### خطأ: "Column not found"
**الحل**: تأكد من تشغيل Migration:
```bash
php artisan migrate:status
php artisan migrate
```

### خطأ: "Class not found"
**الحل**: قم بإعادة تحميل Composer:
```bash
composer dump-autoload
```

### خطأ: "View not found"
**الحل**: امسح cache الـ views:
```bash
php artisan view:clear
```

### الحقول لا تظهر/تختفي
**الحل**: تأكد من تحميل jQuery بشكل صحيح وافتح Console في المتصفح للتحقق من الأخطاء.

## الملفات المعدلة

### تم إنشاؤها:
1. `database/migrations/2026_01_22_000000_add_b2b_fields_to_parties_and_businesses.php`
2. `database/seeders/UpdateB2BFieldsSeeder.php`
3. `database/sql/update_b2b_fields.sql`
4. `docs/B2B_INVOICE_IMPLEMENTATION.md`
5. `docs/B2B_INVOICE_IMPLEMENTATION_EN.md`
6. `docs/B2B_NEXT_STEPS.md`
7. `INSTALLATION_B2B.md`

### تم تعديلها:
1. `app/Models/Party.php`
2. `app/Models/Business.php`
3. `app/Models/Sale.php`
4. `Modules/Business/resources/views/parties/create.blade.php`
5. `Modules/Business/resources/views/parties/edit.blade.php`
6. `Modules/Business/App/Http/Controllers/AcnooPartyController.php`

## الخطوات التالية

بعد التثبيت، راجع ملف `docs/B2B_NEXT_STEPS.md` لمعرفة الخطوات المطلوبة لإكمال التطبيق الكامل.

## الدعم

إذا واجهت أي مشاكل، يرجى:
1. التحقق من logs في `storage/logs/laravel.log`
2. التحقق من Console في المتصفح
3. مراجعة التوثيق في مجلد `docs/`

## ملاحظات مهمة

1. **Backup**: تأكد من عمل نسخة احتياطية من قاعدة البيانات قبل التثبيت
2. **Testing**: اختبر على بيئة تطوير أولاً قبل النشر على الإنتاج
3. **ZATCA**: تأكد من تحديث UBL Generator و ZATCA Service قبل الاستخدام الفعلي
