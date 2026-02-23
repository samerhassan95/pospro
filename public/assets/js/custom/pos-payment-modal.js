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

                    // Set payment type to cash (usually ID 1)
                    const paymentTypeSelect =
                        document.getElementById("payment_type_id");
                    if (
                        paymentTypeSelect &&
                        paymentTypeSelect.options.length > 0
                    ) {
                        // Try to find "Cash" option
                        for (
                            let i = 0;
                            i < paymentTypeSelect.options.length;
                            i++
                        ) {
                            if (
                                paymentTypeSelect.options[i].text
                                    .toLowerCase()
                                    .includes("cash")
                            ) {
                                paymentTypeSelect.value =
                                    paymentTypeSelect.options[i].value;
                                break;
                            }
                        }
                        // If not found, select first option
                        if (!paymentTypeSelect.value) {
                            paymentTypeSelect.value =
                                paymentTypeSelect.options[0].value;
                        }
                    }
                } else {
                    console.warn("⚠️ Cash payment method button not found");
                }

                // Show modal
                if (modalOverlay) {
                    console.log("✅ Opening modal...");
                    modalOverlay.classList.add("active");
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
            const paymentTypeSelect =
                document.getElementById("payment_type_id");

            if (paymentTypeSelect) {
                // Try to find matching payment type by name
                const methodNames = {
                    cash: ["cash", "نقد", "نقدي"],
                    card: ["card", "credit", "debit", "بطاقة", "كرت"],
                    upi: ["upi", "online", "digital"],
                    due: ["due", "credit", "آجل", "دين"],
                };

                let found = false;
                const searchTerms = methodNames[method] || [method];

                for (let i = 0; i < paymentTypeSelect.options.length; i++) {
                    const optionText =
                        paymentTypeSelect.options[i].text.toLowerCase();

                    for (let term of searchTerms) {
                        if (optionText.includes(term.toLowerCase())) {
                            paymentTypeSelect.value =
                                paymentTypeSelect.options[i].value;
                            found = true;
                            break;
                        }
                    }

                    if (found) break;
                }

                // If no match found, use first option
                if (!found && paymentTypeSelect.options.length > 0) {
                    paymentTypeSelect.value =
                        paymentTypeSelect.options[0].value;
                }

                console.log(
                    "Payment method selected:",
                    method,
                    "Payment type ID:",
                    paymentTypeSelect.value,
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
        const formatted = amount.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });

        if (position === "left") {
            return symbol + formatted;
        } else {
            return formatted + symbol;
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
            const paymentTypeSelect =
                document.getElementById("payment_type_id");
            if (paymentTypeSelect && !paymentTypeSelect.value) {
                // Set to first option if not selected
                if (paymentTypeSelect.options.length > 0) {
                    paymentTypeSelect.value =
                        paymentTypeSelect.options[0].value;
                }
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
