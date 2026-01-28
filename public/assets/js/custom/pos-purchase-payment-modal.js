// POS Purchase Payment Modal JavaScript

(function() {
    'use strict';

    // Get elements
    const openModalBtn = document.getElementById('open-purchase-payment-modal');
    const cancelPaymentBtn = document.getElementById('cancel-purchase-payment-btn');
    const modalOverlay = document.getElementById('purchase-payment-modal-overlay');
    const completePaymentBtn = document.getElementById('complete-purchase-payment-btn');
    
    // Payment method buttons
    const paymentMethodBtns = document.querySelectorAll('.payment-method-btn');
    
    // Payment tab buttons
    const paymentTabBtns = document.querySelectorAll('.payment-tab-btn');
    
    // Numpad buttons
    const numpadBtns = document.querySelectorAll('.numpad-btn');
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
        const formatted = amount.toFixed(2);
        
        if (position === 'left') {
            return symbol + formatted;
        } else {
            return formatted + symbol;
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
