@extends('layouts.business.pos')

@section('title')
    {{ __('Pos Purchase') }}
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/choices.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/calculator.css') }}">
    <style>
        /* Force RTL SVG flip */
        @if (in_array(app()->getLocale(), ['ar', 'arbh', 'eg-ar', 'fa', 'prs', 'ps', 'ur']))
        [dir="rtl"] .pos-top-nav a[href*="dashboard"] svg {
            transform: scaleX(-1) !important;
        }
        @endif
        .pos-fullscreen-body { margin: 0; padding: 0; background: #f5f5f5; }
        .pos-fullscreen-wrapper { width: 100%; min-height: 100vh; display: flex; flex-direction: column; }
        .pos-top-header {padding: 12px 24px 0px 24px; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; background:#e6eaf2 !important; }
        .pos-brand { display: flex; align-items: center; gap: 12px; }
        .pos-brand-title { font-size: 20px; font-weight: 700; color: #1a1a1a; margin: 0; }
        .pos-brand-subtitle { font-size: 12px; color: #6b7280; margin: 0; }
        .pos-top-nav { background: white; padding: 8px 8px; border-radius: 100px;  display: flex; flex-wrap: wrap; flex-wrap: wrap; align-items: center; gap: 8px; }
        .pos-nav-btn { width: 40px; height: 40px; border-radius: 8px; border: none; background: #fff; display: flex; align-items: center; justify-content: center; color: #374151; cursor: pointer; transition: all 0.2s; text-decoration: none; flex-shrink: 0; }
        .pos-nav-btn:hover { background: #f9fafb; color: #1a1a1a; }
        .pos-nav-btn i { font-size: 16px; }
        .pos-nav-btn svg { width: 20px; height: 20px; flex-shrink: 0; }
        .pos-nav-divider { width: 1px; height: 24px; background: #e5e7eb; margin: 0 8px; }
        .pos-add-expense-btn { display: flex; align-items: center; gap: 8px; padding: 12px 24px; background: #FF6500; border: none; border-radius: 100px; color: #fff; font-size: 16px; font-weight: 600; cursor: pointer; text-decoration: none; }
        .pos-add-expense-btn:hover { background: #e55a00; color: #fff; }
        .pos-add-expense-btn svg { width: 24px; height: 24px; flex-shrink: 0; }
        .pos-header-btn { display: flex; align-items: center; gap: 8px; padding: 10px 20px; background: #fff; border: 1px solid #e5e7eb; border-radius: 100px; color: #374151; font-size: 14px; font-weight: 500; cursor: pointer; text-decoration: none; transition: all 0.2s; }
        .pos-header-btn:hover { background: #f9fafb; color: #1a1a1a; border-color: #d1d5db; }
        .pos-header-btn svg { width: 20px; height: 20px; flex-shrink: 0; }
        .pos-main-container { display: grid; grid-template-columns: 420px 1fr; gap: 20px; padding: 20px; background: #e6eaf2 !important; }
        @media (max-width: 1200px) { .pos-main-container { grid-template-columns: 380px 1fr; } }
        @media (max-width: 992px) { .pos-main-container { grid-template-columns: 1fr; } }
        .order-sidebar { background: #fff; border-radius: 12px; padding: 20px; display: flex; flex-direction: column; border-right: none; }
        .order-header { margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #f0f0f0; }
        .order-title { font-size: 18px; font-weight: 600; color: #1a1a1a; margin: 0; }
        .order-date { font-size: 13px; color: #666; margin-top: 4px; }
        .supplier-section { margin-bottom: 20px; }
        .supplier-select-wrapper { display: flex; gap: 10px; align-items: center; }
        .supplier-select-wrapper .form-select, .supplier-select-wrapper .choices { flex: 1; }
        .add-supplier-btn { width: 40px; height: 40px; border-radius: 8px; background: #FF6500; border: none; display: flex; align-items: center; justify-content: center; color: #fff; flex-shrink: 0; }
        .add-supplier-btn:hover { background: #e55a00; }
        .cart-section { margin-bottom: 15px; display: flex; flex-direction: column; }
        .cart-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .cart-title { font-size: 16px; font-weight: 600; color: #1a1a1a; }
        .clear-cart-btn { font-size: 13px; color: #FF6500; background: none; border: none; cursor: pointer; display: flex; align-items: center; gap: 4px; }
        .cart-items-list { display: flex; flex-direction: column; gap: 10px; padding-right: 5px; }
        .cart-items-list::-webkit-scrollbar { width: 4px; }
        .cart-items-list::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
        .cart-items-list::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }
        .cart-item-card { display: flex; align-items: stretch; background: #fff; border: 1px solid #eee; border-radius: 14px; overflow: hidden; flex-shrink: 0; }
        .cart-item-image-wrapper { width: 70px; min-width: 70px; height: 70px; background: #f5f5f5; flex-shrink: 0; border-radius: 10px; margin: 6px; overflow: hidden; }
        .cart-item-image { width: 100%; height: 100%; object-fit: cover; }
        .cart-item-details { flex: 1; padding: 8px 4px; display: flex; flex-direction: column; justify-content: center; min-width: 0; }
        .cart-item-name { font-size: 14px; font-weight: 600; color: #1a1a1a; margin: 0 0 2px; line-height: 1.2; }
        .cart-item-price { font-size: 13px; color: #666; margin: 0; font-weight: 500; }
        .cart-item-actions { display: flex; flex-direction: column; align-items: flex-end; justify-content: space-between; padding: 8px; min-width: 90px; }
        .qty-control-wrapper { display: flex; align-items: center; background: #f0f0f0; border-radius: 50px; padding: 2px; }
        .qty-btn { width: 26px; height: 26px; border-radius: 50%; border: none; background: transparent; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 10px; color: #bbb; }
        .qty-btn.plus-btn { background: #1a1a1a; color: #fff; }
        .cart-item-qty { width: 22px; text-align: center; font-size: 13px; font-weight: 600; border: none; background: transparent; color: #1a1a1a; }
        .remove-item-btn { width: 28px; height: 28px; border-radius: 50%; background: #fff; border: 2px solid #dc3545; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #dc3545; font-size: 12px; padding: 0; transition: all 0.2s; }
        .remove-item-btn:hover { background: #dc3545; color: #fff; transform: scale(1.1); }
        .remove-item-btn:active { transform: scale(0.95); }
        .empty-cart { text-align: center; padding: 30px 20px; color: #9ca3af; }
        .empty-cart-icon { font-size: 40px; margin-bottom: 10px; color: #ddd; }
        .order-summary { border-top: 1px solid #eee; padding-top: 15px; margin-bottom: 15px; }
        .summary-row { display: flex; justify-content: space-between; align-items: center; padding: 6px 0; font-size: 14px; color: #666; }
        .summary-row.total { font-size: 16px; font-weight: 600; color: #1a1a1a; border-top: 1px solid #eee; padding-top: 12px; margin-top: 8px; }
        .cancel-order-btn { width: 100%; padding: 12px; border: 1px solid #FF6500; background: #fff; color: #FF6500; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; margin-bottom: 20px; }
        .cancel-order-btn:hover { background: #fff5f5; }
        .payment-section-new { border-top: 1px solid #eee; padding-top: 20px; }
        .payment-field { margin-bottom: 15px; }
        .payment-field label { display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px; }
        .payment-field input, .payment-field select { width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; }
        .payment-field input:focus, .payment-field select:focus { outline: none; border-color: #FF6500; }
        .save-order-btn { width: 100%; padding: 14px; background: #FF6500; color: #fff; border: none; border-radius: 8px; font-size: 15px; font-weight: 500; cursor: pointer; margin-top: 10px; }
        .save-order-btn:hover { background: #e55a00; }
        .products-section { background: #fff; border-radius: 12px; padding: 20px; display: flex; flex-direction: column; }
        .products-header-new { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
        .products-title { font-size: 18px; font-weight: 600; color: #1a1a1a; margin: 0; }
        .filters-btn { display: flex; align-items: center; gap: 6px; padding: 8px 16px; border: 1px solid #e5e7eb; background: #fff; border-radius: 8px; font-size: 14px; color: #374151; cursor: pointer; }
        .filters-btn:hover { background: #f9fafb; }
        .product-search-wrapper { margin-bottom: 20px; }
        .product-search-input { width: 100%; padding: 12px 16px 12px 44px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 14px; background: #f9fafb url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'/%3E%3C/svg%3E") 14px center no-repeat; }
        .product-search-input:focus { outline: none; border-color: #FF6500; background-color: #fff; }
        .choices__inner { padding: 12px 16px; border: 1px solid #e5e7eb !important; border-radius: 10px !important; font-size: 14px !important; background: #f9fafb !important; min-height: 44px !important; }
        .choices__inner:focus, .choices.is-focused .choices__inner { border-color: #FF6500 !important; background-color: #fff !important; }
        .choices__list--dropdown { border: 1px solid #e5e7eb; border-radius: 8px; }
        .choices__list--single { padding: 0; }
        .choices[data-type*=select-one] .choices__input { background-color: #f9fafb; border-radius: 8px; padding: 8px; }
        .products-grid-wrapper { }
        .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 16px; }
        .product-card-new { background: #fff; border: 1px solid #f0f0f0; border-radius: 12px; overflow: hidden; cursor: pointer; transition: all 0.2s; display: flex; flex-direction: column; }
        .product-card-image { width: 100%; height: 140px; object-fit: cover; background: #f9fafb; }
        .product-card-body { padding: 12px; display: flex; flex-direction: column; flex: 1; min-height: 120px; }
        .product-card-row { display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; margin-bottom: 8px; flex-wrap: wrap; }
        .product-card-info { flex: 1; min-width: 0; flex-basis: 100%; }
        .product-card-name { font-size: 14px; font-weight: 500; color: #1a1a1a; margin: 0 0 2px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.3; }
        .product-card-code { font-size: 12px; color: #9ca3af; margin: 0; }
        .product-card-price { font-size: 14px; font-weight: 600; color: #1a1a1a; white-space: nowrap; flex-basis: 100%; text-align: right; }
        .add-product-btn { width: 32px; height: 32px; border-radius: 50%; background: #FF6500; border: none; display: flex; align-items: center; justify-content: center; color: #fff; cursor: pointer; flex-shrink: 0; align-self: flex-end; margin-top: auto; }
        .hidden-cart-inputs { display: none; }
    </style>
@endpush

@php
    $modules = product_setting()->modules ?? [];
@endphp

@section('main_content')
    <form action="{{ route('business.purchases.store') }}" method="post" enctype="multipart/form-data" class="ajaxform pos-fullscreen-form">
        @csrf
        
        <!-- Top Header Bar -->
        <div class="pos-top-header">
            <div class="pos-brand">
                <div>
                    <p class="pos-brand-subtitle">{{ auth()->user()->business->companyName ?? __('Business') }}</p>
                    <h1 class="pos-brand-title">POS Purchase</h1>
                </div>
            </div>

            <div class="pos-top-nav">
                <a href="{{ route('business.dashboard.index') }}" class="pos-nav-btn" title="{{ __('Back') }}">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.0249 7.30624C11.3026 7.08951 11.7843 6.7256 11.9457 6.60372C12.2792 6.35808 12.3505 5.88857 12.1048 5.55506C11.8592 5.22154 11.3892 5.15065 11.0557 5.39628L11.0535 5.39793C10.8846 5.52553 10.3855 5.90245 10.102 6.12372C9.53324 6.5676 8.77234 7.17803 8.00925 7.84058C7.24988 8.4999 6.47119 9.22557 5.87644 9.89869C5.57982 10.2344 5.3131 10.5747 5.11648 10.9008C4.93226 11.2064 4.75098 11.5933 4.75098 11.9998C4.75097 12.4063 4.93226 12.7932 5.11647 13.0988C5.3131 13.425 5.57982 13.7653 5.87643 14.101C6.47119 14.7741 7.24989 15.4997 8.00927 16.1591C8.77237 16.8216 9.53327 17.4321 10.1021 17.8759C10.3853 18.097 10.8841 18.4738 11.0535 18.6017L11.0562 18.6037C11.3897 18.8494 11.8592 18.7781 12.1049 18.4446C12.3505 18.1111 12.2786 17.6411 11.945 17.3954C11.7837 17.2735 11.3026 16.9101 11.0249 16.6934C10.4687 16.2593 9.72958 15.6662 8.99268 15.0264C8.25205 14.3834 7.53075 13.7079 7.00051 13.1078C6.73463 12.8068 6.5326 12.5425 6.4011 12.3244C6.27743 12.1192 6.25171 12.0016 6.25171 12.0016C6.25171 12.0016 6.27743 11.8804 6.4011 11.6753C6.5326 11.4571 6.73463 11.1928 7.00051 10.8919C7.53076 10.2918 8.25205 9.61627 8.99267 8.97323C9.72956 8.33342 10.4687 7.74031 11.0249 7.30624Z" fill="currentColor"/><path d="M18.0249 7.30624C18.3026 7.08951 18.7843 6.7256 18.9457 6.60372C19.2792 6.35808 19.3505 5.88857 19.1048 5.55506C18.8592 5.22154 18.3892 5.15065 18.0557 5.39628L18.0535 5.39793C17.8846 5.52551 17.3856 5.90243 17.102 6.12372C16.5332 6.5676 15.7723 7.17803 15.0092 7.84058C14.2499 8.4999 13.4712 9.22557 12.8764 9.89869C12.5798 10.2344 12.3131 10.5747 12.1165 10.9008C11.9323 11.2064 11.751 11.5933 11.751 11.9998C11.751 12.4063 11.9323 12.7932 12.1165 13.0988C12.3131 13.425 12.5798 13.7653 12.8764 14.101C13.4712 14.7741 14.2499 15.4997 15.0093 16.1591C15.7724 16.8216 16.5333 17.4321 17.1021 17.8759C17.3854 18.097 17.8843 18.4739 18.0536 18.6018L18.0562 18.6037C18.3897 18.8494 18.8592 18.7781 19.1049 18.4446C19.3505 18.1111 19.2786 17.6411 18.945 17.3954C18.7837 17.2735 18.3026 16.9101 18.0249 16.6934C17.4687 16.2593 16.7296 15.6662 15.9927 15.0264C15.2521 14.3834 14.5308 13.7079 14.0005 13.1078C13.7346 12.8068 13.5326 12.5425 13.4011 12.3244C13.2774 12.1192 13.2517 12.0016 13.2517 12.0016C13.2517 12.0016 13.2774 11.8804 13.4011 11.6753C13.5326 11.4571 13.7346 11.1928 14.0005 10.8919C14.5308 10.2918 15.252 9.61627 15.9927 8.97323C16.7296 8.33342 17.4687 7.74031 18.0249 7.30624Z" fill="currentColor"/></svg>
                </a>
                <a href="{{ route('business.products.index') }}" class="pos-nav-btn" title="{{ __('Products') }}">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.8327 18.3327C10.1508 18.3327 9.49952 18.0483 8.19677 17.4797C6.67629 16.816 5.5123 16.3079 4.70475 15.8327H1.66602M10.8327 18.3327C11.5145 18.3327 12.1658 18.0483 13.4686 17.4797C16.7113 16.0643 18.3327 15.3565 18.3327 14.166V5.41602M10.8327 18.3327V9.16602M3.33268 5.41602V7.91602" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/><path d="M7.77225 8.0755L5.33792 6.89756C4.00196 6.2511 3.33398 5.92787 3.33398 5.41602C3.33398 4.90416 4.00196 4.58093 5.33792 3.93447L7.77225 2.75653C9.27465 2.02952 10.0259 1.66602 10.834 1.66602C11.6421 1.66602 12.3933 2.02952 13.8957 2.75653L16.3301 3.93447C17.666 4.58093 18.334 4.90416 18.334 5.41602C18.334 5.92787 17.666 6.2511 16.3301 6.89756L13.8957 8.0755C12.3933 8.80252 11.6421 9.16602 10.834 9.16602C10.0259 9.16602 9.27465 8.80252 7.77225 8.0755Z" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/><path d="M15.1145 3.3457L6.55664 7.48672" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/><path d="M1.66602 10.834H4.16602" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/><path d="M1.66602 13.334H4.16602" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
                <button type="button" data-bs-toggle="modal" data-bs-target="#bulk-upload-modal" class="pos-nav-btn" title="{{ __('Bulk Upload') }}">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 16V8M12 8L9 11M12 8L15 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 15V16C3 18.2091 4.79086 20 7 20H17C19.2091 20 21 18.2091 21 16V15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M7 4H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <a href="{{ route('business.purchases.index', ['today' => true]) }}" class="pos-nav-btn" title="{{ __('Today Purchase') }}">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.40476 15.5264L8.93476 20.0564C10.7948 21.9164 13.8148 21.9164 15.6848 20.0564L20.0748 15.6664C21.9348 13.8064 21.9348 10.7864 20.0748 8.91637L15.5348 4.39637C14.5848 3.44637 13.2748 2.93637 11.9348 3.00637L6.93476 3.24637C4.93476 3.33637 3.34476 4.92637 3.24476 6.91637L3.00476 11.9164C2.94476 13.2664 3.45476 14.5764 4.40476 15.5264Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M9.73438 12.2266C11.1151 12.2266 12.2344 11.1073 12.2344 9.72656C12.2344 8.34585 11.1151 7.22656 9.73438 7.22656C8.35366 7.22656 7.23438 8.34585 7.23438 9.72656C7.23438 11.1073 8.35366 12.2266 9.73438 12.2266Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M13.2344 17.2266L17.2344 13.2266" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
                <button type="button" data-bs-toggle="modal" data-bs-target="#calculatorModal" class="pos-nav-btn" title="{{ __('Calculator') }}">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16.75 6C16.75 5.58579 16.4142 5.25 16 5.25C15.5858 5.25 15.25 5.58579 15.25 6V7.25H14C13.5858 7.25 13.25 7.58579 13.25 8C13.25 8.41421 13.5858 8.75 14 8.75H15.25V10C15.25 10.4142 15.5858 10.75 16 10.75C16.4142 10.75 16.75 10.4142 16.75 10V8.75H18C18.4142 8.75 18.75 8.41421 18.75 8C18.75 7.58579 18.4142 7.25 18 7.25H16.75V6Z" fill="currentColor"/><path d="M13.25 17.5C13.25 17.0858 13.5858 16.75 14 16.75H18C18.4142 16.75 18.75 17.0858 18.75 17.5C18.75 17.9142 18.4142 18.25 18 18.25H14C13.5858 18.25 13.25 17.9142 13.25 17.5Z" fill="currentColor"/><path d="M14 13.75C13.5858 13.75 13.25 14.0858 13.25 14.5C13.25 14.9142 13.5858 15.25 14 15.25H18C18.4142 15.25 18.75 14.9142 18.75 14.5C18.75 14.0858 18.4142 13.75 18 13.75H14Z" fill="currentColor"/><path d="M10.5303 13.4697C10.8232 13.7626 10.8232 14.2374 10.5303 14.5303L9.31066 15.75L10.5303 16.9697C10.8232 17.2626 10.8232 17.7374 10.5303 18.0303C10.2374 18.3232 9.76256 18.3232 9.46967 18.0303L8.25 16.8107L7.03033 18.0303C6.73744 18.3232 6.26256 18.3232 5.96967 18.0303C5.67678 17.7374 5.67678 17.2626 5.96967 16.9697L7.18934 15.75L5.96967 14.5303C5.67678 14.2374 5.67678 13.7626 5.96967 13.4697C6.26256 13.1768 6.73744 13.1768 7.03033 13.4697L8.25 14.6893L9.46967 13.4697C9.76256 13.1768 10.2374 13.1768 10.5303 13.4697Z" fill="currentColor"/><path d="M6 7.25C5.58579 7.25 5.25 7.58579 5.25 8C5.25 8.41421 5.58579 8.75 6 8.75H10C10.4142 8.75 10.75 8.41421 10.75 8C10.75 7.58579 10.4142 7.25 10 7.25H6Z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M12.052 1.75H11.948C9.75288 1.74999 8.03639 1.74998 6.69787 1.91195C5.33461 2.0769 4.24953 2.42064 3.38952 3.19465C2.5182 3.97883 2.12077 4.98563 1.93214 6.24835C1.74997 7.46784 1.74998 9.02533 1.75 10.9878V13.0122C1.74998 14.9747 1.74997 16.5322 1.93214 17.7516C2.12077 19.0144 2.5182 20.0212 3.38952 20.8054C4.24953 21.5794 5.33461 21.9231 6.69787 22.0881C8.03639 22.25 9.75287 22.25 11.948 22.25H12.052C14.2471 22.25 15.9636 22.25 17.3021 22.0881C18.6654 21.9231 19.7505 21.5794 20.6105 20.8054C21.4818 20.0212 21.8792 19.0144 22.0679 17.7516C22.25 16.5322 22.25 14.9747 22.25 13.0123V10.9877C22.25 9.02532 22.25 7.46783 22.0679 6.24835C21.8792 4.98563 21.4818 3.97883 20.6105 3.19465C19.7505 2.42064 18.6654 2.0769 17.3021 1.91195C15.9636 1.74998 14.2471 1.74999 12.052 1.75ZM4.39297 4.30959C4.9242 3.83148 5.65432 3.54916 6.87805 3.40108C8.11596 3.2513 9.7417 3.25 12 3.25C14.2583 3.25 15.884 3.2513 17.1219 3.40108C18.3457 3.54916 19.0758 3.83148 19.607 4.30959C20.127 4.77752 20.4251 5.40441 20.5843 6.46997C20.748 7.56591 20.75 9.01126 20.75 11.05V12.95C20.75 14.9887 20.748 16.4341 20.5843 17.53C20.4251 18.5956 20.127 19.2225 19.607 19.6904C19.0758 20.1685 18.3457 20.4508 17.1219 20.5989C15.884 20.7487 14.2583 20.75 12 20.75C9.7417 20.75 8.11596 20.7487 6.87805 20.5989C5.65432 20.4508 4.92421 20.1685 4.39297 19.6904C3.87304 19.2225 3.57485 18.5956 3.41568 17.53C3.25196 16.4341 3.25 14.9887 3.25 12.95V11.05C3.25 9.01126 3.25196 7.56591 3.41568 6.46997C3.57485 5.40441 3.87304 4.77752 4.39297 4.30959Z" fill="currentColor"/></svg>
                </button>
                <a href="{{ route('business.dashboard.index') }}" class="pos-nav-btn" title="{{ __('Dashboard') }}">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.63606 9.45776L3.33268 9.63192L3.7134 13.4168C3.92852 15.5553 4.03607 16.6246 4.75018 17.2704C5.46429 17.9163 6.53896 17.9163 8.68827 17.9163H11.3104C13.4598 17.9163 14.5344 17.9163 15.2485 17.2704C15.9626 16.6246 16.0702 15.5553 16.2853 13.4168L16.666 9.63192L17.3627 9.45776C17.9328 9.31526 18.3327 8.80301 18.3327 8.21536C18.3327 7.79746 18.1288 7.40586 17.7864 7.16621L10.9551 2.38429C10.3813 1.98258 9.61743 1.98258 9.0436 2.38429L2.21226 7.16621C1.86991 7.40586 1.66602 7.79746 1.66602 8.21536C1.66602 8.80301 2.06596 9.31526 2.63606 9.45776Z" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/><path d="M9.99935 14.1667C11.1499 14.1667 12.0827 13.2339 12.0827 12.0833C12.0827 10.9327 11.1499 10 9.99935 10C8.84876 10 7.91602 10.9327 7.91602 12.0833C7.91602 13.2339 8.84876 14.1667 9.99935 14.1667Z" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
                <button type="button" data-bs-toggle="offcanvas" data-bs-target="#category-search-modal" class="pos-nav-btn" title="{{ __('Category') }}">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 8.52V3.98C22 2.57 21.36 2 19.77 2H15.73C14.14 2 13.5 2.57 13.5 3.98V8.51C13.5 9.93 14.14 10.49 15.73 10.49H19.77C21.36 10.5 22 9.93 22 8.52Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M22 19.77V15.73C22 14.14 21.36 13.5 19.77 13.5H15.73C14.14 13.5 13.5 14.14 13.5 15.73V19.77C13.5 21.36 14.14 22 15.73 22H19.77C21.36 22 22 21.36 22 19.77Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M10.5 8.52V3.98C10.5 2.57 9.86 2 8.27 2H4.23C2.64 2 2 2.57 2 3.98V8.51C2 9.93 2.64 10.49 4.23 10.49H8.27C9.86 10.5 10.5 9.93 10.5 8.52Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M10.5 19.77V15.73C10.5 14.14 9.86 13.5 8.27 13.5H4.23C2.64 13.5 2 14.14 2 15.73V19.77C2 21.36 2.64 22 4.23 22H8.27C9.86 22 10.5 21.36 10.5 19.77Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <button type="button" data-bs-toggle="offcanvas" data-bs-target="#brand-search-modal" class="pos-nav-btn" title="{{ __('Brand') }}">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19 9C19 10.45 18.57 11.78 17.83 12.89C16.75 14.49 15.04 15.62 13.05 15.91C12.71 15.97 12.36 16 12 16C11.64 16 11.29 15.97 10.95 15.91C8.96 15.62 7.25 14.49 6.17 12.89C5.43 11.78 5 10.45 5 9C5 5.13 8.13 2 12 2C15.87 2 19 5.13 19 9Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M21.2491 18.4699L19.5991 18.8599C19.2291 18.9499 18.9391 19.2299 18.8591 19.5999L18.5091 21.0699C18.3191 21.8699 17.2991 22.1099 16.7691 21.4799L11.9991 15.9999L7.2291 21.4899C6.6991 22.1199 5.6791 21.8799 5.4891 21.0799L5.1391 19.6099C5.0491 19.2399 4.7591 18.9499 4.3991 18.8699L2.7491 18.4799C1.9891 18.2999 1.7191 17.3499 2.2691 16.7999L6.1691 12.8999C7.2491 14.4999 8.9591 15.6299 10.9491 15.9199C11.2891 15.9799 11.6391 16.0099 11.9991 16.0099C12.3591 16.0099 12.7091 15.9799 13.0491 15.9199C15.0391 15.6299 16.7491 14.4999 17.8291 12.8999L21.7291 16.7999C22.2791 17.3399 22.0091 18.2899 21.2491 18.4699Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M12.58 5.98L13.17 7.15999C13.25 7.31999 13.46 7.48 13.65 7.51L14.72 7.68999C15.4 7.79999 15.56 8.3 15.07 8.79L14.24 9.61998C14.1 9.75998 14.02 10.03 14.07 10.23L14.31 11.26C14.5 12.07 14.07 12.39 13.35 11.96L12.35 11.37C12.17 11.26 11.87 11.26 11.69 11.37L10.69 11.96C9.96997 12.38 9.53997 12.07 9.72997 11.26L9.96997 10.23C10.01 10.04 9.93997 9.75998 9.79997 9.61998L8.96997 8.79C8.47997 8.3 8.63997 7.80999 9.31997 7.68999L10.39 7.51C10.57 7.48 10.78 7.31999 10.86 7.15999L11.45 5.98C11.74 5.34 12.26 5.34 12.58 5.98Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <!-- Bulk Upload Button -->

                <!-- Today Purchase Button -->
                <a href="{{ route('business.purchases.index', ['today' => true]) }}" class="pos-add-expense-btn">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.40476 15.5264L8.93476 20.0564C10.7948 21.9164 13.8148 21.9164 15.6848 20.0564L20.0748 15.6664C21.9348 13.8064 21.9348 10.7864 20.0748 8.91637L15.5348 4.39637C14.5848 3.44637 13.2748 2.93637 11.9348 3.00637L6.93476 3.24637C4.93476 3.33637 3.34476 4.92637 3.24476 6.91637L3.00476 11.9164C2.94476 13.2664 3.45476 14.5764 4.40476 15.5264Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M9.73438 12.2266C11.1151 12.2266 12.2344 11.1073 12.2344 9.72656C12.2344 8.34585 11.1151 7.22656 9.73438 7.22656C8.35366 7.22656 7.23438 8.34585 7.23438 9.72656C7.23438 11.1073 8.35366 12.2266 9.73438 12.2266Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M13.2344 17.2266L17.2344 13.2266" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    {{ __('Today Purchase') }}
                </a>
            </div>
        </div>

        <div class="pos-main-container">
            <!-- Left Sidebar - Order Section -->
            <div class="order-sidebar">
                <div class="order-header">
                    <h4 class="order-title">{{ __('Purchase') }} #{{ $invoice_no }}</h4>
                    <input type="hidden" name="invoiceNumber" value="{{ $invoice_no }}">
                    <p class="order-date">{{ now()->format('d M, Y') }}</p>
                    <input type="hidden" name="purchaseDate" value="{{ now()->format('Y-m-d') }}">
                </div>

                <div class="supplier-section">
                    <div class="supplier-select-wrapper">
                        <select required name="party_id" id="supplier_id" class="form-select supplier-select choices-select">
                            <option value="">{{ __('Search Supplier') }}</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }} ({{ __('Due: ') }} {{ currency_format($supplier->due, currency: business_currency()) }})</option>
                            @endforeach
                        </select>
                        <a href="#supplier-create-modal" data-bs-toggle="modal" class="add-supplier-btn"><i class="fas fa-plus"></i></a>
                    </div>
                </div>

                <div class="cart-section">
                    <div class="cart-header">
                        <span class="cart-title">{{ __('Products') }}</span>
                        <button type="button" class="clear-cart-btn cancel-sale-btn" data-route="{{ route('business.carts.remove-all') }}">{{ __('Clear All') }} <i class="fas fa-trash-alt"></i></button>
                    </div>
                    <div class="cart-items-list" id="purchase_cart_list">@include('business::purchases.cart-list-new')</div>
                </div>

                <div class="order-summary">
                    <div class="summary-row"><span>{{ __('Items') }}</span><span id="items_count">0</span></div>
                    <div class="summary-row"><span>{{ __('Subtotal') }}</span><span id="sub_total">{{ currency_format(0, currency: business_currency()) }}</span></div>
                    <div class="summary-row"><span>{{ __('Discount') }}</span><span id="discount_display">0</span></div>
                    <div class="summary-row"><span>{{ __('Taxes') }}</span><span id="vat_display">0</span></div>
                    <div class="summary-row"><span>{{ __('Shipping') }}</span><span id="shipping_display">0</span></div>
                    <div class="summary-row total"><span>{{ __('Total') }}</span><span id="total_amount">{{ currency_format(0, currency: business_currency()) }}</span></div>
                </div>

                <button type="button" class="cancel-order-btn cancel-sale-btn" data-route="{{ route('business.carts.remove-all') }}">{{ __('Cancel Order') }}</button>

                <div class="payment-section-new">
                    <div class="payment-field"><label>{{ __('Receive Amount') }}</label><input name="receive_amount" type="number" step="any" id="receive_amount" min="0" placeholder="0"></div>
                    <div class="payment-field"><label>{{ __('Change Amount') }}</label><input type="number" step="any" id="change_amount" placeholder="0" readonly></div>
                    <div class="payment-field"><label>{{ __('Due Amount') }}</label><input type="number" step="any" id="due_amount" placeholder="0" readonly></div>
                    <div class="payment-field"><label>{{ __('Payment Type') }}</label><select name="payment_type_id">@foreach ($payment_types as $type)<option value="{{ $type->id }}">{{ $type->name }}</option>@endforeach</select></div>
                    <div class="hidden-cart-inputs">
                        <select name="vat_id" class="vat_select"><option value="">{{ __('Select') }}</option>@foreach ($vats as $vat)<option value="{{ $vat->id }}" data-rate="{{ $vat->rate }}">{{ $vat->name }} ({{ $vat->rate }}%)</option>@endforeach</select>
                        <input type="number" step="any" name="vat_amount" id="vat_amount" min="0" value="0">
                        <select name="discount_type" class="discount_type"><option value="flat">{{ __('Flat') }}</option><option value="percent">{{ __('Percent') }}</option></select>
                        <input type="number" step="any" name="discountAmount" id="discount_amount" min="0" value="0">
                        <input type="number" step="any" name="shipping_charge" id="shipping_charge" value="0">
                    </div>
                    @usercan('purchases.create')<button type="submit" class="save-order-btn">{{ __('Save Purchase') }}</button>@endusercan
                </div>
            </div>

            <!-- Right Side - Products Section -->
            <div class="products-section">
                <div class="products-header-new">
                    <h4 class="products-title">{{ __('Products') }}</h4>
                    <button type="button" class="filters-btn"><i class="fas fa-sliders-h"></i> {{ __('Filters') }}</button>
                </div>
                <div class="product-search-wrapper">
                    <form action="{{ route('business.purchases.product-filter') }}" method="post" class="product-filter product-filter-form" table="#products-list">@csrf<input type="text" name="search" id="purchase_product_search" class="product-search-input search-input" placeholder="{{ __('Search Product') }}"></form>
                </div>
                <div class="products-grid-wrapper"><div class="products-grid" id="products-list">@include('business::purchases.product-list-new')</div></div>
            </div>
        </div>

        @php $currency = business_currency(); @endphp
        <input type="hidden" id="currency_symbol" value="{{ $currency->symbol }}">
        <input type="hidden" id="currency_position" value="{{ $currency->position }}">
        <input type="hidden" id="currency_code" value="{{ $currency->code }}">
        <input type="hidden" id="get_product" value="{{ route('business.products.prices') }}">
        <input type="hidden" value="{{ route('business.purchases.cart') }}" id="purchase-cart">
        <input type="hidden" value="{{ route('business.carts.remove-all') }}" id="clear-cart">
    </form>
@endsection

@push('modal')
    @include('business::purchases.calculator')
    @include('business::purchases.category-search')
    @include('business::purchases.brand-search')
    @include('business::purchases.supplier-create')
    @include('business::purchases.bulk-upload.index')
    @include('business::purchases.product-modal')
@endpush

@push('js')
    <script src="{{ asset('assets/js/choices.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom/purchase.js') }}"></script>
    <script src="{{ asset('assets/js/custom/math.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom/calculator.js') }}"></script>
@endpush