// POS Purchase Payment Modal JavaScript

(function() {
    'use strict';

    // Get elements
    const openModalBtn = document.getElementById('open-purchase-payment-modal');
    const cancelPaymentBtn = document.getElementById('cancel-purchase-payment-btn');
    const modalOverlay = document.getElementById('purchase-payment-modal-overlay');
    const completePaymentBtn = document.getElementById('complete-purchase-payment-btn');
    
    // Payment method buttons (only within purchase modal)
    const paymentMethodBtns = document.querySelectorAll('#purchase-payment-modal-overlay .payment-method-btn');
    
    // Payment tab buttons (only within purchase modal)
    const paymentTabBtns = document.querySelectorAll('#purchase-payment-modal-overlay .payment-tab-btn');
    
    // Numpad buttons (only within purchase modal)
    const numpadBtns = document.querySelectorAll('#purchase-payment-modal-overlay .numpad-btn');
    const receiveAmountInput = document.getElementById('modal-purchase-receive-amount');
    
    // Supplier select
    const supplierSelect = document.getElementById('supplier_id');
    const supplierNameDisplay = document.getElementById('selected-supplier-name');

    // Open modal
    if (openModalBtn) {
        openModalBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get total amount from sidebar
            const totalAmount = document.getElementById('total_amount').textContent;
            const totalAmountValue = parseFloat(document.getElementById('total_amount').textContent.replace(/[^0-9.-]+/g,"")) || 0;
            
            // Update modal with order info
            document.getElementById('modal-purchase-total').textContent = totalAmount;
            document.getElementById('modal-purchase-total-bill').textContent = totalAmount;
            document.getElementById('modal-purchase-due-amount').value = totalAmountValue;
            document.getElementById('modal-purchase-due-summary').textContent = totalAmount;
            
            // Reset receive amount
            receiveAmountInput.value = totalAmountValue;
            updatePaymentCalculations();
            
            // Show modal
            modalOverlay.classList.add('active');
        });
    }

    // Close modal
    function closeModal() {
        modalOverlay.classList.remove('active');
    }

    if (cancelPaymentBtn) {
        cancelPaymentBtn.addEventListener('click', closeModal);
    }

    // Close modal when clicking outside
    if (modalOverlay) {
        modalOverlay.addEventListener('click', function(e) {
            if (e.target === modalOverlay) {
                closeModal();
            }
        });
    }

    // Payment method selection
    paymentMethodBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            paymentMethodBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Update hidden payment type field
            const method = this.getAttribute('data-method');
            const paymentTypeSelect = document.getElementById('payment_type_id');
            if (paymentTypeSelect) {
                // Map method to payment type
                const methodMap = {
                    'cash': '1',
                    'card': '2',
                    'upi': '3',
                    'due': '4'
                };
                if (methodMap[method]) {
                    paymentTypeSelect.value = methodMap[method];
                }
            }
        });
    });

    // Payment tab selection
    paymentTabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            paymentTabBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Numpad functionality
    numpadBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const value = this.getAttribute('data-value');
            
            if (value === 'clear') {
                receiveAmountInput.value = '0';
            } else if (value === '.') {
                if (!receiveAmountInput.value.includes('.')) {
                    receiveAmountInput.value += value;
                }
            } else {
                if (receiveAmountInput.value === '0') {
                    receiveAmountInput.value = value;
                } else {
                    receiveAmountInput.value += value;
                }
            }
            
            updatePaymentCalculations();
        });
    });

    // Update payment calculations
    function updatePaymentCalculations() {
        const totalAmount = document.getElementById('total_amount').textContent;
        const totalBill = parseFloat(totalAmount.replace(/[^0-9.-]+/g,"")) || 0;
        const receiveAmount = parseFloat(receiveAmountInput.value) || 0;
        const dueAmount = totalBill - receiveAmount;
        
        // Update modal displays
        document.getElementById('modal-purchase-amount-paid').textContent = formatCurrency(receiveAmount);
        document.getElementById('modal-purchase-due-summary').textContent = formatCurrency(Math.max(0, dueAmount));
        
        // Update hidden form fields
        document.getElementById('receive_amount').value = receiveAmount;
        document.getElementById('due_amount').value = Math.max(0, dueAmount);
        document.getElementById('change_amount').value = Math.max(0, -dueAmount);
    }

    // Format currency
    function formatCurrency(amount) {
        const symbol = document.getElementById('currency_symbol')?.value || '$';
        const position = document.getElementById('currency_position')?.value || 'left';
        const code = document.getElementById('currency_code')?.value || '';
        
        // SAR Symbol SVG
        const sarSymbolSVG = '<svg width="11" height="12" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: inline-block; vertical-align: middle; margin-left: 3px;"><g clip-path="url(#clip0_price_5-1)"><path d="M6.68122 10.6309C6.48962 11.0558 6.36297 11.5168 6.31445 12.0003L10.369 11.1384C10.5606 10.7137 10.6872 10.2525 10.7358 9.76904L6.68122 10.6309Z" fill="currentColor"/><path d="M10.3691 8.55619C10.5607 8.13144 10.6873 7.67031 10.7359 7.18683L7.57749 7.85857V6.56725L10.369 5.97403C10.5606 5.54929 10.6873 5.08815 10.7358 4.60467L7.57739 5.27584V0.631863C7.09343 0.903594 6.66363 1.2653 6.31425 1.69195V5.54441L5.05111 5.8129V0.000244141C4.56715 0.27188 4.13735 0.633678 3.78797 1.06033V6.08129L0.961685 6.68186C0.770089 7.1066 0.643345 7.56773 0.594729 8.05122L3.78797 7.3726V8.99879L0.365788 9.72601C0.174192 10.1508 0.0475433 10.6119 -0.000976562 11.0954L3.58109 10.3341C3.87269 10.2735 4.12331 10.1011 4.28625 9.86384L4.94318 8.8899V8.88971C5.01138 8.78895 5.05111 8.66746 5.05111 8.53661V7.10412L6.31425 6.83564V9.41827L10.369 8.55599L10.3691 8.55619Z" fill="currentColor"/></g><defs><clipPath id="clip0_price_5-1"><rect width="10.7368" height="12" fill="white"/></clipPath></defs></svg>';
        
        // Check if currency is SAR
        const isSAR = code === 'SAR' || symbol === '^';
        
        const formatted = amount.toFixed(2);
        
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
    }

    // Update supplier info when supplier is selected
    if (supplierSelect) {
        supplierSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            
            if (this.value === '') {
                supplierNameDisplay.textContent = 'Select Supplier';
            } else {
                const supplierName = selectedOption.textContent.split('(')[0].trim();
                supplierNameDisplay.textContent = supplierName;
            }
        });
    }

    // Complete payment - close modal and submit form
    if (completePaymentBtn) {
        completePaymentBtn.addEventListener('click', function() {
            // Modal will close, form will submit
            closeModal();
        });
    }

})();
