<!-- Order Sidebar -->
<div class="order-sidebar">
    <!-- Top Row: Request Number and Search Supplier -->
    <div class="top-row-container">
        <!-- Request Number Display -->
        <div class="request-number-section">
            <div class="request-number-row">
                <span class="request-label">{{ __('Purchase #') }}</span>
                <span class="request-value" id="request-number-display">{{ $invoice_no }}</span>
            </div>
        </div>

        <!-- Search Supplier Section -->
        <div class="sidebar-search-customer">
        <div class="search-customer-wrapper">
            <select required name="party_id" id="party_id" class="sidebar-customer-select choices-select">
                <option value="">{{ __('Search Supplier') }}</option>
                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" data-type="{{ $supplier->type }}" data-phone="{{ $supplier->phone }}">
                        {{ $supplier->name }}({{ $supplier->type }}{{ $supplier->due ? ' ' . currency_format($supplier->due, currency: business_currency()) : '' }}) {{ $supplier->phone }}
                    </option>
                @endforeach
            </select>
            <a href="#supplier-create-modal" data-bs-toggle="modal" class="sidebar-add-customer-btn">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 4V16M4 10H16" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>
    </div>
    </div>

    <!-- Order Details Section -->
    <div class="order-details-section">
        <div class="customer-info-flex">
            <div class="customer-name" id="selected-customer-name">{{ __('Select Supplier') }}</div>
            <div class="customer-phone" id="selected-customer-phone"></div>
        </div>
        <div class="order-info-line">
            <span class="order-date">{{ now()->format('D, M\TH\LY Y') }}</span>
            <div class="time-with-icon">
                <svg class="clock-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="10" cy="10" r="8" stroke="#6B7280" stroke-width="1.5"/>
                    <path d="M10 5V10L13 13" stroke="#6B7280" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                <span class="order-time" id="current-time">{{ now()->format('h:i A') }}</span>
            </div>
        </div>
        <input type="hidden" name="invoiceNumber" value="{{ $invoice_no }}">
        <input type="hidden" name="purchaseDate" value="{{ now()->format('Y-m-d') }}">
    </div>

    <!-- Delivery Type Tabs -->
    <div class="sidebar-delivery-tabs">
        <button type="button" class="delivery-tab-btn" data-delivery-type="delivery">{{ __('Delivery') }}</button>
        <button type="button" class="delivery-tab-btn" data-delivery-type="pre-order">{{ __('Pre-order') }}</button>
        <button type="button" class="delivery-tab-btn active" data-delivery-type="takeaway">{{ __('Takeaway') }}</button>
    </div>
    
    <!-- Hidden input to store selected delivery type -->
    <input type="hidden" name="delivery_type" id="delivery_type" value="takeaway">

    <!-- Products Section -->
    <div class="products-section">
        <div class="products-header">
            <h3 class="section-title">{{ __('Products') }}</h3>
            <button type="button" class="clear-all-btn cancel-sale-btn" data-route="{{ route('business.carts.remove-all') }}">
                {{ __('Clear All') }} 
                <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5.52024 4.81313C5.32499 4.61786 5.0084 4.61784 4.81313 4.81309C4.61786 5.00835 4.61784 5.32493 4.81309 5.5202L6.4596 7.16688L4.81352 8.81313C4.61827 9.0084 4.61829 9.32499 4.81356 9.52024C5.00883 9.71549 5.32541 9.71547 5.52067 9.5202L7.16667 7.87402L8.81267 9.5202C9.00792 9.71547 9.3245 9.71549 9.51977 9.52024C9.71505 9.32499 9.71506 9.0084 9.51981 8.81313L7.87374 7.16688L9.52024 5.5202C9.71549 5.32493 9.71547 5.00835 9.5202 4.81309C9.32493 4.61784 9.00835 4.61786 8.81309 4.81313L7.16667 6.45974L5.52024 4.81313Z" fill="#E53030"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7.16667 14.3333C3.20863 14.3333 0 11.1247 0 7.16667C0 3.20863 3.20863 0 7.16667 0C11.1247 0 14.3333 3.20863 14.3333 7.16667C14.3333 11.1247 11.1247 14.3333 7.16667 14.3333ZM1 7.16667C1 10.5724 3.76091 13.3333 7.16667 13.3333C10.5724 13.3333 13.3333 10.5724 13.3333 7.16667C13.3333 3.76091 10.5724 1 7.16667 1C3.76091 1 1 3.76091 1 7.16667Z" fill="#E53030"/>
                </svg>
            </button>
        </div>
        <div class="products-list" id="purchase_cart_list">
            @include('business::purchases.cart-list-new', ['cache_bust' => time()])
        </div>
    </div>

    <!-- Order Summary -->
    <div class="order-summary-section">
        <div class="summary-row">
            <span class="summary-label">{{ __('Items') }}</span>
            <span class="summary-value" id="items_count">0</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">{{ __('Subtotal') }}</span>
            <span class="summary-value" id="sub_total">{{ currency_format(0, currency: business_currency()) }}</span>
        </div>
        <div class="discount-shipping-row">
            <div class="summary-row discount-row">
                <span class="summary-label">{{ __('Discount') }}</span>
                <div class="discount-controls">
                    <span class="summary-value" id="discount_display">0</span>
                    <button type="button" class="add-discount-btn" id="add-discount-btn">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 3V13M3 8H13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="summary-row shipping-row">
                <span class="summary-label">{{ __('Shipping') }}</span>
                <div class="shipping-controls">
                    <span class="summary-value" id="shipping_display">0</span>
                    <button type="button" class="add-shipping-btn" id="add-shipping-btn">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 3V13M3 8H13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="discount-input-section d-none" id="discount-input-section">
            <div class="discount-input-wrapper">
                <input type="number" step="0.01" min="0" id="discount_amount_input" class="form-control discount-input" placeholder="0.00">
                <select id="discount_type_select" class="form-select discount-type-select">
                    <option value="flat">{{ __('Flat') }}</option>
                    <option value="percent">{{ __('Percent') }}</option>
                </select>
                <button type="button" class="apply-discount-btn" id="apply-discount-btn">{{ __('Apply') }}</button>
                <button type="button" class="cancel-discount-btn" id="cancel-discount-btn">{{ __('Cancel') }}</button>
            </div>
        </div>
        <div class="shipping-input-section d-none" id="shipping-input-section">
            <div class="shipping-input-wrapper">
                <input type="number" step="0.01" min="0" id="shipping_charge_input" class="form-control shipping-input" placeholder="0.00">
                <button type="button" class="apply-shipping-btn" id="apply-shipping-btn">{{ __('Apply') }}</button>
                <button type="button" class="cancel-shipping-btn" id="cancel-shipping-btn">{{ __('Cancel') }}</button>
            </div>
        </div>
        <div class="summary-row summary-total">
            <span class="summary-label ">{{ __('Total') }}</span>
            <span class="summary-value" id="total_amount">{{ currency_format(0, currency: business_currency()) }}</span>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons-section">
        @usercan('purchases.create')
            <button type="button" class="pay-bill-btn" id="open-payment-modal">{{ __('Pay the Bill') }}</button>
        @endusercan
        <button type="button" class="cancel-order-btn cancel-sale-btn" data-route="{{ route('business.carts.remove-all') }}">
            {{ __('Cancel Order') }}
        </button>
    </div>

    <!-- Hidden Form Fields -->
    <div style="display: none;">
        <input type="hidden" name="receive_amount" id="receive_amount" value="0">
        <input type="hidden" id="change_amount" value="0">
        <input type="hidden" id="due_amount" value="0">
        <select name="payment_type_id" id="payment_type_id">
            @foreach ($payment_types as $type)
                <option value="{{ $type->id }}">{{ $type->name }}</option>
            @endforeach
        </select>
        <input type="hidden" name="note" id="payment_note">
        <select name="vat_id" class="vat_select">
            <option value="">{{ __('Select') }}</option>
            @foreach ($vats as $vat)
                <option value="{{ $vat->id }}" data-rate="{{ $vat->rate }}">
                    {{ $vat->name }} ({{ $vat->rate }}%)
                </option>
            @endforeach
        </select>
        <input type="hidden" name="vat_amount" id="vat_amount" value="0">
        <select name="discount_type" class="discount_type">
            <option value="flat">{{ __('Flat') }}</option>
            <option value="percent">{{ __('Percent') }}</option>
        </select>
        <input type="hidden" name="discountAmount" id="discount_amount" value="0">
        <input type="hidden" name="shipping_charge" id="shipping_charge" value="0">
        <input type="hidden" id="payable_amount" value="0">
    </div>
</div>


<!-- Payment Modal -->
<div class="payment-modal-overlay" id="payment-modal-overlay">
    <div class="payment-modal">
        <div class="payment-modal-header">
            <h3 class="payment-modal-title">{{ __('Collect Payment') }}</h3>
            <div class="payment-modal-order-info">
                <div class="payment-order-number">{{ __('Purchase') }} #<span id="modal-order-number">{{ $invoice_no }}</span></div>
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
            <button type="submit" form="purchase-form" class="payment-complete-btn" id="complete-payment-btn">{{ __('Complete Payment') }}</button>
        </div>
    </div>
</div>
<style>
/* Top row container for request number and search customer */
.top-row-container {
    display: flex;
    gap: 6px;
    margin-bottom: 0px;
    align-items: flex-start;
}

.top-row-container .request-number-section {
    flex-shrink: 0;
    min-width: 120px;
}

.top-row-container .sidebar-search-customer {
    flex: 1;
}

/* Customer info flex layout */
.customer-info-flex {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.customer-info-flex .customer-name {
    font-weight: 600;
    color: #1f2937;
}

.customer-info-flex .customer-phone {
    font-size: 14px;
    color: #6b7280;
}

/* Order info line flex layout */
.order-info-line {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
}

/* Time with icon container */
.time-with-icon {
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Discount and shipping in same row */
.discount-shipping-row {
    display: flex;
    gap: 12px;
}

.discount-shipping-row .summary-row {
    flex: 1;
}

.discount-shipping-row .discount-row {
    border-right: 1px solid #e5e7eb;
    padding-right: 12px;
}
</style>