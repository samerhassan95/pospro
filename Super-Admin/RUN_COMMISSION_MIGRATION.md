# ✅ تشغيل Migration للـ Commission في المشروع 2

## المشكلة
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'commission_type' in 'where clause'
```

## السبب
جدول `users` في المشروع 2 مفيهوش الأعمدة `commission_type` و `commission_value`

---

## الحل: نسخ وتشغيل الـ Migration

### الطريقة 1: نسخ الملف من المشروع 1 ✅ (الأسهل)

**انسخ الملف ده:**
```
من: المشروع_الأول/database/migrations/2026_02_16_203141_add_commission_fields_to_users_table.php
إلى: المشروع_الثاني/database/migrations/2026_02_16_203141_add_commission_fields_to_users_table.php
```

**بعدين شغل الـ migration:**
```bash
php artisan migrate --path=database/migrations/2026_02_16_203141_add_commission_fields_to_users_table.php
```

---

### الطريقة 2: إنشاء الملف يدوياً

إذا مش عايز تنسخ، اعمل الملف ده في المشروع 2:

**المسار:** `database/migrations/2026_02_16_203141_add_commission_fields_to_users_table.php`

**الكود:**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('commission_type')->nullable()->after('visibility'); // percentage or fixed
            $table->decimal('commission_value', 10, 2)->nullable()->after('commission_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['commission_type', 'commission_value']);
        });
    }
};
```

**بعدين شغل:**
```bash
php artisan migrate --path=database/migrations/2026_02_16_203141_add_commission_fields_to_users_table.php
```

---

### الطريقة 3: إضافة الأعمدة مباشرة بـ SQL (الأسرع)

إذا عايز حل سريع، نفذ الـ SQL ده في قاعدة البيانات:

```sql
ALTER TABLE `users` 
ADD COLUMN `commission_type` VARCHAR(255) NULL AFTER `visibility`,
ADD COLUMN `commission_value` DECIMAL(10,2) NULL AFTER `commission_type`;
```

---

## بعد التنفيذ

جرب الراوت تاني:
```
business/sale-commissions
```

المفروض يشتغل دلوقتي! ✅

---

## ملاحظة مهمة

لو عندك مشاكل تانية في الـ migrations، ممكن تكون محتاج تنسخ migrations تانية من المشروع 1:

**Migrations مهمة للنسخ:**
1. ✅ `2026_02_16_203141_add_commission_fields_to_users_table.php` (Commission)
2. ✅ `2025_08_18_162915_add_new_fields_to_multiple_table.php` (Branch ID)

---

**تاريخ الإنشاء:** 17 فبراير 2026
