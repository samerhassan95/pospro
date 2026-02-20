# حل مشكلة الراوتس المفقودة في السايدبار

## المشكلة
عند نقل السايدبار للمشروع الجديد، ظهرت أخطاء:
```
Route [business.banks.index] not defined
```

## السبب
السايدبار يحتوي على راوتس لميزات غير موجودة في المشروع الجديد.

---

## الحل 1: إخفاء الأقسام المفقودة (الحل السريع)

### الخطوات

#### 1. افتح ملف السايدبار
```
resources/views/layouts/business/partials/side-bar.blade.php
```

#### 2. ابحث عن قسم Finance & Accounts
**ابحث عن:**
```php
@usercan('banks.read')
<li class="dropdown {{ Request::routeIs('business.banks.index', 'business.banks.create','business.cashes.index', 'business.cheques.index', 'business.bank-transactions.index', 'business.cash-flow-reports.index','business.balance-sheet-reports.index','business.bill-wise-profit-reports.index') ? 'active' : '' }}">
    <a href="#">
        <span class="sidebar-icon">
            <img src="{{ asset('assets/images/sidebar/finance.svg') }}">
        </span>
        {{ __('Finance & Accounts') }}</a>
    <ul>
        @usercan('banks.read')
        <li><a class="{{ Request::routeIs('business.banks.index') ? 'active' : '' }}" href="{{ route('business.banks.index') }}">{{ __('Bank Account') }}</a></li>
        @endusercan
        @usercan('banks.read')
        <li><a class="{{ Request::routeIs('business.cashes.index') ? 'active' : '' }}" href="{{ route('business.cashes.index') }}">{{ __('Cash In Hand') }}</a></li>
        @endusercan
        @usercan('banks.read')
        <li><a class="{{ Request::routeIs('business.cheques.index') ? 'active' : '' }}" href="{{ route('business.cheques.index') }}">{{ __('Cheques') }}</a></li>
        @endusercan
        @usercan('banks.read')
        <li><a class="{{ Request::routeIs('business.bank-transactions.index') ? 'active' : '' }}" href="{{ route('business.bank-transactions.index') }}">{{ __('Bank Transactions') }}</a></li>
        @endusercan
        <!-- المزيد من العناصر -->
    </ul>
</li>
@endusercan
```

#### 3. علق على القسم كامل أو احذفه
**استبدل بـ:**
```php
{{-- Finance & Accounts section - Not available in this version
@usercan('banks.read')
<li class="dropdown">
    ...
</li>
@endusercan
--}}
```

---

## الحل 2: استخدام Route::has() للتحقق (الحل الأفضل)

### الخطوات

#### 1. استبدل الراوتس المفقودة بفحص
**بدلاً من:**
```php
<li><a href="{{ route('business.banks.index') }}">{{ __('Bank Account') }}</a></li>
```

**استخدم:**
```php
@if(Route::has('business.banks.index'))
<li><a href="{{ route('business.banks.index') }}">{{ __('Bank Account') }}</a></li>
@endif
```

#### 2. طبق على جميع الراوتس المفقودة

**مثال كامل:**
```php
@usercan('banks.read')
<li class="dropdown">
    <a href="#">
        <span class="sidebar-icon">
            <img src="{{ asset('assets/images/sidebar/finance.svg') }}">
        </span>
        {{ __('Finance & Accounts') }}</a>
    <ul>
        @if(Route::has('business.banks.index'))
        <li><a href="{{ route('business.banks.index') }}">{{ __('Bank Account') }}</a></li>
        @endif
        
        @if(Route::has('business.cashes.index'))
        <li><a href="{{ route('business.cashes.index') }}">{{ __('Cash In Hand') }}</a></li>
        @endif
        
        @if(Route::has('business.cheques.index'))
        <li><a href="{{ route('business.cheques.index') }}">{{ __('Cheques') }}</a></li>
        @endif
        
        @if(Route::has('business.bank-transactions.index'))
        <li><a href="{{ route('business.bank-transactions.index') }}">{{ __('Bank Transactions') }}</a></li>
        @endif
    </ul>
</li>
@endusercan
```

---

## الحل 3: إنشاء سايدبار مخصص (الحل الموصى به)

### الخطوات

#### 1. احذف السايدبار القديم
```bash
rm resources/views/layouts/business/partials/side-bar.blade.php
```

#### 2. أنشئ سايدبار جديد يحتوي فقط على الميزات الموجودة

**الكود الأساسي:**
```php
<nav class="side-bar">
    <div class="side-bar-logo">
        <a href="{{ route('business.dashboard.index') }}">
            <img src="{{ asset(get_option('general')['admin_logo'] ?? 'assets/images/logo/backend_logo.png') }}" alt="Logo">
        </a>
        <button class="close-btn"><i class="fal fa-times"></i></button>
    </div>
    <div class="side-bar-manu">
        <ul>
            <!-- Dashboard -->
            @usercan('dashboard.read')
            <li class="{{ Request::routeIs('business.dashboard.index') ? 'active' : '' }}">
                <a href="{{ route('business.dashboard.index') }}">
                    <span class="sidebar-icon">
                        <img src="{{ asset('assets/images/sidebar/dashborad.svg') }}">
                    </span>
                    {{ __('Dashboard') }}
                </a>
            </li>
            @endusercan

            <!-- Sales -->
            @usercanany(['sales.read', 'sales.create'])
            <li class="dropdown">
                <a href="#">
                    <span class="sidebar-icon">
                        <img src="{{ asset('assets/images/sidebar/sales.svg') }}">
                    </span>
                    {{ __('Sales') }}
                </a>
                <ul>
                    @usercan('sales.create')
                    <li><a href="{{ route('business.sales.create') }}">{{ __('POS') }}</a></li>
                    @endusercan
                    @usercan('sales.read')
                    <li><a href="{{ route('business.sales.index') }}">{{ __('Sales List') }}</a></li>
                    @endusercan
                </ul>
            </li>
            @endusercanany

            <!-- Purchases -->
            @usercanany(['purchases.read'])
            <li class="dropdown">
                <a href="#">
                    <span class="sidebar-icon">
                        <img src="{{ asset('assets/images/sidebar/Purchase.svg') }}">
                    </span>
                    {{ __('Purchases') }}
                </a>
                <ul>
                    @usercan('purchases.create')
                    <li><a href="{{ route('business.purchases.create') }}">{{ __('Add Purchase') }}</a></li>
                    @endusercan
                    @usercan('purchases.read')
                    <li><a href="{{ route('business.purchases.index') }}">{{ __('Purchase List') }}</a></li>
                    @endusercan
                </ul>
            </li>
            @endusercanany

            <!-- Products -->
            @usercanany(['products.read'])
            <li class="dropdown">
                <a href="#">
                    <span class="sidebar-icon">
                        <img src="{{ asset('assets/images/sidebar/product.svg') }}">
                    </span>
                    {{ __('Products') }}
                </a>
                <ul>
                    @usercan('products.read')
                    <li><a href="{{ route('business.products.index') }}">{{ __('All Product') }}</a></li>
                    @endusercan
                    @usercan('products.create')
                    <li><a href="{{ route('business.products.create') }}">{{ __('Add Product') }}</a></li>
                    @endusercan
                    @usercan('products.read')
                    <li><a href="{{ route('business.combo-products.index') }}">{{ __('Combo Products') }}</a></li>
                    @endusercan
                    @usercan('categories.read')
                    <li><a href="{{ route('business.categories.index') }}">{{ __('Category') }}</a></li>
                    @endusercan
                    @usercan('brands.read')
                    <li><a href="{{ route('business.brands.index') }}">{{ __('Brand') }}</a></li>
                    @endusercan
                    @usercan('units.read')
                    <li><a href="{{ route('business.units.index') }}">{{ __('Unit') }}</a></li>
                    @endusercan
                </ul>
            </li>
            @endusercanany

            <!-- Parties (Customers & Suppliers) -->
            @usercanany(['parties.read'])
            <li class="dropdown">
                <a href="#">
                    <span class="sidebar-icon">
                        <img src="{{ asset('assets/images/sidebar/parties.svg') }}">
                    </span>
                    {{ __('Parties') }}
                </a>
                <ul>
                    @usercan('parties.read')
                    <li><a href="{{ route('business.parties.index', ['type' => 'customer']) }}">{{ __('Customers') }}</a></li>
                    <li><a href="{{ route('business.parties.index', ['type' => 'supplier']) }}">{{ __('Suppliers') }}</a></li>
                    @endusercan
                </ul>
            </li>
            @endusercanany

            <!-- Expenses & Income -->
            @usercanany(['expenses.read', 'incomes.read'])
            <li class="dropdown">
                <a href="#">
                    <span class="sidebar-icon">
                        <img src="{{ asset('assets/images/sidebar/expense.svg') }}">
                    </span>
                    {{ __('Expenses & Income') }}
                </a>
                <ul>
                    @usercan('expenses.read')
                    <li><a href="{{ route('business.expenses.index') }}">{{ __('Expenses') }}</a></li>
                    @endusercan
                    @usercan('incomes.read')
                    <li><a href="{{ route('business.incomes.index') }}">{{ __('Income') }}</a></li>
                    @endusercan
                </ul>
            </li>
            @endusercanany

            <!-- Reports -->
            @usercanany(['reports.read'])
            <li class="dropdown">
                <a href="#">
                    <span class="sidebar-icon">
                        <img src="{{ asset('assets/images/sidebar/reports.svg') }}">
                    </span>
                    {{ __('Reports') }}
                </a>
                <ul>
                    <!-- Party Reports -->
                    @usercan('party-reports.read')
                    <li class="dropdown">
                        <a href="#">{{ __('Party Reports') }}</a>
                        <ul>
                            <li><a href="{{ route('business.party-reports.customer-ledger') }}">{{ __('Customer Ledger') }}</a></li>
                            <li><a href="{{ route('business.party-reports.supplier-ledger') }}">{{ __('Supplier Ledger') }}</a></li>
                            <li><a href="{{ route('business.party-reports.party-profit-loss') }}">{{ __('Party Profit & Loss') }}</a></li>
                            <li><a href="{{ route('business.party-reports.top-customer') }}">{{ __('Top 5 Customer') }}</a></li>
                            <li><a href="{{ route('business.party-reports.top-supplier') }}">{{ __('Top 5 Supplier') }}</a></li>
                        </ul>
                    </li>
                    @endusercan

                    <!-- Advanced Reports -->
                    <li><a href="{{ route('business.reports.discount-products') }}">{{ __('Discount Products') }}</a></li>
                    <li><a href="{{ route('business.reports.product-sale') }}">{{ __('Product Sale') }}</a></li>
                    <li><a href="{{ route('business.reports.product-purchase') }}">{{ __('Product Purchase') }}</a></li>
                    <li><a href="{{ route('business.reports.product-loss-profit') }}">{{ __('Product Loss/Profit') }}</a></li>
                    <li><a href="{{ route('business.reports.top-products') }}">{{ __('Top Products') }}</a></li>
                    <li><a href="{{ route('business.reports.combo-product-reports') }}">{{ __('Combo Product Reports') }}</a></li>
                </ul>
            </li>
            @endusercanany

            <!-- Commissions -->
            @usercan('commissions.read')
            <li class="dropdown">
                <a href="#">
                    <span class="sidebar-icon">
                        <img src="{{ asset('assets/images/sidebar/commission.svg') }}">
                    </span>
                    {{ __('Commissions') }}
                </a>
                <ul>
                    <li><a href="{{ route('business.commissions.index') }}">{{ __('Set Commissions') }}</a></li>
                    <li><a href="{{ route('business.sale-commissions.index') }}">{{ __('Sale Commissions') }}</a></li>
                </ul>
            </li>
            @endusercan

            <!-- Walk-in Customer Due -->
            @usercan('walk-dues.read')
            <li>
                <a href="{{ route('business.walk-dues.index') }}">
                    <span class="sidebar-icon">
                        <img src="{{ asset('assets/images/sidebar/due.svg') }}">
                    </span>
                    {{ __('Walk-in Customer Due') }}
                </a>
            </li>
            @endusercan

        </ul>
    </div>
</nav>
```

---

## قائمة الراوتس المفقودة الشائعة

إذا ظهرت أخطاء أخرى، هذه قائمة بالراوتس التي قد تكون مفقودة:

### Finance & Accounts
```
business.banks.index
business.cashes.index
business.cheques.index
business.bank-transactions.index
business.cash-flow-reports.index
business.balance-sheet-reports.index
business.bill-wise-profit-reports.index
```

### Warehouse (إذا كان Addon غير مفعل)
```
warehouse.warehouses.index
warehouse.warehouses.product
```

### Multi-Branch (إذا كان Addon غير مفعل)
```
multibranch.branches.overview
multibranch.branches.index
```

### HRM (إذا كان Addon غير مفعل)
```
hrm.employees.index
hrm.attendance.index
hrm.payroll.index
```

---

## الحل السريع الشامل

إذا كنت تريد حل سريع، استخدم هذا الكود في بداية السايدبار:

```php
@php
// Check if routes exist before using them
$availableRoutes = [
    'business.banks.index' => Route::has('business.banks.index'),
    'business.cashes.index' => Route::has('business.cashes.index'),
    'business.cheques.index' => Route::has('business.cheques.index'),
    'warehouse.warehouses.index' => Route::has('warehouse.warehouses.index'),
    'multibranch.branches.index' => Route::has('multibranch.branches.index'),
];
@endphp
```

ثم استخدمه في السايدبار:
```php
@if($availableRoutes['business.banks.index'] ?? false)
<li><a href="{{ route('business.banks.index') }}">{{ __('Bank Account') }}</a></li>
@endif
```

---

## التوصية النهائية

**استخدم الحل 3** (إنشاء سايدبار مخصص) لأنه:
- أنظف وأسهل في الصيانة
- يحتوي فقط على الميزات الموجودة
- لا توجد أخطاء في الراوتس المفقودة

**تاريخ الإنشاء:** 16 فبراير 2026
