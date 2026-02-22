@extends('layouts.business.pos')

@section('title')
    {{ __('Edit Purchase') }}
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/choices.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/calculator.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pos-products.css') . '?v=' . time() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/barcode-scanner.css') . '?v=' . time() }}">
    @include('business::sales.partials.styles')
@endpush

@php
    $modules = product_setting()->modules ?? [];
@endphp

@section('main_content')
    <form id="purchase-form" action="{{ route('business.purchases.update', $purchase->id) }}" method="post" enctype="multipart/form-data" class="ajaxform pos-fullscreen-form">
        @csrf
        @method('put')

        {{-- Main Content Area --}}
        <div class="pos-main-container">
            {{-- Left Column: Header + Products/Tables --}}
            <div class="pos-left-column">
                {{-- Top Header Navigation --}}
                @include('business::purchases.partials.edit-header')
                
                {{-- Products & Tables Section --}}
                @include('business::purchases.partials.products')
            </div>

            {{-- Right Column: Order Sidebar --}}
            @include('business::purchases.partials.edit-sidebar')
        </div>

        {{-- Hidden Configuration Inputs --}}
        @php $currency = business_currency(); @endphp
        <input type="hidden" name="pos_mode" value="1">
        <input type="hidden" id="currency_symbol" value="{{ $currency->symbol }}">
        <input type="hidden" id="currency_position" value="{{ $currency->position }}">
        <input type="hidden" id="currency_code" value="{{ $currency->code }}">
        <input type="hidden" id="get_product" value="{{ route('business.products.prices') }}">
        <input type="hidden" value="{{ route('business.purchases.cart') }}" id="purchase-cart">
        <input type="hidden" value="{{ route('business.carts.remove-all') }}" id="clear-cart">
        <input type="hidden" id="get-product-variants" value="{{ route('business.products.variants', ['product_id' => 'PRODUCT_ID']) }}">
        <input type="hidden" id="setting_expire_type" value="{{ product_setting()->expire_date_type ?? 'dmy' }}">
    </form>

@endsection

@push('modal')
    @include('business::purchases.calculator')
    @include('business::purchases.category-search')
    @include('business::purchases.brand-search')
    @include('business::purchases.supplier-create')
    @include('business::purchases.product-modal')
@endpush

@push('js')
    <script src="{{ asset('assets/js/choices.min.js') }}"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="{{ asset('assets/js/custom/purchase.js') }}"></script>
    <script src="{{ asset('assets/js/custom/math.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom/calculator.js') }}"></script>
    <script src="{{ asset('assets/js/custom/pos-products.js') . '?v=' . time() }}"></script>
    <script src="{{ asset('assets/js/custom/pos-payment-modal.js') . '?v=' . time() }}"></script>
    <script src="{{ asset('assets/js/custom/pos-sidebar.js') . '?v=' . time() }}"></script>
    <script src="{{ asset('assets/js/custom/barcode-scanner.js') . '?v=' . time() }}"></script>
    <script src="{{ asset('assets/js/custom/pos-purchase-payment-modal.js') . '?v=' . time() }}"></script>
    
    <script>
        // POS Purchase JavaScript functionality
        document.addEventListener('DOMContentLoaded', function() {
            console.log('POS Purchase interface initialized');
            
            // View switching functionality
            const viewBtns = document.querySelectorAll('.pos-view-btn');
            const viewSections = document.querySelectorAll('.view-section');
            
            viewBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const view = this.getAttribute('data-view');
                    console.log('Switching to view:', view);
                    
                    // Remove active state from ALL view buttons (including scan button)
                    viewBtns.forEach(vBtn => {
                        vBtn.classList.remove('active', 'pos-nav-btn-active');
                    });
                    
                    // Also specifically remove from scan button by ID
                    const scanBtn = document.getElementById('scan-barcode-btn');
                    if (scanBtn) {
                        scanBtn.classList.remove('active', 'pos-nav-btn-active');
                    }
                    
                    // Add active state to clicked button
                    this.classList.add('active', 'pos-nav-btn-active');
                    
                    // Hide all views
                    viewSections.forEach(section => {
                        section.style.display = 'none';
                    });
                    
                    // Show selected view
                    const targetView = document.getElementById(view + '-view');
                    if (targetView) {
                        targetView.style.display = 'block';
                    }
                    
                    // Update toggle buttons for brand/category (but don't affect scan button)
                    if (view === 'brand' || view === 'category') {
                        document.querySelectorAll('.pos-toggle-btn').forEach(toggleBtn => {
                            toggleBtn.classList.remove('pos-toggle-btn-active');
                        });
                        this.classList.add('pos-toggle-btn-active');
                    }
                });
            });
            
            // Category/Brand filtering
            const categoryBtns = document.querySelectorAll('.pos-category-item');
            const brandBtns = document.querySelectorAll('.pos-brand-item');
            
            categoryBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    categoryBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    const categoryId = this.getAttribute('data-category');
                    console.log('Category selected:', categoryId);
                    // Add category filtering logic here
                });
            });
            
            brandBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    brandBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    const brandId = this.getAttribute('data-brand');
                    console.log('Brand selected:', brandId);
                    // Add brand filtering logic here
                });
            });
            
            // Supplier selection functionality
            const supplierSelect = document.getElementById('party_id');
            const supplierNameDisplay = document.getElementById('selected-customer-name');
            const supplierPhoneDisplay = document.getElementById('selected-customer-phone');
            
            if (supplierSelect) {
                supplierSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    
                    if (this.value === '' || this.value === 'guest') {
                        if (supplierNameDisplay) supplierNameDisplay.textContent = '{{ __("Select Supplier") }}';
                        if (supplierPhoneDisplay) supplierPhoneDisplay.textContent = '';
                    } else {
                        const supplierName = selectedOption.textContent.split('(')[0].trim();
                        const supplierPhone = selectedOption.getAttribute('data-phone') || '';
                        
                        if (supplierNameDisplay) supplierNameDisplay.textContent = supplierName;
                        if (supplierPhoneDisplay) supplierPhoneDisplay.textContent = supplierPhone;
                    }
                });
            }
            
            // Delivery type tabs
            const deliveryTabs = document.querySelectorAll('.delivery-tab-btn');
            const deliveryTypeInput = document.getElementById('delivery_type');
            
            deliveryTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    deliveryTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    const deliveryType = this.getAttribute('data-delivery-type');
                    if (deliveryTypeInput) {
                        deliveryTypeInput.value = deliveryType;
                    }
                });
            });
            
            // Discount controls
            const addDiscountBtn = document.getElementById('add-discount-btn');
            const discountInputSection = document.getElementById('discount-input-section');
            const applyDiscountBtn = document.getElementById('apply-discount-btn');
            const cancelDiscountBtn = document.getElementById('cancel-discount-btn');
            
            if (addDiscountBtn) {
                addDiscountBtn.addEventListener('click', function() {
                    if (discountInputSection) {
                        discountInputSection.classList.remove('d-none');
                    }
                });
            }
            
            if (cancelDiscountBtn) {
                cancelDiscountBtn.addEventListener('click', function() {
                    if (discountInputSection) {
                        discountInputSection.classList.add('d-none');
                    }
                });
            }
            
            // VAT controls
            const addVatBtn = document.getElementById('add-vat-btn');
            const vatInputSection = document.getElementById('vat-input-section');
            const applyVatBtn = document.getElementById('apply-vat-btn');
            const cancelVatBtn = document.getElementById('cancel-vat-btn');
            
            if (addVatBtn) {
                addVatBtn.addEventListener('click', function() {
                    if (vatInputSection) {
                        vatInputSection.classList.remove('d-none');
                    }
                });
            }
            
            if (cancelVatBtn) {
                cancelVatBtn.addEventListener('click', function() {
                    if (vatInputSection) {
                        vatInputSection.classList.add('d-none');
                    }
                });
            }
            
            // Shipping controls
            const addShippingBtn = document.getElementById('add-shipping-btn');
            const shippingInputSection = document.getElementById('shipping-input-section');
            const applyShippingBtn = document.getElementById('apply-shipping-btn');
            const cancelShippingBtn = document.getElementById('cancel-shipping-btn');
            
            if (addShippingBtn) {
                addShippingBtn.addEventListener('click', function() {
                    if (shippingInputSection) {
                        shippingInputSection.classList.remove('d-none');
                    }
                });
            }
            
            if (cancelShippingBtn) {
                cancelShippingBtn.addEventListener('click', function() {
                    if (shippingInputSection) {
                        shippingInputSection.classList.add('d-none');
                    }
                });
            }
            
            // Payment modal controls
            const openPaymentModalBtn = document.getElementById('open-payment-modal');
            const paymentModalOverlay = document.getElementById('payment-modal-overlay');
            const cancelPaymentBtn = document.getElementById('cancel-payment-btn');
            
            if (openPaymentModalBtn) {
                openPaymentModalBtn.addEventListener('click', function() {
                    if (paymentModalOverlay) {
                        paymentModalOverlay.style.display = 'flex';
                    }
                });
            }
            
            if (cancelPaymentBtn) {
                cancelPaymentBtn.addEventListener('click', function() {
                    if (paymentModalOverlay) {
                        paymentModalOverlay.style.display = 'none';
                    }
                });
            }
            
            // Payment method selection
            const paymentMethodBtns = document.querySelectorAll('.payment-method-btn');
            paymentMethodBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    paymentMethodBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                });
            });
            
            // Numpad functionality
            const numpadBtns = document.querySelectorAll('.numpad-btn');
            const receiveAmountInput = document.getElementById('modal-receive-amount');
            
            numpadBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const value = this.getAttribute('data-value');
                    
                    if (value === 'clear') {
                        if (receiveAmountInput) receiveAmountInput.value = '';
                    } else if (receiveAmountInput) {
                        receiveAmountInput.value += value;
                    }
                });
            });
            
            // Function to show product modal (matching the purchase.js functionality)
            function showProductModal(element) {
                const selectedProduct = {
                    product_id: element.data("product_id"),
                    product_code: element.data("product_code"),
                    product_unit_id: element.data("product_unit_id"),
                    product_unit_name: element.data("product_unit_name"),
                    product_image: element.data("product_image"),
                    product_name: element.find(".product_name").text(),
                    brand: element.data("brand"),
                    stock: element.data("stock"),
                    purchase_price: element.data("purchase_price"),
                    sales_price: element.data("sales_price"),
                    whole_sale_price: element.data("whole_sale_price"),
                    dealer_price: element.data("dealer_price"),
                    product_type: element.data("product_type"),
                };

                // Set modal display values
                $("#product_name").text(selectedProduct.product_name);
                $("#brand").text(selectedProduct.brand);
                $("#stock").text(selectedProduct.stock);
                $("#purchase_price").val(selectedProduct.purchase_price);
                $("#sales_price").val(selectedProduct.sales_price);
                $("#whole_sale_price").val(selectedProduct.whole_sale_price);
                $("#dealer_price").val(selectedProduct.dealer_price);
                $("#batch_no").val("");

                // Show modal
                $("#product-modal").modal("show");
                
                // Store selected product globally for form submission
                window.selectedProduct = selectedProduct;
            }
            
            // Product click functionality - handle both direct product clicks and add to cart button clicks
            document.addEventListener('click', function(e) {
                // Handle product card clicks (but not if clicking on buttons inside)
                if (e.target.closest('.single-product') && !e.target.closest('.pos-add-to-cart-btn') && !e.target.closest('.pos-option-btn')) {
                    const productCard = e.target.closest('.single-product');
                    showProductModal($(productCard));
                }
                
                // Handle add to cart button clicks
                if (e.target.closest('.pos-add-to-cart-btn')) {
                    e.preventDefault();
                    const productCard = e.target.closest('.single-product');
                    showProductModal($(productCard));
                }
                
                // Handle remove item button
                if (e.target.closest('.remove-item-btn')) {
                    const cartItem = e.target.closest('.cart-item-card');
                    const destroyRoute = cartItem.getAttribute('data-destroy_route');
                    
                    if (destroyRoute) {
                        // Make AJAX request to remove item without confirmation
                        $.ajax({
                            url: destroyRoute,
                            type: 'DELETE',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    // Remove item from DOM with fade effect
                                    $(cartItem).fadeOut(400, function() {
                                        $(this).remove();
                                        // Refresh cart and update totals
                                        fetchUpdatedCart(calTotalAmount);
                                    });
                                    
                                    // Show success message
                                    if (typeof toastr !== 'undefined') {
                                        toastr.success('{{ __("Item removed from cart") }}');
                                    }
                                } else {
                                    if (typeof toastr !== 'undefined') {
                                        toastr.error(response.message || 'Failed to remove item');
                                    }
                                }
                            },
                            error: function() {
                                console.error('Error removing item');
                                if (typeof toastr !== 'undefined') {
                                    toastr.error('{{ __("Error removing item") }}');
                                }
                            }
                        });
                    }
                }
                
                // Handle quantity increase
                if (e.target.closest('.plus-btn')) {
                    const cartItem = e.target.closest('.cart-item-card');
                    const qtyInput = cartItem.querySelector('.cart-qty');
                    const currentQty = parseInt(qtyInput.value) || 0;
                    const newQty = currentQty + 1;
                    
                    updateCartItemQuantity(cartItem, newQty);
                }
                
                // Handle quantity decrease
                if (e.target.closest('.minus-btn')) {
                    const cartItem = e.target.closest('.cart-item-card');
                    const qtyInput = cartItem.querySelector('.cart-qty');
                    const currentQty = parseInt(qtyInput.value) || 0;
                    
                    if (currentQty > 1) {
                        const newQty = currentQty - 1;
                        updateCartItemQuantity(cartItem, newQty);
                    }
                }
            });
            
            // Function to update cart item quantity
            function updateCartItemQuantity(cartItem, newQty) {
                const updateRoute = cartItem.getAttribute('data-update_route');
                const qtyInput = cartItem.querySelector('.cart-qty');
                const rowId = cartItem.getAttribute('data-row_id');
                
                if (updateRoute) {
                    // Make AJAX request to update quantity
                    $.ajax({
                        url: updateRoute,
                        type: 'PUT',
                        data: {
                            rowId: rowId,
                            qty: newQty,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                // Update quantity in DOM
                                qtyInput.value = newQty;
                                
                                // Refresh cart and update totals
                                fetchUpdatedCart(calTotalAmount);
                            } else {
                                if (typeof toastr !== 'undefined') {
                                    toastr.error(response.message || 'Failed to update quantity');
                                }
                            }
                        },
                        error: function() {
                            console.error('Error updating quantity');
                            if (typeof toastr !== 'undefined') {
                                toastr.error('Error updating cart quantity');
                            }
                        }
                    });
                }
            }
            
            // Clear cart functionality
            document.addEventListener('click', function(e) {
                if (e.target.closest('.cancel-sale-btn')) {
                    e.preventDefault();
                    clearCart('purchase');
                }
            });
            
            // Clear cart function (from purchase.js)
            function clearCart(cartType) {
                let route = $("#clear-cart").val();
                $.ajax({
                    type: "POST",
                    url: route,
                    data: {
                        type: cartType,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: "json",
                    success: function (response) {
                        fetchUpdatedCart(calTotalAmount); // Call calTotalAmount after cart fetch completes
                    },
                    error: function () {
                        console.error("There was an issue clearing the cart.");
                    },
                });
            }
            
            // Fetch updated cart function (from purchase.js)
            function fetchUpdatedCart(callback) {
                let url = $("#purchase-cart").val() + '?v=' + Date.now();
                $.ajax({
                    url: url,
                    type: "GET",
                    success: function (response) {
                        $("#purchase_cart_list").html(response);
                        if (typeof callback === "function") callback(); // Call the callback after updating the cart
                    },
                });
            }
            
            // Calculate total amount function (simplified version)
            function calTotalAmount() {
                let subtotal = 0;
                let itemCount = 0;

                // Calculate subtotal from cart list
                $("#purchase_cart_list .cart-item-card").each(function () {
                    let cart_subtotal = parseFloat($(this).find(".cart-subtotal").val()) || 0;
                    subtotal += cart_subtotal;
                    itemCount++;
                });

                // Update items count
                if ($("#items_count").length) {
                    $("#items_count").text(itemCount);
                }

                // Update subtotal display
                $("#sub_total").text(formatCurrency(subtotal));

                // Calculate total (for now just use subtotal, can add VAT/discount later)
                $("#total_amount").text(formatCurrency(subtotal));
            }
            
            // Function to format currency
            function formatCurrency(amount) {
                const currencySymbol = document.getElementById('currency_symbol')?.value || '$';
                const currencyPosition = document.getElementById('currency_position')?.value || 'before';
                
                const formattedAmount = amount.toFixed(2);
                
                if (currencyPosition === 'after') {
                    return formattedAmount + ' ' + currencySymbol;
                } else {
                    return currencySymbol + formattedAmount;
                }
            }
            
            console.log('✅ POS Purchase Edit functionality initialized');
            
            // Force refresh cart on page load to ensure new design
            setTimeout(function() {
                fetchUpdatedCart(calTotalAmount);
            }, 100);
        });
    </script>
@endpush