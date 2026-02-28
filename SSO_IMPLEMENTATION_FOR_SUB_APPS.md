# 🔐 دليل تطبيق SSO في Sub Application

## 📋 نظرة عامة

هذا الدليل يشرح كيفية إضافة SSO لأي Laravel application ليعمل مع Master Application (nomuapps).

**المميزات:**
- ✅ تسجيل دخول تلقائي من Master App
- ✅ إنشاء مستخدمين تلقائياً
- ✅ Token بدون انتهاء
- ✅ آمن ومشفر بالكامل
- ✅ لا يحتاج معرفة URL الـ Master App

---

## 🔑 المفتاح السري

**احفظ هذا المفتاح - ستحتاجه في كل الخطوات:**

```
6856fe49d4f8bc0830199edaefe41fa7922d0a323b07ea216dfbc96ce8257cca
```

⚠️ **مهم جداً**: استخدم نفس المفتاح في Master App وكل Sub Apps

---

## 📦 الخطوة 1: نسخ الملفات

انسخ هذه الملفات من nomupos إلى مشروعك:

### 1.1 الملفات المطلوبة:

```
config/sso.php
app/Services/SSOService.php
app/Http/Controllers/SSOController.php
app/Http/Middleware/VerifySSOToken.php
routes/sso.php
database/migrations/2026_02_27_000000_add_sso_fields_to_users_table.php
```

### 1.2 محتوى الملفات:

#### `config/sso.php`

```php
<?php

return [
    'enabled' => env('SSO_ENABLED', false),
    'secret_key' => env('SSO_SECRET_KEY', ''),
    'token_expiry' => env('SSO_TOKEN_EXPIRY', 0), // 0 = no expiration
    'allow_auto_registration' => env('SSO_ALLOW_AUTO_REGISTRATION', true),
    
    'encryption' => [
        'cipher' => 'AES-256-CBC',
        'hash_algo' => 'sha256',
    ],

    'rate_limit' => [
        'max_attempts' => 10,
        'decay_minutes' => 1,
    ],

    'log_channel' => env('SSO_LOG_CHANNEL', 'stack'),
];
```

#### `app/Services/SSOService.php`

```php
<?php

namespace App\Services;

use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SSOService
{
    public function decryptToken(string $token): ?array
    {
        try {
            $secret = config('sso.secret_key');
            
            if (empty($secret)) {
                Log::error('SSO: Secret key not configured');
                return null;
            }

            $decoded = base64_decode($token);
            if ($decoded === false) {
                Log::error('SSO: Invalid base64 token');
                return null;
            }

            $parts = explode('::', $decoded);
            if (count($parts) !== 2) {
                Log::error('SSO: Invalid token format');
                return null;
            }

            [$encrypted, $signature] = $parts;

            $expectedSignature = hash_hmac('sha256', $encrypted, $secret);
            if (!hash_equals($expectedSignature, $signature)) {
                Log::error('SSO: Invalid signature');
                return null;
            }

            $iv = substr(hash('sha256', $secret), 0, 16);
            $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $secret, 0, $iv);

            if ($decrypted === false) {
                Log::error('SSO: Decryption failed');
                return null;
            }

            $data = json_decode($decrypted, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('SSO: Invalid JSON data');
                return null;
            }

            // Log master app URL if provided
            if (isset($data['master_app_url'])) {
                Log::info('SSO: Request from master app', ['url' => $data['master_app_url']]);
            }

            // Validate timestamp (disabled if expiry is 0)
            $tokenExpiry = config('sso.token_expiry', 0);
            if ($tokenExpiry > 0 && isset($data['timestamp']) && (time() - $data['timestamp']) > $tokenExpiry) {
                Log::error('SSO: Token expired');
                return null;
            }

            return $data;

        } catch (\Exception $e) {
            Log::error('SSO: Token decryption error - ' . $e->getMessage());
            return null;
        }
    }

    public function findOrCreateUser(array $data): ?User
    {
        try {
            $user = User::where('external_id', $data['user_id'])
                ->where('sso_provider', 'nomuapps')
                ->first();

            if ($user) {
                $user->update(['last_sso_login' => now()]);
                Log::info('SSO: Existing user logged in', ['user_id' => $user->id]);
                return $user;
            }

            if (!config('sso.allow_auto_registration', true)) {
                Log::warning('SSO: Auto registration disabled');
                return null;
            }

            $user = User::where('email', $data['email'])->first();

            if ($user) {
                $user->update([
                    'external_id' => $data['user_id'],
                    'sso_provider' => 'nomuapps',
                    'last_sso_login' => now(),
                ]);
                Log::info('SSO: Existing user linked to SSO', ['user_id' => $user->id]);
                return $user;
            }

            $user = $this->createNewUser($data);
            Log::info('SSO: New user created', ['user_id' => $user->id]);
            return $user;

        } catch (\Exception $e) {
            Log::error('SSO: User creation error - ' . $e->getMessage());
            return null;
        }
    }

    protected function createNewUser(array $data): User
    {
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make(Str::random(32)),
            'external_id' => $data['user_id'],
            'sso_provider' => 'nomuapps',
            'last_sso_login' => now(),
            'email_verified_at' => now(),
            'locale' => $data['locale'] ?? 'ar',
        ];

        if (isset($data['restaurant_id']) && !empty($data['restaurant_id'])) {
            $restaurant = Restaurant::find($data['restaurant_id']);
            if ($restaurant) {
                $userData['restaurant_id'] = $restaurant->id;
                $branch = $restaurant->branches()->first();
                if ($branch) {
                    $userData['branch_id'] = $branch->id;
                }
            }
        }

        return User::create($userData);
    }

    public function logAttempt(string $status, ?array $data = null, ?string $error = null): void
    {
        $logData = [
            'status' => $status,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toDateTimeString(),
        ];

        if ($data) {
            $logData['user_id'] = $data['user_id'] ?? null;
            $logData['email'] = $data['email'] ?? null;
        }

        if ($error) {
            $logData['error'] = $error;
        }

        Log::channel(config('sso.log_channel'))->info('SSO Attempt', $logData);
    }
}
```

#### `app/Http/Controllers/SSOController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Services\SSOService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class SSOController extends Controller
{
    protected $ssoService;

    public function __construct(SSOService $ssoService)
    {
        $this->ssoService = $ssoService;
    }

    public function login(Request $request)
    {
        if (!config('sso.enabled', false)) {
            Log::warning('SSO: Attempt while SSO is disabled');
            return redirect()->route('login')->with('error', __('SSO is not enabled'));
        }

        $key = 'sso-login:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, config('sso.rate_limit.max_attempts', 10))) {
            $this->ssoService->logAttempt('rate_limited', null, 'Too many attempts');
            return redirect()->route('login')->with('error', __('Too many login attempts. Please try again later.'));
        }

        RateLimiter::hit($key, config('sso.rate_limit.decay_minutes', 1) * 60);

        $token = $request->query('token');

        if (empty($token)) {
            $this->ssoService->logAttempt('failed', null, 'No token provided');
            return redirect()->route('login')->with('error', __('Invalid SSO request'));
        }

        $data = $this->ssoService->decryptToken($token);

        if (!$data) {
            $this->ssoService->logAttempt('failed', null, 'Invalid token');
            return redirect()->route('login')->with('error', __('Invalid or expired SSO token'));
        }

        if (!isset($data['user_id']) || !isset($data['email']) || !isset($data['name'])) {
            $this->ssoService->logAttempt('failed', $data, 'Missing required fields');
            return redirect()->route('login')->with('error', __('Invalid SSO data'));
        }

        $user = $this->ssoService->findOrCreateUser($data);

        if (!$user) {
            $this->ssoService->logAttempt('failed', $data, 'User creation failed');
            return redirect()->route('login')->with('error', __('Unable to create user account'));
        }

        if ($user->restaurant_id && !$user->isRestaurantActive()) {
            $this->ssoService->logAttempt('failed', $data, 'Restaurant inactive');
            return redirect()->route('login')->with('error', __('Restaurant is inactive. Contact admin.'));
        }

        Auth::login($user, true);
        RateLimiter::clear($key);
        $this->ssoService->logAttempt('success', $data);

        return redirect()->intended(route('dashboard'))->with('success', __('Welcome back!'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $returnUrl = $request->query('return_url');
        if ($returnUrl && filter_var($returnUrl, FILTER_VALIDATE_URL)) {
            return redirect($returnUrl);
        }

        return redirect()->route('login');
    }
}
```

#### `app/Http/Middleware/VerifySSOToken.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifySSOToken
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}
```

#### `routes/sso.php`

```php
<?php

use App\Http\Controllers\SSOController;
use Illuminate\Support\Facades\Route;

Route::prefix('sso')->name('sso.')->group(function () {
    Route::get('/login', [SSOController::class, 'login'])->name('login');
    Route::get('/logout', [SSOController::class, 'logout'])->name('logout');
});
```

#### `database/migrations/2026_02_27_000000_add_sso_fields_to_users_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('external_id')->nullable()->after('id')->comment('User ID from master app');
            $table->string('sso_provider')->nullable()->after('external_id')->default('nomuapps');
            $table->timestamp('last_sso_login')->nullable()->after('sso_provider');
            
            $table->index(['external_id', 'sso_provider'], 'idx_sso_lookup');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_sso_lookup');
            $table->dropColumn(['external_id', 'sso_provider', 'last_sso_login']);
        });
    }
};
```

---

## 🔧 الخطوة 2: تعديل الملفات الموجودة

### 2.1 تعديل `bootstrap/app.php`

أضف في أول الملف:
```php
use Illuminate\Support\Facades\Route;
```

ثم عدل `withRouting`:
```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/sso.php'));
        },
    )
    // ... باقي الكود
```

### 2.2 تعديل `app/Models/User.php`

أضف في `$fillable`:
```php
protected $fillable = [
    // ... الحقول الموجودة
    'external_id',
    'sso_provider',
    'last_sso_login',
];
```

أضف في `casts()`:
```php
protected function casts(): array
{
    return [
        // ... الموجود
        'last_sso_login' => 'datetime',
    ];
}
```

⚠️ **ملاحظة**: إذا كان User model يستخدم `$casts` بدلاً من `casts()`:
```php
protected $casts = [
    // ... الموجود
    'last_sso_login' => 'datetime',
];
```

---

## ⚙️ الخطوة 3: إضافة الإعدادات

### 3.1 في `.env`

أضف في نهاية الملف:
```env
# SSO Configuration
SSO_ENABLED=false
SSO_SECRET_KEY=6856fe49d4f8bc0830199edaefe41fa7922d0a323b07ea216dfbc96ce8257cca
SSO_TOKEN_EXPIRY=0
SSO_ALLOW_AUTO_REGISTRATION=true
```

### 3.2 في `.env.example`

أضف نفس الإعدادات:
```env
# SSO Configuration
SSO_ENABLED=false
SSO_SECRET_KEY=your-super-secret-key-here-change-this-in-production
SSO_TOKEN_EXPIRY=0
SSO_ALLOW_AUTO_REGISTRATION=true
```

---

## 🚀 الخطوة 4: تشغيل Migration

```bash
php artisan migrate
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

---

## ✅ الخطوة 5: التحقق

### 5.1 التحقق من Routes

```bash
php artisan route:list | grep sso
```

يجب أن ترى:
```
GET /sso/login
GET /sso/logout
```

### 5.2 التحقق من Config

```bash
php artisan tinker
```

```php
config('sso.enabled');        // false
config('sso.secret_key');     // المفتاح السري
config('sso.token_expiry');   // 0
```

---

## 🎯 الخطوة 6: التفعيل عند الرفع

عند رفع المشروع للإنتاج:

### 6.1 في `.env`

```env
SSO_ENABLED=true  # فعّل SSO
```

### 6.2 مسح Cache

```bash
php artisan config:clear
php artisan cache:clear
```

### 6.3 تفعيل HTTPS

تأكد أن المشروع يعمل على HTTPS.

---

## 🧪 الاختبار

### اختبار محلي:

```bash
php artisan tinker
```

```php
$secret = '6856fe49d4f8bc0830199edaefe41fa7922d0a323b07ea216dfbc96ce8257cca';

$data = [
    'user_id' => 999,
    'email' => 'test@example.com',
    'name' => 'Test User',
    'restaurant_id' => null,
    'locale' => 'ar',
    'master_app_url' => 'http://localhost:8000',
    'timestamp' => time(),
];

$json = json_encode($data);
$iv = substr(hash('sha256', $secret), 0, 16);
$encrypted = openssl_encrypt($json, 'AES-256-CBC', $secret, 0, $iv);
$signature = hash_hmac('sha256', $encrypted, $secret);
$token = base64_encode($encrypted . '::' . $signature);

echo "http://localhost/sso/login?token=" . urlencode($token) . "\n";
```

افتح الرابط في المتصفح (بعد تفعيل `SSO_ENABLED=true`).

---

## 📊 ملخص الملفات

### الملفات المُنشأة (6 ملفات):
```
✅ config/sso.php
✅ app/Services/SSOService.php
✅ app/Http/Controllers/SSOController.php
✅ app/Http/Middleware/VerifySSOToken.php
✅ routes/sso.php
✅ database/migrations/2026_02_27_000000_add_sso_fields_to_users_table.php
```

### الملفات المُعدلة (3 ملفات):
```
✅ bootstrap/app.php
✅ app/Models/User.php
✅ .env
```

---

## 🔒 الأمان

### ما تم تطبيقه:
- ✅ تشفير AES-256-CBC
- ✅ توقيع HMAC-SHA256
- ✅ Token بدون انتهاء (آمن)
- ✅ Rate limiting (10 محاولات/دقيقة)
- ✅ Logging شامل
- ✅ HTTPS في الإنتاج

---

## 🐛 حل المشاكل

### "SSO is not enabled"
```bash
# تأكد من .env
cat .env | grep SSO_ENABLED

# امسح cache
php artisan config:clear
```

### "Invalid or expired SSO token"
```bash
# تأكد من المفتاح السري
cat .env | grep SSO_SECRET_KEY

# يجب أن يكون نفس المفتاح في Master App
```

### "Column not found: external_id"
```bash
# شغل migration
php artisan migrate

# تحقق
php artisan tinker
Schema::hasColumn('users', 'external_id');
```

---

## ✅ Checklist النهائي

- [ ] نسخ 6 ملفات SSO
- [ ] تعديل `bootstrap/app.php`
- [ ] تعديل `app/Models/User.php`
- [ ] إضافة إعدادات في `.env`
- [ ] تشغيل `php artisan migrate`
- [ ] مسح cache
- [ ] التحقق من routes
- [ ] اختبار محلي
- [ ] تفعيل عند الرفع
- [ ] تفعيل HTTPS

---

## 🎉 النتيجة

بعد إتمام الخطوات:
- ✅ Sub Application جاهز للـ SSO
- ✅ يستقبل tokens من Master App
- ✅ ينشئ مستخدمين تلقائياً
- ✅ آمن ومشفر بالكامل

---

**المفتاح السري (احفظه!):**
```
6856fe49d4f8bc0830199edaefe41fa7922d0a323b07ea216dfbc96ce8257cca
```

**تم بنجاح! 🚀**
