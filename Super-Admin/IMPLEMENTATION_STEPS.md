# سجل خطوات تفعيل الـ Routes المعطلة

هذا الملف يحتوي على جميع التعديلات التي تمت لتفعيل الخصائص المعطلة في النظام، لسهولة نقلها إلى نسخ أخرى.

---

## 📅 التاريخ: 16 فبراير 2026

### 1️⃣ قسم: Finance & Accounts (البنوك والحسابات)

#### الملاحظة التقنية:

النظام يستخدم موديل `PaymentType` لتخزين البنوك والخزن والشيكات. الراوتس المعطلة كانت تبحث عن موديلات غير موجودة، لذا سنقوم بربطها بالـ `PaymentType` مع عمل الفلاتر اللازمة.

#### المسارات والملفات المُعدلة:

1. **الـ Controllers:**
    - `Modules/Business/App/Http/Controllers/AcnooBankController.php`: (تعديل لربطه بـ `PaymentType`).
    - `Modules/Business/App/Http/Controllers/AcnooCashController.php`: (جديد - للتحكم في الخزينة).
    - `Modules/Business/App/Http/Controllers/AcnooChequeController.php`: (جديد - للتحكم في الشيكات).
    - `Modules/Business/App/Http/Controllers/AcnooBankTransactionController.php`: (جديد - لإدارة التحويلات والمعاملات).

2. **الـ Routes:**
    - `Modules/Business/routes/web.php`: تم إضافة 16 راوت جديد تحت قسم `Finance & Accounts`.

3. **الـ Views (جديد):**
    - `Modules/Business/resources/views/banks/`: (index, datas, create, edit).
    - `Modules/Business/resources/views/cashes/`: (index, datas, create, edit).
    - `Modules/Business/resources/views/cheques/`: (index, datas, create, edit).
    - `Modules/Business/resources/views/bank-transactions/`: (index, datas, create).

---

### 2️⃣ قسم: Financial Reports (التقارير المالية)

#### الملاحظة التقنية:

تم إنشاء Controllers جديدة للتقارير المالية المتقدمة (Cash Flow, Balance Sheet, Bill Wise Profit) مع استخدام `DateFilterTrait` للفلترة حسب التاريخ.

#### المسارات والملفات المُعدلة:

1. **الـ Controllers:**
    - `Modules/Business/App/Http/Controllers/AcnooCashFlowReportController.php`: (جديد - تقرير التدفق النقدي).
    - `Modules/Business/App/Http/Controllers/AcnooBalanceSheetReportController.php`: (جديد - تقرير الميزانية العمومية).
    - `Modules/Business/App/Http/Controllers/AcnooBillWiseProfitReportController.php`: (جديد - تقرير الأرباح حسب الفاتورة).

2. **الـ Routes:**
    - `Modules/Business/routes/web.php`: تم إضافة 15 راوت جديد تحت قسم `Financial Reports`.

3. **الـ Views (تم إنشاؤها):**
    - `Modules/Business/resources/views/reports/cash-flow/`: (index, datas) ✅
    - `Modules/Business/resources/views/reports/balance-sheet/`: (index, datas) ✅
    - `Modules/Business/resources/views/reports/bill-wise-profit/`: (index, datas, show) ✅

---

## 📊 ملخص شامل للتعديلات:

| القسم                  | Controllers | Routes      | Views      |
| ---------------------- | ----------- | ----------- | ---------- |
| **Finance & Accounts** | 4 ملفات     | 16 راوت     | 15 ملف     |
| **Financial Reports**  | 3 ملفات     | 15 راوت     | 7 ملفات    |
| **المجموع**            | **7 ملفات** | **31 راوت** | **22 ملف** |

---

### 3️⃣ الخطوة التالية: تفعيل الـ Routes في الـ Sidebar

- فك الـ Comment عن القوائم المعطلة في `resources/views/layouts/business/partials/side-bar.blade.php`.
- إضافة الأيقونات المناسبة لكل قسم.
- التأكد من الصلاحيات (Permissions) لكل راوت.
