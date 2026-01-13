@extends('layouts.business.pos')

@section('title')
    {{ __('Pos Sale') }}
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/choices.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/calculator.css') }}">
    <style>
        .pos-fullscreen-body { margin: 0; padding: 0; background: #f5f5f5; }
        .pos-fullscreen-wrapper { width: 100%; min-height: 100vh; display: flex; flex-direction: column; }
        .pos-top-header {padding: 12px 24px 0px 24px; display: flex; justify-content: space-between; align-items: center; }
        .pos-brand { display: flex; align-items: center; gap: 12px; }
        .pos-brand-title { font-size: 20px; font-weight: 700; color: #1a1a1a; margin: 0; }
        .pos-brand-subtitle { font-size: 12px; color: #6b7280; margin: 0; }
        .pos-top-nav { background: white; padding: 8px 8px; border-radius: 100px;  display: flex; align-items: center; gap: 8px; }
        .pos-nav-btn { width: 40px; height: 40px; border-radius: 8px; border: none; background: #fff; display: flex; align-items: center; justify-content: center; color: #374151; cursor: pointer; transition: all 0.2s; text-decoration: none; flex-shrink: 0; }
        .pos-nav-btn:hover { background: #f9fafb; color: #1a1a1a; }
        .pos-nav-btn i { font-size: 16px; }
        .pos-nav-btn svg { width: 20px; height: 20px; flex-shrink: 0; }
        .pos-nav-divider { width: 1px; height: 24px; background: #e5e7eb; margin: 0 8px; }
        .pos-add-expense-btn { display: flex; align-items: center; gap: 8px; padding: 12px 24px; background: #FF6500; border: none; border-radius: 100px; color: #fff; font-size: 16px; font-weight: 600; cursor: pointer; text-decoration: none; }
        .pos-add-expense-btn:hover { background: #e55a00; color: #fff; }
        .pos-add-expense-btn svg { width: 24px; height: 24px; flex-shrink: 0; }
        .pos-main-container { display: grid; grid-template-columns: 420px 1fr; gap: 20px; padding: 20px; background: #f5f5f5; }
        @media (max-width: 1200px) { .pos-main-container { grid-template-columns: 380px 1fr; } }
        @media (max-width: 992px) { .pos-main-container { grid-template-columns: 1fr; } }
        .order-sidebar { background: #fff; border-radius: 12px; padding: 20px; display: flex; flex-direction: column; border-right: none; }
        .order-header { margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #f0f0f0; }
        .order-title { font-size: 18px; font-weight: 600; color: #1a1a1a; margin: 0; }
        .order-date { font-size: 13px; color: #666; margin-top: 4px; }
        .customer-section { margin-bottom: 20px; }
        .customer-select-wrapper { display: flex; gap: 10px; align-items: center; }
        .customer-select-wrapper .form-select, .customer-select-wrapper .choices { flex: 1; }
        .add-customer-btn { width: 40px; height: 40px; border-radius: 8px; background: #FF6500; border: none; display: flex; align-items: center; justify-content: center; color: #fff; flex-shrink: 0; }
        .add-customer-btn:hover { background: #e55a00; }
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
        .remove-item-btn { width: 22px; height: 22px; border-radius: 50%; background: transparent; border: 2px solid #dc3545; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #dc3545; font-size: 10px; }
        .remove-item-btn:hover { background: #dc3545; color: #fff; }
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
        .product-card-body { padding: 12px; display: flex; flex-direction: column; flex: 1; }
        .product-card-row { display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; margin-bottom: 8px; }
        .product-card-info { flex: 1; min-width: 0; }
        .product-card-name { font-size: 14px; font-weight: 500; color: #1a1a1a; margin: 0 0 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .product-card-code { font-size: 12px; color: #9ca3af; margin: 0; }
        .product-card-price { font-size: 14px; font-weight: 600; color: #1a1a1a; white-space: nowrap; }
        .add-product-btn { width: 32px; height: 32px; border-radius: 50%; background: #FF6500; border: none; display: flex; align-items: center; justify-content: center; color: #fff; cursor: pointer; flex-shrink: 0; align-self: flex-end; margin-top: auto; }
        .guest-phone-field { margin-top: 10px; }
        .hidden-cart-inputs { display: none; }
    </style>
@endpush

@section('main_content')
    <form action="{{ route('business.sales.store') }}" method="post" enctype="multipart/form-data" class="ajaxform pos-fullscreen-form">
        @csrf
        
        <!-- Top Header Bar -->
        <div class="pos-top-header">
            <div class="pos-brand">
                <div>
                    <p class="pos-brand-subtitle">{{ auth()->user()->business->companyName ?? __('Business') }}</p>
                    <h1 class="pos-brand-title">POS</h1>
                </div>
            </div>

            <div class="pos-top-nav">
                <a href="{{ route('business.dashboard.index') }}" class="pos-nav-btn" title="{{ __('Back') }}">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.0249 7.30624C11.3026 7.08951 11.7843 6.7256 11.9457 6.60372C12.2792 6.35808 12.3505 5.88857 12.1048 5.55506C11.8592 5.22154 11.3892 5.15065 11.0557 5.39628L11.0535 5.39793C10.8846 5.52553 10.3855 5.90245 10.102 6.12372C9.53324 6.5676 8.77234 7.17803 8.00925 7.84058C7.24988 8.4999 6.47119 9.22557 5.87644 9.89869C5.57982 10.2344 5.3131 10.5747 5.11648 10.9008C4.93226 11.2064 4.75098 11.5933 4.75098 11.9998C4.75097 12.4063 4.93226 12.7932 5.11647 13.0988C5.3131 13.425 5.57982 13.7653 5.87643 14.101C6.47119 14.7741 7.24989 15.4997 8.00927 16.1591C8.77237 16.8216 9.53327 17.4321 10.1021 17.8759C10.3853 18.097 10.8841 18.4738 11.0535 18.6017L11.0562 18.6037C11.3897 18.8494 11.8592 18.7781 12.1049 18.4446C12.3505 18.1111 12.2786 17.6411 11.945 17.3954C11.7837 17.2735 11.3026 16.9101 11.0249 16.6934C10.4687 16.2593 9.72958 15.6662 8.99268 15.0264C8.25205 14.3834 7.53075 13.7079 7.00051 13.1078C6.73463 12.8068 6.5326 12.5425 6.4011 12.3244C6.27743 12.1192 6.25171 12.0016 6.25171 12.0016C6.25171 12.0016 6.27743 11.8804 6.4011 11.6753C6.5326 11.4571 6.73463 11.1928 7.00051 10.8919C7.53076 10.2918 8.25205 9.61627 8.99267 8.97323C9.72956 8.33342 10.4687 7.74031 11.0249 7.30624Z" fill="currentColor"/><path d="M18.0249 7.30624C18.3026 7.08951 18.7843 6.7256 18.9457 6.60372C19.2792 6.35808 19.3505 5.88857 19.1048 5.55506C18.8592 5.22154 18.3892 5.15065 18.0557 5.39628L18.0535 5.39793C17.8846 5.52551 17.3856 5.90243 17.102 6.12372C16.5332 6.5676 15.7723 7.17803 15.0092 7.84058C14.2499 8.4999 13.4712 9.22557 12.8764 9.89869C12.5798 10.2344 12.3131 10.5747 12.1165 10.9008C11.9323 11.2064 11.751 11.5933 11.751 11.9998C11.751 12.4063 11.9323 12.7932 12.1165 13.0988C12.3131 13.425 12.5798 13.7653 12.8764 14.101C13.4712 14.7741 14.2499 15.4997 15.0093 16.1591C15.7724 16.8216 16.5333 17.4321 17.1021 17.8759C17.3854 18.097 17.8843 18.4739 18.0536 18.6018L18.0562 18.6037C18.3897 18.8494 18.8592 18.7781 19.1049 18.4446C19.3505 18.1111 19.2786 17.6411 18.945 17.3954C18.7837 17.2735 18.3026 16.9101 18.0249 16.6934C17.4687 16.2593 16.7296 15.6662 15.9927 15.0264C15.2521 14.3834 14.5308 13.7079 14.0005 13.1078C13.7346 12.8068 13.5326 12.5425 13.4011 12.3244C13.2774 12.1192 13.2517 12.0016 13.2517 12.0016C13.2517 12.0016 13.2774 11.8804 13.4011 11.6753C13.5326 11.4571 13.7346 11.1928 14.0005 10.8919C14.5308 10.2918 15.252 9.61627 15.9927 8.97323C16.7296 8.33342 17.4687 7.74031 18.0249 7.30624Z" fill="currentColor"/></svg>
                </a>
                <a href="{{ route('business.products.index') }}" class="pos-nav-btn" title="{{ __('Products') }}">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.8327 18.3327C10.1508 18.3327 9.49952 18.0483 8.19677 17.4797C6.67629 16.816 5.5123 16.3079 4.70475 15.8327H1.66602M10.8327 18.3327C11.5145 18.3327 12.1658 18.0483 13.4686 17.4797C16.7113 16.0643 18.3327 15.3565 18.3327 14.166V5.41602M10.8327 18.3327V9.16602M3.33268 5.41602V7.91602" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/><path d="M7.77225 8.0755L5.33792 6.89756C4.00196 6.2511 3.33398 5.92787 3.33398 5.41602C3.33398 4.90416 4.00196 4.58093 5.33792 3.93447L7.77225 2.75653C9.27465 2.02952 10.0259 1.66602 10.834 1.66602C11.6421 1.66602 12.3933 2.02952 13.8957 2.75653L16.3301 3.93447C17.666 4.58093 18.334 4.90416 18.334 5.41602C18.334 5.92787 17.666 6.2511 16.3301 6.89756L13.8957 8.0755C12.3933 8.80252 11.6421 9.16602 10.834 9.16602C10.0259 9.16602 9.27465 8.80252 7.77225 8.0755Z" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/><path d="M15.1145 3.3457L6.55664 7.48672" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/><path d="M1.66602 10.834H4.16602" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/><path d="M1.66602 13.334H4.16602" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
                <a href="{{ route('business.sales.index', ['today' => true]) }}" class="pos-nav-btn" title="{{ __('Today Sales') }}">
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
                    <svg width="18" height="15" viewBox="0 0 18 15" fill="none" xmlns="http://www.w3.org/2000/svg">
<mask id="path-1-inside-1_3878_47878" fill="white">
<path d="M9.00009 8.1414C8.94309 8.1414 8.88669 8.1288 8.83089 8.1042L0.260492 4.269C0.0990918 4.197 -0.00350824 4.032 9.17575e-05 3.8478C0.00369176 3.666 0.114692 3.501 0.276092 3.438L8.84769 0.0293999C8.89689 0.0101999 8.94789 0 9.00009 0C9.05169 0 9.10329 0.0101999 9.15249 0.0293999L17.7235 3.4368C17.8885 3.5028 17.9965 3.6636 18.0001 3.8466C18.0049 4.0314 17.9023 4.197 17.7397 4.269L9.16869 8.106C9.11529 8.1294 9.05769 8.1414 9.00009 8.1414ZM1.57569 3.8808L8.97909 7.194L16.4245 3.8808L9.00009 0.9294L1.57569 3.8808Z"/>
</mask>
<path d="M9.00009 8.1414C8.94309 8.1414 8.88669 8.1288 8.83089 8.1042L0.260492 4.269C0.0990918 4.197 -0.00350824 4.032 9.17575e-05 3.8478C0.00369176 3.666 0.114692 3.501 0.276092 3.438L8.84769 0.0293999C8.89689 0.0101999 8.94789 0 9.00009 0C9.05169 0 9.10329 0.0101999 9.15249 0.0293999L17.7235 3.4368C17.8885 3.5028 17.9965 3.6636 18.0001 3.8466C18.0049 4.0314 17.9023 4.197 17.7397 4.269L9.16869 8.106C9.11529 8.1294 9.05769 8.1414 9.00009 8.1414ZM1.57569 3.8808L8.97909 7.194L16.4245 3.8808L9.00009 0.9294L1.57569 3.8808Z" fill="black"/>
<path d="M8.83089 8.1042L8.01396 9.92978L8.0241 9.93425L8.83089 8.1042ZM0.260492 4.269L1.07742 2.44345L1.07529 2.4425L0.260492 4.269ZM9.17575e-05 3.8478L-1.99952 3.8082L-1.99953 3.80872L9.17575e-05 3.8478ZM0.276092 3.438L1.00334 5.30114L1.01513 5.29645L0.276092 3.438ZM8.84769 0.0293999L8.12059 -1.83379L8.10866 -1.82905L8.84769 0.0293999ZM9.15249 0.0293999L9.89136 -1.82916L9.87958 -1.83376L9.15249 0.0293999ZM17.7235 3.4368L18.4663 1.57984L18.4623 1.57828L17.7235 3.4368ZM18.0001 3.8466L16.0004 3.88594L16.0008 3.89853L18.0001 3.8466ZM17.7397 4.269L16.9299 2.44025L16.9225 2.44357L17.7397 4.269ZM9.16869 8.106L9.97141 9.93784L9.97866 9.93466L9.98589 9.93143L9.16869 8.106ZM1.57569 3.8808L0.836874 2.02227L-3.56409 3.77177L0.758723 5.70633L1.57569 3.8808ZM8.97909 7.194L8.16212 9.01953L8.97679 9.38411L9.79222 9.02125L8.97909 7.194ZM16.4245 3.8808L17.2376 5.70805L21.5772 3.77692L17.1633 2.02227L16.4245 3.8808ZM9.00009 0.9294L9.73891 -0.929134L9.00009 -1.22283L8.26127 -0.929134L9.00009 0.9294ZM9.00009 8.1414V6.1414C9.2466 6.1414 9.46389 6.19753 9.63769 6.27415L8.83089 8.1042L8.0241 9.93425C8.30949 10.0601 8.63958 10.1414 9.00009 10.1414V8.1414ZM8.83089 8.1042L9.64781 6.27865L1.07741 2.44345L0.260492 4.269L-0.556431 6.09455L8.01397 9.92975L8.83089 8.1042ZM0.260492 4.269L1.07529 2.4425C1.6807 2.71257 2.01109 3.30437 1.99971 3.88688L9.17575e-05 3.8478L-1.99953 3.80872C-2.01811 4.75963 -1.48252 5.68143 -0.554304 6.0955L0.260492 4.269ZM9.17575e-05 3.8478L1.9997 3.8874C1.98834 4.46081 1.64515 5.05057 1.00332 5.3011L0.276092 3.438L-0.45114 1.5749C-1.41577 1.95143 -1.98096 2.87119 -1.99952 3.8082L9.17575e-05 3.8478ZM0.276092 3.438L1.01513 5.29645L9.58673 1.88785L8.84769 0.0293999L8.10866 -1.82905L-0.462942 1.57955L0.276092 3.438ZM8.84769 0.0293999L9.57478 1.89256C9.4083 1.95752 9.21333 2 9.00009 2V0V-2C8.68245 -2 8.38549 -1.93712 8.12061 -1.83376L8.84769 0.0293999ZM9.00009 0V2C8.7877 2 8.59333 1.95808 8.42541 1.89256L9.15249 0.0293999L9.87958 -1.83376C9.61326 -1.93769 9.31568 -2 9.00009 -2V0ZM9.15249 0.0293999L8.41364 1.88792L16.9846 5.29532L17.7235 3.4368L18.4623 1.57828L9.89135 -1.82912L9.15249 0.0293999ZM17.7235 3.4368L16.9807 5.29375C16.3647 5.04733 16.0119 4.46786 16.0005 3.88594L18.0001 3.8466L19.9997 3.80726C19.9811 2.85934 19.4123 1.95827 18.4663 1.57985L17.7235 3.4368ZM18.0001 3.8466L16.0008 3.89853C15.9854 3.30865 16.3199 2.71039 16.9299 2.44027L17.7397 4.269L18.5495 6.09773C19.4847 5.68361 20.0243 4.75415 19.9994 3.79467L18.0001 3.8466ZM17.7397 4.269L16.9225 2.44357L8.3515 6.28057L9.16869 8.106L9.98589 9.93143L18.5569 6.09443L17.7397 4.269ZM9.16869 8.106L8.36597 6.27416C8.56551 6.18672 8.78121 6.1414 9.00009 6.1414V8.1414V10.1414C9.33417 10.1414 9.66508 10.0721 9.97141 9.93784L9.16869 8.106ZM1.57569 3.8808L0.758723 5.70633L8.16212 9.01953L8.97909 7.194L9.79606 5.36847L2.39266 2.05527L1.57569 3.8808ZM8.97909 7.194L9.79222 9.02125L17.2376 5.70805L16.4245 3.8808L15.6114 2.05355L8.16597 5.36675L8.97909 7.194ZM16.4245 3.8808L17.1633 2.02227L9.73891 -0.929134L9.00009 0.9294L8.26127 2.78793L15.6857 5.73933L16.4245 3.8808ZM9.00009 0.9294L8.26127 -0.929134L0.836874 2.02227L1.57569 3.8808L2.31451 5.73933L9.73891 2.78793L9.00009 0.9294Z" fill="black" mask="url(#path-1-inside-1_3878_47878)"/>
<mask id="path-3-inside-2_3878_47878" fill="white">
<path d="M0.259791 7.49404C0.0431913 7.39624 -0.0582086 7.13164 0.0341914 6.90424C0.101991 6.73924 0.257391 6.63184 0.429591 6.63184C0.487791 6.63184 0.544191 6.64444 0.598791 6.66904L8.97899 10.4184L17.4012 6.66904C17.4564 6.64384 17.5128 6.63184 17.571 6.63184C17.7426 6.63184 17.8974 6.73804 17.9646 6.90424C18.0576 7.13164 17.9562 7.39624 17.739 7.49404L8.99939 11.4048L0.259791 7.49404Z"/>
</mask>
<path d="M0.259791 7.49404C0.0431913 7.39624 -0.0582086 7.13164 0.0341914 6.90424C0.101991 6.73924 0.257391 6.63184 0.429591 6.63184C0.487791 6.63184 0.544191 6.64444 0.598791 6.66904L8.97899 10.4184L17.4012 6.66904C17.4564 6.64384 17.5128 6.63184 17.571 6.63184C17.7426 6.63184 17.8974 6.73804 17.9646 6.90424C18.0576 7.13164 17.9562 7.39624 17.739 7.49404L8.99939 11.4048L0.259791 7.49404Z" fill="black"/>
<path d="M0.259791 7.49404L-0.563251 9.31685L-0.557111 9.3196L0.259791 7.49404ZM0.0341914 6.90424L-1.81574 6.14408L-1.81869 6.15135L0.0341914 6.90424ZM0.598791 6.66904L-0.222774 8.49251L-0.218007 8.49464L0.598791 6.66904ZM8.97899 10.4184L8.16219 12.244L8.97695 12.6086L9.79239 12.2456L8.97899 10.4184ZM17.4012 6.66904L18.2146 8.49616L18.2232 8.49233L18.2318 8.48841L17.4012 6.66904ZM17.9646 6.90424L16.1104 7.65394L16.1134 7.66131L17.9646 6.90424ZM17.739 7.49404L18.5559 9.3196L18.5601 9.31769L17.739 7.49404ZM8.99939 11.4048L8.18249 13.2304L8.99939 13.5959L9.81629 13.2304L8.99939 11.4048ZM0.259791 7.49404L1.08283 5.67123C1.89553 6.03819 2.17847 6.93998 1.88707 7.65712L0.0341914 6.90424L-1.81869 6.15135C-2.29489 7.3233 -1.80915 8.75428 -0.563247 9.31684L0.259791 7.49404ZM0.0341914 6.90424L1.8841 7.66438C1.65995 8.20988 1.11037 8.63184 0.429591 8.63184V6.63184V4.63184C-0.595587 4.63184 -1.45597 5.26859 -1.81572 6.14409L0.0341914 6.90424ZM0.429591 6.63184V8.63184C0.189434 8.63184 -0.0332322 8.5779 -0.222771 8.4925L0.598791 6.66904L1.42035 4.84557C1.12161 4.71097 0.786149 4.63184 0.429591 4.63184V6.63184ZM0.598791 6.66904L-0.218007 8.49464L8.16219 12.244L8.97899 10.4184L9.79579 8.59283L1.41559 4.84343L0.598791 6.66904ZM8.97899 10.4184L9.79239 12.2456L18.2146 8.49616L17.4012 6.66904L16.5878 4.84191L8.16559 8.59131L8.97899 10.4184ZM17.4012 6.66904L18.2318 8.48841C18.0264 8.58219 17.7995 8.63184 17.571 8.63184V6.63184V4.63184C17.2261 4.63184 16.8864 4.70548 16.5706 4.84966L17.4012 6.66904ZM17.571 6.63184V8.63184C16.8926 8.63184 16.3354 8.21047 16.1104 7.65394L17.9646 6.90424L19.8188 6.15454C19.4593 5.2656 18.5926 4.63184 17.571 4.63184V6.63184ZM17.9646 6.90424L16.1134 7.66131C15.8187 6.94062 16.1049 6.03644 16.9178 5.67038L17.739 7.49404L18.5601 9.31769C19.8075 8.75604 20.2965 7.32266 19.8158 6.14716L17.9646 6.90424ZM17.739 7.49404L16.9221 5.66848L8.18249 9.57928L8.99939 11.4048L9.81629 13.2304L18.5559 9.3196L17.739 7.49404ZM8.99939 11.4048L9.81629 9.57928L1.07669 5.66848L0.259791 7.49404L-0.557111 9.3196L8.18249 13.2304L8.99939 11.4048Z" fill="black" mask="url(#path-3-inside-2_3878_47878)"/>
<mask id="path-5-inside-3_3878_47878" fill="white">
<path d="M0.26013 10.488C0.0429303 10.3896 -0.0584697 10.1256 0.0345303 9.89935C0.10233 9.73435 0.25773 9.62695 0.42993 9.62695C0.48813 9.62695 0.54453 9.63955 0.59913 9.66355L8.97933 13.4136L17.4015 9.66355C17.4555 9.64015 17.5131 9.62695 17.5713 9.62695C17.7429 9.62695 17.8977 9.73435 17.9649 9.89935C18.0579 10.1262 17.9565 10.3908 17.7399 10.488L8.99973 14.4L0.26013 10.488Z"/>
</mask>
<path d="M0.26013 10.488C0.0429303 10.3896 -0.0584697 10.1256 0.0345303 9.89935C0.10233 9.73435 0.25773 9.62695 0.42993 9.62695C0.48813 9.62695 0.54453 9.63955 0.59913 9.66355L8.97933 13.4136L17.4015 9.66355C17.4555 9.64015 17.5131 9.62695 17.5713 9.62695C17.7429 9.62695 17.8977 9.73435 17.9649 9.89935C18.0579 10.1262 17.9565 10.3908 17.7399 10.488L8.99973 14.4L0.26013 10.488Z" fill="black"/>
<path d="M0.26013 10.488L-0.565208 12.3097L-0.556981 12.3134L0.26013 10.488ZM0.0345303 9.89935L1.88429 10.6599L1.88444 10.6595L0.0345303 9.89935ZM0.59913 9.66355L1.41606 7.83796L1.40393 7.83263L0.59913 9.66355ZM8.97933 13.4136L8.16242 15.2391L8.97729 15.6038L9.79284 15.2406L8.97933 13.4136ZM17.4015 9.66355L16.6063 7.82844L16.5971 7.83241L16.588 7.83648L17.4015 9.66355ZM17.9649 9.89935L16.1127 10.6537L16.1145 10.6581L17.9649 9.89935ZM17.7399 10.488L18.557 12.3134L18.5588 12.3126L17.7399 10.488ZM8.99973 14.4L8.18262 16.2254L8.9997 16.5912L9.8168 16.2254L8.99973 14.4ZM0.26013 10.488L1.08546 8.66619C1.88777 9.02966 2.18464 9.92933 1.88429 10.6599L0.0345303 9.89935L-1.81523 9.13884C-2.30158 10.3218 -1.80191 11.7494 -0.5652 12.3097L0.26013 10.488ZM0.0345303 9.89935L1.88444 10.6595C1.66029 11.205 1.11071 11.627 0.42993 11.627V9.62695V7.62695C-0.595248 7.62695 -1.45563 8.26371 -1.81538 9.13921L0.0345303 9.89935ZM0.42993 9.62695V11.627C0.190561 11.627 -0.026064 11.5734 -0.205673 11.4945L0.59913 9.66355L1.40393 7.83263C1.11512 7.70568 0.7857 7.62695 0.42993 7.62695V9.62695ZM0.59913 9.66355L-0.217777 11.4891L8.16242 15.2391L8.97933 13.4136L9.79624 11.588L1.41604 7.838L0.59913 9.66355ZM8.97933 13.4136L9.79284 15.2406L18.215 11.4906L17.4015 9.66355L16.588 7.83648L8.16582 11.5865L8.97933 13.4136ZM17.4015 9.66355L18.1967 11.4987C18.0283 11.5716 17.8162 11.627 17.5713 11.627V9.62695V7.62695C17.21 7.62695 16.8827 7.70866 16.6063 7.82844L17.4015 9.66355ZM17.5713 9.62695V11.627C16.8861 11.627 16.3354 11.2007 16.1127 10.6537L17.9649 9.89935L19.8172 9.14497C19.46 8.26797 18.5997 7.62695 17.5713 7.62695V9.62695ZM17.9649 9.89935L16.1145 10.6581C15.8187 9.93678 16.1043 9.0298 16.9211 8.66326L17.7399 10.488L18.5588 12.3126C19.8088 11.7517 20.2972 10.3155 19.8154 9.14056L17.9649 9.89935ZM17.7399 10.488L16.9229 8.66247L8.18267 12.5745L8.99973 14.4L9.8168 16.2254L18.557 12.3134L17.7399 10.488ZM8.99973 14.4L9.81684 12.5745L1.07724 8.66249L0.26013 10.488L-0.556981 12.3134L8.18262 16.2254L8.99973 14.4Z" fill="black" mask="url(#path-5-inside-3_3878_47878)"/>
</svg>

                </button>
                <!-- <div class="pos-nav-divider"></div> -->
                <a href="{{ route('business.expenses.index') }}" class="pos-add-expense-btn">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.3173 4.67954C13.5351 4.11073 15.1742 3.75 16.9999 3.75C18.0119 3.75 18.97 3.86093 19.8324 4.05847C20.441 4.19786 20.5725 4.23831 20.7755 4.39894C20.8875 4.48756 21.0672 4.71303 21.1286 4.84199C21.2351 5.06576 21.2499 5.27149 21.2499 6.11397V11.5C21.2499 11.9142 21.5857 12.25 21.9999 12.25C22.4141 12.25 22.7499 11.9142 22.7499 11.5L22.75 6.00845C22.7506 5.31827 22.7512 4.76081 22.483 4.19734C22.3229 3.86108 21.9983 3.45372 21.7063 3.22264C21.2181 2.83636 20.7726 2.73459 20.2257 2.60967L20.1673 2.59633C19.1904 2.37257 18.1197 2.25 16.9999 2.25C14.9914 2.25 13.1297 2.64454 11.6825 3.32046C10.4646 3.88927 8.82554 4.25 6.99989 4.25C5.98789 4.25 5.02978 4.13907 4.16734 3.94153C2.84089 3.63772 1.24989 4.54678 1.24989 6.11397L1.24982 16.9915C1.24915 17.6817 1.24861 18.2392 1.5168 18.8027C1.67685 19.1389 2.00145 19.5463 2.2935 19.7774C2.78166 20.1636 3.22719 20.2654 3.77408 20.3903L3.83244 20.4037C4.80938 20.6274 5.88005 20.75 6.99989 20.75C9.00838 20.75 10.8701 20.3555 12.3173 19.6795C12.6926 19.5043 12.8547 19.0579 12.6794 18.6826C12.5041 18.3073 12.0578 18.1452 11.6825 18.3205C10.4646 18.8893 8.82554 19.25 6.99989 19.25C5.98789 19.25 5.02978 19.1391 4.16734 18.9415C3.55875 18.8021 3.42725 18.7617 3.22426 18.6011C3.11226 18.5124 2.93259 18.287 2.87121 18.158C2.7647 17.9342 2.74989 17.7285 2.74989 16.886V6.11397C2.74989 5.7115 3.23895 5.26774 3.83244 5.40367C4.80938 5.62743 5.88005 5.75 6.99989 5.75C9.00838 5.75 10.8701 5.35546 12.3173 4.67954Z" fill="currentColor"/><path d="M19.2499 14C19.2499 13.5858 18.9141 13.25 18.4999 13.25C18.0857 13.25 17.7499 13.5858 17.7499 14V16.75H14.9999C14.5857 16.75 14.2499 17.0858 14.2499 17.5C14.2499 17.9142 14.5857 18.25 14.9999 18.25H17.7499V21C17.7499 21.4142 18.0857 21.75 18.4999 21.75C18.9141 21.75 19.2499 21.4142 19.2499 21V18.25H21.9999C22.4141 18.25 22.7499 17.9142 22.7499 17.5C22.7499 17.0858 22.4141 16.75 21.9999 16.75H19.2499V14Z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M8.74989 11.5C8.74989 9.70507 10.205 8.25 11.9999 8.25C13.7948 8.25 15.2499 9.70507 15.2499 11.5C15.2499 13.2949 13.7948 14.75 11.9999 14.75C10.205 14.75 8.74989 13.2949 8.74989 11.5ZM11.9999 9.75C11.0334 9.75 10.2499 10.5335 10.2499 11.5C10.2499 12.4665 11.0334 13.25 11.9999 13.25C12.9664 13.25 13.7499 12.4665 13.7499 11.5C13.7499 10.5335 12.9664 9.75 11.9999 9.75Z" fill="currentColor"/><path d="M6.49989 12.5C6.49989 11.9477 6.05217 11.5 5.49989 11.5C4.9476 11.5 4.49989 11.9477 4.49989 12.5V12.509C4.49989 13.0613 4.9476 13.509 5.49989 13.509C6.05217 13.509 6.49989 13.0613 6.49989 12.509V12.5Z" fill="currentColor"/></svg>
                    {{ __('Add Expense') }}
                </a>
            </div>
        </div>

        <div class="pos-main-container">
            <!-- Left Sidebar - Order Section -->
            <div class="order-sidebar">
                <div class="order-header">
                    <h4 class="order-title">{{ __('Order') }} #{{ $invoice_no }}</h4>
                    <input type="hidden" name="invoiceNumber" value="{{ $invoice_no }}">
                    <p class="order-date">{{ now()->format('d M, Y') }}</p>
                    <input type="hidden" name="saleDate" value="{{ now()->format('Y-m-d') }}">
                </div>

                <div class="customer-section">
                    <div class="customer-select-wrapper">
                        <select required name="party_id" id="party_id" class="form-select customer-select choices-select">
                            <option value="">{{ __('Search Customer') }}</option>
                            <option class="guest-option" value="guest">{{ __('Guest') }}</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}" data-type="{{ $customer->type }}" data-phone="{{ $customer->phone }}">{{ $customer->name }}({{ $customer->type }}{{ $customer->due ? ' ' . currency_format($customer->due, currency: business_currency()) : '' }}) {{ $customer->phone }}</option>
                            @endforeach
                        </select>
                        <a href="#customer-create-modal" data-bs-toggle="modal" class="add-customer-btn"><i class="fas fa-plus"></i></a>
                    </div>
                    <div class="guest-phone-field d-none guest_phone">
                        <input type="text" name="customer_phone" class="form-control" placeholder="{{ __('Enter Customer Phone Number') }}">
                    </div>
                </div>

                <div class="cart-section">
                    <div class="cart-header">
                        <span class="cart-title">{{ __('Products') }}</span>
                        <button type="button" class="clear-cart-btn cancel-sale-btn" data-route="{{ route('business.carts.remove-all') }}">{{ __('Clear All') }} <i class="fas fa-trash-alt"></i></button>
                    </div>
                    <div class="cart-items-list" id="cart-list">@include('business::sales.cart-list-new')</div>
                </div>

                <div class="order-summary">
                    <div class="summary-row"><span>{{ __('Items') }}</span><span id="items_count">0</span></div>
                    <div class="summary-row"><span>{{ __('Subtotal') }}</span><span id="sub_total">{{ currency_format(0, currency: business_currency()) }}</span></div>
                    <div class="summary-row"><span>{{ __('Discount') }}</span><span id="discount_display">0</span></div>
                    <div class="summary-row"><span>{{ __('Taxes') }}</span><span id="vat_display">0</span></div>
                    <div class="summary-row"><span>{{ __('Shipping') }}</span><span id="shipping_display">0</span></div>
                    <div class="summary-row"><span>{{ __('Rounding (-/+)') }}</span><span id="rounding_amount">0</span></div>
                    <div class="summary-row total"><span>{{ __('Total') }}</span><span id="total_amount">{{ currency_format(0, currency: business_currency()) }}</span></div>
                </div>

                <button type="button" class="cancel-order-btn cancel-sale-btn" data-route="{{ route('business.carts.remove-all') }}">{{ __('Cancel Order') }}</button>

                <div class="payment-section-new">
                    <div class="payment-field"><label>{{ __('Receive Amount') }}</label><input name="receive_amount" type="number" step="any" id="receive_amount" min="0" placeholder="0"></div>
                    <div class="payment-field"><label>{{ __('Change Amount') }}</label><input type="number" step="any" id="change_amount" placeholder="0" readonly></div>
                    <div class="payment-field"><label>{{ __('Due Amount') }}</label><input type="number" step="any" id="due_amount" placeholder="0" readonly></div>
                    <div class="payment-field"><label>{{ __('Payment Type') }}</label><select name="payment_type_id">@foreach ($payment_types as $type)<option value="{{ $type->id }}">{{ $type->name }}</option>@endforeach</select></div>
                    <div class="payment-field"><label>{{ __('Note') }}</label><input type="text" name="note" placeholder="{{ __('Type note...') }}"></div>
                    <div class="hidden-cart-inputs">
                        <select name="vat_id" class="vat_select"><option value="">{{ __('Select') }}</option>@foreach ($vats as $vat)<option value="{{ $vat->id }}" data-rate="{{ $vat->rate }}">{{ $vat->name }} ({{ $vat->rate }}%)</option>@endforeach</select>
                        <input type="number" step="any" name="vat_amount" id="vat_amount" min="0" value="0">
                        <select name="discount_type" class="discount_type"><option value="flat">{{ __('Flat') }}</option><option value="percent">{{ __('Percent') }}</option></select>
                        <input type="number" step="any" name="discountAmount" id="discount_amount" min="0" value="0">
                        <input type="number" step="any" name="shipping_charge" id="shipping_charge" value="0">
                    </div>
                    @usercan('sales.create')<button type="submit" class="save-order-btn">{{ __('Save Order') }}</button>@endusercan
                </div>
            </div>

            <!-- Right Side - Products Section -->
            <div class="products-section">
                <div class="products-header-new">
                    <h4 class="products-title">{{ __('Products') }}</h4>
                    <button type="button" class="filters-btn"><i class="fas fa-sliders-h"></i> {{ __('Filters') }}</button>
                </div>
                <div class="product-search-wrapper">
                    <form action="{{ route('business.sales.product-filter') }}" method="post" class="product-filter product-filter-form" table="#products-list">@csrf<input type="text" name="search" id="sale_product_search" class="product-search-input" placeholder="{{ __('Search Product') }}"></form>
                </div>
                <div class="products-grid-wrapper"><div class="products-grid" id="products-list">@include('business::sales.product-list-new')</div></div>
            </div>
        </div>

        @php $currency = business_currency(); $rounding_amount_option = sale_rounding(); @endphp
        <input type="hidden" id="currency_symbol" value="{{ $currency->symbol }}">
        <input type="hidden" id="currency_position" value="{{ $currency->position }}">
        <input type="hidden" id="currency_code" value="{{ $currency->code }}">
        <input type="hidden" id="get_product" value="{{ route('business.products.prices') }}">
        <input type="hidden" value="{{ route('business.carts.index') }}" id="get-cart">
        <input type="hidden" value="{{ route('business.sales.cart-data') }}" id="get-cart-data">
        <input type="hidden" value="{{ route('business.carts.remove-all') }}" id="clear-cart">
        <input type="hidden" id="rounding_amount_option" value="{{ $rounding_amount_option }}">
        <input type="hidden" id="get-by-category" value="{{ route('business.products.get-by-category') }}">
        <input type="hidden" id="cart-store-url" value="{{ route('business.carts.store') }}">
        <input type="hidden" id="selectedProductValue" name="selectedProductValue">
        <input type="hidden" id="asset_base_url" value="{{ asset('') }}">
        <input type="hidden" id="get_stock_prices" value="{{ route('business.products.stocks-prices') }}">
        <input type="hidden" id="warehouse_module_exist" value="{{ moduleCheck('WarehouseAddon') ? 1 : 0 }}">
        <input type="hidden" id="payable_amount" value="0">
    </form>
@endsection

@push('modal')
    @include('business::sales.calculator')
    @include('business::sales.category-search')
    @include('business::sales.brand-search')
    @include('business::sales.customer-create')
    @include('business::sales.stock-list')
@endpush

@push('js')
    <script src="{{ asset('assets/js/choices.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom/sale.js') . '?v=' . time() }}"></script>
    <script src="{{ asset('assets/js/custom/math.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom/calculator.js') }}"></script>
@endpush
