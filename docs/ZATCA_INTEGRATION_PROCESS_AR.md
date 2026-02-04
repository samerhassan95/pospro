# 🚀 دليل التكامل الكامل مع هيئة الزكاة والضريبة والجمارك (ZATCA)

## 📋 جدول المحتويات
1. [نظرة عامة](#نظرة-عامة)
2. [المراحل الأساسية](#المراحل-الأساسية)
3. [الحاجات الموجودة حالياً](#الحاجات-الموجودة-حالياً)
4. [الحاجات الناقصة](#الحاجات-الناقصة)
5. [خطوات التنفيذ التفصيلية](#خطوات-التنفيذ-التفصيلية)
6. [الاختبار والتفعيل](#الاختبار-والتفعيل)

---

## 🎯 نظرة عامة

### ما هي مراحل التكامل مع ZATCA؟

التكامل مع هيئة الزكاة يتم على **مرحلتين**:

#### **المرحلة الأولى (Phase 1)** - الفوترة الإلكترونية ✅
- إصدار فواتير إلكترونية بصيغة XML
- إضافة QR Code على الفاتورة
- حفظ الفواتير محلياً
- **لا يتطلب اتصال مباشر بالهيئة**

#### **المرحلة الثانية (Phase 2)** - التكامل والربط 🔄
- ربط النظام مع منصة الهيئة
- إرسال الفواتير للهيئة فوراً
- الحصول على موافقة الهيئة
- **يتطلب اتصال مباشر وشهادات رقمية**

---

## 📊 المراحل الأساسية

### Phase 1: الفوترة الإلكترونية (Generation)
```
✅ إنشاء فاتورة إلكترونية
✅ إضافة QR Code
✅ حفظ الفاتورة
✅ طباعة الفاتورة
```

### Phase 2: التكامل والربط (Integration)
```
🔄 الحصول على شهادة رقمية (CSID)
🔄 توقيع الفاتورة رقمياً
🔄 إرسال الفاتورة للهيئة
🔄 استقبال رد الهيئة
🔄 تحديث حالة الفاتورة
```

---

## ✅ الحاجات الموجودة حالياً

### 1. **قاعدة البيانات** ✅
- ✅ جدول `sales` يحتوي على:
  - `uuid` - معرف فريد للفاتورة
  - `invoice_type` - نوع الفاتورة (B2B/B2C)
  - `invoice_hash` - Hash الفاتورة
  - `cryptographic_stamp` - التوقيع الرقمي
  - `zatca_status` - حالة الإرسال للهيئة
  - `zatca_response` - رد الهيئة
  - `vat_amount` - قيمة الضريبة
  - `vat_percent` - نسبة الضريبة

- ✅ جدول `businesses` يحتوي على:
  - `vat_no` - الرقم الضريبي
  - `building_number` - رقم المبنى
  - `street_name` - اسم الشارع
  - `district` - الحي
  - `city` - المدينة
  - `postal_code` - الرمز البريدي
  - `country_code` - كود الدولة
  - `zatca_setting` - إعدادات ZATCA (JSON)

- ✅ جدول `parties` يحتوي على:
  - `zatca_type` - نوع العميل (B2B/B2C)
  - `vat_number` - الرقم الضريبي للعميل
  - `building_number` - رقم المبنى
  - `street_name` - اسم الشارع
  - `district` - الحي
  - `city` - المدينة
  - `postal_code` - الرمز البريدي
  - `country_code` - كود الدولة

### 2. **الكود البرمجي** ✅
- ✅ `generateZatcaQrCode()` - توليد QR Code
- ✅ `checkZatcaComplianceIssues()` - فحص المشاكل
- ✅ `ReportSaleToZatca` Job - إرسال الفاتورة للهيئة
- ✅ `ZatcaService` - خدمة التكامل مع ZATCA
- ✅ `UblGenerator` - توليد XML بصيغة UBL 2.1

### 3. **الفواتير** ✅
- ✅ فاتورة B2B كاملة
- ✅ فاتورة B2C مبسطة
- ✅ فاتورة POS حرارية
- ✅ QR Code على كل الفواتير

---

## ❌ الحاجات الناقصة

### 1. **واجهة إعدادات ZATCA** ❌
**المطلوب**: صفحة في لوحة التحكم لإدخال:
- OTP (One Time Password) من بوابة الهيئة
- معلومات الشركة للشهادة الرقمية
- اختيار البيئة (Sandbox/Production)
- عرض حالة الاتصال

**الموقع المقترح**: `Settings > ZATCA Integration`

### 2. **عملية الحصول على الشهادة الرقمية (CSID)** ❌
**المطلوب**:
- زر "Get Compliance CSID" في الإعدادات
- إدخال OTP من بوابة الهيئة
- توليد CSR تلقائياً
- حفظ الشهادة والمفاتيح

### 3. **اختبار الفواتير (Compliance Check)** ❌
**المطلوب**:
- زر "Test Invoice" في صفحة الفاتورة
- إرسال فاتورة تجريبية للهيئة
- عرض نتيجة الاختبار
- إصلاح الأخطاء إن وجدت

### 4. **الحصول على شهادة الإنتاج (Production CSID)** ❌
**المطلوب**:
- بعد نجاح الاختبار
- زر "Get Production CSID"
- تفعيل الإرسال التلقائي

### 5. **لوحة متابعة حالة الفواتير** ❌
**المطلوب**:
- عرض حالة كل فاتورة (REPORTED/FAILED/PENDING)
- إعادة إرسال الفواتير الفاشلة
- عرض رسائل الخطأ من الهيئة
- إحصائيات الفواتير المرسلة

### 6. **معالجة الأخطاء** ❌
**المطلوب**:
- رسائل خطأ واضحة بالعربية
- اقتراحات لحل المشاكل
- إعادة المحاولة التلقائية
- تنبيهات للمستخدم

---

## 🔧 خطوات التنفيذ التفصيلية

### المرحلة 1: إنشاء واجهة إعدادات ZATCA

#### الخطوة 1.1: إضافة صفحة الإعدادات
```
الموقع: Modules/Business/resources/views/settings/zatca.blade.php
```

**المحتوى المطلوب**:
- نموذج إدخال معلومات الشركة
- حقل OTP
- اختيار البيئة (Sandbox/Production)
- زر "Get Compliance CSID"
- عرض حالة الشهادة الحالية

#### الخطوة 1.2: إضافة Controller Methods
```
الموقع: Modules/Business/App/Http/Controllers/SettingController.php
```

**Methods المطلوبة**:
- `zatcaSettings()` - عرض صفحة الإعدادات
- `getComplianceCsid()` - الحصول على شهادة الاختبار
- `testInvoice()` - اختبار فاتورة
- `getProductionCsid()` - الحصول على شهادة الإنتاج

---

### المرحلة 2: تنفيذ عملية الحصول على الشهادة

#### الخطوة 2.1: الحصول على OTP من بوابة الهيئة
```
1. الدخول على بوابة الهيئة: https://fatoora.zatca.gov.sa
2. تسجيل الدخول بحساب المنشأة
3. الذهاب إلى "إدارة الأجهزة"
4. إضافة جهاز جديد
5. نسخ OTP (صالح لمدة ساعة واحدة)
```

#### الخطوة 2.2: توليد CSR والحصول على CSID
```php
// في Controller
public function getComplianceCsid(Request $request)
{
    $request->validate([
        'otp' => 'required|string',
        'common_name' => 'required|string',
        'organization_identifier' => 'required|string',
        'organization_unit_name' => 'required|string',
    ]);

    $zatcaService = new ZatcaService();
    
    $csrConfig = [
        'common_name' => $request->common_name,
        'organization_identifier' => $request->organization_identifier,
        'organization_unit_name' => $request->organization_unit_name,
    ];

    try {
        $result = $zatcaService->issueComplianceCsid(
            $request->otp,
            $csrConfig,
            'sandbox' // أو 'production'
        );

        // حفظ النتيجة في قاعدة البيانات
        $business = auth()->user()->business;
        $business->update([
            'zatca_setting' => [
                'csid' => $result['csid'],
                'secret' => $result['secret'],
                'private_key' => $result['private_key'],
                'public_key' => $result['public_key'],
                'request_id' => $result['request_id'],
                'environment' => 'sandbox',
                'status' => 'compliance',
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم الحصول على شهادة الاختبار بنجاح'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'فشل الحصول على الشهادة: ' . $e->getMessage()
        ], 400);
    }
}
```

---

### المرحلة 3: اختبار الفواتير (Compliance Testing)

#### الخطوة 3.1: إضافة زر الاختبار في صفحة الفاتورة
```blade
{{-- في صفحة عرض الفاتورة --}}
@if($sale->zatca_status !== 'REPORTED')
<button onclick="testInvoice({{ $sale->id }})" class="btn btn-warning">
    <i class="fas fa-vial"></i> اختبار الفاتورة مع الهيئة
</button>
@endif
```

#### الخطوة 3.2: تنفيذ اختبار الفاتورة
```php
public function testInvoice($saleId)
{
    $sale = Sale::findOrFail($saleId);
    $business = $sale->business;

    if (empty($business->zatca_setting['csid'])) {
        return response()->json([
            'success' => false,
            'message' => 'يجب الحصول على شهادة ZATCA أولاً'
        ], 400);
    }

    try {
        $zatcaService = new ZatcaService();
        $ublGenerator = new UblGenerator();

        // 1. توليد XML
        $xmlContent = $ublGenerator->generateInvoiceXml(
            $sale, 
            $business, 
            $business->zatca_setting
        );

        // 2. توقيع الفاتورة
        $signingResult = $zatcaService->signInvoice(
            $xmlContent,
            $business->zatca_setting['private_key'],
            $business->zatca_setting['csid']
        );

        // 3. اختبار الفاتورة
        $response = $zatcaService->checkInvoiceCompliance(
            $signingResult['xml'],
            $sale->uuid,
            $signingResult['hash'],
            $business->zatca_setting
        );

        // 4. تحليل النتيجة
        if ($response['success']) {
            $errors = $response['body']['validationResults']['errorMessages'] ?? [];
            $warnings = $response['body']['validationResults']['warningMessages'] ?? [];

            if (empty($errors)) {
                return response()->json([
                    'success' => true,
                    'message' => 'الفاتورة متوافقة مع متطلبات الهيئة ✅',
                    'warnings' => $warnings
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'الفاتورة بها أخطاء',
                    'errors' => $errors,
                    'warnings' => $warnings
                ], 400);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'فشل الاتصال بالهيئة',
                'details' => $response['body']
            ], 400);
        }

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'خطأ: ' . $e->getMessage()
        ], 500);
    }
}
```

---

### المرحلة 4: الحصول على شهادة الإنتاج

#### الخطوة 4.1: بعد نجاح الاختبار
```php
public function getProductionCsid()
{
    $business = auth()->user()->business;
    $zatcaSetting = $business->zatca_setting;

    if (empty($zatcaSetting['request_id'])) {
        return response()->json([
            'success' => false,
            'message' => 'يجب اختبار الفواتير أولاً'
        ], 400);
    }

    try {
        $zatcaService = new ZatcaService();
        
        $result = $zatcaService->requestProductionCsid(
            $zatcaSetting['request_id'],
            $zatcaSetting
        );

        // تحديث الإعدادات
        $zatcaSetting['csid'] = $result['binarySecurityToken'];
        $zatcaSetting['secret'] = $result['secret'];
        $zatcaSetting['status'] = 'production';
        $zatcaSetting['environment'] = 'production';

        $business->update(['zatca_setting' => $zatcaSetting]);

        return response()->json([
            'success' => true,
            'message' => 'تم الحصول على شهادة الإنتاج بنجاح! 🎉'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'فشل الحصول على شهادة الإنتاج: ' . $e->getMessage()
        ], 400);
    }
}
```

---

### المرحلة 5: الإرسال التلقائي للفواتير

#### الخطوة 5.1: تفعيل الإرسال التلقائي
```php
// في AcnooSaleController@store
// بعد إنشاء الفاتورة

if (!empty($business->zatca_setting['csid']) && 
    $business->zatca_setting['status'] === 'production') {
    
    // إرسال الفاتورة للهيئة تلقائياً
    \App\Jobs\ReportSaleToZatca::dispatch($sale->id);
}
```

---

### المرحلة 6: لوحة متابعة الفواتير

#### الخطوة 6.1: إضافة صفحة متابعة
```
الموقع: Modules/Business/resources/views/sales/zatca-status.blade.php
```

**المحتوى**:
- جدول بكل الفواتير
- عمود حالة ZATCA (REPORTED/FAILED/PENDING)
- زر إعادة الإرسال للفواتير الفاشلة
- عرض رسائل الخطأ
- إحصائيات

---

## 🧪 الاختبار والتفعيل

### خطوات الاختبار الكاملة

#### 1. **البيئة التجريبية (Sandbox)**
```
✅ الحصول على OTP من بوابة الهيئة
✅ الحصول على Compliance CSID
✅ إنشاء فاتورة تجريبية
✅ اختبار الفاتورة
✅ إصلاح أي أخطاء
✅ اختبار 5-10 فواتير مختلفة
```

#### 2. **الانتقال للإنتاج (Production)**
```
✅ التأكد من نجاح كل الاختبارات
✅ الحصول على Production CSID
✅ تفعيل الإرسال التلقائي
✅ مراقبة الفواتير الأولى
✅ التأكد من عدم وجود أخطاء
```

---

## 📝 ملاحظات مهمة

### ⚠️ تحذيرات
1. **OTP صالح لمدة ساعة واحدة فقط**
2. **لا تشارك المفاتيح الخاصة (Private Keys)**
3. **احتفظ بنسخة احتياطية من الشهادات**
4. **اختبر في Sandbox قبل Production**

### 💡 نصائح
1. **ابدأ بفواتير بسيطة في الاختبار**
2. **راجع رسائل الخطأ بعناية**
3. **تأكد من صحة الأرقام الضريبية**
4. **تأكد من صحة العناوين**

---

## 🎯 الخلاصة

### ما هو موجود؟ ✅
- قاعدة البيانات كاملة
- الكود البرمجي للتكامل
- الفواتير متوافقة
- QR Code يعمل

### ما هو ناقص؟ ❌
- واجهة إعدادات ZATCA
- عملية الحصول على الشهادة
- اختبار الفواتير
- لوحة المتابعة

### الخطوة التالية؟ 🚀
**إنشاء واجهة إعدادات ZATCA** - هذه أول خطوة عملية

---

## 📞 الدعم

إذا واجهت أي مشكلة:
1. راجع رسائل الخطأ في `storage/logs/laravel.log`
2. تأكد من صحة البيانات المدخلة
3. اختبر في Sandbox أولاً
4. راجع وثائق ZATCA الرسمية

---

**تم إنشاء هذا الدليل في:** {{ date('Y-m-d H:i:s') }}
