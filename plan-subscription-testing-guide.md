# سيناريو اختبار الاشتراك في باقة - Plan Subscription Testing

## الخطوة 1: إعداد السوبر أدمن

### أ) إعداد إعدادات ميسر العامة
1. **إضافة الروت للسوبر أدمن:**
```php
// في routes/web.php
Route::prefix('super-admin')->middleware(['auth', 'super_admin'])->group(function () {
    Route::get('/moyasar', [App\Http\Controllers\SuperAdminMoyasarController::class, 'index'])->name('super-admin.moyasar.index');
    Route::post('/moyasar', [App\Http\Controllers\SuperAdminMoyasarController::class, 'store'])->name('super-admin.moyasar.store');
});
```

2. **الدخول إلى صفحة إعدادات ميسر:**
   - URL: `http://your-domain.com/super-admin/moyasar`
   - تسجيل الدخول كسوبر أدمن

3. **إدخال مفاتيح الاختبار:**
   - Test Publishable Key: `pk_test_vcFUHJGEzPBIBWkwUyOlUhXN`
   - Test Secret Key: `sk_test_kovrMB0mupbQkIQUXyoUHgLy`
   - Default Currency: `SAR`
   - تفعيل: ✅ Enable Moyasar for all businesses

4. **حفظ الإعدادات**

### ب) إنشاء باقة اشتراك
1. الذهاب إلى إدارة الباقات
2. إنشاء باقة جديدة:
   - اسم الباقة: "باقة تجريبية"
   - السعر: 100 ريال
   - المدة: شهر واحد
   - الميزات: حسب الحاجة

## الخطوة 2: إعداد التاجر

### أ) تسجيل حساب تاجر جديد
1. الذهاب إلى صفحة التسجيل
2. إنشاء حساب جديد
3. تسجيل الدخول

### ب) إعداد ميسر للتاجر
1. الذهاب إلى إعدادات ميسر
2. اختيار البيئة: "Test Environment"
3. حفظ الإعدادات

## الخطوة 3: اختبار عملية الاشتراك

### أ) اختيار الباقة
1. الذهاب إلى صفحة الباقات
2. اختيار "باقة تجريبية"
3. النقر على "اشترك الآن"

### ب) صفحة الدفع
1. التأكد من ظهور:
   - المبلغ: 100.00 SAR
   - اسم الباقة
   - تفاصيل الاشتراك

2. **بيانات البطاقة التجريبية:**
   - رقم البطاقة: `4111111111111111`
   - CVV: `123`
   - تاريخ الانتهاء: `12/2025`
   - اسم حامل البطاقة: `Test User`

### ج) إتمام الدفع
1. إدخال بيانات البطاقة
2. النقر على "ادفع الآن"
3. انتظار التحويل لصفحة النجاح

## الخطوة 4: التحقق من النتائج

### أ) فحص قاعدة البيانات
```sql
-- فحص جدول الاشتراكات
SELECT * FROM plan_subscribes WHERE business_id = YOUR_BUSINESS_ID ORDER BY created_at DESC LIMIT 1;

-- فحص جدول المدفوعات
SELECT * FROM payments WHERE gateway = 'moyasar' ORDER BY created_at DESC LIMIT 1;

-- فحص حالة الأعمال
SELECT * FROM businesses WHERE id = YOUR_BUSINESS_ID;
```

### ب) فحص السجلات
```bash
# مراقبة سجلات Laravel
tail -f storage/logs/laravel.log

# البحث عن سجلات ميسر
grep "Moyasar" storage/logs/laravel.log
```

### ج) فحص لوحة تحكم ميسر
1. الدخول إلى https://dashboard.moyasar.com
2. فحص قسم Payments
3. التأكد من وجود الدفعة الجديدة

## الخطوة 5: اختبار الحالات المختلفة

### أ) اختبار فشل الدفع
- استخدام بطاقة فاشلة: `4000000000000002`
- التأكد من عرض رسالة خطأ مناسبة

### ب) اختبار انقطاع الاتصال
- قطع الإنترنت أثناء الدفع
- التأكد من معالجة الخطأ

### ج) اختبار البطاقات المختلفة
- بطاقة مدى: `5297410000000000`
- بطاقة منتهية الصلاحية: `4111111111111111` مع تاريخ قديم

## الخطوة 6: أوامر الفحص السريع

### أ) فحص الإعدادات
```bash
php artisan tinker
>>> $settings = App\Models\Setting::where('key', 'moyasar_settings')->first();
>>> $settings ? json_decode($settings->value, true) : 'Not found';
```

### ب) فحص أعمال التاجر
```bash
php artisan tinker
>>> $business = App\Models\Business::find(1); // استبدل 1 برقم العمل
>>> $business->moyasar_setting;
```

### ج) اختبار إنشاء دفعة
```bash
php artisan tinker
>>> $moyasar = new App\Library\Moyasar();
>>> // اختبار الحصول على المفاتيح
```

## الخطوة 7: نقاط الفحص الحرجة

### ✅ يجب أن تعمل:
- [ ] إعدادات السوبر أدمن تُحفظ بنجاح
- [ ] إعدادات التاجر تُحفظ بنجاح
- [ ] صفحة الدفع تظهر بالمبلغ الصحيح
- [ ] الدفع بالبطاقة التجريبية ينجح
- [ ] الاشتراك يُسجل في قاعدة البيانات
- [ ] حالة العمل تتحدث (active)
- [ ] السجلات تُكتب بشكل صحيح

### ❌ يجب أن تفشل:
- [ ] الدفع ببطاقة فاشلة
- [ ] الدفع ببطاقة منتهية الصلاحية
- [ ] الوصول بدون تفعيل ميسر من السوبر أدمن

## الخطوة 8: استكشاف الأخطاء

### مشاكل شائعة:
1. **خطأ "Moyasar not enabled":**
   - تأكد من تفعيل ميسر في إعدادات السوبر أدمن

2. **خطأ "Invalid API key":**
   - تأكد من صحة مفاتيح الاختبار

3. **خطأ "Payment failed":**
   - تأكد من استخدام بطاقة اختبار صحيحة

4. **صفحة بيضاء:**
   - فحص سجلات الأخطاء في `storage/logs/laravel.log`

## الخطوة 9: التحقق النهائي

بعد نجاح الاختبار، تأكد من:
- [ ] الاشتراك نشط في لوحة التاجر
- [ ] تاريخ انتهاء الاشتراك صحيح
- [ ] الميزات متاحة للتاجر
- [ ] الدفعة مسجلة في ميسر
- [ ] الإشعارات تعمل (إن وجدت)