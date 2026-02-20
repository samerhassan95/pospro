# كيفية تفعيل ميزة البنوك والكاش في المشروع الثاني

## المشكلة
عند نسخ السايدبار من المشروع الأول للمشروع الثاني، ظهر خطأ:
```
Route [business.banks.index] not defined
```

## السبب
المشروع الثاني لا يحتوي على الراوتس الخاصة بـ:
- Banks (البنوك)
- Cashes (الكاش)
- Cheques (الشيكات)
- Bank Transactions (معاملات البنوك)
- Cash Flow Reports (تقارير التدفق النقدي)
- Balance Sheet Reports (الميزانية العمومية)
- Bill Wise Profit Reports (أرباح الفواتير)

هذه الميزات موجودة في المشروع الأول لكن غير موجودة في المشروع الثاني.

---

## الحل السريع (الموصى به)

### الخطوة 1: افتح ملف السايدبار في المشروع الثاني
```
resources/views/layouts/business/partials/side-bar.blade.php
```

### الخطوة 2: ابحث عن قسم Finance & Accounts المعلق

ستجد هذا القسم معلق عليه (بين `{{--` و `--}}`):

```php
{{-- Finance & Accounts - Banks section disabled (routes not available)
@usercan('banks.read')
<li class="dropdown">
    <a href="#">
        <span class="sidebar-icon">
            <img src="{{ asset('assets/images/sidebar/cash_and_bank.svg') }}">
        </span>
        {{ __('Finance & Accounts') }}</a>
    <ul>
        <li><a href="#">{{ __('Bank Account') }}</a></li>
        <li><a href="#">{{ __('Cash In Hand') }}</a></li>
        <li><a href="#">{{ __('Cheques') }}</a></li>
        <li><a href="#">{{ __('Bank Transactions') }}</a></li>
        @endusercan
        @usercan('banks.read')
        <li><a class="{{ Request::routeIs('business.cheques.index') ? 'active' : '' }}" href="{{ route('business.cheques.index') }}">{{ __('Cheques') }}</a></li>
        @endusercan
        @usercan('banks.read')
        <li><a class="{{ Request::routeIs('business.bank-transactions.index') ? 'active' : '' }}" href="{{ route('business.bank-transactions.index') }}">{{ __('Bank Transactions') }}</a></li>
        @endusercan
        
        @usercan('reports.read')
        <li><a class="{{ Request::routeIs('business.cash-flow-reports.index') ? 'active' : '' }}" href="{{ route('business.cash-flow-reports.index') }}">{{ __('Cash Flow Report') }}</a></li>
        @endusercan
        @usercan('reports.read')
        <li><a class="{{ Request::routeIs('business.balance-sheet-reports.index') ? 'active' : '' }}" href="{{ route('business.balance-sheet-reports.index') }}">{{ __('Balance Sheet') }}</a></li>
        @endusercan
        @usercan('reports.read')
        <li><a class="{{ Request::routeIs('business.bill-wise-profit-reports.index') ? 'active' : '' }}" href="{{ route('business.bill-wise-profit-reports.index') }}">{{ __('Bill Wise Profit') }}</a></li>
        @endusercan
    </ul>
</li>
@endusercan
--}}
```

### الخطوة 3: احذف هذا القسم المعلق بالكامل

احذف من `{{--` إلى `--}}` بالكامل.

### الخطوة 4: احفظ الملف

الآن السايدبار سيعمل بدون أخطاء!

---

## لماذا هذا الحل؟

القسم المعلق يحتوي على راوتس غير موجودة في المشروع الثاني:
- `business.banks.index`
- `business.cashes.index`
- `business.cheques.index`
- `business.bank-transactions.index`
- `business.cash-flow-reports.index`
- `business.balance-sheet-reports.index`
- `business.bill-wise-profit-reports.index`

هذه الراوتس موجودة في ملف `Modules/Business/routes/web.php` في المشروع الأول فقط:

```php
// Finance & Accounts
Route::resource('banks', Business\AcnooBankController::class);
Route::resource('cashes', Business\AcnooCashController::class);
Route::resource('cheques', Business\AcnooChequeController::class);
Route::resource('bank-transactions', Business\AcnooBankTransactionController::class);
Route::resource('cash-flow-reports', Business\AcnooCashFlowReportController::class);
Route::resource('balance-sheet-reports', Business\AcnooBalanceSheetReportController::class);
Route::resource('bill-wise-profit-reports', Business\AcnooBillWiseProfitReportController::class);
```

---

## إذا كنت تريد تفعيل هذه الميزات في المشروع الثاني

### ستحتاج إلى:

#### 1. نسخ الكونترولرز من المشروع الأول
```
Modules/Business/App/Http/Controllers/AcnooBankController.php
Modules/Business/App/Http/Controllers/AcnooCashController.php
Modules/Business/App/Http/Controllers/AcnooChequeController.php
Modules/Business/App/Http/Controllers/AcnooBankTransactionController.php
Modules/Business/App/Http/Controllers/AcnooCashFlowReportController.php
Modules/Business/App/Http/Controllers/AcnooBalanceSheetReportController.php
Modules/Business/App/Http/Controllers/AcnooBillWiseProfitReportController.php
```

#### 2. نسخ الـ Views من المشروع الأول
```
Modules/Business/resources/views/banks/
Modules/Business/resources/views/cashes/
Modules/Business/resources/views/cheques/
Modules/Business/resources/views/bank-transactions/
Modules/Business/resources/views/cash-flow-reports/
Modules/Business/resources/views/balance-sheet-reports/
Modules/Business/resources/views/bill-wise-profit-reports/
```

#### 3. نسخ الـ Models من المشروع الأول (إذا كانت موجودة)
```
app/Models/Bank.php
app/Models/Cash.php
app/Models/Cheque.php
app/Models/BankTransaction.php
```

#### 4. نسخ الـ Migrations من المشروع الأول
```
database/migrations/*_create_banks_table.php
database/migrations/*_create_cashes_table.php
database/migrations/*_create_cheques_table.php
database/migrations/*_create_bank_transactions_table.php
```

#### 5. تشغيل الـ Migrations
```bash
php artisan migrate
```

#### 6. إضافة الراوتس في ملف `Modules/Business/routes/web.php`

أضف هذا الكود بعد السطر 30 تقريباً (بعد قسم Dashboard):

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

#### 7. إضافة الـ Permissions في Seeder

أضف الـ permissions في `database/seeders/PermissionSeeder.php`:

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

#### 8. تشغيل الـ Seeder
```bash
php artisan db:seed --class=PermissionSeeder
```

#### 9. إزالة التعليق من قسم Finance & Accounts في السايدبار

---

## التوصية النهائية

**استخدم الحل السريع** (احذف القسم المعلق من السايدبار) لأن:
- أسرع وأسهل
- لا يحتاج نسخ ملفات كثيرة
- لا يحتاج تعديلات في الداتابيز
- المشروع الثاني يعمل بدون مشاكل

إذا كنت تحتاج ميزة البنوك والكاش في المستقبل، يمكنك تطبيق الخطوات الكاملة لاحقاً.

---

**تاريخ الإنشاء:** 16 فبراير 2026
