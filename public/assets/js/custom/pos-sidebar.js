// POS Sidebar JavaScript - Handles all sidebar interactions

(function() {
    'use strict';

    // Update customer info when customer is selected
    const customerSelect = document.getElementById('party_id');
    const customerNameDisplay = document.getElementById('selected-customer-name');
    const customerPhoneDisplay = document.getElementById('selected-customer-phone');

    if (customerSelect) {
        customerSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            
            if (this.value === 'guest' || this.value === '') {
                const guestText = window.posTranslations ? window.posTranslations['Guest'] : 'Guest';
                if (customerNameDisplay) customerNameDisplay.textContent = guestText;
                if (customerPhoneDisplay) customerPhoneDisplay.textContent = '-';
            } else {
                const customerName = selectedOption.textContent.split('(')[0].trim();
                const customerPhone = selectedOption.getAttribute('data-phone') || '-';
                
                if (customerNameDisplay) customerNameDisplay.textContent = customerName;
                if (customerPhoneDisplay) customerPhoneDisplay.textContent = customerPhone;
            }
        });

        // Initialize customer display on page load
        if (customerSelect.value) {
            customerSelect.dispatchEvent(new Event('change'));
        }
    }

    // Delivery tab functionality
    const deliveryTabBtns = document.querySelectorAll('.sidebar-delivery-tabs .delivery-tab-btn, .delivery-tab-btn');
    deliveryTabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            deliveryTabBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Handle quantity display updates in new cart layout
    function updateCartItemDisplay(cartItem, newQty) {
        const qtyDisplay = cartItem.querySelector('.cart-qty-display');
        const hiddenQtyInput = cartItem.querySelector('.cart-qty');
        
        if (qtyDisplay) qtyDisplay.textContent = newQty;
        if (hiddenQtyInput) hiddenQtyInput.value = newQty;
    }

    // Intercept plus/minus button clicks to show proper error messages
    document.addEventListener('click', function(e) {
        const plusBtn = e.target.closest('.plus-btn');
        const minusBtn = e.target.closest('.minus-btn');
        
        if (plusBtn || minusBtn) {
            const cartItem = e.target.closest('.cart-item-card, tr');
            if (!cartItem) return;
            
            const qtyDisplay = cartItem.querySelector('.cart-qty-display');
            const hiddenQtyInput = cartItem.querySelector('.cart-qty, .cart-item-qty');
            
            if (!qtyDisplay || !hiddenQtyInput) return;
            
            const currentQty = parseInt(hiddenQtyInput.value) || 0;
            
            // Store original quantity in case we need to revert
            cartItem.dataset.originalQty = currentQty;
        }
    });

    // Listen for AJAX errors from sale.js
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        return originalFetch.apply(this, args)
            .then(response => {
                // Clone response to read it
                const clonedResponse = response.clone();
                
                // Try to parse JSON
                clonedResponse.json().then(data => {
                    // Check for stock error
                    if (data.success === false && data.message) {
                        if (data.message.includes('stock not available') || 
                            data.message.includes('Available:')) {
                            
                            // Show error with toastr if available
                            if (typeof toastr !== 'undefined') {
                                toastr.error(data.message);
                            } else {
                                alert(data.message);
                            }
                        }
                    }
                }).catch(() => {
                    // Not JSON, ignore
                });
                
                return response;
            });
    };

    // Clear All / Cancel Order functionality
    function handleClearCart(button) {
        const route = button.getAttribute('data-route');
        
        if (!route) {
            console.error('No route found for clear cart button');
            return;
        }

        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                       || document.querySelector('input[name="_token"]')?.value;

        // Make AJAX request to clear cart
        fetch(route, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                type: 'sale'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success || data.status === 'success') {
                // Clear the cart list
                const cartList = document.getElementById('cart-list');
                if (cartList) {
                    cartList.innerHTML = '<div class="empty-cart"><div class="empty-cart-icon"><i class="fas fa-shopping-cart"></i></div><p>' + (typeof __ === 'function' ? __('No items in cart') : 'No items in cart') + '</p></div>';
                }

                // Reset all totals
                const itemsCount = document.getElementById('items_count');
                const subTotal = document.getElementById('sub_total');
                const discountDisplay = document.getElementById('discount_display');
                const vatDisplay = document.getElementById('vat_display');
                const shippingDisplay = document.getElementById('shipping_display');
                const totalAmount = document.getElementById('total_amount');

                if (itemsCount) itemsCount.textContent = '0';
                if (subTotal) subTotal.innerHTML = currencyFormat(0);
                if (discountDisplay) discountDisplay.textContent = '0';
                if (vatDisplay) vatDisplay.textContent = '0';
                if (shippingDisplay) shippingDisplay.textContent = '0';
                if (totalAmount) totalAmount.innerHTML = currencyFormat(0);

                // Reset form inputs
                const receiveAmount = document.getElementById('receive_amount');
                const changeAmount = document.getElementById('change_amount');
                const dueAmount = document.getElementById('due_amount');
                const discountAmount = document.getElementById('discount_amount');
                const shippingCharge = document.getElementById('shipping_charge');
                const vatAmount = document.getElementById('vat_amount');

                if (receiveAmount) receiveAmount.value = '0';
                if (changeAmount) changeAmount.value = '0';
                if (dueAmount) dueAmount.value = '0';
                if (discountAmount) discountAmount.value = '0';
                if (shippingCharge) shippingCharge.value = '0';
                if (vatAmount) vatAmount.value = '0';

                // Show success message
                if (typeof toastr !== 'undefined') {
                    const successText = window.posTranslations ? window.posTranslations['Cart cleared successfully'] : 'Cart cleared successfully';
                    toastr.success(successText);
                }
            } else {
                throw new Error(data.message || 'Failed to clear cart');
            }
        })
        .catch(error => {
            console.error('Error clearing cart:', error);
            if (typeof toastr !== 'undefined') {
                toastr.error('Failed to clear cart: ' + error.message);
            } else {
                alert('Failed to clear cart: ' + error.message);
            }
        });
    }

    // Attach event listeners to all clear/cancel buttons
    document.addEventListener('click', function(e) {
        // Check if clicked element or its parent has the cancel-sale-btn class
        const cancelBtn = e.target.closest('.cancel-sale-btn');
        if (cancelBtn) {
            e.preventDefault();
            handleClearCart(cancelBtn);
        }
    });

    // Open payment modal button
    const openModalBtn = document.getElementById('open-payment-modal');
    const modalOverlay = document.getElementById('payment-modal-overlay');
    
    if (openModalBtn && modalOverlay) {
        openModalBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get total amount from sidebar
            const totalAmount = document.getElementById('total_amount').textContent;
            const payableAmountInput = document.getElementById('payable_amount');
            const totalAmountValue = payableAmountInput ? payableAmountInput.value : '0';
            
            // Update modal with order info
            const modalOrderTotal = document.getElementById('modal-order-total');
            const modalTotalBill = document.getElementById('modal-total-bill');
            const modalDueAmount = document.getElementById('modal-due-amount');
            const modalDueSummary = document.getElementById('modal-due-summary');
            const modalReceiveAmount = document.getElementById('modal-receive-amount');
            
            if (modalOrderTotal) modalOrderTotal.innerHTML = totalAmount;
            if (modalTotalBill) modalTotalBill.innerHTML = totalAmount;
            if (modalDueAmount) modalDueAmount.value = totalAmountValue;
            if (modalDueSummary) modalDueSummary.innerHTML = totalAmount;
            
            // Reset receive amount
            if (modalReceiveAmount) {
                modalReceiveAmount.value = totalAmountValue;
            }
            
            // Update calculations
            if (typeof updatePaymentCalculations === 'function') {
                updatePaymentCalculations();
            }
            
            // Show modal
            modalOverlay.classList.add('active');
        });
    }

    // Update real-time totals in sidebar
    function updateSidebarTotals() {
        // This function is called by sale.js after cart updates
        // Just ensure the display elements are updated
        const itemsCount = document.getElementById('items_count');
        const subTotal = document.getElementById('sub_total');
        const discountDisplay = document.getElementById('discount_display');
        const vatDisplay = document.getElementById('vat_display');
        const shippingDisplay = document.getElementById('shipping_display');
        const totalAmount = document.getElementById('total_amount');
        
        // These values are updated by calTotalAmount() in sale.js
        // This function just ensures the elements exist and are visible
        if (itemsCount && itemsCount.textContent === '3') {
            itemsCount.textContent = '0';
        }
        if (subTotal) {
            subTotal.innerHTML = currencyFormat(0);
        }
        if (totalAmount) {
            totalAmount.innerHTML = currencyFormat(0);
        }
    }

    // Initialize sidebar on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateSidebarTotals();
        
        // Trigger initial customer selection if there's a default
        if (customerSelect && customerSelect.value) {
            customerSelect.dispatchEvent(new Event('change'));
        }
        
        // Set up translations for JavaScript
        window.posTranslations = {
            'No items in cart': 'No items in cart',
            'Cart cleared successfully': 'Cart cleared successfully',
            'Failed to clear cart': 'Failed to clear cart',
            'Guest': 'Guest'
        };
    });

    // Helper function for currency formatting (if not already defined)
    if (typeof currencyFormat !== 'function') {
        window.currencyFormat = function(amount) {
            const symbol = document.getElementById('currency_symbol')?.value || '$';
            const position = document.getElementById('currency_position')?.value || 'left';
            const code = document.getElementById('currency_code')?.value || '';
            
            // SAR Symbol SVG
            const sarSymbolSVG = '<svg width="11" height="12" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: inline-block; vertical-align: middle; margin-left: 3px;"><g clip-path="url(#clip0_price_5-1)"><path d="M6.68122 10.6309C6.48962 11.0558 6.36297 11.5168 6.31445 12.0003L10.369 11.1384C10.5606 10.7137 10.6872 10.2525 10.7358 9.76904L6.68122 10.6309Z" fill="currentColor"/><path d="M10.3691 8.55619C10.5607 8.13144 10.6873 7.67031 10.7359 7.18683L7.57749 7.85857V6.56725L10.369 5.97403C10.5606 5.54929 10.6873 5.08815 10.7358 4.60467L7.57739 5.27584V0.631863C7.09343 0.903594 6.66363 1.2653 6.31425 1.69195V5.54441L5.05111 5.8129V0.000244141C4.56715 0.27188 4.13735 0.633678 3.78797 1.06033V6.08129L0.961685 6.68186C0.770089 7.1066 0.643345 7.56773 0.594729 8.05122L3.78797 7.3726V8.99879L0.365788 9.72601C0.174192 10.1508 0.0475433 10.6119 -0.000976562 11.0954L3.58109 10.3341C3.87269 10.2735 4.12331 10.1011 4.28625 9.86384L4.94318 8.8899V8.88971C5.01138 8.78895 5.05111 8.66746 5.05111 8.53661V7.10412L6.31425 6.83564V9.41827L10.369 8.55599L10.3691 8.55619Z" fill="currentColor"/></g><defs><clipPath id="clip0_price_5-1"><rect width="10.7368" height="12" fill="white"/></clipPath></defs></svg>';
            
            // Check if currency is SAR
            const isSAR = code === 'SAR' || symbol === '^';
            
            const formatted = parseFloat(amount).toFixed(2);
            
            if (isSAR) {
                if (position === 'left') {
                    return sarSymbolSVG + formatted;
                } else {
                    return formatted + sarSymbolSVG;
                }
            } else {
                if (position === 'left') {
                    return symbol + formatted;
                } else {
                    return formatted + symbol;
                }
            }
        };
    }

})();
