# Table Reservation System - Translation Guide

## Translation Files Created

### English Translations
- **File**: `lang/table-reservation-en.json`
- **Language Code**: `en`

### Arabic Translations  
- **File**: `lang/table-reservation-ar.json`
- **Language Code**: `ar`

---

## How to Use Translations

### Method 1: Merge with Existing Language Files

Copy the contents from `table-reservation-en.json` and `table-reservation-ar.json` into your existing `lang/en.json` and `lang/ar.json` files.

**Example for English (`lang/en.json`):**
```json
{
    "auth.failed": "These credentials do not match our records.",
    ...existing translations...,
    
    "Tables": "Tables",
    "Products": "Products",
    "Add Table": "Add Table",
    ...table reservation translations...
}
```

**Example for Arabic (`lang/ar.json`):**
```json
{
    "auth.failed": "بيانات الاعتماد هذه غير متطابقة مع سجلاتنا.",
    ...existing translations...,
    
    "Tables": "الطاولات",
    "Products": "المنتجات",
    "Add Table": "إضافة طاولة",
    ...table reservation translations...
}
```

---

## Method 2: Keep Separate Files (Recommended)

Keep the translation files separate and load them in your application.

### Laravel Configuration

Add to `config/app.php`:
```php
'locale' => 'en', // or 'ar' for Arabic
'fallback_locale' => 'en',
```

### Usage in Blade Templates

All strings are already wrapped with `__()` helper:
```blade
{{ __('Tables') }}
{{ __('Add Table') }}
{{ __('Customer Name') }}
```

### Usage in JavaScript

For JavaScript strings, you can use:
```javascript
const translations = {
    en: @json(__('table-reservation-en')),
    ar: @json(__('table-reservation-ar'))
};
```

---

## Complete Translation List

### Navigation & Buttons
- Tables / الطاولات
- Products / المنتجات
- Add Table / إضافة طاولة
- Manage Tables / إدارة الطاولات
- Make Reservation / إنشاء حجز
- Manage Reservations / إدارة الحجوزات
- Manage Orders / إدارة الطلبات

### Table Status
- Free / متاح (🟢)
- Utilized / مشغول (🔴)
- Reserved / محجوز (🟡)

### Floor Plan Areas
- Entrance / المدخل
- Bar Area / منطقة البار
- Toilets / دورات المياه

### Order Management
- Table Order / طلب الطاولة
- Customer Name / اسم العميل
- Number of Guests / عدد الضيوف
- Order Items / عناصر الطلب
- Special Notes / ملاحظات خاصة
- Order Time / وقت الطلب
- Complete Order / إتمام الطلب
- Save Order / حفظ الطلب

### Reservation Management
- Reservation Details / تفاصيل الحجز
- Reservation Date / تاريخ الحجز
- Reservation Time / وقت الحجز
- Phone Number / رقم الهاتف
- Confirm Reservation / تأكيد الحجز
- Guest Arrived / وصول الضيف
- Cancel Reservation / إلغاء الحجز

### Table Creation
- Table Number / رقم الطاولة
- Number of Chairs / عدد الكراسي
- Table Type / نوع الطاولة
- Circle / دائرية
- Rounded / مستديرة
- Rectangle / مستطيلة
- Create Table / إنشاء طاولة

### Table Rotation
- Rotate 90° / تدوير 90°
- Reset Rotation / إعادة تعيين التدوير
- Current / الحالي

### Common Actions
- Cancel / إلغاء
- Close / إغلاق
- Delete / حذف
- Back / رجوع
- Actions / الإجراءات
- Status / الحالة
- Type / النوع

---

## Testing Translations

### Switch Language in Laravel

**In Controller:**
```php
App::setLocale('ar'); // Switch to Arabic
App::setLocale('en'); // Switch to English
```

**In Middleware:**
```php
public function handle($request, Closure $next)
{
    $locale = $request->user()->language ?? 'en';
    App::setLocale($locale);
    return $next($request);
}
```

**In Blade:**
```blade
@php
    App::setLocale('ar');
@endphp
```

---

## Adding New Translations

When adding new features, add translations to both files:

**English (`table-reservation-en.json`):**
```json
{
    "New Feature": "New Feature"
}
```

**Arabic (`table-reservation-ar.json`):**
```json
{
    "New Feature": "ميزة جديدة"
}
```

---

## RTL Support for Arabic

Add to your CSS for Arabic language:
```css
[dir="rtl"] .restaurant-floor-plan {
    direction: rtl;
}

[dir="rtl"] .table-name {
    text-align: center;
}
```

Set HTML direction based on language:
```blade
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
```

---

## Done!

All table reservation system strings are now translatable in both English and Arabic.
