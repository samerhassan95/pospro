# إصلاح خطأ 400 - Domain Not Allowed

## المشكلة
عند الدخول على الدومين بتظهر رسالة:
```
Error: This domain is not allowed
Please request for a domain/subdomain from the business panel.
```

## السبب
الـ middleware `CheckDomain` بيتحقق إن الدومين:
1. موجود في قاعدة البيانات ✅
2. `is_verified = 1` ❌
3. `status = 1` ❌

الدومين موجود لكن مش متوافق عليه (Pending).

## الحلول

### الحل 1: وافق على الدومين من قاعدة البيانات (موصى به) ⚡

#### الطريقة الأولى: استخدم السكريبت
```bash
# على السيرفر
php approve_domain.php nomuposs.com
```

#### الطريقة الثانية: SQL مباشر
```sql
UPDATE domains 
SET status = 1, is_verified = 1 
WHERE domain = 'nomuposs.com';
```

#### الطريقة الثالثة: من Admin Panel
1. اذهب إلى: Admin Panel → Domains
2. ابحث عن الدومين
3. اضغط "Approve"

---

### الحل 2: عدل الـ Middleware (تم التعديل)

تم تعديل ملف `app/Http/Middleware/CheckDomain.php` ليعطي رسالة أوضح.

**الملف:** `app/Http/Middleware/CheckDomain.php`

**السطر:** حوالي 35-42

**الكود القديم:**
```php
$isAllowed = \Modules\CustomDomainAddon\App\Models\Domain::query()
                ->where('domain', $host)
                ->where('is_verified', 1)
                ->where('status', 1)
                ->exists();

if (!$isAllowed) {
    abort(400, 'Error: this domain is not allowed...');
}
```

**الكود الجديد:**
```php
$domain = \Modules\CustomDomainAddon\App\Models\Domain::query()
                ->where('domain', $host)
                ->first();

if (!$domain) {
    abort(400, 'Error: this domain is not allowed...');
}

if ($domain->status != 1 || $domain->is_verified != 1) {
    abort(400, 'Error: This domain is pending approval...');
}
```

---

### الحل 3: عطل الـ Middleware مؤقتاً (للتجربة فقط)

في ملف `app/Http/Kernel.php`، ابحث عن:
```php
\App\Http\Middleware\CheckDomain::class,
```

وعلق عليه:
```php
// \App\Http\Middleware\CheckDomain::class,
```

⚠️ **تحذير:** هذا الحل للتجربة فقط! لا تستخدمه في Production.

---

## الخطوات على السيرفر

### الخطوة 1: ارفع الملفات المعدلة
```bash
# ارفع الملفات:
# - app/Http/Middleware/CheckDomain.php
# - approve_domain.php
```

### الخطوة 2: وافق على الدومين
```bash
php approve_domain.php nomuposs.com
```

### الخطوة 3: امسح الكاش
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### الخطوة 4: جرب الدخول على الدومين
افتح المتصفح واذهب إلى: `http://nomuposs.com`

---

## التحقق من حالة الدومين

### من قاعدة البيانات:
```sql
SELECT id, domain, business_id, status, is_verified, is_ssl_enabled 
FROM domains 
WHERE domain = 'nomuposs.com';
```

### من السكريبت:
```bash
php check_domain_settings.php
```

---

## الملفات المعدلة

### 1. `app/Http/Middleware/CheckDomain.php`
- تحسين رسالة الخطأ
- إعطاء معلومات أوضح عن سبب الرفض

### 2. `approve_domain.php` (جديد)
- سكريبت للموافقة على الدومينات بسرعة

---

## ملاحظات مهمة

### لماذا الدومين Pending؟
لأن في `DomainController` الإعدادات كانت:
- `automatic_approve = off` (الافتراضي)
- الدومين يحتاج موافقة يدوية

### كيف تتجنب المشكلة مستقبلاً؟
1. **للتطوير (Development):**
   - فعل `automatic_approve = on` في إعدادات الدومين
   - الدومينات هتتوافق عليها تلقائياً

2. **للإنتاج (Production):**
   - خلي `automatic_approve = off`
   - وافق على الدومينات يدوياً من Admin Panel
   - أو استخدم السكريبت `approve_domain.php`

---

## الخلاصة

✅ المشكلة: الدومين موجود لكن مش متوافق عليه  
✅ الحل: وافق على الدومين من قاعدة البيانات  
✅ تم تحسين رسالة الخطأ لتكون أوضح  
✅ تم إنشاء سكريبت للموافقة السريعة  

**الآن شغل السكريبت وهيشتغل معاك! 🎉**
