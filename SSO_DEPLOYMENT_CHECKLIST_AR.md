# ✅ Checklist نشر SSO - خطوات سريعة

## 🎯 Master App (التطبيق الرئيسي)

### 1. إضافة الملفات
- [ ] `app/Services/MasterSSOService.php`
- [ ] `app/Http/Controllers/SSORedirectController.php`
- [ ] `config/sso.php`

### 2. إضافة في .env
```env
SSO_SECRET_KEY=6856fe49d4f8bc0830199edaefe41fa7922d0a323b07ea216dfbc96ce8257cca
SUB_APP_URL=https://your-sub-app.com
```

### 3. إضافة Route
```php
Route::post('/redirect-to-sub-app', [SSORedirectController::class, 'redirectToSubApp']);
```

### 4. استخدام في الكود
```php
$ssoService = new MasterSSOService();
$ssoUrl = $ssoService->generateSSOUrl($subAppUrl, [
    'user_id' => $user->id,
    'name' => $user->name,
    'email' => $user->email,
    'plan_id' => 2,
    'business_name' => 'متجر تجريبي',
]);
return redirect($ssoUrl);
```

---

## 🎯 Sub App (هذا المشروع)

### 1. تفعيل SSO
```env
SSO_ENABLED=true
SSO_SECRET_KEY=6856fe49d4f8bc0830199edaefe41fa7922d0a323b07ea216dfbc96ce8257cca
```

### 2. رفع الملفات
- [ ] `app/Services/SSOService.php` ✅ موجود
- [ ] `app/Http/Controllers/SSOController.php` ✅ موجود
- [ ] `app/Http/Middleware/VerifySSOToken.php` ✅ موجود
- [ ] `routes/sso.php` ✅ موجود
- [ ] `config/sso.php` ✅ موجود

### 3. تشغيل Migrations
```bash
php artisan migrate
```

### 4. مسح Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### 5. التحقق
```bash
php artisan route:list | grep sso
# يجب أن يظهر: sso/login, sso/logout
```

---

## 🧪 الاختبار

### اختبار محلي
```bash
php generate_sso_token.php
# اختر باقة
# انسخ Token
# افتح: http://127.0.0.1:8000/sso/login?token=TOKEN
```

### اختبار على السيرفر
```bash
# 1. ولّد Token
php generate_sso_token.php

# 2. افتح في المتصفح
https://your-domain.com/sso/login?token=TOKEN

# 3. شوف الـ logs
tail -f storage/logs/laravel.log | grep SSO
```

---

## 🔒 الأمان

- [ ] HTTPS مفعّل
- [ ] Rate limiting مفعّل (10 requests/minute)
- [ ] Logging شغال
- [ ] Secret key آمن ومشترك بين Master و Sub Apps

---

## 📊 التحقق من النجاح

### في Database
```sql
-- آخر مستخدم
SELECT * FROM users ORDER BY id DESC LIMIT 1;

-- آخر business
SELECT * FROM businesses ORDER BY id DESC LIMIT 1;

-- آخر subscription
SELECT * FROM plan_subscribes ORDER BY id DESC LIMIT 1;

-- الربط الكامل
SELECT u.name, b.companyName, p.subscriptionName
FROM users u
JOIN businesses b ON u.business_id = b.id
JOIN plan_subscribes ps ON b.plan_subscribe_id = ps.id
JOIN plans p ON ps.plan_id = p.id
ORDER BY u.id DESC LIMIT 1;
```

### في Logs
```bash
tail -f storage/logs/laravel.log | grep SSO

# يجب أن تشوف:
# [INFO] SSO: Business created
# [INFO] SSO: Subscription created
# [INFO] SSO: New user created
```

---

## 🚨 إذا حصلت مشكلة

### عطّل SSO مؤقتاً
```env
SSO_ENABLED=false
```

### امسح Cache
```bash
php artisan config:clear
```

### شوف الـ Logs
```bash
tail -f storage/logs/laravel.log
```

---

## ✅ Checklist النهائي

### Master App
- [ ] MasterSSOService موجود
- [ ] SSORedirectController موجود
- [ ] Secret key في .env
- [ ] Sub App URL في .env
- [ ] Route للـ redirect موجود

### Sub App
- [ ] SSO_ENABLED=true
- [ ] Secret key نفسه في .env
- [ ] Migrations تم تشغيلها
- [ ] Cache تم مسحه
- [ ] Routes موجودة
- [ ] HTTPS شغال

### الاختبار
- [ ] اختبار إنشاء user مع Plan A
- [ ] اختبار إنشاء user مع Plan B
- [ ] اختبار إنشاء user مع Plan C
- [ ] اختبار Login بعد الإنشاء
- [ ] اختبار Logout
- [ ] مراجعة Logs

---

## 🎉 جاهز!

بعد إتمام كل الخطوات:
1. ✅ Master App يقدر يرسل Users
2. ✅ Sub App يقدر يستقبلهم
3. ✅ Business و Subscription يتم إنشاؤهم تلقائياً
4. ✅ User يسجل دخول تلقائياً

**وقت التنفيذ المتوقع:** 30-60 دقيقة

---

**تاريخ الإنشاء:** 2026-02-28  
**الحالة:** ✅ جاهز للتطبيق
