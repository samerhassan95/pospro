# تحديث قائمة فحص تكامل ميسر - Updated Moyasar Integration Testing

## الهيكل الجديد للإعدادات:

### 1. السوبر أدمن يدير:
- مفاتيح API للاختبار والإنتاج
- تفعيل/إلغاء تفعيل ميسر للنظام
- العملة الافتراضية
- نسبة العمولة
- سر الـ Webhook

### 2. التاجر يدير:
- اختيار البيئة (اختبار أم إنتاج) فقط

## خطوات الاختبار المحدثة:

### أ) إعداد السوبر أدمن:
1. الدخول إلى `/super-admin/moyasar`
2. إدخال مفاتيح الاختبار:
   - Test Publishable Key: `pk_test_xxxxxxxxxx`
   - Test Secret Key: `sk_test_xxxxxxxxxx`
3. تفعيل ميسر للنظام
4. حفظ الإعدادات

### ب) إعداد التاجر:
1. الدخول إلى إعدادات ميسر في لوحة التاجر
2. اختيار البيئة: "Test Environment"
3. حفظ الإعدادات

### ج) اختبار الدفع:
```bash
# فحص سريع
php artisan tinker < moyasar-health-check.php

# اختبار الوحدات
php artisan test tests/Feature/MoyasarIntegrationTest.php
```

### د) الروتات المطلوبة:
```php
// في routes/web.php للسوبر أدمن
Route::prefix('super-admin')->group(function () {
    Route::get('/moyasar', [SuperAdminMoyasarController::class, 'index'])->name('super-admin.moyasar.index');
    Route::post('/moyasar', [SuperAdminMoyasarController::class, 'store'])->name('super-admin.moyasar.store');
});
```

## نقاط الفحص الحرجة:
- ✅ السوبر أدمن يدخل مفاتيح API
- ✅ التاجر يختار البيئة فقط
- ✅ النظام يستخدم المفاتيح الصحيحة حسب البيئة
- ✅ التشفير يعمل للمفاتيح السرية
- ✅ الدفع يعمل بنجاح