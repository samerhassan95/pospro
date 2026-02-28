# دليل إنشاء الأعمال والاشتراكات عبر SSO

## نظرة عامة
تم تحديث نظام SSO ليقوم بإنشاء حساب مستخدم كامل مع عمل تجاري واشتراك في باقة تلقائياً.

## كيف يعمل النظام

### 1. عند إرسال Token مع plan_id
عندما يتم إرسال token يحتوي على `plan_id`، سيقوم النظام بـ:

1. **إنشاء Business (عمل تجاري)**
   - يتم إنشاء سجل جديد في جدول `businesses`
   - يتم ملء البيانات الأساسية من الـ token

2. **إنشاء PlanSubscribe (اشتراك)**
   - يتم إنشاء اشتراك مرتبط بالباقة المحددة
   - يتم حساب تاريخ البداية والنهاية بناءً على مدة الباقة
   - حالة الدفع تكون "paid" تلقائياً

3. **إنشاء User (مستخدم)**
   - يتم إنشاء المستخدم كـ "shop-owner" (صاحب متجر)
   - يتم ربطه بالعمل التجاري المُنشأ
   - يتم منحه صلاحيات الباقة المختارة

## بيانات Token المطلوبة

### البيانات الأساسية (مطلوبة)
```json
{
  "user_id": "123",
  "name": "اسم المستخدم",
  "email": "user@example.com",
  "plan_id": 1,
  "timestamp": 1234567890
}
```

### البيانات الاختيارية
```json
{
  "locale": "ar",
  "business_name": "اسم الشركة",
  "phone": "0501234567",
  "address": "العنوان",
  "vat_no": "123456789012345",
  "commercial_registration": "1234567890",
  "building_number": "1234",
  "street_name": "شارع الملك",
  "district": "الحي",
  "city": "الرياض",
  "postal_code": "12345",
  "country_code": "SA",
  "gateway_id": 1,
  "subscription_notes": "ملاحظات الاشتراك"
}
```

## أنواع المستخدمين

### 1. مستخدم عادي مع باقة (plan_id موجود)
```json
{
  "user_id": "123",
  "name": "محمد أحمد",
  "email": "mohammed@example.com",
  "plan_id": 1
}
```
**النتيجة:**
- يتم إنشاء Business جديد
- يتم إنشاء PlanSubscribe مرتبط بالباقة
- يتم إنشاء User بدور "shop-owner"
- المستخدم يحصل على صلاحيات الباقة المختارة

### 2. مستخدم لعمل موجود (business_id موجود)
```json
{
  "user_id": "124",
  "name": "أحمد علي",
  "email": "ahmed@example.com",
  "business_id": 5
}
```
**النتيجة:**
- يتم إنشاء User فقط
- يتم ربطه بالعمل الموجود
- يحصل على دور "shop-owner"

### 3. مستخدم إداري (بدون plan_id أو business_id)
```json
{
  "user_id": "125",
  "name": "Admin User",
  "email": "admin@example.com",
  "role": "admin"
}
```
**النتيجة:**
- يتم إنشاء User بدور "admin"
- لا يتم إنشاء Business أو Subscription

## تفاصيل الاشتراك

### البيانات التي يتم إنشاؤها تلقائياً:
- `service_start_date`: تاريخ اليوم
- `service_end_date`: تاريخ اليوم + مدة الباقة
- `payment_status`: "paid" (مدفوع)
- `invoice_type`: "B2C" (افتراضي)
- `uuid`: معرف فريد
- `invoice_number`: رقم فاتورة تلقائي

### الصلاحيات من الباقة:
- `allow_multibranch`: السماح بفروع متعددة
- `addon_domain_limit`: حد النطاقات الإضافية
- `subdomain_limit`: حد النطاقات الفرعية
- جميع صلاحيات الباقة الأخرى (Sales, Products, Reports, إلخ)

## مثال كامل

### Token من التطبيق الرئيسي:
```json
{
  "user_id": "USER_123",
  "name": "محمد أحمد التجاري",
  "email": "mohammed@business.com",
  "plan_id": 2,
  "business_name": "متجر محمد التجاري",
  "phone": "0501234567",
  "vat_no": "300123456789003",
  "commercial_registration": "1234567890",
  "building_number": "1234",
  "street_name": "شارع الملك فهد",
  "district": "العليا",
  "city": "الرياض",
  "postal_code": "12345",
  "country_code": "SA",
  "locale": "ar",
  "timestamp": 1709164800
}
```

### ما يحدث في النظام:

1. **إنشاء Business:**
```php
Business {
  id: 10,
  companyName: "متجر محمد التجاري",
  email: "mohammed@business.com",
  phoneNumber: "0501234567",
  vat_no: "300123456789003",
  commercial_registration: "1234567890",
  building_number: "1234",
  street_name: "شارع الملك فهد",
  district: "العليا",
  city: "الرياض",
  postal_code: "12345",
  country_code: "SA",
  status: 1,
  subscriptionDate: "2024-02-28",
  will_expire: "2024-03-29" // +30 days
}
```

2. **إنشاء PlanSubscribe:**
```php
PlanSubscribe {
  id: 15,
  business_id: 10,
  plan_id: 2,
  price: 299.00,
  duration: 30,
  payment_status: "paid",
  service_start_date: "2024-02-28",
  service_end_date: "2024-03-29",
  invoice_type: "B2C",
  uuid: "550e8400-e29b-41d4-a716-446655440000",
  invoice_number: "SUB-ABC12345"
}
```

3. **إنشاء User:**
```php
User {
  id: 25,
  name: "محمد أحمد التجاري",
  email: "mohammed@business.com",
  business_id: 10,
  role: "shop-owner",
  external_id: "USER_123",
  sso_provider: "nomuapps",
  locale: "ar",
  email_verified_at: "2024-02-28"
}
```

4. **تحديث Business:**
```php
Business {
  id: 10,
  plan_subscribe_id: 15, // ربط الاشتراك
  // ... باقي البيانات
}
```

## التحقق من النجاح

### في Logs:
```
[INFO] SSO: Business created - business_id: 10
[INFO] SSO: Subscription created - subscription_id: 15, plan_id: 2, duration: 30
[INFO] SSO: Created business and subscription - business_id: 10, subscription_id: 15, plan_id: 2
[INFO] SSO: New user created - user_id: 25
[INFO] SSO: Assigned Spatie role - role: shop-owner
```

### في Database:
```sql
-- التحقق من Business
SELECT * FROM businesses WHERE id = 10;

-- التحقق من Subscription
SELECT * FROM plan_subscribes WHERE business_id = 10;

-- التحقق من User
SELECT * FROM users WHERE business_id = 10;

-- التحقق من الربط الكامل
SELECT 
  u.name as user_name,
  b.companyName as business_name,
  p.subscriptionName as plan_name,
  ps.service_end_date as expires_at
FROM users u
JOIN businesses b ON u.business_id = b.id
JOIN plan_subscribes ps ON b.plan_subscribe_id = ps.id
JOIN plans p ON ps.plan_id = p.id
WHERE u.external_id = 'USER_123';
```

## معالجة الأخطاء

### إذا كان plan_id غير صحيح:
```
[ERROR] SSO: Invalid plan_id provided - plan_id: 999
Exception: Invalid plan_id
```

### إذا فشل إنشاء Business:
```
[ERROR] SSO: User creation error - Business creation failed
```

## الملفات المعدلة

1. `app/Services/SSOService.php`
   - تم إضافة `createBusinessForUser()`
   - تم إضافة `createSubscription()`
   - تم تحديث `createNewUser()`

## الاختبار

### اختبار إنشاء مستخدم مع باقة:
```bash
php artisan tinker

$token = base64_encode('encrypted_data::signature');
$response = Http::post('http://your-app.test/sso/login', [
    'token' => $token
]);
```

### أو استخدم ملف الاختبار:
```bash
php check_user_types.php
```

## ملاحظات مهمة

1. **الباقة يجب أن تكون موجودة**: تأكد من وجود plan_id في جدول plans
2. **الدفع مسبق**: جميع الاشتراكات عبر SSO تعتبر مدفوعة
3. **المدة من الباقة**: يتم حساب تاريخ الانتهاء بناءً على duration في الباقة
4. **البيانات الاختيارية**: يمكن إضافة بيانات B2B إذا كانت متوفرة
5. **اللغة الافتراضية**: ar (العربية)

## الخطوات التالية

1. اختبار إنشاء مستخدم مع باقة A
2. اختبار إنشاء مستخدم مع باقة B
3. اختبار إنشاء مستخدم مع باقة C
4. التحقق من الصلاحيات لكل باقة
5. اختبار تاريخ انتهاء الاشتراك
