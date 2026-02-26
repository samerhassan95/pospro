# مثال تطبيق الصلاحيات على Sidebar

## كيفية إخفاء/إظهار عناصر القائمة حسب صلاحيات الباقة

### مثال 1: إخفاء قسم المالية والحسابات

```blade
@if(plan_allows('finance'))
    <li class="sidebar-item">
        <a href="{{ route('finance.index') }}" class="sidebar-link">
            <i class="icon-finance"></i>
            <span>{{ __('Finance & Accounting') }}</span>
        </a>
    </li>
@endif
```

### مثال 2: إخفاء قسم إدارة الموارد البشرية

```blade
@if(plan_allows('hrm'))
    <li class="sidebar-item">
        <a href="{{ route('hrm.index') }}" class="sidebar-link">
            <i class="icon-hrm"></i>
            <span>{{ __('Human Resources') }}</span>
        </a>
    </li>
@endif
```

### مثال 3: إخفاء قسم العمولات

```blade
@if(plan_allows('commission'))
    <li class="sidebar-item">
        <a href="{{ route('commissions.index') }}" class="sidebar-link">
            <i class="icon-commission"></i>
            <span>{{ __('Commissions') }}</span>
        </a>
    </li>
@endif
```

### مثال 4: إخفاء قسم قائمة المستحقات

```blade
@if(plan_allows('due_list'))
    <li class="sidebar-item">
        <a href="{{ route('dues.index') }}" class="sidebar-link">
            <i class="icon-dues"></i>
            <span>{{ __('Due List') }}</span>
        </a>
    </li>
@endif
```

### مثال 5: إخفاء التطبيق والمتجر

```blade
@if(plan_allows('pos_app'))
    <li class="sidebar-item">
        <a href="{{ route('pos.app') }}" class="sidebar-link">
            <i class="icon-app"></i>
            <span>{{ __('POS Application') }}</span>
        </a>
    </li>
@endif

@if(plan_allows('store'))
    <li class="sidebar-item">
        <a href="{{ route('store.index') }}" class="sidebar-link">
            <i class="icon-store"></i>
            <span>{{ __('Online Store') }}</span>
        </a>
    </li>
@endif
```

### مثال 6: عرض رسالة للترقية

```blade
@if(!plan_allows('hrm'))
    <li class="sidebar-item disabled">
        <a href="#" class="sidebar-link" onclick="showUpgradeModal('hrm')">
            <i class="icon-hrm"></i>
            <span>{{ __('Human Resources') }}</span>
            <span class="badge badge-warning">{{ __('Upgrade') }}</span>
        </a>
    </li>
@endif
```

### مثال 7: التحقق من حد الفروع

```blade
<li class="sidebar-item">
    <a href="{{ route('branches.index') }}" class="sidebar-link">
        <i class="icon-branch"></i>
        <span>{{ __('Branches') }}</span>
        @if(branch_limit() !== null)
            <span class="badge badge-info">{{ branch_limit() }}</span>
        @endif
    </a>
</li>
```

### مثال 8: التحقق من حد المستودعات

```blade
<li class="sidebar-item">
    <a href="{{ route('warehouses.index') }}" class="sidebar-link">
        <i class="icon-warehouse"></i>
        <span>{{ __('Warehouses') }}</span>
        @if(warehouse_limit() !== null)
            <span class="badge badge-info">{{ warehouse_limit() }}</span>
        @endif
    </a>
</li>
```

## في صفحة إضافة فرع جديد

```blade
@if(can_add_branch())
    <a href="{{ route('branches.create') }}" class="btn btn-primary">
        {{ __('Add New Branch') }}
    </a>
@else
    <button class="btn btn-secondary" disabled>
        {{ __('Branch Limit Reached') }}
    </button>
    <p class="text-muted">
        {{ __('Your plan allows') }} {{ branch_limit() }} {{ __('branch(es) only') }}
        <a href="{{ route('plans.upgrade') }}">{{ __('Upgrade Plan') }}</a>
    </p>
@endif
```

## في صفحة إضافة مستودع جديد

```blade
@if(can_add_warehouse())
    <a href="{{ route('warehouses.create') }}" class="btn btn-primary">
        {{ __('Add New Warehouse') }}
    </a>
@else
    <button class="btn btn-secondary" disabled>
        {{ __('Warehouse Limit Reached') }}
    </button>
    <p class="text-muted">
        {{ __('Your plan allows') }} {{ warehouse_limit() }} {{ __('warehouse(s) only') }}
        <a href="{{ route('plans.upgrade') }}">{{ __('Upgrade Plan') }}</a>
    </p>
@endif
```

## عرض معلومات الباقة الحالية

```blade
<div class="plan-info">
    <h4>{{ __('Current Plan') }}: {{ current_plan_name() }}</h4>
    <ul>
        <li>{{ __('Branches') }}: {{ branch_limit() ?? __('Unlimited') }}</li>
        <li>{{ __('Warehouses') }}: {{ warehouse_limit() ?? __('Unlimited') }}</li>
        <li>{{ __('Finance') }}: {{ plan_allows('finance') ? __('Yes') : __('No') }}</li>
        <li>{{ __('HRM') }}: {{ plan_allows('hrm') ? __('Yes') : __('No') }}</li>
        <li>{{ __('Commission') }}: {{ plan_allows('commission') ? __('Yes') : __('No') }}</li>
    </ul>
</div>
```
