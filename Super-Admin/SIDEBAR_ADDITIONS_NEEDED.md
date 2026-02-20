# الإضافات المطلوبة للسايدبار

## المشكلة
السايدبار في المشروع الجديد لا يحتوي على الراوتس التي أضفناها (Party Reports, Combo Products, Commissions, Walk Dues)

---

## الحل: إضافة الأقسام المفقودة

### 1. إضافة Combo Products في قسم Products

**ابحث عن هذا السطر في قسم Products:**
```php
{{-- @usercan('products.read')
<li><a class="{{ Request::routeIs('business.combo-products.index') ? 'active' : '' }}"
href="{{ route('business.combo-products.index') }}">{{ __('Combo Products') }}</a>
</li>
@endusercan --}}
```

**احذف التعليق وفعّله:**
```php
@usercan('products.read')
<li><a class="{{ Request::routeIs('business.combo-products.index') ? 'active' : '' }}"
href="{{ route('business.combo-products.index') }}">{{ __('Combo Products') }}</a>
</li>
@endusercan
```

**وأيضاً في السطر:**
```php
<li class="dropdown {{ Request::routeIs('business.products.index', 'business.products.create', 'business.products.edit', 'business.products.expired', 'business.categories.index', 'business.brands.index', 'business.units.index', 'business.barcodes.index', 'business.bulk-uploads.index', 'business.variations.index', 'business.product-models.index','business.racks.index', 'business.shelfs.index'/*, 'business.combo-products.index'*/) ? 'active' : '' }}">
```

**احذف التعليق من `'business.combo-products.index'`:**
```php
<li class="dropdown {{ Request::routeIs('business.products.index', 'business.products.create', 'business.products.edit', 'business.products.expired', 'business.categories.index', 'business.brands.index', 'business.units.index', 'business.barcodes.index', 'business.bulk-uploads.index', 'business.variations.index', 'business.product-models.index','business.racks.index', 'business.shelfs.index', 'business.combo-products.index') ? 'active' : '' }}">
```

---

### 2. إضافة Walk-in Customer Due في قسم Due List

**ابحث عن هذا السطر:**
```php
{{-- <li><a class="{{ Request::routeIs('business.walk-dues.index','business.collect.walk.dues') ? 'active' : '' }}" href="{{ route('business.walk-dues.index') }}">{{ __('Guest Due') }}</a></li> --}}
```

**احذف التعليق وفعّله:**
```php
<li><a class="{{ Request::routeIs('business.walk-dues.index','business.collect.walk.dues') ? 'active' : '' }}" href="{{ route('business.walk-dues.index') }}">{{ __('Guest Due') }}</a></li>
```

**وأيضاً في السطر:**
```php
<li class="dropdown {{ Request::routeIs('business.dues.index'/*,'business.walk-dues.index','business.collect.walk.dues'*/,'business.collect.dues', 'business.party.dues') ? 'active' : '' }}">
```

**احذف التعليق:**
```php
<li class="dropdown {{ Request::routeIs('business.dues.index','business.walk-dues.index','business.collect.walk.dues','business.collect.dues', 'business.party.dues') ? 'active' : '' }}">
```

---

### 3. إضافة Sale Commission في قسم Finance & Accounts

**ابحث عن هذا القسم المعلق:**
```php
{{-- <li class="dropdown {{ Request::routeIs(/*'business.commissions.index',*/'business.sale-commissions.index') ? 'active' : '' }}">
<a href="#">
<span class="sidebar-icon">
<img src="{{ asset('assets/images/sidebar/cash_and_bank.svg') }}">
</span>
{{ __('Sale Commission') }}</a>
<ul>
@usercan('commissions.read')
<li><a class="{{ Request::routeIs('business.commissions.index') ? 'active' : '' }}" href="{{ route('business.commissions.index') }}">{{ __('Set Commissions') }}</a></li>
@endusercan
@usercan('sale-commissions.read')
<li><a class="{{ Request::routeIs('business.sale-commissions.index') ? 'active' : '' }}" href="{{ route('business.sale-commissions.index') }}">{{ __('Sale Commission') }}</a></li>
@endusercan
</ul>
</li> --}}
```

**احذف التعليق وفعّله:**
```php
<li class="dropdown {{ Request::routeIs('business.commissions.index','business.sale-commissions.index') ? 'active' : '' }}">
<a href="#">
<span class="sidebar-icon">
<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M10 18.333C9.31817 18.333 8.66683 18.0578 7.36411 17.5076C4.12137 16.1378 2.5 15.4528 2.5 14.3008C2.5 13.9782 2.5 8.38676 2.5 5.83301M10 18.333C10.6818 18.333 11.3332 18.0578 12.6359 17.5076C15.8787 16.1378 17.5 15.4528 17.5 14.3008V5.83301M10 18.333V9.46201" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
</span>
{{ __('Sale Commission') }}</a>
<ul>
@usercan('commissions.read')
<li><a class="{{ Request::routeIs('business.commissions.index') ? 'active' : '' }}" href="{{ route('business.commissions.index') }}">{{ __('Set Commissions') }}</a></li>
@endusercan
@usercan('sale-commissions.read')
<li><a class="{{ Request::routeIs('business.sale-commissions.index') ? 'active' : '' }}" href="{{ route('business.sale-commissions.index') }}">{{ __('Sale Commission') }}</a></li>
@endusercan
</ul>
</li>
```

---

### 4. إضافة Party Reports في قسم Reports

**ابحث عن هذا القسم المعلق:**
```php
{{-- <li class="dropdown {{ Request::routeIs(/*'business.customer-ledger.index', 'business.supplier-ledger.index', 'business.top-customers.index', 'business.top-suppliers.index', 'business.party-loss-profit.index', 'business.customer-ledger.show', 'business.supplier-ledger.show'*/) ? 'active' : '' }}">
<a href="#">
<span class="sidebar-icon">
<img src="{{ asset('assets/images/icons/party-report.png') }}">
</span>
{{ __('Party Reports') }}</a>
<ul>
@usercan('customer-ledger.read')
<li><a class="{{ Request::routeIs('business.customer-ledger.index','business.customer-ledger.show') ? 'active' : '' }}"
href="{{ route('business.customer-ledger.index') }}">{{ __('Customer Ledger') }}</a></li>
@endusercan
@usercan('supplier-ledger.read')
<li><a class="{{ Request::routeIs('business.supplier-ledger.index', 'business.supplier-ledger.show') ? 'active' : '' }}"
href="{{ route('business.supplier-ledger.index') }}">{{ __('Supplier Ledger') }}</a></li>
@endusercan
@usercan('party-loss-profit.read')
<li><a class="{{ Request::routeIs('business.party-loss-profit.index') ? 'active' : '' }}"
href="{{ route('business.party-loss-profit.index') }}">{{ __('Party Profit & Loss') }}</a></li>
@endusercan
@usercan('top-customers-reports.read')
<li><a class="{{ Request::routeIs('business.top-customers.index') ? 'active' : '' }}"
href="{{ route('business.top-customers.index') }}">{{ __('Top 5 Customer') }}</a></li>
@endusercan
@usercan('top-suppliers-reports.read')
<li><a class="{{ Request::routeIs('business.top-suppliers.index') ? 'active' : '' }}"
href="{{ route('business.top-suppliers.index') }}">{{ __('Top 5 Supplier') }}</a></li>
@endusercan
</ul>
</li> --}}
```

**استبدله بهذا (مع الراوتس الصحيحة):**
```php
@usercan('party-reports.read')
<li class="dropdown {{ Request::routeIs('business.party-reports.customer-ledger', 'business.party-reports.supplier-ledger', 'business.party-reports.top-customer', 'business.party-reports.top-supplier', 'business.party-reports.party-profit-loss', 'business.party-reports.customer-ledger-show', 'business.party-reports.supplier-ledger-show') ? 'active' : '' }}">
<a href="#">
<span class="sidebar-icon">
<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M10 18.333C9.31817 18.333 8.66683 18.0578 7.36411 17.5076C4.12137 16.1378 2.5 15.4528 2.5 14.3008C2.5 13.9782 2.5 8.38676 2.5 5.83301M10 18.333C10.6818 18.333 11.3332 18.0578 12.6359 17.5076C15.8787 16.1378 17.5 15.4528 17.5 14.3008V5.83301M10 18.333V9.46201" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
</span>
{{ __('Party Reports') }}</a>
<ul>
<li><a class="{{ Request::routeIs('business.party-reports.customer-ledger','business.party-reports.customer-ledger-show') ? 'active' : '' }}"
href="{{ route('business.party-reports.customer-ledger') }}">{{ __('Customer Ledger') }}</a></li>
<li><a class="{{ Request::routeIs('business.party-reports.supplier-ledger', 'business.party-reports.supplier-ledger-show') ? 'active' : '' }}"
href="{{ route('business.party-reports.supplier-ledger') }}">{{ __('Supplier Ledger') }}</a></li>
<li><a class="{{ Request::routeIs('business.party-reports.party-profit-loss') ? 'active' : '' }}"
href="{{ route('business.party-reports.party-profit-loss') }}">{{ __('Party Profit & Loss') }}</a></li>
<li><a class="{{ Request::routeIs('business.party-reports.top-customer') ? 'active' : '' }}"
href="{{ route('business.party-reports.top-customer') }}">{{ __('Top 5 Customer') }}</a></li>
<li><a class="{{ Request::routeIs('business.party-reports.top-supplier') ? 'active' : '' }}"
href="{{ route('business.party-reports.top-supplier') }}">{{ __('Top 5 Supplier') }}</a></li>
</ul>
</li>
@endusercan
```

**ضع هذا القسم بعد قسم Reports الموجود وقبل Custom Reports**

---

### 5. إضافة Advanced Reports في قسم Reports

**أضف هذه الراوتس داخل قسم Reports (بعد Expired Product):**

```php
@usercan('reports.read')
<li><a class="{{ Request::routeIs('business.reports.discount-products') ? 'active' : '' }}"
href="{{ route('business.reports.discount-products') }}">{{ __('Discount Products') }}</a></li>
@endusercan

@usercan('reports.read')
<li><a class="{{ Request::routeIs('business.reports.product-sale') ? 'active' : '' }}"
href="{{ route('business.reports.product-sale') }}">{{ __('Product Sale') }}</a></li>
@endusercan

@usercan('reports.read')
<li><a class="{{ Request::routeIs('business.reports.product-purchase') ? 'active' : '' }}"
href="{{ route('business.reports.product-purchase') }}">{{ __('Product Purchase') }}</a></li>
@endusercan

@usercan('reports.read')
<li><a class="{{ Request::routeIs('business.reports.product-loss-profit') ? 'active' : '' }}"
href="{{ route('business.reports.product-loss-profit') }}">{{ __('Product Loss/Profit') }}</a></li>
@endusercan

@usercan('reports.read')
<li><a class="{{ Request::routeIs('business.reports.top-products') ? 'active' : '' }}"
href="{{ route('business.reports.top-products') }}">{{ __('Top Products') }}</a></li>
@endusercan

@usercan('reports.read')
<li><a class="{{ Request::routeIs('business.reports.combo-product-reports') ? 'active' : '' }}"
href="{{ route('business.reports.combo-product-reports') }}">{{ __('Combo Product Reports') }}</a></li>
@endusercan
```

---

## ملخص التعديلات

### الأقسام التي يجب تفعيلها (إزالة التعليق):
1. ✅ Combo Products في قسم Products
2. ✅ Walk-in Customer Due في قسم Due List  
3. ✅ Sale Commission (قسم كامل)

### الأقسام التي يجب إضافتها (جديدة):
4. ✅ Party Reports (قسم كامل جديد)
5. ✅ Advanced Reports (6 راوتس جديدة في قسم Reports)

---

## الموقع الصحيح لكل قسم

### 1. Combo Products
**الموقع:** داخل قسم Products، بعد "Add Product"

### 2. Walk-in Customer Due
**الموقع:** داخل قسم Due List، بعد "All Due"

### 3. Sale Commission
**الموقع:** بعد قسم Subscriptions وقبل قسم HRM

### 4. Party Reports
**الموقع:** بعد قسم Reports الأساسي وقبل Custom Reports

### 5. Advanced Reports
**الموقع:** داخل قسم Reports، بعد "Expired Product"

---

## نصيحة مهمة

بدلاً من التعديل اليدوي، يمكنك:

1. **نسخ السايدبار من المشروع الحالي** (اللي شغال عندك)
2. **لصقه في المشروع الجديد**
3. **حذف الأقسام المعلقة اللي مش محتاجها** (Banks, Cashes, etc.)

هذا أسرع وأضمن!

---

**تاريخ الإنشاء:** 16 فبراير 2026
