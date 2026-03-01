# ملخص التغييرات والنشر على السيرفر

## 📋 نظرة عامة

تم إنشاء 4 ملفات توثيق شاملة لمساعدتك في نشر التغييرات على السيرفر:

1. **SERVER_DEPLOYMENT_DATABASE_CHANGES_AR.md** - دليل شامل بالعربية
2. **apply_database_changes.sql** - سكريبت SQL جاهز للتنفيذ
3. **DEPLOYMENT_CHECKLIST_AR.md** - قائمة تحقق خطوة بخطوة
4. **DATABASE_CHANGES_QUICK_REFERENCE.md** - مرجع سريع بالإنجليزية

---

## 🎯 التغييرات الرئيسية

### 1. نظام SSO (تسجيل الدخول الموحد)
- إضافة 3 حقول لجدول `users`
- يسمح بتسجيل الدخول من التطبيق الرئيسي
- معطل افتراضياً (SSO_ENABLED=false)

### 2. صلاحيات الباقات
- إضافة 17 حقل صلاحيات لجدول `plans`
- التحكم في الميزات حسب الباقة
- عرض الصلاحيات في صفحة الأسعار

### 3. دعم الفروع في HRM
- إضافة حقل `branch_id` لـ 5 جداول HRM
- اختياري (فقط إذا كانت الجداول موجودة)

---

## 🚀 طريقة النشر السريعة

### الخطوة 1: النسخ الاحتياطي
```bash
mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql
```

### الخطوة 2: رفع الملفات
- رفع جميع ملفات المشروع المحدثة
- التأكد من رفع مجلد `database/migrations`

### الخطوة 3: تحديث .env
أضف هذه الأسطر:
```env
SSO_ENABLED=false
SSO_SECRET_KEY=6856fe49d4f8bc0830199edaefe41fa7922d0a323b07ea216dfbc96ce8257cca
SSO_TOKEN_EXPIRY=0
SSO_ALLOW_AUTO_REGISTRATION=true
SSO_RATE_LIMIT=10
SSO_LOG_CHANNEL=stack
```

### الخطوة 4: تطبيق التغييرات

**الطريقة الأولى (موصى بها):**
```bash
cd /path/to/project
php artisan migrate
php artisan config:clear
php artisan cache:clear
php artisan optimize
```

**الطريقة الثانية (SQL مباشر):**
```bash
mysql -u username -p database_name < apply_database_changes.sql
```

### الخطوة 5: التحقق
```bash
php artisan migrate:status
```

---

## 📁 الملفات المطلوب رفعها

### ملفات SSO الجديدة (6 ملفات)
```
config/sso.php
app/Services/SSOService.php
app/Http/Controllers/SSOController.php
app/Http/Middleware/VerifySSOToken.php
routes/sso.php
```

### ملفات Migrations (3 ملفات)
```
database/migrations/2026_02_26_000000_add_permissions_to_plans_table.php
database/migrations/2026_02_27_000000_add_sso_fields_to_users_table.php
database/migrations/2026_02_28_000001_add_branch_id_to_hrm_tables.php
```

### ملفات محدثة (4 ملفات)
```
app/Providers/RouteServiceProvider.php
app/Models/User.php
app/Models/Business.php
app/Models/Plan.php
```

### ملفات العرض والأصول
```
resources/views/web/plan/index.blade.php
Modules/Business/resources/views/dashboard/index.blade.php
public/assets/css/sar-symbol.css
public/assets/js/custom/clean-sar-svg.js
lang/ar.json
```

---

## ✅ التحقق من النجاح

### 1. التحقق من قاعدة البيانات
```sql
-- التحقق من جدول users
DESCRIBE users;

-- التحقق من جدول plans
DESCRIBE plans;

-- التحقق من migrations
SELECT * FROM migrations WHERE migration LIKE '2026_02_%';
```

### 2. التحقق من الموقع
- [ ] افتح صفحة الباقات `/plans`
- [ ] تأكد من ظهور الصلاحيات
- [ ] تأكد من ظهور رمز الريال بشكل صحيح
- [ ] تحقق من عدم وجود أخطاء في Console

### 3. التحقق من Logs
```bash
tail -f storage/logs/laravel.log
```

---

## 🔧 استكشاف الأخطاء

### خطأ: Column already exists
```sql
-- تحقق من وجود العمود
SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'users' AND COLUMN_NAME = 'external_id';
```
**الحل**: العمود موجود بالفعل، تخطى هذه الخطوة

### خطأ: Table doesn't exist
```sql
-- تحقق من وجود الجدول
SHOW TABLES LIKE 'holidays';
```
**الحل**: الجدول غير موجود، تخطى تغييرات HRM

### خطأ: Migration already run
```bash
php artisan migrate:status
```
**الحل**: Migration تم تشغيلها مسبقاً، لا داعي لإعادة التشغيل

---

## 📊 ملخص التغييرات

| الجدول | عدد الحقول | إلزامي | التأثير |
|--------|-----------|--------|---------|
| users | 3 | نعم | لا يوجد (nullable) |
| plans | 17 | نعم | الكل مفعل افتراضياً |
| holidays | 1 | إذا موجود | لا يوجد (nullable) |
| attendances | 1 | إذا موجود | لا يوجد (nullable) |
| leaves | 1 | إذا موجود | لا يوجد (nullable) |
| payrolls | 1 | إذا موجود | لا يوجد (nullable) |
| employees | 1 | إذا موجود | لا يوجد (nullable) |

---

## ⚠️ ملاحظات مهمة

1. **لا تستبدل قاعدة البيانات** - فقط طبق التغييرات الجديدة
2. **SSO معطل افتراضياً** - فعله فقط عند الجاهزية
3. **جميع التغييرات آمنة** - لا يوجد حذف للبيانات
4. **احتفظ بالنسخة الاحتياطية** - لمدة 30 يوم على الأقل
5. **جداول HRM اختيارية** - تطبق فقط إذا كانت موجودة

---

## 🎓 الملفات المرجعية

### للقراءة التفصيلية:
- **SERVER_DEPLOYMENT_DATABASE_CHANGES_AR.md** - دليل شامل بالعربية مع شرح كل تغيير

### للتطبيق السريع:
- **apply_database_changes.sql** - سكريبت SQL جاهز للتنفيذ مباشرة

### للمتابعة:
- **DEPLOYMENT_CHECKLIST_AR.md** - قائمة تحقق خطوة بخطوة

### للمرجع السريع:
- **DATABASE_CHANGES_QUICK_REFERENCE.md** - مرجع سريع بالإنجليزية

---

## 📞 في حالة المشاكل

1. راجع ملف `storage/logs/laravel.log`
2. تحقق من صلاحيات قاعدة البيانات
3. تأكد من إصدار MySQL (5.7+)
4. استعد النسخة الاحتياطية إذا لزم الأمر

---

## ✨ الميزات الجديدة بعد النشر

### 1. صفحة الباقات
- عرض تفصيلي لصلاحيات كل باقة
- ترجمة عربية كاملة
- تصميم احترافي مع أيقونات

### 2. رمز الريال السعودي
- يظهر كـ SVG بدلاً من HTML code
- تصميم موحد في جميع الصفحات
- دعم الألوان الديناميكية

### 3. نظام SSO
- تسجيل دخول موحد من التطبيق الرئيسي
- إنشاء حسابات تلقائياً
- إنشاء أعمال واشتراكات تلقائياً
- دعم JWT tokens

### 4. دعم الفروع في HRM
- ربط الموظفين بالفروع
- ربط الحضور والغياب بالفروع
- ربط الرواتب بالفروع

---

## 🎉 جاهز للنشر!

جميع الملفات والتوثيق جاهزة. اتبع الخطوات في **DEPLOYMENT_CHECKLIST_AR.md** للنشر الآمن.

**وقت التطبيق المتوقع**: 15-30 دقيقة

**مستوى الصعوبة**: متوسط

**احتمالية المشاكل**: منخفضة (جميع التغييرات آمنة)

---

تم إنشاء هذا الملخص في: 1 مارس 2026
