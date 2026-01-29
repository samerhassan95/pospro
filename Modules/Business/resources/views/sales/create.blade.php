@extends('layouts.business.pos')

@section('title')
    {{ __('Pos Sale') }}
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
        .pos-top-header {padding: 12px 24px 0px 24px; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; background:#F7F7F7 !important; }
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
        .order-date { font-size: 13px; color: #666; margin-top: 4px; }
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
        .products-section { background: #fff; border-radius: 12px; padding: 20px; display: flex; flex-direction: column; overflow: hidden; }
        
        /* Tabs */
        .pos-tabs-wrapper { display: flex; gap: 12px; padding-bottom: 24px;  border-bottom:2px solid #f0f0f0; }
        .pos-tab-btn {    background: transparent; padding: 10px 24px; border:2px solid #f0f0f0; color: #666; border-radius: 100px; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.3s; }
        .pos-tab-btn.active { background: #FF6500; color: #fff; }
        .pos-tab-btn:hover:not(.active) {   background: transparent; }
        
        /* Category Section */
        .pos-category-section { margin-bottom: 32px; }
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
        
        .pos-category-item { display: flex; flex-direction: column; align-items: center; gap: 8px; min-width: 100px; padding: 12px; border: 2px solid #f0f0f0; background: #fff; border-radius: 12px; cursor: pointer; transition: all 0.3s; flex-shrink: 0; }
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
        .pos-products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; padding-bottom: 20px; }
        
        /* Product Card */
        .pos-product-card { background: #fff; border: 1px solid #f0f0f0; border-radius: 16px; overflow: hidden; transition: all 0.3s; display: flex; flex-direction: column; }
        .pos-product-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); transform: translateY(-2px); }
        .pos-product-image-wrapper { width: 100%; height: 160px; overflow: hidden; background: #f9f9f9; border-radius: 16px 16px 0 0; }
        .pos-product-image { width: 100%; height: 100%; object-fit: cover; }
        .pos-product-body { padding: 16px; display: flex; flex-direction: column; gap: 12px; }
        .pos-product-header { display: flex; flex-direction: column; gap: 4px; }
        .pos-product-name { font-size: 15px; font-weight: 600; color: #1a1a1a; margin: 0; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .pos-product-desc { font-size: 12px; color: #999; margin: 0; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
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
        
        /* Table Reservation System */
        .tables-reservation-section { padding: 20px 0; }
        .table-management-buttons { display: flex; flex-wrap:wrap; gap: 15px; margin-bottom: 20px; }
        .btn-add-table, .btn-manage-tables { display: flex; align-items: center; gap: 8px; padding: 12px 24px; background: #FF6500; color: #fff; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s; }
        .btn-add-table:hover, .btn-manage-tables:hover { background: #e55a00; transform: translateY(-2px); }
        .btn-manage-tables { background: #374151; }
        .btn-manage-tables:hover { background: #1f2937; }
        .table-legend { display: flex; gap: 30px; margin-bottom: 30px; flex-wrap: wrap; }
        .legend-item { display: flex; align-items: center; gap: 10px; }
        .legend-color { width: 20px; height: 20px; border-radius: 50%; }
        .legend-color.utilized { background: #ef4e44; }
        .legend-color.free { background: #48f045; }
        .legend-color.blocked { background: #fff301; }
        .legend-text { font-size: 14px; color: #666; }
        
        /* Floor plan wrapper - allows cutout to extend outside */
        .floor-plan-wrapper { 
            position: relative; 
            width: 100%; 
            margin-bottom: 30px; 
        }
        
        /* Make floor plan scrollable on small screens */
        @media (max-width: 768px) {
            .floor-plan-wrapper {
                overflow-x: auto;
                overflow-y: auto;
                max-height: 80vh;
                -webkit-overflow-scrolling: touch; /* Smooth scrolling on iOS */
                border: 1px solid #e0e0e0;
                border-radius: 8px;
            }
            
            .restaurant-floor-plan {
                min-width: 800px; /* Ensure floor plan doesn't shrink too much */
                min-height: 700px;
                position: relative; /* Ensure absolute positioned items stay in place */
            }
            
            /* Ensure draggable items maintain position */
            .table-item, .area-item {
                position: absolute !important;
            }
        }
        
        @media (max-width: 576px) {
            .floor-plan-wrapper {
                max-height: 70vh;
            }
            
            .restaurant-floor-plan {
                min-width: 600px;
            }
        }
        
        .restaurant-floor-plan { position: relative; width: 100%; height: 700px; border: 3px solid #000; border-radius: 8px; background: #fff; overflow: hidden; }
        
        /* Entrance cut-out cover - positioned relative to wrapper */
        .entrance-cutout-cover { position: absolute; background: #fff; z-index: 6; pointer-events: none; }
        
        .floor-area { position: absolute; border: 2px solid #000; background: #fff; z-index: 1; cursor: move; }
        .bar-area { top: 20px; left: 20px; width: 180px; height: 140px; display: flex; align-items: center; justify-content: center; }
        .area-label { font-size: 14px; color: #666; text-align: center; pointer-events: none; }
        .toilets-wall { top: 180px; left: 20px; width: 60px; height: 180px; display: flex; align-items: center; justify-content: center; }
        .toilets-label { font-size: 12px; color: #666; z-index: 2; pointer-events: none; }
        
        .center-square { position: absolute; top: 35%; left: 45%; transform: translate(-50%, -50%); width: 100px; height: 100px; border: 2px solid #000; background: #fff; z-index: 1; cursor: move; }
        
        .entrance-area { position: absolute; display: flex; flex-direction: column; align-items: center; gap: 10px; z-index: 7; cursor: move; }
        .entrance-label { font-size: 14px; color: #666; pointer-events: none; }
        .entrance-arrow { display: none; }
        
        .table-item { position: absolute; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; z-index: 10; }
        .table-item:hover { transform: scale(1.05); }
        
        /* Table rotation support */
        .table-item[data-rotation="90"] { transform: rotate(90deg); }
        .table-item[data-rotation="180"] { transform: rotate(180deg); }
        .table-item[data-rotation="270"] { transform: rotate(270deg); }
        .table-item[data-rotation="90"]:hover { transform: rotate(90deg) scale(1.05); }
        .table-item[data-rotation="180"]:hover { transform: rotate(180deg) scale(1.05); }
        .table-item[data-rotation="270"]:hover { transform: rotate(270deg) scale(1.05); }
        
        .table-circle { width: 80px; height: 80px; border-radius: 50%; }
        .table-rounded { width: 140px; height: 80px; border-radius: 12px; }
        
        .table-item.utilized { background: #ef4e44; }
        .table-item.free { background: #48f045; }
        .table-item.blocked { background: #fff301; }
        
        .table-name { position: absolute; font-size: 14px; font-weight: 600; color: #000; z-index: 2; }
        
        /* Counter-rotate table name to keep it horizontal */
        .table-item[data-rotation="90"] .table-name { transform: rotate(-90deg); }
        .table-item[data-rotation="180"] .table-name { transform: rotate(-180deg); }
        .table-item[data-rotation="270"] .table-name { transform: rotate(-270deg); }
        
        /* Counter-rotate complete order button to keep it upright */
        .table-item[data-rotation="90"] .complete-order-btn { transform: rotate(-90deg); }
        .table-item[data-rotation="180"] .complete-order-btn { transform: rotate(-180deg); }
        .table-item[data-rotation="270"] .complete-order-btn { transform: rotate(-270deg); }
        
        /* Counter-rotate reservation badge to keep it upright */
        .table-item[data-rotation="90"] .reservation-badge { transform: rotate(-90deg); }
        .table-item[data-rotation="180"] .reservation-badge { transform: rotate(-180deg); }
        .table-item[data-rotation="270"] .reservation-badge { transform: rotate(-270deg); }
        
        /* Reservation info badge on table */
        .reservation-badge { position: absolute; top: -10px; right: -10px; background: #fff301; color: #000; border: 2px solid #000; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold; z-index: 3; cursor: pointer; }
        .reservation-badge:hover { transform: scale(1.2); }
        
        /* Complete order button on utilized tables */
        .complete-order-btn {
            position: absolute;
            top: -12px;
            right: -12px;
            background: #10b981;
            color: white;
            border: 2px solid #059669;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: bold;
            z-index: 3;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.4);
        }
        .complete-order-btn:hover {
            transform: scale(1.15);
            background: #059669;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.6);
        }
        .complete-order-btn svg {
            width: 18px;
            height: 18px;
        }
        
        .chair-wrapper { position: absolute; width: 100%; height: 100%; }
        .chair { position: absolute; width: 30px; height: 15px; border-radius: 4px; }
        
        .table-circle .chair.chair-top { top: -20px; left: 50%; transform: translateX(-50%); }
        .table-circle .chair.chair-right { right: -35px; top: 50%; transform: translateY(-50%) rotate(90deg); }
        .table-circle .chair.chair-bottom { bottom: -20px; left: 50%; transform: translateX(-50%); }
        .table-circle .chair.chair-left { left: -35px; top: 50%; transform: translateY(-50%) rotate(90deg); }
        
        /* Horizontal rectangle table for 10 chairs */
        .table-rectangle-h10 { width: 280px; height: 100px; border-radius: 20px; }
        .table-rectangle-h10 .chair.chair-top-1 { top: -20px; left: 12%; }
        .table-rectangle-h10 .chair.chair-top-2 { top: -20px; left: 34%; }
        .table-rectangle-h10 .chair.chair-top-3 { top: -20px; left: 56%; }
        .table-rectangle-h10 .chair.chair-top-4 { top: -20px; left: 78%; }
        .table-rectangle-h10 .chair.chair-bottom-1 { bottom: -20px; left: 12%; }
        .table-rectangle-h10 .chair.chair-bottom-2 { bottom: -20px; left: 34%; }
        .table-rectangle-h10 .chair.chair-bottom-3 { bottom: -20px; left: 56%; }
        .table-rectangle-h10 .chair.chair-bottom-4 { bottom: -20px; left: 78%; }
        .table-rectangle-h10 .chair.chair-left { left: -35px; top: 50%; transform: translateY(-50%) rotate(90deg); }
        .table-rectangle-h10 .chair.chair-right { right: -35px; top: 50%; transform: translateY(-50%) rotate(90deg); }
        
        /* Horizontal rectangle table for 8 chairs */
        .table-rectangle-h { width: 220px; height: 100px; border-radius: 20px; }
        .table-rectangle-h .chair.chair-top-1 { top: -20px; left: 15%; }
        .table-rectangle-h .chair.chair-top-2 { top: -20px; left: 50%; transform: translateX(-50%); }
        .table-rectangle-h .chair.chair-top-3 { top: -20px; right: 15%; }
        .table-rectangle-h .chair.chair-bottom-1 { bottom: -20px; left: 15%; }
        .table-rectangle-h .chair.chair-bottom-2 { bottom: -20px; left: 50%; transform: translateX(-50%); }
        .table-rectangle-h .chair.chair-bottom-3 { bottom: -20px; right: 15%; }
        .table-rectangle-h .chair.chair-left { left: -35px; top: 50%; transform: translateY(-50%) rotate(90deg); }
        .table-rectangle-h .chair.chair-right { right: -35px; top: 50%; transform: translateY(-50%) rotate(90deg); }
        /* For tables with more chairs */
        .table-rectangle { width: 100px; height: 240px; border-radius: 20px; }
        .table-rectangle .chair.chair-top { top: -20px; left: 50%; transform: translateX(-50%); }
        .table-rectangle .chair.chair-bottom { bottom: -20px; left: 50%; transform: translateX(-50%); }
        .table-rectangle .chair.chair-right-1 { right: -35px; top: 10%; transform: rotate(90deg); }
        .table-rectangle .chair.chair-right-2 { right: -35px; top: 25%; transform: rotate(90deg); }
        .table-rectangle .chair.chair-right-3 { right: -35px; top: 50%; transform: translateY(-50%) rotate(90deg); }
        .table-rectangle .chair.chair-right-4 { right: -35px; top: 75%; transform: rotate(90deg); }
        .table-rectangle .chair.chair-right-5 { right: -35px; top: 90%; transform: rotate(90deg); }
        .table-rectangle .chair.chair-left-1 { left: -35px; top: 10%; transform: rotate(90deg); }
        .table-rectangle .chair.chair-left-2 { left: -35px; top: 25%; transform: rotate(90deg); }
        .table-rectangle .chair.chair-left-3 { left: -35px; top: 50%; transform: translateY(-50%) rotate(90deg); }
        .table-rectangle .chair.chair-left-4 { left: -35px; top: 75%; transform: rotate(90deg); }
        .table-rectangle .chair.chair-left-5 { left: -35px; top: 90%; transform: rotate(90deg); }
        
        .table-rounded .chair.chair-top-left { top: -20px; left: 15%; }
        .table-rounded .chair.chair-top-right { top: -20px; right: 15%; }
        .table-rounded .chair.chair-top { top: -20px; left: 50%; transform: translateX(-50%); }
        .table-rounded .chair.chair-right { right: -35px; top: 50%; transform: translateY(-50%) rotate(90deg); }
        .table-rounded .chair.chair-bottom-right { bottom: -20px; right: 15%; }
        .table-rounded .chair.chair-bottom-left { bottom: -20px; left: 15%; }
        .table-rounded .chair.chair-left { left: -35px; top: 50%; transform: translateY(-50%) rotate(90deg); }
        
        .table-item.utilized .chair { background: #ef4e44; border: 2px solid #000; }
        .table-item.free .chair { background: #48f045; border: 2px solid #000; }
        .table-item.blocked .chair { background: #fff301; border: 2px solid #000; }
        
        /* Individual chair status classes - higher priority */
        .table-item .chair.chair-utilized { background: #ef4e44 !important; }
        .table-item .chair.chair-free { background: #48f045 !important; }
        .table-item .chair.chair-blocked { background: #fff301 !important; }
        
        .table-controls { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 30px; }
        
        /* Make table controls single column on small screens */
        @media (max-width: 768px) {
            .table-controls { grid-template-columns: 1fr; gap: 20px; }
        }
        .controls-section { }
        .controls-title { font-size: 16px; font-weight: 600; color: #999; margin-bottom: 15px; }
        .toggle-group { display: flex; flex-direction: column; gap: 15px; }
        .toggle-item { display: flex; align-items: center; gap: 12px; cursor: pointer; }
        .toggle-input { display: none; }
        .toggle-slider { width: 50px; height: 28px; background: #ddd; border-radius: 50px; position: relative; transition: all 0.3s; }
        .toggle-slider::before { content: ''; position: absolute; width: 22px; height: 22px; background: #fff; border-radius: 50%; top: 3px; left: 3px; transition: all 0.3s; }
        .toggle-input:checked + .toggle-slider { background: #FF6500; }
        .toggle-input:checked + .toggle-slider::before { transform: translateX(22px); }
        .toggle-label { font-size: 14px; color: #333; }
        
        /* Pulse animation for recommendations */
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        /* Make tables draggable */
        .table-item { cursor: move; user-select: none; }
        .table-item:active { cursor: grabbing; }
        
        /* Modal styling improvements - Match Payment Modal Style */
        .modal-header { background: #f8f9fa; border-bottom: 2px solid #e5e7eb; padding: 20px 24px; }
        .modal-title { font-weight: 600; color: #1a1a1a; font-size: 20px; }
        .modal-body {  background: #fff; }
        .form-label { font-weight: 500; color: #374151; margin-bottom: 8px; font-size: 14px; }
        .form-control, .form-select { border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px 16px; font-size: 14px; transition: all 0.2s; }
        .form-control:focus, .form-select:focus { border-color: #FF6500; box-shadow: 0 0 0 3px rgba(255, 101, 0, 0.1); outline: none; }
        .modal-footer { padding: 20px 24px;  border-top: 1px solid #e5e7eb; }
        .btn-primary { background: #FF6500; border-color: #FF6500; padding: 12px 24px; font-weight: 600; border-radius: 8px; transition: all 0.2s;  color:#FFFFFF;}
        .btn-deleted { background: #bb2d3b; border-color: #FF6500; padding: 12px 24px; font-weight: 600; border-radius: 8px; transition: all 0.2s;  color:#FFFFFF;}
        .btn-primary:hover { background: #e55a00; border-color: #e55a00; transform: translateY(-1px); color: white;}
        .btn-deleted:hover { background: #bb2d3b; border-color: #e55a00; transform: translateY(-1px); color: white;}
        .btn-secondary { background: #E6E6E6; border-color: #E6E6E6; padding: 12px 24px; font-weight: 600; border-radius: 8px; transition: all 0.2s; }
        .btn-secondary:hover { background: #E6E6E6; border-color: #4b5563; }
        .btn-primary { background: #48f045; border-color: #48f045; color:#FFFFFF; font-weight: 600; padding: 12px 24px; border-radius: 8px; transition: all 0.2s; }
        .btn-primary:hover { background: #3dd93a; border-color: #3dd93a; color: #000; transform: translateY(-1px); }
        .complete-order { background: #157347; border-color: #48f045; color:#FFFFFF; font-weight: 600; padding: 12px 24px; border-radius: 8px; transition: all 0.2s; }
        .complete-order:hover { background: #157347; border-color: #3dd93a; color: #000; transform: translateY(-1px); }
        .btn-close { opacity: 0.5; transition: opacity 0.2s; }
        .btn-close:hover { opacity: 1; }
        
        /* Table order info styling */
        .table-order-info { }
        
        /* Corner shape for L-shaped table */
        .corner-shape { width: 180px; height: 160px; border-radius: 40px 40px 0 40px; }
        .corner-shape::after { content: ''; position: absolute; bottom: 0; right: 0; width: 90px; height: 90px; background: #F7F7F7; border-radius: 0 0 0 40px; }
    </style>
@endpush

@section('main_content')
    <form id="sale-form" action="{{ route('business.sales.store') }}" method="post" enctype="multipart/form-data" class="ajaxform pos-fullscreen-form">
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
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19 9C19 10.45 18.57 11.78 17.83 12.89C16.75 14.49 15.04 15.62 13.05 15.91C12.71 15.97 12.36 16 12 16C11.64 16 11.29 15.97 10.95 15.91C8.96 15.62 7.25 14.49 6.17 12.89C5.43 11.78 5 10.45 5 9C5 5.13 8.13 2 12 2C15.87 2 19 5.13 19 9Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M21.2491 18.4699L19.5991 18.8599C19.2291 18.9499 18.9391 19.2299 18.8591 19.5999L18.5091 21.0699C18.3191 21.8699 17.2991 22.1099 16.7691 21.4799L11.9991 15.9999L7.2291 21.4899C6.6991 22.1199 5.6791 21.8799 5.4891 21.0799L5.1391 19.6099C5.0491 19.2399 4.7591 18.9499 4.3991 18.8699L2.7491 18.4799C1.9891 18.2999 1.7191 17.3499 2.2691 16.7999L6.1691 12.8999C7.2491 14.4999 8.9591 15.6299 10.9491 15.9199C11.2891 15.9799 11.6391 16.0099 11.9991 16.0099C12.3591 16.0099 12.7091 15.9799 13.0491 15.9199C15.0391 15.6299 16.7491 14.4999 17.8291 12.8999L21.7291 16.7999C22.2791 17.3399 22.0091 18.2899 21.2491 18.4699Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M12.58 5.98L13.17 7.15999C13.25 7.31999 13.46 7.48 13.65 7.51L14.72 7.68999C15.4 7.79999 15.56 8.3 15.07 8.79L14.24 9.61998C14.1 9.75998 14.02 10.03 14.07 10.23L14.31 11.26C14.5 12.07 14.07 12.39 13.35 11.96L12.35 11.37C12.17 11.26 11.87 11.26 11.69 11.37L10.69 11.96C9.96997 12.38 9.53997 12.07 9.72997 11.26L9.96997 10.23C10.01 10.04 9.93997 9.75998 9.79997 9.61998L8.96997 8.79C8.47997 8.3 8.63997 7.80999 9.31997 7.68999L10.39 7.51C10.57 7.48 10.78 7.31999 10.86 7.15999L11.45 5.98C11.74 5.34 12.26 5.34 12.58 5.98Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
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

            <!-- Right Side - Products Section -->
            <div class="products-section">
                <!-- Tabs -->
                <div class="pos-tabs-wrapper">
                    <button type="button" class="pos-tab-btn" data-tab="tables">{{ __('Tables') }}</button>
                    <button type="button" class="pos-tab-btn active" data-tab="products">{{ __('Products') }}</button>
                </div>

                <!-- Tables Section -->
                <div class="tables-reservation-section" id="tables-section" style="display: none;">
                    <!-- Management Buttons -->
                    <div class="table-management-buttons">
                        <button type="button" class="btn-primary" id="btn-add-table">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10 4V16M4 10H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            {{ __('Add Table') }}
                        </button>
                                                <button type="button" class="btn btn-primary" id="btn-clear-all-data" style="background: #374151; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600;">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right: 8px;">
                                <path d="M3 3H8V8H3V3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 3H17V8H12V3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M3 12H8V17H3V12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 12H17V17H12V12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            {{ __('Manage Tables') }}
                        </button>
                              <button type="button" class="btn-primary" id="btn-make-reservation">
                             <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10 4V16M4 10H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            {{ __('Make Reservation') }}
                        </button>
                        <button type="button" class="btn-primary" id="btn-manage-all-tables">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 3H8V8H3V3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 3H17V8H12V3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M3 12H8V17H3V12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 12H17V17H12V12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            {{ __('Manage Reservations') }}
                        </button>
                        <button type="button" class="btn-primary" id="btn-manage-orders" style="background: #10b981; border-color: #10b981;">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 3H17V17H3V3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M7 7H13M7 10H13M7 13H10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            {{ __('Manage Orders') }}
                        </button>
               

              
                    </div>
                    
                    <!-- Legend -->
                    <div class="table-legend">
                        <div class="legend-item">
                            <span class="legend-color utilized"></span>
                            <span class="legend-text">{{ __('Utilized table/chair with guests') }}</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color free"></span>
                            <span class="legend-text">{{ __('Free table/chair') }}</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color blocked"></span>
                            <span class="legend-text">{{ __('Blocked table/chair') }}</span>
                        </div>
                    </div>

                    <!-- Restaurant Floor Plan Wrapper -->
                    <div class="floor-plan-wrapper">
                        <!-- Restaurant Floor Plan -->
                        <div class="restaurant-floor-plan">
                            <!-- Entrance -->
                            <div class="entrance-area" data-area="entrance" data-entrance-side="right" style="top: 47%; right: 20px;">
                                <span class="entrance-label">{{ __('Entrance') }}</span>
                                <div class="entrance-arrow"></div>
                            </div>
                            
                            <!-- Bar Area -->
                            <div class="floor-area bar-area" data-area="bar-area">
                                <span class="area-label">{{ __('Bar Area') }}</span>
                            </div>

                            <!-- Toilets Wall -->
                            <div class="floor-area toilets-wall" data-area="toilets">
                                <span class="toilets-label">{{ __('Toilets') }}</span>
                            </div>

                        <!-- Tables -->
                        <!-- Top Row - 10 Chair Horizontal Rectangle (replaces Ta1 and Ta2) -->
                        <div class="table-item table-rectangle-h10 free" style="top: 40px; left: 380px;" data-table="Ta1">
                            <span class="table-name">Ta1</span>
                            <div class="chair-wrapper">
                                <div class="chair chair-top-1"></div>
                                <div class="chair chair-top-2"></div>
                                <div class="chair chair-top-3"></div>
                                <div class="chair chair-top-4"></div>
                                <div class="chair chair-right"></div>
                                <div class="chair chair-bottom-1"></div>
                                <div class="chair chair-bottom-2"></div>
                                <div class="chair chair-bottom-3"></div>
                                <div class="chair chair-bottom-4"></div>
                                <div class="chair chair-left"></div>
                            </div>
                        </div>

                        <div class="table-item table-rounded free" style="top: 50px; right: 60px;" data-table="Tb1">
                            <span class="table-name">Tb1</span>
                            <div class="chair-wrapper">
                                <div class="chair chair-top-left"></div>
                                <div class="chair chair-top-right"></div>
                                <div class="chair chair-right"></div>
                                <div class="chair chair-bottom-right"></div>
                                <div class="chair chair-bottom-left"></div>
                                <div class="chair chair-left"></div>
                            </div>
                        </div>

                        <div class="table-item table-circle free" style="top: 200px; right: 80px;" data-table="Ta3">
                            <span class="table-name">Ta3</span>
                            <div class="chair-wrapper">
                                <div class="chair chair-top"></div>
                                <div class="chair chair-right"></div>
                                <div class="chair chair-bottom"></div>
                                <div class="chair chair-left"></div>
                            </div>
                        </div>

                        <!-- Left Side -->
                        <div class="table-item table-circle free" style="top: 280px; left: 120px;" data-table="Ta8">
                            <span class="table-name">Ta8</span>
                            <div class="chair-wrapper">
                                <div class="chair chair-top"></div>
                                <div class="chair chair-right"></div>
                                <div class="chair chair-bottom"></div>
                                <div class="chair chair-left"></div>
                            </div>
                        </div>

                        <div class="table-item table-rounded free" style="bottom: 60px; left: 80px;" data-table="Tb2">
                            <span class="table-name">Tb2</span>
                            <div class="chair-wrapper">
                                <div class="chair chair-top-left"></div>
                                <div class="chair chair-top-right"></div>
                                <div class="chair chair-right"></div>
                                <div class="chair chair-bottom-right"></div>
                                <div class="chair chair-bottom-left"></div>
                                <div class="chair chair-left"></div>
                            </div>
                        </div>

                        <!-- Center -->
                        <div class="table-item table-circle free" style="top: 380px; left: 420px;" data-table="Ta4">
                            <span class="table-name">Ta4</span>
                            <div class="chair-wrapper">
                                <div class="chair chair-top"></div>
                                <div class="chair chair-right"></div>
                                <div class="chair chair-bottom"></div>
                                <div class="chair chair-left"></div>
                            </div>
                        </div>

                        <!-- Bottom Row - All in one line -->
                        <div class="table-item table-circle free" style="bottom: 60px; left: 380px;" data-table="Ta5">
                            <span class="table-name">Ta5</span>
                            <div class="chair-wrapper">
                                <div class="chair chair-top"></div>
                                <div class="chair chair-right"></div>
                                <div class="chair chair-bottom"></div>
                                <div class="chair chair-left"></div>
                            </div>
                        </div>

                        <!-- 8 Chair Horizontal Rectangle Table (replaces Ta6 and Ta7) -->
                        <div class="table-item table-rectangle-h free" style="bottom: 60px; right: 120px;" data-table="Ta6">
                            <span class="table-name">Ta6</span>
                            <div class="chair-wrapper">
                                <div class="chair chair-top-1"></div>
                                <div class="chair chair-top-2"></div>
                                <div class="chair chair-top-3"></div>
                                <div class="chair chair-right"></div>
                                <div class="chair chair-bottom-1"></div>
                                <div class="chair chair-bottom-2"></div>
                                <div class="chair chair-bottom-3"></div>
                                <div class="chair chair-left"></div>
                            </div>
                        </div>

                        <!-- 2 Chair Table -->
                        <div class="table-item table-circle free" style="top: 180px; left: 280px;" data-table="Ta9">
                            <span class="table-name">Ta9</span>
                            <div class="chair-wrapper">
                                <div class="chair chair-top"></div>
                                <div class="chair chair-bottom"></div>
                            </div>
                        </div>

                        <!-- 12 Chair Table - Large Rectangle -->
                        <div class="table-item table-rectangle free" style="top: 220px; right: 260px;" data-table="Ta10">
                            <span class="table-name">Ta10</span>
                            <div class="chair-wrapper">
                                <div class="chair chair-top"></div>
                                <div class="chair chair-right-1"></div>
                                <div class="chair chair-right-2"></div>
                                <div class="chair chair-right-3"></div>
                                <div class="chair chair-right-4"></div>
                                <div class="chair chair-right-5"></div>
                                <div class="chair chair-bottom"></div>
                                <div class="chair chair-left-1"></div>
                                <div class="chair chair-left-2"></div>
                                <div class="chair chair-left-3"></div>
                                <div class="chair chair-left-4"></div>
                                <div class="chair chair-left-5"></div>
                            </div>
                        </div>

                        <!-- Center Square (decoration/pillar) -->
                        <div class="center-square" data-area="center-square"></div>
                    </div>
                    
                    <!-- Entrance cut-out cover only (no arrow) -->
                    <div class="entrance-cutout-cover"></div>
                    </div>

                    <!-- Live Views & Integration Toggles -->
                    <div class="table-controls">
                        <div class="controls-section">
                            <h4 class="controls-title">{{ __('Live Views') }}</h4>
                            <div class="toggle-group">
                                <label class="toggle-item">
                                    <input type="checkbox" class="toggle-input" id="show-utilization" checked>
                                    <span class="toggle-slider"></span>
                                    <span class="toggle-label">{{ __('Show utilization') }}</span>
                                </label>
                                <label class="toggle-item">
                                    <input type="checkbox" class="toggle-input" id="show-ordered">
                                    <span class="toggle-slider"></span>
                                    <span class="toggle-label">{{ __('Show ordered') }}</span>
                                </label>
                                <label class="toggle-item">
                                    <input type="checkbox" class="toggle-input" id="show-recommendations">
                                    <span class="toggle-slider"></span>
                                    <span class="toggle-label">{{ __('Show recommendations') }}</span>
                                </label>
                            </div>
                        </div>
                        <div class="controls-section">
                            <h4 class="controls-title">{{ __('Integration') }}</h4>
                            <div class="toggle-group">
                                <label class="toggle-item">
                                    <input type="checkbox" class="toggle-input" id="show-reservations">
                                    <span class="toggle-slider"></span>
                                    <span class="toggle-label">{{ __('Show reservations') }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category Section -->
                <div class="pos-category-section" id="products-section">
                    <div class="pos-category-header">
                        <h3 class="pos-section-title">{{ __('Category') }}</h3>
                        <div class="pos-category-nav-buttons">
                            <button type="button" class="pos-category-scroll-btn prev">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.5 15L7.5 10L12.5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <button type="button" class="pos-category-scroll-btn next">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7.5 15L12.5 10L7.5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="pos-category-scroll-wrapper">
                        <div class="pos-category-list" id="category-list">
                            <!-- All Categories Option -->
                            <button type="button" class="pos-category-item active" data-category="all">
                                <div class="pos-category-icon">
                                    <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="6" y="6" width="14" height="14" rx="2" stroke="currentColor" stroke-width="2.5"/>
                                        <rect x="28" y="6" width="14" height="14" rx="2" stroke="currentColor" stroke-width="2.5"/>
                                        <rect x="6" y="28" width="14" height="14" rx="2" stroke="currentColor" stroke-width="2.5"/>
                                        <rect x="28" y="28" width="14" height="14" rx="2" stroke="currentColor" stroke-width="2.5"/>
                                    </svg>
                                </div>
                                <span class="pos-category-name">{{ __('All') }}</span>
                            </button>
                            @foreach($categories as $category)
                            <button type="button" class="pos-category-item" data-category="{{ $category->id }}">
                                <div class="pos-category-icon">
                                    @if($category->icon)
                                    <img src="{{ asset($category->icon) }}" alt="{{ $category->categoryName }}">
                                    @else
                                    <!-- Default category icon -->
                                    <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8 18L24 8L40 18V38C40 39.0609 39.5786 40.0783 38.8284 40.8284C38.0783 41.5786 37.0609 42 36 42H12C10.9391 42 9.92172 41.5786 9.17157 40.8284C8.42143 40.0783 8 39.0609 8 38V18Z" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M18 42V24H30V42" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    @endif
                                </div>
                                <span class="pos-category-name">{{ $category->categoryName }}</span>
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Special Menu / Products Grid -->
                <div class="pos-products-section">
                    <h3 class="pos-section-title">{{ __('Special Menu') }}</h3>
                    <div class="pos-products-grid" id="products-list">
                        @include('business::sales.product-list-new')
                    </div>
                </div>
            </div>
                        <!-- Left Sidebar - Order Section -->
            <div class="order-sidebar">
                <!-- Search Customer Section -->
                <div class="sidebar-search-customer">
                    <div class="search-customer-wrapper">
          
                        <select required name="party_id" id="party_id" class="sidebar-customer-select choices-select">
                            <option value="">{{ __('Search Customer') }}</option>
                            <option class="guest-option" value="guest">{{ __('Guest') }}</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}" data-type="{{ $customer->type }}" data-phone="{{ $customer->phone }}">{{ $customer->name }}({{ $customer->type }}{{ $customer->due ? ' ' . currency_format($customer->due, currency: business_currency()) : '' }}) {{ $customer->phone }}</option>
                            @endforeach
                        </select>
                        <a href="#customer-create-modal" data-bs-toggle="modal" class="sidebar-add-customer-btn">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10 4V16M4 10H16" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>
                    <div class="guest-phone-field d-none guest_phone">
                        <input type="text" name="customer_phone" class="form-control" placeholder="{{ __('Enter Customer Phone Number') }}">
                    </div>
                </div>

                <!-- Order Details Card -->
                <div class="sidebar-order-details">
                    <h4 class="sidebar-section-title">{{ __('Order Details') }}</h4>
                    <div class="order-details-content">
                        <div class="order-detail-row">
                            <span class="detail-value" id="selected-customer-name">{{ __('Johnson Mitchell') }}</span>
                        </div>
                        <div class="order-detail-row">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" style="flex-shrink: 0;">
                                <circle cx="10" cy="10" r="8" stroke="#666" stroke-width="1.5"/>
                                <path d="M10 5V10L13 13" stroke="#666" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                            <span class="detail-value">{{ now()->format('D, M\TH\LY Y') }} {{ now()->format('h:i A') }}</span>
                        </div>
                        <div class="order-detail-row">
                            <span class="detail-value" id="selected-customer-phone">+1(415)123-4547</span>
                        </div>
                    </div>
                    <input type="hidden" name="invoiceNumber" value="{{ $invoice_no }}">
                    <input type="hidden" name="saleDate" value="{{ now()->format('Y-m-d') }}">
                </div>

                <!-- Delivery Type Tabs -->
                <div class="sidebar-delivery-tabs">
                    <button type="button" class="delivery-tab-btn">{{ __('Delivery') }}</button>
                    <button type="button" class="delivery-tab-btn">{{ __('Pre-order') }}</button>
                    <button type="button" class="delivery-tab-btn active">{{ __('Takeaway') }}</button>
                </div>

                <!-- Cart Section -->
                <div class="sidebar-cart-section">
                    <div class="sidebar-cart-header">
                        <span class="cart-title">{{ __('Products') }}</span>
                        <button type="button" class="sidebar-clear-all-btn cancel-sale-btn" data-route="{{ route('business.carts.remove-all') }}">{{ __('Clear All') }}</button>
                    </div>
                    <div class="sidebar-cart-items" id="cart-list">@include('business::sales.cart-list-new')</div>
                </div>

                <!-- Order Summary -->
                <div class="sidebar-order-summary">
                    <div class="summary-row"><span>{{ __('Items') }}</span><span id="items_count">0</span></div>
                    <div class="summary-row"><span>{{ __('Subtotal') }}</span><span id="sub_total">{{ currency_format(0, currency: business_currency()) }}</span></div>
                    <div class="summary-row"><span>{{ __('Discount') }}</span><span id="discount_display">0</span></div>
                    <div class="summary-row"><span>{{ __('Taxes') }}</span><span id="vat_display">0</span></div>
                    <div class="summary-row"><span>{{ __('Shipping') }}</span><span id="shipping_display">0</span></div>
                    <div class="summary-row"><span>{{ __('Rounding (-/+)') }}</span><span id="rounding_amount">0</span></div>
                    <div class="summary-row summary-total"><span>{{ __('Total') }}</span><span id="total_amount">{{ currency_format(0, currency: business_currency()) }}</span></div>
                </div>

                <!-- Hidden Inputs -->
                <div class="hidden-cart-inputs" style="display: none;">
                    <input name="receive_amount" type="number" step="any" id="receive_amount" min="0" value="0">
                    <input type="number" step="any" id="change_amount" value="0" readonly>
                    <input type="number" step="any" id="due_amount" value="0" readonly>
                    <select name="payment_type_id" id="payment_type_id">@foreach ($payment_types as $type)<option value="{{ $type->id }}">{{ $type->name }}</option>@endforeach</select>
                    <input type="text" name="note" id="payment_note">
                    <select name="vat_id" class="vat_select"><option value="">{{ __('Select') }}</option>@foreach ($vats as $vat)<option value="{{ $vat->id }}" data-rate="{{ $vat->rate }}">{{ $vat->name }} ({{ $vat->rate }}%)</option>@endforeach</select>
                    <input type="number" step="any" name="vat_amount" id="vat_amount" min="0" value="0">
                    <select name="discount_type" class="discount_type"><option value="flat">{{ __('Flat') }}</option><option value="percent">{{ __('Percent') }}</option></select>
                    <input type="number" step="any" name="discountAmount" id="discount_amount" min="0" value="0">
                    <input type="number" step="any" name="shipping_charge" id="shipping_charge" value="0">
                </div>

                <!-- Action Buttons -->
                <div class="sidebar-action-buttons">
                    @usercan('sales.create')<button type="button" class="sidebar-pay-btn" id="open-payment-modal">{{ __('Pay the Bill') }}</button>@endusercan
                    <button type="button" class="sidebar-cancel-btn cancel-sale-btn" data-route="{{ route('business.carts.remove-all') }}">{{ __('Cancel Order') }}</button>
                </div>
            </div>

        </div>

        @php $currency = business_currency(); $rounding_amount_option = sale_rounding(); @endphp
        <input type="hidden" id="currency_symbol" value="{{ $currency->symbol }}">
        <input type="hidden" id="currency_position" value="{{ $currency->position }}">
        <input type="hidden" id="currency_code" value="{{ $currency->code }}">
        <input type="hidden" id="get_product" value="{{ route('business.products.prices') }}">
        <input type="hidden" value="{{ route('business.sales.cart') }}" id="get-cart">
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

    <!-- Payment Modal -->
    <div class="payment-modal-overlay" id="payment-modal-overlay">
        <div class="payment-modal">
            <div class="payment-modal-header">
                <h3 class="payment-modal-title">{{ __('Collect Payment') }}</h3>
                <div class="payment-modal-order-info">
                    <div class="payment-order-number">{{ __('Order') }} #<span id="modal-order-number">{{ $invoice_no }}</span></div>
                    <div class="payment-order-total" id="modal-order-total">{{ currency_format(0, currency: business_currency()) }}</div>
                </div>
            </div>

            <div class="payment-modal-tabs">
                <button type="button" class="payment-tab-btn active" data-tab="full">{{ __('Full Payment') }}</button>
                <button type="button" class="payment-tab-btn" data-tab="split">{{ __('Split Bill') }}</button>
            </div>

            <div class="payment-methods">
                <button type="button" class="payment-method-btn active" data-method="cash">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M28 8H4C2.89543 8 2 8.89543 2 10V22C2 23.1046 2.89543 24 4 24H28C29.1046 24 30 23.1046 30 22V10C30 8.89543 29.1046 8 28 8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M16 20C18.2091 20 20 18.2091 20 16C20 13.7909 18.2091 12 16 12C13.7909 12 12 13.7909 12 16C12 18.2091 13.7909 20 16 20Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>{{ __('Cash') }}</span>
                </button>
                <button type="button" class="payment-method-btn" data-method="card">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="2" y="6" width="28" height="20" rx="2" stroke="currentColor" stroke-width="2"/>
                        <path d="M2 12H30" stroke="currentColor" stroke-width="2"/>
                        <path d="M6 20H12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <span>{{ __('Card') }}</span>
                </button>
                <button type="button" class="payment-method-btn" data-method="upi">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16 28C22.6274 28 28 22.6274 28 16C28 9.37258 22.6274 4 16 4C9.37258 4 4 9.37258 4 16C4 22.6274 9.37258 28 16 28Z" stroke="currentColor" stroke-width="2"/>
                        <path d="M16 10V22M10 16H22" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <span>{{ __('UPI') }}</span>
                </button>
                <button type="button" class="payment-method-btn" data-method="due">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16 28C22.6274 28 28 22.6274 28 16C28 9.37258 22.6274 4 16 4C9.37258 4 4 9.37258 4 16C4 22.6274 9.37258 28 16 28Z" stroke="currentColor" stroke-width="2"/>
                        <path d="M16 8V16L20 20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <span>{{ __('DUE') }}</span>
                </button>
            </div>

            <div class="payment-amounts">
                <div class="payment-amount-field">
                    <label>{{ __('Due Amount') }}</label>
                    <input type="text" id="modal-due-amount" readonly value="0">
                </div>
                <div class="payment-amount-field">
                    <label>{{ __('Receive Amount') }}</label>
                    <input type="text" id="modal-receive-amount" value="0">
                </div>
            </div>

            <div class="payment-summary">
                <div class="payment-summary-row">
                    <span>{{ __('Total Bill') }}</span>
                    <span id="modal-total-bill">{{ currency_format(0, currency: business_currency()) }}</span>
                </div>
                <div class="payment-summary-row">
                    <span>{{ __('Amount Paid') }}</span>
                    <span id="modal-amount-paid">{{ currency_format(0, currency: business_currency()) }}</span>
                </div>
                <div class="payment-summary-row">
                    <span>{{ __('Due Amount') }}</span>
                    <span id="modal-due-summary">{{ currency_format(0, currency: business_currency()) }}</span>
                </div>
            </div>

            <div class="payment-numpad">
                <button type="button" class="numpad-btn" data-value="7">7</button>
                <button type="button" class="numpad-btn" data-value="8">8</button>
                <button type="button" class="numpad-btn" data-value="9">9</button>
                <button type="button" class="numpad-btn" data-value="4">4</button>
                <button type="button" class="numpad-btn" data-value="5">5</button>
                <button type="button" class="numpad-btn" data-value="6">6</button>
                <button type="button" class="numpad-btn" data-value="1">1</button>
                <button type="button" class="numpad-btn" data-value="2">2</button>
                <button type="button" class="numpad-btn" data-value="3">3</button>
                <button type="button" class="numpad-btn" data-value="0">0</button>
                <button type="button" class="numpad-btn" data-value=".">.</button>
                <button type="button" class="numpad-btn numpad-clear" data-value="clear">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>

            <div class="payment-modal-actions">
                <button type="button" class="payment-cancel-btn" id="cancel-payment-btn">{{ __('Cancel') }}</button>
                <button type="submit" form="sale-form" class="payment-complete-btn" id="complete-payment-btn">{{ __('Complete Payment') }}</button>
            </div>
        </div>
    </div>
@endsection

@push('modal')
    <!-- Add Table Modal -->
    <div class="modal fade" id="addTableModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Add New Table') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Table Number') }}</label>
                        <input type="text" class="form-control" id="new-table-name" placeholder="Ta11, Ta12, etc.">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Number of Chairs') }}</label>
                        <select class="form-select" id="new-table-chairs">
                            <option value="2">2 {{ __('Chairs') }}</option>
                            <option value="4" selected>4 {{ __('Chairs') }}</option>
                            <option value="6">6 {{ __('Chairs') }}</option>
                            <option value="8">8 {{ __('Chairs') }}</option>
                            <option value="10">10 {{ __('Chairs') }}</option>
                            <option value="12">12 {{ __('Chairs') }}</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-primary" id="save-new-table">{{ __('Add Table') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Make Reservation Modal -->
    <div class="modal fade" id="makeReservationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Make Reservation') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Customer Name') }}</label>
                            <input type="text" class="form-control" id="reservation-customer-name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Phone Number') }}</label>
                            <input type="tel" class="form-control" id="reservation-phone">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">{{ __('Date') }}</label>
                            <input type="date" class="form-control" id="reservation-date" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('Time') }}</label>
                            <input type="time" class="form-control" id="reservation-time" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('Number of Guests') }}</label>
                            <input type="number" class="form-control" id="reservation-guests" value="2" min="1" max="20" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Special Notes') }}</label>
                        <textarea class="form-control" id="reservation-notes" rows="2"></textarea>
                    </div>
                    <div class="mb-3 flex justify-end">
                        <button type="button" class="btn   btn-primary" id="search-available-tables">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right: 8px;">
                                <path d="M9 17C13.4183 17 17 13.4183 17 9C17 4.58172 13.4183 1 9 1C4.58172 1 1 4.58172 1 9C1 13.4183 4.58172 17 9 17Z" stroke="currentColor" stroke-width="2"/>
                                <path d="M19 19L14.65 14.65" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            {{ __('Search Available Tables') }}
                        </button>
                    </div>
                    <div id="available-tables-list" style="display: none;">
                        <h6 class="mb-3">{{ __('Available Tables') }}:</h6>
                        <div id="available-tables-container" class="d-flex flex-wrap gap-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-primary" id="confirm-reservation" disabled>{{ __('Confirm Reservation') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Manage Reservations Modal -->
    <div class="modal fade" id="manageReservationsModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Manage Reservations') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('Table') }}</th>
                                    <th>{{ __('Customer Name') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Time') }}</th>
                                    <th>{{ __('Guests') }}</th>
                                    <th>{{ __('Notes') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="reservations-table-body">
                                <!-- Reservations will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                    <div id="no-reservations-message" style="display: none; text-align: center; padding: 40px;">
                        <p class="text-muted">{{ __('No reservations found') }}</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Manage Orders Modal -->
    <div class="modal fade" id="manageOrdersModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Manage Orders') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('Table') }}</th>
                                    <th>{{ __('Customer Name') }}</th>
                                    <th>{{ __('Guests') }}</th>
                                    <th>{{ __('Order Items') }}</th>
                                    <th>{{ __('Notes') }}</th>
                                    <th>{{ __('Started At') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="orders-table-body">
                                <!-- Orders will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                    <div id="no-orders-message" style="display: none; text-align: center; padding: 40px;">
                        <p class="text-muted">{{ __('No active orders found') }}</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Manage All Tables Modal -->
    <div class="modal fade" id="manageAllTablesModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Manage All Tables') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('Table Name') }}</th>
                                    <th>{{ __('Chairs') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Current Order/Reservation') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="all-tables-body">
                                <!-- Tables will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reservation Details Modal -->
    <div class="modal fade" id="reservationDetailsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header ">
                    <h5 class="modal-title"> {{ __('Reservation Details') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <strong>{{ __('Table') }}:</strong>
                        <span id="detail-table" class="ms-2"></span>
                    </div>
                    <div class="mb-3">
                        <strong>{{ __('Customer Name') }}:</strong>
                        <span id="detail-customer" class="ms-2"></span>
                    </div>
                    <div class="mb-3">
                        <strong>{{ __('Phone') }}:</strong>
                        <span id="detail-phone" class="ms-2"></span>
                    </div>
                    <div class="mb-3">
                        <strong>{{ __('Date') }}:</strong>
                        <span id="detail-date" class="ms-2"></span>
                    </div>
                    <div class="mb-3">
                        <strong>{{ __('Time') }}:</strong>
                        <span id="detail-time" class="ms-2"></span>
                    </div>
                    <div class="mb-3">
                        <strong>{{ __('Number of Guests') }}:</strong>
                        <span id="detail-guests" class="ms-2"></span>
                    </div>
                    <div class="mb-3">
                        <strong>{{ __('Special Notes') }}:</strong>
                        <p id="detail-notes" class="mt-2 p-2 bg-light rounded"></p>
                    </div>
                    <div class="mb-3">
                        <strong>{{ __('Status') }}:</strong>
                        <span id="detail-status" class="ms-2"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-deleted" id="cancel-reservation-btn">{{ __('Cancel Reservation') }}</button>
                    <button type="button" class="btn btn-primary" id="guest-arrived-btn">{{ __('Guest Arrived') }}</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Order Modal -->
    <div class="modal fade" id="tableOrderModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Table Order') }} - <span id="order-table-name"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-order-info">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Customer Name') }}</label>
                                <input type="text" class="form-control" id="order-customer-name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Number of Guests') }}</label>
                                <input type="number" class="form-control" id="order-guests" value="1" min="1">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Order Items') }}</label>
                            <textarea class="form-control" id="order-items" rows="4" placeholder="{{ __('Enter order items...') }}"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Special Notes') }}</label>
                            <textarea class="form-control" id="order-notes" rows="2"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="form-label">{{ __('Order Time') }}</label>
                                <input type="time" class="form-control" id="order-time" value="{{ now()->format('H:i') }}">
                            </div>
                        </div>
                        <!-- Hidden field for table status - managed automatically -->
                        <input type="hidden" id="order-table-status" value="utilized">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-primary" id="save-table-order">{{ __('Save Order') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Complete Order Modal -->
    <div class="modal fade" id="completeOrderModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: #10b981; color: white;">
                    <h5 class="modal-title">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" style="vertical-align: middle; margin-right: 8px;">
                            <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        {{ __('Complete Order') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="complete-order-info">
                        <h6 style="font-weight: bold; margin-bottom: 15px;">{{ __('Order Details') }}</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td style="width: 40%; color: #666;">{{ __('Table') }}:</td>
                                <td style="font-weight: bold;" id="complete-table-name"></td>
                            </tr>
                            <tr>
                                <td style="color: #666;">{{ __('Customer') }}:</td>
                                <td id="complete-customer-name"></td>
                            </tr>
                            <tr>
                                <td style="color: #666;">{{ __('Guests') }}:</td>
                                <td id="complete-guests"></td>
                            </tr>
                            <tr>
                                <td style="color: #666;">{{ __('Order Time') }}:</td>
                                <td id="complete-order-time"></td>
                            </tr>
                        </table>
                        
                        <div style="background: #f3f4f6; padding: 12px; border-radius: 8px; margin-top: 15px;">
                            <strong>{{ __('Order Items') }}:</strong>
                            <div id="complete-order-items" style="margin-top: 8px; white-space: pre-wrap;"></div>
                        </div>
                        
                        <div id="complete-notes-section" style="margin-top: 15px; display: none;">
                            <strong>{{ __('Notes') }}:</strong>
                            <div id="complete-order-notes" style="margin-top: 8px; color: #666; font-style: italic;"></div>
                        </div>
                        
                        <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 12px; margin-top: 20px; border-radius: 4px;">
                            <strong style="color: #92400e;">⚠️ {{ __('Confirm Completion') }}</strong>
                            <p style="margin: 8px 0 0 0; color: #78350f; font-size: 14px;">
                                {{ __('This will mark the order as complete and free the table for new customers.') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn complete-order" id="confirm-complete-order" style="background: #10b981; border-color: #10b981;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" style="vertical-align: middle; margin-right: 6px;">
                            <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        {{ __('Complete Order') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @include('business::sales.calculator')
    @include('business::sales.category-search')
    @include('business::sales.brand-search')
    @include('business::sales.customer-create')
    @include('business::sales.stock-list')
@endpush

@push('js')
    <script>
        // Tab switching functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.pos-tab-btn');
            const tablesSection = document.getElementById('tables-section');
            const productsSection = document.getElementById('products-section');
            const productsGridSection = document.querySelector('.pos-products-section');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const tab = this.getAttribute('data-tab');
                    
                    // Remove active class from all buttons
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    // Show/hide sections
                    if (tab === 'tables') {
                        tablesSection.style.display = 'block';
                        productsSection.style.display = 'none';
                        if (productsGridSection) productsGridSection.style.display = 'none';
                    } else {
                        tablesSection.style.display = 'none';
                        productsSection.style.display = 'block';
                        if (productsGridSection) productsGridSection.style.display = 'block';
                    }
                });
            });
            
            // Table click functionality - open order modal on click
            const tables = document.querySelectorAll('.table-item');
            let selectedTable = null;
            
            tables.forEach(table => {
                // Single click to open order modal
                table.addEventListener('click', function(e) {
                    // If clicking on a chair, don't open modal
                    if (e.target.classList.contains('chair')) {
                        return;
                    }
                    
                    // Check table status
                    const tableName = this.getAttribute('data-table');
                    
                    if (this.classList.contains('blocked')) {
                        // Table is reserved - show reservation details in modal
                        const reservations = JSON.parse(localStorage.getItem('tableReservations') || '{}');
                        let reservationInfo = null;
                        let reservationKey = null;
                        
                        // Find reservation for this table
                        for (const [key, reservation] of Object.entries(reservations)) {
                            if (reservation.table === tableName) {
                                reservationInfo = reservation;
                                reservationKey = key;
                                break;
                            }
                        }
                        
                        if (reservationInfo) {
                            // Show reservation details in modal
                            showReservationDetails(reservationInfo, reservationKey);
                        } else {
                            alert('{{ __("This table is blocked/reserved") }}');
                        }
                        return;
                    }
                    
                    if (this.classList.contains('utilized')) {
                        // Table is occupied - open order modal to view/edit
                        selectedTable = this;
                        document.getElementById('order-table-name').textContent = tableName;
                        
                        // Load existing order data if available
                        const tableOrders = JSON.parse(localStorage.getItem('tableOrders') || '{}');
                        if (tableOrders[tableName]) {
                            document.getElementById('order-customer-name').value = tableOrders[tableName].customer || '';
                            document.getElementById('order-guests').value = tableOrders[tableName].guests || '1';
                            document.getElementById('order-items').value = tableOrders[tableName].items || '';
                            document.getElementById('order-notes').value = tableOrders[tableName].notes || '';
                        }
                        
                        const orderModal = new bootstrap.Modal(document.getElementById('tableOrderModal'));
                        orderModal.show();
                        return;
                    }
                    
                    // Table is free - open reservation modal and pre-select it
                    const chairCount = this.querySelectorAll('.chair').length;
                    
                    // Pre-fill the reservation form
                    document.getElementById('reservation-guests').value = Math.min(chairCount, 4);
                    
                    // Store the clicked table as pre-selected
                    selectedTableForReservation = {
                        name: tableName,
                        chairs: chairCount,
                        element: this
                    };
                    
                    // Open Make Reservation modal
                    const reservationModal = new bootstrap.Modal(document.getElementById('makeReservationModal'));
                    reservationModal.show();
                    
                    // Show the clicked table as pre-selected, but allow changing
                    setTimeout(() => {
                        const container = document.getElementById('available-tables-container');
                        container.innerHTML = '';
                        
                        // Show pre-selected table
                        const tableBtn = document.createElement('button');
                        tableBtn.className = 'btn btn-success';
                        tableBtn.textContent = `${tableName} (${chairCount} {{ __("chairs") }}) - {{ __("Selected") }}`;
                        tableBtn.onclick = function() {
                            // Already selected, do nothing
                        };
                        container.appendChild(tableBtn);
                        
                        // Add message to search for other tables
                        const searchMsg = document.createElement('p');
                        searchMsg.className = 'mt-2 text-muted';
                        searchMsg.innerHTML = '{{ __("Click") }} <strong>{{ __("Search Available Tables") }}</strong> {{ __("to see other options") }}';
                        container.appendChild(searchMsg);
                        
                        document.getElementById('available-tables-list').style.display = 'block';
                        document.getElementById('confirm-reservation').disabled = false;
                    }, 300);
                });
            });
            
            // Chair click functionality - each chair can have different status
            const chairs = document.querySelectorAll('.chair');
            chairs.forEach(chair => {
                chair.addEventListener('click', function(e) {
                    e.stopPropagation(); // Prevent table click
                    
                    // Cycle through chair statuses
                    if (this.classList.contains('chair-utilized')) {
                        this.classList.remove('chair-utilized');
                        this.classList.add('chair-free');
                    } else if (this.classList.contains('chair-free')) {
                        this.classList.remove('chair-free');
                        this.classList.add('chair-blocked');
                    } else if (this.classList.contains('chair-blocked')) {
                        this.classList.remove('chair-blocked');
                        this.classList.add('chair-utilized');
                    } else {
                        // First click - set to utilized
                        this.classList.add('chair-utilized');
                    }
                });
            });
            
            // Live Views & Integration toggles functionality
            const showUtilization = document.getElementById('show-utilization');
            const showOrdered = document.getElementById('show-ordered');
            const showRecommendations = document.getElementById('show-recommendations');
            const showReservations = document.getElementById('show-reservations');
            
            // Show Utilization - toggle visibility of table/chair colors
            if (showUtilization) {
                showUtilization.addEventListener('change', function() {
                    const allTables = document.querySelectorAll('.table-item');
                    if (this.checked) {
                        allTables.forEach(table => {
                            table.style.opacity = '1';
                        });
                        console.log('Utilization view: ON');
                    } else {
                        allTables.forEach(table => {
                            table.style.opacity = '0.3';
                        });
                        console.log('Utilization view: OFF');
                    }
                });
            }
            
            // Show Ordered - highlight tables with orders
            if (showOrdered) {
                showOrdered.addEventListener('change', function() {
                    const utilizedTables = document.querySelectorAll('.table-item.utilized');
                    if (this.checked) {
                        utilizedTables.forEach(table => {
                            table.style.boxShadow = '0 0 20px rgba(239, 78, 68, 0.8)';
                        });
                        console.log('Show ordered: ON');
                    } else {
                        utilizedTables.forEach(table => {
                            table.style.boxShadow = 'none';
                        });
                        console.log('Show ordered: OFF');
                    }
                });
            }
            
            // Show Recommendations - suggest available tables
            if (showRecommendations) {
                showRecommendations.addEventListener('change', function() {
                    const freeTables = document.querySelectorAll('.table-item.free');
                    if (this.checked) {
                        freeTables.forEach(table => {
                            table.style.boxShadow = '0 0 20px rgba(72, 240, 69, 0.8)';
                            table.style.animation = 'pulse 2s infinite';
                        });
                        console.log('Show recommendations: ON');
                    } else {
                        freeTables.forEach(table => {
                            table.style.boxShadow = 'none';
                            table.style.animation = 'none';
                        });
                        console.log('Show recommendations: OFF');
                    }
                });
            }
            
            // Show Reservations - highlight blocked/reserved tables
            if (showReservations) {
                showReservations.addEventListener('change', function() {
                    const blockedTables = document.querySelectorAll('.table-item.blocked');
                    if (this.checked) {
                        blockedTables.forEach(table => {
                            table.style.boxShadow = '0 0 20px rgba(255, 243, 1, 0.8)';
                            const tableName = table.getAttribute('data-table');
                            table.setAttribute('title', tableName + ' - Reserved');
                        });
                        console.log('Show reservations: ON');
                    } else {
                        blockedTables.forEach(table => {
                            table.style.boxShadow = 'none';
                            table.removeAttribute('title');
                        });
                        console.log('Show reservations: OFF');
                    }
                });
            }
            
            // Add Table button functionality
            const btnAddTable = document.getElementById('btn-add-table');
            if (btnAddTable) {
                btnAddTable.addEventListener('click', function() {
                    const addTableModal = new bootstrap.Modal(document.getElementById('addTableModal'));
                    addTableModal.show();
                });
            }
            
            // Save new table functionality
            const saveNewTableBtn = document.getElementById('save-new-table');
            if (saveNewTableBtn) {
                saveNewTableBtn.addEventListener('click', function() {
                    const tableName = document.getElementById('new-table-name').value.trim();
                    const chairCount = parseInt(document.getElementById('new-table-chairs').value);
                    const tableStatus = 'free'; // Always default to free
                    
                    if (!tableName) {
                        alert('{{ __("Please enter a table number") }}');
                        return;
                    }
                    
                    // Auto-determine table type based on chair count
                    let tableType = 'circle';
                    if (chairCount === 6) {
                        tableType = 'rounded';
                    } else if (chairCount === 8) {
                        tableType = 'rectangle-h';
                    } else if (chairCount === 10) {
                        tableType = 'rectangle-h10';
                    } else if (chairCount === 12) {
                        tableType = 'rectangle';
                    }
                    
                    // Create new table element
                    const floorPlan = document.querySelector('.restaurant-floor-plan');
                    const newTable = document.createElement('div');
                    newTable.className = `table-item ${tableStatus}`;
                    newTable.setAttribute('data-table', tableName);
                    newTable.style.opacity = '0.7';
                    newTable.style.border = '3px dashed #FF6500';
                    
                    // Add table type class based on chair count
                    if (chairCount === 12) {
                        newTable.classList.add('table-rectangle');
                    } else if (chairCount === 10) {
                        newTable.classList.add('table-rectangle-h10');
                    } else if (chairCount === 8) {
                        newTable.classList.add('table-rectangle-h');
                    } else if (chairCount === 6) {
                        newTable.classList.add('table-rounded');
                    } else {
                        newTable.classList.add('table-circle');
                    }
                    
                    // Position new table (center of floor plan)
                    newTable.style.top = '300px';
                    newTable.style.left = '300px';
                    
                    // Add table name
                    const nameSpan = document.createElement('span');
                    nameSpan.className = 'table-name';
                    nameSpan.textContent = tableName;
                    newTable.appendChild(nameSpan);
                    
                    // Add chairs based on count
                    const chairWrapper = document.createElement('div');
                    chairWrapper.className = 'chair-wrapper';
                    
                    if (chairCount === 12) {
                        // 12 chairs: 1 top, 5 right, 1 bottom, 5 left (vertical rectangle table)
                        ['top', 'right-1', 'right-2', 'right-3', 'right-4', 'right-5', 'bottom', 'left-1', 'left-2', 'left-3', 'left-4', 'left-5'].forEach(pos => {
                            const chair = document.createElement('div');
                            chair.className = `chair chair-${pos}`;
                            chairWrapper.appendChild(chair);
                        });
                    } else if (chairCount === 10) {
                        // 10 chairs: 4 top, 4 bottom, 1 left, 1 right (horizontal rectangle)
                        ['top-1', 'top-2', 'top-3', 'top-4', 'right', 'bottom-1', 'bottom-2', 'bottom-3', 'bottom-4', 'left'].forEach(pos => {
                            const chair = document.createElement('div');
                            chair.className = `chair chair-${pos}`;
                            chairWrapper.appendChild(chair);
                        });
                    } else if (chairCount === 8) {
                        // 8 chairs: 3 top, 3 bottom, 1 left, 1 right (horizontal rectangle)
                        ['top-1', 'top-2', 'top-3', 'right', 'bottom-1', 'bottom-2', 'bottom-3', 'left'].forEach(pos => {
                            const chair = document.createElement('div');
                            chair.className = `chair chair-${pos}`;
                            chairWrapper.appendChild(chair);
                        });
                    } else if (chairCount === 6) {
                        // 6 chairs: rounded table
                        ['top-left', 'top-right', 'right', 'bottom-right', 'bottom-left', 'left'].forEach(pos => {
                            const chair = document.createElement('div');
                            chair.className = `chair chair-${pos}`;
                            chairWrapper.appendChild(chair);
                        });
                    } else if (chairCount === 4) {
                        // 4 chairs: circle table
                        ['top', 'right', 'bottom', 'left'].forEach(pos => {
                            const chair = document.createElement('div');
                            chair.className = `chair chair-${pos}`;
                            chairWrapper.appendChild(chair);
                        });
                    } else if (chairCount === 2) {
                        // 2 chairs: circle table
                        ['top', 'bottom'].forEach(pos => {
                            const chair = document.createElement('div');
                            chair.className = `chair chair-${pos}`;
                            chairWrapper.appendChild(chair);
                        });
                    }
                    
                    newTable.appendChild(chairWrapper);
                    floorPlan.appendChild(newTable);
                    
                    // Close modal first
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addTableModal'));
                    modal.hide();
                    
                    // Show instruction
                    const instruction = document.createElement('div');
                    instruction.style.cssText = 'position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.9); color: white; padding: 30px 50px; border-radius: 12px; z-index: 10000; font-size: 18px; text-align: center; box-shadow: 0 10px 40px rgba(0,0,0,0.3);';
                    instruction.innerHTML = `
                        <div style="font-size: 24px; font-weight: bold; margin-bottom: 15px;">📍 {{ __("Position Your Table") }}</div>
                        <div style="margin-bottom: 20px;">{{ __("Click and drag the table to position it") }}</div>
                        <button id="confirm-position-btn" style="background: #FF6500; color: white; border: none; padding: 12px 30px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer;">{{ __("Confirm Position") }}</button>
                        <button id="cancel-position-btn" style="background: #E6E6E6; color: white; border: none; padding: 12px 30px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; margin-left: 10px;">{{ __("Cancel") }}</button>
                    `;
                    document.body.appendChild(instruction);
                    
                    // Enable immediate dragging
                    let isPositioning = true;
                    let positioningTable = newTable;
                    let isDraggingNew = false;
                    let dragOffsetX = 0;
                    let dragOffsetY = 0;
                    
                    // Mouse down on table to start dragging
                    positioningTable.addEventListener('mousedown', function(e) {
                        if (!isPositioning) return;
                        isDraggingNew = true;
                        
                        const rect = positioningTable.getBoundingClientRect();
                        const floorRect = floorPlan.getBoundingClientRect();
                        
                        dragOffsetX = e.clientX - rect.left;
                        dragOffsetY = e.clientY - rect.top;
                        
                        positioningTable.style.cursor = 'grabbing';
                        e.preventDefault();
                    });
                    
                    // Mouse move to drag table
                    function positionMouseMove(e) {
                        if (!isPositioning || !isDraggingNew) return;
                        
                        const floorRect = floorPlan.getBoundingClientRect();
                        const tableWidth = positioningTable.offsetWidth;
                        const tableHeight = positioningTable.offsetHeight;
                        
                        let newX = e.clientX - floorRect.left - dragOffsetX;
                        let newY = e.clientY - floorRect.top - dragOffsetY;
                        
                        // Keep within bounds
                        newX = Math.max(0, Math.min(newX, floorPlan.offsetWidth - tableWidth));
                        newY = Math.max(0, Math.min(newY, floorPlan.offsetHeight - tableHeight));
                        
                        positioningTable.style.left = newX + 'px';
                        positioningTable.style.top = newY + 'px';
                        positioningTable.style.right = 'auto';
                        positioningTable.style.bottom = 'auto';
                    }
                    
                    // Mouse up to stop dragging
                    function positionMouseUp() {
                        if (isDraggingNew) {
                            isDraggingNew = false;
                            positioningTable.style.cursor = 'move';
                        }
                    }
                    
                    document.addEventListener('mousemove', positionMouseMove);
                    document.addEventListener('mouseup', positionMouseUp);
                    
                    // Confirm position
                    document.getElementById('confirm-position-btn').addEventListener('click', function() {
                        isPositioning = false;
                        isDraggingNew = false;
                        document.removeEventListener('mousemove', positionMouseMove);
                        document.removeEventListener('mouseup', positionMouseUp);
                        
                        // Finalize table
                        newTable.style.opacity = '1';
                        newTable.style.border = 'none';
                        newTable.style.cursor = 'move';
                        
                        // Add event listeners
                        addTableEventListeners(newTable);
                        makeDraggable(newTable);
                        
                        // Save to localStorage
                        saveCustomTable(newTable);
                        
                        // Remove instruction
                        instruction.remove();
                        
                        // Reset form
                        document.getElementById('new-table-name').value = '';
                        document.getElementById('new-table-chairs').value = '4';
                        
                        alert('{{ __("Table added successfully!") }}');
                    });
                    
                    // Cancel
                    document.getElementById('cancel-position-btn').addEventListener('click', function() {
                        isPositioning = false;
                        isDraggingNew = false;
                        document.removeEventListener('mousemove', positionMouseMove);
                        document.removeEventListener('mouseup', positionMouseUp);
                        newTable.remove();
                        instruction.remove();
                    });
                });
            }
            
            // Function to add event listeners to tables
            function addTableEventListeners(table) {
                // Single click to check status and open appropriate modal
                table.addEventListener('click', function(e) {
                    // If clicking on a chair, don't open modal
                    if (e.target.classList.contains('chair')) {
                        return;
                    }
                    
                    // Check table status
                    const tableName = this.getAttribute('data-table');
                    
                    if (this.classList.contains('blocked')) {
                        // Table is reserved - show reservation details in modal
                        const reservations = JSON.parse(localStorage.getItem('tableReservations') || '{}');
                        let reservationInfo = null;
                        let reservationKey = null;
                        
                        // Find reservation for this table
                        for (const [key, reservation] of Object.entries(reservations)) {
                            if (reservation.table === tableName) {
                                reservationInfo = reservation;
                                reservationKey = key;
                                break;
                            }
                        }
                        
                        if (reservationInfo) {
                            // Show reservation details in modal
                            showReservationDetails(reservationInfo, reservationKey);
                        } else {
                            alert('{{ __("This table is blocked/reserved") }}');
                        }
                        return;
                    }
                    
                    if (this.classList.contains('utilized')) {
                        // Table is occupied - open order modal to view/edit
                        selectedTable = this;
                        document.getElementById('order-table-name').textContent = tableName;
                        
                        // Load existing order data if available
                        const tableOrders = JSON.parse(localStorage.getItem('tableOrders') || '{}');
                        if (tableOrders[tableName]) {
                            document.getElementById('order-customer-name').value = tableOrders[tableName].customer || '';
                            document.getElementById('order-guests').value = tableOrders[tableName].guests || '1';
                            document.getElementById('order-items').value = tableOrders[tableName].items || '';
                            document.getElementById('order-notes').value = tableOrders[tableName].notes || '';
                        }
                        
                        const orderModal = new bootstrap.Modal(document.getElementById('tableOrderModal'));
                        orderModal.show();
                        return;
                    }
                    
                    // Table is free - open reservation modal and pre-select it
                    const chairCount = this.querySelectorAll('.chair').length;
                    
                    // Pre-fill the reservation form
                    document.getElementById('reservation-guests').value = Math.min(chairCount, 4);
                    
                    // Store the clicked table as pre-selected
                    selectedTableForReservation = {
                        name: tableName,
                        chairs: chairCount,
                        element: this
                    };
                    
                    // Open Make Reservation modal
                    const reservationModal = new bootstrap.Modal(document.getElementById('makeReservationModal'));
                    reservationModal.show();
                    
                    // Show the clicked table as pre-selected, but allow changing
                    setTimeout(() => {
                        const container = document.getElementById('available-tables-container');
                        container.innerHTML = '';
                        
                        // Show pre-selected table
                        const tableBtn = document.createElement('button');
                        tableBtn.className = 'btn btn-success';
                        tableBtn.textContent = `${tableName} (${chairCount} {{ __("chairs") }}) - {{ __("Selected") }}`;
                        tableBtn.onclick = function() {
                            // Already selected, do nothing
                        };
                        container.appendChild(tableBtn);
                        
                        // Add message to search for other tables
                        const searchMsg = document.createElement('p');
                        searchMsg.className = 'mt-2 text-muted';
                        searchMsg.innerHTML = '{{ __("Click") }} <strong>{{ __("Search Available Tables") }}</strong> {{ __("to see other options") }}';
                        container.appendChild(searchMsg);
                        
                        document.getElementById('available-tables-list').style.display = 'block';
                        document.getElementById('confirm-reservation').disabled = false;
                    }, 300);
                });
                
                // Add chair event listeners
                const chairs = table.querySelectorAll('.chair');
                chairs.forEach(chair => {
                    chair.addEventListener('click', function(e) {
                        e.stopPropagation();
                        
                        if (this.classList.contains('chair-utilized')) {
                            this.classList.remove('chair-utilized');
                            this.classList.add('chair-free');
                        } else if (this.classList.contains('chair-free')) {
                            this.classList.remove('chair-free');
                            this.classList.add('chair-blocked');
                        } else if (this.classList.contains('chair-blocked')) {
                            this.classList.remove('chair-blocked');
                            this.classList.add('chair-utilized');
                        } else {
                            this.classList.add('chair-utilized');
                        }
                    });
                });
            }
            
            // ========== TABLE ROTATION FEATURE ==========
            
            // Rotate table by 90 degrees
            function rotateTable(table) {
                const currentRotation = parseInt(table.getAttribute('data-rotation') || '0');
                const newRotation = (currentRotation + 90) % 360;
                
                table.setAttribute('data-rotation', newRotation);
                
                // Save rotation
                const tableName = table.getAttribute('data-table');
                saveTablePosition(tableName, table);
                
                console.log(`Table ${tableName} rotated to ${newRotation}°`);
            }
            
            // Reset table rotation to 0
            function resetTableRotation(table) {
                table.setAttribute('data-rotation', '0');
                
                const tableName = table.getAttribute('data-table');
                saveTablePosition(tableName, table);
                
                console.log(`Table ${tableName} rotation reset`);
            }
            
            // Add right-click context menu for rotation
            document.addEventListener('contextmenu', function(e) {
                const table = e.target.closest('.table-item');
                if (!table) return;
                
                e.preventDefault();
                
                // Remove existing context menu
                const existingMenu = document.getElementById('table-context-menu');
                if (existingMenu) existingMenu.remove();
                
                // Create context menu
                const menu = document.createElement('div');
                menu.id = 'table-context-menu';
                menu.style.cssText = `
                    position: fixed;
                    top: ${e.clientY}px;
                    left: ${e.clientX}px;
                    background: white;
                    border: 1px solid #ddd;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    z-index: 10000;
                    min-width: 180px;
                    padding: 8px 0;
                `;
                
                const currentRotation = parseInt(table.getAttribute('data-rotation') || '0');
                const tableName = table.getAttribute('data-table');
                
                menu.innerHTML = `
                    <div style="padding: 8px 16px; font-weight: bold; border-bottom: 1px solid #eee; color: #666;">
                        ${tableName}
                    </div>
                    <div class="menu-item" data-action="rotate" style="padding: 10px 16px; cursor: pointer; transition: background 0.2s;">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" style="vertical-align: middle; margin-right: 8px;">
                            <path d="M14 8C14 11.3137 11.3137 14 8 14C4.68629 14 2 11.3137 2 8C2 4.68629 4.68629 2 8 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M8 2L10 4M8 2L6 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        Rotate 90° (Current: ${currentRotation}°)
                    </div>
                    <div class="menu-item" data-action="reset" style="padding: 10px 16px; cursor: pointer; transition: background 0.2s;">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" style="vertical-align: middle; margin-right: 8px;">
                            <path d="M2 8H14M8 2V14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        Reset Rotation
                    </div>
                `;
                
                document.body.appendChild(menu);
                
                // Add hover effects
                menu.querySelectorAll('.menu-item').forEach(item => {
                    item.addEventListener('mouseenter', function() {
                        this.style.background = '#f3f4f6';
                    });
                    item.addEventListener('mouseleave', function() {
                        this.style.background = 'transparent';
                    });
                });
                
                // Handle menu actions
                menu.querySelector('[data-action="rotate"]').addEventListener('click', function() {
                    rotateTable(table);
                    menu.remove();
                });
                
                menu.querySelector('[data-action="reset"]').addEventListener('click', function() {
                    resetTableRotation(table);
                    menu.remove();
                });
                
                // Close menu on click outside
                setTimeout(() => {
                    document.addEventListener('click', function closeMenu() {
                        menu.remove();
                        document.removeEventListener('click', closeMenu);
                    });
                }, 10);
            });
            
            // ========== END TABLE ROTATION FEATURE ==========
            
            // Make tables draggable function
            function makeDraggable(table) {
                table.addEventListener('mousedown', function(e) {
                    // Don't drag if clicking on chair or double-clicking
                    if (e.target.classList.contains('chair') || e.detail === 2) {
                        return;
                    }
                    
                    isDragging = true;
                    currentTable = this;
                    
                    const rect = this.getBoundingClientRect();
                    const parent = this.offsetParent.getBoundingClientRect();
                    
                    offsetX = e.clientX - rect.left;
                    offsetY = e.clientY - rect.top;
                    
                    this.style.cursor = 'grabbing';
                    e.preventDefault();
                });
            }
            
            // Dragging variables
            let isDragging = false;
            let currentTable = null;
            let offsetX = 0;
            let offsetY = 0;
            
            // Save table order functionality
            const saveTableOrderBtn = document.getElementById('save-table-order');
            if (saveTableOrderBtn) {
                saveTableOrderBtn.addEventListener('click', function() {
                    const customerName = document.getElementById('order-customer-name').value;
                    const guests = document.getElementById('order-guests').value;
                    const orderItems = document.getElementById('order-items').value;
                    const notes = document.getElementById('order-notes').value;
                    const time = document.getElementById('order-time').value;
                    const status = document.getElementById('order-table-status').value;
                    
                    if (!customerName) {
                        alert('{{ __("Please enter customer name") }}');
                        return;
                    }
                    
                    // Update table status based on order status
                    if (selectedTable) {
                        const tableName = selectedTable.getAttribute('data-table');
                        
                        // Remove all status classes
                        selectedTable.classList.remove('utilized', 'free', 'blocked');
                        
                        // Add new status
                        if (status === 'completed') {
                            // Order completed - table becomes free
                            selectedTable.classList.add('free');
                            
                            // Remove from table orders
                            const tableOrders = JSON.parse(localStorage.getItem('tableOrders') || '{}');
                            delete tableOrders[tableName];
                            localStorage.setItem('tableOrders', JSON.stringify(tableOrders));
                        } else if (status === 'utilized') {
                            // Order in progress
                            selectedTable.classList.add('utilized');
                            
                            // Add complete order button
                            addCompleteOrderButton(selectedTable);
                            
                            // Store order data
                            const orderData = {
                                table: tableName,
                                customer: customerName,
                                guests: guests,
                                items: orderItems,
                                notes: notes,
                                time: time,
                                status: status,
                                timestamp: new Date().toISOString()
                            };
                            
                            const tableOrders = JSON.parse(localStorage.getItem('tableOrders') || '{}');
                            tableOrders[tableName] = orderData;
                            localStorage.setItem('tableOrders', JSON.stringify(tableOrders));
                        } else {
                            // Free or blocked
                            selectedTable.classList.add(status);
                        }
                        
                        console.log('Order saved:', {table: tableName, customer: customerName, status: status});
                    }
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('tableOrderModal'));
                    modal.hide();
                    
                    // Reset form
                    document.getElementById('order-customer-name').value = '';
                    document.getElementById('order-guests').value = '1';
                    document.getElementById('order-items').value = '';
                    document.getElementById('order-notes').value = '';
                    document.getElementById('order-table-status').value = 'utilized';
                    
                    if (status === 'completed') {
                        alert('{{ __("Order completed! Table is now free.") }}');
                    } else {
                        alert('{{ __("Order saved successfully!") }}');
                    }
                });
            }
            
            // Manage Tables button functionality (both buttons do the same thing)
            const btnManageTables = document.getElementById('btn-manage-tables');
            const btnManageAllTables = document.getElementById('btn-manage-all-tables');
            
            console.log('Manage Tables button found:', btnManageTables);
            console.log('Manage All Tables button found:', btnManageAllTables);
            
            function openManageReservationsModal() {
                console.log('📋 Opening Manage Reservations modal...');
                
                // Load and display all reservations in modal
                const reservations = JSON.parse(localStorage.getItem('tableReservations') || '{}');
                console.log('📋 Reservations from localStorage:', reservations);
                console.log('📋 Number of reservations:', Object.keys(reservations).length);
                const tbody = document.getElementById('reservations-table-body');
                const noReservationsMsg = document.getElementById('no-reservations-message');
                
                console.log('Reservations:', reservations);
                console.log('Table body element:', tbody);
                console.log('No reservations message element:', noReservationsMsg);
                
                if (!tbody) {
                    console.error('reservations-table-body element not found!');
                    alert('Error: Table body element not found. Please refresh the page.');
                    return;
                }
                
                tbody.innerHTML = '';
                
                if (Object.keys(reservations).length === 0) {
                    if (tbody.closest('.table-responsive')) {
                        tbody.closest('.table-responsive').style.display = 'none';
                    }
                    if (noReservationsMsg) {
                        noReservationsMsg.style.display = 'block';
                    }
                } else {
                    if (tbody.closest('.table-responsive')) {
                        tbody.closest('.table-responsive').style.display = 'block';
                    }
                    if (noReservationsMsg) {
                        noReservationsMsg.style.display = 'none';
                    }
                    
                    // Get current date/time for status check
                    const now = new Date();
                    const currentDate = now.toISOString().split('T')[0];
                    const currentTime = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
                    
                    for (const [key, reservation] of Object.entries(reservations)) {
                        const row = document.createElement('tr');
                        
                        // Determine status
                        let status = '🟡 Reserved';
                        let statusClass = 'text-warning';
                        const reservationDateTime = new Date(reservation.date + ' ' + reservation.time);
                        const currentDateTime = new Date(currentDate + ' ' + currentTime);
                        
                        if (currentDateTime >= reservationDateTime) {
                            status = '⏰ Time Arrived';
                            statusClass = 'text-success';
                        }
                        
                        row.innerHTML = `
                            <td><strong>${reservation.table}</strong></td>
                            <td>${reservation.customerName}</td>
                            <td>${reservation.phone || 'N/A'}</td>
                            <td>${reservation.date}</td>
                            <td>${reservation.time}</td>
                            <td>${reservation.guests}</td>
                            <td>${reservation.notes || '-'}</td>
                            <td class="${statusClass}">${status}</td>
                            <td>
                                <button class="btn btn-sm btn-danger delete-reservation" data-key="${key}" data-table="${reservation.table}">
                                    {{ __('Cancel') }}
                                </button>
                            </td>
                        `;
                        tbody.appendChild(row);
                    }
                    
                    // Add delete functionality
                    document.querySelectorAll('.delete-reservation').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const key = this.getAttribute('data-key');
                            const tableName = this.getAttribute('data-table');
                            
                            if (confirm('{{ __("Are you sure you want to cancel this reservation?") }}')) {
                                // Remove reservation
                                delete reservations[key];
                                localStorage.setItem('tableReservations', JSON.stringify(reservations));
                                
                                // Update table status to free
                                const table = document.querySelector(`[data-table="${tableName}"]`);
                                if (table && table.classList.contains('blocked')) {
                                    table.classList.remove('blocked');
                                    table.classList.add('free');
                                }
                                
                                // Reload modal
                                openManageReservationsModal();
                            }
                        });
                    });
                }
                
                // Open modal
                console.log('Opening manage reservations modal...');
                const manageModal = new bootstrap.Modal(document.getElementById('manageReservationsModal'));
                manageModal.show();
            }
            
            // Only add event listener to the button that exists
            if (btnManageAllTables) {
                btnManageAllTables.addEventListener('click', openManageReservationsModal);
            } else if (btnManageTables) {
                btnManageTables.addEventListener('click', openManageReservationsModal);
            } else {
                console.error('No manage reservations button found in DOM!');
            }
            
            // Manage Orders button functionality
            const btnManageOrders = document.getElementById('btn-manage-orders');
            console.log('Manage Orders button found:', btnManageOrders);
            
            function openManageOrdersModal() {
                console.log('📦 Opening Manage Orders modal...');
                
                // Load and display all orders in modal
                const tableOrders = JSON.parse(localStorage.getItem('tableOrders') || '{}');
                console.log('📦 Orders from localStorage:', tableOrders);
                console.log('📦 Number of orders:', Object.keys(tableOrders).length);
                const tbody = document.getElementById('orders-table-body');
                const noOrdersMsg = document.getElementById('no-orders-message');
                
                if (!tbody) {
                    console.error('orders-table-body element not found!');
                    alert('Error: Table body element not found. Please refresh the page.');
                    return;
                }
                
                tbody.innerHTML = '';
                
                if (Object.keys(tableOrders).length === 0) {
                    if (tbody.closest('.table-responsive')) {
                        tbody.closest('.table-responsive').style.display = 'none';
                    }
                    if (noOrdersMsg) {
                        noOrdersMsg.style.display = 'block';
                    }
                } else {
                    if (tbody.closest('.table-responsive')) {
                        tbody.closest('.table-responsive').style.display = 'block';
                    }
                    if (noOrdersMsg) {
                        noOrdersMsg.style.display = 'none';
                    }
                    
                    for (const [tableName, order] of Object.entries(tableOrders)) {
                        const row = document.createElement('tr');
                        
                        // Format timestamp
                        const startedAt = new Date(order.timestamp).toLocaleString();
                        
                        row.innerHTML = `
                            <td><strong>${tableName}</strong></td>
                            <td>${order.customer || 'N/A'}</td>
                            <td>${order.guests || 'N/A'}</td>
                            <td style="max-width: 200px; white-space: pre-wrap;">${order.items || '-'}</td>
                            <td>${order.notes || '-'}</td>
                            <td>${startedAt}</td>
                            <td>
                                <button class="btn btn-sm btn-primary view-order" data-table="${tableName}">
                                    {{ __('View/Edit') }}
                                </button>
                                <button class="btn btn-sm  complete-order" data-table="${tableName}">
                                    {{ __('Complete') }}
                                </button>
                            </td>
                        `;
                        tbody.appendChild(row);
                    }
                    
                    // Add view/edit functionality
                    document.querySelectorAll('.view-order').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const tableName = this.getAttribute('data-table');
                            const order = tableOrders[tableName];
                            const table = document.querySelector(`[data-table="${tableName}"]`);
                            
                            if (table && order) {
                                // Close manage orders modal
                                bootstrap.Modal.getInstance(document.getElementById('manageOrdersModal')).hide();
                                
                                // Open order modal with existing data
                                selectedTable = table;
                                document.getElementById('order-table-name').textContent = tableName;
                                document.getElementById('order-customer-name').value = order.customer || '';
                                document.getElementById('order-guests').value = order.guests || 1;
                                document.getElementById('order-items').value = order.items || '';
                                document.getElementById('order-notes').value = order.notes || '';
                                document.getElementById('order-table-status').value = 'utilized';
                                
                                const orderModal = new bootstrap.Modal(document.getElementById('tableOrderModal'));
                                orderModal.show();
                            }
                        });
                    });
                    
                    // Add complete functionality
                    document.querySelectorAll('.complete-order').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const tableName = this.getAttribute('data-table');
                            
                            if (confirm(`{{ __("Complete order for table") }} ${tableName}?`)) {
                                // Remove order from localStorage
                                delete tableOrders[tableName];
                                localStorage.setItem('tableOrders', JSON.stringify(tableOrders));
                                
                                // Update table status to free
                                const table = document.querySelector(`[data-table="${tableName}"]`);
                                if (table) {
                                    table.classList.remove('utilized', 'blocked');
                                    table.classList.add('free');
                                }
                                
                                // Refresh the modal
                                openManageOrdersModal();
                                
                                alert(`{{ __("Order completed! Table") }} ${tableName} {{ __("is now free.") }}`);
                            }
                        });
                    });
                }
                
                // Show the modal
                const modal = new bootstrap.Modal(document.getElementById('manageOrdersModal'));
                modal.show();
            }
            
            if (btnManageOrders) {
                btnManageOrders.addEventListener('click', openManageOrdersModal);
            }
            
            // Auto-check reservations and update table status
            function checkReservationTimes() {
                console.log('⏰ checkReservationTimes called');
                const reservations = JSON.parse(localStorage.getItem('tableReservations') || '{}');
                console.log('⏰ Current reservations:', reservations);
                const now = new Date();
                const currentDate = now.toISOString().split('T')[0];
                const currentTime = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
                console.log('⏰ Current date/time:', currentDate, currentTime);
                
                // First, remove all existing badges
                document.querySelectorAll('.reservation-badge').forEach(badge => badge.remove());
                
                let needsUpdate = false;
                for (const [key, reservation] of Object.entries(reservations)) {
                    console.log('⏰ Checking reservation:', key, reservation);
                    const table = document.querySelector(`[data-table="${reservation.table}"]`);
                    if (!table) {
                        console.log('⏰ Table not found:', reservation.table);
                        continue;
                    }
                    
                    // Add reservation badge to table
                    if (table.classList.contains('blocked')) {
                        console.log('⏰ Adding badge to table:', reservation.table);
                        const badge = document.createElement('div');
                        badge.className = 'reservation-badge';
                        badge.textContent = 'R';
                        badge.title = `Reserved for ${reservation.customerName}`;
                        badge.onclick = function(e) {
                            e.stopPropagation();
                            showReservationDetails(reservation, key);
                        };
                        table.appendChild(badge);
                    }
                    
                    // Just mark that time has arrived, but don't change status automatically
                    // Status will change when user clicks on the table
                    if (reservation.date === currentDate && reservation.time <= currentTime && !reservation.timeArrived) {
                        console.log('⏰ Marking time arrived for:', reservation.table);
                        // Store that reservation time has arrived
                        reservation.timeArrived = true;
                        needsUpdate = true;
                    }
                }
                
                // Only update localStorage if needed
                if (needsUpdate) {
                    console.log('⏰ Updating localStorage with timeArrived flags');
                    localStorage.setItem('tableReservations', JSON.stringify(reservations));
                }
                console.log('⏰ checkReservationTimes completed');
            }
            
            // Show reservation details in modal
            function showReservationDetails(reservation, reservationKey) {
                console.log('🔍 showReservationDetails called for:', reservation.table, reservationKey);
                console.log('🔍 Reservation data:', reservation);
                
                document.getElementById('detail-table').textContent = reservation.table;
                document.getElementById('detail-customer').textContent = reservation.customerName;
                document.getElementById('detail-phone').textContent = reservation.phone || 'N/A';
                document.getElementById('detail-date').textContent = reservation.date;
                document.getElementById('detail-time').textContent = reservation.time;
                document.getElementById('detail-guests').textContent = reservation.guests;
                document.getElementById('detail-notes').textContent = reservation.notes || '-';
                
                // Check table current status
                const table = document.querySelector(`[data-table="${reservation.table}"]`);
                let statusText = '🟡 Reserved';
                let showGuestArrivedBtn = true;
                
                if (table && table.classList.contains('utilized')) {
                    // Table is already utilized (guest has arrived)
                    statusText = '� Utilized - Guest Arrived';
                    showGuestArrivedBtn = false;
                } else {
                    // Check if reservation time has arrived
                    const now = new Date();
                    const currentDate = now.toISOString().split('T')[0];
                    const currentTime = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
                    const reservationDateTime = new Date(reservation.date + ' ' + reservation.time);
                    const currentDateTime = new Date(currentDate + ' ' + currentTime);
                    
                    if (currentDateTime >= reservationDateTime) {
                        statusText = '⏰ Time Arrived - Waiting for Guest';
                    }
                }
                
                document.getElementById('detail-status').textContent = statusText;
                
                // Show/hide Guest Arrived button based on status
                const guestArrivedBtn = document.getElementById('guest-arrived-btn');
                if (guestArrivedBtn) {
                    guestArrivedBtn.style.display = showGuestArrivedBtn ? 'inline-block' : 'none';
                }
                
                // Cancel reservation button
                document.getElementById('cancel-reservation-btn').onclick = function() {
                    if (confirm('{{ __("Are you sure you want to cancel this reservation?") }}')) {
                        const reservations = JSON.parse(localStorage.getItem('tableReservations') || '{}');
                        delete reservations[reservationKey];
                        localStorage.setItem('tableReservations', JSON.stringify(reservations));
                        
                        // Update table status
                        const table = document.querySelector(`[data-table="${reservation.table}"]`);
                        if (table) {
                            table.classList.remove('blocked');
                            table.classList.add('free');
                        }
                        
                        // Close modal and refresh
                        bootstrap.Modal.getInstance(document.getElementById('reservationDetailsModal')).hide();
                        checkReservationTimes();
                        alert('{{ __("Reservation cancelled successfully") }}');
                    }
                };
                
                // Guest arrived button
                document.getElementById('guest-arrived-btn').onclick = function() {
                    console.log('🚨 Guest Arrived button clicked!');
                    console.log('🚨 Reservation key:', reservationKey);
                    console.log('🚨 Table:', reservation.table);
                    
                    const table = document.querySelector(`[data-table="${reservation.table}"]`);
                    if (table) {
                        table.classList.remove('blocked');
                        table.classList.add('utilized');
                        
                        // Remove reservation from localStorage (guest has arrived)
                        const reservations = JSON.parse(localStorage.getItem('tableReservations') || '{}');
                        console.log('🚨 Before delete:', reservations);
                        delete reservations[reservationKey];
                        console.log('🚨 After delete:', reservations);
                        localStorage.setItem('tableReservations', JSON.stringify(reservations));
                        console.log('🚨 Saved to localStorage');
                        
                        // Remove reservation badge
                        const badge = table.querySelector('.reservation-badge');
                        if (badge) badge.remove();
                        
                        // Open order modal
                        selectedTable = table;
                        document.getElementById('order-table-name').textContent = reservation.table;
                        document.getElementById('order-customer-name').value = reservation.customerName;
                        document.getElementById('order-guests').value = reservation.guests;
                        document.getElementById('order-notes').value = reservation.notes || '';
                        
                        bootstrap.Modal.getInstance(document.getElementById('reservationDetailsModal')).hide();
                        const orderModal = new bootstrap.Modal(document.getElementById('tableOrderModal'));
                        orderModal.show();
                    }
                };
                
                // Open modal
                const detailsModal = new bootstrap.Modal(document.getElementById('reservationDetailsModal'));
                detailsModal.show();
            }
            
            // Add complete order button to utilized tables
            function addCompleteOrderButton(table) {
                // Remove existing button if any
                const existingBtn = table.querySelector('.complete-order-btn');
                if (existingBtn) existingBtn.remove();
                
                // Create complete order button
                const completeBtn = document.createElement('div');
                completeBtn.className = 'complete-order-btn';
                completeBtn.title = 'Complete Order & Free Table';
                completeBtn.innerHTML = `
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                `;
                
                // Add click handler to open modal
                completeBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    
                    const tableName = table.getAttribute('data-table');
                    const tableOrders = JSON.parse(localStorage.getItem('tableOrders') || '{}');
                    const orderData = tableOrders[tableName];
                    
                    if (orderData) {
                        // Populate modal with order details
                        document.getElementById('complete-table-name').textContent = tableName;
                        document.getElementById('complete-customer-name').textContent = orderData.customer || '-';
                        document.getElementById('complete-guests').textContent = orderData.guests || '1';
                        document.getElementById('complete-order-time').textContent = orderData.time || '-';
                        document.getElementById('complete-order-items').textContent = orderData.items || '{{ __("No items") }}';
                        
                        // Show/hide notes section
                        const notesSection = document.getElementById('complete-notes-section');
                        const notesContent = document.getElementById('complete-order-notes');
                        if (orderData.notes) {
                            notesContent.textContent = orderData.notes;
                            notesSection.style.display = 'block';
                        } else {
                            notesSection.style.display = 'none';
                        }
                        
                        // Store table reference for completion
                        window.currentCompleteTable = table;
                        
                        // Open modal
                        const modal = new bootstrap.Modal(document.getElementById('completeOrderModal'));
                        modal.show();
                    }
                });
                
                table.appendChild(completeBtn);
            }
            
            // Handle complete order confirmation - use setTimeout to ensure DOM is ready
            setTimeout(function() {
                const confirmCompleteBtn = document.getElementById('confirm-complete-order');
                if (confirmCompleteBtn) {
                    // Remove any existing listeners
                    const newBtn = confirmCompleteBtn.cloneNode(true);
                    confirmCompleteBtn.parentNode.replaceChild(newBtn, confirmCompleteBtn);
                    
                    // Add new listener
                    newBtn.addEventListener('click', function() {
                        const table = window.currentCompleteTable;
                        if (!table) {
                            console.error('No table reference found');
                            return;
                        }
                        
                        const tableName = table.getAttribute('data-table');
                        console.log('Completing order for:', tableName);
                        
                        // Remove order from localStorage
                        const tableOrders = JSON.parse(localStorage.getItem('tableOrders') || '{}');
                        delete tableOrders[tableName];
                        localStorage.setItem('tableOrders', JSON.stringify(tableOrders));
                        
                        // Update table status to free
                        table.classList.remove('utilized', 'blocked');
                        table.classList.add('free');
                        
                        // Remove the complete button
                        const completeBtn = table.querySelector('.complete-order-btn');
                        if (completeBtn) completeBtn.remove();
                        
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('completeOrderModal'));
                        if (modal) modal.hide();
                        
                        console.log(`Order completed for ${tableName}, table is now free`);
                        
                        // Show success message
                        setTimeout(() => {
                            alert(`✅ Order completed! ${tableName} is now free.`);
                        }, 300);
                    });
                    
                    console.log('Complete order button listener attached');
                } else {
                    console.error('confirm-complete-order button not found');
                }
            }, 500);
            
            // Restore table statuses from localStorage on page load
            function restoreTableStatuses() {
                const reservations = JSON.parse(localStorage.getItem('tableReservations') || '{}');
                const tableOrders = JSON.parse(localStorage.getItem('tableOrders') || '{}');
                
                console.log('Restoring table statuses...');
                console.log('Reservations:', reservations);
                console.log('Table Orders:', tableOrders);
                
                // First, reset all tables to free
                document.querySelectorAll('.table-item').forEach(table => {
                    const tableName = table.getAttribute('data-table');
                    
                    // Check if table has a reservation
                    let hasReservation = false;
                    for (const [key, reservation] of Object.entries(reservations)) {
                        if (reservation.table === tableName) {
                            hasReservation = true;
                            // Table is reserved - set to blocked
                            table.classList.remove('free', 'utilized');
                            table.classList.add('blocked');
                            console.log(`${tableName}: Reserved (blocked)`);
                            break;
                        }
                    }
                    
                    // If no reservation, check if table has an active order
                    if (!hasReservation && tableOrders[tableName]) {
                        table.classList.remove('free', 'blocked');
                        table.classList.add('utilized');
                        // Add complete order button
                        addCompleteOrderButton(table);
                        console.log(`${tableName}: Has active order (utilized)`);
                    }
                    
                    // If neither reservation nor order, ensure it's free
                    if (!hasReservation && !tableOrders[tableName]) {
                        table.classList.remove('blocked', 'utilized');
                        table.classList.add('free');
                        console.log(`${tableName}: No reservation or order (free)`);
                    }
                });
                
                console.log('Table statuses restored from localStorage');
            }
            
            // Restore custom tables from localStorage
            function restoreCustomTables() {
                const customTables = JSON.parse(localStorage.getItem('customTables') || '[]');
                const floorPlan = document.querySelector('.restaurant-floor-plan');
                
                console.log('Restoring custom tables:', customTables);
                
                customTables.forEach(tableData => {
                    // Create table element
                    const newTable = document.createElement('div');
                    newTable.className = `table-item ${tableData.status}`;
                    newTable.setAttribute('data-table', tableData.name);
                    newTable.style.top = tableData.top;
                    newTable.style.left = tableData.left;
                    
                    // Add table type class
                    newTable.classList.add(tableData.tableClass);
                    
                    // Add table name
                    const nameSpan = document.createElement('span');
                    nameSpan.className = 'table-name';
                    nameSpan.textContent = tableData.name;
                    newTable.appendChild(nameSpan);
                    
                    // Add chairs
                    const chairWrapper = document.createElement('div');
                    chairWrapper.className = 'chair-wrapper';
                    
                    tableData.chairs.forEach(chairClass => {
                        const chair = document.createElement('div');
                        chair.className = `chair ${chairClass}`;
                        chairWrapper.appendChild(chair);
                    });
                    
                    newTable.appendChild(chairWrapper);
                    floorPlan.appendChild(newTable);
                    
                    // Add event listeners
                    addTableEventListeners(newTable);
                    makeDraggable(newTable);
                });
                
                console.log(`Restored ${customTables.length} custom tables`);
            }
            
            // Save custom table to localStorage
            function saveCustomTable(tableElement) {
                const customTables = JSON.parse(localStorage.getItem('customTables') || '[]');
                const tableName = tableElement.getAttribute('data-table');
                
                // Get table class (type)
                let tableClass = '';
                if (tableElement.classList.contains('table-rectangle')) tableClass = 'table-rectangle';
                else if (tableElement.classList.contains('table-rectangle-h10')) tableClass = 'table-rectangle-h10';
                else if (tableElement.classList.contains('table-rectangle-h')) tableClass = 'table-rectangle-h';
                else if (tableElement.classList.contains('table-rounded')) tableClass = 'table-rounded';
                else if (tableElement.classList.contains('table-circle')) tableClass = 'table-circle';
                
                // Get chair classes
                const chairs = [];
                tableElement.querySelectorAll('.chair').forEach(chair => {
                    const classList = Array.from(chair.classList);
                    const chairClass = classList.find(c => c.startsWith('chair-'));
                    if (chairClass) chairs.push(chairClass);
                });
                
                // Get status
                let status = 'free';
                if (tableElement.classList.contains('utilized')) status = 'utilized';
                else if (tableElement.classList.contains('blocked')) status = 'blocked';
                
                const tableData = {
                    name: tableName,
                    tableClass: tableClass,
                    chairs: chairs,
                    top: tableElement.style.top,
                    left: tableElement.style.left,
                    status: status
                };
                
                // Check if table already exists
                const existingIndex = customTables.findIndex(t => t.name === tableName);
                if (existingIndex >= 0) {
                    customTables[existingIndex] = tableData;
                } else {
                    customTables.push(tableData);
                }
                
                localStorage.setItem('customTables', JSON.stringify(customTables));
                console.log('Custom table saved:', tableData);
            }
            
            // Save table position to localStorage (for all tables)
            function saveTablePosition(tableName, tableElement) {
                const tablePositions = JSON.parse(localStorage.getItem('tablePositions') || '{}');
                
                tablePositions[tableName] = {
                    top: tableElement.style.top,
                    left: tableElement.style.left,
                    right: tableElement.style.right,
                    bottom: tableElement.style.bottom,
                    rotation: tableElement.getAttribute('data-rotation') || '0'
                };
                
                localStorage.setItem('tablePositions', JSON.stringify(tablePositions));
            }
            
            // Restore table positions from localStorage
            function restoreTablePositions() {
                const tablePositions = JSON.parse(localStorage.getItem('tablePositions') || '{}');
                
                console.log('Restoring table positions:', tablePositions);
                
                document.querySelectorAll('.table-item').forEach(table => {
                    const tableName = table.getAttribute('data-table');
                    
                    if (tablePositions[tableName]) {
                        const position = tablePositions[tableName];
                        if (position.top) table.style.top = position.top;
                        if (position.left) table.style.left = position.left;
                        if (position.right) table.style.right = position.right;
                        if (position.bottom) table.style.bottom = position.bottom;
                        
                        // Restore rotation
                        if (position.rotation && position.rotation !== '0') {
                            table.setAttribute('data-rotation', position.rotation);
                        }
                        
                        console.log(`Position restored for ${tableName}:`, position);
                    }
                });
                
                console.log('All table positions restored');
            }
            
            // Save area position to localStorage (for Bar, Toilets, Entrance)
            function saveAreaPosition(areaName, areaElement) {
                const areaPositions = JSON.parse(localStorage.getItem('areaPositions') || '{}');
                
                areaPositions[areaName] = {
                    top: areaElement.style.top,
                    left: areaElement.style.left,
                    right: areaElement.style.right,
                    bottom: areaElement.style.bottom
                };
                
                // Save entrance side if it's the entrance
                if (areaName === 'entrance') {
                    areaPositions[areaName].entranceSide = areaElement.getAttribute('data-entrance-side');
                }
                
                localStorage.setItem('areaPositions', JSON.stringify(areaPositions));
            }
            
            // Restore area positions from localStorage
            function restoreAreaPositions() {
                const areaPositions = JSON.parse(localStorage.getItem('areaPositions') || '{}');
                
                console.log('Restoring area positions:', areaPositions);
                
                document.querySelectorAll('[data-area]').forEach(area => {
                    const areaName = area.getAttribute('data-area');
                    
                    if (areaPositions[areaName]) {
                        const position = areaPositions[areaName];
                        if (position.top) area.style.top = position.top;
                        if (position.left) area.style.left = position.left;
                        if (position.right) area.style.right = position.right;
                        if (position.bottom) area.style.bottom = position.bottom;
                        if (position.entranceSide && areaName === 'entrance') {
                            area.setAttribute('data-entrance-side', position.entranceSide);
                            // Don't call updateEntranceCutout here - will be called in setTimeout
                        }
                        
                        console.log(`Position restored for ${areaName}:`, position);
                    }
                });
                
                console.log('All area positions restored');
            }
            
            // Update entrance cutout position based on entrance location
            function updateEntranceCutout(entranceElement, side) {
                const wrapper = entranceElement.closest('.floor-plan-wrapper');
                const floorPlan = entranceElement.closest('.restaurant-floor-plan');
                const cutoutCover = wrapper.querySelector('.entrance-cutout-cover');
                
                if (!cutoutCover) return;
                
                // Always use getBoundingClientRect for accurate positioning
                const entranceRect = entranceElement.getBoundingClientRect();
                const floorPlanRect = floorPlan.getBoundingClientRect();
                
                // Calculate entrance position relative to floor plan
                const entranceTop = entranceRect.top - floorPlanRect.top;
                const entranceLeft = entranceRect.left - floorPlanRect.left;
                const entranceWidth = entranceRect.width;
                const entranceHeight = entranceRect.height;
                
                console.log('Cutout calculation:', {
                    side,
                    entranceTop,
                    entranceLeft,
                    entranceWidth,
                    entranceHeight
                });
                
                if (side === 'right') {
                    // Cover the border line on right side
                    cutoutCover.style.width = '6px';
                    cutoutCover.style.height = '120px';
                    cutoutCover.style.right = '-3px';
                    cutoutCover.style.left = 'auto';
                    cutoutCover.style.top = (entranceTop + entranceHeight / 2 - 60) + 'px';
                    cutoutCover.style.bottom = 'auto';
                } else if (side === 'left') {
                    // Cover the border line on left side
                    cutoutCover.style.width = '6px';
                    cutoutCover.style.height = '120px';
                    cutoutCover.style.left = '-3px';
                    cutoutCover.style.right = 'auto';
                    cutoutCover.style.top = (entranceTop + entranceHeight / 2 - 60) + 'px';
                    cutoutCover.style.bottom = 'auto';
                } else if (side === 'top') {
                    // Cover the border line on top side
                    cutoutCover.style.width = '120px';
                    cutoutCover.style.height = '6px';
                    cutoutCover.style.top = '-3px';
                    cutoutCover.style.bottom = 'auto';
                    cutoutCover.style.left = (entranceLeft + entranceWidth / 2 - 60) + 'px';
                    cutoutCover.style.right = 'auto';
                } else if (side === 'bottom') {
                    // Cover the border line on bottom side
                    cutoutCover.style.width = '120px';
                    cutoutCover.style.height = '6px';
                    cutoutCover.style.bottom = '-3px';
                    cutoutCover.style.top = 'auto';
                    cutoutCover.style.left = (entranceLeft + entranceWidth / 2 - 60) + 'px';
                    cutoutCover.style.right = 'auto';
                }
                
                console.log('Cutout positioned:', {
                    top: cutoutCover.style.top,
                    left: cutoutCover.style.left,
                    right: cutoutCover.style.right,
                    bottom: cutoutCover.style.bottom
                });
            }
            
            // Delete custom table from localStorage
            function deleteCustomTable(tableName) {
                const customTables = JSON.parse(localStorage.getItem('customTables') || '[]');
                const filtered = customTables.filter(t => t.name !== tableName);
                localStorage.setItem('customTables', JSON.stringify(filtered));
                console.log('Custom table deleted:', tableName);
            }
            
            // Clear all data button - now "Manage Tables" button
            const btnClearAllData = document.getElementById('btn-clear-all-data');
            if (btnClearAllData) {
                btnClearAllData.addEventListener('click', function() {
                    // Show modal with all tables (default + custom)
                    const customTables = JSON.parse(localStorage.getItem('customTables') || '[]');
                    const allTables = document.querySelectorAll('.table-item');
                    
                    let tablesList = '<div style="max-height: 400px; overflow-y: auto;"><table class="table table-striped"><thead><tr><th>Table Name</th><th>Chairs</th><th>Status</th><th>Type</th><th>Actions</th></tr></thead><tbody>';
                    
                    allTables.forEach(table => {
                        const tableName = table.getAttribute('data-table');
                        const chairCount = table.querySelectorAll('.chair').length;
                        let status = 'Free';
                        if (table.classList.contains('utilized')) status = 'Utilized';
                        else if (table.classList.contains('blocked')) status = 'Reserved';
                        
                        const isCustom = customTables.some(t => t.name === tableName);
                        const tableType = isCustom ? 'Custom' : 'Default';
                        
                        const deleteBtn = isCustom ? `<button class="btn btn-sm btn-danger delete-table-btn" data-table="${tableName}">Delete</button>` : '<span class="text-muted">-</span>';
                        
                        tablesList += `<tr>
                            <td><strong>${tableName}</strong></td>
                            <td>${chairCount} chairs</td>
                            <td><span class="badge bg-${status === 'Free' ? 'success' : status === 'Utilized' ? 'danger' : 'warning'}">${status}</span></td>
                            <td>${tableType}</td>
                            <td>${deleteBtn}</td>
                        </tr>`;
                    });
                    
                    tablesList += '</tbody></table></div>';
                    
                    // Create custom modal
                    const modalHtml = `
                        <div class="modal fade" id="manageTablesModal" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Manage Tables</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        ${tablesList}
                             
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    // Remove old modal if exists
                    const oldModal = document.getElementById('manageTablesModal');
                    if (oldModal) oldModal.remove();
                    
                    // Add new modal
                    document.body.insertAdjacentHTML('beforeend', modalHtml);
                    
                    // Open modal
                    const manageTablesModal = new bootstrap.Modal(document.getElementById('manageTablesModal'));
                    manageTablesModal.show();
                    
                    // Add delete table functionality
                    document.querySelectorAll('.delete-table-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const tableName = this.getAttribute('data-table');
                            if (confirm(`Are you sure you want to delete table ${tableName}?`)) {
                                // Remove from DOM
                                const tableElement = document.querySelector(`[data-table="${tableName}"]`);
                                if (tableElement) tableElement.remove();
                                
                                // Remove from localStorage
                                deleteCustomTable(tableName);
                                
                                // Close and reopen modal to refresh
                                manageTablesModal.hide();
                                setTimeout(() => btnClearAllData.click(), 300);
                            }
                        });
                    });
                    
                    // Clear all reservations & orders
                    document.getElementById('clear-all-data-btn').addEventListener('click', function() {
                        if (confirm('Are you sure you want to clear all reservations, orders, and positions? This cannot be undone.')) {
                            localStorage.removeItem('tableReservations');
                            localStorage.removeItem('tableOrders');
                            localStorage.removeItem('tablePositions');
                            localStorage.removeItem('areaPositions');
                            
                            document.querySelectorAll('.table-item').forEach(table => {
                                table.classList.remove('blocked', 'utilized');
                                table.classList.add('free');
                            });
                            
                            document.querySelectorAll('.reservation-badge').forEach(badge => badge.remove());
                            
                            alert('All reservations, orders, and positions cleared! Refresh the page to reset positions.');
                            manageTablesModal.hide();
                        }
                    });
                    
                    // Clear all custom tables
                    document.getElementById('clear-custom-tables-btn').addEventListener('click', function() {
                        if (confirm('Are you sure you want to delete all custom tables? This cannot be undone.')) {
                            customTables.forEach(tableData => {
                                const tableElement = document.querySelector(`[data-table="${tableData.name}"]`);
                                if (tableElement) tableElement.remove();
                            });
                            
                            localStorage.removeItem('customTables');
                            alert('All custom tables deleted!');
                            manageTablesModal.hide();
                        }
                    });
                });
            }
            
            // Restore custom tables first (before restoring statuses)
            restoreCustomTables();
            
            // Restore table statuses on page load
            restoreTableStatuses();
            
            // Restore table positions after tables are loaded
            restoreTablePositions();
            
            // Restore area positions (Bar, Toilets, Entrance)
            restoreAreaPositions();
            
            // Wait for DOM to be fully ready, then update entrance cutout
            // Use window.load event AND font loading to ensure all elements are fully rendered
            function initializeEntranceCutout() {
                const entranceArea = document.querySelector('.entrance-area');
                if (!entranceArea) {
                    console.error('❌ Entrance area not found');
                    return;
                }
                
                const entranceSide = entranceArea.getAttribute('data-entrance-side') || 'right';
                
                // Retry logic with multiple attempts
                let retryCount = 0;
                const maxRetries = 10;
                
                function attemptUpdate() {
                    // Force a reflow
                    entranceArea.offsetHeight;
                    
                    const rect = entranceArea.getBoundingClientRect();
                    const computedStyle = window.getComputedStyle(entranceArea);
                    const isVisible = computedStyle.display !== 'none' && computedStyle.visibility !== 'hidden';
                    
                    console.log(`=== Entrance Cutout Attempt ${retryCount + 1} ===`);
                    console.log('Side:', entranceSide);
                    console.log('Rect:', rect.width, 'x', rect.height);
                    console.log('Display:', computedStyle.display, 'Visibility:', computedStyle.visibility);
                    
                    // Check if element has dimensions OR if we've exhausted retries
                    if ((rect.width === 0 || rect.height === 0) && retryCount < maxRetries && isVisible) {
                        retryCount++;
                        console.log(`⚠️ Entrance not rendered yet, retry ${retryCount}/${maxRetries} in 300ms...`);
                        setTimeout(attemptUpdate, 300);
                    } else if (rect.width > 0 && rect.height > 0) {
                        updateEntranceCutout(entranceArea, entranceSide);
                        console.log('✅ Cutout Updated Successfully');
                    } else {
                        // Force update anyway - the cutout calculation will use the entrance position
                        console.warn('⚠️ Forcing cutout update despite 0 dimensions');
                        updateEntranceCutout(entranceArea, entranceSide);
                        console.log('✅ Cutout forced update completed');
                    }
                }
                
                attemptUpdate();
            }
            
            // Wait for fonts to load before initializing
            function startInitialization() {
                if (document.fonts && document.fonts.ready) {
                    document.fonts.ready.then(() => {
                        console.log('📝 Fonts loaded, initializing entrance cutout...');
                        setTimeout(initializeEntranceCutout, 200);
                    });
                } else {
                    // Fallback if fonts API not available
                    setTimeout(initializeEntranceCutout, 500);
                }
            }
            
            // Initialize on window load (ensures all resources loaded)
            if (document.readyState === 'complete') {
                startInitialization();
            } else {
                window.addEventListener('load', startInitialization);
            }
            
            // Also run initial checks
            setTimeout(() => {
                checkReservationTimes();
                console.log('Initial reservation check completed');
            }, 500);
            
            // Check reservation times every minute
            setInterval(checkReservationTimes, 60000); // Check every 60 seconds
            
            // Make Reservation button functionality
            const btnMakeReservation = document.getElementById('btn-make-reservation');
            if (btnMakeReservation) {
                btnMakeReservation.addEventListener('click', function() {
                    // Reset form first
                    document.getElementById('reservation-customer-name').value = '';
                    document.getElementById('reservation-phone').value = '';
                    document.getElementById('reservation-guests').value = '2';
                    document.getElementById('reservation-notes').value = '';
                    document.getElementById('available-tables-list').style.display = 'none';
                    document.getElementById('confirm-reservation').disabled = true;
                    
                    // Set default date to today
                    const today = new Date().toISOString().split('T')[0];
                    document.getElementById('reservation-date').value = today;
                    document.getElementById('reservation-date').min = today;
                    
                    // Set default time to current time (always fresh)
                    const now = new Date();
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    document.getElementById('reservation-time').value = `${hours}:${minutes}`;
                    
                    const reservationModal = new bootstrap.Modal(document.getElementById('makeReservationModal'));
                    reservationModal.show();
                });
            }
            
            // Search available tables
            let selectedTableForReservation = null;
            const searchAvailableBtn = document.getElementById('search-available-tables');
            if (searchAvailableBtn) {
                searchAvailableBtn.addEventListener('click', function() {
                    const guests = parseInt(document.getElementById('reservation-guests').value);
                    const date = document.getElementById('reservation-date').value;
                    const time = document.getElementById('reservation-time').value;
                    const customerName = document.getElementById('reservation-customer-name').value;
                    
                    if (!customerName || !date || !time) {
                        alert('{{ __("Please fill in customer name, date and time") }}');
                        return;
                    }
                    
                    // Get all tables
                    const allTables = document.querySelectorAll('.table-item');
                    const reservations = JSON.parse(localStorage.getItem('tableReservations') || '{}');
                    const availableTables = [];
                    
                    allTables.forEach(table => {
                        const tableName = table.getAttribute('data-table');
                        const chairCount = table.querySelectorAll('.chair').length;
                        
                        // Check if table has enough chairs
                        if (chairCount >= guests) {
                            // Check if table is not reserved at this time
                            const reservationKey = `${tableName}_${date}_${time}`;
                            if (!reservations[reservationKey] && !table.classList.contains('utilized')) {
                                availableTables.push({
                                    name: tableName,
                                    chairs: chairCount,
                                    element: table
                                });
                            }
                        }
                    });
                    
                    // Display available tables
                    const container = document.getElementById('available-tables-container');
                    container.innerHTML = '';
                    
                    if (availableTables.length === 0) {
                        container.innerHTML = '<p class="text-danger">{{ __("No available tables found for this time and guest count") }}</p>';
                        document.getElementById('available-tables-list').style.display = 'block';
                        return;
                    }
                    
                    availableTables.forEach(table => {
                        const tableBtn = document.createElement('button');
                        tableBtn.className = 'btn btn-outline-success';
                        tableBtn.textContent = `${table.name} (${table.chairs} {{ __("chairs") }})`;
                        tableBtn.onclick = function() {
                            // Remove selection from other buttons
                            container.querySelectorAll('.btn').forEach(btn => {
                                btn.classList.remove('btn-success');
                                btn.classList.add('btn-outline-success');
                            });
                            // Select this button
                            this.classList.remove('btn-outline-success');
                            this.classList.add('btn-success');
                            selectedTableForReservation = table;
                            document.getElementById('confirm-reservation').disabled = false;
                        };
                        container.appendChild(tableBtn);
                    });
                    
                    document.getElementById('available-tables-list').style.display = 'block';
                });
            }
            
            // Confirm reservation
            const confirmReservationBtn = document.getElementById('confirm-reservation');
            console.log('Confirm reservation button found:', confirmReservationBtn);
            console.log('Button exists?', confirmReservationBtn !== null);
            
            if (confirmReservationBtn) {
                console.log('Adding click event listener to confirm reservation button...');
                confirmReservationBtn.addEventListener('click', function() {
                    console.log('✅ Confirm reservation clicked!');
                    console.log('Selected table:', selectedTableForReservation);
                    
                    if (!selectedTableForReservation) {
                        alert('{{ __("Please select a table") }}');
                        return;
                    }
                    
                    const customerName = document.getElementById('reservation-customer-name').value;
                    const phone = document.getElementById('reservation-phone').value;
                    const date = document.getElementById('reservation-date').value;
                    const time = document.getElementById('reservation-time').value;
                    const guests = parseInt(document.getElementById('reservation-guests').value);
                    const notes = document.getElementById('reservation-notes').value;
                    
                    console.log('Reservation data:', {customerName, phone, date, time, guests, notes});
                    
                    // Validate required fields
                    if (!customerName || !date || !time) {
                        alert('{{ __("Please fill in customer name, date and time") }}');
                        return;
                    }
                    
                    // Validate: guests cannot exceed table chairs
                    if (guests > selectedTableForReservation.chairs) {
                        alert(`{{ __("Number of guests") }} (${guests}) {{ __("cannot exceed table capacity") }} (${selectedTableForReservation.chairs} {{ __("chairs") }}). {{ __("Please select a larger table or reduce guests.") }}`);
                        return;
                    }
                    
                    // Save reservation
                    const reservations = JSON.parse(localStorage.getItem('tableReservations') || '{}');
                    const reservationKey = `${selectedTableForReservation.name}_${date}_${time}`;
                    
                    console.log('Reservation key:', reservationKey);
                    
                    reservations[reservationKey] = {
                        table: selectedTableForReservation.name,
                        customerName: customerName,
                        phone: phone,
                        date: date,
                        time: time,
                        guests: guests,
                        notes: notes,
                        timestamp: new Date().toISOString()
                    };
                    
                    console.log('Saving to localStorage:', reservations);
                    localStorage.setItem('tableReservations', JSON.stringify(reservations));
                    
                    // Verify save
                    const savedData = localStorage.getItem('tableReservations');
                    console.log('✅ Saved! Verifying:', savedData);
                    console.log('✅ Parsed saved data:', JSON.parse(savedData));
                    
                    // Change table status to blocked (reserved)
                    selectedTableForReservation.element.classList.remove('free', 'utilized');
                    selectedTableForReservation.element.classList.add('blocked');
                    console.log('Table status changed to blocked');
                    
                    // Immediately add reservation badge to the table
                    console.log('✅ Calling checkReservationTimes to add badge...');
                    checkReservationTimes();
                    console.log('✅ Badge added, now closing modal...');
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('makeReservationModal'));
                    modal.hide();
                    
                    // Reset form
                    document.getElementById('reservation-customer-name').value = '';
                    document.getElementById('reservation-phone').value = '';
                    document.getElementById('reservation-guests').value = '2';
                    document.getElementById('reservation-guests').removeAttribute('max'); // Remove max limit
                    document.getElementById('reservation-notes').value = '';
                    document.getElementById('available-tables-list').style.display = 'none';
                    document.getElementById('confirm-reservation').disabled = true;
                    selectedTableForReservation = null;
                    
                    alert(`{{ __("Reservation confirmed for") }} ${customerName} {{ __("at table") }} ${reservations[reservationKey].table}`);
                });
            }
            
            // Add draggable to all existing tables
            document.querySelectorAll('.table-item').forEach(table => {
                makeDraggable(table);
            });
            
            // Add draggable to static areas (Bar, Toilets, Entrance)
            document.querySelectorAll('[data-area]').forEach(area => {
                makeDraggable(area);
            });
            
            document.addEventListener('mousemove', function(e) {
                if (!isDragging || !currentTable) return;
                
                const parent = currentTable.offsetParent;
                const parentRect = parent.getBoundingClientRect();
                
                let newX = e.clientX - parentRect.left - offsetX;
                let newY = e.clientY - parentRect.top - offsetY;
                
                // Check if this is the entrance area
                const isEntrance = currentTable.getAttribute('data-area') === 'entrance';
                
                if (isEntrance) {
                    // Entrance can move freely, but snap to nearest edge
                    const tableWidth = currentTable.offsetWidth;
                    const tableHeight = currentTable.offsetHeight;
                    
                    // Calculate distances to each edge
                    const distToLeft = newX;
                    const distToRight = parent.offsetWidth - (newX + tableWidth);
                    const distToTop = newY;
                    const distToBottom = parent.offsetHeight - (newY + tableHeight);
                    
                    // Find nearest edge
                    const minDist = Math.min(distToLeft, distToRight, distToTop, distToBottom);
                    
                    let entranceSide = 'right';
                    
                    if (minDist === distToLeft) {
                        // Snap to left edge
                        entranceSide = 'left';
                        currentTable.style.left = '20px';
                        currentTable.style.right = 'auto';
                        newY = Math.max(0, Math.min(newY, parent.offsetHeight - tableHeight));
                        currentTable.style.top = newY + 'px';
                        currentTable.style.bottom = 'auto';
                    } else if (minDist === distToRight) {
                        // Snap to right edge
                        entranceSide = 'right';
                        currentTable.style.right = '20px';
                        currentTable.style.left = 'auto';
                        newY = Math.max(0, Math.min(newY, parent.offsetHeight - tableHeight));
                        currentTable.style.top = newY + 'px';
                        currentTable.style.bottom = 'auto';
                    } else if (minDist === distToTop) {
                        // Snap to top edge
                        entranceSide = 'top';
                        currentTable.style.top = '20px';
                        currentTable.style.bottom = 'auto';
                        newX = Math.max(0, Math.min(newX, parent.offsetWidth - tableWidth));
                        currentTable.style.left = newX + 'px';
                        currentTable.style.right = 'auto';
                    } else {
                        // Snap to bottom edge
                        entranceSide = 'bottom';
                        currentTable.style.bottom = '20px';
                        currentTable.style.top = 'auto';
                        newX = Math.max(0, Math.min(newX, parent.offsetWidth - tableWidth));
                        currentTable.style.left = newX + 'px';
                        currentTable.style.right = 'auto';
                    }
                    
                    // Update entrance side attribute
                    currentTable.setAttribute('data-entrance-side', entranceSide);
                    
                    // Update cutout position
                    updateEntranceCutout(currentTable, entranceSide);
                } else {
                    // Normal dragging for tables and other areas
                    const tableWidth = currentTable.offsetWidth;
                    const tableHeight = currentTable.offsetHeight;
                    
                    newX = Math.max(0, Math.min(newX, parent.offsetWidth - tableWidth));
                    newY = Math.max(0, Math.min(newY, parent.offsetHeight - tableHeight));
                    
                    currentTable.style.left = newX + 'px';
                    currentTable.style.top = newY + 'px';
                    currentTable.style.right = 'auto';
                    currentTable.style.bottom = 'auto';
                }
            });
            
            document.addEventListener('mouseup', function() {
                if (isDragging && currentTable) {
                    currentTable.style.cursor = 'move';
                    
                    // Check if it's a table or an area
                    const tableName = currentTable.getAttribute('data-table');
                    const areaName = currentTable.getAttribute('data-area');
                    
                    if (tableName) {
                        // Save position for tables
                        saveTablePosition(tableName, currentTable);
                        
                        // Check if this is a custom table and save its full data
                        const customTables = JSON.parse(localStorage.getItem('customTables') || '[]');
                        const isCustomTable = customTables.some(t => t.name === tableName);
                        
                        if (isCustomTable) {
                            saveCustomTable(currentTable);
                            console.log('Custom table position updated:', tableName);
                        } else {
                            console.log('Default table position saved:', tableName);
                        }
                    } else if (areaName) {
                        // Save position for static areas
                        saveAreaPosition(areaName, currentTable);
                        console.log('Area position saved:', areaName);
                    }
                    
                    isDragging = false;
                    currentTable = null;
                }
            });
        });
        
        // Position sidebar to start from pos-main-container and stick on scroll
        function positionSidebar() {
            const mainContainer = document.querySelector('.pos-main-container');
            const sidebar = document.querySelector('.order-sidebar');
            
            if (mainContainer && sidebar && window.innerWidth > 992) {
                const containerTop = mainContainer.offsetTop;
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                
                if (scrollTop >= containerTop) {
                    // When scrolled past container, stick to top
                    sidebar.style.top = '0px';
                } else {
                    // Before scroll, align with container
                }
            }
        }
        
        // Run on load, scroll, and resize
        window.addEventListener('load', positionSidebar);
        window.addEventListener('scroll', positionSidebar);
        window.addEventListener('resize', positionSidebar);
    </script>
    <script src="{{ asset('assets/js/choices.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom/sale.js') . '?v=' . time() }}"></script>
    <script src="{{ asset('assets/js/custom/math.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom/calculator.js') }}"></script>
    <script src="{{ asset('assets/js/custom/pos-products.js') . '?v=' . time() }}"></script>
    <script src="{{ asset('assets/js/custom/pos-payment-modal.js') . '?v=' . time() }}"></script>
@endpush
