# 🚀 دليل نشر SSO على مشروع حقيقي (Production)

## المشروع الحقيقي = Master App + Sub Apps

```
Master App (التطبيق الرئيسي)
    ↓
    يرسل Users مع Tokens
    ↓
Sub App 1, Sub App 2, Sub App 3... (التطبيقات الفرعية)
```

---

## 📋 الخطوات الكاملة

### 1️⃣ في Master App (التطبيق الرئيسي)

#### أ. إضافة SSO Secret Key
```env
# في ملف .env
SSO_SECRET_KEY=6856fe49d4f8bc0830199edaefe41fa7922d0a323b07ea216dfbc96ce8257cca
```

#### ب. إنشاء Service لتوليد Tokens

**ملف:** `app/Services/MasterSSOService.php`

```php
<?php

namespace App\Services;

class MasterSSOService
{
    private $secret;
    
    public function __construct()
    {
        $this->secret = config('sso.secret_key');
    }
    
    /**
     * Generate SSO token for user
     */
    public function generateToken(array $userData): string
    {
        // Add timestamp
        $userData['timestamp'] = time();
        
        // Encrypt
        $json = json_encode($userData);
        $iv = substr(hash('sha256', $this->secret), 0, 16);
        $encrypted = openssl_encrypt($json, 'AES-256-CBC', $this->secret, 0, $iv);
        
        // Sign
        $signature = hash_hmac('sha256', $encrypted, $this->secret);
        
        // Encode
        return base64_encode($encrypted . '::' . $signature);
    }
    
    /**
     * Generate SSO URL for sub app
     */
    public function generateSSOUrl(string $subAppUrl, array $userData): string
    {
        $token = $this->generateToken($userData);
        return $subAppUrl . '/sso/login?token=' . urlencode($token);
    }
}
```

#### ج. إنشاء Controller للـ Redirect

**ملف:** `app/Http/Controllers/SSORedirectController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Services\MasterSSOService;
use Illuminate\Http\Request;

class SSORedirectController extends Controller
{
    protected $ssoService;
    
    public function __construct(MasterSSOService $ssoService)
    {
        $this->ssoService = $ssoService;
    }
    
    /**
     * Redirect user to sub app with SSO
     */
    public function redirectToSubApp(Request $request)
    {
        $user = auth()->user();
        $subscription = $request->subscription; // من الـ form
        
        // Prepare user data
        $userData = [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'plan_id' => $subscription->plan_id,
            'business_name' => $request->business_name,
            'phone' => $request->phone,
            'locale' => app()->getLocale(),
        ];
        
        // Add optional B2B data if provided
        if ($request->filled('vat_no')) {
            $userData['vat_no'] = $request->vat_no;
        }
        if ($request->filled('commercial_registration')) {
            $userData['commercial_registration'] = $request->commercial_registration;
        }
        
        // Generate SSO URL
        $subAppUrl = config('services.sub_apps.default_url');
        $ssoUrl = $this->ssoService->generateSSOUrl($subAppUrl, $userData);
        
        // Log the redirect
        \Log::info('SSO Redirect', [
            'user_id' => $user->id,
            'sub_app' => $subAppUrl,
            'plan_id' => $subscription->plan_id
        ]);
        
        // Redirect
        return redirect($ssoUrl);
    }
}
```

#### د. إضافة Routes

**ملف:** `routes/web.php`

```php
use App\Http\Controllers\SSORedirectController;

Route::middleware(['auth'])->group(function () {
    Route::post('/redirect-to-sub-app', [SSORedirectController::class, 'redirectToSubApp'])
        ->name('sso.redirect');
});
```

#### هـ. إضافة Config

**ملف:** `config/sso.php`

```php
<?php

return [
    'secret_key' => env('SSO_SECRET_KEY'),
    
    'sub_apps' => [
        'default_url' => env('SUB_APP_URL', 'https://sub-app.com'),
        
        // يمكن إضافة sub apps متعددة
        'app1' => env('SUB_APP_1_URL'),
        'app2' => env('SUB_APP_2_URL'),
    ],
];
```

#### و. إضافة في .env

```env
SSO_SECRET_KEY=6856fe49d4f8bc0830199edaefe41fa7922d0a323b07ea216dfbc96ce8257cca
SUB_APP_URL=https://your-sub-app.com
```

---

### 2️⃣ في Sub App (التطبيق الفرعي) - هذا المشروع

#### أ. تفعيل SSO

```env
# في ملف .env
SSO_ENABLED=true
SSO_SECRET_KEY=6856fe49d4f8bc0830199edaefe41fa7922d0a323b07ea216dfbc96ce8257cca
SSO_TOKEN_EXPIRY=0
SSO_ALLOW_AUTO_REGISTRATION=true
```

#### ب. رفع الملفات على السيرفر

```bash
# 1. رفع الكود
git push origin main

# أو عبر FTP/SFTP:
# - app/Services/SSOService.php
# - app/Http/Controllers/SSOController.php
# - app/Http/Middleware/VerifySSOToken.php
# - routes/sso.php
# - config/sso.php
```

#### ج. تشغيل Migration

```bash
# على السيرفر
php artisan migrate

# تحديداً:
# - 2026_02_27_000000_add_sso_fields_to_users_table.php
# - 2026_02_28_000001_add_branch_id_to_hrm_tables.php
```

#### د. مسح Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

#### هـ. التحقق من Routes

```bash
php artisan route:list | grep sso

# يجب أن يظهر:
# GET|HEAD  sso/login   sso.login
# GET|HEAD  sso/logout  sso.logout
```

---

### 3️⃣ اختبار على Production

#### أ. اختبار من Master App

**في Master App:**
```php
// في Controller أو Service
$ssoService = new MasterSSOService();

$userData = [
    'user_id' => auth()->user()->id,
    'name' => auth()->user()->name,
    'email' => auth()->user()->email,
    'plan_id' => 2, // الباقة المختارة
    'business_name' => 'متجر تجريبي',
    'phone' => '0501234567',
];

$ssoUrl = $ssoService->generateSSOUrl(
    'https://your-sub-app.com',
    $userData
);

return redirect($ssoUrl);
```

#### ب. اختبار يدوي

```bash
# 1. ولّد Token على السيرفر
php generate_sso_token.php

# 2. انسخ الـ Token

# 3. افتح في المتصفح:
https://your-sub-app.com/sso/login?token=YOUR_TOKEN
```

#### ج. مراقبة Logs

```bash
# على السيرفر
tail -f storage/logs/laravel.log | grep SSO

# يجب أن تشوف:
# [INFO] SSO: Business created
# [INFO] SSO: Subscription created
# [INFO] SSO: New user created
```

---

### 4️⃣ الأمان في Production

#### أ. HTTPS إجباري

```php
// في app/Http/Middleware/VerifySSOToken.php
public function handle($request, Closure $next)
{
    // Force HTTPS in production
    if (app()->environment('production') && !$request->secure()) {
        abort(403, 'HTTPS required for SSO');
    }
    
    // ... rest of code
}
```

#### ب. Rate Limiting

```php
// في routes/sso.php
Route::middleware(['throttle:10,1'])->group(function () {
    Route::get('/sso/login', [SSOController::class, 'login'])->name('sso.login');
    Route::post('/sso/login', [SSOController::class, 'login']);
});
```

#### ج. IP Whitelist (اختياري)

```php
// في config/sso.php
'allowed_ips' => [
    '123.456.789.0', // Master App IP
],

// في SSOController
public function login(Request $request)
{
    $allowedIps = config('sso.allowed_ips', []);
    
    if (!empty($allowedIps) && !in_array($request->ip(), $allowedIps)) {
        abort(403, 'Unauthorized IP');
    }
    
    // ... rest of code
}
```

---

### 5️⃣ Monitoring & Logging

#### أ. إضافة Logging مفصل

```php
// في SSOService.php
public function findOrCreateUser(array $data): ?User
{
    \Log::channel('sso')->info('SSO Login Attempt', [
        'user_id' => $data['user_id'] ?? null,
        'email' => $data['email'] ?? null,
        'ip' => request()->ip(),
        'user_agent' => request()->userAgent(),
    ]);
    
    // ... rest of code
}
```

#### ب. إنشاء Log Channel منفصل

```php
// في config/logging.php
'channels' => [
    'sso' => [
        'driver' => 'daily',
        'path' => storage_path('logs/sso.log'),
        'level' => 'info',
        'days' => 30,
    ],
],
```

#### ج. مراقبة الأخطاء

```bash
# على السيرفر
tail -f storage/logs/sso.log

# أو استخدم monitoring service مثل:
# - Sentry
# - Bugsnag
# - Laravel Telescope
```

---

### 6️⃣ Backup & Recovery

#### أ. Backup قبل النشر

```bash
# Backup Database
php artisan backup:run

# أو يدوياً:
mysqldump -u username -p database_name > backup_before_sso.sql
```

#### ب. Rollback Plan

```bash
# إذا حصلت مشكلة:

# 1. عطّل SSO
SSO_ENABLED=false

# 2. امسح Cache
php artisan config:clear

# 3. Rollback Migration (إذا لزم)
php artisan migrate:rollback --step=1
```

---

### 7️⃣ Testing Checklist

قبل النشر النهائي، تأكد من:

- [ ] ✅ SSO enabled في .env
- [ ] ✅ Secret key نفسه في Master و Sub Apps
- [ ] ✅ HTTPS شغال
- [ ] ✅ Migrations تم تشغيلها
- [ ] ✅ Routes موجودة
- [ ] ✅ Cache تم مسحه
- [ ] ✅ Logs شغالة
- [ ] ✅ اختبار إنشاء user جديد
- [ ] ✅ اختبار إنشاء business
- [ ] ✅ اختبار إنشاء subscription
- [ ] ✅ اختبار login بعد الإنشاء
- [ ] ✅ اختبار logout
- [ ] ✅ اختبار مع باقات مختلفة (A, B, C)

---

### 8️⃣ Documentation للفريق

#### أ. للمطورين في Master App

```markdown
# كيفية إرسال User لـ Sub App

1. استخدم MasterSSOService
2. حدد plan_id من الاشتراك
3. أضف بيانات المستخدم
4. ولّد SSO URL
5. Redirect المستخدم

مثال:
$ssoUrl = $ssoService->generateSSOUrl($subAppUrl, $userData);
return redirect($ssoUrl);
```

#### ب. للمطورين في Sub App

```markdown
# SSO Endpoints

- GET/POST /sso/login - تسجيل دخول
- GET /sso/logout - تسجيل خروج

# ما يحدث تلقائياً:
- إنشاء User
- إنشاء Business
- إنشاء Subscription
- تسجيل دخول
```

---

### 9️⃣ Troubleshooting في Production

#### مشكلة: "SSO is not enabled"
```bash
# تحقق من .env
grep SSO_ENABLED .env

# امسح cache
php artisan config:clear
```

#### مشكلة: "Invalid token"
```bash
# تحقق من Secret Key
grep SSO_SECRET_KEY .env

# يجب أن يكون نفسه في Master و Sub
```

#### مشكلة: "Business creation failed"
```bash
# شوف الـ logs
tail -f storage/logs/laravel.log

# تحقق من Database permissions
# تحقق من business_category_id
```

---

### 🔟 Performance Optimization

#### أ. Cache Plans

```php
// في SSOService
protected function getPlan($planId)
{
    return cache()->remember("plan_{$planId}", 3600, function() use ($planId) {
        return \App\Models\Plan::find($planId);
    });
}
```

#### ب. Queue للعمليات الثقيلة (اختياري)

```php
// إذا كان إنشاء Business بطيء
dispatch(new CreateBusinessJob($userData))->afterResponse();
```

---

## 📊 ملخص سريع

### في Master App:
1. ✅ أضف SSO Service
2. ✅ أضف Controller للـ redirect
3. ✅ ولّد Token
4. ✅ Redirect المستخدم

### في Sub App:
1. ✅ فعّل SSO في .env
2. ✅ شغّل Migrations
3. ✅ امسح Cache
4. ✅ اختبر

### الأمان:
1. ✅ HTTPS إجباري
2. ✅ Rate limiting
3. ✅ Logging
4. ✅ Monitoring

---

**آخر تحديث:** 2026-02-28  
**الحالة:** ✅ جاهز للنشر  
**الإصدار:** 1.0.0 Production Ready
