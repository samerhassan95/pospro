# 🎯 الخطوات البسيطة للتكامل مع هيئة الزكاة

## 📌 ملخص سريع

### الوضع الحالي ✅
```
✅ الفواتير جاهزة ومتوافقة
✅ QR Code يعمل
✅ البيانات كاملة (B2B/B2C)
✅ الكود البرمجي موجود
```

### المطلوب عمله ❌
```
❌ واجهة الإعدادات
❌ الحصول على الشهادة
❌ الاختبار
❌ التفعيل
```

---

## 🚀 البروسيس خطوة بخطوة

### المرحلة 1️⃣: الإعداد الأولي (30 دقيقة)

#### الخطوة 1: إنشاء صفحة إعدادات ZATCA
```
📁 الملف: Modules/Business/resources/views/settings/zatca.blade.php
```

**محتوى الصفحة**:
```html
<form id="zatca-settings-form">
    <!-- معلومات الشركة -->
    <input name="common_name" placeholder="اسم الشركة" required>
    <input name="organization_identifier" placeholder="الرقم الضريبي" required>
    <input name="organization_unit_name" placeholder="اسم الفرع" required>
    
    <!-- OTP من بوابة الهيئة -->
    <input name="otp" placeholder="OTP (من بوابة الهيئة)" required>
    
    <!-- البيئة -->
    <select name="environment">
        <option value="sandbox">تجريبي (Sandbox)</option>
        <option value="production">إنتاج (Production)</option>
    </select>
    
    <!-- زر الحصول على الشهادة -->
    <button type="submit">الحصول على شهادة الاختبار</button>
</form>

<!-- عرض حالة الشهادة -->
<div id="certificate-status">
    @if($business->zatca_setting)
        <div class="alert alert-success">
            ✅ الشهادة موجودة
            <br>الحالة: {{ $business->zatca_setting['status'] ?? 'غير معروف' }}
            <br>البيئة: {{ $business->zatca_setting['environment'] ?? 'غير معروف' }}
        </div>
    @else
        <div class="alert alert-warning">
            ⚠️ لم يتم الحصول على الشهادة بعد
        </div>
    @endif
</div>
```

#### الخطوة 2: إضافة Route
```php
// في routes/web.php أو routes الخاص بالـ Module
Route::get('/settings/zatca', [SettingController::class, 'zatcaSettings'])
    ->name('settings.zatca');
    
Route::post('/settings/zatca/get-csid', [SettingController::class, 'getComplianceCsid'])
    ->name('settings.zatca.get-csid');
```

#### الخطوة 3: إضافة Methods في Controller
```php
// في SettingController.php

public function zatcaSettings()
{
    $business = auth()->user()->business;
    return view('business::settings.zatca', compact('business'));
}

public function getComplianceCsid(Request $request)
{
    $request->validate([
        'otp' => 'required|string',
        'common_name' => 'required|string',
        'organization_identifier' => 'required|string',
        'organization_unit_name' => 'required|string',
        'environment' => 'required|in:sandbox,production',
    ]);

    $zatcaService = app(\App\Services\Zatca\ZatcaService::class);
    
    $csrConfig = [
        'common_name' => $request->common_name,
        'organization_identifier' => $request->organization_identifier,
        'organization_unit_name' => $request->organization_unit_name,
    ];

    try {
        $result = $zatcaService->issueComplianceCsid(
            $request->otp,
            $csrConfig,
            $request->environment
        );

        $business = auth()->user()->business;
        $business->update([
            'zatca_setting' => [
                'csid' => $result['csid'],
                'secret' => $result['secret'],
                'private_key' => $result['private_key'],
                'public_key' => $result['public_key'],
                'request_id' => $result['request_id'],
                'environment' => $request->environment,
                'status' => 'compliance',
                'created_at' => now()->toDateTimeString(),
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => '✅ تم الحصول على شهادة الاختبار بنجاح!'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => '❌ فشل: ' . $e->getMessage()
        ], 400);
    }
}
```

---

### المرحلة 2️⃣: الحصول على OTP (5 دقائق)

#### كيف تحصل على OTP؟

1. **افتح بوابة الهيئة**
   ```
   https://fatoora.zatca.gov.sa
   ```

2. **سجل دخول** بحساب المنشأة

3. **اذهب إلى "إدارة الأجهزة"**

4. **اضغط "إضافة جهاز جديد"**

5. **املأ البيانات**:
   - اسم الجهاز: "POS System 1"
   - نوع الجهاز: "نظام نقاط البيع"

6. **انسخ OTP** (صالح لمدة ساعة واحدة)

---

### المرحلة 3️⃣: الحصول على الشهادة (دقيقتين)

#### الخطوات:

1. **افتح صفحة إعدادات ZATCA** في النظام
   ```
   Settings > ZATCA Integration
   ```

2. **املأ البيانات**:
   - اسم الشركة: مثل "شركة التجارة المحدودة"
   - الرقم الضريبي: 15 رقم (مثل: 123456789012345)
   - اسم الفرع: "الفرع الرئيسي"
   - OTP: الصق الكود من بوابة الهيئة
   - البيئة: اختر "تجريبي (Sandbox)"

3. **اضغط "الحصول على شهادة الاختبار"**

4. **انتظر** (5-10 ثواني)

5. **ستظهر رسالة نجاح** ✅

---

### المرحلة 4️⃣: اختبار الفواتير (10 دقائق)

#### الخطوة 1: إضافة زر الاختبار

```php
// في صفحة عرض الفاتورة
// Modules/Business/resources/views/sales/show.blade.php

@if($sale->zatca_status !== 'REPORTED')
<button onclick="testInvoiceWithZatca({{ $sale->id }})" 
        class="btn btn-warning">
    <i class="fas fa-vial"></i> اختبار مع الهيئة
</button>
@endif

<script>
function testInvoiceWithZatca(saleId) {
    if (!confirm('هل تريد اختبار هذه الفاتورة مع هيئة الزكاة؟')) {
        return;
    }
    
    fetch(`/sales/${saleId}/test-zatca`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            location.reload();
        } else {
            alert('❌ ' + data.message);
            if (data.errors) {
                console.log('الأخطاء:', data.errors);
            }
        }
    })
    .catch(error => {
        alert('❌ خطأ في الاتصال: ' + error);
    });
}
</script>
```

#### الخطوة 2: إضافة Route
```php
Route::post('/sales/{id}/test-zatca', [AcnooSaleController::class, 'testInvoiceWithZatca'])
    ->name('sales.test-zatca');
```

#### الخطوة 3: إضافة Method في Controller
```php
// في AcnooSaleController.php

public function testInvoiceWithZatca($id)
{
    $sale = Sale::with('business', 'party', 'details.product')->findOrFail($id);
    $business = $sale->business;

    // التحقق من وجود الشهادة
    if (empty($business->zatca_setting['csid'])) {
        return response()->json([
            'success' => false,
            'message' => 'يجب الحصول على شهادة ZATCA أولاً من الإعدادات'
        ], 400);
    }

    try {
        $zatcaService = app(\App\Services\Zatca\ZatcaService::class);
        $ublGenerator = app(\App\Services\Zatca\UblGenerator::class);

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
                // حفظ النتيجة
                $sale->update([
                    'invoice_hash' => $signingResult['hash'],
                    'cryptographic_stamp' => $signingResult['signature'],
                    'zatca_status' => 'TESTED',
                    'zatca_response' => $response['body']
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'الفاتورة متوافقة مع متطلبات الهيئة ✅',
                    'warnings' => $warnings
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'الفاتورة بها أخطاء يجب إصلاحها',
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
        \Log::error('ZATCA Test Error: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'خطأ: ' . $e->getMessage()
        ], 500);
    }
}
```

#### الخطوة 4: اختبر الفاتورة

1. **أنشئ فاتورة جديدة** (B2B أو B2C)
2. **افتح الفاتورة**
3. **اضغط "اختبار مع الهيئة"**
4. **انتظر النتيجة** (5-10 ثواني)
5. **إذا نجحت** ✅ - رائع!
6. **إذا فشلت** ❌ - راجع الأخطاء وأصلحها

---

### المرحلة 5️⃣: الحصول على شهادة الإنتاج (5 دقائق)

#### بعد نجاح الاختبار:

```php
// إضافة زر في صفحة الإعدادات
@if($business->zatca_setting && $business->zatca_setting['status'] === 'compliance')
<button onclick="getProductionCsid()" class="btn btn-success">
    <i class="fas fa-rocket"></i> الحصول على شهادة الإنتاج
</button>
@endif

<script>
function getProductionCsid() {
    if (!confirm('هل أنت متأكد؟ سيتم تفعيل الإرسال التلقائي للفواتير.')) {
        return;
    }
    
    fetch('/settings/zatca/get-production-csid', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            location.reload();
        } else {
            alert('❌ ' + data.message);
        }
    });
}
</script>
```

```php
// في SettingController.php
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
        $zatcaService = app(\App\Services\Zatca\ZatcaService::class);
        
        $result = $zatcaService->requestProductionCsid(
            $zatcaSetting['request_id'],
            $zatcaSetting
        );

        // تحديث الإعدادات
        $zatcaSetting['csid'] = $result['binarySecurityToken'];
        $zatcaSetting['secret'] = $result['secret'];
        $zatcaSetting['status'] = 'production';
        $zatcaSetting['production_activated_at'] = now()->toDateTimeString();

        $business->update(['zatca_setting' => $zatcaSetting]);

        return response()->json([
            'success' => true,
            'message' => 'تم الحصول على شهادة الإنتاج بنجاح! 🎉 الآن سيتم إرسال الفواتير تلقائياً.'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'فشل: ' . $e->getMessage()
        ], 400);
    }
}
```

---

### المرحلة 6️⃣: التفعيل والمراقبة (مستمر)

#### الإرسال التلقائي يعمل بالفعل! ✅

الكود الموجود في `AcnooSaleController@store`:
```php
// بعد إنشاء الفاتورة
if (!empty($business->zatca_setting) && 
    !empty($business->zatca_setting['csid'])) {
    \App\Jobs\ReportSaleToZatca::dispatch($sale->id);
}
```

#### إضافة عمود الحالة في جدول الفواتير

```blade
{{-- في sales/index.blade.php --}}
<table>
    <thead>
        <tr>
            <th>رقم الفاتورة</th>
            <th>العميل</th>
            <th>المبلغ</th>
            <th>حالة ZATCA</th>
            <th>الإجراءات</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sales as $sale)
        <tr>
            <td>{{ $sale->invoiceNumber }}</td>
            <td>{{ $sale->party->name ?? 'Guest' }}</td>
            <td>{{ currency_format($sale->totalAmount) }}</td>
            <td>
                @if($sale->zatca_status === 'REPORTED')
                    <span class="badge bg-success">✅ مُرسلة</span>
                @elseif($sale->zatca_status === 'FAILED')
                    <span class="badge bg-danger">❌ فشلت</span>
                @elseif($sale->zatca_status === 'REPORTING')
                    <span class="badge bg-warning">⏳ جاري الإرسال</span>
                @else
                    <span class="badge bg-secondary">⚪ لم تُرسل</span>
                @endif
            </td>
            <td>
                @if($sale->zatca_status === 'FAILED')
                <button onclick="retryZatca({{ $sale->id }})" 
                        class="btn btn-sm btn-warning">
                    <i class="fas fa-redo"></i> إعادة المحاولة
                </button>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
```

---

## 📊 الجدول الزمني المتوقع

| المرحلة | الوقت المتوقع | الصعوبة |
|---------|---------------|---------|
| إنشاء واجهة الإعدادات | 30 دقيقة | سهل ⭐ |
| الحصول على OTP | 5 دقائق | سهل جداً ⭐ |
| الحصول على الشهادة | دقيقتين | سهل جداً ⭐ |
| إضافة زر الاختبار | 15 دقيقة | سهل ⭐ |
| اختبار الفواتير | 10 دقائق | متوسط ⭐⭐ |
| شهادة الإنتاج | 5 دقائق | سهل ⭐ |
| إضافة عمود الحالة | 10 دقائق | سهل ⭐ |
| **المجموع** | **~75 دقيقة** | |

---

## ✅ Checklist التنفيذ

### Phase 1: الإعداد
- [ ] إنشاء صفحة `zatca.blade.php`
- [ ] إضافة Routes
- [ ] إضافة Methods في Controller
- [ ] اختبار الصفحة

### Phase 2: الحصول على الشهادة
- [ ] الدخول على بوابة الهيئة
- [ ] الحصول على OTP
- [ ] إدخال البيانات في النظام
- [ ] الحصول على Compliance CSID
- [ ] التحقق من حفظ الشهادة

### Phase 3: الاختبار
- [ ] إضافة زر الاختبار
- [ ] إضافة Route و Method
- [ ] إنشاء فاتورة تجريبية
- [ ] اختبار الفاتورة
- [ ] إصلاح أي أخطاء
- [ ] اختبار 5 فواتير مختلفة

### Phase 4: الإنتاج
- [ ] إضافة زر شهادة الإنتاج
- [ ] الحصول على Production CSID
- [ ] التحقق من التفعيل
- [ ] اختبار الإرسال التلقائي

### Phase 5: المراقبة
- [ ] إضافة عمود الحالة
- [ ] إضافة زر إعادة المحاولة
- [ ] مراقبة الفواتير الأولى
- [ ] التأكد من عدم وجود أخطاء

---

## 🎯 الخلاصة

### الوضع الحالي
```
✅ 80% من العمل جاهز
✅ الكود البرمجي موجود
✅ الفواتير متوافقة
```

### المطلوب
```
❌ 20% واجهات فقط
❌ ~75 دقيقة عمل
❌ سهل التنفيذ
```

### النتيجة النهائية
```
🎉 نظام متكامل مع هيئة الزكاة
🎉 إرسال تلقائي للفواتير
🎉 متوافق 100% مع المتطلبات
```

---

**جاهز للبدء؟ ابدأ بالمرحلة 1️⃣ الآن! 🚀**
