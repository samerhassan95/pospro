    <style>
        /* Force RTL SVG flip */
        @if (in_array(app()->getLocale(), ['ar', 'arbh', 'eg-ar', 'fa', 'prs', 'ps', 'ur']))
        [dir="rtl"] .pos-top-nav a[href*="dashboard"] svg {
            transform: scaleX(-1) !important;
        }
        @endif
        .pos-fullscreen-body { margin: 0; padding: 0; background: #f5f5f5; height: 100vh; overflow: hidden; }
        .pos-fullscreen-wrapper { width: 100%; height: 100vh; display: flex; flex-direction: column; }
        
        /* Free-flowing layout on small screens */
        @media (max-width: 768px) {
            .pos-fullscreen-body { height: auto; overflow: auto; min-height: 100vh; }
            .pos-fullscreen-wrapper { height: auto; min-height: 100vh; }
        }
        .pos-top-header {width:fit-content; padding: 6px 8px 6px 8px; border-radius:30px; display: flex; flex-wrap: wrap; gap:10px; align-items: center; background: white !important; box-shadow: 4px 6px 20px 3px #7090B014; }
        .pos-badge { display: flex; align-items: center; justify-content: center; padding: 8px 16px; background: var(--clr-primary); color: #fff; border-radius: 100px; font-size: 14px; font-weight: 600; min-width: 60px; }
        .pos-badge span { color: #fff; }
        .pos-brand { display: flex; align-items: center; gap: 12px; }
        .pos-brand-title { font-size: 20px; font-weight: 700; color: #1a1a1a; margin: 0; }
        .pos-brand-subtitle { font-size: 12px; color: #E6E6E6; margin: 0; }
        .pos-top-nav { background: white; padding: 8px 8px; border-radius: 100px;  display: flex; flex-wrap: wrap; flex-wrap: wrap; align-items: center; gap: 8px; }
        .pos-nav-btn { width: 40px; height: 40px; border-radius: 8px; border: none; background: #fff; display: flex; align-items: center; justify-content: center; color: #374151; cursor: pointer; transition: all 0.2s; text-decoration: none; flex-shrink: 0; }
        .pos-nav-btn:hover { background: #f9fafb; color: #1a1a1a; }
        .pos-nav-btn.active { background: var(--clr-primary) !important; color: #fff !important; }
        .pos-nav-btn.active svg { color: #fff !important; }
        .pos-nav-btn.active svg path { fill: #fff !important; stroke: #fff !important; }
        .pos-nav-btn i { font-size: 16px; }
        .pos-nav-btn svg { width: 20px; height: 20px; flex-shrink: 0; }
        .pos-nav-divider { width: 1px; height: 24px; background: #e5e7eb; margin: 0 8px; }
        .pos-add-expense-btn { display: flex; align-items: center; gap: 8px; padding: 12px 24px; background: var(--clr-primary); border: none; border-radius: 100px; color: #fff; font-size: 16px; font-weight: 600; cursor: pointer; text-decoration: none; }
        .pos-add-expense-btn:hover { background: var(--clr-secondary); color: #fff; }
        .pos-add-expense-btn svg { width: 24px; height: 24px; flex-shrink: 0; }
        .pos-header-btn { display: flex; align-items: center; gap: 8px; padding: 10px 20px; background: #fff; border: 1px solid #e5e7eb; border-radius: 100px; color: #374151; font-size: 14px; font-weight: 500; cursor: pointer; text-decoration: none; transition: all 0.2s; }
        .pos-header-btn:hover { background: #f9fafb; color: #1a1a1a; border-color: #d1d5db; }
        .pos-header-btn svg { width: 20px; height: 20px; flex-shrink: 0; }

        /* Brand/Category Toggle Wrapper */
        .pos-toggle-wrapper { display: flex; align-items: center;  border: 1px solid #e5e7eb; border-radius: 100px; padding: 4px; gap: 4px; }
        .pos-toggle-btn { display: flex; align-items: center; gap: 8px; padding: 8px 16px; background: transparent; border: none; border-radius: 100px; color: #6b7280; font-size: 14px; font-weight: 500; cursor: pointer; text-decoration: none; transition: all 0.3s; min-width: 80px; justify-content: center; }
        .pos-toggle-btn:hover { color: #374151; }
        .pos-toggle-btn-active { background: var(--clr-primary) !important; color: #fff !important; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .pos-toggle-btn-active:hover { color: #fff !important; }
        .pos-main-container { display: flex; gap: 20px; padding: 20px; background: #F7F7F7 !important; flex: 1; min-height: 0; height:100vh; overflow: hidden; }
        @media (max-width: 1200px) { .pos-main-container { gap: 15px; } }
        @media (max-width: 992px) { .pos-main-container { flex-direction: column; } }
        @media (max-width: 768px) { 
            .pos-main-container { 
                height: auto; 
                min-height: calc(100vh - 100px); 
                overflow: visible; 
            } 
        }
        @media (max-width: 992px) { .pos-main-container { flex-direction: column; } }
        
        .pos-left-column { flex: 1; display: flex; flex-direction: column; min-height: 0; }
        .order-sidebar { width: 420px; background: #fff; border-radius: 12px; padding: 16px; display: flex; flex-direction: column; max-height: calc(100vh - 10px); }
        @media (max-width: 1200px) { .order-sidebar { width: 380px; } }
        @media (max-width: 992px) { .order-sidebar { width: 100%; max-height: none; } }

        /* Sidebar Sections */
        .sidebar-search-section { margin-bottom: 20px; }
        .search-input-wrapper { position: relative; display: flex; align-items: center; margin-bottom: 8px; }
        .search-icon { position: absolute; left: 12px; z-index: 2; }
        .search-customer-input { width: 100%; padding: 12px 12px 12px 40px; border: 1px solid #E5E7EB; border-radius: 8px; font-size: 14px; color: #9CA3AF; background: #F9FAFB; appearance: none; }
        .dropdown-icon { position: absolute; right: 12px; z-index: 2; }
        .add-customer-btn { width: 48px; height: 48px; background: #1E3A8A; border: none; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-left: 8px; cursor: pointer; }

        /* Order Details Section */
        .order-details-section { margin-bottom: 3px; }
        .section-title { font-size: 18px; font-weight: bold; color: #1F2937; }
        .customer-name { font-size: 16px; font-weight: 600; color: #1F2937;  }
        .order-info-line { display: flex; align-items: center; gap: 8px; font-size: 14px; color: #6B7280; }
        .order-date { color: #000000; }
        .clock-icon { flex-shrink: 0; }
        .order-time { color: #000000; }
        .phone-number { color: #000000; margin-left: auto; }

        /* Delivery Tabs */
        .delivery-tabs { display: flex; gap: 8px; margin-bottom: 20px; }
        .delivery-tab { padding: 8px 16px; border: 1px solid #E5E7EB; background: #F9FAFB; border-radius: 20px; font-size: 14px; color: #6B7280; cursor: pointer; transition: all 0.2s; }
        .delivery-tab.active { background: #1E3A8A; color: white; border-color: #1E3A8A; }

        /* Products Section */
        .products-section { flex: 1;  display: flex; flex-direction: column; min-height: 0; }
        .products-header { display: flex; justify-content: space-between; align-items: center; flex-shrink: 0; }
        .clear-all-btn { display: flex; align-items: center; gap: 6px; background: none; border: none; color: #E53030; font-size: 14px; font-weight: 500; cursor: pointer; }
        .clear-all-btn:hover { color: #dc2626; }
        .products-list { flex: 1; overflow-y: auto; min-height: 0; }
        .products-list::-webkit-scrollbar { width: 4px; }
        .products-list::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
        .products-list::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }

        /* Sidebar Cart Items - Horizontal Layout like image */
        .sidebar-cart-item { display: flex; justify-content: space-between; align-items: center; padding: 6px 0; border-bottom: none !important; }
        .sidebar-cart-item:last-child { border-bottom: none; }
        
        .cart-item-content { display: flex; align-items: center; gap: 16px; flex: 1; min-width: 0; }
        .cart-item-name { font-size: 16px; font-weight: 500; color: #1F2937; margin: 0; line-height: 1.3; }
        .cart-item-price { font-size: 14px; color:#000000 ; margin: 0; font-weight: bold !important; }
        
        .cart-item-controls { display: flex; align-items: center; gap: 4px; flex-shrink: 0; }
        
        .qty-btn { width: 20px; height: 20px; border: none; background: transparent; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; padding: 0; }
        .qty-btn:hover { transform: scale(1.1); }
        .cart-qty-display { font-size: 16px; font-weight: 600; color: #1F2937; min-width: 20px; text-align: center; margin: 0 4px; }
        
        .remove-item-btn { width: 20px; height: 20px; border: none; background: transparent; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; padding: 0; }
        .remove-item-btn:hover { transform: scale(1.1); }

        /* Order Summary Section */
        .order-summary-section { border: 1px solid #E5E7EB; border-radius: 8px; padding: 8px; margin-bottom: 6px; }
        .summary-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0px; }
        .summary-label { font-size: 14px; color: #000000; }
        .summary-value { font-size: 14px; font-weight: 500; color: #1F2937; }
        .summary-row.summary-total { font-size: 18px; font-weight: bold; color: #1F2937; margin-top: 8px; padding-top: 8px; border-top: 1px solid #E5E7EB; }
        .summary-row.summary-total .summary-label { font-weight: bold; font-size:18px; color: #000000; }
        .summary-row.summary-total .summary-value { font-weight: 700; color: #1F2937; }

        /* Action Buttons Section */
        .action-buttons-section { display: flex; gap: 12px; }
        .pay-bill-btn { flex: 1; padding: 16px 24px; background: var(--clr-primary); color: white; border: none; border-radius: 50px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
        .pay-bill-btn:hover { background: var(--clr-secondary); }
        .cancel-order-btn { flex: 1; padding: 16px 24px; background: transparent; color: var(--clr-primary); border: 1px solid #E5E7EB; border-radius: 50px; font-size: 16px; font-weight: 500; cursor: pointer; transition: all 0.2s; }
        .cancel-order-btn:hover { background: #F9FAFB; border-color: var(--clr-primary); }
        .order-header { margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #f0f0f0; }
        .order-title { font-size: 18px; font-weight: 600; color: #1a1a1a; margin: 0; }
        .order-date { font-size: 13px; color: #666;  }
        .supplier-section { margin-bottom: 20px; }
        .supplier-select-wrapper { display: flex; gap: 10px; align-items: center; }
        .supplier-select-wrapper .form-select, .supplier-select-wrapper .choices { flex: 1; }
        .add-supplier-btn { width: 40px; height: 40px; border-radius: 8px; background: var(--clr-primary); border: none; display: flex; align-items: center; justify-content: center; color: #fff; flex-shrink: 0; }
        .add-supplier-btn:hover { background: var(--clr-secondary); }
        .customer-section { margin-bottom: 20px; }
        .customer-select-wrapper { display: flex; gap: 10px; align-items: center; }
        .customer-select-wrapper .form-select, .customer-select-wrapper .choices { flex: 1; }
        .add-customer-btn { width: 40px; height: 40px; border-radius: 8px; background: var(--clr-primary); border: none; display: flex; align-items: center; justify-content: center; color: #fff; flex-shrink: 0; }
        .add-customer-btn:hover { background: var(--clr-secondary); }
        .guest-phone-field { margin-top: 10px; }
        .guest-phone-field input { width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; }
        .guest-phone-field input:focus { outline: none; border-color: var(--clr-primary); }
        .cart-section { margin-bottom: 15px; display: flex; flex-direction: column; }
        .cart-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .cart-title { font-size: 16px; font-weight: 600; color: #1a1a1a; }
        .clear-cart-btn { font-size: 13px; color: var(--clr-primary); background: none; border: none; cursor: pointer; display: flex; align-items: center; gap: 4px; }
        .cart-items-list { display: flex; flex-direction: column; gap: 10px; padding-right: 5px; }
        .cart-items-list::-webkit-scrollbar { width: 4px; }
        .cart-items-list::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
        .cart-items-list::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }
        .cart-item-card { display: flex; align-items: stretch; background: #fff; border-bottom: 1px solid #eee; border-radius: 14px; overflow: hidden; flex-shrink: 0; }
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
        .remove-item-btn { width: 28px; height: 28px; border-radius: 50%; background: #fff;  display: flex; align-items: center; justify-content: center; cursor: pointer; color: #dc3545; font-size: 12px; padding: 0; transition: all 0.2s; }
        .remove-item-btn:hover { background: #dc3545; color: #fff; transform: scale(1.1); }
        .remove-item-btn:active { transform: scale(0.95); }
        .empty-cart { text-align: center; padding: 30px 20px; color: #9ca3af; }
        .empty-cart-icon { font-size: 40px; margin-bottom: 10px; color: #ddd; }
        .order-summary { border-top: 1px solid #eee; padding-top: 15px; margin-bottom: 15px; }
        .summary-row { display: flex; justify-content: space-between; align-items: center; padding: 0px 0; font-size: 14px; color: #666; }
        .summary-row.total { font-size: 16px; font-weight: 600; color: #1a1a1a; border-top: 1px solid #eee; padding-top: 12px; margin-top: 8px; }
        .cancel-order-btn { width: 100%; padding: 12px; border: none; background: #fff; color: var(--clr-primary); border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; margin-bottom: 6px; }
        .cancel-order-btn:hover { background: #fff5f5; }
        .payment-section-new { border-top: 1px solid #eee; padding-top: 20px; }
        .payment-field { margin-bottom: 15px; }
        .payment-field label { display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px; }
        .payment-field input, .payment-field select { width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; }
        .payment-field input:focus, .payment-field select:focus { outline: none; border-color: var(--clr-primary); }
        .save-order-btn { width: 100%; padding: 14px; background: var(--clr-primary); color: #fff; border: none; border-radius: 8px; font-size: 15px; font-weight: 500; cursor: pointer; margin-top: 10px; }
        .save-order-btn:hover { background: var(--clr-secondary); }
        .products-section {  display: flex; flex-direction: column; overflow: hidden; }

        /* Tabs */
        .pos-tabs-wrapper { display: flex; gap: 12px; padding-bottom: 24px;  border-bottom:2px solid #f0f0f0; }
        .pos-tab-btn {    background: transparent; padding: 10px 24px; border:2px solid #f0f0f0; color: #666; border-radius: 100px; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.3s; }
        .pos-tab-btn.active { background: var(--clr-primary); color: #fff; }
        .pos-tab-btn:hover:not(.active) {   background: transparent; }

        /* Category Section */
        .pos-category-section { margin-top: 10px; }
        .pos-section-title { font-size: 20px; font-weight: 700; color: #1a1a1a; margin: 0 0 16px 0; }
        .pos-category-scroll-wrapper { position: relative; overflow-x: auto; }
        .pos-category-scroll-wrapper::-webkit-scrollbar {
            height: 6px;
        }
        .pos-category-scroll-wrapper::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .pos-category-scroll-wrapper::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 10px;
        }
        .pos-category-scroll-wrapper::-webkit-scrollbar-thumb:hover {
            background: #999;
        }
        .pos-category-list { display: flex; gap: 8px; scroll-behavior: smooth; padding: 8px 0; flex-wrap: nowrap; }
        .pos-brand-list { display: flex; gap: 8px; scroll-behavior: smooth; padding: 8px 0; flex-wrap: nowrap; }

        /* Category/Brand Items - Horizontal tabs like image */
        .pos-category-item, .pos-brand-item { 
              display: flex; 
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
            white-space: nowrap;
        }
        .pos-category-item:hover, .pos-brand-item:hover { 
            border-color: #9CA3AF; 
            transform: translateY(-2px); 
            box-shadow: 0 4px 12px rgba(0,0,0,0.08); 
        }
        .pos-category-item.active, .pos-brand-item.active { 
            border-color: #1F2937; 
            background: #fff; 
        }
        .pos-category-icon, .pos-brand-icon { 
            width: 22px; 
            height: 22px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            flex-shrink: 0; 
        }
        .pos-category-icon img, .pos-brand-icon img { 
            max-width: 100%; 
            max-height: 100%; 
            width: auto; 
            height: auto; 
            object-fit: contain; 
        }
        .pos-category-icon svg, .pos-brand-icon svg { 
            width: 36px; 
            height: 36px; 
            color: #1F2937; 
            stroke-width: 1.5;
        }
        .pos-category-item.active .pos-category-icon svg, 
        .pos-brand-item.active .pos-brand-icon svg { 
            color: #1F2937; 
        }
        .pos-category-name, .pos-brand-name { 
            font-size: 16px; 
            font-weight: 500; 
            color: #1F2937; 
        }
        .pos-category-item.active .pos-category-name,
        .pos-brand-item.active .pos-brand-name { 
            color: #1F2937; 
            font-weight: 600;
        }

        /* Limit visible categories on small screens */
        @media (max-width: 768px) {
            .pos-category-scroll-wrapper { max-width: 100%; overflow: hidden; }
            .pos-category-list { max-width: 100%; } 
        }

        @media (max-width: 576px) {
            .pos-category-list { max-width: 100%; }
        }

        /* Products Grid - Single column for horizontal cards */
        .pos-products-section  { 
            flex: 1; 
            overflow-y: auto; 
            min-height: 0;
            padding-bottom:20px;
            max-height: calc(100vh - 0px);
        }
        
        /* Free-flowing products on small screens */
        @media (max-width: 768px) {
            .pos-products-section {
                overflow-y: visible;
                max-height: none;
                height: auto;
            }
        }
        
        #brand-products-list{
            padding-bottom:20px;
        }
        .pos-products-section::-webkit-scrollbar {
            width: 6px;
        }
        .pos-products-section::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .pos-products-section::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 10px;
        }
        .pos-products-section::-webkit-scrollbar-thumb:hover {
            background: #999;
        }
        .pos-menu-title { font-size: 20px; font-weight: 700; color: #1F2937; margin: 0 0 5px 0; }
        .pos-products-grid { display: flex; flex-direction: column; gap: 10px; padding-bottom: 5px; }

        /* Product Card - Horizontal layout like image */
        .pos-product-card { 
            background: #fff; 
            border: 1px solid #E5E7EB; 
            border-radius: 8px; 
            overflow: hidden; 
            transition: all 0.3s; 
            display: flex; 
            flex-direction: row;
            align-items: stretch;
            cursor: pointer;
        }
        .pos-product-card:hover { 
            box-shadow: 0 4px 12px rgba(0,0,0,0.08); 
            transform: translateY(-2px); 
            border-color: #D1D5DB;
        }
        .pos-product-top { 
            display: flex; 
            flex: 1;
            align-items: stretch;
        }
        .pos-product-image-wrapper { 
            width: 120px; 
            min-width: 120px;
            height: auto; 
            overflow: hidden; 
            background: #F9FAFB; 
            border-radius: 12px;
            /* margin: 8px; */
            flex-shrink: 0;
        }
        .pos-product-image { 
            width: 100%; 
            height: 100%; 
            object-fit: cover; 
        }
        .pos-product-info { 
            flex: 1; 
            padding: 0px 0px 0px 0px; 
            display: flex; 
            flex-direction: column; 
            justify-content:start;
            align-items:start;
            min-width: 0;
        }
        .pos-product-body { 
            display: none; 
        }
        .pos-product-header { 
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .pos-product-name { 
            font-size: 16px; 
            font-weight: 600; 
            color: #1F2937; 
            margin: 0; 
            line-height: 1.3;
        }
        .pos-product-desc { 
            font-size: 13px; 
            color: #919191; 
            margin: 0; 
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .pos-product-price { 
            font-size: 14px; 
            font-weight: 700; 
            color: #1F2937; 
            /* margin-top: 8px; */
        }

        /* Category Header with Arrows */
        .pos-category-header { display: flex !important; justify-content: flex-start !important; align-items: center !important; margin-bottom: 20px !important; gap: 20px !important; }
        .pos-section-title { font-size: 22px !important; font-weight: 700 !important; color: #000 !important; margin: 0 !important; flex: 0 0 auto !important; }
        .pos-category-nav-buttons { display: flex !important; gap: 8px !important; align-items: center !important; }
        .pos-category-scroll-btn { position: static !important; transform: none !important; width: 36px !important; height: 36px !important; border-radius: 50% !important; background: #fff !important; border: 2px solid #e0e0e0 !important; display: flex !important; align-items: center !important; justify-content: center !important; cursor: pointer !important; box-shadow: none !important; transition: all 0.3s !important; flex-shrink: 0 !important; }
        .pos-category-scroll-btn:hover { background: #f5f5f5 !important; border-color: var(--clr-primary) !important; }
        .pos-category-scroll-btn.active { background: var(--clr-primary) !important; border-color: var(--clr-primary) !important; }
        .pos-category-scroll-btn.active:hover { background: var(--clr-secondary) !important; }
        .pos-category-scroll-btn.disabled { opacity: 0.3 !important; cursor: not-allowed !important; pointer-events: none !important; }
        .pos-category-scroll-btn svg { color: #666 !important; width: 18px !important; height: 18px !important; }
        .pos-category-scroll-btn.active svg { color: #fff !important; }

        /* Product Options - Hidden for cleaner look */
        .pos-product-options { 
            display: none !important; 
        }

        /* Add to Cart Button - Hidden for cleaner look */
        .pos-add-to-cart-btn { 
            display: none !important;
        }

        .hidden-cart-inputs { display: none; }

        /* Responsive */
        @media (max-width: 768px) { 
            .pos-product-image-wrapper { 
                width: 100px; 
                min-width: 100px;
                height: auto; 
            }
            .pos-product-name { 
                font-size: 15px; 
            }
            .pos-product-desc { 
                font-size: 12px; 
            }
            .pos-product-price { 
                font-size: 14px; 
            }
        }

        /* Table Reservation System */
        .tables-reservation-section { padding: 20px 0; }
        .table-management-buttons { display: flex; flex-wrap:wrap; gap: 6px; margin-bottom: 10px; }
        .btn-add-table, .btn-manage-tables { display: flex; align-items: center; gap: 8px; padding: 12px 24px; background: var(--clr-primary); color: #fff; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s; }
        .btn-add-table:hover, .btn-manage-tables:hover { background: var(--clr-secondary); transform: translateY(-2px); }
        .btn-manage-tables { background: #374151; }
        .btn-manage-tables:hover { background: #1f2937; }
        .table-legend { display: flex; gap: 10px; margin-bottom: 10px; flex-wrap: wrap; }
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

        .table-controls { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 10px; }

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
        .toggle-input:checked + .toggle-slider { background: var(--clr-primary); }
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
        .form-control:focus, .form-select:focus { border-color: var(--clr-primary); box-shadow: 0 0 0 3px rgba(255, 101, 0, 0.1); outline: none; }
        .modal-footer { padding: 20px 24px;  border-top: 1px solid #e5e7eb; }
        .btn-primary { background: var(--clr-primary); border-color: var(--clr-primary); padding: 12px 24px; font-weight: 600; border-radius: 8px; transition: all 0.2s;  color:#FFFFFF;}
        .btn-deleted { background: #bb2d3b; border-color: var(--clr-primary); padding: 12px 24px; font-weight: 600; border-radius: 8px; transition: all 0.2s;  color:#FFFFFF;}
        .btn-primary:hover { background: var(--clr-secondary); border-color: var(--clr-secondary); transform: translateY(-1px); color: white;}
        .btn-deleted:hover { background: #bb2d3b; border-color: var(--clr-secondary); transform: translateY(-1px); color: white;}
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

        /* TABLES VIEW - VERTICAL SCROLL ONLY */
        #tables-view {
            flex: 1 !important;
            padding-top: 10px;
            height: calc(100vh - 120px) !important;
            width: 100% !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
        }
        #tables-view::-webkit-scrollbar {
            width: 6px !important;
        }
        #tables-view::-webkit-scrollbar-track {
            background: #f1f1f1 !important;
            border-radius: 10px !important;
        }
        #tables-view::-webkit-scrollbar-thumb {
            background: #ccc !important;
            border-radius: 10px !important;
        }
        #tables-view::-webkit-scrollbar-thumb:hover {
            background: #999 !important;
        }

        /* Search View Table Responsive */
        .responsive-table {
            overflow-x: auto;
        }
        
        @media (max-width: 768px) {
            .responsive-table {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            .responsive-table::-webkit-scrollbar {
                height: 6px;
            }
            .responsive-table::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 10px;
            }
            .responsive-table::-webkit-scrollbar-thumb {
                background: #ccc;
                border-radius: 10px;
            }
            .responsive-table::-webkit-scrollbar-thumb:hover {
                background: #999;
            }
            .responsive-table table {
                min-width: 800px;
            }
        }
    </style>