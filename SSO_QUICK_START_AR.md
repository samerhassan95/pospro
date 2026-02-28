# 🚀 دليل سريع لاختبار SSO على Postman

## الخطوات السريعة

### 1️⃣ تأكد من تفعيل SSO
```bash
# في ملف .env
SSO_ENABLED=true

# امسح الـ cache
php artisan config:clear
```

### 2️⃣ ولّد Token
```bash
php generate_sso_token.php
```

**اختر من القائمة:**
- `1` → مستخدم مع باقة A (مجاني، 7 أيام)
- `2` → مستخدم مع باقة B (10 ريال، 30 يوم)
- `3` → مستخدم مع باقة C (60 ريال، 180 يوم)

**النتيجة:**
```
Token Generated Successfully!
Token: eyJpdiI6IjEyMzQ1Njc4OTBhYmNkZWYiLCJ2YWx1ZSI6...
```

### 3️⃣ افتح Postman

#### طريقة 1: استيراد Collection
1. افتح Postman
2. اضغط Import
3. اختر ملف `SSO_Postman_Collection.json`
4. Collection جاهز! ✅

#### طريقة 2: Request يدوي
**GET Request:**
```
Method: GET
URL: http://127.0.0.1:8000/sso/login?token=YOUR_TOKEN_HERE
```

**POST Request:**
```
Method: POST
URL: http://127.0.0.1:8000/sso/login
Headers:
  - Content-Type: application/json
  - Accept: application/json
Body (JSON):
{
    "token": "YOUR_TOKEN_HERE"
}
```

### 4️⃣ اضغط Send

**النتيجة المتوقعة (نجاح):**
```json
{
    "message": "SSO login successful",
    "redirect": "http://127.0.0.1:8000/business/dashboard",
    "user": {
        "id": 24,
        "name": "مستخدم باقة A",
        "email": "plan_a_123@test.com",
        "role": "shop-owner",
        "business_id": 27
    }
}
```

### 5️⃣ تحقق من النتيجة

**في Database:**
```sql
-- شوف المستخدم
SELECT * FROM users ORDER BY id DESC LIMIT 1;

-- شوف العمل التجاري
SELECT * FROM businesses ORDER BY id DESC LIMIT 1;

-- شوف الاشتراك
SELECT * FROM plan_subscribes ORDER BY id DESC LIMIT 1;
```

**أو استخدم:**
```bash
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
\$user = App\Models\User::latest()->first();
echo 'Last User: ' . \$user->name . ' (' . \$user->email . ')' . PHP_EOL;
echo 'Business: ' . (\$user->business->companyName ?? 'None') . PHP_EOL;
echo 'Plan: ' . (\$user->business->enrolled_plan->plan->subscriptionName ?? 'None') . PHP_EOL;
"
```

---

## 🎯 أمثلة سريعة

### مثال 1: إنشاء مستخدم مع باقة B
```bash
php generate_sso_token.php
# اختر: 2
# انسخ الـ Token
# الصقه في Postman
# اضغط Send
```

### مثال 2: إنشاء مستخدم لعمل موجود
```bash
php generate_sso_token.php
# اختر: 4
# أدخل business_id: 5
# انسخ الـ Token
# الصقه في Postman
# اضغط Send
```

### مثال 3: إنشاء Admin
```bash
php generate_sso_token.php
# اختر: 5
# انسخ الـ Token
# الصقه في Postman
# اضغط Send
```

---

## ⚡ One-Liner (سريع جداً)

**توليد Token مباشرة:**
```bash
php -r "
\$secret = '6856fe49d4f8bc0830199edaefe41fa7922d0a323b07ea216dfbc96ce8257cca';
\$data = json_encode(['user_id' => 'QUICK_'.time(), 'name' => 'Quick Test', 'email' => 'quick'.time().'@test.com', 'plan_id' => 1, 'timestamp' => time()]);
\$iv = substr(hash('sha256', \$secret), 0, 16);
\$encrypted = openssl_encrypt(\$data, 'AES-256-CBC', \$secret, 0, \$iv);
\$signature = hash_hmac('sha256', \$encrypted, \$secret);
echo base64_encode(\$encrypted . '::' . \$signature);
"
```

**اختبار مباشر بـ cURL:**
```bash
TOKEN=$(php -r "...")
curl -X GET "http://127.0.0.1:8000/sso/login?token=$TOKEN" -L
```

---

## 🔍 استكشاف الأخطاء

### خطأ: "SSO is not enabled"
```bash
# تأكد من .env
grep SSO_ENABLED .env

# يجب أن يكون:
SSO_ENABLED=true

# امسح cache
php artisan config:clear
```

### خطأ: "Invalid or expired SSO token"
**الأسباب:**
- المفتاح السري خطأ
- Token منتهي الصلاحية
- Token format غلط

**الحل:**
```bash
# تأكد من المفتاح في .env
grep SSO_SECRET_KEY .env

# يجب أن يكون:
SSO_SECRET_KEY=6856fe49d4f8bc0830199edaefe41fa7922d0a323b07ea216dfbc96ce8257cca

# ولّد token جديد
php generate_sso_token.php
```

### خطأ: "Invalid plan_id"
```bash
# شوف الباقات المتاحة
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
\$plans = App\Models\Plan::where('status', 1)->get(['id', 'subscriptionName']);
foreach (\$plans as \$p) echo \"ID: {\$p->id} - {\$p->subscriptionName}\" . PHP_EOL;
"
```

---

## 📝 ملاحظات مهمة

1. ✅ Token بدون انتهاء صلاحية (آمن بسبب التشفير القوي)
2. ✅ كل token يُستخدم مرة واحدة فقط
3. ✅ المستخدم يتم إنشاؤه تلقائياً
4. ✅ Business و Subscription يتم إنشاؤهم تلقائياً
5. ✅ Session يتم إنشاؤه تلقائياً

---

## 🎉 جاهز للرفع!

بعد الاختبار على Postman:
1. ✅ تأكد من SSO شغال
2. ✅ تأكد من إنشاء Users
3. ✅ تأكد من إنشاء Business
4. ✅ تأكد من إنشاء Subscriptions
5. 🚀 ارفع على السيرفر!

---

**آخر تحديث:** 2026-02-28  
**الحالة:** ✅ جاهز للاختبار
