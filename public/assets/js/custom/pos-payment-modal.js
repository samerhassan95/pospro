// POS Payment Modal JavaScript

(function() {
    'use strict';

    // Get elements
    const openModalBtn = document.getElementById('open-payment-modal');
    const cancelPaymentBtn = document.getElementById('cancel-payment-btn');
    const modalOverlay = document.getElementById('payment-modal-overlay');
    const completePaymentBtn = document.getElementById('complete-payment-btn');
    
    // Payment method buttons
    const paymentMethodBtns = document.querySelectorAll('.payment-method-btn');
    
    // Payment tab buttons
    const paymentTabBtns = document.querySelectorAll('.payment-tab-btn');
    
    // Numpad buttons
    const numpadBtns = document.querySelectorAll('.numpad-btn');
    const receiveAmountInput = document.getElementById('modal-receive-amount');
    
    // Customer select
    const customerSelect = document.getElementById('party_id');
    const customerNameDisplay = document.getElementById('selected-customer-name');
    const customerPhoneDisplay = document.getElementById('selected-customer-phone');

    // Open modal
    if (openModalBtn) {
        openModalBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get total amount from sidebar
            const totalAmount = document.getElementById('total_amount').textContent;
            const totalAmountValue = document.getElementById('payable_amount').value || '0';
            
            // Update modal with order info
            document.getElementById('modal-order-total').textContent = totalAmount;
            document.getElementById('modal-total-bill').textContent = totalAmount;
            document.getElementById('modal-due-amount').value = totalAmountValue;
            document.getElementById('modal-due-summary').textContent = totalAmount;
            
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
                // Map method to payment type (you may need to adjust this based on your payment types)
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
        const totalBill = parseFloat(document.getElementById('payable_amount').value) || 0;
        const receiveAmount = parseFloat(receiveAmountInput.value) || 0;
        const dueAmount = totalBill - receiveAmount;
        
        // Update modal displays
        document.getElementById('modal-amount-paid').textContent = formatCurrency(receiveAmount);
        document.getElementById('modal-due-summary').textContent = formatCurrency(Math.max(0, dueAmount));
        
        // Update hidden form fields
        document.getElementById('receive_amount').value = receiveAmount;
        document.getElementById('due_amount').value = Math.max(0, dueAmount);
        document.getElementById('change_amount').value = Math.max(0, -dueAmount);
    }

    // Format currency (basic implementation)
    function formatCurrency(amount) {
        const symbol = document.getElementById('currency_symbol')?.value || '$';
        const position = document.getElementById('currency_position')?.value || 'left';
        const formatted = amount.toFixed(2);
        
        if (position === 'left') {
            return symbol + formatted;
        } else {
            return formatted + symbol;
        }
    }

    // Update customer info when customer is selected
    if (customerSelect) {
        customerSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            
            if (this.value === 'guest' || this.value === '') {
                customerNameDisplay.textContent = 'Guest';
                customerPhoneDisplay.textContent = '-';
            } else {
                const customerName = selectedOption.textContent.split('(')[0].trim();
                const customerPhone = selectedOption.getAttribute('data-phone') || '-';
                
                customerNameDisplay.textContent = customerName;
                customerPhoneDisplay.textContent = customerPhone;
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

    // Delivery tab functionality (optional - add your logic)
    const deliveryTabBtns = document.querySelectorAll('.delivery-tab-btn');
    deliveryTabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            deliveryTabBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });

})();
