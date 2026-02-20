# 🔧 حل مشكلة 400 Error على السيرفر

## المشكلة
لما بترفع الموقع على CloudPanel، بيظهر خطأ:
```
400 - This domain is not allowed
```

## السبب
الـ `CheckDomain` middleware بيفحص كل الدومينات، والملف المعدل مش موجود على السيرفر.

---

## ✅ الحل السريع (موصى به)

### الخطوة 1: ارفع الملف المعدل

ارفع الملف ده للسيرفر:
```
app/Http/Middleware/CheckDomain.php
```

**مكان الملف على السيرفر:**
```
/home/[username]/htdocs/nomupos.com/app/Http/Middleware/CheckDomain.php
```

استخدم FTP أو File Manager في CloudPanel.

---

### الخطوة 2: امسح الكاش

اتصل بالسيرفر عن طريق SSH أو Terminal في CloudPanel:

```bash
cd /home/[username]/htdocs/nomupos.com

php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

### الخطوة 3: جرب الموقع

افتح المتصفح واذهب إلى:
```
https://nomupos.com
```

المفروض يشتغل بدون مشاكل! ✅

---

## 🔄 الحل البديل (إذا لم ينجح الأول)

### الطريقة 1: تعطيل الـ Middleware مؤقتاً

عدل الملف ده على السيرفر:
```
app/Http/Kernel.php
```

**السطر 43:**
```php
// قبل التعديل:
\App\Http\Middleware\CheckDomain::class,

// بعد التعديل (ضع // قبل السطر):
// \App\Http\Middleware\CheckDomain::class,
```

ثم امسح الكاش:
```bash
php artisan cache:clear
php artisan route:clear
```

⚠️ **ملاحظة:** هذا الحل مؤقت، لازم ترفع الملف المعدل بعدين.

---

### الطريقة 2: تعطيل الـ Module

عدل الملف ده على السيرفر:
```
modules_statuses.json
```

**غير:**
```json
{
    "CustomDomainAddon": false
}
```

ثم امسح الكاش:
```bash
php artisan cache:clear
```

⚠️ **ملاحظة:** هذا يعطل الـ Custom Domain addon بالكامل.

---

## 📋 الخطوات التفصيلية لرفع الملف

### باستخدام CloudPanel File Manager:

1. **سجل دخول إلى CloudPanel**
   - اذهب إلى: `https://your-server-ip:8443`

2. **افتح File Manager**
   - Sites → اختر `nomupos.com` → File Manager

3. **اذهب إلى المجلد**
   - افتح: `htdocs/nomupos.com/app/Http/Middleware/`

4. **ارفع الملف**
   - اضغط "Upload"
   - اختر `CheckDomain.php` من جهازك
   - اضغط "Replace" إذا طلب منك

5. **تحقق من الصلاحيات**
   - الملف يجب أن يكون `644`
   - المالك يجب أن يكون نفس مستخدم الموقع

---

### باستخدام FTP (FileZilla):

1. **اتصل بالسيرفر**
   - Host: `your-server-ip`
   - Username: `[your-username]`
   - Password: `[your-password]`
   - Port: `21` (أو `22` لـ SFTP)

2. **اذهب إلى المجلد**
   ```
   /home/[username]/htdocs/nomupos.com/app/Http/Middleware/
   ```

3. **ارفع الملف**
   - اسحب `CheckDomain.php` من جهازك إلى السيرفر
   - اختر "Overwrite" إذا طلب منك

---

### باستخدام SSH:

1. **اتصل بالسيرفر**
   ```bash
   ssh [username]@[server-ip]
   ```

2. **اذهب إلى مجلد الموقع**
   ```bash
   cd /home/[username]/htdocs/nomupos.com
   ```

3. **ارفع الملف باستخدام Git**
   ```bash
   git pull origin main
   ```

   أو انسخ الملف مباشرة:
   ```bash
   # من جهازك (في terminal منفصل):
   scp app/Http/Middleware/CheckDomain.php [username]@[server-ip]:/home/[username]/htdocs/nomupos.com/app/Http/Middleware/
   ```

---

## 🧪 التحقق من الإصلاح

### 1. تحقق من الملف

على السيرفر:
```bash
cat app/Http/Middleware/CheckDomain.php | grep "moduleCheck"
```

يجب أن يظهر:
```php
if (!moduleCheck('CustomDomainAddon')) {
    return $next($request);
}
```

---

### 2. تحقق من الكاش

```bash
php artisan route:list | grep CheckDomain
```

---

### 3. جرب الموقع

افتح المتصفح:
```
https://nomupos.com
```

يجب أن يعمل بدون أخطاء! ✅

---

## 🔍 استكشاف الأخطاء

### إذا استمرت المشكلة:

#### 1. تحقق من الـ logs
```bash
tail -f storage/logs/laravel.log
```

#### 2. تحقق من APP_URL
```bash
cat .env | grep APP_URL
```

يجب أن يكون:
```
APP_URL=https://nomupos.com
```

#### 3. تحقق من modules_statuses.json
```bash
cat modules_statuses.json
```

يجب أن يكون:
```json
{
    "CustomDomainAddon": true
}
```

#### 4. تحقق من قاعدة البيانات
```bash
php artisan tinker
```

ثم:
```php
\Modules\CustomDomainAddon\App\Models\Domain::all();
```

---

## 📝 ملاحظات مهمة

### 1. الدومين الرئيسي
تأكد إن `APP_URL` في `.env` هو:
```
APP_URL=https://nomupos.com
```

وليس:
```
APP_URL=https://nomuposs.com  ❌ (خطأ)
```

---

### 2. الـ Middleware
الـ `CheckDomain` middleware بيسمح بـ:
- الدومين الرئيسي (`nomupos.com`)
- localhost و local IPs
- Subdomains المسجلة في قاعدة البيانات
- Custom domains المسجلة في قاعدة البيانات

---

### 3. الـ Module Status
لو عايز تعطل الـ addon مؤقتاً:
```json
{
    "CustomDomainAddon": false
}
```

لو عايز تفعله:
```json
{
    "CustomDomainAddon": true
}
```

---

## ✅ Checklist

قبل ما تجرب الموقع، تأكد من:

- [ ] رفعت `CheckDomain.php` المعدل
- [ ] مسحت الكاش (`php artisan cache:clear`)
- [ ] `APP_URL` صحيح في `.env`
- [ ] `CustomDomainAddon` مفعل في `modules_statuses.json`
- [ ] صلاحيات الملفات صحيحة (644)
- [ ] الـ logs مافيهاش أخطاء

---

## 🎯 الخلاصة

**الحل الأسرع:**
1. ارفع `app/Http/Middleware/CheckDomain.php`
2. امسح الكاش
3. جرب الموقع

**إذا لم ينجح:**
1. عطل الـ middleware في `Kernel.php`
2. أو عطل الـ module في `modules_statuses.json`

**بعد الإصلاح:**
- الموقع يشتغل على `https://nomupos.com` ✅
- الـ Custom Domain addon يشتغل ✅
- مافيش 400 errors ✅

---

## 📞 الدعم

إذا استمرت المشكلة، أرسل:
1. محتوى `storage/logs/laravel.log`
2. محتوى `.env` (بدون passwords)
3. محتوى `modules_statuses.json`
4. Screenshot من الخطأ

---

**دلوقتي الموقع يشتغل على السيرفر بدون مشاكل! 🎉**
