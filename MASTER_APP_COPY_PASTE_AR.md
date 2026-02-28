# 📋 كود جاهز للنسخ - Master App

## الملفات المطلوبة (3 ملفات فقط!)

---

## 1️⃣ ملف: `app/Services/MasterSSOService.php`

**انسخ هذا الملف كامل:**

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

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
        
        // Add master app URL
        $userData['master_app_url'] = config('app.url');
        
        // Encrypt
        $json = json_encode($userData);
        $iv = substr(hash('sha256', $this->secret), 0, 16);
        $encrypted = openssl_encrypt($json, 'AES-256-CBC', $this->secret, 0, $iv);
        
        // Sign
        $signature = hash_hmac('sha256', $encrypted, $this->secret);
        
        // Encode
        $token = base64_encode($encrypted . '::' . $signature);
        
        // Log
        Log::info('SSO Token Generated', [
            'user_id' => $userData['user_id'] ?? null,
            'email' => $userData['email'] ?? null,
            'plan_id' => $userData['plan_id'] ?? null,
        ]);
        
        return $token;
    }
    
    /**
     * Generate SSO URL for sub app
     */
    public function generateSSOUrl(string $subAppUrl, array $userData): string
    {
        $token = $this->generateToken($userData);
        return $subAppUrl . '/sso/login?token=' . urlencode($token);
    }
    
    /**
     * Quick redirect to sub app
     */
    public function redirectToSubApp(array $userData, string $subAppUrl = null)
    {
        $subAppUrl = $subAppUrl ?? config('sso.sub_apps.default_url');
        $ssoUrl = $this->generateSSOUrl($subAppUrl, $userData);
        
        return redirect($ssoUrl);
    }
}
```

---

## 2️⃣ ملف: `config/sso.php`

**انسخ هذا الملف كامل:**

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SSO Secret Key
    |--------------------------------------------------------------------------
    |
    | This key is used to encrypt and sign SSO tokens.
    | MUST be the same in Master App and all Sub Apps.
    |
    */
    'secret_key' => env('SSO_SECRET_KEY', '6856fe49d4f8bc0830199edaefe41fa7922d0a323b07ea216dfbc96ce8257cca'),
    
    /*
    |--------------------------------------------------------------------------
    | Sub Apps URLs
    |--------------------------------------------------------------------------
    |
    | URLs of your sub applications that will receive SSO tokens.
    |
    */
    'sub_apps' => [
        'default_url' => env('SUB_APP_URL', 'http://127.0.0.1:8000'),
        
        // Add more sub apps if needed
        // 'app1' => env('SUB_APP_1_URL'),
        // 'app2' => env('SUB_APP_2_URL'),
    ],
];
```

---

## 3️⃣ في ملف: `.env`

**أضف هذه الأسطر:**

```env
# SSO Configuration
SSO_SECRET_KEY=6856fe49d4f8bc0830199edaefe41fa7922d0a323b07ea216dfbc96ce8257cca
SUB_APP_URL=http://127.0.0.1:8000
```

⚠️ **مهم:** غيّر `SUB_APP_URL` لـ URL الـ Sub App الحقيقي

---

## 4️⃣ استخدام في الكود

### مثال 1: في Controller

```php
<?php

namespace App\Http\Controllers;

use App\Services\MasterSSOService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    protected $ssoService;
    
    public function __construct(MasterSSOService $ssoService)
    {
        $this->ssoService = $ssoService;
    }
    
    /**
     * بعد ما المستخدم يختار باقة ويدفع
     */
    public function redirectToSubApp(Request $request)
    {
        $user = auth()->user();
        
        // بيانات المستخدم
        $userData = [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'plan_id' => $request->plan_id, // 1, 2, or 3
            'business_name' => $request->business_name ?? $user->name . ' Business',
            'phone' => $request->phone ?? $user->phone,
            'locale' => app()->getLocale(),
        ];
        
        // إضافة بيانات B2B (اختياري)
        if ($request->filled('vat_no')) {
            $userData['vat_no'] = $request->vat_no;
        }
        if ($request->filled('commercial_registration')) {
            $userData['commercial_registration'] = $request->commercial_registration;
        }
        
        // Redirect للـ Sub App
        return $this->ssoService->redirectToSubApp($userData);
    }
}
```

### مثال 2: استخدام مباشر

```php
use App\Services\MasterSSOService;

// في أي مكان في الكود
$ssoService = new MasterSSOService();

$userData = [
    'user_id' => auth()->id(),
    'name' => auth()->user()->name,
    'email' => auth()->user()->email,
    'plan_id' => 2, // الباقة المختارة
    'business_name' => 'متجر تجريبي',
];

return $ssoService->redirectToSubApp($userData);
```

### مثال 3: في Blade Template

```blade
{{-- في صفحة اختيار الباقة --}}
<form action="{{ route('subscription.redirect') }}" method="POST">
    @csrf
    
    <input type="hidden" name="plan_id" value="2">
    
    <div class="form-group">
        <label>اسم المتجر</label>
        <input type="text" name="business_name" class="form-control" required>
    </div>
    
    <div class="form-group">
        <label>رقم الهاتف</label>
        <input type="text" name="phone" class="form-control">
    </div>
    
    <div class="form-group">
        <label>الرقم الضريبي (اختياري)</label>
        <input type="text" name="vat_no" class="form-control">
    </div>
    
    <button type="submit" class="btn btn-primary">
        ابدأ الآن
    </button>
</form>
```

---

## 5️⃣ إضافة Route

**في ملف:** `routes/web.php`

```php
use App\Http\Controllers\SubscriptionController;

Route::middleware(['auth'])->group(function () {
    Route::post('/subscription/redirect', [SubscriptionController::class, 'redirectToSubApp'])
        ->name('subscription.redirect');
});
```

---

## 🧪 اختبار سريع

### في Tinker:

```bash
php artisan tinker
```

```php
$ssoService = new App\Services\MasterSSOService();

$userData = [
    'user_id' => 999,
    'name' => 'Test User',
    'email' => 'test@example.com',
    'plan_id' => 1,
    'business_name' => 'Test Business',
];

$url = $ssoService->generateSSOUrl('http://127.0.0.1:8000', $userData);

echo $url;
// انسخ الـ URL وافتحه في المتصفح
```

---

## 📝 ملاحظات مهمة

### 1. Secret Key
```
6856fe49d4f8bc0830199edaefe41fa7922d0a323b07ea216dfbc96ce8257cca
```
- ✅ يجب أن يكون نفسه في Master App و Sub App
- ✅ لا تغيره إلا إذا كنت متأكد
- ✅ احفظه في مكان آمن

### 2. Plan IDs
- `1` = Plan A (7 days, Free)
- `2` = Plan B (30 days, 10 SAR)
- `3` = Plan C (180 days, 60 SAR)

### 3. البيانات المطلوبة (Required)
- `user_id` - معرف المستخدم
- `name` - اسم المستخدم
- `email` - البريد الإلكتروني
- `plan_id` - رقم الباقة (1, 2, أو 3)

### 4. البيانات الاختيارية (Optional)
- `business_name` - اسم المتجر
- `phone` - رقم الهاتف
- `vat_no` - الرقم الضريبي
- `commercial_registration` - السجل التجاري
- `building_number` - رقم المبنى
- `street_name` - اسم الشارع
- `district` - الحي
- `city` - المدينة
- `postal_code` - الرمز البريدي
- `country_code` - رمز الدولة (مثل: SA)
- `locale` - اللغة (ar أو en)

---

## ✅ Checklist

- [ ] نسخت `MasterSSOService.php`
- [ ] نسخت `config/sso.php`
- [ ] أضفت في `.env`:
  - [ ] `SSO_SECRET_KEY`
  - [ ] `SUB_APP_URL`
- [ ] أضفت Route
- [ ] استخدمت في Controller
- [ ] جربت في Tinker
- [ ] فتحت الـ URL في المتصفح

---

## 🚀 جاهز!

بعد نسخ الملفات:
1. ✅ امسح cache: `php artisan config:clear`
2. ✅ جرب في Tinker
3. ✅ افتح الـ URL في المتصفح
4. ✅ شوف المستخدم اتسجل في Sub App

---

**وقت التنفيذ:** 5-10 دقائق فقط! ⚡

**تاريخ الإنشاء:** 2026-02-28  
**الحالة:** ✅ جاهز للنسخ واللصق
