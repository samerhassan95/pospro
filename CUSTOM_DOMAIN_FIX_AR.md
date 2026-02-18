# إصلاح مشكلة CustomDomain Addon على السيرفر

## المشكلة
الـ CustomDomain addon بيشتغل على اللوكال لكن بيعمل مشاكل على السيرفر.

## السبب
المشكلة في الكود اللي بيعمل DNS lookup و HTTP requests في `DomainController`:

```php
$domainCheck = checkDomainStatus($request->domain);
```

الفانكشن `checkDomainStatus()` بتحاول تتحقق من:
1. DNS records (A/AAAA)
2. HTTP connectivity (port 80)
3. HTTPS connectivity (port 443)

وده ممكن يفشل على السيرفر بسبب:
- Firewall بيمنع outgoing requests
- DNS resolution مش شغال
- Timeout issues
- SSL certificate problems
- Server restrictions

## الحلول

### الحل 1: تعطيل الـ Automatic Approval (موصى به)
في لوحة التحكم الإدارية:
1. اذهب إلى: **Admin Panel** → **Settings** → **Domain Settings**
2. غير `automatic_approve` إلى `off`
3. غير `ssl_required` إلى `off` (اختياري)

بكده الدومينات هتتضاف بدون فحص تلقائي، وتقدر توافق عليها يدوياً.

### الحل 2: زيادة Timeout للـ HTTP Requests
في ملف `app/Helpers/Helper.php`، الفانكشن `checkDomainStatus()`:

```php
// قبل
$response = Http::timeout(5)->get("http://{$domain}");

// بعد
$response = Http::timeout(30)->get("http://{$domain}");
```

### الحل 3: إضافة Try-Catch للـ Domain Check
عدل الكود في `DomainController.php`:

```php
// قبل
$domainCheck = checkDomainStatus($request->domain_type === 'addon' ? $request->domain : get_root_domain());

// بعد
try {
    $domainCheck = checkDomainStatus($request->domain_type === 'addon' ? $request->domain : get_root_domain());
} catch (\Exception $e) {
    \Log::error('Domain check failed: ' . $e->getMessage());
    $domainCheck = ['exists' => false, 'http' => false, 'https' => false];
}
```

### الحل 4: تعطيل Domain Check على السيرفر فقط
أضف شرط في `.env`:

```env
DOMAIN_CHECK_ENABLED=false
```

ثم عدل الكود:

```php
if (env('DOMAIN_CHECK_ENABLED', true) && $automatic_approve === 'on') {
    $domainCheck = checkDomainStatus(...);
    // ... rest of code
}
```

### الحل 5: استخدام Queue للـ Domain Verification
بدل ما تعمل الفحص مباشرة، استخدم Queue:

```php
// في DomainController
Domain::create([...]);

// Dispatch job للفحص لاحقاً
VerifyDomainJob::dispatch($domain)->delay(now()->addMinutes(5));
```

## الحل السريع (Recommended)

عدل ملف `Modules/CustomDomainAddon/App/Http/Controllers/Business/DomainController.php`:

```php
// في الـ store method، استبدل السطر ده:
if ($automatic_approve === 'on') {
    $domainCheck = checkDomainStatus($request->domain_type === 'addon' ? $request->domain : get_root_domain());
    // ...
}

// بـ:
if ($automatic_approve === 'on') {
    try {
        $domainCheck = checkDomainStatus($request->domain_type === 'addon' ? $request->domain : get_root_domain());
        
        if ($domainCheck['exists']) {
            $domain_status = 1;
        }
        if ($domainCheck['https']) {
            $is_ssl_enabled = 1;
        }
        if ($domain_status && ($ssl_required === 'off' || ($ssl_required === 'on' && $is_ssl_enabled))) {
            $is_verified = 1;
        }
    } catch (\Exception $e) {
        \Log::error('Domain check failed', [
            'domain' => $request->domain,
            'error' => $e->getMessage()
        ]);
        // في حالة الفشل، خلي الدومين يتضاف بدون verification
        // وهيحتاج موافقة يدوية من الأدمن
    }
}
```

## التحقق من الإصلاح

1. جرب تضيف دومين جديد
2. لو نجح، يبقى المشكلة اتحلت
3. لو لسه فيه مشكلة، شوف الـ logs:
```bash
tail -f storage/logs/laravel.log
```

## ملاحظات

- الحل الأفضل هو تعطيل الـ automatic approval على السيرفر
- لو عايز automatic approval، استخدم Queue
- تأكد إن السيرفر يسمح بـ outgoing HTTP requests
- تأكد إن الـ DNS resolution شغال على السيرفر
