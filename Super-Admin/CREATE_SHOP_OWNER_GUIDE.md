# دليل إنشاء Shop Owner جديد

## الطريقة الأولى: استخدام Artisan Command (الأسهل)

### إنشاء shop owner بإيميل وباسورد مخصص:

```bash
php artisan shop:create your-email@example.com your-password
```

### مثال:
```bash
php artisan shop:create admin@admin.com admin123
```

### مع تحديد الاسم:
```bash
php artisan shop:create admin@admin.com admin123 --name="Ahmed Mohamed"
```

---

## الطريقة الثانية: استخدام SQL Script

### 1. افتح ملف `create_shop_owner.sql`

### 2. عدل البيانات التالية:

```sql
-- اسم المحل
SET @business_name = 'My Shop';

-- إيميل المحل
SET @business_email = 'shop@example.com';

-- تليفون المحل
SET @business_phone = '1234567890';

-- إيميل المستخدم
SET @user_email = 'shopowner@example.com';

-- اسم المستخدم
SET @user_name = 'Shop Owner';

-- الباسورد (الافتراضي: 123456)
-- لو عايز تغير الباسورد، استخدم bcrypt hash
SET @user_password = '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
```

### 3. نفذ الـ SQL:

```bash
# MySQL
mysql -u username -p database_name < create_shop_owner.sql

# أو من phpMyAdmin
# انسخ والصق الكود في SQL tab
```

---

## الطريقة الثالثة: من الكود مباشرة

### أضف في `database/seeders/DatabaseSeeder.php`:

```php
public function run()
{
    // ... existing seeders

    // Create custom shop owner
    $this->call(CustomShopOwnerSeeder::class);
}
```

### أنشئ ملف `database/seeders/CustomShopOwnerSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Business;
use App\Models\Plan;
use App\Models\PlanSubscribe;
use App\Models\BusinessCategory;
use Illuminate\Database\Seeder;

class CustomShopOwnerSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create category
        $category = BusinessCategory::firstOrCreate(
            ['id' => 1],
            ['name' => 'General', 'status' => 1]
        );

        // Get or create plan
        $plan = Plan::firstOrCreate(
            ['id' => 1],
            [
                'subscriptionName' => 'Premium',
                'duration' => 365,
                'subscriptionPrice' => 0,
                'status' => 1,
                'allow_multibranch' => 1,
                'addon_domain_limit' => 999,
                'subdomain_limit' => 999,
                'features' => json_encode([]),
            ]
        );

        // Create business
        $business = Business::create([
            'business_category_id' => $category->id,
            'companyName' => 'My Custom Shop',
            'will_expire' => '2035-12-31',
            'address' => 'Shop Address',
            'email' => 'myshop@example.com',
            'phoneNumber' => '1234567890',
            'subscriptionDate' => now(),
            'remainingShopBalance' => 0,
            'shopOpeningBalance' => 0,
            'status' => 1,
        ]);

        // Create plan subscription
        $planSubscribe = PlanSubscribe::create([
            'plan_id' => $plan->id,
            'business_id' => $business->id,
            'price' => 0,
            'payment_status' => 'paid',
            'duration' => 365,
            'allow_multibranch' => 1,
            'addon_domain_limit' => 999,
            'subdomain_limit' => 999,
        ]);

        $business->update(['plan_subscribe_id' => $planSubscribe->id]);

        // Create shop owner
        User::create([
            'business_id' => $business->id,
            'name' => 'My Shop Owner',
            'email' => 'myowner@example.com',  // غير الإيميل هنا
            'role' => 'shop-owner',
            'phone' => '1234567890',
            'lang' => 'en',
            'password' => bcrypt('mypassword'),  // غير الباسورد هنا
            'status' => 1,
            'email_verified_at' => now(),
        ]);
    }
}
```

### ثم نفذ:
```bash
php artisan db:seed --class=CustomShopOwnerSeeder
```

---

## توليد Bcrypt Hash للباسورد

### من Laravel Tinker:
```bash
php artisan tinker
```

```php
bcrypt('your-password')
// النتيجة: $2y$12$...
```

### من PHP مباشرة:
```php
<?php
echo password_hash('your-password', PASSWORD_BCRYPT);
?>
```

---

## المميزات المفعلة للـ Shop Owner الجديد:

✅ **كل الـ Addons مفعلة:**
- Business Module
- Warehouse Addon
- HRM Addon
- Multi-Branch Addon
- Custom Domain Addon
- Social Login Addon
- Thermal Printer Addon
- Affiliate Addon
- Marketing Addon
- وكل الـ addons الأخرى

✅ **صلاحيات غير محدودة:**
- Multi-branch: مفعل
- Addon Domains: 999
- Subdomains: 999
- Expiry: 2035-12-31

✅ **الاشتراك:**
- Plan: Premium
- Duration: 365 يوم
- Status: Paid
- Price: مجاني

---

## أمثلة سريعة:

### مثال 1: إنشاء shop owner باسم "أحمد"
```bash
php artisan shop:create ahmed@shop.com ahmed123 --name="Ahmed Shop"
```

### مثال 2: إنشاء shop owner باسم "محمد"
```bash
php artisan shop:create mohamed@store.com mohamed456 --name="Mohamed Store"
```

### مثال 3: إنشاء shop owner بإيميل admin@admin.com
```bash
php artisan shop:create admin@admin.com admin --name="Admin Shop"
```

---

## التحقق من النجاح:

بعد الإنشاء، جرب تسجيل الدخول:
1. اذهب إلى `/login`
2. أدخل الإيميل والباسورد
3. يجب أن تشاهد Dashboard الخاص بالـ Shop Owner
4. تحقق من وجود كل الـ Addons في الـ Sidebar

---

## ملاحظات مهمة:

⚠️ **الإيميل يجب أن يكون فريد** - لا يمكن تكرار نفس الإيميل

⚠️ **الباسورد** - احفظه في مكان آمن

✅ **كل الـ Addons مفعلة globally** في `modules_statuses.json`

✅ **الـ Plan يسمح بكل الـ Addons** بدون حدود
