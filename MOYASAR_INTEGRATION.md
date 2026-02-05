# Moyasar Payment Gateway Integration

## نظرة عامة

تم تكامل بوابة الدفع Moyasar بشكل شامل في نظام Super Admin POS لتوفير حلول دفع آمنة ومتقدمة للشركات في المملكة العربية السعودية ودول الخليج.

## الميزات الرئيسية

### 🔄 تكامل شامل
- **مدفوعات المبيعات**: دفع مستحقات المبيعات مباشرة
- **مدفوعات المشتريات**: دفع مستحقات الموردين
- **تحصيل المستحقات**: تحصيل المبالغ المستحقة من العملاء
- **الفواتير العامة**: دفع الفواتير عبر رابط عام
- **اشتراكات الخطط**: دفع رسوم الاشتراك في الخطط

### 💳 طرق الدفع المدعومة
- بطاقات الائتمان (Visa, Mastercard)
- بطاقات مدى
- Apple Pay
- STC Pay

### 🌍 العملات المدعومة
- الريال السعودي (SAR) - العملة الأساسية
- الدولار الأمريكي (USD)
- اليورو (EUR)
- الدرهم الإماراتي (AED)
- الدينار الكويتي (KWD)
- الدينار البحريني (BHD)
- الريال العماني (OMR)
- الريال القطري (QAR)

## التثبيت والإعداد

### 1. تشغيل Migration
```bash
php artisan migrate
```

### 2. إضافة الأذونات
```bash
php artisan db:seed --class=MoyasarPermissionSeeder
```

### 3. إعداد متغيرات البيئة (اختياري)
```env
MOYASAR_API_URL=https://api.moyasar.com/v1
MOYASAR_DEFAULT_CURRENCY=SAR
MOYASAR_TEST_MODE=false
MOYASAR_TIMEOUT=30
MOYASAR_LOGGING_ENABLED=true
```

## الاستخدام

### إعداد Moyasar للأعمال

1. انتقل إلى **الإعدادات** > **إعدادات Moyasar**
2. أدخل **API Secret Key** (يبدأ بـ sk_)
3. أدخل **Publishable Key** (يبدأ بـ pk_)
4. احفظ الإعدادات

### استخدام المدفوعات

#### في صفحة المبيعات
```php
// عرض زر الدفع عبر Moyasar
@include('business::sales.partials.moyasar-buttons', [
    'sale' => $sale,
    'business' => $business
])
```

#### في صفحة المشتريات
```php
// عرض زر الدفع عبر Moyasar
@include('business::purchases.partials.moyasar-buttons', [
    'purchase' => $purchase,
    'business' => $business
])
```

#### في صفحة المستحقات
```php
// عرض أزرار تحصيل المستحقات
@include('business::dues.partials.moyasar-buttons', [
    'parties' => $parties,
    'business' => $business
])
```

### استخدام JavaScript API

```javascript
// دفع مستحقات البيع
moyasarPayment.processPayment('sale', saleId);

// دفع مستحقات الشراء
moyasarPayment.processPayment('purchase', purchaseId);

// تحصيل المستحقات
moyasarPayment.processDueCollection(partyId);
```

## API Routes

### Business Module Routes
```php
// دفع مستحقات البيع
POST /business/moyasar/pay-sale-due/{sale_id}

// دفع مستحقات الشراء
POST /business/moyasar/pay-purchase-due/{purchase_id}

// تحصيل المستحقات
POST /business/moyasar/pay-due-collection

// معالجة دفع البيع المباشر
POST /business/moyasar/process-sale-payment

// معالجة دفع الشراء المباشر
POST /business/moyasar/process-purchase-payment
```

### Public Routes
```php
// عرض الفاتورة العامة
GET /invoice/{uuid}

// دفع الفاتورة العامة
POST /invoice/{uuid}/pay

// صفحة الدفع عبر Moyasar
GET /payment/moyasar/view

// معالجة حالة الدفع
GET /payment/moyasar/status
```

## الأمان

### تشفير البيانات
- جميع مفاتيح API محفوظة بشكل مشفر في قاعدة البيانات
- استخدام HTTPS لجميع الاتصالات
- التحقق من صحة المدفوعات عبر Moyasar API

### التحقق من المدفوعات
```php
// التحقق من حالة الدفع
$response = Http::withBasicAuth($api_key, '')
    ->get("https://api.moyasar.com/v1/payments/{$payment_id}");

if ($response->successful() && $response->json()['status'] == 'paid') {
    // معالجة الدفع الناجح
}
```

## معالجة الأخطاء

### أخطاء شائعة وحلولها

#### 1. "Moyasar settings not configured"
**الحل**: تأكد من إعداد مفاتيح API في إعدادات Moyasar

#### 2. "Session expired"
**الحل**: تأكد من أن الجلسة نشطة أثناء عملية الدفع

#### 3. "Amount cannot be more than due amount"
**الحل**: تأكد من أن المبلغ المدخل لا يتجاوز المبلغ المستحق

#### 4. "Payment failed"
**الحل**: تحقق من صحة مفاتيح API وحالة الاتصال بالإنترنت

## السجلات والمراقبة

### تفعيل السجلات
```php
// في ملف config/moyasar.php
'logging' => [
    'enabled' => true,
    'level' => 'info',
    'channel' => 'single',
],
```

### مراقبة المدفوعات
```php
// سجل نجاح الدفع
Log::info('Moyasar payment processed successfully', [
    'payment_id' => $payment_id,
    'amount' => $amount,
    'type' => $payment_type
]);

// سجل فشل الدفع
Log::error('Moyasar payment processing failed', [
    'payment_id' => $payment_id,
    'error' => $error_message
]);
```

## التخصيص

### تخصيص التصميم
```css
/* تخصيص أزرار Moyasar */
.moyasar-payment-btn {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    /* المزيد من التخصيصات */
}
```

### تخصيص الترجمات
```php
// في ملف lang/ar.json
"Pay via Moyasar": "الدفع عبر ميسر",
"Moyasar Settings": "إعدادات ميسر",
```

## الاختبار

### بيئة الاختبار
```env
MOYASAR_TEST_MODE=true
```

### بيانات اختبار
- **Test API Key**: sk_test_...
- **Test Publishable Key**: pk_test_...

### بطاقات اختبار
- **نجاح الدفع**: 4111111111111111
- **فشل الدفع**: 4000000000000002

## الدعم الفني

### معلومات الاتصال
- **الموقع الرسمي**: [moyasar.com](https://moyasar.com)
- **الوثائق**: [docs.moyasar.com](https://docs.moyasar.com)
- **الدعم**: support@moyasar.com

### الإبلاغ عن المشاكل
1. تحقق من السجلات في `storage/logs/laravel.log`
2. تأكد من صحة إعدادات API
3. تحقق من حالة الاتصال بالإنترنت
4. راجع وثائق Moyasar الرسمية

## التحديثات المستقبلية

### الميزات المخططة
- [ ] دعم المحافظ الرقمية الإضافية
- [ ] تقارير مفصلة للمدفوعات
- [ ] إشعارات الدفع عبر SMS
- [ ] دعم الدفع المتكرر
- [ ] تكامل مع أنظمة المحاسبة

### سجل التغييرات
- **v1.0.0**: الإصدار الأولي مع الميزات الأساسية
- **v1.1.0**: إضافة دعم الفواتير العامة
- **v1.2.0**: تحسينات الأمان والأداء

---

**ملاحظة**: هذا التكامل مصمم خصيصاً لنظام Super Admin POS ويتطلب حساب Moyasar نشط للعمل في البيئة الإنتاجية.