# ⚠️ الأخطاء الشائعة وحلولها - تكامل ZATCA

## 📋 جدول المحتويات
1. [أخطاء الحصول على الشهادة](#أخطاء-الحصول-على-الشهادة)
2. [أخطاء اختبار الفواتير](#أخطاء-اختبار-الفواتير)
3. [أخطاء الإرسال التلقائي](#أخطاء-الإرسال-التلقائي)
4. [أخطاء البيانات](#أخطاء-البيانات)
5. [أخطاء التوقيع الرقمي](#أخطاء-التوقيع-الرقمي)

---

## 🔐 أخطاء الحصول على الشهادة

### ❌ خطأ: "OTP Invalid or Expired"

**السبب**:
- OTP منتهي الصلاحية (أكثر من ساعة)
- OTP مستخدم من قبل
- OTP خاطئ

**الحل**:
```
1. احصل على OTP جديد من بوابة الهيئة
2. استخدمه فوراً (خلال ساعة)
3. تأكد من نسخه بشكل صحيح (بدون مسافات)
```

---

### ❌ خطأ: "Organization Identifier Invalid"

**السبب**:
- الرقم الضريبي غير صحيح
- الرقم الضريبي ليس 15 رقم
- الرقم الضريبي غير مسجل في الهيئة

**الحل**:
```
1. تأكد أن الرقم الضريبي 15 رقم بالضبط
2. تأكد أنه مسجل في بوابة الهيئة
3. تأكد من عدم وجود مسافات أو أحرف
```

**مثال صحيح**:
```
✅ 123456789012345
❌ 12345678901234 (14 رقم)
❌ 1234567890123456 (16 رقم)
❌ 123 456 789 012 345 (مسافات)
```

---

### ❌ خطأ: "OpenSSL PKey generation failed"

**السبب**:
- OpenSSL غير مثبت
- إصدار OpenSSL قديم
- إعدادات PHP خاطئة

**الحل**:
```bash
# تحقق من OpenSSL
php -i | grep OpenSSL

# يجب أن يكون الإصدار 1.1.1 أو أحدث
# إذا لم يكن موجود، ثبته:

# Ubuntu/Debian
sudo apt-get install openssl php-openssl

# CentOS/RHEL
sudo yum install openssl php-openssl

# أعد تشغيل PHP
sudo systemctl restart php-fpm
```

---

## 📄 أخطاء اختبار الفواتير

### ❌ خطأ: "Invoice Hash Mismatch"

**السبب**:
- XML تغير بعد التوقيع
- مشكلة في حساب Hash

**الحل**:
```php
// تأكد من عدم تعديل XML بعد التوقيع
// في ZatcaService.php

// ❌ خطأ
$signedXml = $this->signInvoice($xml);
$signedXml = str_replace('something', 'else', $signedXml); // لا تفعل هذا!

// ✅ صحيح
$signedXml = $this->signInvoice($xml);
// لا تعدل XML بعد التوقيع
```

---

### ❌ خطأ: "VAT Number Invalid"

**السبب**:
- الرقم الضريبي للبائع أو المشتري خاطئ
- الرقم الضريبي ليس 15 رقم
- الرقم الضريبي يحتوي على أحرف

**الحل**:
```sql
-- تحقق من الأرقام الضريبية في قاعدة البيانات
SELECT id, companyName, vat_no, LENGTH(vat_no) as length
FROM businesses
WHERE LENGTH(vat_no) != 15 OR vat_no REGEXP '[^0-9]';

-- أصلح الأرقام الخاطئة
UPDATE businesses 
SET vat_no = '123456789012345' 
WHERE id = 1;
```

---

### ❌ خطأ: "Address Information Missing"

**السبب**:
- معلومات العنوان ناقصة
- رقم المبنى فارغ
- الرمز البريدي فارغ

**الحل**:
```sql
-- تحقق من العناوين الناقصة
SELECT id, companyName, 
       building_number, street_name, district, 
       city, postal_code, country_code
FROM businesses
WHERE building_number IS NULL 
   OR street_name IS NULL 
   OR district IS NULL 
   OR city IS NULL 
   OR postal_code IS NULL 
   OR country_code IS NULL;

-- املأ البيانات الناقصة
UPDATE businesses 
SET building_number = '1234',
    street_name = 'King Fahd Road',
    district = 'Al Olaya',
    city = 'Riyadh',
    postal_code = '12345',
    country_code = 'SA'
WHERE id = 1;
```

---

### ❌ خطأ: "Invoice Date Invalid"

**السبب**:
- تاريخ الفاتورة في المستقبل
- تاريخ الفاتورة قديم جداً (أكثر من سنة)
- صيغة التاريخ خاطئة

**الحل**:
```php
// في AcnooSaleController.php
$sale = Sale::create([
    // ✅ استخدم التاريخ الحالي أو تاريخ محدد صحيح
    'saleDate' => $request->saleDate ?? now(),
    
    // ❌ لا تستخدم تاريخ في المستقبل
    // 'saleDate' => now()->addDays(10), // خطأ!
]);
```

---

### ❌ خطأ: "VAT Amount Calculation Error"

**السبب**:
- حساب الضريبة خاطئ
- نسبة الضريبة خاطئة
- الضريبة لا تتطابق مع الإجمالي

**الحل**:
```php
// تأكد من صحة الحساب
$subtotal = 100; // المجموع قبل الضريبة
$vatPercent = 15; // نسبة الضريبة

// ✅ الطريقة الصحيحة
$vatAmount = ($subtotal * $vatPercent) / 100; // = 15
$totalAmount = $subtotal + $vatAmount; // = 115

// ❌ خطأ شائع
$vatAmount = $subtotal * 0.15; // قد يسبب مشاكل في الدقة
```

---

## 🔄 أخطاء الإرسال التلقائي

### ❌ خطأ: "Queue Not Running"

**السبب**:
- Queue Worker غير مشغل
- الفواتير لا تُرسل تلقائياً

**الحل**:
```bash
# شغل Queue Worker
php artisan queue:work

# أو استخدم Supervisor للتشغيل الدائم
# في /etc/supervisor/conf.d/laravel-worker.conf
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/path/to/storage/logs/worker.log
```

---

### ❌ خطأ: "Connection Timeout"

**السبب**:
- الاتصال بخوادم الهيئة بطيء
- Firewall يمنع الاتصال
- مشكلة في الشبكة

**الحل**:
```php
// في ZatcaService.php
// زد وقت الانتظار

$response = \Illuminate\Support\Facades\Http::timeout(30) // 30 ثانية
    ->withHeaders([...])
    ->post($url, $body);
```

---

### ❌ خطأ: "Certificate Expired"

**السبب**:
- الشهادة الرقمية منتهية الصلاحية
- يجب تجديد الشهادة

**الحل**:
```
1. احصل على OTP جديد
2. احصل على شهادة جديدة
3. الشهادات صالحة لمدة سنة واحدة
```

---

## 📊 أخطاء البيانات

### ❌ خطأ: "Party Type Not Set"

**السبب**:
- نوع العميل (B2B/B2C) غير محدد
- حقل `zatca_type` فارغ

**الحل**:
```sql
-- تحقق من العملاء بدون نوع
SELECT id, name, zatca_type 
FROM parties 
WHERE zatca_type IS NULL;

-- حدد النوع بناءً على وجود الرقم الضريبي
UPDATE parties 
SET zatca_type = CASE 
    WHEN vat_number IS NOT NULL AND vat_number != '' THEN 'b2b'
    ELSE 'b2c'
END
WHERE zatca_type IS NULL;
```

---

### ❌ خطأ: "Product Description Too Long"

**السبب**:
- وصف المنتج أطول من الحد المسموح (127 حرف)

**الحل**:
```php
// في UblGenerator.php
// اقتطع الوصف الطويل

$productName = $detail->product->productName ?? '';
if (strlen($productName) > 127) {
    $productName = substr($productName, 0, 124) . '...';
}
```

---

### ❌ خطأ: "Negative Amount"

**السبب**:
- مبلغ سالب في الفاتورة
- خصم أكبر من الإجمالي

**الحل**:
```php
// في AcnooSaleController.php
// تحقق من المبالغ

if ($discountAmount > $subtotalWithVat) {
    return response()->json([
        'message' => __('Discount cannot be more than subtotal!')
    ], 400);
}

// تأكد أن كل المبالغ موجبة
$totalAmount = max(0, $subtotalWithVat - $discountAmount + $shippingCharge);
```

---

## 🔏 أخطاء التوقيع الرقمي

### ❌ خطأ: "Invalid Signature"

**السبب**:
- المفتاح الخاص خاطئ
- الشهادة لا تتطابق مع المفتاح
- خطأ في عملية التوقيع

**الحل**:
```php
// تحقق من المفتاح الخاص
$privateKey = openssl_get_privatekey($privateKeyContent);
if (!$privateKey) {
    throw new \Exception("Invalid Private Key: " . openssl_error_string());
}

// تأكد من صحة الشهادة
$cert = openssl_x509_read($certificateContent);
if (!$cert) {
    throw new \Exception("Invalid Certificate: " . openssl_error_string());
}
```

---

### ❌ خطأ: "Certificate Chain Invalid"

**السبب**:
- سلسلة الشهادات غير كاملة
- الشهادة غير موثوقة

**الحل**:
```
1. تأكد من استخدام الشهادة الصادرة من الهيئة
2. لا تعدل الشهادة يدوياً
3. احصل على شهادة جديدة إذا لزم الأمر
```

---

## 🔍 كيف تعرف سبب الخطأ؟

### 1. تحقق من Logs
```bash
# Laravel Logs
tail -f storage/logs/laravel.log

# ZATCA Specific Logs
grep "ZATCA" storage/logs/laravel.log
```

### 2. تحقق من رد الهيئة
```php
// في قاعدة البيانات
SELECT id, invoiceNumber, zatca_status, zatca_response
FROM sales
WHERE zatca_status = 'FAILED'
ORDER BY id DESC
LIMIT 10;
```

### 3. استخدم دالة الفحص
```php
// في Controller أو Console
$sale = Sale::find(1);
$issues = checkZatcaComplianceIssues($sale);

if (!empty($issues)) {
    foreach ($issues as $issue) {
        echo "❌ " . $issue . "\n";
    }
}
```

---

## 📝 Checklist قبل الإرسال

قبل إرسال أي فاتورة للهيئة، تأكد من:

### معلومات البائع
- [ ] الرقم الضريبي 15 رقم
- [ ] رقم المبنى موجود
- [ ] اسم الشارع موجود
- [ ] الحي موجود
- [ ] المدينة موجودة
- [ ] الرمز البريدي موجود (5 أرقام)
- [ ] كود الدولة موجود (SA)

### معلومات المشتري (B2B فقط)
- [ ] الرقم الضريبي 15 رقم
- [ ] العنوان الكامل موجود
- [ ] نوع العميل = b2b

### معلومات الفاتورة
- [ ] UUID فريد موجود
- [ ] رقم الفاتورة فريد
- [ ] التاريخ صحيح (ليس في المستقبل)
- [ ] المبلغ الإجمالي موجب
- [ ] الضريبة محسوبة صحيح
- [ ] يوجد منتج واحد على الأقل

### الإعدادات
- [ ] الشهادة الرقمية موجودة
- [ ] المفتاح الخاص موجود
- [ ] البيئة محددة (sandbox/production)
- [ ] Queue Worker يعمل

---

## 🆘 ماذا تفعل إذا فشل كل شيء؟

### الخطوة 1: جمع المعلومات
```bash
# معلومات النظام
php -v
php -m | grep openssl
php -m | grep curl

# معلومات Laravel
php artisan --version
php artisan queue:work --help

# معلومات الخطأ
tail -100 storage/logs/laravel.log > error_log.txt
```

### الخطوة 2: اختبار الاتصال
```php
// اختبر الاتصال بخوادم الهيئة
$response = Http::get('https://gw-fatoora.zatca.gov.sa/e-invoicing/developer-portal');
echo $response->status(); // يجب أن يكون 200 أو 401
```

### الخطوة 3: اختبار الشهادة
```php
// تحقق من صحة الشهادة
$business = Business::find(1);
$zatcaSetting = $business->zatca_setting;

if (empty($zatcaSetting['csid'])) {
    echo "❌ الشهادة غير موجودة\n";
} else {
    echo "✅ الشهادة موجودة\n";
    echo "البيئة: " . ($zatcaSetting['environment'] ?? 'غير محدد') . "\n";
    echo "الحالة: " . ($zatcaSetting['status'] ?? 'غير محدد') . "\n";
}
```

### الخطوة 4: ابدأ من جديد
```
1. احذف الشهادة الحالية
2. احصل على OTP جديد
3. احصل على شهادة جديدة
4. اختبر فاتورة بسيطة
5. راجع الأخطاء خطوة بخطوة
```

---

## 📞 مصادر المساعدة

### وثائق ZATCA الرسمية
```
https://zatca.gov.sa/ar/E-Invoicing/Pages/default.aspx
```

### بوابة المطورين
```
https://zatca.gov.sa/ar/E-Invoicing/SystemsDevelopers/Pages/TechnicalRequirements.aspx
```

### الدعم الفني للهيئة
```
الهاتف: 19993
البريد: info@zatca.gov.sa
```

---

## ✅ نصائح لتجنب الأخطاء

### 1. اختبر في Sandbox أولاً
```
❌ لا تختبر مباشرة في Production
✅ استخدم Sandbox للاختبار
```

### 2. تحقق من البيانات قبل الإرسال
```php
// استخدم دالة الفحص
$issues = checkZatcaComplianceIssues($sale);
if (!empty($issues)) {
    // أصلح المشاكل قبل الإرسال
}
```

### 3. احتفظ بنسخة احتياطية
```bash
# احتفظ بنسخة من الشهادات
cp .env .env.backup
mysqldump database > backup.sql
```

### 4. راقب الـ Logs
```bash
# راقب الأخطاء باستمرار
tail -f storage/logs/laravel.log | grep "ERROR"
```

### 5. اختبر فواتير متنوعة
```
✅ فاتورة B2C بسيطة
✅ فاتورة B2B كاملة
✅ فاتورة بخصم
✅ فاتورة بشحن
✅ فاتورة بمنتجات متعددة
```

---

**تذكر**: معظم الأخطاء سببها بيانات ناقصة أو خاطئة، وليس مشكلة في الكود! 🎯
