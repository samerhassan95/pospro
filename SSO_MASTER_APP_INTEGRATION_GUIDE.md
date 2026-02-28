# SSO Master App Integration Guide

## Quick Start for Master App Developers

This guide shows you how to send users from your Master App to Sub Apps with automatic business and subscription creation.

---

## 🔑 Configuration

### 1. Secret Key (Same for all apps)
```
6856fe49d4f8bc0830199edaefe41fa7922d0a323b07ea216dfbc96ce8257cca
```

⚠️ **Important:** Use the exact same key in Master App and all Sub Apps.

---

## 📤 Sending Users to Sub App

### Basic Example (Create User + Business + Subscription)

```php
<?php

// 1. Prepare user data
$tokenData = [
    'user_id' => $user->id,                    // Required: Your user ID
    'name' => $user->name,                     // Required: User name
    'email' => $user->email,                   // Required: User email
    'plan_id' => $selectedPlan->id,            // Required: Plan ID (1, 2, or 3)
    'business_name' => $request->business_name, // Optional: Business name
    'phone' => $request->phone,                // Optional: Phone number
    'locale' => 'ar',                          // Optional: Language (default: ar)
    'timestamp' => time()                      // Required: Current timestamp
];

// 2. Encrypt token
$secret = '6856fe49d4f8bc0830199edaefe41fa7922d0a323b07ea216dfbc96ce8257cca';
$json = json_encode($tokenData);
$iv = substr(hash('sha256', $secret), 0, 16);
$encrypted = openssl_encrypt($json, 'AES-256-CBC', $secret, 0, $iv);
$signature = hash_hmac('sha256', $encrypted, $secret);
$token = base64_encode($encrypted . '::' . $signature);

// 3. Redirect to Sub App
$subAppUrl = 'https://sub-app.com/sso/login?token=' . urlencode($token);
return redirect($subAppUrl);
```

---

## 📋 Token Data Fields

### Required Fields
| Field | Type | Description | Example |
|-------|------|-------------|---------|
| `user_id` | string/int | Unique user ID from Master App | `"123"` or `123` |
| `name` | string | User's full name | `"محمد أحمد"` |
| `email` | string | User's email address | `"user@example.com"` |
| `plan_id` | int | Plan ID (1=A, 2=B, 3=C) | `1` |
| `timestamp` | int | Current Unix timestamp | `time()` |

### Optional Fields (Business Data)
| Field | Type | Description | Example |
|-------|------|-------------|---------|
| `business_name` | string | Company/Business name | `"متجر محمد"` |
| `phone` | string | Phone number | `"0501234567"` |
| `address` | string | Full address | `"الرياض، السعودية"` |
| `locale` | string | Language code | `"ar"` or `"en"` |

### Optional Fields (B2B/ZATCA)
| Field | Type | Description | Example |
|-------|------|-------------|---------|
| `vat_no` | string | VAT/Tax number (15 digits) | `"300123456789003"` |
| `commercial_registration` | string | CR number | `"1234567890"` |
| `building_number` | string | Building number | `"1234"` |
| `street_name` | string | Street name | `"شارع الملك فهد"` |
| `district` | string | District/Neighborhood | `"العليا"` |
| `city` | string | City | `"الرياض"` |
| `postal_code` | string | Postal/ZIP code | `"12345"` |
| `country_code` | string | ISO country code | `"SA"` |

---

## 🎯 User Types

### 1. Regular User with Plan (Creates Everything)
```php
$tokenData = [
    'user_id' => '123',
    'name' => 'محمد أحمد',
    'email' => 'mohammed@example.com',
    'plan_id' => 2,  // ⭐ This triggers business creation
    'business_name' => 'متجر محمد',
    'timestamp' => time()
];
```

**Result:**
- ✅ Creates User (shop-owner)
- ✅ Creates Business
- ✅ Creates PlanSubscribe
- ✅ Links everything together

### 2. User for Existing Business
```php
$tokenData = [
    'user_id' => '124',
    'name' => 'أحمد علي',
    'email' => 'ahmed@example.com',
    'business_id' => 5,  // ⭐ Links to existing business
    'timestamp' => time()
];
```

**Result:**
- ✅ Creates User (shop-owner)
- ✅ Links to existing Business

### 3. Admin User
```php
$tokenData = [
    'user_id' => '125',
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'role' => 'admin',  // ⭐ No business
    'timestamp' => time()
];
```

**Result:**
- ✅ Creates User (admin)
- ❌ No Business or Subscription

---

## 📊 Available Plans

| Plan ID | Name | Duration | Price | Features |
|---------|------|----------|-------|----------|
| 1 | A | 7 days | Free | Basic features, 1 warehouse, 1 branch |
| 2 | B | 30 days | 10 SAR | Standard features |
| 3 | C | 180 days | 60 SAR | All features |

---

## 🔄 Complete Flow

```
Master App
    ↓
1. User selects plan
    ↓
2. Master App creates token with plan_id
    ↓
3. Master App redirects to Sub App with token
    ↓
Sub App
    ↓
4. Sub App decrypts token
    ↓
5. Sub App verifies plan exists
    ↓
6. Sub App creates Business
    ↓
7. Sub App creates PlanSubscribe
    ↓
8. Sub App creates User (shop-owner)
    ↓
9. Sub App logs in user
    ↓
10. User redirected to /business/dashboard
```

---

## 💻 Complete Code Example

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SSOController extends Controller
{
    private $ssoSecret = '6856fe49d4f8bc0830199edaefe41fa7922d0a323b07ea216dfbc96ce8257cca';
    
    public function sendToSubApp(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'user_id' => 'required',
            'name' => 'required|string',
            'email' => 'required|email',
            'plan_id' => 'required|integer|in:1,2,3',
            'business_name' => 'nullable|string',
            'phone' => 'nullable|string',
            'vat_no' => 'nullable|string|size:15',
            'commercial_registration' => 'nullable|string',
        ]);
        
        // Prepare token data
        $tokenData = [
            'user_id' => $validated['user_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'plan_id' => $validated['plan_id'],
            'timestamp' => time(),
            'master_app_url' => config('app.url'),
            'locale' => app()->getLocale(),
        ];
        
        // Add optional fields
        if (!empty($validated['business_name'])) {
            $tokenData['business_name'] = $validated['business_name'];
        }
        if (!empty($validated['phone'])) {
            $tokenData['phone'] = $validated['phone'];
        }
        if (!empty($validated['vat_no'])) {
            $tokenData['vat_no'] = $validated['vat_no'];
        }
        if (!empty($validated['commercial_registration'])) {
            $tokenData['commercial_registration'] = $validated['commercial_registration'];
        }
        
        // Encrypt token
        $token = $this->encryptToken($tokenData);
        
        // Get Sub App URL
        $subAppUrl = config('services.sub_app.url');
        
        // Redirect
        return redirect($subAppUrl . '/sso/login?token=' . urlencode($token));
    }
    
    private function encryptToken(array $data): string
    {
        $json = json_encode($data);
        $iv = substr(hash('sha256', $this->ssoSecret), 0, 16);
        $encrypted = openssl_encrypt($json, 'AES-256-CBC', $this->ssoSecret, 0, $iv);
        $signature = hash_hmac('sha256', $encrypted, $this->ssoSecret);
        return base64_encode($encrypted . '::' . $signature);
    }
}
```

---

## 🧪 Testing

### Test Token Generation

```php
php artisan tinker

$secret = '6856fe49d4f8bc0830199edaefe41fa7922d0a323b07ea216dfbc96ce8257cca';

$data = [
    'user_id' => 999,
    'email' => 'test@example.com',
    'name' => 'Test User',
    'plan_id' => 1,
    'business_name' => 'Test Business',
    'locale' => 'ar',
    'timestamp' => time(),
];

$json = json_encode($data);
$iv = substr(hash('sha256', $secret), 0, 16);
$encrypted = openssl_encrypt($json, 'AES-256-CBC', $secret, 0, $iv);
$signature = hash_hmac('sha256', $encrypted, $secret);
$token = base64_encode($encrypted . '::' . $signature);

echo "https://sub-app.com/sso/login?token=" . urlencode($token) . "\n";
```

---

## 🔍 Verification

### Check if user was created successfully:

```sql
-- In Sub App database
SELECT 
    u.id as user_id,
    u.name,
    u.email,
    u.role,
    u.external_id,
    b.id as business_id,
    b.companyName,
    ps.id as subscription_id,
    p.subscriptionName as plan_name,
    ps.service_end_date
FROM users u
LEFT JOIN businesses b ON u.business_id = b.id
LEFT JOIN plan_subscribes ps ON b.plan_subscribe_id = ps.id
LEFT JOIN plans p ON ps.plan_id = p.id
WHERE u.external_id = '999';  -- Your user_id from Master App
```

---

## ⚠️ Important Notes

1. **Same Secret Key:** Must be identical in Master App and all Sub Apps
2. **Plan Must Exist:** Ensure plan_id exists in Sub App's plans table
3. **Unique Email:** Email must be unique in Sub App
4. **HTTPS Required:** Use HTTPS in production
5. **Token Expiry:** Currently disabled (0), tokens don't expire
6. **Auto Registration:** Enabled by default

---

## 🐛 Troubleshooting

### "Invalid or expired SSO token"
- Check if secret key matches
- Verify token encryption code
- Check timestamp is current

### "Invalid plan_id"
- Verify plan exists in Sub App
- Use plan_id: 1, 2, or 3

### "Email already exists"
- User with this email already exists
- SSO will link existing user to SSO

### "Business creation failed"
- Check Sub App logs: `storage/logs/laravel.log`
- Verify database permissions

---

## 📞 Support

For issues or questions:
1. Check Sub App logs: `storage/logs/laravel.log`
2. Review this documentation
3. Contact Sub App administrator

---

**Last Updated:** 2026-02-28  
**Version:** 1.1.0  
**Status:** ✅ Production Ready
