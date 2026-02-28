# ✅ Checklist قبل الرفع - SSO مع JWT

## 📋 في Sub App (المشروع الحالي)

### 1. الملفات الأساسية
- [x] `app/Services/SSOService.php` - يدعم JWT + Custom encryption
- [x] `app/Http/Controllers/SSOController.php` - endpoints: `/sso/login` و `/sso/auth`
- [x] `app/Http/Middleware/VerifySSOToken.php`
- [x] `routes/sso.php` - Routes مسجلة
- [x] `config/sso.php` - Configuration
- [x] `app/Providers/RouteServiceProvider.php` - يحمل SSO routes
- [x] `app/Models/User.php` - فيه SSO fields

### 2. Database Migrations
- [x] `2026_02_27_000000_add_sso_fields_to_users_table.php` - تم تشغيلها
- [x] `2026_02_28_000001_add_branch_id_to_hrm_tables.php` - تم تشغيلها

### 3. Environment Variables (.env)
```env
SSO_ENABLED=true
SSO_SECRET_KEY=6856fe49d4f8bc0830199edaefe41fa7922d0a323b07ea216dfbc96ce8257cca
SSO_TOKEN_EXPIRY=0
SSO_ALLOW_AUTO_REGISTRATION=true
SSO_LOG_CHANNEL=stack
```

### 4. Features المدعومة
- [x] JWT token decryption (من Master App)
- [x] Custom encryption token (للاختبار)
- [x] Auto-create User + Business + Subscription
- [x] Mapping من `app_id` لـ `plan_id`
- [x] Auto-login بعد الإنشاء
- [x] Redirect صحيح (admin/business dashboard)

### 5. App ID Mapping
في `app/Services/SSOService.php`:
```php
protected function mapAppIdToPlanId($appId): ?int
{
    $mapping = [
        12 => 2, // Plan B (30 days)
        13 => 3, // Plan C (180 days)
        // أضف المزيد حسب الحاجة
    ];
    return $mapping[$appId] ?? 2; // Default: Plan B
}
```

### 6. قبل الرفع
- [ ] امسح cache: `php artisan config:clear`
- [ ] امسح routes: `php artisan route:clear`
- [ ] تأكد من `.env` فيه SSO settings
- [ ] تأكد من Migrations تم تشغيلها
- [ ] جرب SSO مرة أخيرة على localhost
- [ ] احذف ملفات الاختبار (اختياري):
  - `test_jwt_sso.php`
  - `test_sso_auto.php`
  - `delete_sso_user.php`
  - `generate_sso_token.php`

### 7. على السيرفر (بعد الرفع)
```bash
# 1. رفع الملفات
git push origin main
# أو FTP/SFTP

# 2. تشغيل Migrations
php artisan migrate

# 3. مسح Cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# 4. تحقق من Routes
php artisan route:list | grep sso

# 5. تحقق من .env
cat .env | grep SSO
```

### 8. Testing على Production
```bash
# Test URL
https://your-domain.com/sso/auth?token=JWT_TOKEN_FROM_MASTER

# Check logs
tail -f storage/logs/laravel.log | grep SSO
```

---

## 📋 في Master App

### الملفات المطلوبة (3 ملفات فقط!)

#### 1. `app/Services/MasterSSOService.php`
انسخ من: `MASTER_APP_COPY_PASTE_AR.md`

#### 2. `config/sso.php`
```php
<?php
return [
    'secret_key' => env('SSO_SECRET_KEY', '6856fe49d4f8bc0830199edaefe41fa7922d0a323b07ea216dfbc96ce8257cca'),
    'sub_apps' => [
        'default_url' => env('SUB_APP_URL', 'https://nomupos.com'),
    ],
];
```

#### 3. `.env`
```env
SSO_SECRET_KEY=6856fe49d4f8bc0830199edaefe41fa7922d0a323b07ea216dfbc96ce8257cca
SUB_APP_URL=https://nomupos.com
```

### استخدام في Master App

#### في Controller:
```php
use App\Services\MasterSSOService;

public function redirectToSubApp(Request $request)
{
    $ssoService = new MasterSSOService();
    
    // بيانات المستخدم من JWT الموجود
    $jwtPayload = $this->decodeCurrentJWT(); // من الـ JWT الحالي
    
    // إنشاء URL للـ Sub App
    $subAppUrl = config('sso.sub_apps.default_url');
    $ssoUrl = $subAppUrl . '/sso/auth?token=' . $request->token;
    
    return redirect($ssoUrl);
}
```

أو إذا عايز تولد JWT جديد:
```php
// لو عندك JWT library في Master App
// استخدمه مباشرة، مش محتاج تعمل حاجة جديدة
// فقط redirect للـ Sub App مع الـ token الموجود
```

---

## 🔑 Secret Key (مهم جداً!)

```
6856fe49d4f8bc0830199edaefe41fa7922d0a323b07ea216dfbc96ce8257cca
```

- ✅ يجب أن يكون نفسه في Master App و Sub App
- ✅ لا تغيره إلا إذا كنت متأكد
- ✅ احفظه في مكان آمن

---

## 🎯 JWT Payload المطلوب

```json
{
  "iss": "marketplace",
  "sub": "140",              // user_id
  "email": "user@example.com",
  "name": "User Name",
  "app_id": 12,              // سيتم mapping لـ plan_id
  "subscription_ends": 1774882528,
  "iat": 1772290626,
  "exp": 2087650626,
  "jti": "unique-token-id"
}
```

### Mapping:
- `app_id: 12` → Plan B (30 days)
- `app_id: 13` → Plan C (180 days)

---

## 🚨 Troubleshooting

### مشكلة: "Route not found"
```bash
php artisan route:clear
php artisan config:clear
```

### مشكلة: "Invalid token"
- تحقق من Secret Key (نفسه في Master و Sub)
- تحقق من JWT format صحيح

### مشكلة: "Business creation failed"
- تحقق من Migrations تم تشغيلها
- تحقق من `business_categories` table فيها بيانات

### مشكلة: User يدخل كـ admin مش shop-owner
- تحقق من `app_id` موجود في JWT
- تحقق من mapping في `SSOService.php`

---

## 📊 ملخص سريع

### Sub App (nomupos.com):
1. ✅ SSO enabled في .env
2. ✅ Migrations تم تشغيلها
3. ✅ Routes موجودة: `/sso/auth` و `/sso/login`
4. ✅ يدعم JWT من Master App
5. ✅ ينشئ User + Business + Subscription تلقائياً

### Master App:
1. ⏳ أضف `MasterSSOService.php`
2. ⏳ أضف `config/sso.php`
3. ⏳ أضف SSO keys في `.env`
4. ⏳ Redirect للـ Sub App مع JWT token

---

## 🎉 جاهز للرفع!

بعد التأكد من كل النقاط أعلاه، المشروع جاهز للرفع على Production.

**تاريخ:** 2026-02-28  
**الحالة:** ✅ Ready for Production
