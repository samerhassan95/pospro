# 🚀 دليل بسيط للـ Master App

## الوضع الحالي

الـ Master App عندك بيولد JWT tokens بالفعل، وفيها:
- `app_id: 12` (للتطبيق)
- بيانات المستخدم (email, name, sub)
- تاريخ انتهاء الاشتراك

## ✅ ما تحتاجه (بسيط جداً!)

### الطريقة الأسهل: Redirect مباشر

لما المستخدم يختار تطبيق ويدفع، فقط redirect له:

```php
// في Controller بتاعك
public function launchApp(Request $request)
{
    // الـ JWT token الموجود بالفعل
    $jwtToken = $request->token; // أو من session/database
    
    // URL الـ Sub App
    $subAppUrl = 'https://nomupos.com';
    
    // Redirect
    return redirect($subAppUrl . '/sso/auth?token=' . $jwtToken);
}
```

### في Blade Template:

```blade
{{-- بعد الدفع الناجح --}}
<a href="https://nomupos.com/sso/auth?token={{ $jwtToken }}" 
   class="btn btn-primary">
    ابدأ استخدام التطبيق
</a>
```

أو JavaScript:

```javascript
// بعد الدفع
const jwtToken = '{{ $jwtToken }}';
window.location.href = `https://nomupos.com/sso/auth?token=${jwtToken}`;
```

---

## 🔧 إذا عايز تعدل الـ JWT

لو محتاج تضيف `plan_id` في الـ JWT بدل `app_id`:

```php
// في الكود اللي بيولد JWT
$payload = [
    'iss' => 'marketplace',
    'sub' => $user->id,
    'email' => $user->email,
    'name' => $user->name,
    'app_id' => 12,
    'plan_id' => 2,  // أضف هذا السطر (1=Plan A, 2=Plan B, 3=Plan C)
    'subscription_ends' => $subscriptionEnd,
    'iat' => time(),
    'exp' => time() + (365 * 24 * 60 * 60),
    'jti' => uniqid(),
];
```

---

## 📊 Mapping الحالي

الـ Sub App بيعمل mapping تلقائي:

| app_id | Plan في Sub App | المدة |
|--------|-----------------|-------|
| 12     | Plan B          | 30 يوم |
| 13     | Plan C          | 180 يوم |

لو عايز تغير الـ mapping، قولي وهعدله في Sub App.

---

## 🎯 الخلاصة

### ما تحتاجه في Master App:

1. ✅ الـ JWT token الموجود بالفعل
2. ✅ Redirect للـ URL: `https://nomupos.com/sso/auth?token=JWT_TOKEN`
3. ✅ خلاص! 🎉

### لا تحتاج:
- ❌ مكتبات جديدة
- ❌ Services معقدة
- ❌ Database changes
- ❌ Encryption/Decryption

---

## 💡 مثال كامل

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppLaunchController extends Controller
{
    public function launch(Request $request)
    {
        // 1. تحقق من الدفع
        $subscription = $request->user()->subscriptions()
            ->where('app_id', 12)
            ->where('status', 'active')
            ->first();
        
        if (!$subscription) {
            return redirect()->back()->with('error', 'No active subscription');
        }
        
        // 2. احصل على JWT token (من session أو database)
        $jwtToken = $subscription->jwt_token;
        
        // 3. Redirect للـ Sub App
        $subAppUrl = 'https://nomupos.com';
        
        return redirect($subAppUrl . '/sso/auth?token=' . $jwtToken);
    }
}
```

---

## 🔐 Secret Key

```
6856fe49d4f8bc0830199edaefe41fa7922d0a323b07ea216dfbc96ce8257cca
```

- الـ Sub App بيستخدمه للتحقق من JWT
- مش محتاج تستخدمه في Master App (إلا لو عايز تولد JWT جديد)

---

## ✅ Checklist

- [ ] عندك JWT token بيتولد بالفعل
- [ ] الـ JWT فيه: `sub`, `email`, `name`, `app_id`
- [ ] عملت redirect للـ URL: `https://nomupos.com/sso/auth?token=JWT`
- [ ] جربت على localhost أو staging
- [ ] شغال! 🎉

---

**ملاحظة:** لو محتاج أي تعديلات أو عندك استفسارات، قولي قبل ما ترفع!

**تاريخ:** 2026-02-28  
**الحالة:** ✅ جاهز للاستخدام
