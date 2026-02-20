# نسخ ميزة البنوك للمشروع الثاني

## الملفات المطلوب نسخها من المشروع الأول للمشروع الثاني

### 1. Controllers (7 ملفات)
انسخ من المشروع الأول إلى المشروع الثاني:

```
من: المشروع الأول/Modules/Business/App/Http/Controllers/
إلى: المشروع الثاني/Modules/Business/App/Http/Controllers/

الملفات:
✓ AcnooBankController.php
✓ AcnooCashController.php
✓ AcnooChequeController.php
✓ AcnooBankTransactionController.php
✓ AcnooCashFlowReportController.php
✓ AcnooBalanceSheetReportController.php
✓ AcnooBillWiseProfitReportController.php
```

### 2. Views (7 مجلدات)
انسخ المجلدات كاملة:

```
من: المشروع الأول/Modules/Business/resources/views/
إلى: المشروع الثاني/Modules/Business/resources/views/

المجلدات:
✓ banks/
✓ cashes/
✓ cheques/
✓ bank-transactions/
✓ cash-flow-reports/
✓ balance-sheet-reports/
✓ bill-wise-profit-reports/
```

### 3. Models (إذا كانت موجودة)
ابحث في المشروع الأول عن:

```
من: المشروع الأول/app/Models/
إلى: المشروع الثاني/app/Models/

الملفات (إذا موجودة):
✓ Bank.php
✓ Cash.php
✓ Cheque.php
✓ BankTransaction.php
```

### 4. Migrations
ابحث في المشروع الأول عن الـ migrations:

```
من: المشروع الأول/database/migrations/
إلى: المشروع الثاني/database/migrations/

ابحث عن ملفات تحتوي على:
✓ create_banks_table
✓ create_cashes_table
✓ create_cheques_table
✓ create_bank_transactions_table
```

### 5. إضافة الراوتس
افتح ملف: `المشروع الثاني/Modules/Business/routes/web.php`

أضف هذا الكود بعد السطر 30 (بعد قسم Dashboard):

```php
// Finance & Accounts
Route::resource('banks', Business\AcnooBankController::class);
Route::post('banks/filter', [Business\AcnooBankController::class, 'acnooFilter'])->name('banks.filter');
Route::post('banks/status/{id}', [Business\AcnooBankController::class, 'status'])->name('banks.status');
Route::post('banks/delete-all', [Business\AcnooBankController::class, 'deleteAll'])->name('banks.delete-all');

Route::resource('cashes', Business\AcnooCashController::class);
Route::post('cashes/filter', [Business\AcnooCashController::class, 'acnooFilter'])->name('cashes.filter');
Route::post('cashes/status/{id}', [Business\AcnooCashController::class, 'status'])->name('cashes.status');
Route::post('cashes/delete-all', [Business\AcnooCashController::class, 'deleteAll'])->name('cashes.delete-all');

Route::resource('cheques', Business\AcnooChequeController::class);
Route::post('cheques/filter', [Business\AcnooChequeController::class, 'acnooFilter'])->name('cheques.filter');
Route::post('cheques/status/{id}', [Business\AcnooChequeController::class, 'status'])->name('cheques.status');
Route::post('cheques/delete-all', [Business\AcnooChequeController::class, 'deleteAll'])->name('cheques.delete-all');

Route::resource('bank-transactions', Business\AcnooBankTransactionController::class);
Route::post('bank-transactions/filter', [Business\AcnooBankTransactionController::class, 'acnooFilter'])->name('bank-transactions.filter');

// Financial Reports
Route::resource('cash-flow-reports', Business\AcnooCashFlowReportController::class)->only('index');
Route::post('cash-flow-reports/filter', [Business\AcnooCashFlowReportController::class, 'acnooFilter'])->name('cash-flow-reports.filter');
Route::get('cash-flow-reports/pdf', [Business\AcnooCashFlowReportController::class, 'generatePDF'])->name('cash-flow-reports.pdf');
Route::get('cash-flow-reports/excel', [Business\AcnooCashFlowReportController::class, 'exportExcel'])->name('cash-flow-reports.excel');
Route::get('cash-flow-reports/csv', [Business\AcnooCashFlowReportController::class, 'exportCsv'])->name('cash-flow-reports.csv');

Route::resource('balance-sheet-reports', Business\AcnooBalanceSheetReportController::class)->only('index');
Route::post('balance-sheet-reports/filter', [Business\AcnooBalanceSheetReportController::class, 'acnooFilter'])->name('balance-sheet-reports.filter');
Route::get('balance-sheet-reports/pdf', [Business\AcnooBalanceSheetReportController::class, 'generatePDF'])->name('balance-sheet-reports.pdf');
Route::get('balance-sheet-reports/excel', [Business\AcnooBalanceSheetReportController::class, 'exportExcel'])->name('balance-sheet-reports.excel');
Route::get('balance-sheet-reports/csv', [Business\AcnooBalanceSheetReportController::class, 'exportCsv'])->name('balance-sheet-reports.csv');

Route::resource('bill-wise-profit-reports', Business\AcnooBillWiseProfitReportController::class)->only('index', 'show');
Route::post('bill-wise-profit-reports/filter', [Business\AcnooBillWiseProfitReportController::class, 'acnooFilter'])->name('bill-wise-profit-reports.filter');
Route::get('bill-wise-profit-reports/pdf', [Business\AcnooBillWiseProfitReportController::class, 'generatePDF'])->name('bill-wise-profit-reports.pdf');
Route::get('bill-wise-profit-reports/excel', [Business\AcnooBillWiseProfitReportController::class, 'exportExcel'])->name('bill-wise-profit-reports.excel');
Route::get('bill-wise-profit-reports/csv', [Business\AcnooBillWiseProfitReportController::class, 'exportCsv'])->name('bill-wise-profit-reports.csv');
```

### 6. تشغيل الـ Migrations في المشروع الثاني
بعد نسخ ملفات الـ migrations:

```bash
cd المشروع_الثاني
php artisan migrate
```

### 7. إضافة الـ Permissions
افتح: `المشروع الثاني/database/seeders/PermissionSeeder.php`

أضف هذه الـ permissions في الـ array:

```php
'banks.read',
'banks.create',
'banks.update',
'banks.delete',
'cashes.read',
'cashes.create',
'cashes.update',
'cashes.delete',
'cheques.read',
'cheques.create',
'cheques.update',
'cheques.delete',
'bank-transactions.read',
'bank-transactions.create',
'bank-transactions.update',
'bank-transactions.delete',
'cash-flow-reports.read',
'balance-sheet-reports.read',
'bill-wise-profit-reports.read',
```

ثم شغل الـ seeder:

```bash
php artisan db:seed --class=PermissionSeeder
```

### 8. إزالة التعليق من السايدبار
افتح: `المشروع الثاني/resources/views/layouts/business/partials/side-bar.blade.php`

ابحث عن:
```php
{{-- Finance & Accounts - Banks section disabled (routes not available)
```

احذف `{{--` من البداية و `--}}` من النهاية لإزالة التعليق.

---

## الخطوات بالترتيب

1. ✅ انسخ الـ 7 Controllers
2. ✅ انسخ الـ 7 Views folders
3. ✅ انسخ الـ Models (إذا موجودة)
4. ✅ انسخ الـ Migrations
5. ✅ أضف الراوتس في web.php
6. ✅ شغل `php artisan migrate`
7. ✅ أضف الـ Permissions في PermissionSeeder
8. ✅ شغل `php artisan db:seed --class=PermissionSeeder`
9. ✅ احذف التعليق من السايدبار

---

## ملاحظات مهمة

- تأكد إن الـ namespace في الـ Controllers صحيح
- تأكد إن الـ use statements في الـ Controllers موجودة
- لو في أي dependencies تانية، انسخها كمان
- اعمل backup للمشروع الثاني قبل ما تبدأ

---

**تاريخ الإنشاء:** 16 فبراير 2026
