# Routes المعطلة في الـ Sidebar

## ملخص سريع:
- **إجمالي الـ Routes المعطلة:** 30 route
- **الأقسام المعطلة بالكامل:** 3 أقسام (Finance & Accounts, Sale Commission, Party Reports)
- **Routes معطلة في أقسام موجودة:** 27 route

---

## 1️⃣ Products Section

### ❌ Combo Products
- **Route:** `business.combo-products.index`
- **الموقع:** Products → Combo Products
- **السبب:** Feature غير مطور

---

## 2️⃣ Due List Section

### ❌ Guest Due (Walk-in Dues)
- **Routes:**
  - `business.walk-dues.index`
  - `business.collect.walk.dues`
- **الموقع:** Due List → Guest Due
- **السبب:** Feature غير مطور

---

## 3️⃣ Finance & Accounts Section (القسم كامل معطل)

### ❌ Bank Account
- **Route:** `business.banks.index`

### ❌ Cash In Hand
- **Route:** `business.cashes.index`

### ❌ Cheques
- **Route:** `business.cheques.index`

### ❌ Profit & Loss
- **Route:** `business.loss-profit-history.index`

### ❌ Transactions
- **Route:** `business.transactions.index`

### ❌ Day Book
- **Route:** `business.day-book-reports.index`

### ❌ Cash Flow
- **Route:** `business.cash-flow-reports.index`

### ❌ Balance Sheet
- **Route:** `business.balance-sheet.index`

**ملاحظة:** Income, Income Category, Expenses, Expense Category موجودين وشغالين (مش معطلين)

**إجمالي Routes معطلة في Finance & Accounts:** 8 routes

---

## 4️⃣ Sale Commission Section (القسم كامل معطل)

### ❌ Set Commissions
- **Route:** `business.commissions.index`

### ❌ Sale Commission
- **Route:** `business.sale-commissions.index`

**إجمالي Routes معطلة في Sale Commission:** 2 routes

---

## 5️⃣ Reports Section

### ❌ Product Wise Profit & Loss
- **Route:** `business.product-loss-profit-reports.index`

### ❌ Top 5 Customer
- **Route:** `business.top-customer-reports.index`

### ❌ Top 5 Supplier
- **Route:** `business.top-supplier-reports.index`

### ❌ Top 5 Product
- **Route:** `business.top-product-reports.index`

### ❌ Combo Product Report
- **Route:** `business.combo-product-reports.index`

### ❌ Discount Product
- **Route:** `business.discount-product-reports.index`

### ❌ Product Wise Purchase
- **Route:** `business.product-purchase-reports.index`

### ❌ Product Wise Sale
- **Route:** `business.product-sale-reports.index`

### ❌ Loss Profit History
- **Route:** `business.loss-profit-history-reports.index`

### ❌ Product Sale History
- **Routes:**
  - `business.product-sale-history-reports.index`
  - `business.product-sale-history-reports.show`

### ❌ Product Purchase History
- **Routes:**
  - `business.product-purchase-history-reports.index`
  - `business.product-purchase-history-reports.show`

**إجمالي Routes معطلة في Reports:** 13 routes

---

## 6️⃣ Party Reports Section (القسم كامل معطل)

### ❌ Customer Ledger
- **Routes:**
  - `business.customer-ledger.index`
  - `business.customer-ledger.show`

### ❌ Supplier Ledger
- **Routes:**
  - `business.supplier-ledger.index`
  - `business.supplier-ledger.show`

### ❌ Party Profit & Loss
- **Route:** `business.party-loss-profit.index`

### ❌ Top 5 Customer (في Party Reports)
- **Route:** `business.top-customers.index`

### ❌ Top 5 Supplier (في Party Reports)
- **Route:** `business.top-suppliers.index`

**إجمالي Routes معطلة في Party Reports:** 7 routes

---

## 📊 إحصائيات:

### حسب القسم:
| القسم | عدد Routes المعطلة | الحالة |
|------|-------------------|--------|
| Products | 1 | جزئي |
| Due List | 2 | جزئي |
| Finance & Accounts | 8 | كامل ❌ |
| Sale Commission | 2 | كامل ❌ |
| Reports | 13 | جزئي |
| Party Reports | 7 | كامل ❌ |
| **الإجمالي** | **33** | - |

### حسب النوع:
- **أقسام معطلة بالكامل:** 3 (Finance & Accounts, Sale Commission, Party Reports)
- **Routes معطلة في أقسام شغالة:** 16 route
- **إجمالي Routes معطلة:** 33 route

---

## ✅ Routes الشغالة:

### في Finance & Accounts (شغالة):
- ✅ Income (`business.incomes.index`)
- ✅ Income Category (`business.income-categories.index`)
- ✅ Expenses (`business.expenses.index`)
- ✅ Expense Category (`business.expense-categories.index`)

### في Reports (شغالة):
- ✅ Sale Reports
- ✅ Sale Return Reports
- ✅ Purchase Reports
- ✅ Purchase Return Reports
- ✅ Tax Report (VAT)
- ✅ Income Reports
- ✅ Expense Reports
- ✅ Current Stock
- ✅ Customer Due
- ✅ Supplier Due
- ✅ Bill Wise Profit & Loss
- ✅ Due Transaction
- ✅ Subscription Report
- ✅ Expired Product

---

## 🔧 كيفية تفعيل الـ Routes المعطلة:

### الخطوات المطلوبة:

1. **إنشاء Controllers:**
```bash
php artisan make:controller Business/BankController
php artisan make:controller Business/CashController
# ... إلخ
```

2. **إنشاء Routes في `Modules/Business/routes/web.php`:**
```php
Route::resource('banks', BankController::class);
Route::resource('cashes', CashController::class);
// ... إلخ
```

3. **إنشاء Views:**
```
Modules/Business/resources/views/banks/index.blade.php
Modules/Business/resources/views/cashes/index.blade.php
// ... إلخ
```

4. **إنشاء Migrations (إذا لزم الأمر):**
```bash
php artisan make:migration create_banks_table
php artisan make:migration create_cashes_table
# ... إلخ
```

5. **إزالة الـ Comment من الـ Sidebar:**
```php
// من:
{{-- <li><a href="{{ route('business.banks.index') }}">Bank Account</a></li> --}}

// إلى:
<li><a href="{{ route('business.banks.index') }}">Bank Account</a></li>
```

---

## 📝 ملاحظات:

1. **الـ Routes المعطلة هي features اختيارية** - النظام يعمل بدونها
2. **بعض الـ Routes قد تحتاج addons إضافية** غير مثبتة حالياً
3. **الأولوية للـ Routes الأساسية** اللي شغالة حالياً
4. **يمكن تطوير الـ Features المعطلة تدريجياً** حسب الحاجة

---

تم التحديث: 2024
