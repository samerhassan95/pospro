# 👥 SSO User Types & Roles

## 📋 نظرة عامة

هذا الدليل يشرح أنواع المستخدمين التي يتم إنشاؤها عبر SSO والصلاحيات المخصصة لهم.

---

## 👤 أنواع المستخدمين في النظام

### 1. **Super Admin** (المدير الأعلى)
```
Role: superadmin
Business ID: NULL
Permissions: كل الصلاحيات
```
- يدير النظام بالكامل
- يدير جميع الـ businesses
- يدير المستخدمين والصلاحيات

### 2. **Admin** (مدير النظام)
```
Role: admin
Business ID: NULL
Permissions: صلاحيات إدارية
```
- يدير النظام
- يدير الـ businesses
- صلاحيات أقل من Super Admin

### 3. **Manager** (مدير)
```
Role: manager
Business ID: NULL
Permissions: صلاحيات محدودة
```
- صلاحيات إدارية محدودة
- لا يملك business_id

### 4. **Shop Owner** (صاحب متجر/مطعم)
```
Role: shop-owner
Business ID: [رقم المتجر]
Branch ID: [رقم الفرع] (اختياري)
Permissions: صلاحيات المتجر
```
- يدير متجره/مطعمه الخاص
- له business_id محدد
- قد يكون له branch_id

---

## 🔄 كيف يعمل SSO؟

### السيناريو 1: إنشاء Business User (Shop Owner)

عندما يرسل Master App token مع `business_id`:

```php
$data = [
    'user_id' => 123,
    'email' => 'owner@example.com',
    'name' => 'Restaurant Owner',
    'business_id' => 5,  // ✅ موجود
    'locale' => 'ar',
];
```

**النتيجة:**
```php
User::create([
    'name' => 'Restaurant Owner',
    'email' => 'owner@example.com',
    'role' => 'shop-owner',  // ✅ تلقائياً
    'business_id' => 5,
    'branch_id' => [أول فرع للمتجر],
    'external_id' => 123,
    'sso_provider' => 'nomuapps',
]);
```

**الصلاحيات:**
- ✅ يدخل على `/business/dashboard`
- ✅ يدير متجره فقط (business_id = 5)
- ✅ له صلاحيات shop-owner من Spatie

---

### السيناريو 2: إنشاء Admin User

عندما يرسل Master App token **بدون** `business_id`:

```php
$data = [
    'user_id' => 456,
    'email' => 'admin@example.com',
    'name' => 'System Admin',
    'business_id' => null,  // ❌ فاضي
    'role' => 'admin',  // ✅ محدد
    'locale' => 'ar',
];
```

**النتيجة:**
```php
User::create([
    'name' => 'System Admin',
    'email' => 'admin@example.com',
    'role' => 'admin',  // ✅ من الـ token
    'business_id' => null,
    'branch_id' => null,
    'external_id' => 456,
    'sso_provider' => 'nomuapps',
]);
```

**الصلاحيات:**
- ✅ يدخل على `/admin/dashboard`
- ✅ يدير النظام بالكامل
- ✅ له صلاحيات admin من Spatie

---

### السيناريو 3: إنشاء Admin بدون role محدد

عندما يرسل Master App token بدون `business_id` وبدون `role`:

```php
$data = [
    'user_id' => 789,
    'email' => 'user@example.com',
    'name' => 'New User',
    'business_id' => null,
    'role' => null,  // ❌ مش محدد
    'locale' => 'ar',
];
```

**النتيجة:**
```php
User::create([
    'name' => 'New User',
    'email' => 'user@example.com',
    'role' => 'admin',  // ✅ Default
    'business_id' => null,
    'branch_id' => null,
    'external_id' => 789,
    'sso_provider' => 'nomuapps',
]);
```

**الصلاحيات:**
- ✅ يدخل على `/admin/dashboard`
- ✅ صلاحيات admin افتراضية

---

## 📊 إحصائيات المستخدمين الحالية

### حسب الـ Role:
```
superadmin: 1 user
admin: 1 user
manager: 1 user
shop-owner: 19 users
```

### حسب النوع:
```
Admin Users (business_id = NULL): 8 users
Business Users (business_id != NULL): 14 users
```

---

## 🔐 Spatie Roles

النظام يستخدم Spatie Permission Package:

```
ID: 1 - Name: superadmin - Guard: web
ID: 2 - Name: admin - Guard: web
ID: 3 - Name: manager - Guard: web
```

**ملاحظة:** `shop-owner` مش موجود في Spatie roles، لكن موجود في `users.role` column.

---

## 🎯 التوجيه بعد تسجيل الدخول

في `SSOController`:

```php
// Determine redirect route based on user type
$redirectRoute = 'admin.dashboard';
if ($user->business_id) {
    $redirectRoute = 'business.dashboard';
}

return redirect()->intended(route($redirectRoute));
```

**القاعدة:**
- إذا `business_id` موجود → `/business/dashboard`
- إذا `business_id` فاضي → `/admin/dashboard`

---

## 🔧 تخصيص الـ Role

### في Master App (عند إنشاء Token):

```php
// لإنشاء Business User
$data = [
    'user_id' => $user->id,
    'email' => $user->email,
    'name' => $user->name,
    'business_id' => $business->id,  // ✅ مهم
    'locale' => 'ar',
];

// لإنشاء Admin User
$data = [
    'user_id' => $user->id,
    'email' => $user->email,
    'name' => $user->name,
    'business_id' => null,  // ❌ فاضي
    'role' => 'admin',  // أو 'manager' أو 'superadmin'
    'locale' => 'ar',
];
```

---

## ⚠️ ملاحظات مهمة

### 1. Business ID مطلوب لـ Shop Owners
```php
if ($data['business_id']) {
    // ✅ Shop Owner
    $role = 'shop-owner';
} else {
    // ✅ Admin/Manager/SuperAdmin
    $role = $data['role'] ?? 'admin';
}
```

### 2. Branch ID تلقائي
```php
// إذا كان business_id موجود، يتم البحث عن أول فرع
$business = Business::find($data['business_id']);
if ($business) {
    $branch = $business->branches()->first();
    if ($branch) {
        $userData['branch_id'] = $branch->id;
    }
}
```

### 3. Spatie Role Assignment
```php
// يتم تعيين Spatie role تلقائياً إذا كان موجوداً
if (Role::where('name', $role)->exists()) {
    $user->assignRole($role);
}
```

---

## 🧪 اختبار أنواع المستخدمين

### اختبار Shop Owner:

```bash
php artisan tinker
```

```php
$secret = '6856fe49d4f8bc0830199edaefe41fa7922d0a323b07ea216dfbc96ce8257cca';

$data = [
    'user_id' => 999,
    'email' => 'shop@test.com',
    'name' => 'Test Shop Owner',
    'business_id' => 1,  // ✅ موجود
    'locale' => 'ar',
    'timestamp' => time(),
];

$json = json_encode($data);
$iv = substr(hash('sha256', $secret), 0, 16);
$encrypted = openssl_encrypt($json, 'AES-256-CBC', $secret, 0, $iv);
$signature = hash_hmac('sha256', $encrypted, $secret);
$token = base64_encode($encrypted . '::' . $signature);

echo "http://127.0.0.1:8000/sso/login?token=" . urlencode($token) . "\n";
```

**المتوقع:**
- ينشئ user بـ `role = 'shop-owner'`
- `business_id = 1`
- يوجه لـ `/business/dashboard`

---

### اختبار Admin:

```php
$data = [
    'user_id' => 888,
    'email' => 'admin@test.com',
    'name' => 'Test Admin',
    'business_id' => null,  // ❌ فاضي
    'role' => 'admin',
    'locale' => 'ar',
    'timestamp' => time(),
];

// ... نفس الكود
```

**المتوقع:**
- ينشئ user بـ `role = 'admin'`
- `business_id = null`
- يوجه لـ `/admin/dashboard`

---

## 📝 الخلاصة

| النوع | business_id | role | Dashboard | الصلاحيات |
|------|-------------|------|-----------|-----------|
| **Shop Owner** | ✅ موجود | shop-owner | /business | متجره فقط |
| **Admin** | ❌ NULL | admin | /admin | النظام كامل |
| **Manager** | ❌ NULL | manager | /admin | محدودة |
| **Super Admin** | ❌ NULL | superadmin | /admin | كل شيء |

---

## 🔄 التحديثات

**تاريخ:** 2026-02-27  
**الإصدار:** 1.1.0  
**التغييرات:**
- ✅ إضافة تحديد تلقائي للـ role
- ✅ إضافة دعم Spatie roles
- ✅ إضافة تحديد تلقائي للـ branch_id
- ✅ تحسين التوجيه بعد تسجيل الدخول

