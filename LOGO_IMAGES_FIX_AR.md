# إصلاح مشكلة عدم ظهور الصور واللوجوهات

## المشكلة
بعض اللوجوهات والصور لا تظهر في النظام لأن المسارات المحفوظة في قاعدة البيانات تشير إلى ملفات غير موجودة.

## التشخيص
تم فحص قاعدة البيانات ووجدنا أن جدول `options` يحتوي على المسارات التالية:

```json
{
    "logo": "uploads/26/01/1768296987-928.PNG",
    "favicon": "uploads/24/06/1740222466-64.png",
    "common_header_logo": "uploads/24/06/1717409713-716.png",
    "footer_logo": "uploads/24/06/1717409644-61.png",
    "admin_logo": "uploads/24/06/1717409871-257.png",
    "login_page_logo": "uploads/25/01/1738128807-906.svg",
    "login_page_image": "uploads/25/01/1738128807-720.svg"
}
```

**المشكلة:** هذه الملفات غير موجودة في مجلد `public/uploads/`

## الحل

### الخيار 1: رفع الصور من جديد (الحل الموصى به)
1. اذهب إلى لوحة التحكم الإدارية
2. اذهب إلى **Settings** > **General Settings**
3. قم برفع الصور واللوجوهات من جديد:
   - Logo (الشعار الرئيسي)
   - Admin Logo (شعار لوحة التحكم)
   - Common Header Logo (شعار الهيدر المشترك)
   - Footer Logo (شعار الفوتر)
   - Favicon (أيقونة المتصفح)
   - Login Page Logo (شعار صفحة تسجيل الدخول)
   - Login Page Image (صورة صفحة تسجيل الدخول)

### الخيار 2: إعادة تعيين المسارات إلى القيم الافتراضية
إذا كنت تريد استخدام الصور الافتراضية، قم بتشغيل هذا الأمر:

```bash
php artisan tinker
```

ثم نفذ:

```php
$option = App\Models\Option::where('key', 'general')->first();
$value = $option->value;
$value['logo'] = 'assets/images/Logo.png';
$value['admin_logo'] = 'assets/images/Logo.png';
$value['common_header_logo'] = 'assets/images/Logo.png';
$value['footer_logo'] = 'assets/images/Logo.png';
$value['favicon'] = 'assets/images/favicon.ico';
$value['login_page_logo'] = 'assets/images/Logo.png';
$value['login_page_image'] = 'assets/images/login.png';
$option->value = $value;
$option->save();
exit
```

### الخيار 3: نسخ الصور من backup إذا كان موجود
إذا كان لديك نسخة احتياطية من مجلد `uploads/`، قم بنسخها إلى:
```
public/uploads/
```

## التحقق من الإصلاح

بعد تطبيق أي من الحلول أعلاه:

1. امسح الكاش:
```bash
php artisan cache:clear
php artisan view:clear
```

2. أعد تحميل الصفحة في المتصفح (Ctrl+F5)

3. تحقق من ظهور اللوجوهات في:
   - الصفحة الرئيسية
   - لوحة التحكم
   - الهيدر والفوتر
   - صفحة تسجيل الدخول

## ملاحظات إضافية

### أيقونات الفئات (Categories)
إذا كانت أيقونات الفئات لا تظهر أيضاً، تحقق من:
1. جدول `categories` في قاعدة البيانات
2. عمود `icon` يحتوي على مسارات صحيحة
3. الملفات موجودة في المسارات المحددة

### الصور في الفواتير
إذا كانت اللوجوهات لا تظهر في الفواتير:
1. تحقق من إعدادات الأعمال (Business Settings)
2. تأكد من رفع `invoice_logo` أو `logo` في إعدادات كل تاجر
3. المسار الافتراضي للفواتير: `assets/images/default.svg`

## الملفات الافتراضية المتوفرة

النظام يحتوي على هذه الصور الافتراضية:
- `public/assets/images/Logo.png` ✅
- `public/assets/images/default.svg` ✅
- `public/assets/images/favicon.ico` ✅
- `public/assets/images/login.png` (تحقق من وجودها)

## الخلاصة

المشكلة الرئيسية هي أن المسارات في قاعدة البيانات تشير إلى ملفات محذوفة أو غير موجودة. الحل الأفضل هو رفع الصور من جديد عبر لوحة التحكم، أو إعادة تعيين المسارات إلى القيم الافتراضية.
