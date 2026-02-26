@extends('layouts.business.pos')

@section('title')
    {{ __('Edit Sale') }}
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/choices.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/calculator.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pos-products.css') . '?v=' . time() }}">
    <style>
        /* Force RTL SVG flip */
        @if (in_array(app()->getLocale(), ['ar', 'arbh', 'eg-ar', 'fa', 'prs', 'ps', 'ur']))
        [dir="rtl"] .pos-top-nav a[href*="dashboard"] svg {
            transform: scaleX(-1) !important;
        }
        @endif
        .pos-fullscreen-body { margin: 0; padding: 0; background: #f5f5f5; }
        .pos-fullscreen-wrapper { width: 100%; min-height: 100vh; display: flex; flex-direction: column; }
        .pos-top-header {padding: 12px 24px 0px 24px; display: flex; flex-wrap: wrap;  align-items: center; background:#F7F7F7 !important; }
        .pos-brand { display: flex; align-items: center; gap: 12px; }
        .pos-brand-title { font-size: 20px; font-weight: 700; color: #1a1a1a; margin: 0; }
        .pos-brand-subtitle { font-size: 12px; color: #E6E6E6; margin: 0; }
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
        .pos-main-container { display: grid; grid-template-columns: 1fr 420px; gap: 20px; padding: 20px; background: #F7F7F7 !important; }
        @media (max-width: 1200px) { .pos-main-container { grid-template-columns:1fr 380px ; } }
        @media (max-width: 992px) { .pos-main-container { grid-template-columns: 1fr; } }
        .order-sidebar { background: #fff; border-radius: 12px; padding: 20px; display: flex; flex-direction: column; border-right: none; }
        .order-header { margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #f0f0f0; }
        .order-title { font-size: 18px; font-weight: 600; color: #1a1a1a; margin: 0; }
        .order-date { font-size: 13px; color: #666;  }
        .supplier-section { margin-bottom: 20px; }
        .supplier-select-wrapper { display: flex; gap: 10px; align-items: center; }
        .supplier-select-wrapper .form-select, .supplier-select-wrapper .choices { flex: 1; }
        .add-supplier-btn { width: 40px; height: 40px; border-radius: 8px; background: #FF6500; border: none; display: flex; align-items: center; justify-content: center; color: #fff; flex-shrink: 0; }
        .add-supplier-btn:hover { background: #e55a00; }
        .customer-section { margin-bottom: 20px; }
        .customer-select-wrapper { display: flex; gap: 10px; align-items: center; }
        .customer-select-wrapper .form-select, .customer-select-wrapper .choices { flex: 1; }
        .add-customer-btn { width: 40px; height: 40px; border-radius: 8px; background: #FF6500; border: none; display: flex; align-items: center; justify-content: center; color: #fff; flex-shrink: 0; }
        .add-customer-btn:hover { background: #e55a00; }
        .guest-phone-field { margin-top: 10px; }
        .guest-phone-field input { width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; }
        .guest-phone-field input:focus { outline: none; border-color: #FF6500; }
        .cart-section { margin-bottom: 15px; display: flex; flex-direction: column; }
        .cart-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .cart-title { font-size: 16px; font-weight: 600; color: #1a1a1a; }
        .clear-cart-btn { font-size: 13px; color: #FF6500; background: none; border: none; cursor: pointer; display: flex; align-items: center; gap: 4px; }
        .cart-items-list { display: flex; flex-direction: column; gap: 10px; padding-right: 5px; }
        .cart-items-list::-webkit-scrollbar { width: 4px; }
        .cart-items-list::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
        .cart-items-list::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }
        .cart-item-card { display: flex; align-items: stretch; background: #fff; border-bottom: 1px solid #E5E7EB;  overflow: hidden; flex-shrink: 0; }
        .cart-item-image-wrapper { width: 70px; min-width: 70px; height: 70px; background: #f5f5f5; flex-shrink: 0; border-radius: 10px; margin: 6px; overflow: hidden; }
        .cart-item-image { width: 100%; height: 100%; object-fit: cover; }
        .cart-item-details { flex: 1; padding: 8px 4px; display: flex; flex-direction: column; justify-content: center; min-width: 0; }
        .cart-item-name { font-size: 14px; font-weight: 600; color: #1a1a1a; margin: 0 0 2px; line-height: 1.2; }
        .cart-item-price { font-size: 14px; color:#000000 ; margin: 0; font-weight: bold !important; }
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
        .summary-row { display: flex; justify-content: space-between; align-items: center; padding: 0px 0; font-size: 14px; color: #666; }
        .summary-row.total { font-size: 16px; font-weight: 600; color: #1a1a1a; border-top: 1px solid #eee; padding-top: 4px; margin-top: 2px; }
        .cancel-order-btn { width: 100%; padding: 8px 24px; border: 1px solid #FF6500; background: #fff; color: #FF6500; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer;  }
        .cancel-order-btn:hover { background: #fff5f5; }
        .payment-section-new { border-top: 1px solid #eee; padding-top: 20px; }
        .payment-field { margin-bottom: 15px; }
        .payment-field label { display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px; }
        .payment-field input, .payment-field select { width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; }
        .payment-field input:focus, .payment-field select:focus { outline: none; border-color: #FF6500; }
        .save-order-btn { width: 100%; padding: 14px; background: #FF6500; color: #fff; border: none; border-radius: 8px; font-size: 15px; font-weight: 500; cursor: pointer; margin-top: 10px; }
        .save-order-btn:hover { background: #e55a00; }
        .products-section { border-radius: 12px; padding: 20px; display: flex; flex-direction: column; overflow: hidden; }
        
        /* Tabs */
        .pos-tabs-wrapper { display: flex; gap: 12px; padding-bottom: 24px;  border-bottom:2px solid #f0f0f0; }
        .pos-tab-btn {    background: transparent; padding: 10px 24px; border:2px solid #f0f0f0; color: #666; border-radius: 100px; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.3s; }
        .pos-tab-btn.active { background: #FF6500; color: #fff; }
        .pos-tab-btn:hover:not(.active) {   background: transparent; }
        
        /* Category Section */
        .pos-category-section { margin-top: 15px; }
        .pos-section-title { font-size: 20px; font-weight: 700; color: #1a1a1a; margin: 0 0 16px 0; }
        .pos-category-scroll-wrapper { position: relative; }
        .pos-category-list { display: flex; gap: 16px; overflow-x: auto; scroll-behavior: smooth; padding: 8px 0; scrollbar-width: none; -ms-overflow-style: none; }
        .pos-category-list::-webkit-scrollbar { display: none; }
        
        /* Limit visible categories on small screens - show 2.25 items */
        @media (max-width: 768px) {
            .pos-category-scroll-wrapper { max-width: 100%; overflow: hidden; }
            .pos-category-list { max-width: calc((100px * 2.25) + (16px * 2.25) + 24px); } /* 2.25 items + gaps + padding */
        }
        
        @media (max-width: 576px) {
            .pos-category-list { max-width: calc((100px * 2.25) + (16px * 2.25) + 24px); } /* 2.25 items visible */
        }
        
        .pos-category-item {            display: flex; 
            flex-direction: row !important;
            align-items: center; 
            gap: 4px; 
            padding: 4px 8px !important; 
            border: 2px solid #E5E7EB; 
            background: #fff; 
            border-radius: 50px; 
            cursor: pointer; 
            transition: all 0.3s; 
            flex-shrink: 0;
            white-space: nowrap; }
        .pos-category-item:hover { border-color: #FF6500; transform: translateY(-2px); }
        .pos-category-item.active { border-color: #FF6500; background: #fff5f0; }
        .pos-category-icon { width: 56px; height: 56px; display: flex; align-items: center; justify-content: center; border-radius: 8px; background: #f9f9f9; }
        .pos-category-icon img { width: 100%; height: 100%; object-fit: contain; }
        .pos-category-icon svg { width: 32px; height: 32px; color: #666; }
        .pos-category-item.active .pos-category-icon svg { color: #FF6500; }
        .pos-category-name { font-size: 13px; font-weight: 500; color: #1a1a1a; text-align: center; }
        .pos-category-scroll-btn { position: absolute; top: 50%; transform: translateY(-50%); width: 36px; height: 36px; border-radius: 50%; background: transparent !important; border: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .pos-category-scroll-btn:hover { background: #f9fafb; }
        .pos-category-scroll-btn.prev { left: -12px; }
        .pos-category-scroll-btn.next { right: -12px; }
        
        /* Products Grid */
        .pos-products-section { flex: 1; overflow-y: auto; }
        .pos-products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 10px; padding-bottom: 5px; }
        
        /* Product Card */
        .pos-product-card { background: #fff; border: 1px solid #f0f0f0; border-radius: 8px; overflow: hidden; transition: all 0.3s; display: flex; flex-direction: column; }
        .pos-product-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); transform: translateY(-2px); }
        .pos-product-image-wrapper { width: 100%; height: auto; overflow: hidden; background: #f9f9f9; border-radius: 8px; }
        .pos-product-image { width: 100%; height: 100%; object-fit: cover; }
        .pos-product-body { padding: 16px; display: flex; flex-direction: column; gap: 12px; }
        .pos-product-header { display: flex; flex-direction: column; gap: 4px; }
        .pos-product-name { font-size: 15px; font-weight: 600; color: #1a1a1a; margin: 0; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .pos-product-desc { font-size: 12px; color: #919191; margin: 0; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .pos-product-price { font-size: 16px; font-weight: 700; color: #1a1a1a; margin: 0; }
        
        /* Category Header with Arrows */
        .pos-category-header { display: flex !important; justify-content: flex-start !important; align-items: center !important; margin-bottom: 20px !important; gap: 20px !important; }
        .pos-section-title { font-size: 22px !important; font-weight: 700 !important; color: #000 !important; margin: 0 !important; flex: 0 0 auto !important; }
        .pos-category-nav-buttons { display: flex !important; gap: 8px !important; align-items: center !important; }
        .pos-category-scroll-btn { position: static !important; transform: none !important; width: 36px !important; height: 36px !important; border-radius: 50% !important; background: #fff !important; border: 2px solid #e0e0e0 !important; display: flex !important; align-items: center !important; justify-content: center !important; cursor: pointer !important; box-shadow: none !important; transition: all 0.3s !important; flex-shrink: 0 !important; }
        .pos-category-scroll-btn:hover { background: #f5f5f5 !important; border-color: #FF6500 !important; }
        .pos-category-scroll-btn.active { background: #FF6500 !important; border-color: #FF6500 !important; }
        .pos-category-scroll-btn.active:hover { background: #e55a00 !important; }
        .pos-category-scroll-btn.disabled { opacity: 0.3 !important; cursor: not-allowed !important; pointer-events: none !important; }
        .pos-category-scroll-btn svg { color: #666 !important; width: 18px !important; height: 18px !important; }
        .pos-category-scroll-btn.active svg { color: #fff !important; }
        
        /* Product Options */
        .pos-product-options { display: flex; justify-content: space-between; align-items: center; gap: 12px; }
        .pos-option-group { display: flex; flex-direction: column; gap: 6px; }
        .pos-option-label { font-size: 16px; font-weight: bold; color: #1a1a1a; margin: 0; }
        .pos-option-buttons { display: flex; gap: 6px; }
        .pos-option-btn { width: 32px; height: 32px; border-radius: 50%; border: 1px solid #e5e7eb; background: #fff; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; font-size: 12px; font-weight: bold; color: #666; }
        .pos-option-btn:hover { border-color: #FF6500; color: #FF6500; }
        .pos-option-btn.active { border-color: #FF6500; background: #fff5f0; color: #FF6500; }
        .pos-option-btn svg { width: 16px; height: 16px; }
        
        /* Add to Cart Button */
        .pos-add-to-cart-btn { width: 100%; padding: 12px; background: #FF6500; color: #fff; border: none; border-radius: 100px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s; }
        .pos-add-to-cart-btn:hover { background: #e55a00; transform: scale(1.02); }
        .pos-add-to-cart-btn:active { transform: scale(0.98); }
        
        .hidden-cart-inputs { display: none; }
        
        /* Responsive */
        @media (max-width: 1400px) { .pos-products-grid { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); } }
        @media (max-width: 1200px) { .pos-products-grid { grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); } }
        @media (max-width: 992px) { .pos-products-grid { grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); } }
    </style>
@endpush

@section('main_content')
    <div class="container-fluid">
        <div class="grid row p-lr2 sales-main-container">
            <div class="sales-container">
                <!-- Quick Action Section -->
                <div class="quick-act-header">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center">
                        <div class="mb-2 mb-sm-0">
                            <h4 class='quick-act-title'>{{ __('Quick Action') }}</h4>
                        </div>
                        <div class="quick-actions-container">
                            <a href="{{ route('business.products.index') }}"
                               class='save-product-btn d-flex align-items-center gap-1'>
                                <img src="{{ asset('assets/images/icons/product.svg') }}" alt="">
                                {{ __('Product List') }}
                            </a>

                            <a href="{{ route('business.sales.index', ['today' => true]) }}"
                               class='sales-btn d-flex align-items-center gap-1'>
                                <img src="{{ asset('assets/images/icons/sales.svg') }}" alt="">
                                {{ __('Today Sales') }}
                            </a>

                            <button data-bs-toggle="modal" data-bs-target="#calculatorModal"
                                    class='calculator-btn d-flex align-items-center gap-1'>
                                <img src="{{ asset('assets/images/icons/calculator.svg') }}" alt="">
                                {{ __('Calculator') }}
                            </button>

                            <a href="{{ route('business.dashboard.index') }}"
                               class='dashboard-btn d-flex align-items-center gap-1'>
                                <img src="{{ asset('assets/images/icons/dashboard.svg') }}" alt="">
                                {{ __('Dashboard') }}
                            </a>
                        </div>
                    </div>
                </div>
                <form action="{{ route('business.sales.update', $sale->id) }}" method="post"
                      enctype="multipart/form-data"
                      class="ajaxform">
                    @csrf
                    @method('put')
                    <div class="mt-4 mb-3">
                        <div class="row g-3">
                            <!-- First Row -->
                            <div class="col-12 col-md-6">
                                <div class="input-group">
                                    <input type="date" name="saleDate" class="form-control"
                                           value="{{ formatted_date($sale->saleDate, 'Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <input type="text" name="invoiceNumber" value="{{ $sale->invoiceNumber }}"
                                       class="form-control" placeholder="{{ __('Invoice no') }}.">
                            </div>
                            <div class="col-12 ">
                                <div class="d-flex align-items-center">
                                    <select name="party_id" id="party_id_edit" class="form-select  choices-select customer-select"
                                            aria-label="Select Customer">
                                        <option value="">{{ __('Select Customer') }}</option>
                                        <option class="guest-option"
                                                value="guest" @selected($sale->party_id === null || $sale->party_id === 'guest')>
                                            {{ __('Guest') }}</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}" data-type="{{ $customer->type }}" data-zatca-type="{{ $customer->zatca_type ?? 'b2c' }}"
                                                    @selected($sale->party_id == $customer->id)>{{ $customer->name }}
                                                ({{ $customer->type }}{{ $customer->due ? ' ' . currency_format($customer->due, currency:business_currency()) : '' }}
                                                )
                                            </option>
                                        @endforeach
                                    </select>

                                    <a href="{{ route('business.parties.create', ['type' => 'Customer']) }}"
                                       class="btn btn-danger square-btn d-flex justify-content-center align-items-center"
                                       type="button">
                                        <img src="{{ asset('assets/images/icons/plus-square.svg') }}" alt="">
                                    </a>
                                </div>
                            </div>
                            <div
                                    class="col-12 mt-3 {{ $sale->party_id === null || $sale->party_id === 'guest' ? '' : 'd-none' }} guest_phone">
                                <input type="text" name="customer_phone" class="form-control" id="customer_phone"
                                       placeholder="{{ __('Enter Customer Phone Number') }}"
                                       value="{{ $sale->meta['customer_phone'] ?? '' }}">
                            </div>
                            
                            <!-- B2B Additional Fields Button -->
                            <div class="col-12 mt-3 {{ $sale->party && $sale->party->zatca_type === 'b2b' ? '' : 'd-none' }}" id="b2b-fields-wrapper-edit">
                                <button type="button" class="btn btn-outline-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#b2bAdditionalFieldsModal">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right: 5px;">
                                        <path d="M9 11L12 14L22 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M21 12V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V5C3.89543 5 3 5.89543 3 7V19C3 19.5523 3.44772 20 4 20H19C19.5523 20 20 19.5523 20 19V12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    {{ __('B2B Additional Fields') }}
                                </button>
                            </div>

                        </div>
                    </div>
                    <div class="cart-payment">
                        <div class="responsive-table m-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ __('Image') }}</th>
                                    <th class="text-start">{{ __('Items') }}</th>
                                    <th>{{ __('Code') }}</th>
                                    <th>{{ __('Batch') }}</th>
                                    <th>{{ __('Unit') }}</th>
                                    <th>{{ __('Sale Price') }}</th>
                                    <th>{{ __('Qty') }}</th>
                                    <th>{{ __('Sub Total') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                                </thead>
                                <tbody id="cart-list">
                                @include('business::sales.cart-list')
                                </tbody>
                            </table>
                        </div>

                        <div class="hr-container">
                            <hr>
                        </div>

                        <!-- Make Payment Section start -->
                        <div class="grid row py-3 payment-section">
                            <div class="col-sm-12 col-md-6 col-lg-6">
                                <div class="amount-info-container">
                                    <div class="row amount-container  align-items-center mb-2">
                                        <h6 class="payment-title">{{ __('Receive Amount') }}</h6>
                                        <input name="receive_amount" type="number" step="any" id="receive_amount"
                                               value="{{ $sale->change_amount + $sale->paidAmount }}" min="0"
                                               class="form-control"
                                               placeholder="0">
                                    </div>
                                    <div class="row amount-container  align-items-center mb-2">
                                        <h6 class="payment-title">{{ __('Change Amount') }}</h6>
                                        <input type="number" step="any" id="change_amount"
                                               value="{{ $sale->change_amount }}" class="form-control"
                                               placeholder="0" readonly>
                                    </div>
                                    <div class="row amount-container  align-items-center mb-2">
                                        <h6 class="payment-title">{{ __('Due Amount') }}</h6>
                                        <input type="number" step="any" id="due_amount" class="form-control"
                                               placeholder="0" readonly>
                                    </div>
                                    <div class="row amount-container  align-items-center mb-2">
                                        <h6 class="payment-title">{{ __('Payment Type') }}</h6>
                                        <select name="payment_type_id" class="form-select" id='form-ware'>
                                            @foreach($payment_types as $type)
                                                {{-- If payment_type_id does not exist compare with paymantType --}}
                                                <option value="{{ $type->id }}" @selected($sale->payment_type_id == $type->id || ($sale->payment_type_id === null && $sale->paymentType == $type->name))>
                                                    {{ $type->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="row amount-container  align-items-center mb-2">
                                        <h6 class="payment-title">{{ __('Note') }}</h6>
                                        <input type="text" name="note" value="{{ $sale->meta['note'] ?? '' }}"
                                               class="form-control" placeholder="{{ __('Type note...') }}">
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button class="save-btn cancel-sale-btn"
                                            data-route="{{ route('business.carts.remove-all') }}">{{ __('Cancel') }}</button>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-6">
                                <div class="payment-container mb-3 amount-info-container">
                                    <div class="mb-2 d-flex flex-wrap align-items-center justify-content-between">
                                        <h6>{{ __('Sub Total') }}</h6>
                                        <h6 class="fw-bold" id="sub_total">
                                            {{ currency_format(0, currency: business_currency()) }}</h6>
                                    </div>
                                    <div class="row save-amount-container  align-items-center mb-2">
                                        <h6 class="payment-title col-6">{{ __('Vat') }}</h6>
                                        <div class="col-6 w-100 d-flex justify-content-between gap-2">
                                            <div class="d-flex d-flex align-items-center gap-2">
                                                <select name="vat_id" class="form-select vat_select" id='form-ware'>
                                                    <option value="">{{ __('Select') }}</option>
                                                    @foreach($vats as $vat)
                                                        <option value="{{ $vat->id }}"
                                                                data-rate="{{ $vat->rate }}" @selected($sale->vat_id == $vat->id)>{{ $vat->name }}
                                                            ({{ $vat->rate }}%)
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <input type="number" step="any" name="vat_amount" id="vat_amount"
                                                   value="{{ ($sale->vat_amount ?? 0) != 0 ? $sale->vat_amount : (($sale->vat_percent ?? 0) != 0 ? $sale->vat_percent : 0) }}"
                                                   min="0" class="form-control right-start-input"
                                                   placeholder="{{ __('0') }}" readonly>
                                        </div>
                                    </div>

                                    <div class="row save-amount-container  align-items-center mb-2">
                                        <h6 class="payment-title col-6">{{ __('Discount') }}</h6>
                                        <div class="col-6 w-100 d-flex justify-content-between gap-2">
                                            <div class="d-flex d-flex align-items-center gap-2">
                                                <select name="discount_type" class="form-select discount_type"
                                                        id='form-ware'>
                                                    <option value="flat" @selected($sale->discount_type == 'flat')>{{ __('Flat') }}
                                                        ({!! currency_symbol_svg() !!})
                                                    </option>
                                                    <option value="percent" @selected($sale->discount_type == 'percent')>{{ __('Percent (%)') }}</option>
                                                </select>
                                            </div>
                                            <input type="number" step="any" name="discountAmount"
                                                   value="{{ $sale->discount_type == 'percent' ? $sale->discount_percent : $sale->discountAmount }}"
                                                   id="discount_amount" min="0" class="right-start-input form-control"
                                                   placeholder="{{ __('0') }}">
                                        </div>
                                    </div>

                                    <div class="row save-amount-container  align-items-center mb-2">
                                        <h6 class="payment-title col-6">{{ __('Shipping Charge') }}</h6>
                                        <div class="col-12">
                                            <input type="number" step="any" name="shipping_charge"
                                                   value="{{ $sale->shipping_charge }}" id="shipping_charge"
                                                   class="form-control right-start-input" placeholder="0">
                                        </div>
                                    </div>

                                    <div class=" d-flex flex-wrap align-items-center justify-content-between fw-bold">
                                        <div class="fw-bold">{{ __('Total Amount') }}</div>
                                        <h6 class='fw-bold' id="total_amount">
                                            {{ currency_format($sale->actual_total_amount, currency: business_currency()) }}</h6>
                                    </div>

                                    <div class="mb-2 d-flex flex-wrap align-items-center justify-content-between">
                                        <h6>{{ __('Rounding(+/-)') }}</h6>
                                        <h6 id="rounding_amount">
                                            {{ currency_format(abs($sale->rounding_amount), currency: business_currency()) }}</h6>
                                    </div>
                                    <div class="mb-2 d-flex flex-wrap align-items-center justify-content-between">
                                        <h6 class="fw-bold">{{ __('Payable Amount') }}</h6>
                                        <h6 class="fw-bold" id="payable_amount">
                                            {{ currency_format($sale->totalAmount, currency:  business_currency()) }}</h6>
                                    </div>

                                </div>
                                @usercan('sales.update')
                                <div class="mt-2">
                                    <button class="submit-btn payment-btn">{{ __('Save') }}</button>
                                </div>
                                @endusercan
                            </div>
                        </div>
                        <!-- Make Payment Section end -->
                    </div>
                </form>
            </div>
            <div class=" main-container">
                <!-- Products Header -->
                <div class="products-header">
                    <div class="container-fluid p-0">
                        <div class="row g-2 w-100 align-items-center search-product">
                            <div class="w-100">
                                <!-- Search Input and Add Button -->
                                <form action="{{ route('business.sales.product-filter') }}" method="post"
                                      class="product-filter" table="#products-list">
                                    @csrf
                                    <div class="d-flex">
                                        <input type="text" name="search" class="form-control search-input"
                                               placeholder="{{ __('Search product...') }}">
                                        <button class="btn btn-search">
                                            <i class="far fa-search"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="w-100 d-flex align-items-center gap-2">
                                <!-- Category Button -->
                                <a data-bs-toggle="offcanvas" data-bs-target="#category-search-modal"
                                   aria-controls="offcanvasRight"
                                   class="btn btn-category w-100">{{ __('Category') }}</a>
                                <!-- Brand Button -->
                                <a data-bs-toggle="offcanvas" data-bs-target="#brand-search-modal"
                                   aria-controls="offcanvasRight" class="btn btn-brand w-100">{{ __('Brand') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="products-container">
                    <div class="p-3 scroll-card">
                        <div class="search-product-card products gap-2 @if (count($products) === 1) single-product @endif product-list-container"
                             id="products-list">
                            @include('business::sales.product-list')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $currency = business_currency();
        $rounding_amount_option = sale_rounding();
    @endphp
    {{-- Hidden input fields to store currency details --}}
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

@endsection

@push('modal')
    @include('business::sales.calculator')
    @include('business::sales.category-search')
    @include('business::sales.brand-search')
    @include('business::sales.stock-list')
    @include('business::sales.partials.b2b-additional-fields')
@endpush

@push('js')
    <script src="{{ asset('assets/js/custom/sale.js') . '?v=' . time() }}"></script>
    <script src="{{ asset('assets/js/custom/calculator.js') }}"></script>
    <script src="{{ asset('assets/js/choices.min.js') }}"></script>
    
    <script>
        // Show/Hide B2B Additional Fields button based on customer type
        document.addEventListener('DOMContentLoaded', function() {
            const partySelect = document.getElementById('party_id_edit');
            const b2bFieldsWrapper = document.getElementById('b2b-fields-wrapper-edit');
            
            if (partySelect && b2bFieldsWrapper) {
                partySelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const zatcaType = selectedOption.getAttribute('data-zatca-type');
                    
                    if (zatcaType === 'b2b') {
                        b2bFieldsWrapper.classList.remove('d-none');
                    } else {
                        b2bFieldsWrapper.classList.add('d-none');
                    }
                });
            }
            
            // Pre-fill B2B fields if editing existing sale
            @if($sale->supply_date || $sale->po_number || $sale->contract_number)
                document.getElementById('supply_date').value = '{{ $sale->supply_date }}';
                document.getElementById('po_number').value = '{{ $sale->po_number }}';
                document.getElementById('contract_number').value = '{{ $sale->contract_number }}';
                document.getElementById('payment_terms').value = '{{ $sale->payment_terms }}';
                document.getElementById('payment_means').value = '{{ $sale->payment_means }}';
                document.getElementById('shipping_address_line1').value = '{{ $sale->shipping_address_line1 }}';
                document.getElementById('shipping_address_line2').value = '{{ $sale->shipping_address_line2 }}';
                document.getElementById('shipping_city').value = '{{ $sale->shipping_city }}';
                document.getElementById('shipping_postal_code').value = '{{ $sale->shipping_postal_code }}';
                document.getElementById('shipping_country_code').value = '{{ $sale->shipping_country_code }}';
            @endif
        });
    </script>
@endpush
