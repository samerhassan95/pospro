// POS Payment Modal JavaScript

(function () {
    "use strict";

    // Get elements
    const openModalBtn = document.getElementById("open-payment-modal");
    const cancelPaymentBtn = document.getElementById("cancel-payment-btn");
    const modalOverlay = document.getElementById("payment-modal-overlay");
    const completePaymentBtn = document.getElementById("complete-payment-btn");

    // Payment method buttons
    const paymentMethodBtns = document.querySelectorAll(
        ".payment-modal .payment-method-btn",
    );

    // Payment tab buttons
    const paymentTabBtns = document.querySelectorAll(
        ".payment-modal .payment-tab-btn",
    );

    // Numpad buttons
    const numpadBtns = document.querySelectorAll(".payment-modal .numpad-btn");
    const receiveAmountInput = document.getElementById("modal-receive-amount");

    // Customer select
    const customerSelect = document.getElementById("party_id");
    const customerNameDisplay = document.getElementById(
        "selected-customer-name",
    );
    const customerPhoneDisplay = document.getElementById(
        "selected-customer-phone",
    );

    // Open modal
    if (openModalBtn) {
        openModalBtn.addEventListener("click", function (e) {
            e.preventDefault();

            console.log("🔵 Pay Bill button clicked");

            try {
                // Get total amount from sidebar
                const totalAmountElement =
                    document.getElementById("total_amount");
                const payableAmountInput =
                    document.getElementById("payable_amount");

                console.log("🔍 Elements check:", {
                    totalAmountElement: totalAmountElement,
                    payableAmountInput: payableAmountInput,
                    modalOverlay: modalOverlay,
                });

                if (!totalAmountElement) {
                    console.error("❌ total_amount element not found");
                    alert(
                        "Error: Total amount element not found. Please refresh the page.",
                    );
                    return;
                }

                if (!payableAmountInput) {
                    console.error("❌ payable_amount input not found");
                    alert(
                        "Error: Payable amount input not found. Please refresh the page.",
                    );
                    return;
                }

                const totalAmount = totalAmountElement.textContent || "0";
                const totalAmountValue = payableAmountInput.value || "0";

                console.log("💰 Amounts:", {
                    totalAmount: totalAmount,
                    totalAmountValue: totalAmountValue,
                });

                // Update modal with order info
                const modalOrderTotal =
                    document.getElementById("modal-order-total");
                const modalTotalBill =
                    document.getElementById("modal-total-bill");
                const modalDueAmount =
                    document.getElementById("modal-due-amount");
                const modalDueSummary =
                    document.getElementById("modal-due-summary");

                console.log("🔍 Modal elements:", {
                    modalOrderTotal: modalOrderTotal,
                    modalTotalBill: modalTotalBill,
                    modalDueAmount: modalDueAmount,
                    modalDueSummary: modalDueSummary,
                });

                if (modalOrderTotal) modalOrderTotal.textContent = totalAmount;
                if (modalTotalBill) modalTotalBill.textContent = totalAmount;
                if (modalDueAmount) modalDueAmount.value = totalAmountValue;
                if (modalDueSummary) modalDueSummary.textContent = totalAmount;

                // Reset receive amount
                if (receiveAmountInput) {
                    receiveAmountInput.value = totalAmountValue;
                    updatePaymentCalculations();
                } else {
                    console.warn("⚠️ receiveAmountInput not found");
                }

                // Set default payment method to cash (first option)
                const firstPaymentMethod = document.querySelector(
                    '.payment-method-btn[data-method="cash"]',
                );
                if (firstPaymentMethod) {
                    paymentMethodBtns.forEach((b) =>
                        b.classList.remove("active"),
                    );
                    firstPaymentMethod.classList.add("active");

                    // Payment type is already set in Blade template as hidden input
                    console.log("✅ Payment method set to Cash (default)");
                } else {
                    console.warn("⚠️ Cash payment method button not found");
                }

                // Show modal
                if (modalOverlay) {
                    console.log("✅ Opening modal...");
                    modalOverlay.classList.add("active");
                    
                    // Set default payment type if empty
                    const paymentTypeInput = document.getElementById("payment_type_id");
                    if (paymentTypeInput && !paymentTypeInput.value) {
                        console.log("⚠️ Payment type is empty, but it should have a default value from Blade");
                    } else if (paymentTypeInput) {
                        console.log("✅ Payment type already set:", paymentTypeInput.value);
                    }
                    
                    // Replace SAR symbols after modal is shown
                    setTimeout(function() {
                        if (typeof window.replaceSARSymbol === 'function') {
                            window.replaceSARSymbol();
                            console.log("✅ SAR symbols replaced in modal");
                        }
                    }, 100);
                } else {
                    console.error("❌ Modal overlay not found");
                    alert(
                        "Error: Payment modal not found. Please refresh the page.",
                    );
                }
            } catch (error) {
                console.error("❌ Error opening payment modal:", error);
                alert("Something went wrong: " + error.message);
            }
        });
    } else {
        console.error("❌ Pay Bill button not found");
    }

    // Close modal
    function closeModal() {
        modalOverlay.classList.remove("active");
    }

    if (cancelPaymentBtn) {
        cancelPaymentBtn.addEventListener("click", closeModal);
    }

    // Close modal when clicking outside
    if (modalOverlay) {
        modalOverlay.addEventListener("click", function (e) {
            if (e.target === modalOverlay) {
                closeModal();
            }
        });
    }

    // Payment method selection
    paymentMethodBtns.forEach((btn) => {
        btn.addEventListener("click", function () {
            paymentMethodBtns.forEach((b) => b.classList.remove("active"));
            this.classList.add("active");

            // Update hidden payment type field
            const method = this.getAttribute("data-method");
            const paymentTypeInput = document.getElementById("payment_type_id");

            if (paymentTypeInput) {
                // Payment type is already set, just log it
                console.log(
                    "Payment method selected:",
                    method,
                    "Payment type ID:",
                    paymentTypeInput.value,
                );
            }
        });
    });

    // Payment tab selection
    paymentTabBtns.forEach((btn) => {
        btn.addEventListener("click", function () {
            paymentTabBtns.forEach((b) => b.classList.remove("active"));
            this.classList.add("active");
        });
    });

    // Numpad functionality
    numpadBtns.forEach((btn) => {
        btn.addEventListener("click", function () {
            const value = this.getAttribute("data-value");

            if (value === "clear") {
                receiveAmountInput.value = "0";
            } else if (value === ".") {
                if (!receiveAmountInput.value.includes(".")) {
                    receiveAmountInput.value += value;
                }
            } else {
                if (receiveAmountInput.value === "0") {
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
        const payableAmountInput = document.getElementById("payable_amount");
        const totalBill =
            parseFloat(payableAmountInput ? payableAmountInput.value : "0") ||
            0;
        const receiveAmount = parseFloat(receiveAmountInput.value) || 0;
        const dueAmount = totalBill - receiveAmount;

        // Update modal displays
        document.getElementById("modal-amount-paid").textContent =
            formatCurrency(receiveAmount);
        document.getElementById("modal-due-summary").textContent =
            formatCurrency(Math.max(0, dueAmount));

        // Update hidden form fields
        const receiveAmountField = document.getElementById("receive_amount");
        const dueAmountField = document.getElementById("due_amount");
        const changeAmountField = document.getElementById("change_amount");

        if (receiveAmountField) receiveAmountField.value = receiveAmount;
        if (dueAmountField) dueAmountField.value = Math.max(0, dueAmount);
        if (changeAmountField)
            changeAmountField.value = Math.max(0, -dueAmount);
        
        // Replace SAR symbols after updating
        setTimeout(function() {
            if (typeof window.replaceSARSymbol === 'function') {
                window.replaceSARSymbol();
            }
        }, 50);
    }

    // Make updatePaymentCalculations available globally
    window.updatePaymentCalculations = updatePaymentCalculations;

    // Format currency using global currencyFormat if available, otherwise use local implementation
    function formatCurrency(amount) {
        if (typeof window.currencyFormat === "function") {
            return window.currencyFormat(amount);
        }

        const symbol = document.getElementById("currency_symbol")?.value || "";
        const position =
            document.getElementById("currency_position")?.value || "left";
        const code = document.getElementById("currency_code")?.value || "";
        
        // SAR Symbol SVG
        const sarSymbolSVG = '<svg width="11" height="12" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: inline-block; vertical-align: middle; margin-left: 3px;"><g clip-path="url(#clip0_price_5-1)"><path d="M6.68122 10.6309C6.48962 11.0558 6.36297 11.5168 6.31445 12.0003L10.369 11.1384C10.5606 10.7137 10.6872 10.2525 10.7358 9.76904L6.68122 10.6309Z" fill="currentColor"/><path d="M10.3691 8.55619C10.5607 8.13144 10.6873 7.67031 10.7359 7.18683L7.57749 7.85857V6.56725L10.369 5.97403C10.5606 5.54929 10.6873 5.08815 10.7358 4.60467L7.57739 5.27584V0.631863C7.09343 0.903594 6.66363 1.2653 6.31425 1.69195V5.54441L5.05111 5.8129V0.000244141C4.56715 0.27188 4.13735 0.633678 3.78797 1.06033V6.08129L0.961685 6.68186C0.770089 7.1066 0.643345 7.56773 0.594729 8.05122L3.78797 7.3726V8.99879L0.365788 9.72601C0.174192 10.1508 0.0475433 10.6119 -0.000976562 11.0954L3.58109 10.3341C3.87269 10.2735 4.12331 10.1011 4.28625 9.86384L4.94318 8.8899V8.88971C5.01138 8.78895 5.05111 8.66746 5.05111 8.53661V7.10412L6.31425 6.83564V9.41827L10.369 8.55599L10.3691 8.55619Z" fill="currentColor"/></g><defs><clipPath id="clip0_price_5-1"><rect width="10.7368" height="12" fill="white"/></clipPath></defs></svg>';
        
        // Check if currency is SAR
        const isSAR = code === 'SAR' || symbol === '^';
        
        const formatted = amount.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });

        if (isSAR) {
            if (position === "left") {
                return sarSymbolSVG + formatted;
            } else {
                return formatted + sarSymbolSVG;
            }
        } else {
            if (position === "left") {
                return symbol + formatted;
            } else {
                return formatted + symbol;
            }
        }
    }

    // Update customer info when customer is selected
    if (customerSelect) {
        customerSelect.addEventListener("change", function () {
            const selectedOption = this.options[this.selectedIndex];

            if (this.value === "guest" || this.value === "") {
                if (customerNameDisplay)
                    customerNameDisplay.textContent = "Guest";
                if (customerPhoneDisplay)
                    customerPhoneDisplay.textContent = "-";
            } else {
                const customerName = selectedOption.textContent
                    .split("(")[0]
                    .trim();
                const customerPhone =
                    selectedOption.getAttribute("data-phone") || "-";

                if (customerNameDisplay)
                    customerNameDisplay.textContent = customerName;
                if (customerPhoneDisplay)
                    customerPhoneDisplay.textContent = customerPhone;
            }
        });
    }

    // Complete payment - close modal and submit form
    if (completePaymentBtn) {
        completePaymentBtn.addEventListener("click", function () {
            // Ensure payment type is selected
            const paymentTypeInput = document.getElementById("payment_type_id");
            if (paymentTypeInput && !paymentTypeInput.value) {
                alert("Please select a payment type");
                return false;
            }

            // Modal will close, form will submit
            closeModal();
        });
    }

    // Delivery tab functionality (optional - add your logic)
    const deliveryTabBtns = document.querySelectorAll(
        ".sidebar-delivery-tabs .delivery-tab-btn",
    );
    deliveryTabBtns.forEach((btn) => {
        btn.addEventListener("click", function () {
            deliveryTabBtns.forEach((b) => b.classList.remove("active"));
            this.classList.add("active");
        });
    });
})();
